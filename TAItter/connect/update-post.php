<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    $content = trim($_POST['content']);
    
    if (empty($content)) {
        header("Location: ../user.php?error=Post content cannot be empty");
        exit;
    }
    
    if (strlen($content) > 144) {
        header("Location: ../user.php?error=Post is too long (max 144 characters)");
        exit;
    }
    
    try {
        $check = $yhteys->prepare("SELECT user_id FROM posts WHERE post_id = :post_id");
        $check->execute([':post_id' => $post_id]);
        $post = $check->fetch();
        
        if (!$post || $post['user_id'] != $user_id) {
            header("Location: ../user.php?error=Unauthorized");
            exit;
        }
        
        $sql = "UPDATE posts SET content = :content WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([
            ':content' => $content,
            ':post_id' => $post_id,
            ':user_id' => $user_id
        ]);
        
        $yhteys->prepare("DELETE FROM post_hashtags WHERE post_id = :post_id")->execute([':post_id' => $post_id]);
        $yhteys->prepare("DELETE FROM mentions WHERE post_id = :post_id")->execute([':post_id' => $post_id]);
        
        preg_match_all('/#(\w+)/', $content, $hashtags);
        foreach ($hashtags[1] as $hashtag) {
            $check = $yhteys->prepare("SELECT hashtag_id FROM hashtags WHERE hashtag_name = :name");
            $check->execute([':name' => $hashtag]);
            
            if ($check->rowCount() == 0) {
                $insert = $yhteys->prepare("INSERT INTO hashtags (hashtag_name) VALUES (:name)");
                $insert->execute([':name' => $hashtag]);
                $hashtag_id = $yhteys->lastInsertId();
            } else {
                $hashtag_id = $check->fetch()['hashtag_id'];
            }
            
            $link = $yhteys->prepare("INSERT INTO post_hashtags (post_id, hashtag_id) VALUES (:post_id, :hashtag_id)");
            $link->execute([':post_id' => $post_id, ':hashtag_id' => $hashtag_id]);
        }
        
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
        
        header("Location: ../user.php?success=Post updated successfully");
        exit;
    } catch (PDOException $e) {
        header("Location: ../user.php?error=Error updating post");
        exit;
    }
}
?>