<?php
// goldland/products.php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';


// دریافت پارامترهای جستجو و فیلتر
$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? 0;
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 0;
$sort = $_GET['sort'] ?? 'newest';
$page = $_GET['page'] ?? 1;
$per_page = 12;

// ساخت کوئری پایه
$sql = "SELECT p.*, c.name as category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.status = 1";

$params = [];

// اعمال فیلترها
if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if ($min_price > 0) {
    $sql .= " AND (p.discount_price > 0 ? p.discount_price : p.price) >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $sql .= " AND (p.discount_price > 0 ? p.discount_price : p.price) <= ?";
    $params[] = $max_price;
}

// اعمال مرتب سازی
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY (p.discount_price > 0 ? p.discount_price : p.price) ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY (p.discount_price > 0 ? p.discount_price : p.price) DESC";
        break;
    case 'popular':
        $sql .= " ORDER BY p.views DESC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

// محاسبه صفحه‌بندی
$total_products = getTotalProducts($sql, $params);
$total_pages = ceil($total_products / $per_page);
$offset = ($page - 1) * $per_page;

$sql .= " LIMIT $offset, $per_page";

// اجرای کوئری
$stmt = executeQuery($sql, $params);
$products = $stmt->fetchAll();

// دریافت دسته‌بندی‌ها برای منوی فیلتر
$categories = getAllCategories();

// دریافت تمام دسته‌بندی‌های فعال
$categories = getAllCategories();

// دریافت محصولات یک دسته‌بندی خاص
$categoryId = $_GET['category_id'] ?? null;
if ($categoryId) {
    $products = getProductsByCategory($categoryId);
    
    foreach ($products as $product) {
        echo "<div class='product'>
                <h3>{$product['name']}</h3>
                <p>وزن: {$product['weight']} گرم</p>
                <p>عیار: {$product['purity']}</p>
                <!-- بقیه فیلدها -->
              </div>";
    }
}

$page_title = "محصولات طلا و جواهر - GoldLand";
include 'includes/header.php';
?>

<!-- صفحه محصولات -->
<section class="products-page">
    <div class="container">
        <div class="page-header">
            <h1>محصولات طلا و جواهر</h1>
            <p>مجموعه‌ای از زیباترین طرح‌های طلا و جواهر</p>
        </div>

        <div class="products-container">
            <!-- فیلترها و مرتب سازی -->
            <div class="products-filters">
                <form action="products.php" method="get" class="filter-form">
                    <div class="filter-group">
                        <label for="search">جستجو:</label>
                        <input type="text" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="نام محصول...">
                    </div>

                    <div class="filter-group">
                        <label for="category">دسته‌بندی:</label>
                        <select name="category" id="category">
                            <option value="0">همه دسته‌بندی‌ها</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group price-range">
                        <label>محدوده قیمت:</label>
                        <div class="range-inputs">
                            <input type="number" name="min_price" placeholder="حداقل" value="<?= $min_price ?>">
                            <span>تا</span>
                            <input type="number" name="max_price" placeholder="حداکثر" value="<?= $max_price ?>">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="sort">مرتب‌سازی:</label>
                        <select name="sort" id="sort">
                            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>جدیدترین</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>ارزان‌ترین</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>گران‌ترین</option>
                            <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>پربازدیدترین</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">اعمال فیلترها</button>
                    <a href="products.php" class="btn btn-outline">حذف فیلترها</a>
                </form>
            </div>

            <!-- لیست محصولات -->
            <div class="products-grid">
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <p>محصولی با این مشخصات یافت نشد</p>
                        <a href="products.php" class="btn btn-primary">مشاهده همه محصولات</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-badge">
                                <?php if($product['discount_price']): ?>
                                    <span class="badge badge-discount">%<?= calculateDiscountPercentage($product['price'], $product['discount_price']) ?></span>
                                <?php endif; ?>
                                <?php if (isNewProduct($product['created_at'])): ?>
                                    <span class="badge badge-new">جدید</span>
                                <?php endif; ?>
                            </div>
                            <a href="product-details.php?id=<?= $product['id'] ?>" class="product-image">
                                <img src="uploads/products/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            </a>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="product-details.php?id=<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a>
                                </h3>
                                <div class="product-meta">
                                    <span class="product-category"><?= htmlspecialchars($product['category_name']) ?></span>
                                    <span class="product-weight"><?= $product['weight'] ?> گرم</span>
                                </div>
                                <div class="product-price">
                                    <?php if($product['discount_price']): ?>
                                        <span class="price-old"><?= number_format($product['price']) ?> تومان</span>
                                        <span class="price-new"><?= number_format($product['discount_price']) ?> تومان</span>
                                    <?php else: ?>
                                        <span class="price"><?= number_format($product['price']) ?> تومان</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-wishlist" data-product-id="<?= $product['id'] ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="btn-add-to-cart" data-product-id="<?= $product['id'] ?>">
                                        <i class="fas fa-shopping-cart"></i> افزودن به سبد
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- صفحه‌بندی -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?= buildPaginationUrl($page - 1) ?>" class="page-link">
                            <i class="fas fa-chevron-right"></i> قبلی
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= buildPaginationUrl($i) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?= buildPaginationUrl($page + 1) ?>" class="page-link">
                            بعدی <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// افزودن به لیست علاقه‌مندی‌ها
$(document).on('click', '.btn-wishlist', function() {
    const productId = $(this).data('product-id');
    $.ajax({
        url: 'ajax/toggle_wishlist.php',
        method: 'POST',
        data: { product_id: productId },
        success: function(response) {
            if (response.success) {
                $('.wishlist-count').text(response.wishlist_count);
                showToast(response.message);
            }
        }
    });
});

// افزودن به سبد خرید
$(document).on('click', '.btn-add-to-cart', function() {
    const productId = $(this).data('product-id');
    $.ajax({
        url: 'ajax/add_to_cart.php',
        method: 'POST',
        data: { product_id: productId, quantity: 1 },
        success: function(response) {
            if (response.success) {
                $('.cart-count').text(response.cart_count);
                showToast('محصول به سبد خرید اضافه شد');
            }
        }
    });
});

// نمایش پیام
function showToast(message) {
    const toast = $('<div class="toast">' + message + '</div>');
    $('body').append(toast);
    toast.fadeIn().delay(3000).fadeOut(function() {
        $(this).remove();
    });
}
</script>

<?php include 'includes/footer.php'; ?>