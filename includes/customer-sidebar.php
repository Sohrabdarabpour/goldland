<?php
// goldland/includes/customer-sidebar.php
if (!isset($_SESSION['user_id'])) {
    return;
}

$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

// بررسی مسیر عکس پروفایل
$avatar_path = !empty($user['avatar']) ? $user['avatar'] : 'assets/images/avatars/default-' . ($user['gender'] ?? 'other') . '.png';
$full_avatar_path = BASE_URL . $avatar_path;
?>
<aside class="dashboard-sidebar">
    <div class="user-profile">
        <div class="profile-image">
            <img src="<?php echo $full_avatar_path; ?>" alt="پروفایل کاربر" onerror="this.src='<?php echo BASE_URL; ?>assets/images/avatars/default-other.png'">
        </div>
        <div class="profile-info">
            <h3><?php echo htmlspecialchars($user['username']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <a href="<?php echo BASE_URL; ?>customer/profile.php" class="btn btn-outline btn-sm">ویرایش پروفایل</a>
        </div>
    </div>
    
    <nav class="dashboard-menu">
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>customer/dashboard.php"><i class="fas fa-home"></i> داشبورد</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>customer/orders.php"><i class="fas fa-shopping-bag"></i> سفارشات من</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'wishlist.php' ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>customer/wishlist.php"><i class="fas fa-heart"></i> لیست علاقه‌مندی‌ها</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'addresses.php' ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>customer/addresses.php"><i class="fas fa-map-marker-alt"></i> آدرس‌ها</a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>customer/profile.php"><i class="fas fa-user"></i> اطلاعات حساب</a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>logout.php"><i class="fas fa-sign-out-alt"></i> خروج</a>
            </li>
        </ul>
    </nav>
</aside>