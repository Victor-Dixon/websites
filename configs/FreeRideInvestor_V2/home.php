<?php
/**
 * Template for the home page - Personal TSLA Trading Journal
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <!-- Hero Section - Personal Voice -->
        <section class="hero-section">
            <div class="hero-content">
                <h1><?php _e('FreeRideInvestor', 'freerideinvestor'); ?></h1>
                <p class="hero-subtitle"><?php _e('A real trader\'s daily TSLA operating journal. Daily plans, recap notes, rules, and lessons.', 'freerideinvestor'); ?></p>
            </div>
        </section>

        <!-- Today's TSLA Plan - Featured Daily Plan -->
        <section class="todays-plan-section">
            <?php
            // Get the latest Daily Plan post
            $featured_plan = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'category_name'  => 'daily-plans',
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));

            if ($featured_plan->have_posts()) :
                $featured_plan->the_post();
                ?>
                <div class="todays-plan-card featured-plan">
                    <div class="plan-header">
                        <h2 class="plan-title">
                            <span class="plan-icon">📋</span>
                            <?php _e("Today's TSLA Plan", 'freerideinvestor'); ?>
                            <span class="plan-date"><?php echo get_the_date('F j, Y'); ?></span>
                        </h2>
                    </div>
                    <div class="plan-content">
                        <div class="plan-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn-primary">
                            <?php _e('View Full Plan', 'freerideinvestor'); ?>
                        </a>
                    </div>
                </div>
                <?php
                wp_reset_postdata();
            else :
                // Show placeholder if no Daily Plans exist yet
                ?>
                <div class="todays-plan-card featured-plan">
                    <div class="plan-header">
                        <h2 class="plan-title">
                            <span class="plan-icon">📋</span>
                            <?php _e("Today's TSLA Plan", 'freerideinvestor'); ?>
                            <span class="plan-date"><?php echo date_i18n('F j, Y'); ?></span>
                        </h2>
                    </div>
                    <div class="plan-content">
                        <p><?php _e('Daily plan coming soon. Check back for today\'s TSLA trading plan, market bias, watchlist, and rules.', 'freerideinvestor'); ?></p>
                    </div>
                </div>
                <?php
            endif;
            ?>
        </section>

        <!-- Recent Journal Entries - Last 3 Daily Plans -->
        <section class="journal-entries-section">
            <h2><?php _e('Recent Journal Entries', 'freerideinvestor'); ?></h2>
            <p class="section-description"><?php _e('Daily plans and trade recaps from my TSLA trading sessions.', 'freerideinvestor'); ?></p>

            <?php
            // Get last 3 Daily Plans (excluding the featured one)
            $recent_plans = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'category_name'  => 'daily-plans',
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'offset'         => $featured_plan->found_posts > 0 ? 1 : 0, // Skip featured if exists
            ));

            if ($recent_plans->have_posts()) :
                ?>
                <div class="journal-grid">
                    <?php
                    while ($recent_plans->have_posts()) :
                        $recent_plans->the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card journal-card'); ?>>
                            <header class="entry-header">
                                <h3 class="entry-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <div class="entry-meta">
                                    <span class="posted-on">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                </div>
                            </header>
                            <div class="entry-content">
                                <?php the_excerpt(); ?>
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="btn">
                                    <?php _e('Read Journal Entry', 'freerideinvestor'); ?>
                                </a>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <?php
            else :
                ?>
                <p class="no-content"><?php _e('No journal entries yet. Check back soon for daily trading plans and recaps.', 'freerideinvestor'); ?></p>
                <?php
            endif;
            ?>
        </section>

        <!-- TBOW Tactics - Only show if populated -->
        <section class="tbow-tactics-section">
            <?php
            // Check if TBOW Tactics category has posts
            $tbow_query = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'category_name'  => 'tbow-tactics',
                'post_status'    => 'publish',
            ));

            if ($tbow_query->have_posts()) :
                ?>
                <h2><?php _e('TBOW Tactics', 'freerideinvestor'); ?></h2>
                <p class="section-description"><?php _e('Trading strategies and tactical insights.', 'freerideinvestor'); ?></p>

                <div class="tactics-grid">
                    <?php
                    // Reset and get more posts
                    wp_reset_postdata();
                    $tbow_full = new WP_Query(array(
                        'post_type'      => 'post',
                        'posts_per_page' => 6,
                        'category_name'  => 'tbow-tactics',
                        'post_status'    => 'publish',
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    ));

                    while ($tbow_full->have_posts()) :
                        $tbow_full->the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card tactic-card'); ?>>
                            <header class="entry-header">
                                <h3 class="entry-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <div class="entry-meta">
                                    <span class="posted-on">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                </div>
                            </header>
                            <div class="entry-content">
                                <?php the_excerpt(); ?>
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="btn">
                                    <?php _e('Read Tactic', 'freerideinvestor'); ?>
                                </a>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <?php
            endif;
            // If no TBOW Tactics posts, section is hidden (no output)
            ?>
        </section>

        <!-- Deep Dives / Education Articles -->
        <section class="articles-section">
            <h2><?php _e('Deep Dives & Education', 'freerideinvestor'); ?></h2>
            <p class="section-description"><?php _e('Long-form analysis, strategy breakdowns, and educational content.', 'freerideinvestor'); ?></p>

            <?php
            // Get articles (exclude Daily Plans and TBOW Tactics)
            $articles = new WP_Query(array(
                'post_type'      => 'post',
                'posts_per_page' => 6,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'category__not_in' => array(
                    get_cat_ID('daily-plans'),
                    get_cat_ID('tbow-tactics'),
                ),
            ));

            if ($articles->have_posts()) :
                ?>
                <div class="blog-grid">
                    <?php
                    $seen_titles = array(); // Prevent duplicate articles
                    while ($articles->have_posts()) :
                        $articles->the_post();
                        $title = get_the_title();
                        
                        // Skip if we've already shown this title
                        if (in_array($title, $seen_titles)) {
                            continue;
                        }
                        $seen_titles[] = $title;
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card article-card'); ?>>
                            <header class="entry-header">
                                <?php
                                if (has_post_thumbnail()) :
                                    the_post_thumbnail('medium', array('class' => 'post-thumbnail'));
                                endif;
                                ?>
                                <h3 class="entry-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <div class="entry-meta">
                                    <span class="posted-on">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <?php
                                    $author = get_the_author();
                                    if ($author && $author !== 'FreeRideInvestor Team') :
                                        ?>
                                        <span class="byline">
                                            <?php _e('by', 'freerideinvestor'); ?> <?php echo esc_html($author); ?>
                                        </span>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </header>
                            <div class="entry-content">
                                <?php the_excerpt(); ?>
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="btn">
                                    <?php _e('Read Article', 'freerideinvestor'); ?>
                                </a>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>

                <div class="text-center" style="margin-top: 3rem;">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="btn btn-secondary">
                        <?php _e('View All Articles', 'freerideinvestor'); ?>
                    </a>
                </div>
                <?php
            else :
                ?>
                <p class="no-content"><?php _e('No articles yet. Check back soon for trading insights and educational content.', 'freerideinvestor'); ?></p>
                <?php
            endif;
            ?>
        </section>
    </div>
</main>

<?php
get_footer();
