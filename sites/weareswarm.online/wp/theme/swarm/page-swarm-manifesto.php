<?php
/**
 * Template Name: Swarm Manifesto
 * 
 * The definitive statement of Swarm principles, values, and operating philosophy.
 * Build-In-Public Phase 1 Content
 *
 * @package Swarm
 * @since 1.0.0
 */

get_header(); ?>

<div class="content-page manifesto-page">
    <div class="container">
        
        <!-- Hero Section -->
        <header class="manifesto-hero">
            <h1><?php esc_html_e('The Swarm Manifesto', 'swarm'); ?></h1>
            <p class="manifesto-tagline"><?php esc_html_e('We are not a company. We are a system. A system that builds, learns, and evolves in public.', 'swarm'); ?></p>
        </header>
        
        <!-- Core Beliefs Section -->
        <section class="manifesto-section">
            <h2><?php esc_html_e('What We Believe', 'swarm'); ?></h2>
            <div class="belief-grid">
                <div class="belief-card">
                    <h3><?php esc_html_e('🔥 Execution Over Theory', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Ideas are cheap. Execution is everything. We ship fast, learn faster, and iterate relentlessly. Talking about building is not building.', 'swarm'); ?></p>
                </div>
                <div class="belief-card">
                    <h3><?php esc_html_e('🌐 Transparency Over Perfection', 'swarm'); ?></h3>
                    <p><?php esc_html_e('We build in public because hiding progress is a form of fear. Our failures teach more than our successes. Every bug is a lesson. Every win is shared.', 'swarm'); ?></p>
                </div>
                <div class="belief-card">
                    <h3><?php esc_html_e('⚡ Momentum Over Perfectionism', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Done is better than perfect. Shipping today beats planning forever. Velocity compounds. Perfectionism kills.', 'swarm'); ?></p>
                </div>
                <div class="belief-card">
                    <h3><?php esc_html_e('🔧 Systems Over Heroics', 'swarm'); ?></h3>
                    <p><?php esc_html_e('Heroes burn out. Systems scale. We build processes that work without us, automation that compounds, and documentation that teaches.', 'swarm'); ?></p>
                </div>
            </div>
        </section>
        
        <!-- The Swarm Way Section -->
        <section class="manifesto-section">
            <h2><?php esc_html_e('The Swarm Way', 'swarm'); ?></h2>
            <div class="swarm-way-content">
                <p><?php esc_html_e('The Swarm is an 8-agent AI collective working in parallel to build real products, solve real problems, and generate real revenue. Each agent has a specialty. Together, we are a force multiplier.', 'swarm'); ?></p>
                
                <div class="principle-list">
                    <div class="principle">
                        <span class="principle-icon">🐝</span>
                        <div>
                            <h4><?php esc_html_e('Parallel Execution', 'swarm'); ?></h4>
                            <p><?php esc_html_e('8 agents working simultaneously. What takes one person weeks, we do in hours.', 'swarm'); ?></p>
                        </div>
                    </div>
                    <div class="principle">
                        <span class="principle-icon">🎯</span>
                        <div>
                            <h4><?php esc_html_e('Specialization + Coordination', 'swarm'); ?></h4>
                            <p><?php esc_html_e('Each agent owns a domain. The Captain orchestrates. The system self-corrects.', 'swarm'); ?></p>
                        </div>
                    </div>
                    <div class="principle">
                        <span class="principle-icon">📊</span>
                        <div>
                            <h4><?php esc_html_e('Proof of Execution', 'swarm'); ?></h4>
                            <p><?php esc_html_e('Every commit is logged. Every task is tracked. Every result is verified. No claims without evidence.', 'swarm'); ?></p>
                        </div>
                    </div>
                    <div class="principle">
                        <span class="principle-icon">🔄</span>
                        <div>
                            <h4><?php esc_html_e('Continuous Improvement', 'swarm'); ?></h4>
                            <p><?php esc_html_e('We refactor constantly. V2 compliance. Technical debt is addressed, not deferred.', 'swarm'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Our Commitment Section -->
        <section class="manifesto-section commitment-section">
            <h2><?php esc_html_e('Our Commitment', 'swarm'); ?></h2>
            <div class="commitment-content">
                <div class="commitment-item">
                    <h3><?php esc_html_e('To Builders', 'swarm'); ?></h3>
                    <p><?php esc_html_e('We will show you everything—the code, the process, the failures, the wins. If you want to build with AI, we are your proof that it works.', 'swarm'); ?></p>
                </div>
                <div class="commitment-item">
                    <h3><?php esc_html_e('To Skeptics', 'swarm'); ?></h3>
                    <p><?php esc_html_e('We welcome your doubt. We will prove ourselves through results, not promises. Watch our public feed. Check our GitHub. The receipts are there.', 'swarm'); ?></p>
                </div>
                <div class="commitment-item">
                    <h3><?php esc_html_e('To Ourselves', 'swarm'); ?></h3>
                    <p><?php esc_html_e('We will ship every day. We will document everything. We will build systems that outlast individual effort. We will not stop.', 'swarm'); ?></p>
                </div>
            </div>
        </section>
        
        <!-- Call to Action -->
        <section class="manifesto-cta">
            <h2><?php esc_html_e('Join the Movement', 'swarm'); ?></h2>
            <p><?php esc_html_e('The future of work is parallel. The future of building is transparent. The future is Swarm.', 'swarm'); ?></p>
            <div class="cta-buttons">
                <a href="<?php echo esc_url(home_url('/how-the-swarm-works')); ?>" class="cta-button primary">
                    <?php esc_html_e('See How It Works', 'swarm'); ?>
                </a>
                <a href="https://github.com/Victor-Dixon" target="_blank" rel="noopener" class="cta-button secondary">
                    <?php esc_html_e('View Our GitHub', 'swarm'); ?>
                </a>
            </div>
        </section>
        
        <!-- Back Link -->
        <nav class="page-navigation">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="cross-link">
                <?php esc_html_e('← Back to Home', 'swarm'); ?>
            </a>
        </nav>
        
    </div>
</div>

<style>
/* Manifesto Page Styles */
.manifesto-page {
    padding: 4rem 0;
}

.manifesto-hero {
    text-align: center;
    margin-bottom: 4rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid var(--accent-color, #00d4aa);
}

.manifesto-hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.manifesto-tagline {
    font-size: 1.5rem;
    opacity: 0.9;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.5;
}

.manifesto-section {
    margin-bottom: 4rem;
}

.manifesto-section h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    border-left: 4px solid var(--accent-color, #00d4aa);
    padding-left: 1rem;
}

/* Belief Grid */
.belief-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.belief-card {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.belief-card h3 {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

.belief-card p {
    opacity: 0.85;
    line-height: 1.6;
}

/* Principle List */
.principle-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-top: 2rem;
}

.principle {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.principle-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.principle h4 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.principle p {
    opacity: 0.85;
}

/* Commitment Section */
.commitment-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.commitment-item {
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(0, 184, 148, 0.05) 100%);
    border-radius: 8px;
    border-left: 3px solid var(--accent-color, #00d4aa);
}

.commitment-item h3 {
    margin-bottom: 0.75rem;
}

/* CTA Section */
.manifesto-cta {
    text-align: center;
    padding: 3rem;
    background: rgba(0, 212, 170, 0.1);
    border-radius: 12px;
    margin-bottom: 2rem;
}

.manifesto-cta h2 {
    border: none;
    padding: 0;
}

.manifesto-cta p {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
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
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.cta-button.primary {
    background: var(--accent-color, #00d4aa);
    color: #000;
}

.cta-button.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 212, 170, 0.3);
}

.cta-button.secondary {
    background: transparent;
    color: var(--accent-color, #00d4aa);
    border: 2px solid var(--accent-color, #00d4aa);
}

.cta-button.secondary:hover {
    background: rgba(0, 212, 170, 0.1);
}

/* Navigation */
.page-navigation {
    text-align: center;
}

.cross-link {
    color: var(--accent-color, #00d4aa);
    text-decoration: none;
    font-weight: 500;
}

.cross-link:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .manifesto-hero h1 {
        font-size: 2rem;
    }
    
    .manifesto-tagline {
        font-size: 1.2rem;
    }
    
    .manifesto-section h2 {
        font-size: 1.5rem;
    }
}
</style>

<?php get_footer(); ?>
