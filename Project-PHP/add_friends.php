<?php
include 'auth.php';
require 'config.php';

$user_id = $_SESSION['user'];
$username = $_GET['username'] ?? '';

if (!$username) {
    die("No username provided");
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->execute([$username, $user_id]);
$friend = $stmt->fetch();

if (!$friend) {
    die("User not found or cannot add yourself");
}

$friend_id = $friend['id'];

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO friends (user_id, friend_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $friend_id]);
    echo "Friend added!";
} catch (Exception $e) {
    echo "Error adding friend";
}
