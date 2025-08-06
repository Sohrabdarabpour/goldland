<?php
// goldland/includes/header.php

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="فروشگاه اینترنتی طلا و جواهرات GoldLand - خرید آنلاین انواع طلا و جواهر با بهترین قیمت">
    <title><?= $page_title ?? 'فروشگاه طلا GoldLand' ?></title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo BASE_URL; ?>assets/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo BASE_URL; ?>assets/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL; ?>assets/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo BASE_URL; ?>assets/images/favicon/site.webmanifest">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css?v=1.1">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/slick/slick.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendors/slick/slick-theme.css">

    <!-- JS -->
    <script src="<?php echo BASE_URL; ?>assets/vendors/jquery/jquery-3.6.0.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/vendors/slick/slick.min.js"></script>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "GoldLand",
        "url": "<?php echo BASE_URL; ?>",
        "logo": "<?php echo BASE_URL; ?>assets/images/logos/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+98-21-1234567",
            "contactType": "customer service"
        }
    }
    </script>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="contact-info">
                    <span><i class="fas fa-phone"></i> 021-1234567</span>
                    <span><i class="fas fa-envelope"></i> info@goldland.com</span>
                </div>
                <div class="user-actions">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>customer/dashboard.php"><i class="fas fa-user"></i> حساب کاربری من </a>
                        <a href="<?php echo BASE_URL; ?>logout.php" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> خروج
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login.php"><i class="fas fa-sign-in-alt"></i> ورود</a>
                        <a href="<?php echo BASE_URL; ?>register.php"><i class="fas fa-user-plus"></i> ثبت نام</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo BASE_URL; ?>index.php">
                        <img src="<?php echo BASE_URL; ?>assets/images/logos/logo.png" alt="GoldLand" width="150" height="50">
                    </a>
                </div>
                
                <div class="search-box">
                    <form action="<?php echo BASE_URL; ?>products.php" method="get">
                        <input type="text" name="search" placeholder="جستجوی محصولات..." aria-label="جستجوی محصولات">
                        <button type="submit" aria-label="جستجو"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="header-actions">
                    <a href="<?php echo BASE_URL; ?>wishlist.php" class="wishlist-icon" aria-label="لیست علاقه‌مندی‌ها">
                        <i class="far fa-heart"></i>
                        <span class="count wishlist-count">0</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>cart.php" class="cart-icon" aria-label="سبد خرید">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="count cart-count">0</span>
                    </a>
                    <div class="mobile-menu-toggle">
                        <i class="fas fa-bars"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav" aria-label="منوی اصلی">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>index.php">خانه</a></li>
                <li><a href="<?php echo BASE_URL; ?>products.php">محصولات</a></li>
                <li><a href="<?php echo BASE_URL; ?>about.php">درباره ما</a></li>
                <li><a href="<?php echo BASE_URL; ?>blog.php">وبلاگ</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact.php">تماس با ما</a></li>
            </ul>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <div class="close-menu" id="closeMenu">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <ul class="mobile-nav">
            <li><a href="<?php echo BASE_URL; ?>index.php">خانه</a></li>
            <li><a href="<?php echo BASE_URL; ?>products.php">محصولات</a></li>
            <li><a href="<?php echo BASE_URL; ?>about.php">درباره ما</a></li>
            <li><a href="<?php echo BASE_URL; ?>blog.php">وبلاگ</a></li>
            <li><a href="<?php echo BASE_URL; ?>contact.php">تماس با ما</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="<?php echo BASE_URL; ?>customer/dashboard.php">حساب کاربری</a></li>
                <li><a href="<?php echo BASE_URL; ?>logout.php">خروج</a></li>
            <?php else: ?>
                <li><a href="<?php echo BASE_URL; ?>login.php">ورود</a></li>
                <li><a href="<?php echo BASE_URL; ?>register.php">ثبت نام</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- نمایش پیام‌های فلت -->
        <?php 
        if (function_exists('flash')) {
            flash(); 
        }
        ?>