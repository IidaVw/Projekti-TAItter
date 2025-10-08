<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $bio = trim($_POST['bio']);
    
    if (empty($first_name) || empty($last_name)) {
        header("Location: ../user.php?error=First name and last name are required");
        exit;
    }
    
    try {
        $sql = "UPDATE users SET first_name = :fname, last_name = :lname, bio = :bio WHERE user_id = :user_id";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([
            ':fname' => $first_name,
            ':lname' => $last_name,
            ':bio' => $bio,
            ':user_id' => $user_id
        ]);
        
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        
        header("Location: ../user.php?success=Profile updated successfully");
        exit;
    } catch (PDOException $e) {
        header("Location: ../user.php?error=Error updating profile");
        exit;
    }
}
?>