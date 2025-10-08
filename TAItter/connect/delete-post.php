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
    
    try {
        $check = $yhteys->prepare("SELECT user_id FROM posts WHERE post_id = :post_id");
        $check->execute([':post_id' => $post_id]);
        $post = $check->fetch();
        
        if (!$post || $post['user_id'] != $user_id) {
            header("Location: ../user.php?error=Unauthorized");
            exit;
        }
        
        $sql = "DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id
        ]);
        
        header("Location: ../user.php?success=Post deleted successfully");
        exit;
    } catch (PDOException $e) {
        header("Location: ../user.php?error=Error deleting post");
        exit;
    }
}
?>