<?php
session_start();
require_once 'connect/connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Hae käyttäjän seuraamat hashtagit
$hashtags_sql = "SELECT h.hashtag_id, h.hashtag_name, fh.followed_at
                 FROM followed_hashtags fh
                 JOIN hashtags h ON fh.hashtag_id = h.hashtag_id
                 WHERE fh.user_id = :user_id
                 ORDER BY fh.followed_at DESC";
$hashtags_stmt = $yhteys->prepare($hashtags_sql);
$hashtags_stmt->execute([':user_id' => $user_id]);
$followed_hashtags = $hashtags_stmt->fetchAll(PDO::FETCH_ASSOC);

// Hae käyttäjän seuraamat käyttäjät
$users_sql = "SELECT u.user_id, u.username, u.first_name, u.last_name, u.bio, fu.followed_at
              FROM followed_users fu
              JOIN users u ON fu.followed_id = u.user_id
              WHERE fu.follower_id = :user_id
              ORDER BY fu.followed_at DESC";
$users_stmt = $yhteys->prepare($users_sql);
$users_stmt->execute([':user_id' => $user_id]);
$followed_users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// Käsittele poistopyynnöt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['unfollow_hashtag'])) {
        $hashtag_id = $_POST['hashtag_id'];
        $delete = $yhteys->prepare("DELETE FROM followed_hashtags WHERE user_id = :uid AND hashtag_id = :hid");
        $delete->execute([':uid' => $user_id, ':hid' => $hashtag_id]);
        header("Location: manage-follows.php?success=Hashtag unfollowed");
        exit;
    }
    
    if (isset($_POST['unfollow_user'])) {
        $followed_id = $_POST['user_id'];
        $delete = $yhteys->prepare("DELETE FROM followed_users WHERE follower_id = :fid AND followed_id = :uid");
        $delete->execute([':fid' => $user_id, ':uid' => $followed_id]);
        header("Location: manage-follows.php?success=User unfollowed");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/posts.css">
    <script src="js/dark-light.js"></script>
    <title>Manage Follows - TAItter</title>
    <style>
        .manage-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        .section {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .follow-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        .follow-item:last-child {
            border-bottom: none;
        }
        .follow-info {
            flex: 1;
        }
        .unfollow-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .unfollow-btn:hover {
            background: #c82333;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success-msg {
            padding: 10px;
            background: #d4edda;
            color: #155724;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo"><span class="logo-highlight">TAI</span>tter</a>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="color: var(--text-color);">Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?>!</span>
                <a href="posts.php" style="color: var(--primary-color); text-decoration: none;">Feed</a>
                <a href="connect/logout.php" style="color: var(--primary-color); text-decoration: none;">Logout</a>
                <button class="theme-toggle" onclick="toggleTheme()">
                    <span class="theme-icon moon" id="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </header>

    <div class="manage-container">
        <a href="posts.php" class="back-btn">← Back to Feed</a>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success-msg"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <h1>Manage Your Follows</h1>

        <!-- Followed Hashtags Section -->
        <div class="section">
            <h2>Followed Hashtags (<?= count($followed_hashtags) ?>)</h2>
            
            <?php if (count($followed_hashtags) > 0): ?>
                <?php foreach ($followed_hashtags as $hashtag): ?>
                <div class="follow-item">
                    <div class="follow-info">
                        <strong style="color: var(--primary-color); font-size: 18px;">#<?= htmlspecialchars($hashtag['hashtag_name']) ?></strong>
                        <br>
                        <small style="color: var(--text-secondary);">Followed on <?= date('M d, Y', strtotime($hashtag['followed_at'])) ?></small>
                    </div>
                    <form method="post" style="margin: 0;">
                        <input type="hidden" name="hashtag_id" value="<?= $hashtag['hashtag_id'] ?>">
                        <button type="submit" name="unfollow_hashtag" class="unfollow-btn" onclick="return confirm('Unfollow #<?= htmlspecialchars($hashtag['hashtag_name']) ?>?')">
                            Unfollow
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You're not following any hashtags yet. Click on hashtags in posts to follow them!</p>
            <?php endif; ?>
        </div>

        <!-- Followed Users Section -->
        <div class="section">
            <h2>Followed Users (<?= count($followed_users) ?>)</h2>
            
            <?php if (count($followed_users) > 0): ?>
                <?php foreach ($followed_users as $user): ?>
                <div class="follow-item">
                    <div class="follow-info">
                        <strong style="font-size: 18px;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                        <span style="color: var(--text-secondary);"> @<?= htmlspecialchars($user['username']) ?></span>
                        <br>
                        <?php if ($user['bio']): ?>
                            <small style="color: var(--text-secondary);"><?= htmlspecialchars($user['bio']) ?></small>
                            <br>
                        <?php endif; ?>
                        <small style="color: var(--text-secondary);">Followed on <?= date('M d, Y', strtotime($user['followed_at'])) ?></small>
                    </div>
                    <form method="post" style="margin: 0;">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <button type="submit" name="unfollow_user" class="unfollow-btn" onclick="return confirm('Unfollow @<?= htmlspecialchars($user['username']) ?>?')">
                            Unfollow
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You're not following any users yet. Click on @mentions in posts to follow them!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>