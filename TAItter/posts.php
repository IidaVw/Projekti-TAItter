<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/posts.css">
    <script src="js/dark-light.js"></script>
    <title>Posts</title>
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

    <section class="container">
        <div class="comment-section">

        </div>

        <div class="post-section">
            <div class="user">
                <div class="avatar">AA</div>
                <div class="user-info">
                    <div class="username">Autsku</div>
                    <div class="handle">@autsku</div>
                </div>
            </div>
            <div class="tweet-text">
                Hello! I'm new to TAItter 🚀 I hope to help this community. #awesome
            </div>
            <div class="tweet-actions">
                <div class="action">💬 12</div>
                <div class="action">🔄 45</div>
                <div class="action">❤️ 128</div>
                <div class="action">📤 Share</div>
            </div>
        </div>

        <!-- Post with single image -->
        <div class="post-section">
            <div class="user">
                <div class="avatar">DU</div>
                <div class="user-info">
                    <div class="username">Demo User</div>
                    <div class="handle">@demo_user</div>
                </div>
            </div>
            <div class="tweet-text">
                Just tried the new AI content suggestions feature – it’s amazing how it understands exactly what I’m interested in! 🔥 #TAItter #AI
            </div>
            <div class="tweet-image">
                <img src="img/3.jpg" alt="Demo Post Image">
            </div>
            <div class="tweet-actions">
                <div class="action">💬 18</div>
                <div class="action">🔄 75</div>
                <div class="action">❤️ 202</div>
                <div class="action">📤 Share</div>
            </div>
        </div>

        <!-- Post with multiple images -->
        <div class="post-section">
            <div class="user">
                <div class="avatar">AI</div>
                <div class="user-info">
                    <div class="username">TAItter Assistant</div>
                    <div class="handle">@taitter_ai</div>
                </div>
            </div>
            <div class="tweet-text">
                Here's how the new trending dashboard looks 👇
            </div>
            <div class="tweet-gallery">
                <img src="img/1.jpg" alt="Trending 1">
                <img src="img/2.jpg" alt="Trending 2">
            </div>
            <div class="tweet-actions">
                <div class="action">💬 7</div>
                <div class="action">🔄 33</div>
                <div class="action">❤️ 150</div>
                <div class="action">📤 Share</div>
            </div>
        </div>

        <div class="friend-section">

        </div>

    </section>
</body>
</html>