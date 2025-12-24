<?php
/**
 * The main template file
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    if (have_posts()) :
        ?>
        <div class="posts-container">
            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content', get_post_format());
            endwhile;
            ?>
        </div>
        
        <?php
        the_posts_navigation();
    else :
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
</main>

<?php
// Removed get_sidebar() - sidebar.php doesn't exist
get_footer();
