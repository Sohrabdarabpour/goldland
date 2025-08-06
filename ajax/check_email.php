<?php
// goldland/ajax/check_email.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_POST['email']) || empty($_POST['email'])) {
    echo json_encode(['available' => false]);
    exit;
}

$email = trim($_POST['email']);
$available = isEmailUnique($email);

echo json_encode(['available' => $available]);