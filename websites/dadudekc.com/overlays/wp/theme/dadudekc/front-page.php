<?php
/**
 * Front page template.
 *
 * @package DaDudeKC
 */

get_header();

$latest_posts = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post_status' => 'publish',
]);

$latest_notes = new WP_Query([
    'post_type' => 'note',
    'posts_per_page' => 4,
    'post_status' => 'publish',
]);

$idea_tags = get_terms([
    'taxonomy' => 'post_tag',
    'hide_empty' => true,
    'number' => 12,
]);
?>
<main>
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <?php $theme_version = wp_get_theme()->get('Version'); ?>
                <div class="portal-marker">
                    <?php esc_html_e('Portal active', 'dadudekc'); ?>
                    <span><?php echo esc_html($theme_version); ?></span>
                </div>
                <h1><?php esc_html_e('Victor builds ambitious systems, ships experiments, and documents the path.', 'dadudekc'); ?></h1>
                <p><?php esc_html_e('Welcome to the portfolio + Idea Lab + blog hub. Explore projects, browse live experiments, and dive into long-form deep dives.', 'dadudekc'); ?></p>
                <div class="cta-row">
                    <form action="#" method="post" class="cta-row">
                        <label class="screen-reader-text" for="hero-email"><?php esc_html_e('Email', 'dadudekc'); ?></label>
                        <input type="email" id="hero-email" name="email" placeholder="<?php esc_attr_e('Email address', 'dadudekc'); ?>">
                        <button type="submit"><?php esc_html_e('Subscribe', 'dadudekc'); ?></button>
                    </form>
                    <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>"><?php esc_html_e('Contact Victor →', 'dadudekc'); ?></a>
                </div>
            </div>
            <div class="primary-links">
                <a href="<?php echo esc_url(dadudekc_get_portfolio_url()); ?>"><?php esc_html_e('Portfolio: shipped systems', 'dadudekc'); ?> <span>→</span></a>
                <a href="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>"><?php esc_html_e('Idea Lab: notes + articles', 'dadudekc'); ?> <span>→</span></a>
                <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>"><?php esc_html_e('Latest writing + series', 'dadudekc'); ?> <span>→</span></a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e('Latest Posts', 'dadudekc'); ?></h2>
                    <p class="section-subtitle"><?php esc_html_e('Recent deep dives, system builds, and lessons learned.', 'dadudekc'); ?></p>
                </div>
                <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>"><?php esc_html_e('View all →', 'dadudekc'); ?></a>
            </div>
            <div class="grid">
                <?php if ($latest_posts->have_posts()) : ?>
                    <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                        <article class="card">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="post-meta"><?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?></p>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 22)); ?></p>
                        </article>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="card"><?php esc_html_e('New posts coming soon.', 'dadudekc'); ?></div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/components/project-demos'); ?>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e('Idea Lab', 'dadudekc'); ?></h2>
                    <p class="section-subtitle"><?php esc_html_e('Notes, experiments, and brainstorms organized by tag.', 'dadudekc'); ?></p>
                </div>
                <a href="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>"><?php esc_html_e('Browse Idea Lab →', 'dadudekc'); ?></a>
            </div>
            <div class="tag-list">
                <?php if (!empty($idea_tags) && !is_wp_error($idea_tags)) : ?>
                    <?php foreach ($idea_tags as $tag) : ?>
                        <a class="tag-pill" href="<?php echo esc_url(add_query_arg('tag', $tag->slug, dadudekc_get_idea_lab_url())); ?>">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <span class="tag-pill"><?php esc_html_e('Add tags to notes and posts to populate filters.', 'dadudekc'); ?></span>
                <?php endif; ?>
            </div>
            <div class="grid" style="margin-top: 2rem;">
                <?php if ($latest_notes->have_posts()) : ?>
                    <?php while ($latest_notes->have_posts()) : $latest_notes->the_post(); ?>
                        <article class="card">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="post-meta"><?php esc_html_e('Note', 'dadudekc'); ?> · <?php echo esc_html(get_the_date()); ?></p>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 20)); ?></p>
                        </article>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="card"><?php esc_html_e('Idea Lab notes will appear here.', 'dadudekc'); ?></div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e('Quick Links', 'dadudekc'); ?></h2>
                    <p class="section-subtitle"><?php esc_html_e('Jump into Victor’s current focus, latest series, and active builds.', 'dadudekc'); ?></p>
                </div>
            </div>
            <div class="quick-links">
                <a class="quick-link" href="<?php echo esc_url(dadudekc_get_now_url()); ?>"><?php esc_html_e('Now: current focus + status →', 'dadudekc'); ?></a>
                <a class="quick-link" href="<?php echo esc_url(add_query_arg('series', 'dreamscape', dadudekc_get_blog_page_url())); ?>"><?php esc_html_e('Series: Dreamscape →', 'dadudekc'); ?></a>
                <a class="quick-link" href="<?php echo esc_url(add_query_arg('series', 'swarm', dadudekc_get_blog_page_url())); ?>"><?php esc_html_e('Series: Swarm →', 'dadudekc'); ?></a>
                <a class="quick-link" href="<?php echo esc_url(add_query_arg('series', 'trading-systems', dadudekc_get_blog_page_url())); ?>"><?php esc_html_e('Series: Trading Systems →', 'dadudekc'); ?></a>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
