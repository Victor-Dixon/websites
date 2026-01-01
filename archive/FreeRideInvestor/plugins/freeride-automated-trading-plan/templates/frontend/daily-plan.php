<?php
/**
 * Frontend Daily Plan Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="fratp-daily-plan">
    <div class="fratp-plan-header">
        <h2><?php printf(__('Daily Trading Plan: %s', 'freeride-automated-trading-plan'), esc_html($plan['symbol'])); ?></h2>
        <p class="fratp-plan-date"><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($plan['date']))); ?></p>
    </div>
    
    <div class="fratp-plan-summary">
        <div class="fratp-current-price">
            <strong><?php _e('Current Price:', 'freeride-automated-trading-plan'); ?></strong>
            <span class="fratp-price">$<?php echo number_format($plan['current_price'], 2); ?></span>
        </div>
        
        <div class="fratp-signal">
            <strong><?php _e('Signal:', 'freeride-automated-trading-plan'); ?></strong>
            <span class="fratp-signal-<?php echo esc_attr($plan['signal']); ?>">
                <?php echo strtoupper(esc_html($plan['signal'])); ?>
            </span>
        </div>
    </div>
    
    <div class="fratp-indicators">
        <h3><?php _e('Technical Indicators', 'freeride-automated-trading-plan'); ?></h3>
        <ul>
            <li><?php _e('MA 50:', 'freeride-automated-trading-plan'); ?> <strong>$<?php echo number_format($plan['ma_short'], 2); ?></strong></li>
            <li><?php _e('MA 200:', 'freeride-automated-trading-plan'); ?> <strong>$<?php echo number_format($plan['ma_long'], 2); ?></strong></li>
            <li><?php _e('RSI:', 'freeride-automated-trading-plan'); ?> <strong><?php echo number_format($plan['rsi'], 2); ?></strong></li>
        </ul>
    </div>
    
    <div class="fratp-recommendation">
        <h3><?php _e('Recommendation', 'freeride-automated-trading-plan'); ?></h3>
        <p><?php echo esc_html($plan['recommendation']); ?></p>
    </div>
    
    <?php if (isset($plan['trade']) && $plan['signal'] !== 'none'): ?>
    <div class="fratp-trade-details">
        <h3><?php _e('Trade Details', 'freeride-automated-trading-plan'); ?></h3>
        <table class="fratp-trade-table">
            <tr>
                <td><?php _e('Direction:', 'freeride-automated-trading-plan'); ?></td>
                <td><strong><?php echo strtoupper(esc_html($plan['trade']['direction'])); ?></strong></td>
            </tr>
            <tr>
                <td><?php _e('Entry Price:', 'freeride-automated-trading-plan'); ?></td>
                <td><strong>$<?php echo number_format($plan['trade']['entry_price'], 2); ?></strong></td>
            </tr>
            <tr>
                <td><?php _e('Position Size:', 'freeride-automated-trading-plan'); ?></td>
                <td><strong><?php echo number_format($plan['trade']['position_size']); ?> <?php _e('shares', 'freeride-automated-trading-plan'); ?></strong></td>
            </tr>
            <tr>
                <td><?php _e('Stop Loss:', 'freeride-automated-trading-plan'); ?></td>
                <td><strong>$<?php echo number_format($plan['trade']['stop_loss'], 2); ?></strong></td>
            </tr>
            <tr>
                <td><?php _e('Profit Target:', 'freeride-automated-trading-plan'); ?></td>
                <td><strong>$<?php echo number_format($plan['trade']['profit_target'], 2); ?></strong></td>
            </tr>
            <tr>
                <td><?php _e('Risk Amount:', 'freeride-automated-trading-plan'); ?></td>
                <td><strong>$<?php echo number_format($plan['trade']['risk_amount'], 2); ?></strong></td>
            </tr>
            <tr>
                <td><?php _e('Risk/Reward Ratio:', 'freeride-automated-trading-plan'); ?></td>
                <td><strong><?php echo number_format($plan['trade']['risk_reward_ratio'], 2); ?>:1</strong></td>
            </tr>
            <?php if (isset($plan['trade']['trailing_stop'])): ?>
            <tr>
                <td><?php _e('Trailing Stop:', 'freeride-automated-trading-plan'); ?></td>
                <td>
                    <strong><?php _e('Enabled', 'freeride-automated-trading-plan'); ?></strong><br>
                    <small><?php _e('Trigger:', 'freeride-automated-trading-plan'); ?> $<?php echo number_format($plan['trade']['trailing_stop']['trail_points'], 2); ?></small><br>
                    <small><?php _e('Offset:', 'freeride-automated-trading-plan'); ?> $<?php echo number_format($plan['trade']['trailing_stop']['trail_offset'], 2); ?></small>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>
    
    <div class="fratp-action-items">
        <h3><?php _e('Action Items', 'freeride-automated-trading-plan'); ?></h3>
        <ul>
            <?php foreach ($plan['action_items'] as $item): ?>
            <li><?php echo esc_html($item); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <div class="fratp-risk-metrics">
        <h3><?php _e('Risk Metrics', 'freeride-automated-trading-plan'); ?></h3>
        <ul>
            <li><?php _e('Equity:', 'freeride-automated-trading-plan'); ?> <strong>$<?php echo number_format($plan['risk_metrics']['equity'], 2); ?></strong></li>
            <li><?php _e('Risk per Trade:', 'freeride-automated-trading-plan'); ?> <strong>$<?php echo number_format($plan['risk_metrics']['risk_per_trade'], 2); ?></strong></li>
            <li><?php _e('Risk Percentage:', 'freeride-automated-trading-plan'); ?> <strong><?php echo number_format($plan['risk_metrics']['risk_percentage'], 2); ?>%</strong></li>
        </ul>
    </div>
</div>



