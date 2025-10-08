<?php
$servername = "localhost";
$dbname = "teht2";        // Tietokannan nimi
$username = "teht2";      // Käyttäjäntunnus
$password = "1234";       // Käyttäjän salasana

try {
    $yhteys = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Aseta virhetilan ilmoittaminen poikkeuksina
    $yhteys->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ei yhteyttä tietokantaan!<br>" . $e->getMessage();
    exit;  // Lopeta suoritus, jos yhteyttä ei saada
}
?>