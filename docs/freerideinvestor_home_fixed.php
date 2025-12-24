<?php
/**
 * The homepage template for displaying blog posts
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main home">
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
get_footer();
