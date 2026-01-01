<?php
/**
 * Main index template.
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <?php if (have_posts()) : ?>
        <div class="posts-list">
            <?php while (have_posts()) : the_post(); ?>
                <article>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="post-meta"><?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?></p>
                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 40)); ?></p>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No posts found.', 'dadudekc'); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
