<?php
session_start();
require_once 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $bio = trim($_POST['bio']) ?? null;
    $date_of_birth = $_POST['age'];
    
    // Tarkista että kentät eivät ole tyhjiä
    if (empty($fname) || empty($lname) || empty($email) || empty($username) || empty($password) || empty($date_of_birth)) {
        header("Location: ../signup.php?error=Please fill in all required fields");
        exit;
    }
    
    // Tarkista sähköpostin muoto
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../signup.php?error=Invalid email format");
        exit;
    }
    
    try {
        // Tarkista onko käyttäjänimi tai sähköposti jo käytössä
        $check = $yhteys->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $check->execute([':username' => $username, ':email' => $email]);
        
        if ($check->rowCount() > 0) {
            header("Location: ../signup.php?error=Username or email already exists");
            exit;
        }
        
        // Hashaa salasana
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Lisää käyttäjä tietokantaan
        $sql = "INSERT INTO users (first_name, last_name, email, username, password, bio, date_of_birth) 
                VALUES (:fname, :lname, :email, :username, :password, :bio, :dob)";
        
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([
            ':fname' => $fname,
            ':lname' => $lname,
            ':email' => $email,
            ':username' => $username,
            ':password' => $hashed_password,
            ':bio' => $bio,
            ':dob' => $date_of_birth
        ]);
        
        header("Location: ../login.php?success=Registration successful! Please log in.");
        exit;
        
    } catch (PDOException $e) {
        header("Location: ../signup.php?error=Database error");
        exit;
    }
} else {
    header("Location: ../signup.php");
    exit;
}
?>