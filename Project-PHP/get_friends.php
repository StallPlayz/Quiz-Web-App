<?php
include 'auth.php';
require 'config.php'; // connect to your DB

$user_id = $_SESSION['user'];

$sql = "SELECT u.username FROM friends f 
        JOIN users u ON u.id = f.friend_id 
        WHERE f.user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
