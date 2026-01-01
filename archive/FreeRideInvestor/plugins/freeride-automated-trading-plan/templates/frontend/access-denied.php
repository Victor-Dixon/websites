<?php
/**
 * Access Denied Template - Sales Funnel Entry Point
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_logged_in = FRATP_Membership::is_logged_in();
$is_premium = FRATP_Membership::is_premium();
?>

<div class="fratp-access-denied">
    <div class="fratp-access-message">
        <h2><?php _e('🔒 Premium Content', 'freeride-automated-trading-plan'); ?></h2>
        
        <?php if (!$is_logged_in): ?>
            <p class="fratp-access-text">
                <?php _e('This trading plan is available to premium members only. Sign up to get daily automated trading plans delivered to your inbox.', 'freeride-automated-trading-plan'); ?>
            </p>
            <div class="fratp-access-actions">
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="fratp-btn-primary">
                    <?php _e('Sign Up for Free', 'freeride-automated-trading-plan'); ?>
                </a>
                <a href="<?php echo esc_url(FRATP_Membership::get_login_url()); ?>" class="fratp-btn-secondary">
                    <?php _e('Log In', 'freeride-automated-trading-plan'); ?>
                </a>
            </div>
        <?php elseif (!$is_premium): ?>
            <p class="fratp-access-text">
                <?php _e('Upgrade to premium to access this trading plan and all future daily plans.', 'freeride-automated-trading-plan'); ?>
            </p>
            <div class="fratp-access-actions">
                <a href="<?php echo esc_url(FRATP_Membership::get_premium_signup_url()); ?>" class="fratp-btn-primary">
                    <?php _e('Upgrade to Premium', 'freeride-automated-trading-plan'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="fratp-preview-box">
        <h3><?php _e('What You\'ll Get with Premium:', 'freeride-automated-trading-plan'); ?></h3>
        <ul>
            <li>✅ <?php _e('Daily automated trading plans', 'freeride-automated-trading-plan'); ?></li>
            <li>✅ <?php _e('Real-time entry/exit signals', 'freeride-automated-trading-plan'); ?></li>
            <li>✅ <?php _e('Risk management calculations', 'freeride-automated-trading-plan'); ?></li>
            <li>✅ <?php _e('Position sizing recommendations', 'freeride-automated-trading-plan'); ?></li>
            <li>✅ <?php _e('Options strategy suggestions', 'freeride-automated-trading-plan'); ?></li>
            <li>✅ <?php _e('TBOW tactic posts with full breakdowns', 'freeride-automated-trading-plan'); ?></li>
        </ul>
    </div>
</div>



