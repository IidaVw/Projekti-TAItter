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
    <script src="js/dark-light.js"></script>
    <title>New post - TAItter</title>
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
    
    <section class="content">
        <h2>New post</h2>
        
        <?php
        if (isset($_GET['error'])) {
            echo '<p style="color: red; text-align: center;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        if (isset($_GET['success'])) {
            echo '<p style="color: green; text-align: center;">' . htmlspecialchars($_GET['success']) . '</p>';
        }
        ?>
        
        <form action="connect/send-post.php" method="post">
            <textarea name="content" maxlength="144" placeholder="What's happening? (max 144 characters)" required style="width: 100%; min-height: 150px; padding: 10px; border-radius: 5px;"></textarea><br>
            <small id="char-count">0/144</small><br><br>
            <input type="submit" value="Post">
            <a href="posts.php" style="margin-left: 10px;">Cancel</a>
        </form>
    </section>
    
    <script>
        const textarea = document.querySelector('textarea[name="content"]');
        const charCount = document.getElementById('char-count');
        
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length + '/144';
        });
    </script>
</body>
</html>