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
                <h4>Platform</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/strategies')); ?>">Strategy Marketplace</a></li>
                    <li><a href="<?php echo esc_url(home_url('/features')); ?>">Features</a></li>
                    <li><a href="<?php echo esc_url(home_url('/pricing')); ?>">Pricing</a></li>
                    <li><a href="https://weareswarm.site" target="_blank" rel="noopener noreferrer">🐝 Live Swarm Status</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Trading Tools</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/waitlist')); ?>">Beta Access</a></li>
                    <li><a href="<?php echo esc_url(home_url('/ai-swarm')); ?>">AI Swarm</a></li>
                    <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Trading Insights</a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Support</a></li>
                </ul>
            </div>

            <!-- Performance Badge -->
            <div class="footer-section performance-badge">
                <div class="performance-highlight">
                    <div class="perf-stat">
                        <span class="perf-number">89.3%</span>
                        <span class="perf-label">Win Rate</span>
                    </div>
                    <div class="perf-stat">
                        <span class="perf-number">+32.8%</span>
                        <span class="perf-label">Avg Return</span>
                    </div>
                    <p class="perf-note">Validated performance since 2023</p>
                </div>
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
            <div class="footer-bottom-content">
                <p>&copy; <?php echo date('Y'); ?> TradingRobotPlug. Building the future of algorithmic trading.</p>
                <div class="footer-trust-signals">
                    <span class="trust-signal">🔒 SEC-Compliant Trading</span>
                    <span class="trust-signal">🤖 AI-Powered Analysis</span>
                    <span class="trust-signal">📊 Real Performance Data</span>
                </div>
            </div>
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
