<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TAItter</title>
    <script src="js/index.js"></script>
    <link rel="stylesheet" href="style/index.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="#" class="logo"><span class="logo-highlight">TAI</span>tter</a> 
            <button class="theme-toggle" onclick="toggleTheme()">
                <span id="theme-icon">🌙</span>
                <span id="theme-text">Dark mode</span>
            </button>
        </div>
    </header>

    <main class="main-container">
        <section class="hero-section">
            <h1 class="hero-title">Connect with other users on the platform!</h1>
            <p class="hero-subtitle">
                TAItter brings together social networking and artificial intelligence to create meaningful connections and smarter discussions.
            </p>
            
            <div class="cta-buttons">
                <a href="#sign.php" class="btn-primary">Join TAItter</a>
                <a href="#info.php" class="btn-secondary">Learn more</a>
            </div>

            <ul class="features-list">
                <li>AI-enhanced content discovery</li>
                <li>Smart conversation suggestions</li>
                <li>Automated content moderation</li>
                <li>Personalized trending topics</li>
            </ul>
        </section>

        <section class="demo-section">
            <h2 class="demo-title">See TAItter in action</h2>
            <div class="demo-content">
                <div class="tweet-preview">
                    <div class="tweet-user">
                        <div class="avatar">AI</div>
                        <div class="user-info">
                        <div class="username">TAItter Assistant</div>
                        <div class="handle">@taitter_ai</div>
                    </div>
                </div>
                <div class="tweet-text">
                    Welcome to TAItter! 🚀 Our AI helps you discover relevant content, connect with like-minded people, and join conversations that matter to you.
                </div>
                <div class="tweet-actions">
                    <div class="action">💬 Reply</div>
                    <div class="action">🔄 Retweet</div>
                    <div class="action">❤️ Like</div>
                    <div class="action">📤 Share</div>
                </div>
            </div>

                <div class="tweet-preview">
                    <div class="tweet-user">
                        <div class="avatar">U</div>
                        <div class="user-info">
                        <div class="username">Demo User</div>
                        <div class="handle">@demo_user</div>
                    </div>
                </div>
                <div class="tweet-text">
                    Just tried the new AI content suggestions feature - it's amazing how it understands exactly what I'm interested in! #TAItter #AI
                </div>
                <div class="tweet-actions">
                    <div class="action">💬 12</div>
                    <div class="action">🔄 45</div>
                    <div class="action">❤️ 128</div>
                    <div class="action">📤 Share</div>
                </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>© 2025 TAItter. All rights reserved.</p>
    </footer>
</body>
</html>