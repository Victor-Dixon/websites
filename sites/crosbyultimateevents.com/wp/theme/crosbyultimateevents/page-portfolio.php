<?php

/**
 * Portfolio Page Template
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">

        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page portfolio-page'); ?>>

                <header class="entry-header">
                    <h1 class="page-title">Our Portfolio</h1>
                    <p class="page-subtitle">A showcase of extraordinary events we've created</p>
                </header>

                <div class="portfolio-page-content">
                    <!-- Portfolio Introduction -->
                    <section class="portfolio-intro">
                        <p>From intimate dinner parties to grand celebrations, we've had the privilege of creating memorable experiences. Here's a glimpse into some of our recent work.</p>
                    </section>

                    <!-- Portfolio Grid -->
                    <section class="portfolio-grid-section">
                        <div class="portfolio-grid">
                            
                            <!-- Case Study 1 -->
                            <div class="portfolio-item case-study" style="grid-column: span 3; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <div class="portfolio-image" style="min-height: 400px; background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%); display: flex; align-items: center; justify-content: center;">
                                    <div style="text-align: center; color: white;">
                                        <span style="font-size: 4rem; display: block; margin-bottom: 1rem;">🍽️</span>
                                        <span style="text-transform: uppercase; letter-spacing: 2px;">Private Dining</span>
                                    </div>
                                </div>
                                <div class="portfolio-content" style="padding: 2rem; display: flex; flex-direction: column; justify-content: center;">
                                    <span class="portfolio-tag" style="background: #f0f0f0; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; text-transform: uppercase; align-self: flex-start; margin-bottom: 1rem;">Private Chef</span>
                                    <h3>The Thompson Estate Dinner</h3>
                                    <div class="case-study-details" style="margin: 1.5rem 0; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <strong>Guest Count</strong>
                                            <p>12 VIP Guests</p>
                                        </div>
                                        <div>
                                            <strong>Theme</strong>
                                            <p>Seasonal Harvest</p>
                                        </div>
                                    </div>
                                    <div class="menu-highlight" style="background: #f9f9f9; padding: 1rem; border-left: 3px solid var(--primary-color, #c0392b); margin-bottom: 1.5rem;">
                                        <strong>Menu Highlights</strong>
                                        <p style="font-size: 0.9rem; margin-top: 0.5rem;">Pan-seared scallops with truffle foam, Wagyu beef tenderloin with root vegetable puree, and Deconstructed lemon tart.</p>
                                    </div>
                                    <div class="testimonial-snippet" style="font-style: italic; color: #666;">
                                        "A flawless evening of culinary exploration. The chef's attention to detail was unmatched."
                                    </div>
                                </div>
                            </div>

                            <!-- Case Study 2 -->
                            <div class="portfolio-item case-study" style="grid-column: span 3; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <div class="portfolio-content" style="padding: 2rem; display: flex; flex-direction: column; justify-content: center; order: 1;"> <!-- Content first on desktop -->
                                    <span class="portfolio-tag" style="background: #f0f0f0; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; text-transform: uppercase; align-self: flex-start; margin-bottom: 1rem;">Event Planning</span>
                                    <h3>TechCorp Annual Gala</h3>
                                    <div class="case-study-details" style="margin: 1.5rem 0; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <strong>Guest Count</strong>
                                            <p>250 Guests</p>
                                        </div>
                                        <div>
                                            <strong>Timeline</strong>
                                            <p>3-Month Planning</p>
                                        </div>
                                    </div>
                                    <div class="menu-highlight" style="background: #f9f9f9; padding: 1rem; border-left: 3px solid var(--primary-color, #c0392b); margin-bottom: 1.5rem;">
                                        <strong>Outcome</strong>
                                        <p style="font-size: 0.9rem; margin-top: 0.5rem;">Seamless execution of a multi-stage event including cocktail hour, seated dinner, and awards ceremony.</p>
                                    </div>
                                    <div class="testimonial-snippet" style="font-style: italic; color: #666;">
                                        "Professional, organized, and creative. The best event our company has ever hosted."
                                    </div>
                                </div>
                                <div class="portfolio-image" style="min-height: 400px; background: linear-gradient(135deg, #2980b9 0%, #2c3e50 100%); display: flex; align-items: center; justify-content: center; order: 2;">
                                    <div style="text-align: center; color: white;">
                                        <span style="font-size: 4rem; display: block; margin-bottom: 1rem;">🎉</span>
                                        <span style="text-transform: uppercase; letter-spacing: 2px;">Corporate Gala</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Case Study 3 -->
                            <div class="portfolio-item case-study" style="grid-column: span 3; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <div class="portfolio-image" style="min-height: 400px; background: linear-gradient(135deg, #8e44ad 0%, #c0392b 100%); display: flex; align-items: center; justify-content: center;">
                                    <div style="text-align: center; color: white;">
                                        <span style="font-size: 4rem; display: block; margin-bottom: 1rem;">✨</span>
                                        <span style="text-transform: uppercase; letter-spacing: 2px;">Wedding</span>
                                    </div>
                                </div>
                                <div class="portfolio-content" style="padding: 2rem; display: flex; flex-direction: column; justify-content: center;">
                                    <span class="portfolio-tag" style="background: #f0f0f0; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; text-transform: uppercase; align-self: flex-start; margin-bottom: 1rem;">Combined Services</span>
                                    <h3>Sarah & James's Intimate Wedding</h3>
                                    <div class="case-study-details" style="margin: 1.5rem 0; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <strong>Guest Count</strong>
                                            <p>45 Guests</p>
                                        </div>
                                        <div>
                                            <strong>Theme</strong>
                                            <p>Rustic Elegance</p>
                                        </div>
                                    </div>
                                    <div class="menu-highlight" style="background: #f9f9f9; padding: 1rem; border-left: 3px solid var(--primary-color, #c0392b); margin-bottom: 1.5rem;">
                                        <strong>The Experience</strong>
                                        <p style="font-size: 0.9rem; margin-top: 0.5rem;">Full event design paired with a farm-to-table family style feast. Custom cocktails and live jazz coordination.</p>
                                    </div>
                                    <div class="testimonial-snippet" style="font-style: italic; color: #666;">
                                        "The perfect blend of logistical precision and culinary magic. It was the wedding of our dreams."
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                    <!-- CTA Section -->
                    <section class="lead-capture">
                        <div class="lead-capture-content">
                            <h2>Ready to Create Your Own Extraordinary Event?</h2>
                            <p>Let's discuss how we can bring your vision to life.</p>
                            <div class="cta-buttons">
                                <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="btn-primary btn-large">Book Free Consultation</a>
                                <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn-secondary btn-large">View Services</a>
                            </div>
                        </div>
                    </section>
                </div>

            </article>
        <?php endwhile; ?>

    </div>
</main>

<style>
/* Responsive adjustments for case studies */
@media (max-width: 768px) {
    .portfolio-item.case-study {
        grid-template-columns: 1fr !important;
    }
    .portfolio-item.case-study .portfolio-content {
        order: 2 !important;
    }
    .portfolio-item.case-study .portfolio-image {
        order: 1 !important;
        min-height: 250px !important;
    }
}
</style>

<?php get_footer(); ?>