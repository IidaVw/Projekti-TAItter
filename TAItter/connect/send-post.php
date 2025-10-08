<?php
session_start();
require_once 'connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    
    // Tarkista että sisältö ei ole tyhjä
    if (empty($content)) {
        header("Location: ../posts.php?error=Post cannot be empty");
        exit;
    }
    
    // Tarkista pituus (max 144 merkkiä)
    if (strlen($content) > 144) {
        header("Location: ../posts.php?error=Post is too long (max 144 characters)");
        exit;
    }
    
    try {
        // Lisää postaus tietokantaan
        $sql = "INSERT INTO posts (user_id, content) VALUES (:user_id, :content)";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':content' => $content
        ]);
        
        $post_id = $yhteys->lastInsertId();
        
        // Parsee ja tallenna #hashtagit
        preg_match_all('/#(\w+)/', $content, $hashtags);
        foreach ($hashtags[1] as $hashtag) {
            // Tarkista onko hashtag jo olemassa
            $check = $yhteys->prepare("SELECT hashtag_id FROM hashtags WHERE hashtag_name = :name");
            $check->execute([':name' => $hashtag]);
            
            if ($check->rowCount() == 0) {
                // Lisää uusi hashtag
                $insert = $yhteys->prepare("INSERT INTO hashtags (hashtag_name) VALUES (:name)");
                $insert->execute([':name' => $hashtag]);
                $hashtag_id = $yhteys->lastInsertId();
            } else {
                $hashtag_id = $check->fetch()['hashtag_id'];
            }
            
            // Yhdistä postaus ja hashtag
            $link = $yhteys->prepare("INSERT INTO post_hashtags (post_id, hashtag_id) VALUES (:post_id, :hashtag_id)");
            $link->execute([':post_id' => $post_id, ':hashtag_id' => $hashtag_id]);
        }
        
        // Parsee ja tallenna @-maininnat
        preg_match_all('/@(\w+)/', $content, $mentions);
        foreach ($mentions[1] as $mentioned_username) {
            $check = $yhteys->prepare("SELECT user_id FROM users WHERE username = :username");
            $check->execute([':username' => $mentioned_username]);
            
            if ($check->rowCount() > 0) {
                $mentioned_user_id = $check->fetch()['user_id'];
                $insert = $yhteys->prepare("INSERT INTO mentions (post_id, mentioned_user_id) VALUES (:post_id, :user_id)");
                $insert->execute([':post_id' => $post_id, ':user_id' => $mentioned_user_id]);
            }
        }
        
        header("Location: ../posts.php?success=Post created successfully");
        exit;
        
    } catch (PDOException $e) {
        header("Location: ../posts.php?error=Error creating post");
        exit;
    }
} else {
    header("Location: ../posts.php");
    exit;
}
?>