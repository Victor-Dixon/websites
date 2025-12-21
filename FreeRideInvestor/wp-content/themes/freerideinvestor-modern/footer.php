    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php bloginfo('name'); ?></h3>
                    <p><?php bloginfo('description'); ?></p>
                </div>

                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <?php
                    if (has_nav_menu('footer')) {
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'container' => false,
                            'menu_class' => 'footer-links',
                            'fallback_cb' => false,
                        ));
                    } else {
                    ?>
                        <ul class="footer-links">
                            <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                            <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
                            <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
                            <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
                        </ul>
                    <?php
                    }
                    ?>
                </div>

                <div class="footer-section">
                    <h3>Connect</h3>
                    <ul class="footer-links">
                        <li><a href="#">Twitter</a></li>
                        <li><a href="#">Discord</a></li>
                        <li><a href="#">YouTube</a></li>
                    </ul>
                </div>

                <?php if (is_active_sidebar('footer-1')) : ?>
                    <div class="footer-section">
                        <?php dynamic_sidebar('footer-1'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    </div>

    <?php wp_footer(); ?>
    </body>

    </html>