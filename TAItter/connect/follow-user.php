<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$follower_id = $_SESSION['user_id'];
$username = $_POST['username'] ?? '';

// Hae käyttäjän ID
$check = $yhteys->prepare("SELECT user_id FROM users WHERE username = :username");
$check->execute([':username' => $username]);
$user = $check->fetch();

if ($user) {
    $followed_id = $user['user_id'];
    
    // Ei voi seurata itseään
    if ($follower_id == $followed_id) {
        echo json_encode(['error' => 'Cannot follow yourself']);
        exit;
    }
    
    // Tarkista onko jo seurattu
    $exists = $yhteys->prepare("SELECT * FROM followed_users WHERE follower_id = :fid AND followed_id = :uid");
    $exists->execute([':fid' => $follower_id, ':uid' => $followed_id]);
    
    if ($exists->rowCount() == 0) {
        // Lisää seuranta
        $insert = $yhteys->prepare("INSERT INTO followed_users (follower_id, followed_id) VALUES (:fid, :uid)");
        $insert->execute([':fid' => $follower_id, ':uid' => $followed_id]);
        echo json_encode(['success' => true, 'message' => 'Now following @' . $username]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Already following']);
    }
} else {
    echo json_encode(['error' => 'User not found']);
}
?>