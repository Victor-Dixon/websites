<?php
/**
 * Thank You Page Template
 * Confirmation page after form submission
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
        <div class="hero-content" style="text-align: center;">
            <div style="font-size: 72px; margin-bottom: 24px;">✅</div>
            <h1 class="gradient-text">You're on the List!</h1>
            <p class="hero-subheadline">Thank you for joining our waitlist. We'll notify you as soon as our trading robots are ready for launch.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="max-width: 700px; margin: 0 auto; text-align: center;">
            <h2 style="margin-bottom: 24px;">What Happens Next?</h2>
            
            <div style="display: grid; gap: 24px; text-align: left;">
                <div style="background: #f9f9f9; padding: 24px; border-radius: 12px; display: flex; gap: 16px; align-items: flex-start;">
                    <div style="background: #667eea; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</div>
                    <div>
                        <h4 style="margin: 0 0 8px 0;">Check Your Email</h4>
                        <p style="color: #666; margin: 0;">You'll receive a confirmation email shortly. Make sure to check your spam folder.</p>
                    </div>
                </div>
                
                <div style="background: #f9f9f9; padding: 24px; border-radius: 12px; display: flex; gap: 16px; align-items: flex-start;">
                    <div style="background: #667eea; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</div>
                    <div>
                        <h4 style="margin: 0 0 8px 0;">Watch Our Progress</h4>
                        <p style="color: #666; margin: 0;">Follow along as our AI swarm builds and tests trading strategies in real-time.</p>
                    </div>
                </div>
                
                <div style="background: #f9f9f9; padding: 24px; border-radius: 12px; display: flex; gap: 16px; align-items: flex-start;">
                    <div style="background: #667eea; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</div>
                    <div>
                        <h4 style="margin: 0 0 8px 0;">Get Early Access</h4>
                        <p style="color: #666; margin: 0;">When we launch, you'll be first in line to try our trading robots.</p>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 48px;">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="cta-button primary">← Back to Home</a>
                <a href="<?php echo esc_url(home_url('/#swarm-status')); ?>" class="cta-button secondary" style="margin-left: 16px;">Watch Us Build</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>


