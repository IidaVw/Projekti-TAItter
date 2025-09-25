<?php
    session_start();
    if(!array_key_exists('user', $_SESSION)) {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/user.css">
    <script src="js/dark-light.js"></script>
    <title>Super foorumi</title>
</head>
<body>
    <h1>Super foorumi</h1>
    <p>
        Welcome! <?php echo $_SESSION['user']['realname']; ?>
        <a href="logout.php"><button>Kirjaudu ulos</button></a>
    </p>
    <?php
    include 'posts.php';
    include 'post-form.php';
    ?>
</body>
</html>