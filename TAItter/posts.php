<?php
session_start();
require_once 'connect/connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Tarkista mikä näkymä valittu (default: all)
$view = isset($_GET['view']) ? $_GET['view'] : 'all';

// Tarkista onko käyttäjä seurannut mitään
$check_hashtags = $yhteys->prepare("SELECT COUNT(*) as count FROM followed_hashtags WHERE user_id = :user_id");
$check_hashtags->execute([':user_id' => $user_id]);
$hashtag_count = $check_hashtags->fetch()['count'];

$check_users = $yhteys->prepare("SELECT COUNT(*) as count FROM followed_users WHERE follower_id = :user_id");
$check_users->execute([':user_id' => $user_id]);
$user_count = $check_users->fetch()['count'];

$has_follows = ($hashtag_count + $user_count) > 0;

// Valitse query näkymän mukaan
if ($view === 'personalized' && $has_follows) {
    // Näytä VAIN suodatetut postaukset
    $sql = "SELECT DISTINCT p.*, u.username, u.first_name, u.last_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.user_id 
            WHERE 
                p.post_id IN (
                    SELECT ph.post_id FROM post_hashtags ph
                    JOIN followed_hashtags fh ON ph.hashtag_id = fh.hashtag_id
                    WHERE fh.user_id = :user_id
                )
                OR p.user_id IN (
                    SELECT fu.followed_id FROM followed_users fu
                    WHERE fu.follower_id = :user_id
                )
                OR p.post_id IN (
                    SELECT m.post_id FROM mentions m
                    WHERE m.mentioned_user_id = :user_id
                )
            ORDER BY p.created_at DESC";
    
    $stmt = $yhteys->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Näytä KAIKKI postaukset
    $sql = "SELECT p.*, u.username, u.first_name, u.last_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.user_id 
            ORDER BY p.created_at DESC";
    $stmt = $yhteys->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Funktio avatarin luomiseen
function getAvatar($firstName, $lastName) {
    return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
}

// Funktio hashtagien ja @-viittausten korostamiseen
function highlightContent($content) {
    $content = preg_replace('/#(\w+)/', '<span class="hashtag clickable" data-hashtag="$1">#$1</span>', $content);
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
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo"><span class="logo-highlight">TAI</span>tter</a>

            <!-- View Toggle Buttons -->
            <div class="view-toggle">
                <a href="posts.php?view=all" class="view-btn <?= $view === 'all' ? 'active' : '' ?>">
                    🌍 All Posts
                </a>
                <a href="posts.php?view=personalized" class="view-btn <?= $view === 'personalized' ? 'active' : '' ?>" <?= !$has_follows ? 'style="opacity: 0.5; pointer-events: none;" title="Follow hashtags or users first"' : '' ?>>
                    ✨ Following
                </a>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" placeholder="Search people or hashtags..." id="search-input">
                <button id="search-btn">🔍</button>
            </div>

            <!-- User info ja logout -->
            <div class="header-user-controls">
                <a href="user.php" class="welcome-link">
                    <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?>!</span>
                </a>
                <a href="manage-follows.php" class="header-link">Manage Follows</a>
                <a href="connect/logout.php" class="header-link">Logout</a>
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
            <div class="new-post-section">
                <h3>What's happening?</h3>
                <form action="connect/send-post.php" method="post">
                    <textarea 
                        name="content" 
                        maxlength="144" 
                        placeholder="Share your thoughts... (max 144 characters)" 
                        required 
                        class="post-textarea"
                    ></textarea>
                    <div class="post-form-footer">
                        <small id="char-count">0/144</small>
                        <button type="submit" class="post-submit-btn">Post</button>
                    </div>
                </form>
            </div>

            <!-- Filter Notice -->
            <?php if ($view === 'all'): ?>
                <div class="filter-notice">
                    <strong>🌍 Showing all posts</strong>
                    <p>Browse everything on TAItter. Click on #hashtags or @usernames to follow them!</p>
                </div>
            <?php elseif ($view === 'personalized' && $has_follows): ?>
                <div class="filter-notice">
                    <strong>✨ Personalized feed</strong>
                    <p>Showing posts from <?= $hashtag_count ?> hashtags and <?= $user_count ?> users you follow</p>
                    <a href="manage-follows.php" class="filter-notice-link">Manage follows →</a>
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
                    <p>No posts matching your filters. 📭</p>
                    <?php if ($view === 'personalized'): ?>
                        <p>Try <a href="posts.php?view=all" class="inline-link">viewing all posts</a> or <a href="manage-follows.php" class="inline-link">follow more hashtags/users</a>!</p>
                    <?php else: ?>
                        <p>Be the first to post something!</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Friends Sidebar -->
        <div class="friend-section">
            <h2>Following (<?= $user_count ?>)</h2>
            <?php
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
                <div class="follow-item">
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
            
            <h2 class="sidebar-hashtag-title">Hashtags (<?= $hashtag_count ?>)</h2>
            <?php
            $hashtags_sql = "SELECT h.hashtag_name 
                            FROM followed_hashtags fh
                            JOIN hashtags h ON fh.hashtag_id = h.hashtag_id
                            WHERE fh.user_id = :user_id
                            LIMIT 5";
            $hashtags_stmt = $yhteys->prepare($hashtags_sql);
            $hashtags_stmt->execute([':user_id' => $user_id]);
            $followed_hashtags_list = $hashtags_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($followed_hashtags_list) > 0):
                foreach ($followed_hashtags_list as $hashtag):
            ?>
                <div class="hashtag-item">
                    <strong>#<?= htmlspecialchars($hashtag['hashtag_name']) ?></strong>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <p>Not following any hashtags yet 🏷️</p>
                <small>Click on #hashtags in posts to follow!</small>
            <?php endif; ?>
            
            <a href="manage-follows.php" class="manage-follows-link">Manage all follows →</a>
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
                        location.reload();
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
                        location.reload();
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