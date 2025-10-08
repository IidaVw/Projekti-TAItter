<?php
session_start();
require_once 'connect/connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/log.css">
    <script defer src="js/dark-light.js"></script>
    <title>New post - TAItter</title>
    <style>
        .content {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: var(--bg-secondary);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .message.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .message.success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        textarea {
            width: 100%;
            min-height: 150px;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 16px;
            resize: vertical;
            box-sizing: border-box;
        }
        
        textarea:focus {
            outline: none;
            border-color: #dc143c;
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.1);
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .btn-post {
            padding: 12px 30px;
            background: #dc143c;
            color: white;
            border: none;
            border-radius: 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-post:hover {
            background: #a70b2a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 20, 60, 0.3);
        }
        
        .btn-cancel {
            padding: 12px 30px;
            background: transparent;
            color: #6c757d;
            border: 2px solid #dee2e6;
            border-radius: 24px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            border-color: #dc143c;
            color: #dc143c;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="posts.php" class="logo"><span class="logo-highlight">TAI</span>tter</a>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="color: var(--text-primary);">Welcome, <?= htmlspecialchars($_SESSION['first_name']) ?>!</span>
                <a href="posts.php" style="color: #dc143c; text-decoration: none; font-weight: 600;">← Back to Feed</a>
                <button class="theme-toggle" onclick="toggleTheme()">
                    <span class="theme-icon moon" id="theme-icon">🌙</span>
                </button>
            </div>
        </div>
    </header>
    
    <section class="content">
        <h2>✍️ Create a new post</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="message error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="message success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        
        <form action="connect/send-post.php" method="post">
            <textarea 
                name="content" 
                maxlength="144" 
                placeholder="What's happening? Share your thoughts... (max 144 characters)" 
                required
                autofocus
            ></textarea>
            
            <div class="form-footer">
                <small id="char-count" style="color: #6c757d;">0/144</small>
                <div>
                    <a href="posts.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-post">Post</button>
                </div>
            </div>
        </form>
    </section>
    
    <script>
        const textarea = document.querySelector('textarea[name="content"]');
        const charCount = document.getElementById('char-count');
        
        if (textarea && charCount) {
            textarea.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length + '/144';
                
                // Vaihda väriä kun lähestytään rajaa
                if (length > 130) {
                    charCount.style.color = '#dc143c';
                    charCount.style.fontWeight = 'bold';
                } else if (length > 100) {
                    charCount.style.color = '#ff6b00';
                } else {
                    charCount.style.color = '#6c757d';
                    charCount.style.fontWeight = 'normal';
                }
            });
        }
    </script>
</body>
</html>