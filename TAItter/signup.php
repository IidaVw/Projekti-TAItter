<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/sign.css">
    <script src="js/dark-light.js"></script>
    <title>Sign up - TAItter</title>
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
        <h2>Sign up</h2>
        
        <?php
        // Näytä virheilmoitus jos on
        if (isset($_GET['error'])) {
            echo '<p style="color: red; text-align: center;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>
        
        <form action="connect/signup-process.php" method="post" class="sign-form">
            <div class="form-group">
                <label for="fname">First name:</label>
                <input type="text" id="fname" name="fname" required>
            </div>
            <div class="form-group">
                <label for="lname">Last name:</label>
                <input type="text" id="lname" name="lname" required>
            </div>
            <div class="form-group">
                <label for="age">Date of birth:</label>
                <input type="date" id="age" name="age" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="bio">Bio (description):</label>
                <textarea id="bio" name="bio" rows="3" placeholder="Tell us about yourself..."></textarea>
            </div>
            <input type="submit" value="Sign Up">
            <p>You already have an account? <a href="login.php">Log in</a></p>
        </form>
    </section>
</body>
</html>