<?php
// goldland/ajax/remove_wishlist.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'لطفاً ابتدا وارد شوید']);
    exit;
}

if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'محصول نامعتبر']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];

// حذف از لیست علاقه‌مندی‌ها
$sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt = executeQuery($sql, [$user_id, $product_id]);

echo json_encode(['success' => true]);