<?php
/**
 * Archive template.
 *
 * @package DaDudeKC
 */

get_header();

$series = isset($_GET['series']) ? sanitize_text_field(wp_unslash($_GET['series'])) : '';
$args = [
    'post_type' => 'post',
    'posts_per_page' => 10,
    'paged' => max(1, get_query_var('paged')),
];

if ($series) {
    $args['category_name'] = $series;
}

$posts_query = new WP_Query($args);
?>
<main class="content-area">
    <header>
        <h1><?php echo esc_html($series ? sprintf(__('Series: %s', 'dadudekc'), ucfirst(str_replace('-', ' ', $series))) : get_the_archive_title()); ?></h1>
        <p class="post-meta"><?php esc_html_e('Explore long-form writing, build logs, and research notes.', 'dadudekc'); ?></p>
    </header>

    <section class="series-nav">
        <strong><?php esc_html_e('Pinned Series', 'dadudekc'); ?></strong>
        <div class="tag-list" style="margin-top: 0.75rem;">
            <a class="tag-pill" href="<?php echo esc_url(add_query_arg('series', 'dreamscape', dadudekc_get_blog_page_url())); ?>">
                <?php esc_html_e('Dreamscape', 'dadudekc'); ?>
            </a>
            <a class="tag-pill" href="<?php echo esc_url(add_query_arg('series', 'swarm', dadudekc_get_blog_page_url())); ?>">
                <?php esc_html_e('Swarm', 'dadudekc'); ?>
            </a>
            <a class="tag-pill" href="<?php echo esc_url(add_query_arg('series', 'trading-systems', dadudekc_get_blog_page_url())); ?>">
                <?php esc_html_e('Trading Systems', 'dadudekc'); ?>
            </a>
        </div>
    </section>

    <?php if ($posts_query->have_posts()) : ?>
        <div class="posts-list">
            <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                <article>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="post-meta"><?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?></p>
                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 45)); ?></p>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No posts found yet.', 'dadudekc'); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
</main>
<?php
get_footer();
