<?php
/**
 * The template for displaying all pages
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">

                <?php while (have_posts()) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>

                            <?php
                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . __('Pages:', 'freerideinvestor'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>
                    </article>

                <?php endwhile; ?>

            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php
get_footer();
?>