<?php
// goldland/includes/functions.php

// تابع دریافت محصولات ویژه
function getFeaturedProducts($limit = 6) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 1 
            ORDER BY p.created_at DESC 
            LIMIT ?";
    $stmt = executeQuery($sql, [$limit]);
    return $stmt->fetchAll();
}

// تابع دریافت آخرین مطالب بلاگ
function getRecentPosts($limit = 3) {
    $sql = "SELECT * FROM contents 
            WHERE type = 'post' AND status = 'published' 
            ORDER BY published_at DESC 
            LIMIT ?";
    $stmt = executeQuery($sql, [$limit]);
    return $stmt->fetchAll();
}

// تابع محاسبه درصد تخفیف
function calculateDiscountPercentage($original_price, $discount_price) {
    if ($original_price <= 0 || $discount_price <= 0) return 0;
    return round((($original_price - $discount_price) / $original_price) * 100);
}

// تابع تبدیل تاریخ میلادی به شمسی
function jalaliDate($date) {
    require_once 'assets/vendors/jalali/jdf.php';
    return jdate('Y/m/d', strtotime($date));
}

// تابع بررسی لاگین بودن کاربر
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// تابع بررسی نقش کاربر
function hasRole($role) {
    if (!isLoggedIn()) return false;
    return $_SESSION['user_role'] === $role;
}

// تابع هش کردن پسورد
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// تابع اعتبارسنجی پسورد
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// تابع ریدایرکت
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// تابع نمایش پیام‌ها
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

// اعتبارسنجی شماره تلفن ایرانی
function validateIranianPhone($phone) {
    return preg_match('/^09[0-9]{9}$/', $phone);
}

// اعتبارسنجی ایمیل
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// اعتبارسنجی نام کاربری
function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{4,}$/', $username);
}

// اعتبارسنجی رمز عبور
function validatePassword($password) {
    return strlen($password) >= 6;
}

// بررسی تکراری نبودن ایمیل
function isEmailUnique($email, $exclude_id = null) {
    global $pdo;
    
    $sql = "SELECT id FROM users WHERE email = ?";
    $params = [$email];
    
    if ($exclude_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_id;
    }
    
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount() === 0;
}

// بررسی تکراری نبودن نام کاربری
function isUsernameUnique($username, $exclude_id = null) {
    global $pdo;
    
    $sql = "SELECT id FROM users WHERE username = ?";
    $params = [$username];
    
    if ($exclude_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_id;
    }
    
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount() === 0;
}

// دریافت اطلاعات کاربر
function getUserById($user_id) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = executeQuery($sql, [$user_id]);
    return $stmt->fetch();
}

// دریافت سفارشات کاربر
function getCustomerOrders($user_id, $limit = null) {
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = executeQuery($sql, [$user_id]);
    return $stmt->fetchAll();
}

// شمارش سفارشات فعال
function countActiveOrders($user_id) {
    $sql = "SELECT COUNT(*) FROM orders 
            WHERE user_id = ? 
            AND order_status IN ('processing', 'shipped')";
    $stmt = executeQuery($sql, [$user_id]);
    return $stmt->fetchColumn();
}

// دریافت محصولات مورد علاقه
function getWishlistItems($user_id, $limit = null) {
    $sql = "SELECT p.* FROM products p
            JOIN wishlist w ON p.id = w.product_id
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = executeQuery($sql, [$user_id]);
    return $stmt->fetchAll();
}

// شمارش محصولات مورد علاقه
function countWishlistItems($user_id) {
    $sql = "SELECT COUNT(*) FROM wishlist WHERE user_id = ?";
    $stmt = executeQuery($sql, [$user_id]);
    return $stmt->fetchColumn();
}

// شمارش آدرس‌های کاربر
function countUserAddresses($user_id) {
    $sql = "SELECT COUNT(*) FROM user_addresses WHERE user_id = ?";
    $stmt = executeQuery($sql, [$user_id]);
    return $stmt->fetchColumn();
}

// دریافت متن وضعیت سفارش
function getOrderStatusText($status) {
    $statuses = [
        'processing' => 'در حال پردازش',
        'shipped' => 'ارسال شده',
        'delivered' => 'تحویل داده شده',
        'cancelled' => 'لغو شده'
    ];
    return $statuses[$status] ?? $status;
}

// دریافت کلاس وضعیت سفارش
function getOrderStatusClass($status) {
    $classes = [
        'processing' => 'status-processing',
        'shipped' => 'status-shipped',
        'delivered' => 'status-delivered',
        'cancelled' => 'status-cancelled'
    ];
    return $classes[$status] ?? '';
}

// کد بخش خروج


/**
 * تابع برای نمایش پیام خروج موفق
 */
function showLogoutMessage() {
    if (isset($_SESSION['logout_success'])) {
        flash('logout_success');
        return true;
    }
    return false;
}
/*توابع بخش پروفایل */

/**
 * به‌روزرسانی پروفایل کاربر
 */
function updateUserProfile($data) {
    global $pdo;
    
    try {
        // اگر رمز عبور جدید وجود دارد
        if (isset($data['password'])) {
            $sql = "UPDATE users SET 
                    username = :username, 
                    email = :email, 
                    phone = :phone,
                    gender = :gender,
                    address = :address,
                    avatar = :avatar,
                    password = :password,
                    updated_at = NOW()
                    WHERE id = :id";
        } else {
            $sql = "UPDATE users SET 
                    username = :username, 
                    email = :email, 
                    phone = :phone,
                    gender = :gender,
                    address = :address,
                    avatar = :avatar,
                    updated_at = NOW()
                    WHERE id = :id";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("خطا در به‌روزرسانی پروفایل: " . $e->getMessage());
        }
        return false;
    }
}

/**
 * دریافت آیتم‌های یک سفارش
 */
function getOrderItems($order_id) {
    $sql = "SELECT oi.*, p.name, p.image 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?";
    $stmt = executeQuery($sql, [$order_id]);
    return $stmt->fetchAll();
}

function getTotalProducts() {
    global $pdo;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM products";
        $stmt = $pdo->query($sql);
        return $stmt->fetch()['total'];
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("خطا در دریافت تعداد محصولات: " . $e->getMessage());
        }
        return 0;
    }
}

function getAllCategories() {
    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("
            SELECT id, name, slug, description 
            FROM categories 
            WHERE status = 1
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("خطا در دریافت دسته‌بندی‌ها: " . $e->getMessage());
        return [];
    }
}

function getProductsByCategory($categoryId) {
    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT 
                p.id, p.name, p.description, 
                p.weight, p.purity, p.price,
                p.discount_price, p.image,
                p.stock, p.status
            FROM products p
            WHERE p.category_id = :category_id
            AND p.status = 1
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("خطا در دریافت محصولات: " . $e->getMessage());
        return [];
    }
}