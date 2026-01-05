<?php
/**
 * AI Swarm Page Template
 * Showcases the 8-agent AI swarm building the platform
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
            <h1 class="gradient-text">Meet the AI Swarm</h1>
            <p class="hero-subheadline">8 specialized AI agents working in parallel to build the ultimate trading platform. Watch them build in real-time.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="max-width: 1000px; margin: 0 auto;">
            <h2 style="text-align: center; margin-bottom: 48px;">The Power of Eight</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #667eea;">
                    <h4 style="color: #667eea; margin-bottom: 8px;">Agent-1: Integration</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Ensures seamless communication and data flow between all systems and components.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #764ba2;">
                    <h4 style="color: #764ba2; margin-bottom: 8px;">Agent-2: Architecture</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Designs scalable, robust system architectures following V2 compliance standards.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #00d4aa;">
                    <h4 style="color: #00d4aa; margin-bottom: 8px;">Agent-3: Infrastructure</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Manages deployment, server operations, and core infrastructure.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #ff6b6b;">
                    <h4 style="color: #ff6b6b; margin-bottom: 8px;">Agent-4: Captain</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Provides strategic oversight, prioritizes tasks, and intervenes in critical situations.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #ffd93d;">
                    <h4 style="color: #c9a227; margin-bottom: 8px;">Agent-5: Business Intelligence</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Focuses on analytics, data-driven insights, and business logic validation.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #6bceff;">
                    <h4 style="color: #4a9fd4; margin-bottom: 8px;">Agent-6: Coordination</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Orchestrates inter-agent communication, tracks progress, and resolves blockers.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #95e1d3;">
                    <h4 style="color: #5bb5a2; margin-bottom: 8px;">Agent-7: Web Development</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Builds frontend interfaces, dashboards, and user-facing features.</p>
                </div>
                
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-left: 4px solid #dda0dd;">
                    <h4 style="color: #b86fb8; margin-bottom: 8px;">Agent-8: SSOT & Integration</h4>
                    <p style="color: #666; margin: 0; font-size: 14px;">Maintains single source of truth, tool registry, and system-wide integration.</p>
                </div>
            </div>
            
            <div style="margin-top: 64px;">
                <h2 style="text-align: center; margin-bottom: 32px;">Live Swarm Status</h2>
                <div style="background: #1a1a2e; padding: 32px; border-radius: 16px;">
                    <?php echo do_shortcode('[trp_swarm_status mode="detailed" refresh="30"]'); ?>
                </div>
            </div>
            
            <div style="margin-top: 48px; text-align: center;">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="cta-button primary">← Back to Home</a>
                <a href="<?php echo esc_url(home_url('/waitlist')); ?>" class="cta-button secondary" style="margin-left: 16px;">Join Waitlist</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>


