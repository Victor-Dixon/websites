<?php
/**
 * Template Name: Performance Dashboard
 */

get_header(); ?>

<main id="primary" class="site-main">
    <div class="container page-header">
        <h1>Performance Dashboard</h1>
        <p>Track your trading metrics and analyze robot performance.</p>
    </div>

    <div class="container">
        <!-- Render Performance Shortcode -->
        <?php echo do_shortcode('[trading_robot_performance]'); ?>
        
        <div class="public-leaderboard-section">
            <h3>Top Performers (Public)</h3>
            <p>See how other traders are performing on the platform.</p>
            <!-- Mock Leaderboard Table -->
            <table class="leaderboard-table" style="width:100%; text-align:left; border-collapse:collapse; margin-top:20px;">
                <thead>
                    <tr style="border-bottom:2px solid #eee;">
                        <th style="padding:10px;">Rank</th>
                        <th style="padding:10px;">Trader</th>
                        <th style="padding:10px;">Win Rate</th>
                        <th style="padding:10px;">Total P&L</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;">1</td>
                        <td style="padding:10px;">TraderX</td>
                        <td style="padding:10px;">72%</td>
                        <td style="padding:10px; color:green;">+$5,400</td>
                    </tr>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;">2</td>
                        <td style="padding:10px;">AlphaBot</td>
                        <td style="padding:10px;">68%</td>
                        <td style="padding:10px; color:green;">+$4,200</td>
                    </tr>
                    <tr>
                        <td style="padding:10px;">3</td>
                        <td style="padding:10px;">CryptoKing</td>
                        <td style="padding:10px;">65%</td>
                        <td style="padding:10px; color:green;">+$3,800</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php if (!is_user_logged_in()): ?>
            <div class="login-prompt" style="text-align:center; margin-top:40px; padding:30px; background:#f8f9fa; border-radius:8px;">
                <h3>Want to see your own stats?</h3>
                <a href="/pricing" class="btn btn-primary">Start Tracking Today</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.page-header {
    text-align: center;
    padding: 60px 0 40px;
}
.public-leaderboard-section {
    margin-top: 60px;
}
</style>

<?php get_footer(); ?>
