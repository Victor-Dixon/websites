<?php

/**
 * Homepage Template
 * 
 * @package FreeRideInvestor_Modern
 */

get_header();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?php bloginfo('name'); ?></h1>
            <p class="hero-subtitle">A hobbyist trading blog sharing strategy analysis, reports, and insights</p>
            <div class="hero-cta">
                <a href="<?php echo esc_url(home_url('/tsla-strategy-report')); ?>" class="btn btn-primary btn-large">View Free Strategy Report</a>
                <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-secondary btn-large">Read Blog Posts</a>
            </div>
        </div>
    </div>
</section>

<!-- Strategy Reports Section -->
<section class="reports-section" style="padding: 3rem 0; background: var(--bg-secondary);">
    <div class="container">
        <h2 class="text-center" style="margin-bottom: 2rem;">Strategy Reports</h2>
        <div class="card-grid" style="max-width: 800px; margin: 0 auto;">
            <div class="card">
                <h3>Free TSLA Strategy Report</h3>
                <p>Get a comprehensive analysis of the Improved TSLA Strategy including:</p>
                <ul style="text-align: left; margin: 1rem 0;">
                    <li>Strategy configuration and indicators</li>
                    <li>Entry and exit logic</li>
                    <li>Risk management details</li>
                    <li>Risk/reward analysis</li>
                </ul>
                <a href="<?php echo esc_url(home_url('/tsla-strategy-report')); ?>" class="btn btn-primary">View Free Report</a>
            </div>
            <div class="card">
                <h3>Premium TSLA Strategy Report</h3>
                <p>Upgrade to premium for detailed analysis including:</p>
                <ul style="text-align: left; margin: 1rem 0;">
                    <li>Complete backtesting results</li>
                    <li>Performance metrics and statistics</li>
                    <li>Trade-by-trade analysis</li>
                    <li>Optimization suggestions</li>
                    <li>Full PineScript code download</li>
                </ul>
                <div style="margin: 1.5rem 0;">
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary-blue);">$9.99</div>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">One-time payment</p>
                </div>
                <a href="<?php echo esc_url(home_url('/tsla-strategy-report-premium')); ?>" class="btn btn-primary">Get Premium Report</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Content -->
<div class="container">
    <section class="featured-section">
        <h2 class="text-center">Latest Insights</h2>
        <div class="card-grid">
            <?php
            $featured_posts = new WP_Query(array(
                'posts_per_page' => 6,
                'post_status' => 'publish',
            ));

            if ($featured_posts->have_posts()) :
                while ($featured_posts->have_posts()) : $featured_posts->the_post();
            ?>
                    <article class="post-card card">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', array('class' => 'post-card-image')); ?>
                            </a>
                        <?php endif; ?>

                        <h3 class="post-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>

                        <div class="post-card-excerpt">
                            <?php the_excerpt(); ?>
                        </div>

                        <div class="post-card-meta">
                            <span><?php echo get_the_date(); ?></span>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </article>
            <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>
</div>

<?php
get_footer();
