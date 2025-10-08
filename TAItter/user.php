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
$user_sql = "SELECT * FROM users WHERE user_id = :user_id";
$user_stmt = $yhteys->prepare($user_sql);
$user_stmt->execute([':user_id' => $user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Hae käyttäjän postaukset
$posts_sql = "SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC";
$posts_stmt = $yhteys->prepare($posts_sql);
$posts_stmt->execute([':user_id' => $user_id]);
$posts = $posts_stmt->fetchAll(PDO::FETCH_ASSOC);

// Laske tilastot
$follower_count_sql = "SELECT COUNT(*) as count FROM followed_users WHERE followed_id = :user_id";
$follower_stmt = $yhteys->prepare($follower_count_sql);
$follower_stmt->execute([':user_id' => $user_id]);
$follower_count = $follower_stmt->fetch()['count'];

$following_count_sql = "SELECT COUNT(*) as count FROM followed_users WHERE follower_id = :user_id";
$following_stmt = $yhteys->prepare($following_count_sql);
$following_stmt->execute([':user_id' => $user_id]);
$following_count = $following_stmt->fetch()['count'];

// Funktio hashtagien korostamiseen
function highlightContent($content) {
    $content = preg_replace('/#(\w+)/', '<span style="color: #dc143c; font-weight: 600;">#$1</span>', $content);
    $content = preg_replace('/@(\w+)/', '<span style="color: #dc143c; font-weight: 600;">@$1</span>', $content);
    return $content;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/user.css">
    <script defer src="js/dark-light.js"></script>
    <title><?= htmlspecialchars($user['username']) ?> - TAItter</title>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo"><span class="logo-highlight">TAI</span>tter</a>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="posts.php" style="color: var(--accent-primary); text-decoration: none; font-weight: 600;">Feed</a>
                <a href="manage-follows.php" style="color: var(--accent-primary); text-decoration: none; font-weight: 600;">Manage Follows</a>
                <a href="connect/logout.php" style="color: var(--accent-primary); text-decoration: none; font-weight: 600;">Logout</a>
                <button class="theme-toggle" onclick="toggleTheme()">
                    <span class="theme-icon moon" id="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </header>
    
    <main class="main-container">
        <?php if (isset($_GET['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

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
                    <strong><?= count($posts) ?></strong>
                    <span>Posts</span>
                </div>
                <div class="stat">
                    <strong><?= $following_count ?></strong>
                    <span>Following</span>
                </div>
                <div class="stat">
                    <strong><?= $follower_count ?></strong>
                    <span>Followers</span>
                </div>
            </div>
            
            <div class="profile-actions">
                <button onclick="showEditProfile()" class="btn">Edit Profile</button>
                <a href="posts.php" class="btn-secondary">Back to Feed</a>
            </div>
        </section>

        <!-- Edit Profile Form (Hidden by default) -->
        <section id="edit-profile-form" style="display: none; max-width: 800px; margin: 30px auto; background: var(--bg-secondary); padding: 30px; border-radius: 15px;">
            <h2>Edit Profile</h2>
            <form action="connect/update-profile.php" method="post">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">First Name:</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-primary); color: var(--text-primary); box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Last Name:</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-primary); color: var(--text-primary); box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Bio:</label>
                    <textarea name="bio" rows="4" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-primary); color: var(--text-primary); box-sizing: border-box; resize: vertical;"><?= htmlspecialchars($user['bio'] ?: '') ?></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn">Save Changes</button>
                    <button type="button" onclick="hideEditProfile()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </section>

        <!-- My Posts Section -->
        <section style="max-width: 800px; margin: 30px auto;">
            <h2 style="margin-bottom: 20px; font-size: 24px;">My Posts (<?= count($posts) ?>)</h2>
            
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                <div class="post-item" id="post-<?= $post['post_id'] ?>" style="background: var(--bg-secondary); padding: 20px; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div class="post-content" id="content-<?= $post['post_id'] ?>" style="margin-bottom: 15px; color: var(--text-primary);">
                        <?= highlightContent(htmlspecialchars($post['content'])) ?>
                    </div>
                    
                    <!-- Edit Form (Hidden by default) -->
                    <div class="edit-form" id="edit-form-<?= $post['post_id'] ?>" style="display: none; margin-bottom: 15px;">
                        <form action="connect/update-post.php" method="post">
                            <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                            <textarea name="content" maxlength="144" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-primary); color: var(--text-primary); resize: vertical; min-height: 80px; box-sizing: border-box;"><?= htmlspecialchars($post['content']) ?></textarea>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <button type="submit" class="btn" style="padding: 8px 16px; font-size: 14px;">Save</button>
                                <button type="button" onclick="cancelEdit(<?= $post['post_id'] ?>)" class="btn-secondary" style="padding: 8px 16px; font-size: 14px;">Cancel</button>
                            </div>
                        </form>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-secondary); font-size: 14px;">
                        <span><?= date('M d, Y H:i', strtotime($post['created_at'])) ?></span>
                        <div style="display: flex; gap: 10px;">
                            <button onclick="editPost(<?= $post['post_id'] ?>)" class="btn" style="padding: 6px 16px; font-size: 13px;">Edit</button>
                            <button onclick="deletePost(<?= $post['post_id'] ?>)" style="padding: 6px 16px; background: #dc3545; color: white; border: none; border-radius: 16px; cursor: pointer; font-size: 13px; font-weight: 600;">Delete</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    <p style="font-size: 18px; margin-bottom: 10px;">📭 No posts yet</p>
                    <p>Start sharing your thoughts!</p>
                    <a href="posts.php" class="btn" style="display: inline-block; margin-top: 15px; text-decoration: none;">Create your first post</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function showEditProfile() {
            document.getElementById('edit-profile-form').style.display = 'block';
            window.scrollTo({top: document.getElementById('edit-profile-form').offsetTop - 100, behavior: 'smooth'});
        }
        
        function hideEditProfile() {
            document.getElementById('edit-profile-form').style.display = 'none';
        }
        
        function editPost(postId) {
            document.getElementById('content-' + postId).style.display = 'none';
            document.getElementById('edit-form-' + postId).style.display = 'block';
        }
        
        function cancelEdit(postId) {
            document.getElementById('content-' + postId).style.display = 'block';
            document.getElementById('edit-form-' + postId).style.display = 'none';
        }
        
        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'connect/delete-post.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'post_id';
                input.value = postId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>