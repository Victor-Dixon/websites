<?php
/**
 * The homepage template for displaying blog posts
 *
 * @package freerideinvestor-modern
 */

// DEBUG: Template loaded
error_log('DEBUG: home.php template loaded');

get_header();

// DEBUG: Header loaded
error_log('DEBUG: Header loaded, about to open main');
?>

<!-- DEBUG MARKER: Before main tag -->
<main id="main" class="site-main home">
    <!-- DEBUG MARKER: Main tag opened -->
    <?php
    // DEBUG: Before have_posts check
    error_log('DEBUG: Before have_posts check');
    
    global $wp_query;
    error_log('DEBUG: wp_query->post_count = ' . $wp_query->post_count);
    error_log('DEBUG: have_posts() = ' . (have_posts() ? 'true' : 'false'));
    
    if (have_posts()) :
        error_log('DEBUG: have_posts() is true, entering loop');
        ?>
        <div class="posts-container">
            <!-- DEBUG MARKER: Posts container opened -->
            <?php
            $post_count = 0;
            while (have_posts()) :
                the_post();
                $post_count++;
                error_log("DEBUG: Processing post #{$post_count}: " . get_the_title());
                get_template_part('template-parts/content', get_post_format());
            endwhile;
            error_log("DEBUG: Loop complete, processed {$post_count} posts");
            ?>
        </div>
        
        <?php
        the_posts_navigation();
        error_log('DEBUG: Posts navigation added');
    else :
        error_log('DEBUG: have_posts() is false, showing no-content');
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
    <!-- DEBUG MARKER: End of main content -->
</main>
<!-- DEBUG MARKER: Main tag closed -->

<?php
error_log('DEBUG: About to load footer');
get_footer();
error_log('DEBUG: Footer loaded, template complete');
