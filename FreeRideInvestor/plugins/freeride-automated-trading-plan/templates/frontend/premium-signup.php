<?php
/**
 * Premium Signup Template - Sales Funnel
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_premium = FRATP_Membership::is_premium();
$is_logged_in = FRATP_Membership::is_logged_in();
$premium_price = get_option('fratp_premium_price', '29.99');
$features = explode(',', get_option('fratp_premium_features', 'Daily trading plans, Real-time signals, Risk management tools, Options strategies'));
?>

<div class="fratp-premium-signup">
    <?php if ($is_premium): ?>
        <div class="fratp-premium-active">
            <h2><?php _e('✅ You Have Premium Access!', 'freeride-automated-trading-plan'); ?></h2>
            <p><?php _e('Thank you for being a premium member. You have full access to all daily trading plans.', 'freeride-automated-trading-plan'); ?></p>
            <a href="<?php echo esc_url(home_url('/trading-plans')); ?>" class="fratp-btn-primary">
                <?php _e('View Trading Plans', 'freeride-automated-trading-plan'); ?>
            </a>
        </div>
    <?php else: ?>
        <div class="fratp-premium-hero">
            <h1><?php echo esc_html($atts['title']); ?></h1>
            <p class="fratp-subtitle"><?php _e('Get Daily Automated Trading Plans Delivered to Your Inbox', 'freeride-automated-trading-plan'); ?></p>
        </div>

        <div class="fratp-premium-benefits">
            <h2><?php _e('What You Get:', 'freeride-automated-trading-plan'); ?></h2>
            <ul class="fratp-features-list">
                <?php foreach ($features as $feature): ?>
                    <li>✅ <?php echo esc_html(trim($feature)); ?></li>
                <?php endforeach; ?>
                <li>✅ <?php _e('TBOW Tactic Posts - Full Strategy Breakdowns', 'freeride-automated-trading-plan'); ?></li>
                <li>✅ <?php _e('Real-time Entry/Exit Signals', 'freeride-automated-trading-plan'); ?></li>
                <li>✅ <?php _e('Risk Management Calculations', 'freeride-automated-trading-plan'); ?></li>
                <li>✅ <?php _e('Position Sizing Recommendations', 'freeride-automated-trading-plan'); ?></li>
                <li>✅ <?php _e('Options Strategy Suggestions', 'freeride-automated-trading-plan'); ?></li>
            </ul>
        </div>

        <div class="fratp-pricing-box">
            <div class="fratp-price">
                <span class="fratp-currency">$</span>
                <span class="fratp-amount"><?php echo esc_html($premium_price); ?></span>
                <span class="fratp-period"><?php _e('/month', 'freeride-automated-trading-plan'); ?></span>
            </div>
            <p class="fratp-price-note"><?php _e('Cancel anytime. No long-term commitment.', 'freeride-automated-trading-plan'); ?></p>
        </div>

        <div class="fratp-signup-actions">
            <?php if (!$is_logged_in): ?>
                <p class="fratp-login-prompt">
                    <?php _e('Already have an account?', 'freeride-automated-trading-plan'); ?>
                    <a href="<?php echo esc_url(FRATP_Membership::get_login_url()); ?>">
                        <?php _e('Log in here', 'freeride-automated-trading-plan'); ?>
                    </a>
                </p>
                <a href="<?php echo esc_url(wp_registration_url()); ?>" class="fratp-btn-primary fratp-btn-large">
                    <?php _e('Sign Up for Premium', 'freeride-automated-trading-plan'); ?>
                </a>
            <?php else: ?>
                <button id="fratp-upgrade-btn" class="fratp-btn-primary fratp-btn-large">
                    <?php _e('Upgrade to Premium Now', 'freeride-automated-trading-plan'); ?>
                </button>
                <p class="fratp-upgrade-note">
                    <?php _e('You will be redirected to complete your payment.', 'freeride-automated-trading-plan'); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="fratp-testimonials">
            <h3><?php _e('What Our Members Say:', 'freeride-automated-trading-plan'); ?></h3>
            <blockquote>
                <p>"<?php _e('The daily plans have transformed my trading. Clear signals and risk management make all the difference.', 'freeride-automated-trading-plan'); ?>"</p>
                <cite>— <?php _e('Premium Member', 'freeride-automated-trading-plan'); ?></cite>
            </blockquote>
        </div>

        <div class="fratp-guarantee">
            <h3><?php _e('30-Day Money-Back Guarantee', 'freeride-automated-trading-plan'); ?></h3>
            <p><?php _e('Not satisfied? Get a full refund within 30 days. No questions asked.', 'freeride-automated-trading-plan'); ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('#fratp-upgrade-btn').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).text('<?php _e('Processing...', 'freeride-automated-trading-plan'); ?>');
        
        $.ajax({
            url: fratp_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'fratp_upgrade_premium',
                nonce: fratp_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect || '<?php echo esc_url(home_url('/trading-plans')); ?>';
                } else {
                    alert(response.data.message || '<?php _e('Error upgrading. Please try again.', 'freeride-automated-trading-plan'); ?>');
                    $btn.prop('disabled', false).text('<?php _e('Upgrade to Premium Now', 'freeride-automated-trading-plan'); ?>');
                }
            },
            error: function() {
                alert('<?php _e('Error upgrading. Please try again.', 'freeride-automated-trading-plan'); ?>');
                $btn.prop('disabled', false).text('<?php _e('Upgrade to Premium Now', 'freeride-automated-trading-plan'); ?>');
            }
        });
    });
});
</script>



