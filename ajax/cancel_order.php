<?php
// goldland/ajax/cancel_order.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'لطفاً ابتدا وارد شوید']);
    exit;
}

if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'سفارش نامعتبر']);
    exit;
}

$order_id = (int)$_POST['order_id'];
$user_id = $_SESSION['user_id'];

// بررسی مالکیت سفارش
$sql = "SELECT id FROM orders WHERE id = ? AND user_id = ?";
$stmt = executeQuery($sql, [$order_id, $user_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'سفارش یافت نشد']);
    exit;
}

// بروزرسانی وضعیت سفارش
try {
    $sql = "UPDATE orders SET order_status = 'cancelled' WHERE id = ?";
    $stmt = executeQuery($sql, [$order_id]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'خطا در لغو سفارش']);
}