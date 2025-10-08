<?php
session_start();
require_once 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Tarkista että kentät eivät ole tyhjiä
    if (empty($username) || empty($password)) {
        header("Location: ../login.php?error=Please fill in all fields");
        exit;
    }
    
    try {
        // Hae käyttäjä tietokannasta
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([':username' => $username]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Tarkista löytyikö käyttäjä ja onko salasana oikein
        if ($user && password_verify($password, $user['password'])) {
            // Kirjautuminen onnistui - tallenna session tiedot
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['bio'] = $user['bio'];
            
            // Regeneroi session ID turvallisuuden vuoksi
            session_regenerate_id(true);
            
            // Ohjaa käyttäjä posts-sivulle
            header("Location: ../posts.php");
            exit;
        } else {
            // Väärä käyttäjänimi tai salasana
            header("Location: ../login.php?error=Invalid username or password");
            exit;
        }
    } catch (PDOException $e) {
        // Tietokantavirhe
        header("Location: ../login.php?error=Database error");
        exit;
    }
} else {
    // Jos tullaan suoraan ilman POST-pyyntöä
    header("Location: ../login.php");
    exit;
}
?>