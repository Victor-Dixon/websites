<?php

/**
 * Main Index Template
 * 
 * For homepage, load front-page.php instead
 * 
 * @package DaDudeKC
 * @since 1.0.0
 */

// If this is the front page, use front-page.php template
if (is_front_page()) {
    $front_page = locate_template('front-page.php');
    if ($front_page) {
        include $front_page;
        return;
    }
}

get_header(); ?>

<div class="container">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h2 class="entry-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                </header>

                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php esc_html_e('No content found.', 'dadudekc'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();

