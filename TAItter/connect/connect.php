<?php
    $host="localhost";
    $dbuser="sql-user";
    $dbpassword="sql-user";
    $db="sql-inject";

    try {
        $conn = new PDO("mysql:host=$host; dbname=$db", $dbuser, $dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOExceptio $e) {
        die("Virhe: " . $e->getMessage());
    }
?>