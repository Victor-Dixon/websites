<?php

/**
 * Front Page Template
 * 
 * Custom homepage template with optimized hero section (Tier 1 Quick Win WEB-01)
 * 
 * @package DaDudeKC
 * @since 1.0.0
 */

get_header(); ?>

<!-- Optimized Hero Section - Tier 1 Quick Win WEB-01 -->
<section class="hero">
    <div class="container">
        <h1 id="hero-heading"><?php esc_html_e('Get 10+ Hours Per Week Back From Automated Workflows', 'dadudekc'); ?></h1>
        <p class="hero-subheadline"><?php esc_html_e('Done-for-you automation sprints that eliminate workflow bottlenecks in 2 weeks—zero technical knowledge required.', 'dadudekc'); ?></p>
        <div class="hero-cta-row">
            <a class="cta-button primary" href="<?php echo esc_url(home_url('/audit')); ?>" role="button">
                <?php esc_html_e('Get Your Free Workflow Audit →', 'dadudekc'); ?>
            </a>
            <a class="cta-button secondary" href="<?php echo esc_url(home_url('/how-it-works')); ?>" role="button">
                <?php esc_html_e('See How It Works', 'dadudekc'); ?>
            </a>
        </div>
        <p class="hero-urgency"><?php esc_html_e('Limited spots available—start your automation sprint today', 'dadudekc'); ?></p>
    </div>
</section>

<!-- Primary CTA - Build-In-Public -->
<section class="primary-cta-section">
    <div class="container">
        <a class="cta-button primary large" href="<?php echo esc_url(home_url('/contact')); ?>" role="button">
            <?php esc_html_e('Start a Build Sprint', 'dadudekc'); ?>
        </a>
    </div>
</section>

<!-- ICP Definition Section - BRAND-03 -->
<?php get_template_part('template-parts/components/icp-definition'); ?>

<!-- Offer Ladder Section - BRAND-02 -->
<?php get_template_part('template-parts/components/offer-ladder'); ?>

