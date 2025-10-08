<?php
session_start();
require_once 'connect/connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Hae käyttäjän tiedot
$sql = "SELECT * FROM users WHERE user_id = :user_id";
$stmt = $yhteys->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/user.css">
    <script src="js/dark-light.js"></script>
    <title><?= htmlspecialchars($user['username']) ?> - TAItter</title>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo"><span class="logo-highlight">TAI</span>tter</a>
            <button class="theme-toggle" onclick="toggleTheme()">
                <span class="theme-icon moon" id="theme-icon">🌙</span>
            </button>
        </div>
    </header>
    
    <main class="main-container">
        <section class="profile-section">
            <div class="profile-header">
                <div class="avatar-large"><?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?></div>
                <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                <p class="username">@<?= htmlspecialchars($user['username']) ?></p>
                <p class="bio"><?= htmlspecialchars($user['bio'] ?? 'No bio yet.') ?></p>
                <p class="join-date">Joined: <?= date('F Y', strtotime($user['created_at'])) ?></p>
            </div>
            
            <div class="profile-stats">
                <div class="stat">
                    <strong>0</strong>
                    <span>Posts</span>
                </div>
                <div class="stat">
                    <strong>0</strong>
                    <span>Following</span>
                </div>
                <div class="stat">
                    <strong>0</strong>
                    <span>Followers</span>
                </div>
            </div>
            
            <div class="profile-actions">
                <a href="posts.php" class="btn">Back to Feed</a>
                <a href="connect/logout.php" class="btn-secondary">Logout</a>
            </div>
        </section>
    </main>
</body>
</html>