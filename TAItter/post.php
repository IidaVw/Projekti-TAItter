<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/log.css">
    <script src="js/dark-light.js"></script>
    <title>New post</title>
</head>
<body>
    <h4>New post</h4>
    <form action="connect/send-post.php" method="post">
    <input type="text" name="title" id="title"><br>
    <textarea name="body" id="body"></textarea><br>
    <input type="image" name="image" id="image"><br>
    <input type="submit" value="Send">
</form>  
</body>
</html>