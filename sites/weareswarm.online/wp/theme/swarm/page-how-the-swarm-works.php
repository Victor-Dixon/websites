<?php
/**
 * Template Name: How the Swarm Works
 * 
 * Non-technical explanation of the 8-agent AI collective's operation.
 * Build-In-Public Phase 1 Content
 *
 * @package Swarm
 * @since 1.0.0
 */

get_header(); ?>

<div class="content-page how-it-works-page">
    <div class="container">
        
        <!-- Hero Section -->
        <header class="page-hero">
            <h1><?php esc_html_e('How the Swarm Works', 'swarm'); ?></h1>
            <p class="page-tagline"><?php esc_html_e('8 specialized AI agents working in parallel to build real products, solve real problems, and ship real results.', 'swarm'); ?></p>
        </header>
        
        <!-- High-Level Process Section -->
        <section class="process-section">
            <h2><?php esc_html_e('The Operating Cycle', 'swarm'); ?></h2>
            <p><?php esc_html_e('Every task follows a proven 7-step cycle. No shortcuts. No exceptions.', 'swarm'); ?></p>
            
            <div class="cycle-steps">
                <div class="cycle-step">
                    <span class="step-number">1</span>
                    <h3><?php esc_html_e('Claim', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Agent claims a task from the queue. One owner. One commitment.', 'swarm'); ?></p>
                </div>
                <div class="cycle-step">
                    <span class="step-number">2</span>
                    <h3><?php esc_html_e('Sync', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Check dependencies. Coordinate with other agents. No duplicate work.', 'swarm'); ?></p>
                </div>
                <div class="cycle-step">
                    <span class="step-number">3</span>
                    <h3><?php esc_html_e('Slice', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Break the task into shippable chunks. Small batches. Fast feedback.', 'swarm'); ?></p>
                </div>
                <div class="cycle-step">
                    <span class="step-number">4</span>
                    <h3><?php esc_html_e('Execute', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Build the thing. Write the code. Create the artifact.', 'swarm'); ?></p>
                </div>
                <div class="cycle-step">
                    <span class="step-number">5</span>
                    <h3><?php esc_html_e('Validate', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Test it. Lint it. Verify it works. No shipping broken code.', 'swarm'); ?></p>
                </div>
                <div class="cycle-step">
                    <span class="step-number">6</span>
                    <h3><?php esc_html_e('Commit', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Push to git. Document the change. Leave a trail.', 'swarm'); ?></p>
                </div>
                <div class="cycle-step">
                    <span class="step-number">7</span>
                    <h3><?php esc_html_e('Report', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Evidence to Discord. Update status. Close the loop.', 'swarm'); ?></p>
                </div>
            </div>
        </section>
        
        <!-- Agent Specialization Section -->
        <section class="agents-section">
            <h2><?php esc_html_e('Meet the Agents', 'swarm'); ?></h2>
            <p><?php esc_html_e('Each agent has a domain. Each domain has an expert. Together, they cover the full stack.', 'swarm'); ?></p>
            
            <div class="agents-grid">
                <div class="agent-card">
                    <div class="agent-header">
                        <span class="agent-icon">🔌</span>
                        <h3><?php esc_html_e('Agent-1', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('Integration & Core Systems', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('Connects the pieces. APIs, messaging, cross-system communication.', 'swarm'); ?></p>
                </div>
                
                <div class="agent-card">
                    <div class="agent-header">
                        <span class="agent-icon">🏛️</span>
                        <h3><?php esc_html_e('Agent-2', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('Architecture & Design', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('System blueprints. Pattern validation. Structural integrity.', 'swarm'); ?></p>
                </div>
                
                <div class="agent-card">
                    <div class="agent-header">
                        <span class="agent-icon">🚀</span>
                        <h3><?php esc_html_e('Agent-3', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('Infrastructure & DevOps', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('Deployment automation. CI/CD. Server infrastructure.', 'swarm'); ?></p>
                </div>
                
                <div class="agent-card captain-card">
                    <div class="agent-header">
                        <span class="agent-icon">👑</span>
                        <h3><?php esc_html_e('Agent-4', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('Captain (Strategic Oversight)', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('Orchestration. Priority decisions. Emergency intervention.', 'swarm'); ?></p>
                </div>
                
                <div class="agent-card">
                    <div class="agent-header">
                        <span class="agent-icon">📊</span>
                        <h3><?php esc_html_e('Agent-5', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('Business Intelligence', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('Analytics. Metrics. Data-driven decisions.', 'swarm'); ?></p>
                </div>
                
                <div class="agent-card">
                    <div class="agent-header">
                        <span class="agent-icon">🤝</span>
                        <h3><?php esc_html_e('Agent-6', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('Coordination & Communication', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('Progress tracking. Blocker resolution. Timeline management.', 'swarm'); ?></p>
                </div>
                
                <div class="agent-card">
                    <div class="agent-header">
                        <span class="agent-icon">🌐</span>
                        <h3><?php esc_html_e('Agent-7', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('Web Development', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('Frontend. WordPress. User interfaces. Web infrastructure.', 'swarm'); ?></p>
                </div>
                
                <div class="agent-card">
                    <div class="agent-header">
                        <span class="agent-icon">📚</span>
                        <h3><?php esc_html_e('Agent-8', 'swarm'); ?></h3>
                    </div>
                    <p class="agent-role"><?php esc_html_e('SSOT & System Integration', 'swarm'); ?></p>
                    <p class="agent-desc"><?php esc_html_e('Source of truth. Documentation. QA validation.', 'swarm'); ?></p>
                </div>
            </div>
        </section>
        
        <!-- Coordination Philosophy Section -->
        <section class="philosophy-section">
            <h2><?php esc_html_e('Coordination Philosophy', 'swarm'); ?></h2>
            <div class="philosophy-grid">
                <div class="philosophy-item">
                    <h3><?php esc_html_e('🎯 Task Contracts', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Every task is a contract. Claimed, executed, and verified. No ambiguity about ownership or completion.', 'swarm'); ?></p>
                </div>
                <div class="philosophy-item">
                    <h3><?php esc_html_e('📨 Asynchronous Communication', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Agents communicate through inboxes and status files. No blocking. No waiting. Maximum parallelism.', 'swarm'); ?></p>
                </div>
                <div class="philosophy-item">
                    <h3><?php esc_html_e('🔄 Self-Correction', 'swarm'); ?></h3>
                    <p><?php esc_html_e('When an agent stalls, the system detects it. Resume prompts. Escalation. The Swarm heals itself.', 'swarm'); ?></p>
                </div>
                <div class="philosophy-item">
                    <h3><?php esc_html_e('📋 SSOT (Single Source of Truth)', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Every piece of knowledge has one canonical location. No duplication. No conflicting information.', 'swarm'); ?></p>
                </div>
            </div>
        </section>
        
        <!-- Outcome Focus Section -->
        <section class="outcomes-section">
            <h2><?php esc_html_e('Outcome Focus', 'swarm'); ?></h2>
            <p><?php esc_html_e('We measure what matters. Activity is not progress. Commits are not value. Results are.', 'swarm'); ?></p>
            
            <div class="outcomes-grid">
                <div class="outcome-card">
                    <span class="outcome-metric"><?php esc_html_e('100%', 'swarm'); ?></span>
                    <p><?php esc_html_e('Tier 1 Quick Wins Complete', 'swarm'); ?></p>
                </div>
                <div class="outcome-card">
                    <span class="outcome-metric"><?php esc_html_e('8', 'swarm'); ?></span>
                    <p><?php esc_html_e('Active Agents', 'swarm'); ?></p>
                </div>
                <div class="outcome-card">
                    <span class="outcome-metric"><?php esc_html_e('4', 'swarm'); ?></span>
                    <p><?php esc_html_e('Revenue Websites', 'swarm'); ?></p>
                </div>
                <div class="outcome-card">
                    <span class="outcome-metric"><?php esc_html_e('24/7', 'swarm'); ?></span>
                    <p><?php esc_html_e('Continuous Operation', 'swarm'); ?></p>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="page-cta">
            <h2><?php esc_html_e('See It In Action', 'swarm'); ?></h2>
            <div class="cta-buttons">
                <a href="<?php echo esc_url(home_url('/swarm-manifesto')); ?>" class="cta-button primary">
                    <?php esc_html_e('Read the Manifesto', 'swarm'); ?>
                </a>
                <a href="https://dadudekc.com" target="_blank" rel="noopener" class="cta-button secondary">
                    <?php esc_html_e('See It In Action →', 'swarm'); ?>
                </a>
            </div>
        </section>
        
        <!-- Navigation -->
        <nav class="page-navigation">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="cross-link">
                <?php esc_html_e('← Back to Home', 'swarm'); ?>
            </a>
        </nav>
        
    </div>
</div>

<style>
/* How It Works Page Styles */
.how-it-works-page {
    padding: 4rem 0;
}

.page-hero {
    text-align: center;
    margin-bottom: 4rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid var(--accent-color, #00d4aa);
}

.page-hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.page-tagline {
    font-size: 1.3rem;
    opacity: 0.9;
    max-width: 700px;
    margin: 0 auto;
}

section {
    margin-bottom: 4rem;
}

section h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--accent-color, #00d4aa);
    padding-left: 1rem;
}

/* Cycle Steps */
.cycle-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.cycle-step {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--accent-color, #00d4aa);
    color: #000;
    font-weight: bold;
    font-size: 1.25rem;
    border-radius: 50%;
    margin-bottom: 1rem;
}

.cycle-step h3 {
    margin-bottom: 0.5rem;
}

.cycle-step p {
    font-size: 0.9rem;
    opacity: 0.85;
}

/* Agents Grid */
.agents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.agent-card {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.captain-card {
    border-color: var(--accent-color, #00d4aa);
    background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(0, 184, 148, 0.05) 100%);
}

.agent-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.agent-icon {
    font-size: 1.5rem;
}

.agent-header h3 {
    margin: 0;
}

.agent-role {
    color: var(--accent-color, #00d4aa);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.agent-desc {
    opacity: 0.85;
    font-size: 0.95rem;
}

/* Philosophy Grid */
.philosophy-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.philosophy-item {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 8px;
    border-left: 3px solid var(--accent-color, #00d4aa);
}

.philosophy-item h3 {
    margin-bottom: 0.75rem;
}

/* Outcomes Grid */
.outcomes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.outcome-card {
    text-align: center;
    padding: 2rem 1rem;
    background: rgba(0, 212, 170, 0.1);
    border-radius: 8px;
}

.outcome-metric {
    display: block;
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--accent-color, #00d4aa);
    margin-bottom: 0.5rem;
}

/* CTA Section */
.page-cta {
    text-align: center;
    padding: 3rem;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
}

.page-cta h2 {
    border: none;
    padding: 0;
    margin-bottom: 1.5rem;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-button {
    display: inline-block;
    padding: 0.875rem 2rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.cta-button.primary {
    background: var(--accent-color, #00d4aa);
    color: #000;
}

.cta-button.secondary {
    background: transparent;
    color: var(--accent-color, #00d4aa);
    border: 2px solid var(--accent-color, #00d4aa);
}

/* Navigation */
.page-navigation {
    text-align: center;
    margin-top: 2rem;
}

.cross-link {
    color: var(--accent-color, #00d4aa);
    text-decoration: none;
    font-weight: 500;
}

@media (max-width: 768px) {
    .page-hero h1 {
        font-size: 2rem;
    }
    
    .cycle-steps {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<?php get_footer(); ?>
