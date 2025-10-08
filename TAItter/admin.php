<?php
session_start();
require_once 'connect/connect.php';

// Tarkista onko käyttäjä kirjautunut JA admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hae tilastoja
$total_users = $yhteys->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_posts = $yhteys->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$total_hashtags = $yhteys->query("SELECT COUNT(*) FROM hashtags")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
    <title>Admin - TAItter</title>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo"><span class="logo-highlight">TAI</span>tter</a>
            <a href="connect/logout.php">Logout</a>
        </div>
    </header>
    
    <main class="main-container">
        <h1>Admin Dashboard</h1>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?= $total_users ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Posts</h3>
                <p><?= $total_posts ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Hashtags</h3>
                <p><?= $total_hashtags ?></p>
            </div>
        </div>
        
        <a href="posts.php">Back to Posts</a>
    </main>
</body>
</html>