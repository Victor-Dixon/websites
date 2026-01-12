<?php
/**
 * Blog Page Template
 * Displays blog posts list
 * 
 * @package TradingRobotPlug
 * @version 1.0.0
 * @since 2025-12-28
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<section class="hero" style="padding: 80px 0;">
    <div class="container">
        <div class="hero-content">
            <h1 class="gradient-text">Blog</h1>
            <p class="hero-subheadline">Updates, insights, and progress reports from our AI swarm as we build the ultimate trading platform.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $blog_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 10,
                'paged' => $paged,
            ));
            
            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post();
            ?>
            <article style="background: white; padding: 32px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 24px;">
                <header>
                    <h2 style="margin: 0 0 12px 0;">
                        <a href="<?php the_permalink(); ?>" style="color: #333; text-decoration: none;">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <div style="color: #888; font-size: 14px; margin-bottom: 16px;">
                        <?php echo get_the_date(); ?> • <?php the_author(); ?>
                    </div>
                </header>
                
                <div style="color: #666; line-height: 1.8;">
                    <?php the_excerpt(); ?>
                </div>
                
                <a href="<?php the_permalink(); ?>" style="display: inline-block; margin-top: 16px; color: #667eea; text-decoration: none; font-weight: 500;">
                    Read More →
                </a>
            </article>
            <?php
                endwhile;
                
                // Pagination
                $total_pages = $blog_query->max_num_pages;
                if ($total_pages > 1) :
            ?>
            <div style="text-align: center; margin-top: 48px;">
                <?php
                echo paginate_links(array(
                    'total' => $total_pages,
                    'current' => $paged,
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                ));
                ?>
            </div>
            <?php
                endif;
                wp_reset_postdata();
            else :
            ?>
            <div style="text-align: center; padding: 48px; background: #f9f9f9; border-radius: 16px;">
                <div style="font-size: 48px; margin-bottom: 16px;">📝</div>
                <h3 style="margin-bottom: 16px;">No Posts Yet</h3>
                <p style="color: #666; margin-bottom: 24px;">We're busy building. Blog posts coming soon!</p>
                <a href="<?php echo esc_url(home_url('/waitlist')); ?>" class="cta-button primary">Join Waitlist for Updates</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>


