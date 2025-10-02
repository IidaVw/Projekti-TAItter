<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/log.css">
    <script src="js/dark-light.js"></script>
    <title>Log in - TAItter</title>
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
        <h2>Log in</h2>
        <form action="connect/log.php" method="post" class="log-form">
            <div class="form-group">
                <label for="usernamex">Username:</label>
                <input type="text" name="username" id="usernamex" required>
            </div>
            <div class="form-group">
                <label for="passwordx">Password:</label>
                <input type="password" name="password" id="passwordx" required>
            </div>
            <input type="submit" value="Log In">
            <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        </form>
    </section>
</body>
</html>