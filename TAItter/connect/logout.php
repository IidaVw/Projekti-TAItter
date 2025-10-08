<?php
session_start();

// Tuhoa kaikki session tiedot
$_SESSION = array();

// Tuhoa session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Tuhoa session
session_destroy();

// Ohjaa takaisin etusivulle
header("Location: ../index.php");
exit;
?>