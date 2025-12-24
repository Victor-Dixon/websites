<?php
/**
 * The main template file - MINIMAL WORKING VERSION
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="posts-container">
        <h1>FreeRide Investor</h1>
        <p>Welcome to FreeRide Investor - Your source for trading insights.</p>
        
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    </header>
                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
                <?php
            endwhile;
        else :
            ?>
            <p>No posts found.</p>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();
