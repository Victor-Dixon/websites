<?php
/**
 * The front page template file
 *
 * @package freerideinvestor-modern
 */

get_header();
?>

<main id="main" class="site-main front-page">
    <?php
    // Display latest posts or static content
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            get_template_part('template-parts/content', 'front-page');
        endwhile;
    else :
        // If no posts, show welcome message
        ?>
        <article class="no-content">
            <div class="entry-content">
                <h1>Welcome to FreeRide Investor</h1>
                <p>Content coming soon...</p>
            </div>
        </article>
        <?php
    endif;
    ?>
</main>

<?php
get_footer();
