<?php
session_start();
require_once 'connect.php';

// Tarkista onko käyttäjä kirjautunut
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Hae hakuparametri (jos annettu)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        // Hae käyttäjät hakusanalla
        $sql = "SELECT user_id, username, first_name, last_name, bio 
                FROM users 
                WHERE username LIKE :search 
                OR first_name LIKE :search 
                OR last_name LIKE :search
                ORDER BY username ASC";
        $stmt = $yhteys->prepare($sql);
        $stmt->execute([':search' => "%$search%"]);
    } else {
        // Hae kaikki käyttäjät
        $sql = "SELECT user_id, username, first_name, last_name, bio 
                FROM users 
                ORDER BY username ASC";
        $stmt = $yhteys->query($sql);
    }
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Palauta JSON
    header('Content-Type: application/json');
    echo json_encode($users);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>