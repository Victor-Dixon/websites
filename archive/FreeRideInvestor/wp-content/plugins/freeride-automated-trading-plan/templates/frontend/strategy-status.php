<?php
/**
 * Frontend Strategy Status Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="fratp-strategy-status">
    <h3><?php printf(__('Strategy Status: %s', 'freeride-automated-trading-plan'), esc_html($status['symbol'])); ?></h3>
    
    <div class="fratp-status-grid">
        <div class="fratp-status-item">
            <span class="fratp-label"><?php _e('Current Price:', 'freeride-automated-trading-plan'); ?></span>
            <span class="fratp-value">$<?php echo number_format($status['price'], 2); ?></span>
        </div>
        
        <div class="fratp-status-item">
            <span class="fratp-label"><?php _e('MA 50:', 'freeride-automated-trading-plan'); ?></span>
            <span class="fratp-value">$<?php echo number_format($status['ma_short'], 2); ?></span>
        </div>
        
        <div class="fratp-status-item">
            <span class="fratp-label"><?php _e('MA 200:', 'freeride-automated-trading-plan'); ?></span>
            <span class="fratp-value">$<?php echo number_format($status['ma_long'], 2); ?></span>
        </div>
        
        <div class="fratp-status-item">
            <span class="fratp-label"><?php _e('RSI:', 'freeride-automated-trading-plan'); ?></span>
            <span class="fratp-value"><?php echo number_format($status['rsi'], 2); ?></span>
        </div>
        
        <div class="fratp-status-item">
            <span class="fratp-label"><?php _e('Signal:', 'freeride-automated-trading-plan'); ?></span>
            <span class="fratp-value fratp-signal-<?php echo esc_attr($status['signal']); ?>">
                <?php echo strtoupper(esc_html($status['signal'])); ?>
            </span>
        </div>
    </div>
    
    <div class="fratp-conditions">
        <h4><?php _e('Entry Conditions', 'freeride-automated-trading-plan'); ?></h4>
        <ul>
            <li class="<?php echo $status['long_condition'] ? 'fratp-active' : ''; ?>">
                <?php _e('Long Condition:', 'freeride-automated-trading-plan'); ?>
                <?php echo $status['long_condition'] ? __('✅ Active', 'freeride-automated-trading-plan') : __('❌ Inactive', 'freeride-automated-trading-plan'); ?>
            </li>
            <li class="<?php echo $status['short_condition'] ? 'fratp-active' : ''; ?>">
                <?php _e('Short Condition:', 'freeride-automated-trading-plan'); ?>
                <?php echo $status['short_condition'] ? __('✅ Active', 'freeride-automated-trading-plan') : __('❌ Inactive', 'freeride-automated-trading-plan'); ?>
            </li>
        </ul>
    </div>
</div>

