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

$hashtag_count_sql = "SELECT COUNT(*) as count FROM followed_hashtags WHERE user_id = :user_id";
$hashtag_stmt = $yhteys->prepare($hashtag_count_sql);
$hashtag_stmt->execute([':user_id' => $user_id]);
$hashtag_count = $hashtag_stmt->fetch()['count'];

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
    <link rel="stylesheet" href="style/posts.css">
    <script defer src="js/dark-light.js"></script>
    <title><?= htmlspecialchars($user['username']) ?> - TAItter</title>
    <style>
        body {
            background: var(--bg-primary);
        }
        
        .profile-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-cover {
            position: relative;
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px 20px 0 0;
            overflow: hidden;
        }

        .profile-cover::before {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        .avatar-section {
            display: flex;
            align-items: flex-start;
            margin-top: 20px;
            gap: 20px;
        }

        .avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc143c 0%, #ff6b6b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 48px;
            border: 5px solid var(--bg-secondary);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            flex-shrink: 0;
            margin-top: -60px; /* Vain avatar ylhäällä */
        }
            
        .profile-main {
            background: var(--bg-secondary);
            border-radius: 0 0 20px 20px;
            box-shadow: 0 8px 32px var(--shadow-medium);
            margin-bottom: 30px;
        }
        
        .profile-header {
            position: relative;
            padding: 0 30px 30px;
        }
        
        .profile-info {
            flex: 1;
            padding-top: 20px;
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 5px 0;
        }
        
        .profile-username {
            color: var(--text-secondary);
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        .profile-bio {
            color: var(--text-primary);
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .profile-meta {
            display: flex;
            gap: 20px;
            color: var(--text-secondary);
            font-size: 14px;
            flex-wrap: wrap;
        }
        
        .profile-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            padding: 0 30px 30px;
        }
        
        .stat-card {
            background: var(--bg-tertiary);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px var(--shadow-medium);
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--accent-primary);
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Action Buttons */
        .profile-actions {
            display: flex;
            gap: 10px;
            padding: 0 30px 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 24px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--accent-primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--accent-primary);
            border: 2px solid var(--accent-primary);
        }
        
        .btn-secondary:hover {
            background: var(--accent-primary);
            color: white;
        }
        
        /* Posts Section */
        .posts-section {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px var(--shadow-light);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .post-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .post-card:hover {
            box-shadow: 0 4px 16px var(--shadow-medium);
            transform: translateY(-2px);
        }
        
        .post-content {
            color: var(--text-primary);
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 15px;
        }
        
        .edit-form {
            display: none;
            margin-bottom: 15px;
        }
        
        .edit-form.active {
            display: block;
        }
        
        .edit-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
            box-sizing: border-box;
            font-size: 15px;
        }
        
        .edit-textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.1);
        }
        
        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }
        
        .post-date {
            color: var(--text-secondary);
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .post-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-edit, .btn-delete, .btn-save, .btn-cancel {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: var(--accent-primary);
            color: white;
        }
        
        .btn-edit:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .btn-save {
            background: #28a745;
            color: white;
        }
        
        .btn-cancel {
            background: var(--bg-tertiary);
            color: var(--text-primary);
        }
        
        .no-posts {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .no-posts-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-posts h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--text-primary);
        }
        
        /* Edit Profile Form */
        #edit-profile-form {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px var(--shadow-light);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-primary);
            color: var(--text-primary);
            box-sizing: border-box;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.1);
        }
        
        .message {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            gap: 10px;
        }
    </style>
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
    
    <div class="profile-wrapper">
        <a href="posts.php" class="back-link">← Back to Feed</a>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="message success">
                ✓ <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="message error">
                ⚠ <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="profile-main">
            <div class="profile-cover"></div>
            
            <div class="profile-header">
                <div class="avatar-section">
                    <div class="avatar-large">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                        <p class="profile-username">@<?= htmlspecialchars($user['username']) ?></p>
                        <p class="profile-bio"><?= htmlspecialchars($user['bio'] ?: '✨ No bio yet. Click "Edit Profile" to add one!') ?></p>
                        <div class="profile-meta">
                            <span class="profile-meta-item">📅 Joined <?= date('F Y', strtotime($user['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?= count($posts) ?></span>
                    <span class="stat-label">Posts</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $following_count ?></span>
                    <span class="stat-label">Following</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $follower_count ?></span>
                    <span class="stat-label">Followers</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $hashtag_count ?></span>
                    <span class="stat-label">Hashtags</span>
                </div>
            </div>
            
            <div class="profile-actions">
                <button onclick="showEditProfile()" class="btn btn-primary">✏️ Edit Profile</button>
                <a href="posts.php" class="btn btn-secondary">🏠 Back to Feed</a>
                <a href="manage-follows.php" class="btn btn-secondary">👥 Manage Follows</a>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div id="edit-profile-form" style="display: none;">
            <h2 style="margin-bottom: 20px;">✏️ Edit Profile</h2>
            <form action="connect/update-profile.php" method="post">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" rows="4" class="form-input" placeholder="Tell us about yourself..."><?= htmlspecialchars($user['bio'] ?: '') ?></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <button type="button" onclick="hideEditProfile()" class="btn btn-secondary">✖ Cancel</button>
                </div>
            </form>
        </div>

        <!-- Posts Section -->
        <div class="posts-section">
            <div class="section-header">
                <h2 class="section-title">
                    📝 My Posts
                    <span style="background: var(--accent-primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px;"><?= count($posts) ?></span>
                </h2>
            </div>
            
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="post-content" id="content-<?= $post['post_id'] ?>">
                        <?= highlightContent(htmlspecialchars($post['content'])) ?>
                    </div>
                    
                    <div class="edit-form" id="edit-form-<?= $post['post_id'] ?>">
                        <form action="connect/update-post.php" method="post">
                            <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                            <textarea name="content" maxlength="144" required class="edit-textarea"><?= htmlspecialchars($post['content']) ?></textarea>
                            <div style="display: flex; gap: 8px; margin-top: 10px;">
                                <button type="submit" class="btn-save">💾 Save</button>
                                <button type="button" onclick="cancelEdit(<?= $post['post_id'] ?>)" class="btn-cancel">✖ Cancel</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="post-footer">
                        <span class="post-date">🕒 <?= date('M d, Y • H:i', strtotime($post['created_at'])) ?></span>
                        <div class="post-actions">
                            <button onclick="editPost(<?= $post['post_id'] ?>)" class="btn-edit">✏️ Edit</button>
                            <button onclick="deletePost(<?= $post['post_id'] ?>)" class="btn-delete">🗑️ Delete</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-posts">
                    <div class="no-posts-icon">📭</div>
                    <h3>No posts yet</h3>
                    <p>Share your first thought with the world!</p>
                    <a href="posts.php" class="btn btn-primary" style="margin-top: 20px; text-decoration: none;">✍️ Create your first post</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

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
            document.getElementById('edit-form-' + postId).classList.add('active');
        }
        
        function cancelEdit(postId) {
            document.getElementById('content-' + postId).style.display = 'block';
            document.getElementById('edit-form-' + postId).classList.remove('active');
        }
        
        function deletePost(postId) {
            if (confirm('🗑️ Are you sure you want to delete this post?\n\nThis action cannot be undone.')) {
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