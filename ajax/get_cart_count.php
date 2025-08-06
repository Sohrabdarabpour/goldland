<?php
// goldland/ajax/get_cart_count.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$count = 0;

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT SUM(quantity) FROM cart WHERE user_id = ?";
    $stmt = executeQuery($sql, [$user_id]);
    $count = (int)$stmt->fetchColumn();
} elseif (isset($_SESSION['cart'])) {
    $count = count($_SESSION['cart']);
}

echo json_encode(['count' => $count]);