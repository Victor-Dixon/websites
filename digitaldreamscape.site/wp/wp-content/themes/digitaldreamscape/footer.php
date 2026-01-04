<?php
/**
 * Footer Template
 * 
 * @package DigitalDreamscape
 * @since 1.0.0
 */
?>

<footer id="site-footer" class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <nav class="footer-links">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                <a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a>
                <a href="<?php echo esc_url(home_url('/streaming')); ?>">Streaming</a>
                <a href="<?php echo esc_url(home_url('/community')); ?>">Community</a>
                <a href="<?php echo esc_url(home_url('/about')); ?>">About</a>
            </nav>

            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> Digital Dreamscape. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

