    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="footer-widgets">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <?php dynamic_sidebar('footer-1'); ?>
                <?php else : ?>
                    <div class="footer-widget">
                        <h3><?php _e('About FreeRideInvestor', 'freerideinvestor'); ?></h3>
                        <p><?php _e('Your trusted source for trading insights, strategies, and market analysis. Learn from experienced traders and improve your investment decisions.', 'freerideinvestor'); ?></p>
                    </div>

                    <div class="footer-widget">
                        <h3><?php _e('Quick Links', 'freerideinvestor'); ?></h3>
                        <nav class="footer-navigation">
                            <?php
                            // Only show footer menu if it exists and has items
                            $footer_menu = wp_nav_menu(array(
                                'theme_location' => 'footer',
                                'menu_class'     => 'footer-menu',
                                'depth'          => 1,
                                'echo'           => false,
                                'fallback_cb'    => false,
                            ));
                            
                            if ($footer_menu) {
                                echo $footer_menu;
                            } else {
                                // Show safe links that exist
                                ?>
                                <ul class="footer-menu">
                                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'freerideinvestor'); ?></a></li>
                                    <?php
                                    // Only show links to pages that exist
                                    $about_page = get_page_by_path('about');
                                    if ($about_page) :
                                        ?>
                                        <li><a href="<?php echo esc_url(get_permalink($about_page->ID)); ?>"><?php _e('About', 'freerideinvestor'); ?></a></li>
                                        <?php
                                    endif;
                                    
                                    $contact_page = get_page_by_path('contact');
                                    if ($contact_page) :
                                        ?>
                                        <li><a href="<?php echo esc_url(get_permalink($contact_page->ID)); ?>"><?php _e('Contact', 'freerideinvestor'); ?></a></li>
                                        <?php
                                    endif;
                                    
                                    // Show blog link
                                    $blog_page_id = get_option('page_for_posts');
                                    if ($blog_page_id) :
                                        ?>
                                        <li><a href="<?php echo esc_url(get_permalink($blog_page_id)); ?>"><?php _e('Journal', 'freerideinvestor'); ?></a></li>
                                        <?php
                                    endif;
                                    ?>
                                </ul>
                                <?php
                            }
                            ?>
                        </nav>
                    </div>

                    <div class="footer-widget">
                        <h3><?php _e('Connect With Us', 'freerideinvestor'); ?></h3>
                        <div class="footer-links">
                            <a href="#" aria-label="Email us"><span class="dashicons dashicons-email"></span></a>
                            <a href="#" aria-label="Join Discord"><span class="dashicons dashicons-groups"></span></a>
                            <a href="#" aria-label="Twitter"><span class="dashicons dashicons-twitter"></span></a>
                            <a href="#" aria-label="Twitch"><span class="dashicons dashicons-video-alt3"></span></a>
                        </div>
                        <p><?php _e('Stay connected with our community for the latest trading insights and market updates.', 'freerideinvestor'); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="site-info">
                <p>
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.
                    <?php _e('All rights reserved.', 'freerideinvestor'); ?>
                    <span class="sep"> | </span>
                    <a href="<?php echo esc_url(__('https://wordpress.org/', 'freerideinvestor')); ?>">
                        <?php _e('Powered by WordPress', 'freerideinvestor'); ?>
                    </a>
                </p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

