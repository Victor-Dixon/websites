<?php
/**
 * Waitlist Page Template
 * Join the early access waitlist for trading robots
 * 
 * @package TradingRobotPlug
 * @version 1.0.0
 * @since 2025-12-28
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="gradient-text">Join the Waitlist</h1>
            <p class="hero-subheadline">Be first in line when our AI-powered trading robots launch. Get early access, exclusive updates, and priority support.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto; text-align: center;">
            <h2 style="margin-bottom: 24px;">Get Early Access</h2>
            <p style="color: #666; margin-bottom: 32px;">Join our waitlist to be notified when we launch. Early access members get priority access and exclusive benefits.</p>
            
            <div class="subscription-form low-friction" style="background: #f9f9f9; padding: 40px; border-radius: 16px;">
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST" class="subscription-form-simple">
                    <?php wp_nonce_field('waitlist_form', 'waitlist_nonce'); ?>
                    <input type="hidden" name="action" value="handle_waitlist_form">
                    <input 
                        type="email" 
                        name="email" 
                        class="email-only-input" 
                        placeholder="Enter your email address" 
                        required
                        style="width: 100%; padding: 16px; font-size: 16px; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 16px;"
                    >
                    <button type="submit" class="cta-button primary" style="width: 100%;">Join Waitlist →</button>
                </form>
                <p style="color: #888; font-size: 14px; margin-top: 16px;">We respect your privacy. No spam, ever.</p>
            </div>
            
            <div style="margin-top: 48px;">
                <h3 style="margin-bottom: 24px;">What You'll Get</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; text-align: left;">
                    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <div style="font-size: 24px; margin-bottom: 12px;">🚀</div>
                        <h4 style="margin: 0 0 8px 0; color: #667eea;">Early Access</h4>
                        <p style="color: #666; margin: 0; font-size: 14px;">Be first to try our trading robots before public launch.</p>
                    </div>
                    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <div style="font-size: 24px; margin-bottom: 12px;">📊</div>
                        <h4 style="margin: 0 0 8px 0; color: #667eea;">Progress Updates</h4>
                        <p style="color: #666; margin: 0; font-size: 14px;">Watch our AI swarm build and test trading strategies in real-time.</p>
                    </div>
                    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <div style="font-size: 24px; margin-bottom: 12px;">💎</div>
                        <h4 style="margin: 0 0 8px 0; color: #667eea;">Exclusive Benefits</h4>
                        <p style="color: #666; margin: 0; font-size: 14px;">Special pricing and features for early supporters.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>


