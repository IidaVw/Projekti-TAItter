<?php
session_start();
require_once 'connect/connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Tarkista onko käyttäjä seurannut mitään
$has_follows_sql = "SELECT 
    (SELECT COUNT(*) FROM followed_hashtags WHERE user_id = :user_id1) +
    (SELECT COUNT(*) FROM followed_users WHERE follower_id = :user_id2) as total_follows";
$has_follows_stmt = $yhteys->prepare($has_follows_sql);
$has_follows_stmt->execute([':user_id1' => $user_id, ':user_id2' => $user_id]);
$has_follows = $has_follows_stmt->fetch()['total_follows'] > 0;

// Jos käyttäjä ei seuraa mitään, näytä KAIKKI postaukset
// Muuten näytä vain suodatetut postaukset
if (!$has_follows) {
    // Näytä KAIKKI postaukset
    $sql = "SELECT p.*, u.username, u.first_name, u.last_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.user_id 
            ORDER BY p.created_at DESC";
    $stmt = $yhteys->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Näytä VAIN suodatetut postaukset
    $sql = "SELECT DISTINCT p.*, u.username, u.first_name, u.last_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.user_id 
            WHERE 
                -- Postaukset käyttäjän seuraamista hashtagista
                p.post_id IN (
                    SELECT ph.post_id FROM post_hashtags ph
                    JOIN followed_hashtags fh ON ph.hashtag_id = fh.hashtag_id
                    WHERE fh.user_id = :user_id
                )
                -- TAI postaukset käyttäjän seuraamista käyttäjistä
                OR p.user_id IN (
                    SELECT fu.followed_id FROM followed_users fu
                    WHERE fu.follower_id = :user_id
                )
                -- TAI postaukset joissa mainitaan käyttäjä
                OR p.post_id IN (
                    SELECT m.post_id FROM mentions m
                    WHERE m.mentioned_user_id = :user_id
                )
            ORDER BY p.created_at DESC";
    
    $stmt = $yhteys->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funktio avatarin luomiseen
function getAvatar($firstName, $lastName) {
    return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
}

// Funktio hashtagien ja @-viittausten korostamiseen KLIKKAUS-toiminnolla
function highlightContent($content) {
    // Korosta #hashtagit ja tee niistä klikattavia
    $content = preg_replace('/#(\w+)/', '<span class="hashtag clickable" data-hashtag="$1">#$1</span>', $content);
    // Korosta @-maininnat ja tee niistä klikattavia
    $content = preg_replace('/@(\w+)/', '<span class="mention clickable" data-username="$1">@$1</span>', $content);
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
  <style>
    .clickable {
        cursor: pointer;
        text-decoration: underline;
    }
    .clickable:hover {
        opacity: 0.8;
    }
    .filter-notice {
        background: var(--card-bg);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        text-align: center;
        border: 2px solid var(--primary-color);
    }
  </style>
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
                <a href="manage-follows.php" style="color: var(--primary-color); text-decoration: none;">Manage Follows</a>
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

            <!-- Filter Notice -->
            <?php if (!$has_follows): ?>
                <div class="filter-notice">
                    <strong>🌍 Showing all posts</strong>
                    <p style="margin: 10px 0;">You're not following anyone yet. Click on #hashtags or @usernames to personalize your feed!</p>
                    <a href="manage-follows.php" style="color: var(--primary-color);">Manage follows →</a>
                </div>
            <?php else: ?>
                <div class="filter-notice">
                    <strong>✨ Personalized feed</strong>
                    <p style="margin: 10px 0;">Showing posts from your followed hashtags and users</p>
                    <a href="manage-follows.php" style="color: var(--primary-color);">Manage follows →</a>
                </div>
            <?php endif; ?>

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
                    <p>No posts available yet. 📭</p>
                    <p>Be the first to post something!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Friends Sidebar -->
        <div class="friend-section">
            <h2>Following</h2>
            <?php
            // Näytä käyttäjän seuraamat käyttäjät
            $follows_sql = "SELECT u.username, u.first_name, u.last_name 
                           FROM followed_users fu
                           JOIN users u ON fu.followed_id = u.user_id
                           WHERE fu.follower_id = :user_id
                           LIMIT 5";
            $follows_stmt = $yhteys->prepare($follows_sql);
            $follows_stmt->execute([':user_id' => $user_id]);
            $following = $follows_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($following) > 0):
                foreach ($following as $user):
            ?>
                <div style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                    <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                    <br>
                    <small>@<?= htmlspecialchars($user['username']) ?></small>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <p>Not following anyone yet 👥</p>
                <small>Click on @usernames in posts to follow!</small>
            <?php endif; ?>
            <a href="manage-follows.php" style="display: block; margin-top: 10px; color: var(--primary-color);">View all →</a>
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

    // HASHTAG click handler - Follow hashtag
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('hashtag') && e.target.classList.contains('clickable')) {
            const hashtag = e.target.dataset.hashtag;
            
            if (confirm('Do you want to follow #' + hashtag + '?')) {
                fetch('connect/follow-hashtag.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'hashtag=' + encodeURIComponent(hashtag)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Refresh to show filtered posts
                    } else {
                        alert(data.message || 'Already following or error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error following hashtag');
                });
            }
        }
    });

    // MENTION click handler - Follow user
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('mention') && e.target.classList.contains('clickable')) {
            const username = e.target.dataset.username;
            
            if (confirm('Do you want to follow @' + username + '?')) {
                fetch('connect/follow-user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'username=' + encodeURIComponent(username)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Refresh to show filtered posts
                    } else {
                        alert(data.message || data.error || 'Already following or error occurred');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error following user');
                });
            }
        }
    });
  </script>
</body>
</html>