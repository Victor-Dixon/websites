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
                    <li><a href="#features">Features</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="https://weareswarm.site" target="_blank" rel="noopener noreferrer">🐝 See the Swarm</a></li>
                    <li><a href="#changelog">Changelog</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Company</h4>
                <ul>
                    <li><a href="#about">About</a></li>
                    <li><a href="#careers">Careers</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#blog">Blog</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#docs">Documentation</a></li>
                    <li><a href="#api">API</a></li>
                    <li><a href="#support">Support</a></li>
                    <li><a href="#community">Community</a></li>
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
            <p>&copy; <?php echo date('Y'); ?> TradingRobotPlug. All rights reserved.
                <span class="footer-links">
                    <a href="#privacy">Privacy</a> |
                    <a href="#terms">Terms</a>
                </span>
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
