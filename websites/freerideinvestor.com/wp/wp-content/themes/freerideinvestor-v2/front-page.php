<?php
/**
 * Front page fallback template.
 *
 * Prevents empty homepage output if a static front page has no body content.
 *
 * @package FreeRideInvestor_V2
 */

get_header();

while (have_posts()) {
    the_post();
}

$raw_content = trim((string) get_post_field('post_content', get_the_ID()));
$has_static_content = $raw_content !== '';
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php include get_template_directory() . '/hero-investor.php'; ?>

        <?php if ($has_static_content) : ?>
            <section class="front-page-content-section">
                <article id="post-<?php the_ID(); ?>" <?php post_class('front-page-content'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            </section>
        <?php else : ?>
            <section class="front-page-content-section">
                <header class="section-header">
                    <h2><?php esc_html_e('Today\'s Market Focus', 'freerideinvestor'); ?></h2>
                    <p><?php esc_html_e('No static homepage content was configured, so this fallback keeps the site live and informative.', 'freerideinvestor'); ?></p>
                </header>

                <?php
                $latest_posts = new WP_Query([
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ]);
                ?>

                <?php if ($latest_posts->have_posts()) : ?>
                    <div class="blog-grid">
                        <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                                <header class="entry-header">
                                    <h3 class="entry-title">
                                        <a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="entry-meta">
                                        <span class="posted-on"><?php echo esc_html(get_the_date()); ?></span>
                                    </div>
                                </header>
                                <div class="entry-content">
                                    <?php the_excerpt(); ?>
                                    <a href="<?php echo esc_url(get_permalink()); ?>" class="btn"><?php esc_html_e('Read Update', 'freerideinvestor'); ?></a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p class="no-content"><?php esc_html_e('Content pipeline is active. New trading updates will appear here shortly.', 'freerideinvestor'); ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php get_footer();
