<?php
/**
 * Template Name: Now
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <?php while (have_posts()) : the_post(); ?>
        <article>
            <h1><?php the_title(); ?></h1>
            <p class="post-meta"><?php esc_html_e('Current focus, status, and links.', 'dadudekc'); ?></p>
            <div class="card" style="margin: 2rem 0;">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php
get_footer();
