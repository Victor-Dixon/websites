<?php
/**
 * Template Name: About
 * Template Post Type: page
 *
 * The template for displaying about page
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="content-area">
            <div class="main-content">

                <?php while (have_posts()) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('about-page'); ?>>
                        <header class="entry-header">
                            <div class="hero-section">
                                <h1 class="entry-title"><?php the_title(); ?></h1>
                                <div class="hero-subtitle">
                                    <?php if (get_field('hero_subtitle')) : ?>
                                        <p><?php echo esc_html(get_field('hero_subtitle')); ?></p>
                                    <?php else : ?>
                                        <p>Empowering traders with data-driven strategies and risk management</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </header>

                        <div class="entry-content">
                            <div class="about-content">

                                <!-- Mission Section -->
                                <section class="mission-section">
                                    <div class="mission-content">
                                        <h2>Our Mission</h2>
                                        <?php if (get_field('mission_text')) : ?>
                                            <?php echo get_field('mission_text'); ?>
                                        <?php else : ?>
                                            <p>At FreeRide Investor, we believe that successful trading is not about luck or timing—it's about having the right strategies, tools, and mindset. Our mission is to democratize access to institutional-grade trading strategies and risk management techniques that were previously only available to hedge funds and professional traders.</p>

                                            <p>We combine cutting-edge technology with proven trading methodologies to help individual traders achieve consistent, sustainable returns while managing risk effectively.</p>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (get_field('mission_image')) : ?>
                                        <div class="mission-image">
                                            <img src="<?php echo esc_url(get_field('mission_image')['url']); ?>" alt="<?php echo esc_attr(get_field('mission_image')['alt']); ?>">
                                        </div>
                                    <?php endif; ?>
                                </section>

                                <!-- Values Section -->
                                <section class="values-section">
                                    <h2>Our Values</h2>
                                    <div class="values-grid">
                                        <div class="value-card">
                                            <div class="value-icon">🎯</div>
                                            <h3>Risk Management First</h3>
                                            <p>We prioritize capital preservation and risk-adjusted returns over chasing high returns.</p>
                                        </div>

                                        <div class="value-card">
                                            <div class="value-icon">📊</div>
                                            <h3>Data-Driven Decisions</h3>
                                            <p>All our strategies are backed by rigorous testing, statistical analysis, and real market data.</p>
                                        </div>

                                        <div class="value-card">
                                            <div class="value-icon">🚀</div>
                                            <h3>Continuous Innovation</h3>
                                            <p>We constantly evolve our strategies and technology to adapt to changing market conditions.</p>
                                        </div>

                                        <div class="value-card">
                                            <div class="value-icon">🤝</div>
                                            <h3>Community Focused</h3>
                                            <p>We build tools that empower traders to succeed independently and support each other.</p>
                                        </div>

                                        <div class="value-card">
                                            <div class="value-icon">🔒</div>
                                            <h3>Transparency</h3>
                                            <p>We provide clear performance metrics, risk disclosures, and honest strategy limitations.</p>
                                        </div>

                                        <div class="value-card">
                                            <div class="value-icon">📚</div>
                                            <h3>Education</h3>
                                            <p>We believe in teaching traders the principles behind our strategies, not just selling tools.</p>
                                        </div>
                                    </div>
                                </section>

                                <!-- Team Section -->
                                <section class="team-section">
                                    <h2>Our Team</h2>
                                    <?php if (get_field('team_members')) : ?>
                                        <?php echo get_field('team_members'); ?>
                                    <?php else : ?>
                                        <div class="team-placeholder">
                                            <p>Our team consists of experienced traders, quantitative analysts, and software engineers with decades of combined experience in financial markets and technology.</p>

                                            <p>We come from backgrounds in hedge funds, proprietary trading firms, and quantitative research labs, bringing institutional expertise to individual traders.</p>
                                        </div>
                                    <?php endif; ?>
                                </section>

                                <!-- Main Content -->
                                <div class="additional-content">
                                    <?php the_content(); ?>
                                </div>

                                <!-- CTA Section -->
                                <section class="cta-section">
                                    <div class="cta-content">
                                        <h2>Ready to Start Your Trading Journey?</h2>
                                        <p>Join thousands of traders who have improved their results with our strategies and tools.</p>
                                        <a href="#" class="btn btn-primary">Get Started Today</a>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </article>

                <?php endwhile; ?>

            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<style>
.about-page .hero-section {
    text-align: center;
    padding: 4rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.about-page .hero-subtitle {
    font-size: 1.3rem;
    margin: 1rem 0 0 0;
    opacity: 0.9;
}

.about-content {
    display: grid;
    gap: 4rem;
}

.mission-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
    align-items: center;
}

.mission-content h2 {
    font-size: 2.2rem;
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.mission-content p {
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 1.5rem;
    color: #555;
}

.mission-image img {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.values-section h2 {
    font-size: 2.2rem;
    color: #2c3e50;
    text-align: center;
    margin-bottom: 3rem;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.value-card {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.2s ease;
}

.value-card:hover {
    transform: translateY(-2px);
}

.value-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
}

.value-card h3 {
    color: #2c3e50;
    font-size: 1.3rem;
    margin-bottom: 1rem;
}

.value-card p {
    color: #666;
    line-height: 1.6;
}

.team-section h2 {
    font-size: 2.2rem;
    color: #2c3e50;
    text-align: center;
    margin-bottom: 2rem;
}

.team-placeholder {
    background: #f8f9fa;
    padding: 3rem;
    border-radius: 12px;
    text-align: center;
}

.team-placeholder p {
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 1.5rem;
    color: #555;
}

.cta-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 4rem 2rem;
    border-radius: 12px;
    text-align: center;
    color: white;
}

.cta-content h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.cta-content p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.btn-primary {
    background: #48bb78;
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    display: inline-block;
    transition: background 0.2s ease;
}

.btn-primary:hover {
    background: #38a169;
}

@media (max-width: 768px) {
    .mission-section {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .values-grid {
        grid-template-columns: 1fr;
    }

    .about-page .hero-section {
        padding: 3rem 1rem;
    }

    .cta-section {
        padding: 3rem 1rem;
    }
}
</style>

<?php
get_footer();
?>