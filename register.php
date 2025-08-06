<?php
// goldland/register.php
session_start();
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$errors = [];
$success = false;

// بررسی ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // دریافت و اعتبارسنجی داده‌ها
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // اعتبارسنجی نام کاربری
    if (empty($username)) {
        $errors['username'] = 'نام کاربری الزامی است';
    } elseif (strlen($username) < 4) {
        $errors['username'] = 'نام کاربری باید حداقل 4 کاراکتر باشد';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'نام کاربری فقط می‌تواند شامل حروف، اعداد و زیرخط باشد';
    } else {
        // بررسی تکراری نبودن نام کاربری
        $stmt = executeQuery("SELECT id FROM users WHERE username = ?", [$username]);
        if ($stmt->rowCount() > 0) {
            $errors['username'] = 'این نام کاربری قبلا ثبت شده است';
        }
    }

    // اعتبارسنجی ایمیل
    if (empty($email)) {
        $errors['email'] = 'آدرس ایمیل الزامی است';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'آدرس ایمیل معتبر نیست';
    } else {
        // بررسی تکراری نبودن ایمیل
        $stmt = executeQuery("SELECT id FROM users WHERE email = ?", [$email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = 'این ایمیل قبلا ثبت شده است';
        }
    }

    // اعتبارسنجی رمز عبور
    if (empty($password)) {
        $errors['password'] = 'رمز عبور الزامی است';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'رمز عبور باید حداقل 6 کاراکتر باشد';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'رمز عبور و تکرار آن مطابقت ندارند';
    }

    // اعتبارسنجی شماره تلفن
    if (empty($phone)) {
        $errors['phone'] = 'شماره تلفن الزامی است';
    } elseif (!preg_match('/^09[0-9]{9}$/', $phone)) {
        $errors['phone'] = 'شماره تلفن معتبر نیست (فرمت صحیح: 09123456789)';
    }

    // اگر خطایی وجود نداشت
    if (empty($errors)) {
        // هش کردن رمز عبور
        $hashed_password = hashPassword($password);
        
        // ذخیره کاربر در پایگاه داده
        // در بخش ثبت کاربر جدید:
        $gender = $_POST['gender'] ?? 'other';
        $avatar = 'assets/images/avatars/default-' . $gender . '.png';

        // در کوئری INSERT:
        $sql = "INSERT INTO users (username, email, password, phone, address, gender, avatar, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'customer')";
        $params = [$username, $email, $hashed_password, $phone, $address, $gender, $avatar];
        
        $stmt = executeQuery($sql, $params);
        
        if ($stmt->rowCount() > 0) {
            $success = true;
            
            // ورود خودکار کاربر بعد از ثبت نام
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_role'] = 'customer';
            $_SESSION['username'] = $username;
            
            // ریدایرکت به صفحه حساب کاربری
            redirect('customer/dashboard.php');
        } else {
            $errors['general'] = 'خطایی در ثبت نام رخ داد. لطفاً مجدداً تلاش کنید.';
        }
    }
}

$page_title = "ثبت نام در GoldLand";
include 'includes/header.php';
?>

<!-- صفحه ثبت نام -->
<section class="auth-section">
    <div class="container">
        <div class="auth-form-container">
            <div class="auth-form-header">
                <h2>ثبت نام در GoldLand</h2>
                <p>حساب کاربری جدید ایجاد کنید</p>
            </div>
            
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= $errors['general'] ?></div>
            <?php endif; ?>
            
            <form action="register.php" method="post" class="auth-form" id="register-form">
                <div class="form-group">
                    <label for="username">نام کاربری</label>
                    <input type="text" name="username" id="username" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="invalid-feedback"><?= $errors['username'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email">آدرس ایمیل</label>
                    <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">رمز عبور</label>
                    <input type="password" name="password" id="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= $errors['password'] ?></div>
                    <?php endif; ?>
                    <small class="form-text text-muted">حداقل 6 کاراکتر</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">تکرار رمز عبور</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group gender-selection">
                <div class="gender-option">
                    <input type="radio" name="gender" id="male" value="male" <?= ($gender ?? '') === 'male' ? 'checked' : '' ?>>
                    <label for="male">آقا</label>
                </div>
                <div class="gender-option">
                    <input type="radio" name="gender" id="female" value="female" <?= ($gender ?? '') === 'female' ? 'checked' : '' ?>>
                    <label for="female">خانم</label>
                </div>
                <div class="gender-option">
                    <input type="radio" name="gender" id="other" value="other" <?= ($gender ?? 'other') === 'other' ? 'checked' : '' ?>>
                    <label for="other">سایر</label>
                </div>
            </div>
                
                <div class="form-group">
                    <label for="phone">شماره تلفن</label>
                    <input type="tel" name="phone" id="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                    <?php endif; ?>
                    <small class="form-text text-muted">مثال: 09123456789</small>
                </div>
                
                <div class="form-group">
                    <label for="address">آدرس</label>
                    <textarea name="address" id="address" class="form-control" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                    <label for="terms" class="form-check-label">با <a href="terms.php">قوانین و شرایط</a> استفاده از سایت موافقم</label>
                    <?php if (isset($errors['terms'])): ?>
                        <div class="invalid-feedback d-block"><?= $errors['terms'] ?></div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">ثبت نام</button>
            </form>
            
            <div class="auth-form-footer">
                <p>قبلاً حساب کاربری دارید؟ <a href="login.php">وارد شوید</a></p>
            </div>
        </div>
    </div>
</section>

<script>
// اعتبارسنجی سمت کلاینت
document.getElementById('register-form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm_password = document.getElementById('confirm_password').value;
    const terms = document.getElementById('terms').checked;
    
    if (password !== confirm_password) {
        e.preventDefault();
        alert('رمز عبور و تکرار آن مطابقت ندارند');
        return false;
    }
    
    if (!terms) {
        e.preventDefault();
        alert('لطفاً با قوانین و شرایط موافقت کنید');
        return false;
    }
    
    return true;
});
</script>

<?php include 'includes/footer.php'; ?>