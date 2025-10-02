<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/posts.css">
  <script defer src="js/dark-light.js"></script>
  <script defer src="js/posts.js"></script>
  <title>Posts</title>
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

            <button class="theme-toggle" onclick="toggleTheme()">
            <span class="theme-icon moon" id="theme-icon">🌙</span>
            </button>
        </div>
    </header>

  <section class="container">
        <!-- Comment Sidebar -->
        <div class="comment-section">
            <h2>Comments</h2>

            <div class="comment-list">
                <p>Select a post to view comments 💬</p>
                <!-- Individual comments will go here -->
            </div>

            <!-- Comment form at the bottom -->
            <div class="comment-form">
                <textarea placeholder="Write a comment..."></textarea>
                <button>Send</button>
            </div>
        </div>

        <!-- Center Feed -->
        <div class="feed">
        <!-- Post 1 -->
        <div class="post-section" data-id="1">
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
            <div class="action comment-btn">💬 12</div>
            <div class="action">🔄 45</div>
            <div class="action">❤️ 128</div>
            <div class="action">📤 Share</div>
            </div>
        </div>

        <!-- Post 2 with image -->
        <div class="post-section" data-id="2">
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
                <div class="action comment-btn">💬 18</div>
                <div class="action">🔄 75</div>
                <div class="action">❤️ 202</div>
                <div class="action">📤 Share</div>
            </div>
        </div>

        <!-- Post 3 with multiple images -->
        <div class="post-section" data-id="3">
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
                <div class="action comment-btn">💬 7</div>
                <div class="action">🔄 33</div>
                <div class="action">❤️ 150</div>
                <div class="action">📤 Share</div>
            </div>
        </div>
        </div>

        <!-- Friends Sidebar -->
        <div class="friend-section">
        <h2>Friends</h2>
        <p>Coming soon… 👥</p>
        </div>
  </section>
</body>
</html>
