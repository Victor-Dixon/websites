    <footer id="colophon" class="site-footer">
        <div class="container">
            <!-- Main Footer Content -->
            <div class="footer-main">
                <div class="footer-grid">
                    <!-- Brand Column -->
                    <div class="footer-brand">
                        <h2 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <span class="logo-mark">✦</span>
                                <?php bloginfo('name'); ?>
                            </a>
                        </h2>
                        <p class="footer-tagline">
                            <?php _e("A little corner of the internet where creativity lives. Games, music, ideas, and whatever else I'm dreaming up.", 'ariajet-studio'); ?>
                        </p>
                    </div>
                    
                    <!-- Explore Links -->
                    <nav class="footer-nav">
                        <h3 class="footer-heading"><?php _e('Explore', 'ariajet-studio'); ?></h3>
                        <ul>
                            <li>
                                <a href="<?php echo esc_url(home_url('/')); ?>">
                                    <span class="nav-icon">→</span>
                                    <?php _e('Home', 'ariajet-studio'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>">
                                    <span class="nav-icon">→</span>
                                    <?php _e('Games', 'ariajet-studio'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#about">
                                    <span class="nav-icon">→</span>
                                    <?php _e('About Me', 'ariajet-studio'); ?>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <!-- Connect Links -->
                    <nav class="footer-nav">
                        <h3 class="footer-heading"><?php _e('Say Hi', 'ariajet-studio'); ?></h3>
                        <ul>
                            <li>
                                <a href="#">
                                    <span class="nav-icon">📺</span>
                                    <?php _e('YouTube', 'ariajet-studio'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="nav-icon">🎵</span>
                                    <?php _e('TikTok', 'ariajet-studio'); ?>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p class="footer-copy">
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. 
                    <?php _e('All rights reserved.', 'ariajet-studio'); ?>
                </p>
                <p class="footer-made">
                    <span>Made with</span>
                    <span class="heart-icon">♥</span>
                    <span>by Aria</span>
                </p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
