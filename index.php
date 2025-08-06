<?php
// goldland/index.php
session_start();
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// دریافت محصولات ویژه
$featured_products = getFeaturedProducts(6);

// دریافت آخرین مطالب بلاگ
$recent_posts = getRecentPosts(3);

$page_title = "فروشگاه طلا GoldLand - صفحه اصلی";
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-slider">
        <div class="hero-slide" style="background-image: url('assets/images/banners/banner1.jpg')">
            <div class="container">
                <div class="hero-content">
                    <h1>مجموعه‌ای منحصر به فرد از طلاهای دست‌ساز</h1>
                    <p>با طراحی‌های مدرن و قیمت‌های مناسب</p>
                    <a href="products.php" class="btn btn-primary">مشاهده محصولات</a>
                </div>
            </div>
        </div>
        <div class="hero-slide" style="background-image: url('assets/images/banners/banner2.jpg')">
            <div class="container">
                <div class="hero-content">
                    <h1>تخفیف‌های ویژه فصل تابستان</h1>
                    <p>تا 30% تخفیف روی محصولات منتخب</p>
                    <a href="products.php?discount=1" class="btn btn-primary">مشاهده تخفیف‌ها</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="section categories-section">
    <div class="container">
        <div class="section-header">
            <h2>دسته‌بندی‌های محبوب</h2>
            <a href="products.php" class="view-all">مشاهده همه</a>
        </div>
        <div class="categories-grid">
            <a href="products.php?category=1" class="category-card">
                <img src="assets/images/categories/necklaces.jpg" alt="گردنبند">
                <h3>گردنبند</h3>
            </a>
            <a href="products.php?category=2" class="category-card">
                <img src="assets/images/categories/rings.jpg" alt="انگشتر">
                <h3>انگشتر</h3>
            </a>
            <a href="products.php?category=3" class="category-card">
                <img src="assets/images/categories/bracelets.jpg" alt="دستبند">
                <h3>دستبند</h3>
            </a>
            <a href="products.php?category=4" class="category-card">
                <img src="assets/images/categories/earrings.jpg" alt="گوشواره">
                <h3>گوشواره</h3>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section products-section">
    <div class="container">
        <div class="section-header">
            <h2>محصولات ویژه</h2>
            <a href="products.php" class="view-all">مشاهده همه</a>
        </div>
        <div class="products-grid">
            <?php foreach ($featured_products as $product): ?>
            <div class="product-card">
                <div class="product-badge">
                    <?php if($product['discount_price']): ?>
                        <span class="badge badge-discount">%<?= calculateDiscountPercentage($product['price'], $product['discount_price']) ?></span>
                    <?php endif; ?>
                    <span class="badge badge-new">جدید</span>
                </div>
                <a href="product-details.php?id=<?= $product['id'] ?>" class="product-image">
                    <img src="uploads/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                </a>
                <div class="product-info">
                    <h3 class="product-title"><a href="product-details.php?id=<?= $product['id'] ?>"><?= $product['name'] ?></a></h3>
                    <div class="product-meta">
                        <span class="product-weight">وزن: <?= $product['weight'] ?> گرم</span>
                        <span class="product-purity">عیار: <?= $product['purity'] ?></span>
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
                        <button class="btn btn-wishlist" data-product-id="<?= $product['id'] ?>">
                            <i class="far fa-heart"></i>
                        </button>
                        <button class="btn btn-add-to-cart" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-cart"></i> افزودن به سبد
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>درباره GoldLand</h2>
                <p>فروشگاه اینترنتی GoldLand با بیش از 15 سال سابقه در زمینه طراحی و فروش طلا و جواهرات، ارائه‌دهنده محصولاتی با کیفیت و طراحی‌های منحصر به فرد است. ما با بهره‌گیری از هنرمندان و طراحان مجرب، مجموعه‌ای بی‌نظیر از زیورآلات طلا را برای شما فراهم کرده‌ایم.</p>
                <a href="about.php" class="btn btn-outline">اطلاعات بیشتر</a>
            </div>
            <div class="about-image">
                <img src="assets/images/about.jpg" alt="درباره GoldLand">
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="section blog-section">
    <div class="container">
        <div class="section-header">
            <h2>آخرین مقالات</h2>
            <a href="blog.php" class="view-all">مشاهده همه</a>
        </div>
        <div class="blog-grid">
            <?php foreach ($recent_posts as $post): ?>
            <div class="blog-card">
                <a href="blog-post.php?slug=<?= $post['slug'] ?>" class="blog-image">
                    <img src="uploads/blog/<?= $post['image'] ?>" alt="<?= $post['title'] ?>">
                </a>
                <div class="blog-info">
                    <span class="blog-date"><?= jalaliDate($post['created_at']) ?></span>
                    <h3 class="blog-title"><a href="blog-post.php?slug=<?= $post['slug'] ?>"><?= $post['title'] ?></a></h3>
                    <p class="blog-excerpt"><?= mb_substr(strip_tags($post['content']), 0, 100) ?>...</p>
                    <a href="blog-post.php?slug=<?= $post['slug'] ?>" class="read-more">ادامه مطلب</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>