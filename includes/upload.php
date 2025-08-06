<?php
// goldland/includes/upload.php
function uploadAvatar($file, $user_id, $gender = 'other') {
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/goldland/uploads/avatars/';
    
    // ایجاد پوشه اگر وجود ندارد
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // اگر فایلی آپلود نشده، از آواتار پیش‌فرض استفاده کن
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        $default_avatar = ($gender === 'male') ? 'default-male.png' : 
                        (($gender === 'female') ? 'default-female.png' : 'default-other.png');
        return 'assets/images/avatars/' . $default_avatar;
    }

    // اعتبارسنجی فایل آپلود شده
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('فقط فایل‌های JPEG, PNG و GIF مجاز هستند');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('حجم فایل نباید بیشتر از 2 مگابایت باشد');
    }

    // تغییر نام فایل برای امنیت بیشتر
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
    $target_path = $upload_dir . $filename;

    // آپلود فایل
    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        throw new Exception('خطا در آپلود فایل');
    }

    return 'uploads/avatars/' . $filename;
}