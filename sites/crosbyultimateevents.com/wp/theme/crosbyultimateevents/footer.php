<footer class="site-footer">
    <div class="container">
        <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; padding: 2rem 0;">
            <div class="footer-info">
                <h3><?php bloginfo('name'); ?></h3>
                <p>Private Chef & Event Planning Services</p>
                <p class="footer-contact">
                    <span style="display: block; margin-bottom: 0.5rem;">📞 (917) 555-0198</span>
                    <span style="display: block; margin-bottom: 0.5rem;">📍 NYC & Tri-State Area</span>
                    <span style="display: block;">⏰ Mon-Sun: 9AM - 8PM</span>
                </p>
            </div>
            <div class="footer-nav">
                <h3>Quick Links</h3>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class' => 'footer-menu',
                    'container' => false,
                    'fallback_cb' => false,
                ));
                ?>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem; text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>

</html>