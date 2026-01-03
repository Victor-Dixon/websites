<?php

/**
 * Front Page Template
 * 
 * @package DigitalDreamscape
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <!-- Streaming Status & Hero Section -->
    <?php
    $is_live = digitaldreamscape_is_streaming_live();
    $stream_info = digitaldreamscape_get_stream_info();
    ?>

    <section class="hero-section <?php echo $is_live ? 'streaming-live' : 'streaming-offline'; ?>">
        <div class="hero-background">
            <div class="hero-overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <?php if ($is_live): ?>
                    <!-- LIVE STREAMING HERO -->
                    <div class="live-indicator">
                        <span class="live-badge">🔴 LIVE NOW</span>
                        <span class="viewer-count">
                            <?php if ($stream_info && isset($stream_info['viewer_count'])): ?>
                                <?php echo number_format($stream_info['viewer_count']); ?> watching
                            <?php endif; ?>
                        </span>
                    </div>
                    <h1 class="hero-title live-title">
                        <span class="hero-title-line">Digital Dreamscape</span>
                        <span class="hero-title-line">is LIVE!</span>
                    </h1>
                    <p class="hero-subtitle">
                        <?php if ($stream_info && isset($stream_info['title'])): ?>
                            <?php echo esc_html($stream_info['title']); ?>
                        <?php else: ?>
                            Join the live stream and see what's being built in real-time!
                        <?php endif; ?>
                    </p>
                    <div class="hero-cta">
                        <a href="https://www.twitch.tv/digital_dreamscape" target="_blank" rel="noopener" class="btn btn-primary btn-live">
                            🎮 Watch Live Stream →
                        </a>
                        <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-secondary">
                            Read Updates
                        </a>
                    </div>
                <?php else: ?>
                    <!-- OFFLINE HERO -->
                    <h1 class="hero-title">
                        <span class="hero-title-line">Digital Dreamscape</span>
                        <span class="hero-title-line">Twitch Channel</span>
                    </h1>
                    <p class="hero-subtitle">
                        Follow for live coding sessions, AI experiments, and the journey of building
                        an autonomous narrative-driven world. Currently offline, but check back soon!
                    </p>
                    <div class="hero-cta">
                        <a href="https://www.twitch.tv/digital_dreamscape" target="_blank" rel="noopener" class="btn btn-primary">
                            📺 Follow on Twitch →
                        </a>
                        <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-secondary">
                            Read the Blog
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Social Links -->
                <div class="social-links">
                    <a href="https://www.twitch.tv/digital_dreamscape" target="_blank" rel="noopener" class="social-link twitch">
                        <span class="social-icon">📺</span>
                        <span class="social-text">Twitch</span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/blog')); ?>" class="social-link blog">
                        <span class="social-icon">📝</span>
                        <span class="social-text">Blog</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Content Section -->
    <section class="featured-section">
        <div class="container">
            <h2 class="section-title">Latest Updates</h2>
            <div class="featured-grid">
                <?php
                // Safely query posts with error handling
                try {
                    $featured_posts = new WP_Query(array(
                        'posts_per_page' => 3,
                        'post_status' => 'publish',
                        'ignore_sticky_posts' => true,
                    ));

                    if ($featured_posts && $featured_posts->have_posts()) :
                        while ($featured_posts->have_posts()) : $featured_posts->the_post();
                ?>
                            <article class="featured-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="card-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium_large'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="card-content">
                                    <div class="card-meta">
                                        <time datetime="<?php echo get_the_date('c'); ?>">
                                            <?php echo get_the_date(); ?>
                                        </time>
                                    </div>
                                    <h3 class="card-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="card-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="card-link">
                                        Read More →
                                    </a>
                                </div>
                            </article>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <div class="no-posts">
                            <p>No posts yet. Check back soon for updates!</p>
                        </div>
                    <?php
                    endif;
                } catch (Exception $e) {
                    // Silently fail and show empty state
                    ?>
                    <div class="no-posts">
                        <p>Content loading. Check back soon for updates!</p>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Join the Community</h2>
                <p class="cta-text">
                    Connect with other builders, share your journey, and grow together.
                </p>
                <a href="<?php echo esc_url(home_url('/community')); ?>" class="btn btn-primary btn-large">
                    Join Community
                </a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>