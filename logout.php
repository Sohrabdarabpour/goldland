<?php
// goldland/logout.php
session_start();
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// بررسی آیا کاربر لاگین کرده است
if (!isLoggedIn()) {
    redirect('login.php');
}

// دریافت اطلاعات کاربر
$user_id = $_SESSION['user_id'];

if (isset($_COOKIE['remember_token'])) {
    // پیدا کردن کاربر با توکن
    $sql = "SELECT * FROM users WHERE remember_token = ? AND token_expires > NOW()";
    $stmt = executeQuery($sql, [$_COOKIE['remember_token']]);
    $user = $stmt->fetch();
    
    if ($user) {
        // حذف توکن از دیتابیس
        $sql = "UPDATE users SET remember_token = NULL, token_expires = NULL WHERE id = ?";
        executeQuery($sql, [$user['id']]);
    }
    
    // حذف کوکی
    setcookie('remember_token', '', time() - 3600, '/');
}


// تخریب تمام داده‌های session
$_SESSION = array();

// اگر می‌خواهید session را کاملاً تخریب کنید، کوکی session را نیز حذف کنید
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// در نهایت تخریب session
session_destroy();

// ریدایرکت به صفحه اصلی با پیام خروج موفق
flash('logout_success', 'شما با موفقیت از حساب کاربری خود خارج شدید.', 'alert alert-success');
redirect('login.php');