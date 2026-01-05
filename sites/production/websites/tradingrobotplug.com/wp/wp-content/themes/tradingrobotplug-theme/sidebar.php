<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\sidebar.php
Description: Sidebar template for The Trading Robot Plug theme, displaying widgets if the sidebar is active, with accessibility improvements.
Version: 1.1.0
Author: Victor Dixon
*/
?>

<aside id="sidebar" role="complementary" aria-label="<?php esc_attr_e('Sidebar', 'your-theme-textdomain'); ?>">
    <?php if (is_active_sidebar('sidebar-1')) : ?>
        <ul>
            <?php dynamic_sidebar('sidebar-1'); ?>
        </ul>
    <?php endif; ?>
</aside>
