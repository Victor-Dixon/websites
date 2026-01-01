<?php
/**
 * Page template.
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <?php while (have_posts()) : the_post(); ?>
        <article>
            <h1><?php the_title(); ?></h1>
            <div class="post-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php
get_footer();