<!-- What I Do Section - Build-In-Public Phase 0 -->
<section class="what-i-do-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('Three Ways to Work Together', 'dadudekc'); ?></h2>
        <div class="offer-cards-grid">
            <!-- Card 1: AI Build Sprints -->
            <div class="offer-card">
                <div class="offer-card-header">
                    <h3 class="offer-card-title"><?php esc_html_e('AI Build Sprints', 'dadudekc'); ?></h3>
                    <span class="status-badge status-live"><?php esc_html_e('Live', 'dadudekc'); ?></span>
                </div>
                <p class="offer-card-subtitle"><?php esc_html_e('72-hour outcome-based builds', 'dadudekc'); ?></p>
                <p class="offer-card-description"><?php esc_html_e('Fast, focused delivery. Clear timeline, clear outcome.', 'dadudekc'); ?></p>
                <a class="cta-button primary" href="<?php echo esc_url(home_url('/contact')); ?>" role="button">
                    <?php esc_html_e('Start a Build Sprint', 'dadudekc'); ?>
                </a>
            </div>
            
            <!-- Card 2: Automation / Ops Systems -->
            <div class="offer-card">
                <div class="offer-card-header">
                    <h3 class="offer-card-title"><?php esc_html_e('Automation & Ops Systems', 'dadudekc'); ?></h3>
                    <span class="status-badge status-live"><?php esc_html_e('Live', 'dadudekc'); ?></span>
                </div>
                <p class="offer-card-subtitle"><?php esc_html_e('Higher-ticket infrastructure installs', 'dadudekc'); ?></p>
                <p class="offer-card-description"><?php esc_html_e('Enterprise/established businesses. Infrastructure focus.', 'dadudekc'); ?></p>
                <a class="cta-button secondary" href="<?php echo esc_url(home_url('/contact')); ?>" role="button">
                    <?php esc_html_e('Discuss Automation', 'dadudekc'); ?>
                </a>
            </div>
            
            <!-- Card 3: Experimental Builds -->
            <div class="offer-card">
                <div class="offer-card-header">
                    <h3 class="offer-card-title"><?php esc_html_e('Experimental Builds', 'dadudekc'); ?></h3>
                    <span class="status-badge status-in-progress"><?php esc_html_e('In Progress', 'dadudekc'); ?></span>
                </div>
                <p class="offer-card-subtitle"><?php esc_html_e('Mods, tools, prototypes — built in public', 'dadudekc'); ?></p>
                <p class="offer-card-description"><?php esc_html_e('Innovation showcase. Transparency, community.', 'dadudekc'); ?></p>
                <a class="cta-button secondary" href="<?php echo esc_url(home_url('/experiments')); ?>" role="button">
                    <?php esc_html_e('Watch Experiments', 'dadudekc'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Receipts / Proof Section - Build-In-Public Phase 0 -->
<section class="proof-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('Proof of Execution', 'dadudekc'); ?></h2>
        <div class="proof-cards-grid">
            <!-- Usage Stats Card -->
            <div class="proof-card">
                <h3 class="proof-card-title"><?php esc_html_e('Usage Stats', 'dadudekc'); ?></h3>
                <p class="proof-card-placeholder"><?php esc_html_e('Systems deployed • Automation running • Experiments live', 'dadudekc'); ?></p>
                <span class="status-badge status-live"><?php esc_html_e('Live', 'dadudekc'); ?></span>
            </div>
            
            <!-- Shipped Systems Card -->
            <div class="proof-card">
                <h3 class="proof-card-title"><?php esc_html_e('Shipped Systems', 'dadudekc'); ?></h3>
                <p class="proof-card-placeholder"><?php esc_html_e('Completed builds • Live examples • Timelines', 'dadudekc'); ?></p>
                <span class="status-badge status-shipped"><?php esc_html_e('Shipped', 'dadudekc'); ?></span>
            </div>
            
            <!-- Experiments Log Card -->
            <div class="proof-card">
                <h3 class="proof-card-title"><?php esc_html_e('Experiments Log', 'dadudekc'); ?></h3>
                <p class="proof-card-placeholder"><?php esc_html_e('Active experiments • Completed experiments • Learnings', 'dadudekc'); ?></p>
                <span class="status-badge status-experiment"><?php esc_html_e('Experiment Complete', 'dadudekc'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Live Experiments Feed - Build-In-Public Phase 1 -->
<section class="experiments-feed-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('🧪 Live Experiments', 'dadudekc'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('Real projects. Real progress. Built transparently.', 'dadudekc'); ?></p>
        
        <div class="experiments-feed">
            <!-- Active: AI Agent Swarm -->
            <div class="experiment-feed-item featured">
                <div class="experiment-feed-header">
                    <h3 class="experiment-feed-title"><?php esc_html_e('AI Agent Swarm (8 Agents)', 'dadudekc'); ?></h3>
                    <span class="status-badge status-live"><?php esc_html_e('LIVE', 'dadudekc'); ?></span>
                </div>
                <p class="experiment-feed-description"><?php esc_html_e('8 specialized AI agents working in parallel: Integration, Architecture, Infrastructure, Captain, Business Intelligence, Coordination, Web Development, SSOT. Fully operational with automated task assignment, resume prompts, and Discord coordination.', 'dadudekc'); ?></p>
                <div class="experiment-stats">
                    <span class="stat">✅ 100% Tier 1 Complete</span>
                    <span class="stat">📊 4 Revenue Sites</span>
                    <span class="stat">🔄 24/7 Operation</span>
                </div>
                <a href="https://weareswarm.online" class="experiment-link" target="_blank" rel="noopener">
                    <?php esc_html_e('Watch the Swarm →', 'dadudekc'); ?>
                </a>
            </div>
            
            <!-- Active: Trading Robot Platform -->
            <div class="experiment-feed-item">
                <div class="experiment-feed-header">
                    <h3 class="experiment-feed-title"><?php esc_html_e('TradingRobotPlug Platform', 'dadudekc'); ?></h3>
                    <span class="status-badge status-in-progress"><?php esc_html_e('Building', 'dadudekc'); ?></span>
                </div>
                <p class="experiment-feed-description"><?php esc_html_e('Automated trading tools with paper trading validation. REST API complete (9 endpoints), dashboard UI built, real-time updates next.', 'dadudekc'); ?></p>
                <div class="experiment-stats">
                    <span class="stat">📈 Dashboard Complete</span>
                    <span class="stat">🔌 REST API Live</span>
                </div>
                <a href="https://tradingrobotplug.com" class="experiment-link" target="_blank" rel="noopener">
                    <?php esc_html_e('View Progress →', 'dadudekc'); ?>
                </a>
            </div>
            
            <!-- Completed: Website Optimization -->
            <div class="experiment-feed-item">
                <div class="experiment-feed-header">
                    <h3 class="experiment-feed-title"><?php esc_html_e('Revenue Site Optimization', 'dadudekc'); ?></h3>
                    <span class="status-badge status-shipped"><?php esc_html_e('Shipped', 'dadudekc'); ?></span>
                </div>
                <p class="experiment-feed-description"><?php esc_html_e('4-week sprint: 27 P0 fixes across 4 websites. Brand positioning, hero optimization, contact form friction reduction. Week 1 Tier 1 complete.', 'dadudekc'); ?></p>
                <div class="experiment-stats">
                    <span class="stat">🎯 11/11 Quick Wins</span>
                    <span class="stat">📝 Tier 2 In Progress</span>
                </div>
            </div>
        </div>
        
        <!-- Cross-link -->
        <div class="experiments-cta">
            <a href="https://weareswarm.online" class="cta-button secondary" target="_blank" rel="noopener">
                <?php esc_html_e('See All Experiments on weareswarm.online →', 'dadudekc'); ?>
            </a>
        </div>
    </div>
</section>

<style>
/* Live Experiments Section Styles */
.experiments-feed-section {
    padding: 4rem 0;
    background: rgba(0, 0, 0, 0.2);
}

.section-subtitle {
    text-align: center;
    opacity: 0.8;
    margin-bottom: 2rem;
}

.experiments-feed {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    max-width: 800px;
    margin: 0 auto;
}

.experiment-feed-item {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    border-left: 4px solid var(--accent-color, #00d4aa);
}

.experiment-feed-item.featured {
    border-left-width: 6px;
    background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(0, 184, 148, 0.05) 100%);
}

.experiment-feed-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.experiment-feed-title {
    margin: 0;
    font-size: 1.1rem;
}

.experiment-feed-description {
    opacity: 0.85;
    margin-bottom: 1rem;
    line-height: 1.6;
}

.experiment-stats {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.experiment-stats .stat {
    font-size: 0.85rem;
    padding: 0.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

.experiment-link {
    color: var(--accent-color, #00d4aa);
    text-decoration: none;
    font-weight: 500;
}

.experiment-link:hover {
    text-decoration: underline;
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.status-live {
    background: #00d4aa;
    color: #000;
}

.status-badge.status-in-progress {
    background: #ffaa00;
    color: #000;
}

.status-badge.status-shipped {
    background: #6c757d;
    color: #fff;
}

.experiments-cta {
    text-align: center;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .experiment-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<?php
get_footer();

