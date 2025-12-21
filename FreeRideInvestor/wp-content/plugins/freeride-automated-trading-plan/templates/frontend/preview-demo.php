<?php
/**
 * Preview/Demo Template - Shows Free vs Premium Views
 * Use this to preview what users see
 */

if (!defined('ABSPATH')) {
    exit;
}

// Create a sample plan for preview
$sample_plan = array(
    'symbol' => 'TSLA',
    'date' => current_time('Y-m-d'),
    'current_price' => 245.50,
    'signal' => 'long',
    'ma_short' => 240.25,
    'ma_long' => 235.80,
    'rsi' => 55.5,
    'recommendation' => 'BUY signal detected. Price is above both moving averages (MA50: $240.25, MA200: $235.80) and RSI (55.5) is not overbought. Consider entering a long position.',
    'action_items' => array(
        'Enter LONG position at $245.50',
        'Set stop loss at $243.05',
        'Set profit target at $282.33',
        'Position size: 205 shares',
        'Enable trailing stop after trigger',
        'Monitor position and adjust stops as needed'
    ),
    'risk_metrics' => array(
        'equity' => 1000000,
        'risk_per_trade' => 5000,
        'risk_percentage' => 0.5
    ),
    'trade' => array(
        'direction' => 'long',
        'entry_price' => 245.50,
        'position_size' => 205,
        'stop_loss' => 243.05,
        'profit_target' => 282.33,
        'risk_amount' => 5000,
        'risk_reward_ratio' => 15.0
    )
);
?>

