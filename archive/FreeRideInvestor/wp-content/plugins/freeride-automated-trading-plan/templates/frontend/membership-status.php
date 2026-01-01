<?php
/**
 * Membership Status Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$status = FRATP_Membership::get_membership_status();
?>

<div class="fratp-membership-status">
    <div class="fratp-status-badge fratp-status-<?php echo esc_attr($status['type']); ?>">
        <?php if ($status['type'] === 'premium'): ?>
            <span class="fratp-badge-icon">⭐</span>
            <span class="fratp-badge-text"><?php _e('Premium Member', 'freeride-automated-trading-plan'); ?></span>
        <?php elseif ($status['type'] === 'free'): ?>
            <span class="fratp-badge-icon">👤</span>
            <span class="fratp-badge-text"><?php _e('Free Member', 'freeride-automated-trading-plan'); ?></span>
        <?php else: ?>
            <span class="fratp-badge-icon">🔒</span>
            <span class="fratp-badge-text"><?php _e('Not Logged In', 'freeride-automated-trading-plan'); ?></span>
        <?php endif; ?>
    </div>
    
    <p class="fratp-status-message"><?php echo esc_html($status['message']); ?></p>
    
    <?php if ($status['type'] !== 'premium'): ?>
        <a href="<?php echo esc_url(FRATP_Membership::get_premium_signup_url()); ?>" class="fratp-btn-primary">
            <?php _e('Upgrade to Premium', 'freeride-automated-trading-plan'); ?>
        </a>
    <?php endif; ?>
</div>

