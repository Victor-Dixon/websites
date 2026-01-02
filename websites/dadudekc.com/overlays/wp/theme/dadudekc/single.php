<?php
/**
 * Single post template.
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article>
                <p class="post-meta">
                    <?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?>
                </p>
                <h1><?php the_title(); ?></h1>
                <div class="post-meta">
                    <?php the_category(', '); ?>
                </div>
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
                <?php if (dadudekc_is_swarm_intro_post()) : ?>
                    <?php get_template_part('template-parts/components/swarm-post-extras'); ?>
                <?php endif; ?>
                <?php
                $series_slugs = ['dreamscape', 'swarm', 'trading-systems'];
                $post_categories = wp_get_post_categories(get_the_ID(), ['fields' => 'slugs']);
                $matched_series = array_intersect($series_slugs, $post_categories);
                if (!empty($matched_series)) :
                    $series_slug = array_values($matched_series)[0];
                    ?>
                    <div class="series-nav">
                        <strong><?php esc_html_e('Series Navigation', 'dadudekc'); ?></strong>
                        <p><?php echo esc_html(sprintf(__('This post is part of the %s series.', 'dadudekc'), ucfirst(str_replace('-', ' ', $series_slug)))); ?></p>
                        <a href="<?php echo esc_url(add_query_arg('series', $series_slug, dadudekc_get_blog_page_url())); ?>">
                            <?php esc_html_e('View all in series →', 'dadudekc'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    <?php endif; ?>
</main>
<?php
get_footer();
