<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\404.php
Description: Template for displaying a "Page Not Found" (404) error within The Trading Robot Plug theme, including a search form.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<?php get_header(); ?>

<div id="primary">
    <main id="main">
        <h1><?php _e('Page Not Found', 'my-custom-theme'); ?></h1>
        <p><?php _e('It looks like nothing was found at this location. Maybe try a search?', 'my-custom-theme'); ?></p>
        <?php get_search_form(); ?>
    </main>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
