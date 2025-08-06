<?php
// goldland/ajax/check_username.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_POST['username']) || empty($_POST['username'])) {
    echo json_encode(['available' => false]);
    exit;
}

$username = trim($_POST['username']);
$available = isUsernameUnique($username);

echo json_encode(['available' => $available]);