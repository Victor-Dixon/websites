    <footer class="swarm-footer">
        <div class="swarm-content">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('Powered by Swarm Intelligence', 'swarm-core-theme'); ?></p>
            <nav class="footer-navigation">
                <?php wp_nav_menu(array('theme_location' => 'footer', 'menu_class' => 'footer-menu')); ?>
            </nav>
        </div>
    </footer>
    <?php wp_footer(); ?>
</body>
</html>