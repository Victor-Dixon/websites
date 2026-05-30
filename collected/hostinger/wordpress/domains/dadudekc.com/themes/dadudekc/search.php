<?php
/**
 * Search results template.
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <header>
        <h1><?php echo esc_html(sprintf(__('Search results for "%s"', 'dadudekc'), get_search_query())); ?></h1>
    </header>
    <?php if (have_posts()) : ?>
        <div class="posts-list">
            <?php while (have_posts()) : the_post(); ?>
                <article>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="post-meta"><?php echo esc_html(get_the_date()); ?></p>
                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 35)); ?></p>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No results found.', 'dadudekc'); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
