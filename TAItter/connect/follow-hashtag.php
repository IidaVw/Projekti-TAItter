<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$hashtag_name = $_POST['hashtag'] ?? '';

// Hae hashtag_id
$check = $yhteys->prepare("SELECT hashtag_id FROM hashtags WHERE hashtag_name = :name");
$check->execute([':name' => $hashtag_name]);
$hashtag = $check->fetch();

if ($hashtag) {
    $hashtag_id = $hashtag['hashtag_id'];
    
    // Tarkista onko jo seurattu
    $exists = $yhteys->prepare("SELECT * FROM followed_hashtags WHERE user_id = :uid AND hashtag_id = :hid");
    $exists->execute([':uid' => $user_id, ':hid' => $hashtag_id]);
    
    if ($exists->rowCount() == 0) {
        // Lisää seuranta
        $insert = $yhteys->prepare("INSERT INTO followed_hashtags (user_id, hashtag_id) VALUES (:uid, :hid)");
        $insert->execute([':uid' => $user_id, ':hid' => $hashtag_id]);
        echo json_encode(['success' => true, 'message' => 'Now following #' . $hashtag_name]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Already following']);
    }
} else {
    echo json_encode(['error' => 'Hashtag not found']);
}
?>