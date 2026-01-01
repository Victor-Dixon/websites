<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\footer.php
Description: Footer template for The Trading Robot Plug theme, including copyright information and footer navigation.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <strong>TradingRobotPlug</strong>
                    </a>
                </div>
            </div>

            <div class="footer-section">
                <h4>Product</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/features')); ?>">Features</a></li>
                    <li><a href="<?php echo esc_url(home_url('/pricing')); ?>">Pricing</a></li>
                    <li><a href="<?php echo esc_url(home_url('/ai-swarm')); ?>">AI Swarm</a></li>
                    <li><a href="https://weareswarm.site" target="_blank" rel="noopener noreferrer">🐝 See the Swarm</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Resources</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/privacy')); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo esc_url(home_url('/terms-of-service')); ?>">Terms of Service</a></li>
                    <li><a href="<?php echo esc_url(home_url('/product-terms')); ?>">Product Terms & Risk Disclosure</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Connect</h4>
                <div class="social-links">
                    <a href="#" aria-label="Twitter">🐦</a>
                    <a href="#" aria-label="LinkedIn">💼</a>
                    <a href="#" aria-label="Discord">💬</a>
                    <a href="#" aria-label="GitHub">🐙</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <hr>
            <p>&copy; <?php echo date('Y'); ?> TradingRobotPlug. All rights reserved.</p>
            <?php
            // Display footer menu if available
            if (has_nav_menu('footer')) {
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'container' => false,
                    'menu_class' => 'footer-links',
                    'depth' => 1,
                ));
            } else {
                // Fallback to simple links
                ?>
                <div class="footer-links">
                    <a href="<?php echo esc_url(home_url('/privacy')); ?>">Privacy Policy</a> |
                    <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>">Terms of Service</a> |
                    <a href="<?php echo esc_url(home_url('/product-terms')); ?>">Product Terms</a>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
