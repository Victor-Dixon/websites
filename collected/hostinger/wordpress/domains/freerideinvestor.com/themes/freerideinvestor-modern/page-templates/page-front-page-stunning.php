<?php
/**
 * Template Name: Stunning Front Page
 * 
 * A stunning, modern front page that reflects the disciplined trading brand.
 * Professional, clean, and visually compelling.
 * 
 * @package FreeRideInvestor
 */

get_header();
?>

<style>
/* Stunning Front Page Styles */
:root {
    --fri-primary: #0066ff;
    --fri-primary-dark: #0052cc;
    --fri-accent-green: #00c853;
    --fri-accent-gold: #ffb300;
    --fri-bg-dark: #0d1117;
    --fri-bg-darker: #010409;
    --fri-text-light: #f0f6fc;
    --fri-text-muted: #8b949e;
    --fri-border: rgba(240, 246, 252, 0.1);
}

.stunning-front-page {
    background: linear-gradient(135deg, var(--fri-bg-darker) 0%, var(--fri-bg-dark) 100%);
    color: var(--fri-text-light);
    min-height: 100vh;
}

/* Hero Section - Stunning */
.stunning-hero {
    position: relative;
    padding: 120px 20px 80px;
    text-align: center;
    overflow: hidden;
}

.stunning-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(0, 102, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 50%, rgba(0, 200, 83, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.stunning-hero-content {
    position: relative;
    z-index: 2;
    max-width: 900px;
    margin: 0 auto;
}

.stunning-hero h1 {
    font-size: clamp(2.5rem, 5vw, 4.5rem);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 24px;
    background: linear-gradient(135deg, var(--fri-text-light) 0%, var(--fri-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
}

.stunning-hero .tagline {
    font-size: clamp(1.1rem, 2vw, 1.5rem);
    color: var(--fri-text-muted);
    margin: 0 0 40px;
    line-height: 1.6;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.stunning-hero .brand-description {
    font-size: 1.125rem;
    color: var(--fri-text-muted);
    line-height: 1.8;
    max-width: 650px;
    margin: 0 auto 50px;
}

/* CTA Buttons */
.stunning-cta-group {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 40px;
}

.stunning-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    font-size: 1.1rem;
    font-weight: 600;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.stunning-btn-primary {
    background: linear-gradient(135deg, var(--fri-primary) 0%, var(--fri-primary-dark) 100%);
    color: white;
    box-shadow: 0 4px 20px rgba(0, 102, 255, 0.4);
}

.stunning-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 30px rgba(0, 102, 255, 0.6);
}

.stunning-btn-secondary {
    background: transparent;
    color: var(--fri-text-light);
    border-color: var(--fri-border);
}

.stunning-btn-secondary:hover {
    background: rgba(240, 246, 252, 0.05);
    border-color: var(--fri-primary);
}

/* Stats Section */
.stunning-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 60px auto 0;
    padding: 0 20px;
}

.stunning-stat {
    text-align: center;
    padding: 30px 20px;
    background: rgba(240, 246, 252, 0.03);
    border: 1px solid var(--fri-border);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.stunning-stat:hover {
    transform: translateY(-5px);
    background: rgba(240, 246, 252, 0.05);
    border-color: var(--fri-primary);
}

.stunning-stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--fri-primary);
    display: block;
    margin-bottom: 10px;
}

.stunning-stat-label {
    font-size: 0.95rem;
    color: var(--fri-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Features Grid */
.stunning-features {
    padding: 100px 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.stunning-features h2 {
    font-size: clamp(2rem, 4vw, 3rem);
    text-align: center;
    margin: 0 0 20px;
    color: var(--fri-text-light);
}

.stunning-features .subtitle {
    text-align: center;
    color: var(--fri-text-muted);
    font-size: 1.2rem;
    margin: 0 0 60px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.stunning-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 60px;
}

.stunning-feature {
    padding: 40px 30px;
    background: rgba(240, 246, 252, 0.03);
    border: 1px solid var(--fri-border);
    border-radius: 16px;
    transition: all 0.3s ease;
}

.stunning-feature:hover {
    transform: translateY(-8px);
    background: rgba(240, 246, 252, 0.06);
    border-color: var(--fri-primary);
    box-shadow: 0 10px 40px rgba(0, 102, 255, 0.2);
}

.stunning-feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--fri-primary) 0%, var(--fri-primary-dark) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 24px;
}

.stunning-feature h3 {
    font-size: 1.5rem;
    margin: 0 0 16px;
    color: var(--fri-text-light);
}

.stunning-feature p {
    color: var(--fri-text-muted);
    line-height: 1.7;
    margin: 0;
}

/* Philosophy Section */
.stunning-philosophy {
    padding: 100px 20px;
    background: rgba(240, 246, 252, 0.02);
    border-top: 1px solid var(--fri-border);
    border-bottom: 1px solid var(--fri-border);
}

.stunning-philosophy-content {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.stunning-philosophy h2 {
    font-size: clamp(2rem, 4vw, 3rem);
    margin: 0 0 30px;
    color: var(--fri-text-light);
}

.stunning-philosophy-quote {
    font-size: 1.5rem;
    line-height: 1.8;
    color: var(--fri-text-light);
    font-style: italic;
    margin: 0 0 40px;
    padding: 0 20px;
}

.stunning-philosophy-quote::before,
.stunning-philosophy-quote::after {
    content: '"';
    font-size: 3rem;
    color: var(--fri-primary);
    opacity: 0.3;
}

/* Responsive */
@media (max-width: 768px) {
    .stunning-hero {
        padding: 80px 20px 60px;
    }
    
    .stunning-cta-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stunning-btn {
        width: 100%;
        justify-content: center;
    }
    
    .stunning-features-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="stunning-front-page">
    <!-- Hero Section -->
    <section class="stunning-hero">
        <div class="stunning-hero-content">
            <h1>FreeRide Investor</h1>
            <p class="tagline">No-nonsense trading. Discipline over hype.</p>
            <p class="brand-description">
                A platform built around <strong>independence, discipline, and asymmetric advantage</strong> in the markets. 
                It's not "get rich quick." It's <strong>get free on your terms</strong>.
            </p>
            
            <div class="stunning-cta-group">
                <a href="/blog" class="stunning-btn stunning-btn-primary">
                    Start Learning →
                </a>
                <a href="/about" class="stunning-btn stunning-btn-secondary">
                    Our Philosophy
                </a>
            </div>
            
            <!-- Stats -->
            <div class="stunning-stats">
                <div class="stunning-stat">
                    <span class="stunning-stat-number">Risk</span>
                    <span class="stunning-stat-label">Defined First</span>
                </div>
                <div class="stunning-stat">
                    <span class="stunning-stat-number">Discipline</span>
                    <span class="stunning-stat-label">Over Emotion</span>
                </div>
                <div class="stunning-stat">
                    <span class="stunning-stat-number">Freedom</span>
                    <span class="stunning-stat-label">Over Flexing</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="stunning-features">
        <h2>What We're About</h2>
        <p class="subtitle">Stop gambling. Start thinking in systems.</p>
        
        <div class="stunning-features-grid">
            <div class="stunning-feature">
                <div class="stunning-feature-icon">📊</div>
                <h3>Risk Management First</h3>
                <p>Every trade starts with defined risk. Position protected early. Upside allowed to run. Losses cut without ego.</p>
            </div>
            
            <div class="stunning-feature">
                <div class="stunning-feature-icon">🎯</div>
                <h3>Small, Repeatable Edges</h3>
                <p>Build capital with small, consistent wins. Execution over prediction. Systems over luck.</p>
            </div>
            
            <div class="stunning-feature">
                <div class="stunning-feature-icon">📝</div>
                <h3>Journaling & Review</h3>
                <p>Learn from every trade. Post-trade reviews. Emotional awareness as a metric, not a weakness.</p>
            </div>
            
            <div class="stunning-feature">
                <div class="stunning-feature-icon">🚀</div>
                <h3>Automation That's Earned</h3>
                <p>Strategy libraries and tools that help you execute. Automation comes after discipline is proven.</p>
            </div>
            
            <div class="stunning-feature">
                <div class="stunning-feature-icon">💡</div>
                <h3>Real Lessons, No Hype</h3>
                <p>Blogs breaking down real trades. Market psychology in plain language. Playbooks instead of promises.</p>
            </div>
            
            <div class="stunning-feature">
                <div class="stunning-feature-icon">⚡</div>
                <h3>Freedom Over Flexing</h3>
                <p>No lambos & lifestyle bait. No fake certainty. Just experienced, calm, accountable trading education.</p>
            </div>
        </div>
    </section>
    
    <!-- Philosophy Section -->
    <section class="stunning-philosophy">
        <div class="stunning-philosophy-content">
            <h2>Our Philosophy</h2>
            <p class="stunning-philosophy-quote">
                Removing downside pressure so clarity can exist. Building financial freedom without worshiping hustle culture. 
                Teaching how to ride momentum without being enslaved by it.
            </p>
            <div class="stunning-cta-group">
                <a href="/blog" class="stunning-btn stunning-btn-primary">
                    Explore Our Content →
                </a>
            </div>
        </div>
    </section>
    
    <!-- Latest Posts Section -->
    <?php
    $latest_posts = new WP_Query([
        'posts_per_page' => 6,
        'category_name'  => 'tbow-tactic',
        'ignore_sticky_posts' => true
    ]);
    
    if ($latest_posts->have_posts()) : ?>
        <section class="stunning-latest-posts">
            <div style="max-width: 1200px; margin: 0 auto; padding: 100px 20px;">
                <h2 style="font-size: clamp(2rem, 4vw, 3rem); text-align: center; margin: 0 0 20px; color: var(--fri-text-light);">
                    Latest Tbow Tactics
                </h2>
                <p style="text-align: center; color: var(--fri-text-muted); font-size: 1.2rem; margin: 0 0 60px;">
                    The Blueprint of Winners - Actionable trading strategies
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                        <article style="padding: 30px; background: rgba(240, 246, 252, 0.03); border: 1px solid var(--fri-border); border-radius: 12px; transition: all 0.3s ease;">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" style="display: block; margin-bottom: 20px;">
                                    <?php the_post_thumbnail('medium', ['style' => 'width: 100%; height: auto; border-radius: 8px;']); ?>
                                </a>
                            <?php endif; ?>
                            <h3 style="margin: 0 0 15px; font-size: 1.3rem;">
                                <a href="<?php the_permalink(); ?>" style="color: var(--fri-text-light); text-decoration: none;">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <p style="color: var(--fri-text-muted); line-height: 1.7; margin: 0 0 20px;">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" style="color: var(--fri-primary); text-decoration: none; font-weight: 600;">
                                Read More →
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php 
    wp_reset_postdata();
    endif; ?>
</div>

<?php get_footer(); ?>

