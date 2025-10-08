<?php
session_start();
require_once 'connect/connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hae postaukset tietokannasta
$sql = "SELECT p.*, u.username, u.first_name, u.last_name 
        FROM posts p 
        JOIN users u ON p.user_id = u.user_id 
        ORDER BY p.created_at DESC";
$stmt = $yhteys->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Funktio avatarin luomiseen
function getAvatar($firstName, $lastName) {
    return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
}

// Funktio hashtagien ja @-viittausten korostamiseen
function highlightContent($content) {
    // Korosta #hashtagit
    $content = preg_replace('/#(\w+)/', '<span class="hashtag">#$1</span>', $content);
    // Korosta @-maininnat
    $content = preg_replace('/@(\w+)/', '<span class="mention">@$1</span>', $content);
    return $content;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/posts.css">
  <script defer src="js/dark-light.js"></script>
  <script defer src="js/posts.js"></script>
  <title>Posts - TAItter</title>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo"><span class="logo-highlight">TAI</span>tter</a>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" placeholder="Search people or hashtags..." id="search-input">
                <button id="search-btn">🔍</button>
            </div>

            <!-- User info ja logout -->
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="color: var(--text-color);">Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?>!</span>
                <a href="connect/logout.php" style="color: var(--primary-color); text-decoration: none;">Logout</a>
                <button class="theme-toggle" onclick="toggleTheme()">
                    <span class="theme-icon moon" id="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </header>

  <section class="container">
        <!-- Comment Sidebar -->
        <div class="comment-section">
            <h2>Comments</h2>

            <div class="comment-list">
                <p>Select a post to view comments 💬</p>
            </div>

            <div class="comment-form">
                <textarea placeholder="Write a comment..."></textarea>
                <button>Send</button>
            </div>
        </div>

        <!-- Center Feed -->
        <div class="feed">
            <!-- New Post Form -->
            <div class="new-post-section" style="background: var(--card-bg); padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h3>What's happening?</h3>
                <form action="connect/send-post.php" method="post">
                    <textarea name="content" maxlength="144" placeholder="Share your thoughts... (max 144 characters)" required style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);"></textarea>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                        <small id="char-count">0/144</small>
                        <input type="submit" value="Post" style="padding: 8px 20px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">
                    </div>
                </form>
            </div>

            <!-- Display Posts from Database -->
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                <div class="post-section" data-id="<?= $post['post_id'] ?>">
                    <div class="user">
                        <div class="avatar"><?= getAvatar($post['first_name'], $post['last_name']) ?></div>
                        <div class="user-info">
                            <div class="username"><?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?></div>
                            <div class="handle">@<?= htmlspecialchars($post['username']) ?></div>
                        </div>
                    </div>
                    <div class="tweet-text">
                        <?= highlightContent(htmlspecialchars($post['content'])) ?>
                    </div>
                    <div class="tweet-actions">
                        <div class="action comment-btn">💬 0</div>
                        <div class="action">🔄 0</div>
                        <div class="action">❤️ 0</div>
                        <div class="action">📤 Share</div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="post-section">
                    <p>No posts yet. Be the first to post! 🚀</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Friends Sidebar -->
        <div class="friend-section">
            <h2>Friends</h2>
            <p>Coming soon… 👥</p>
        </div>
  </section>

  <script>
    // Character counter for new post
    const textarea = document.querySelector('textarea[name="content"]');
    const charCount = document.getElementById('char-count');
    
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length + '/144';
        });
    }
  </script>
</body>
</html>