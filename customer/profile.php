<?php
// goldland/customer/profile.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';
require_once '../includes/upload.php';

// بررسی لاگین بودن کاربر
if (!isLoggedIn() || !hasRole('customer')) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// دریافت اطلاعات کاربر فعلی
$user = getUserById($user_id);

// بررسی ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // دریافت داده‌های فرم
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = $_POST['gender'] ?? $user['gender'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');

    // اعتبارسنجی نام کاربری
    if (empty($username)) {
        $errors['username'] = 'نام کاربری الزامی است';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'نام کاربری فقط می‌تواند شامل حروف، اعداد و زیرخط باشد';
    } elseif ($username != $user['username'] && !isUsernameUnique($username)) {
        $errors['username'] = 'این نام کاربری قبلا ثبت شده است';
    }

    // اعتبارسنجی ایمیل
    if (empty($email)) {
        $errors['email'] = 'آدرس ایمیل الزامی است';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'آدرس ایمیل معتبر نیست';
    } elseif ($email != $user['email'] && !isEmailUnique($email)) {
        $errors['email'] = 'این ایمیل قبلا ثبت شده است';
    }

    // اعتبارسنجی شماره تلفن
    if (empty($phone)) {
        $errors['phone'] = 'شماره تلفن الزامی است';
    } elseif (!preg_match('/^09[0-9]{9}$/', $phone)) {
        $errors['phone'] = 'شماره تلفن معتبر نیست (فرمت صحیح: 09123456789)';
    }

    // اعتبارسنجی رمز عبور (اگر کاربر خواست تغییر دهد)
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors['current_password'] = 'برای تغییر رمز عبور، رمز فعلی را وارد کنید';
        } elseif (!verifyPassword($current_password, $user['password'])) {
            $errors['current_password'] = 'رمز عبور فعلی اشتباه است';
        } elseif (strlen($new_password) < 6) {
            $errors['new_password'] = 'رمز عبور جدید باید حداقل 6 کاراکتر باشد';
        } elseif ($new_password !== $confirm_password) {
            $errors['confirm_password'] = 'رمز عبور جدید و تکرار آن مطابقت ندارند';
        }
    }

    // آپلود عکس پروفایل
    try {
        if (!empty($_FILES['avatar']['name'])) {
            $avatar = uploadAvatar($_FILES['avatar'], $user_id, $gender);
        } else {
            $avatar = $user['avatar'];
        }
    } catch (Exception $e) {
        $errors['avatar'] = $e->getMessage();
    }

    // اگر خطایی وجود نداشت
    if (empty($errors)) {
        // آماده‌سازی داده‌ها برای به‌روزرسانی
        $data = [
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'gender' => $gender,
            'address' => $address,
            'avatar' => $avatar,
            'id' => $user_id
        ];

        // اگر رمز عبور جدید وارد شده، آن را اضافه کن
        if (!empty($new_password)) {
            $data['password'] = hashPassword($new_password);
        }

        // به‌روزرسانی اطلاعات کاربر
        if (updateUserProfile($data)) {
            $success = true;
            // به‌روزرسانی اطلاعات session
            $_SESSION['username'] = $username;
            // دریافت اطلاعات جدید کاربر
            $user = getUserById($user_id);
        } else {
            $errors['general'] = 'خطایی در به‌روزرسانی پروفایل رخ داد';
        }
    }
}

$page_title = "ویرایش پروفایل - " . $user['username'];
include '../includes/header.php';
?>

<!-- صفحه ویرایش پروفایل -->
<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <h1>ویرایش پروفایل</h1>
            <p>اطلاعات حساب کاربری خود را مدیریت کنید</p>
        </div>
        
        <div class="dashboard-grid">
            <?php include '../includes/customer-sidebar.php'; ?>
            
            <main class="dashboard-content">
                <?php if ($success): ?>
                    <div class="alert alert-success">پروفایل با موفقیت به‌روزرسانی شد</div>
                <?php endif; ?>
                
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger"><?= $errors['general'] ?></div>
                <?php endif; ?>
                
                <form action="profile.php" method="post" class="profile-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="avatar">تصویر پروفایل</label>
                        <div class="avatar-upload">
                            <div class="avatar-preview">
                                <img src="<?php echo BASE_URL . $user['avatar']; ?>" alt="پروفایل فعلی" id="avatarPreview">
                            </div>
                            <input type="file" name="avatar" id="avatar" accept="image/*">
                            <label for="avatar" class="btn btn-outline">انتخاب تصویر جدید</label>
                            <?php if (isset($errors['avatar'])): ?>
                                <div class="invalid-feedback d-block"><?= $errors['avatar'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">نام کاربری</label>
                            <input type="text" name="username" id="username" 
                                   class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($user['username']) ?>" required>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?= $errors['username'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">آدرس ایمیل</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">شماره تلفن</label>
                            <input type="tel" name="phone" id="phone" 
                                   class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($user['phone']) ?>" required>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>جنسیت</label>
                            <div class="gender-selection">
                                <div class="gender-option">
                                    <input type="radio" name="gender" id="male" value="male" <?= $user['gender'] === 'male' ? 'checked' : '' ?>>
                                    <label for="male">آقا</label>
                                </div>
                                <div class="gender-option">
                                    <input type="radio" name="gender" id="female" value="female" <?= $user['gender'] === 'female' ? 'checked' : '' ?>>
                                    <label for="female">خانم</label>
                                </div>
                                <div class="gender-option">
                                    <input type="radio" name="gender" id="other" value="other" <?= $user['gender'] === 'other' ? 'checked' : '' ?>>
                                    <label for="other">سایر</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">آدرس</label>
                        <textarea name="address" id="address" class="form-control" rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    
                    <div class="password-change-section">
                        <h3>تغییر رمز عبور</h3>
                        <p>برای تغییر رمز عبور، فیلدهای زیر را پر کنید. در غیر این صورت خالی بگذارید.</p>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="current_password">رمز عبور فعلی</label>
                                <input type="password" name="current_password" id="current_password" 
                                       class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>">
                                <?php if (isset($errors['current_password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['current_password'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">رمز عبور جدید</label>
                                <input type="password" name="new_password" id="new_password" 
                                       class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>">
                                <?php if (isset($errors['new_password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['new_password'] ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">تکرار رمز عبور جدید</label>
                                <input type="password" name="confirm_password" id="confirm_password" 
                                       class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                </form>
            </main>
        </div>
    </div>
</div>

<script>
// نمایش پیش‌نمایش عکس قبل از آپلود
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// اعتبارسنجی سمت کلاینت
document.querySelector('.profile-form').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword && newPassword !== confirmPassword) {
        e.preventDefault();
        alert('رمز عبور جدید و تکرار آن مطابقت ندارند');
        return false;
    }
    
    return true;
});
</script>

<?php include '../includes/footer.php'; ?>