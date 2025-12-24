<?php
/**
 * Footer Template
 * 
 * @package HoustonSipQueen
 * @since 1.0.0
 */
?>

<footer id="site-footer" class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <!-- Footer Widgets -->
            <div class="footer-widgets">
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <li><a href="<?php echo esc_url(home_url('/services')); ?>">Services</a></li>
                        <li><a href="<?php echo esc_url(home_url('/pricing')); ?>">Pricing</a></li>
                        <li><a href="<?php echo esc_url(home_url('/portfolio')); ?>">Portfolio</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>About</h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url(home_url('/about')); ?>">About Us</a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
                        <li><a href="<?php echo esc_url(home_url('/testimonials')); ?>">Testimonials</a></li>
                        <li><a href="<?php echo esc_url(home_url('/faq')); ?>">FAQ</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Get Started</h3>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url(home_url('/quote')); ?>">Request a Quote</a></li>
                        <li><a href="<?php echo esc_url(home_url('/book')); ?>">Book Consultation</a></li>
                        <li><a href="<?php echo esc_url(home_url('/event-bar-checklist')); ?>">Free Checklist</a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Contact</h3>
                    <div class="footer-contact">
                        <?php if (get_option('houstonsipqueen_phone')) : ?>
                            <p><a href="tel:<?php echo esc_attr(get_option('houstonsipqueen_phone')); ?>">
                                <?php echo esc_html(get_option('houstonsipqueen_phone')); ?>
                            </a></p>
                        <?php endif; ?>
                        <?php if (get_option('admin_email')) : ?>
                            <p><a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>">
                                <?php echo esc_html(get_option('admin_email')); ?>
                            </a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> Houston Sip Queen. All rights reserved.</p>
                </div>
                <div class="footer-social">
                    <?php
                    // Social media links can be added here
                    // Example: <a href="#" class="social-link">Facebook</a>
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

