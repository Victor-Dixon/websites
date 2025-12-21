<?php

/**
 * Report Page Template
 * 
 * @package FreeRideInvestor_Modern
 */

get_header();
?>

<div class="container">
    <div class="content-area">
        <div class="main-content">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('report-page'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <?php
                    // Check if this is a premium report
                    $is_premium = strpos(get_permalink(), 'premium') !== false;

                    if ($is_premium):
                        // Check if user has access (you can implement your own logic here)
                        $has_access = false;

                        // Simple check - you can integrate with payment system
                        if (isset($_GET['access_token']) || is_user_logged_in()) {
                            $has_access = true;
                        }

                        if (!$has_access):
                    ?>
                            <div class="premium-gate">
                                <div class="premium-gate-content">
                                    <h2>Premium Report Access</h2>
                                    <p>This premium report includes:</p>
                                    <ul>
                                        <li>Complete backtesting results with performance metrics</li>
                                        <li>Trade-by-trade analysis</li>
                                        <li>Optimization suggestions</li>
                                        <li>Full PineScript code download</li>
                                        <li>Email support for strategy questions</li>
                                    </ul>

                                    <div class="pricing">
                                        <div class="price">$9.99</div>
                                        <p class="price-note">One-time payment for lifetime access</p>
                                    </div>

                                    <form action="/wp-admin/admin-post.php" method="post" class="payment-form">
                                        <input type="hidden" name="action" value="purchase_premium_report">
                                        <input type="hidden" name="report_id" value="<?php echo get_the_ID(); ?>">
                                        <input type="hidden" name="report_url" value="<?php echo esc_url(get_permalink()); ?>">

                                        <button type="submit" class="btn btn-primary btn-large">Purchase Premium Report</button>
                                    </form>

                                    <p class="alternative-link">
                                        <a href="<?php echo esc_url(str_replace('-premium', '', get_permalink())); ?>">View Free Report Instead</a>
                                    </p>
                                </div>
                            </div>
                    <?php
                        endif;
                    endif;
                    ?>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php
get_footer();

