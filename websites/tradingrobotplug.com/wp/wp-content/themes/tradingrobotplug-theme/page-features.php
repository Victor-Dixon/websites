<?php
/**
 * Features Page Template
 * Showcases platform features and capabilities
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
            <h1 class="gradient-text">Features</h1>
            <p class="hero-subheadline">AI-powered trading robots built by our swarm of specialized agents. Here's what we're building.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="max-width: 1000px; margin: 0 auto;">
            <h2 style="text-align: center; margin-bottom: 48px;">What We're Building</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px;">
                <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="font-size: 48px; margin-bottom: 16px;">🤖</div>
                    <h3 style="color: #667eea; margin-bottom: 16px;">AI-Powered Trading</h3>
                    <p style="color: #666; line-height: 1.8;">Automated trading robots powered by advanced AI algorithms. Execute trades 24/7 without emotion or fatigue.</p>
                </div>
                
                <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
                    <h3 style="color: #667eea; margin-bottom: 16px;">Real-Time Analytics</h3>
                    <p style="color: #666; line-height: 1.8;">Track performance with live dashboards. Monitor P&L, win rates, drawdowns, and more in real-time.</p>
                </div>
                
                <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="font-size: 48px; margin-bottom: 16px;">🛡️</div>
                    <h3 style="color: #667eea; margin-bottom: 16px;">Risk Management</h3>
                    <p style="color: #666; line-height: 1.8;">Built-in risk controls including position sizing, stop losses, and portfolio diversification to protect your capital.</p>
                </div>
                
                <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="font-size: 48px; margin-bottom: 16px;">📈</div>
                    <h3 style="color: #667eea; margin-bottom: 16px;">Paper Trading First</h3>
                    <p style="color: #666; line-height: 1.8;">All strategies validated through paper trading before going live. We only launch what's proven to work.</p>
                </div>
                
                <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="font-size: 48px; margin-bottom: 16px;">🐝</div>
                    <h3 style="color: #667eea; margin-bottom: 16px;">Swarm Intelligence</h3>
                    <p style="color: #666; line-height: 1.8;">Built by 8 specialized AI agents working in parallel. Each agent is a master of its domain.</p>
                </div>
                
                <div style="background: white; padding: 32px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="font-size: 48px; margin-bottom: 16px;">🔧</div>
                    <h3 style="color: #667eea; margin-bottom: 16px;">Custom Strategies</h3>
                    <p style="color: #666; line-height: 1.8;">Work with our team to develop custom trading strategies tailored to your risk profile and goals.</p>
                </div>
            </div>
            
            <div style="margin-top: 64px; text-align: center; padding: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; color: white;">
                <h2 style="color: white; margin-bottom: 16px;">Currently in Building Mode</h2>
                <p style="color: rgba(255,255,255,0.9); max-width: 600px; margin: 0 auto 24px;">We're testing and validating trading strategies right now. Join our waitlist to be notified when we launch.</p>
                <a href="<?php echo esc_url(home_url('/waitlist')); ?>" class="cta-button" style="background: white; color: #667eea;">Join Waitlist →</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>


