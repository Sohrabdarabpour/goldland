<?php
// goldland/includes/footer.php
?>
    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-col about-col">
                        <h3>درباره GoldLand</h3>
                        <p>فروشگاه اینترنتی GoldLand با هدف ارائه محصولات با کیفیت و طراحی‌های منحصر به فرد در حوزه طلا و جواهرات فعالیت می‌کند.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-telegram"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                    
                    <div class="footer-col links-col">
                        <h3>لینک‌های سریع</h3>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>index.php">خانه</a></li>
                            <li><a href="<?php echo BASE_URL; ?>products.php">محصولات</a></li>
                            <li><a href="<?php echo BASE_URL; ?>about.php">درباره ما</a></li>
                            <li><a href="<?php echo BASE_URL; ?>blog.php">وبلاگ</a></li>
                            <li><a href="<?php echo BASE_URL; ?>contact.php">تماس با ما</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-col contact-col">
                        <h3>اطلاعات تماس</h3>
                        <ul>
                            <li><i class="fas fa-map-marker-alt"></i> تهران، خیابان ولیعصر، پلاک ۱۲۳۴</li>
                            <li><i class="fas fa-phone"></i> ۰۲۱-۱۲۳۴۵۶۷</li>
                            <li><i class="fas fa-envelope"></i> info@goldland.com</li>
                            <li><i class="fas fa-clock"></i> شنبه تا پنجشنبه: ۹ صبح تا ۸ شب</li>
                        </ul>
                    </div>
                    
                    <div class="footer-col newsletter-col">
                        <h3>عضویت در خبرنامه</h3>
                        <p>برای دریافت آخرین تخفیف‌ها و محصولات جدید در خبرنامه ما عضو شوید.</p>
                        <form class="newsletter-form">
                            <input type="email" placeholder="آدرس ایمیل شما">
                            <button type="submit">عضویت</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        <p>تمامی حقوق برای فروشگاه طلا GoldLand محفوظ است. © ۱۴۰۲</p>
                    </div>
                    <div class="payment-methods">
                        <img src="assets/images/payments/saman.png" alt="Saman">
                        <img src="assets/images/payments/melli.png" alt="Melli">
                        <img src="assets/images/payments/pasargad.png" alt="Pasargad">
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        $(document).ready(function(){
            // Initialize Hero Slider
            $('.hero-slider').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 5000,
                rtl: true
            });
            
            // Back to Top Button
            $(window).scroll(function(){
                if ($(this).scrollTop() > 300) {
                    $('.back-to-top').fadeIn();
                } else {
                    $('.back-to-top').fadeOut();
                }
            });
            
            $('.back-to-top').click(function(){
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
            
            // Mobile Menu Toggle
            $('.mobile-menu-toggle').click(function(){
                $('.mobile-menu').addClass('active');
            });
            
            $('.close-menu').click(function(){
                $('.mobile-menu').removeClass('active');
            });
        });
    </script>
</body>
</html>