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
                        <li><a href="<?php echo esc_url(home_url('/quote')); ?>">Book Your Event</a></li>
                        <li><a href="<?php echo esc_url(home_url('/event-planning-guide')); ?>">Free Planning Guide</a></li>
                        <li><a href="https://www.instagram.com/houston_sipqueen/" target="_blank" rel="noopener noreferrer">@houston_sipqueen</a></li>
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
                        <?php 
                        $contact_email = get_option('houstonsipqueen_contact_email', 'houstonsipqueen@gmail.com');
                        if ($contact_email) : ?>
                            <p><a href="mailto:<?php echo esc_attr($contact_email); ?>">
                                <?php echo esc_html($contact_email); ?>
                            </a></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="footer-policy">
                <?php houstonsipqueen_alcohol_policy_notice(); ?>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> Houston Sip Queen. All rights reserved.</p>
                </div>
                <div class="footer-social">
                    <a href="https://www.instagram.com/houston_sipqueen/" class="social-link" target="_blank" rel="noopener noreferrer">Instagram</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

