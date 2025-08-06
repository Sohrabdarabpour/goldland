<?php
// goldland/customer/dashboard.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// بررسی لاگین بودن کاربر
if (!isLoggedIn() || !hasRole('customer')) {
    redirect('../login.php');
}

// دریافت اطلاعات کاربر
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

// دریافت آخرین سفارشات کاربر
$orders = getCustomerOrders($user_id, 3);

// دریافت محصولات مورد علاقه
$wishlist = getWishlistItems($user_id, 4);

$page_title = "داشبورد کاربری - " . $user['username'];
include '../includes/header.php';
?>

<!-- صفحه داشبورد -->
<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <h1>خوش آمدید، <?= htmlspecialchars($user['username']) ?></h1>
            <p>آخرین فعالیت‌های حساب کاربری شما</p>
        </div>
        
        <div class="dashboard-grid">
            <!-- سایدبار -->
            <aside class="dashboard-sidebar">
                <div class="user-profile">
                    <div class="profile-image">
                        <img src="../assets/images/users/default.jpg" alt="پروفایل کاربر">
                    </div>
                    <div class="profile-info">
                        <h3><?= htmlspecialchars($user['username']) ?></h3>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                        <a href="profile.php" class="btn btn-outline btn-sm">ویرایش پروفایل</a>
                    </div>
                </div>
                
                <nav class="dashboard-menu">
                    <ul>
                        <li class="active"><a href="dashboard.php"><i class="fas fa-home"></i> داشبورد</a></li>
                        <li><a href="orders.php"><i class="fas fa-shopping-bag"></i> سفارشات من</a></li>
                        <li><a href="wishlist.php"><i class="fas fa-heart"></i> لیست علاقه‌مندی‌ها</a></li>
                        <li><a href="addresses.php"><i class="fas fa-map-marker-alt"></i> آدرس‌ها</a></li>
                        <li><a href="profile.php"><i class="fas fa-user"></i> اطلاعات حساب</a></li>
                        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> خروج</a></li>
                    </ul>
                </nav>
            </aside>
            
            <!-- محتوای اصلی -->
            <main class="dashboard-content">
                <!-- خلاصه حساب -->
                <div class="dashboard-summary">
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="summary-info">
                            <h3>سفارشات فعال</h3>
                            <p><?= countActiveOrders($user_id) ?> سفارش</p>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="summary-info">
                            <h3>علاقه‌مندی‌ها</h3>
                            <p><?= countWishlistItems($user_id) ?> محصول</p>
                        </div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="summary-info">
                            <h3>آدرس‌ها</h3>
                            <p><?= countUserAddresses($user_id) ?> آدرس</p>
                        </div>
                    </div>
                </div>
                
                <!-- آخرین سفارشات -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>آخرین سفارشات</h2>
                        <a href="orders.php" class="view-all">مشاهده همه</a>
                    </div>
                    
                    <?php if (empty($orders)): ?>
                        <div class="alert alert-info">هنوز سفارشی ثبت نکرده‌اید</div>
                    <?php else: ?>
                        <div class="orders-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>شماره سفارش</th>
                                        <th>تاریخ</th>
                                        <th>مبلغ</th>
                                        <th>وضعیت</th>
                                        <th>عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td><?= jalaliDate($order['order_date']) ?></td>
                                        <td><?= number_format($order['total_amount']) ?> تومان</td>
                                        <td>
                                            <span class="status-badge <?= getOrderStatusClass($order['order_status']) ?>">
                                                <?= getOrderStatusText($order['order_status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order-details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline">مشاهده</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>
                
                <!-- محصولات مورد علاقه -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2>لیست علاقه‌مندی‌ها</h2>
                        <a href="wishlist.php" class="view-all">مشاهده همه</a>
                    </div>
                    
                    <?php if (empty($wishlist)): ?>
                        <div class="alert alert-info">لیست علاقه‌مندی‌های شما خالی است</div>
                    <?php else: ?>
                        <div class="wishlist-grid">
                            <?php foreach ($wishlist as $item): ?>
                            <div class="wishlist-item">
                                <div class="wishlist-image">
                                    <img src="../uploads/products/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
                                </div>
                                <div class="wishlist-info">
                                    <h3><a href="../product-details.php?id=<?= $item['id'] ?>"><?= $item['name'] ?></a></h3>
                                    <div class="product-price">
                                        <?php if($item['discount_price']): ?>
                                            <span class="price-old"><?= number_format($item['price']) ?> تومان</span>
                                            <span class="price-new"><?= number_format($item['discount_price']) ?> تومان</span>
                                        <?php else: ?>
                                            <span class="price"><?= number_format($item['price']) ?> تومان</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="wishlist-actions">
                                    <button class="btn-remove-wishlist" data-product-id="<?= $item['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn-add-to-cart" data-product-id="<?= $item['id'] ?>">
                                        <i class="fas fa-shopping-cart"></i> افزودن به سبد
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>
</div>

<script>
// حذف از لیست علاقه‌مندی‌ها
$(document).on('click', '.btn-remove-wishlist', function() {
    const product_id = $(this).data('product-id');
    const item = $(this).closest('.wishlist-item');
    
    $.ajax({
        url: '../ajax/remove_wishlist.php',
        method: 'POST',
        data: { product_id: product_id },
        success: function(response) {
            if (response.success) {
                item.fadeOut(300, function() {
                    $(this).remove();
                    updateWishlistCount();
                });
            }
        }
    });
});

// افزودن به سبد خرید
$(document).on('click', '.btn-add-to-cart', function() {
    const product_id = $(this).data('product-id');
    
    $.ajax({
        url: '../ajax/add_to_cart.php',
        method: 'POST',
        data: { product_id: product_id, quantity: 1 },
        success: function(response) {
            if (response.success) {
                updateCartCount();
                showToast('محصول به سبد خرید اضافه شد');
            }
        }
    });
});

function updateWishlistCount() {
    $.ajax({
        url: '../ajax/get_wishlist_count.php',
        method: 'GET',
        success: function(response) {
            $('.wishlist-count').text(response.count);
        }
    });
}

function updateCartCount() {
    $.ajax({
        url: '../ajax/get_cart_count.php',
        method: 'GET',
        success: function(response) {
            $('.cart-count').text(response.count);
        }
    });
}

function showToast(message) {
    const toast = $('<div class="toast">' + message + '</div>');
    $('body').append(toast);
    toast.fadeIn(300).delay(3000).fadeOut(300, function() {
        $(this).remove();
    });
}
</script>

<?php include '../includes/footer.php'; ?>