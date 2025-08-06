<?php
// goldland/customer/orders.php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// بررسی لاگین بودن کاربر
if (!isLoggedIn() || !hasRole('customer')) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// دریافت سفارشات کاربر
$orders = getCustomerOrders($user_id);

$page_title = "سفارشات من - " . $_SESSION['username'];
include '../includes/header.php';
include '../includes/customer-sidebar.php';
?>

<!-- صفحه سفارشات -->
<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <h1>سفارشات من</h1>
            <p>تاریخچه و وضعیت سفارشات شما</p>
        </div>

        <main class="dashboard-content">
            <?php if (empty($orders)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> هنوز هیچ سفارشی ثبت نکرده‌اید.
                    <a href="<?php echo BASE_URL; ?>products.php" class="btn btn-outline">مشاهده محصولات</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-meta">
                                    <span class="order-number">سفارش #<?php echo $order['id']; ?></span>
                                    <span class="order-date"><?php echo jalaliDate($order['order_date']); ?></span>
                                </div>
                                <div class="order-status <?php echo getOrderStatusClass($order['order_status']); ?>">
                                    <?php echo getOrderStatusText($order['order_status']); ?>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="order-items">
                                    <?php 
                                    $order_items = getOrderItems($order['id']);
                                    foreach ($order_items as $item): 
                                    ?>
                                        <div class="order-item">
                                            <div class="item-image">
                                                <img src="<?php echo BASE_URL . 'uploads/products/' . $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            </div>
                                            <div class="item-info">
                                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                                <div class="item-meta">
                                                    <span>تعداد: <?php echo $item['quantity']; ?></span>
                                                    <span>قیمت واحد: <?php echo number_format($item['unit_price']); ?> تومان</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="order-summary">
                                    <div class="summary-row">
                                        <span>جمع کل:</span>
                                        <span><?php echo number_format($order['total_amount']); ?> تومان</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>روش پرداخت:</span>
                                        <span><?php echo htmlspecialchars($order['payment_method']); ?></span>
                                    </div>
                                    <div class="summary-row">
                                        <span>وضعیت پرداخت:</span>
                                        <span class="payment-status <?php echo $order['payment_status']; ?>">
                                            <?php 
                                            echo $order['payment_status'] == 'paid' ? 'پرداخت شده' : 
                                                 ($order['payment_status'] == 'pending' ? 'در انتظار پرداخت' : 'ناموفق'); 
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="order-actions">
                                <a href="<?php echo BASE_URL; ?>customer/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i> مشاهده جزئیات
                                </a>
                                <?php if ($order['order_status'] == 'processing'): ?>
                                    <button class="btn btn-danger cancel-order" data-order-id="<?php echo $order['id']; ?>">
                                        <i class="fas fa-times"></i> لغو سفارش
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// مدیریت لغو سفارش
$(document).on('click', '.cancel-order', function() {
    const orderId = $(this).data('order-id');
    if (confirm('آیا از لغو این سفارش مطمئن هستید؟')) {
        $.ajax({
            url: '<?php echo BASE_URL; ?>ajax/cancel_order.php',
            method: 'POST',
            data: { order_id: orderId },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>