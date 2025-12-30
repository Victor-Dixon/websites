<?php
/**
 * Generic Page Template
 * 
 * Used for standard WordPress pages like Community, About, etc.
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 * @cache-bust 2025-12-24-v1
 */

get_header(); ?>

<main class="site-main">
    <div class="container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                <header class="page-header dreamscape-page-header">
                    <div class="page-badge">[PAGE]</div>
                    <h1 class="page-title"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </div>
</main>

<?php get_footer(); ?>

