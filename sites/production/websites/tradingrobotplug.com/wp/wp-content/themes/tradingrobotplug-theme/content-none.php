<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\content-none.php
Description: Template part for displaying a message when no content is found within The Trading Robot Plug theme, including a search form.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php _e('Nothing Found', 'your-text-domain'); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <p><?php _e('It seems we can’t find what you’re looking for. Perhaps searching can help.', 'your-text-domain'); ?></p>
        <?php get_search_form(); ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->
