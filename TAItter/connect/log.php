<?php

include 'connect.php';
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = :username AND password = :password;";
try {
    $query = $conn->prepare($sql);
    $query->execute(['username'=>$username, 'password'=>$password]);
}
catch (PDOException $e) {
    $conn = null;
    $query = null;
    die("Virhe: " . $e->getMessage());
}
$user = $query->fetchAll();

if (count($user)) {
    $sql = "SELECT * FROM users;";
    try {
        $query = $conn->prepare($sql);
        $query->execute();
    }
    catch (PDOException $e) {
        $conn = null;
        $query = null;
        die("Virhe: " . $e->getMessage());
    }
    $users = $query->fetchAll();
    $conn = null;
    $query = null;
    session_start();
    $_SESSION['user'] = $user[0];
    $_SESSION['users'] = $users;
    session_regenerate_id(True);
    header('Location: home.php');
    // exit;
} else {
    header('Location: index.php');
}
?>