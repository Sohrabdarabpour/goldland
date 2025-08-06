<?php
// goldland/ajax/get_wishlist_count.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$count = 0;

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $count = countWishlistItems($user_id);
}

echo json_encode(['count' => $count]);<?php
// goldland/ajax/get_wishlist_count.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$count = 0;

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $count = countWishlistItems($user_id);
}

echo json_encode(['count' => $count]);