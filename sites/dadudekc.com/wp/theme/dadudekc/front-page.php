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

<!-- Hero Section - SSOT: "Sound just like me" -->
<section class="hero">
    <div class="container">
        <h1 id="hero-heading"><?php esc_html_e('Sound just like me', 'dadudekc'); ?></h1>
        <p class="hero-subheadline"><?php esc_html_e('I push all my ideas and brainstorms in one space. I also sell automation.', 'dadudekc'); ?></p>
        <div class="hero-cta-row">
            <a class="cta-button primary" href="<?php echo esc_url(home_url('/contact')); ?>" role="button">
                <?php esc_html_e('Work With Me →', 'dadudekc'); ?>
            </a>
            <a class="cta-button secondary" href="<?php echo esc_url(home_url('/experiments')); ?>" role="button">
                <?php esc_html_e('See What I\'m Building', 'dadudekc'); ?>
            </a>
        </div>
    </div>
</section>

<!-- Primary Outputs Section - SSOT -->
<section class="primary-outputs-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('What You\'ll Find Here', 'dadudekc'); ?></h2>
        <div class="outputs-grid">
            <div class="output-card">
                <h3><?php esc_html_e('Builder Logs', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('Experiments turn into learnings, learnings turn into the next build.', 'dadudekc'); ?></p>
                <?php 
                $experiment_count = wp_count_posts('experiment')->publish;
                if ($experiment_count > 0) : 
                ?>
                    <p class="output-count"><?php printf(esc_html__('%d experiments documented', 'dadudekc'), $experiment_count); ?></p>
                <?php endif; ?>
            </div>
            <div class="output-card">
                <h3><?php esc_html_e('Project Demos', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('What shipped and proof it works.', 'dadudekc'); ?></p>
                <?php 
                $project_count = wp_count_posts('project')->publish;
                if ($project_count > 0) : 
                ?>
                    <p class="output-count"><?php printf(esc_html__('%d projects shipped', 'dadudekc'), $project_count); ?></p>
                <?php endif; ?>
            </div>
            <div class="output-card">
                <h3><?php esc_html_e('Automation Offers', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('What I sell and how it works.', 'dadudekc'); ?></p>
                <?php 
                $offer_count = wp_count_posts('offer_ladder')->publish;
                if ($offer_count > 0) : 
                ?>
                    <p class="output-count"><?php printf(esc_html__('%d offers available', 'dadudekc'), $offer_count); ?></p>
                <?php endif; ?>
            </div>
            <div class="output-card">
                <h3><?php esc_html_e('Resume & Portfolio', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('Compiled skills, projects, and proof of execution.', 'dadudekc'); ?></p>
                <?php 
                $resume_count = wp_count_posts('resume_item')->publish;
                if ($resume_count > 0) : 
                ?>
                    <p class="output-count"><?php printf(esc_html__('%d resume items', 'dadudekc'), $resume_count); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ICP Definition Section - BRAND-03 -->
<?php get_template_part('template-parts/components/icp-definition'); ?>

<!-- Offer Ladder Section - BRAND-02 -->
<?php get_template_part('template-parts/components/offer-ladder'); ?>

<!-- Content Sources Section - SSOT Definition of Done -->
<section class="content-sources-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('How This Site Works', 'dadudekc'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('Dreamvault and ChatGPT conversation history become blogging. Plans and learnings from experiments become content. Demos of projects become proof. Skills learned get added to resume.', 'dadudekc'); ?></p>
        <div class="content-sources-grid">
            <div class="source-card">
                <h3><?php esc_html_e('Conversations → Blogging', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('Dreamvault and ChatGPT conversations turn into posts.', 'dadudekc'); ?></p>
            </div>
            <div class="source-card">
                <h3><?php esc_html_e('Experiments → Learnings', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('Plans and learnings from experiments become content.', 'dadudekc'); ?></p>
            </div>
            <div class="source-card">
                <h3><?php esc_html_e('Projects → Demos', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('Demos of projects become content and proof.', 'dadudekc'); ?></p>
            </div>
            <div class="source-card">
                <h3><?php esc_html_e('Skills → Resume', 'dadudekc'); ?></h3>
                <p><?php esc_html_e('Skills learned get added to resume and portfolio.', 'dadudekc'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Receipts / Proof Section - WEB-02 Tier 2 Enhanced (Dynamic) -->
<section class="proof-section">
    <div class="container">
        <h2 class="section-title"><?php esc_html_e('Proof of Execution', 'dadudekc'); ?></h2>
        <p class="section-subtitle"><?php esc_html_e('Real systems. Real results. Verifiable work.', 'dadudekc'); ?></p>
        
        <!-- Proof Metrics (Dynamic) -->
        <?php $metrics = dadudekc_get_proof_metrics(); ?>
        <div class="proof-metrics-grid">
            <div class="proof-metric">
                <span class="proof-metric-value"><?php echo esc_html($metrics['ai_agents']); ?></span>
                <span class="proof-metric-label"><?php esc_html_e('AI Agents Deployed', 'dadudekc'); ?></span>
            </div>
            <div class="proof-metric">
                <span class="proof-metric-value"><?php echo esc_html($metrics['revenue_sites']); ?></span>
                <span class="proof-metric-label"><?php esc_html_e('Revenue Sites Built', 'dadudekc'); ?></span>
            </div>
            <div class="proof-metric">
                <span class="proof-metric-value"><?php echo esc_html($metrics['avg_delivery']); ?></span>
                <span class="proof-metric-label"><?php esc_html_e('Avg Sprint Delivery', 'dadudekc'); ?></span>
            </div>
            <div class="proof-metric">
                <span class="proof-metric-value"><?php echo esc_html($metrics['automation']); ?></span>
                <span class="proof-metric-label"><?php esc_html_e('Automation Running', 'dadudekc'); ?></span>
            </div>
        </div>
        
        <div class="proof-cards-grid">
            <!-- Shipped Systems Card (Dynamic) -->
            <div class="proof-card">
                <span class="status-badge status-shipped"><?php esc_html_e('Shipped', 'dadudekc'); ?></span>
                <h3 class="proof-card-title"><?php esc_html_e('Shipped Systems', 'dadudekc'); ?></h3>
                <ul class="proof-list">
                    <?php 
                    $shipped_systems = dadudekc_get_shipped_systems();
                    foreach ($shipped_systems as $system) : 
                    ?>
                        <li>✅ <?php echo esc_html($system); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Active Experiments Card (Dynamic) -->
            <div class="proof-card">
                <span class="status-badge status-live"><?php esc_html_e('Live', 'dadudekc'); ?></span>
                <h3 class="proof-card-title"><?php esc_html_e('Active Experiments', 'dadudekc'); ?></h3>
                <ul class="proof-list">
                    <?php 
                    $active_experiments = dadudekc_get_active_experiments();
                    foreach ($active_experiments as $experiment) : 
                    ?>
                        <li>🔄 <?php echo esc_html($experiment); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Build Transparency Card -->
            <div class="proof-card">
                <span class="status-badge status-experiment"><?php esc_html_e('Transparent', 'dadudekc'); ?></span>
                <h3 class="proof-card-title"><?php esc_html_e('Build Transparency', 'dadudekc'); ?></h3>
                <ul class="proof-list">
                    <li>📊 <?php esc_html_e('All work tracked in GitHub', 'dadudekc'); ?></li>
                    <li>📊 <?php esc_html_e('Devlogs posted to Discord', 'dadudekc'); ?></li>
                    <li>📊 <?php esc_html_e('Progress shared publicly', 'dadudekc'); ?></li>
                    <li>📊 <?php esc_html_e('No hidden processes', 'dadudekc'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Live Experiments Feed - Build-In-Public Phase 1 (Dynamic) -->
<?php get_template_part('template-parts/components/experiments-feed'); ?>

<!-- Project Demos Section - SSOT (Dynamic) -->
<?php get_template_part('template-parts/components/project-demos'); ?>

<style>
/* Live Experiments Section Styles */
.experiments-feed-section {
    padding: 4rem 0;
    background: rgba(255, 255, 255, 0.02);
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
    background: #2a2a2a;
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
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
    color: var(--text-color, #e0e0e0);
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