<style>
.preview-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}
.preview-section {
    margin: 40px 0;
    padding: 30px;
    background: #f9f9f9;
    border-radius: 8px;
}
.preview-header {
    background: #0073aa;
    color: white;
    padding: 20px;
    border-radius: 8px 8px 0 0;
    margin: -30px -30px 20px -30px;
}
.preview-comparison {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 30px;
}
.preview-box {
    background: white;
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 20px;
}
.preview-box.free {
    border-color: #f0b90b;
}
.preview-box.premium {
    border-color: #00a32a;
}
.preview-badge {
    display: inline-block;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 15px;
}
.badge-free {
    background: #fff9e6;
    color: #f0b90b;
}
.badge-premium {
    background: #e7f5e7;
    color: #00a32a;
}
@media (max-width: 768px) {
    .preview-comparison {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="preview-container">
    <h1>FreeRide Trading Plans - Preview</h1>
    <p>See what Free vs Premium users see</p>

    <!-- FREE USER VIEW -->
    <div class="preview-section">
        <div class="preview-header">
            <h2>🔒 FREE USER VIEW</h2>
            <p>What free members see when trying to access a trading plan</p>
        </div>
        
        <div class="preview-box free">
            <span class="preview-badge badge-free">FREE MEMBER</span>
            
            <?php
            // Show access denied template
            $is_logged_in = true; // Simulate logged in free user
            $is_premium = false; // Free user
            $plan = $sample_plan; // For template
            ?>
            <div class="fratp-access-denied">
                <div class="fratp-access-message">
                    <h2><?php _e('🔒 Premium Content', 'freeride-automated-trading-plan'); ?></h2>
                    <p class="fratp-access-text">
                        <?php _e('This trading plan is available to premium members only. Sign up to get daily automated trading plans delivered to your inbox.', 'freeride-automated-trading-plan'); ?>
                    </p>
                    <div class="fratp-access-actions">
                        <a href="<?php echo esc_url(home_url('/premium-signup')); ?>" class="fratp-btn-primary">
                            <?php _e('Upgrade to Premium', 'freeride-automated-trading-plan'); ?>
                        </a>
                    </div>
                </div>
                <div class="fratp-preview-box">
                    <h3><?php _e('What You\'ll Get with Premium:', 'freeride-automated-trading-plan'); ?></h3>
                    <ul>
                        <li>✅ <?php _e('Daily automated trading plans', 'freeride-automated-trading-plan'); ?></li>
                        <li>✅ <?php _e('Real-time entry/exit signals', 'freeride-automated-trading-plan'); ?></li>
                        <li>✅ <?php _e('Risk management calculations', 'freeride-automated-trading-plan'); ?></li>
                        <li>✅ <?php _e('Position sizing recommendations', 'freeride-automated-trading-plan'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- PREMIUM USER VIEW -->
    <div class="preview-section">
        <div class="preview-header">
            <h2>⭐ PREMIUM USER VIEW</h2>
            <p>What premium members see - Full trading plan</p>
        </div>
        
        <div class="preview-box premium">
            <span class="preview-badge badge-premium">PREMIUM MEMBER</span>
            
            <?php
            // Show full plan template - we'll render it inline for preview
            $plan = $sample_plan;
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
                
                <?php if (isset($plan['trade'])): ?>
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
                            <td><?php _e('Risk/Reward:', 'freeride-automated-trading-plan'); ?></td>
                            <td><strong><?php echo number_format($plan['trade']['risk_reward_ratio'], 2); ?>:1</strong></td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
                
                <div class="fratp-action-items">
                    <h3><?php _e('Action Items', 'freeride-automated-trading-plan'); ?></h3>
                    <ul>
                        <?php foreach ($plan['action_items'] as $item): ?>
                        <li>✅ <?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- SIDE BY SIDE COMPARISON -->
    <div class="preview-section">
        <h2>Side-by-Side Comparison</h2>
        
        <div class="preview-comparison">
            <!-- FREE -->
            <div class="preview-box free">
                <h3>🔒 Free User Experience</h3>
                <ul>
                    <li>❌ Cannot view trading plans</li>
                    <li>✅ Can see "Access Denied" message</li>
                    <li>✅ Sees upgrade prompt</li>
                    <li>✅ Can sign up for premium</li>
                    <li>❌ No plan details visible</li>
                    <li>❌ No entry/exit signals</li>
                    <li>❌ No risk management info</li>
                </ul>
                <div style="margin-top: 20px; padding: 15px; background: #fff9e6; border-radius: 5px;">
                    <strong>Conversion Point:</strong>
                    <p>User sees value but can't access → Upgrades to premium</p>
                </div>
            </div>

            <!-- PREMIUM -->
            <div class="preview-box premium">
                <h3>⭐ Premium User Experience</h3>
                <ul>
                    <li>✅ Full access to all trading plans</li>
                    <li>✅ Complete plan details</li>
                    <li>✅ Entry/exit signals</li>
                    <li>✅ Position sizing</li>
                    <li>✅ Stop loss & profit targets</li>
                    <li>✅ Risk management metrics</li>
                    <li>✅ Action items checklist</li>
                </ul>
                <div style="margin-top: 20px; padding: 15px; background: #e7f5e7; border-radius: 5px;">
                    <strong>Value Delivered:</strong>
                    <p>Complete trading plan with actionable recommendations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- PREMIUM SIGNUP PAGE PREVIEW -->
    <div class="preview-section">
        <div class="preview-header">
            <h2>💰 PREMIUM SIGNUP PAGE</h2>
            <p>The sales page where users upgrade</p>
        </div>
        
        <div class="preview-box">
            <p><strong>This is the Premium Signup Page</strong> - The main sales/conversion page</p>
            <p>Visit: <code>/premium-signup</code> to see the full page</p>
            <p>Or use shortcode: <code>[fratp_premium_signup]</code></p>
            <div style="margin-top: 20px; padding: 20px; background: #f0f8ff; border-radius: 5px;">
                <h4>What it includes:</h4>
                <ul>
                    <li>✅ Premium pricing display</li>
                    <li>✅ Features list</li>
                    <li>✅ Sign up button</li>
                    <li>✅ Testimonials</li>
                    <li>✅ Money-back guarantee</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- TBOW POST PREVIEW -->
    <div class="preview-section">
        <div class="preview-header">
            <h2>📄 TBOW TACTIC POST PREVIEW</h2>
            <p>What gets published as a WordPress post</p>
        </div>
        
        <div class="preview-box">
            <p><strong>Note:</strong> TBOW posts are full HTML pages published to your WordPress blog.</p>
            <p>They include:</p>
            <ul>
                <li>✅ Contextual Insight</li>
                <li>✅ Tactic Objective</li>
                <li>✅ Key Levels to Watch</li>
                <li>✅ Actionable Steps (Short/Long/Options)</li>
                <li>✅ Real-Time Monitoring</li>
                <li>✅ Risk Management</li>
                <li>✅ Execution Checklist</li>
            </ul>
            <p><em>TBOW posts are visible to everyone (for SEO), but detailed plans require premium access.</em></p>
        </div>
    </div>

    <div style="margin-top: 40px; padding: 20px; background: #e7f5e7; border-radius: 8px;">
        <h3>💡 How to Use This Preview</h3>
        <ol>
            <li><strong>Free View:</strong> Shows what drives conversions (FOMO, value demonstration)</li>
            <li><strong>Premium View:</strong> Shows what users get (complete value delivery)</li>
            <li><strong>Signup Page:</strong> The conversion point between free and premium</li>
            <li><strong>TBOW Posts:</strong> SEO content that drives free traffic</li>
        </ol>
    </div>
</div>

