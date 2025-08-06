<?php
// goldland/includes/config.php


// تنظیمات پایه
define('BASE_URL', 'http://localhost/goldland/');
define('SITE_NAME', 'GoldLand');
define('SITE_DESC', 'فروشگاه اینترنتی طلا و جواهرات GoldLand');

// تنظیمات پایگاه داده
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'goldland_db');

// تنظیمات ایمیل
define('EMAIL_HOST', 'smtp.example.com');
define('EMAIL_USER', 'info@goldland.com');
define('EMAIL_PASS', 'email_password');
define('EMAIL_PORT', 587);

// سایر تنظیمات
define('DEBUG_MODE', true);
define('CURRENCY', 'تومان');
define('TAX_RATE', 0.09); // نرخ مالیات

// شروع session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// تنظیم منطقه زمانی
date_default_timezone_set('Asia/Tehran');

// نمایش خطاها در حالت توسعه
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}