<?php
/**
 * Plans List Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_premium = FRATP_Membership::is_premium();
$is_logged_in = FRATP_Membership::is_logged_in();
?>

<div class="fratp-plans-list-page">
    <div class="fratp-plans-header">
        <h1><?php _e('Daily Trading Plans', 'freeride-automated-trading-plan'); ?></h1>
        <p><?php _e('Automated trading plans generated daily based on MA, RSI, and risk management strategies.', 'freeride-automated-trading-plan'); ?></p>
    </div>

    <?php if (!$is_logged_in): ?>
        <div class="fratp-login-prompt">
            <p><?php _e('Please log in to view trading plans.', 'freeride-automated-trading-plan'); ?></p>
            <a href="<?php echo esc_url(FRATP_Membership::get_login_url()); ?>" class="fratp-btn-primary">
                <?php _e('Log In', 'freeride-automated-trading-plan'); ?>
            </a>
        </div>
    <?php elseif (!$is_premium): ?>
        <div class="fratp-upgrade-prompt">
            <p><?php _e('Upgrade to premium to view all daily trading plans.', 'freeride-automated-trading-plan'); ?></p>
            <a href="<?php echo esc_url(FRATP_Membership::get_premium_signup_url()); ?>" class="fratp-btn-primary">
                <?php _e('Upgrade to Premium', 'freeride-automated-trading-plan'); ?>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($is_premium && !empty($plans)): ?>
        <div class="fratp-plans-grid">
            <?php foreach ($plans as $plan): ?>
                <div class="fratp-plan-card">
                    <div class="fratp-plan-header">
                        <h2><?php echo esc_html($plan['symbol']); ?></h2>
                        <span class="fratp-plan-date"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($plan['date']))); ?></span>
                    </div>
                    
                    <div class="fratp-plan-summary">
                        <div class="fratp-plan-price">
                            <strong><?php _e('Price:', 'freeride-automated-trading-plan'); ?></strong>
                            $<?php echo number_format($plan['current_price'], 2); ?>
                        </div>
                        <div class="fratp-plan-signal">
                            <strong><?php _e('Signal:', 'freeride-automated-trading-plan'); ?></strong>
                            <span class="fratp-signal-<?php echo esc_attr($plan['signal']); ?>">
                                <?php echo strtoupper(esc_html($plan['signal'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="fratp-plan-actions">
                        <a href="<?php echo esc_url(add_query_arg(array('symbol' => $plan['symbol'], 'date' => $plan['date']), home_url('/trading-plans'))); ?>" class="fratp-btn-secondary">
                            <?php _e('View Full Plan', 'freeride-automated-trading-plan'); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($is_premium && empty($plans)): ?>
        <div class="fratp-no-plans">
            <p><?php _e('No trading plans available yet. Plans are generated daily at market open.', 'freeride-automated-trading-plan'); ?></p>
        </div>
    <?php endif; ?>
</div>

