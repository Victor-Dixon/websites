<?php
/**
 * Pricing Page Template
 * Displays pricing tiers (currently showing building mode status)
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
            <h1 class="gradient-text">Pricing</h1>
            <p class="hero-subheadline">We're still in building mode. Pricing will be announced when our trading robots are validated and ready for launch.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto; text-align: center;">
            <h2 style="margin-bottom: 24px;">Coming Soon</h2>
            <p style="color: #666; margin-bottom: 48px;">We're focused on building a winning trading bot first. Once we validate our strategies through paper trading, we'll announce pricing.</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <div style="background: #f9f9f9; padding: 32px; border-radius: 16px; border: 2px solid #e0e0e0;">
                    <h3 style="color: #667eea; margin-bottom: 16px;">Starter</h3>
                    <div style="font-size: 48px; font-weight: bold; color: #333; margin-bottom: 16px;">TBD</div>
                    <p style="color: #666; margin-bottom: 24px;">Perfect for getting started with automated trading.</p>
                    <ul style="text-align: left; color: #666; list-style: none; padding: 0;">
                        <li style="padding: 8px 0;">✓ Basic trading bot access</li>
                        <li style="padding: 8px 0;">✓ Paper trading mode</li>
                        <li style="padding: 8px 0;">✓ Email support</li>
                    </ul>
                </div>
                
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 32px; border-radius: 16px; color: white; transform: scale(1.05);">
                    <div style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; display: inline-block; font-size: 12px; margin-bottom: 16px;">MOST POPULAR</div>
                    <h3 style="color: white; margin-bottom: 16px;">Pro</h3>
                    <div style="font-size: 48px; font-weight: bold; margin-bottom: 16px;">TBD</div>
                    <p style="color: rgba(255,255,255,0.9); margin-bottom: 24px;">For serious traders who want the full experience.</p>
                    <ul style="text-align: left; color: rgba(255,255,255,0.9); list-style: none; padding: 0;">
                        <li style="padding: 8px 0;">✓ All Starter features</li>
                        <li style="padding: 8px 0;">✓ Advanced strategies</li>
                        <li style="padding: 8px 0;">✓ Real-time analytics</li>
                        <li style="padding: 8px 0;">✓ Priority support</li>
                    </ul>
                </div>
                
                <div style="background: #f9f9f9; padding: 32px; border-radius: 16px; border: 2px solid #e0e0e0;">
                    <h3 style="color: #667eea; margin-bottom: 16px;">Enterprise</h3>
                    <div style="font-size: 48px; font-weight: bold; color: #333; margin-bottom: 16px;">Custom</div>
                    <p style="color: #666; margin-bottom: 24px;">For teams and institutions with custom needs.</p>
                    <ul style="text-align: left; color: #666; list-style: none; padding: 0;">
                        <li style="padding: 8px 0;">✓ All Pro features</li>
                        <li style="padding: 8px 0;">✓ Custom strategies</li>
                        <li style="padding: 8px 0;">✓ Dedicated support</li>
                        <li style="padding: 8px 0;">✓ API access</li>
                    </ul>
                </div>
            </div>
            
            <div style="margin-top: 48px; padding: 32px; background: #f0f4ff; border-radius: 16px;">
                <h3 style="margin-bottom: 16px;">Be First to Know</h3>
                <p style="color: #666; margin-bottom: 24px;">Join our waitlist to get notified when pricing is announced. Early supporters get exclusive benefits.</p>
                <a href="<?php echo esc_url(home_url('/waitlist')); ?>" class="cta-button primary">Join Waitlist →</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>


