<?php
// goldland/login.php
session_start();
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// اگر کاربر از قبل لاگین کرده باشد، به داشبورد هدایت شود
if (isLoggedIn()) {
    redirect('customer/dashboard.php');
}

// نمایش پیام خروج موفق
showLogoutMessage();
$errors = [];

// بررسی ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // اعتبارسنجی
    if (empty($username)) {
        $errors['username'] = 'نام کاربری یا ایمیل الزامی است';
    }
    
    if (empty($password)) {
        $errors['password'] = 'رمز عبور الزامی است';
    }

    // اگر خطایی وجود نداشت
    if (empty($errors)) {
        // پیدا کردن کاربر با نام کاربری یا ایمیل
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = executeQuery($sql, [$username, $username]);
        $user = $stmt->fetch();

        // بررسی وجود کاربر و تطابق رمز عبور
        if ($user && verifyPassword($password, $user['password'])) {
            // ثبت اطلاعات کاربر در session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            // اگر گزینه "مرا به خاطر بسپار" فعال بود
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + 60 * 60 * 24 * 30; // 30 روز
                
                setcookie('remember_token', $token, $expires, '/');
                
                // ذخیره توکن در دیتابیس
                $sql = "UPDATE users SET remember_token = ?, token_expires = ? WHERE id = ?";
                executeQuery($sql, [$token, date('Y-m-d H:i:s', $expires), $user['id']]);
            }

            // ریدایرکت بر اساس نقش کاربر
            switch ($user['role']) {
                case 'admin':
                    redirect('admin/dashboard.php');
                    break;
                case 'content_manager':
                    redirect('content-manager/dashboard.php');
                    break;
                default:
                    redirect('customer/dashboard.php');
            }
        } else {
            $errors['general'] = 'نام کاربری یا رمز عبور اشتباه است';
        }
    }
}

$page_title = "ورود به حساب کاربری - GoldLand";
include 'includes/header.php';
?>

<!-- صفحه ورود -->
<section class="auth-section">
    <div class="container">
        <div class="auth-form-container">
            <div class="auth-form-header">
                <h2>ورود به حساب کاربری</h2>
                <p>لطفاً اطلاعات خود را وارد کنید</p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= $errors['general'] ?></div>
            <?php endif; ?>

            <form action="login.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">نام کاربری یا ایمیل</label>
                    <input type="text" name="username" id="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback"><?= $errors['username'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">رمز عبور</label>
                    <input type="password" name="password" id="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                    <div class="text-left mt-2">
                        <a href="forgot-password.php" class="text-small">رمز عبور را فراموش کرده‌اید؟</a>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">مرا به خاطر بسپار</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">ورود</button>
            </form>

            <div class="auth-form-footer">
                <p>حساب کاربری ندارید؟ <a href="register.php">ثبت نام کنید</a></p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>