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

                    <!-- Portfolio Categories -->
                    <section class="portfolio-categories">
                        <div class="category-filters">
                            <button class="filter-btn active" data-filter="all">All Events</button>
                            <button class="filter-btn" data-filter="private-chef">Private Chef</button>
                            <button class="filter-btn" data-filter="event-planning">Event Planning</button>
                            <button class="filter-btn" data-filter="combined">Combined Services</button>
                        </div>
                    </section>

                    <!-- Portfolio Grid -->
                    <section class="portfolio-grid-section">
                        <div class="portfolio-grid">
                            <!-- Portfolio Item 1 -->
                            <div class="portfolio-item" data-category="private-chef">
                                <div class="portfolio-image">
                                    <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-color) 0%, #000000 100%); height: 300px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                        🍽️
                                    </div>
                                </div>
                                <div class="portfolio-content">
                                    <h3>Intimate Dinner Party</h3>
                                    <p class="portfolio-type">Private Chef Service</p>
                                    <p>Custom 5-course menu for an intimate gathering of 12 guests. Featured seasonal ingredients and wine pairings.</p>
                                </div>
                            </div>

                            <!-- Portfolio Item 2 -->
                            <div class="portfolio-item" data-category="event-planning">
                                <div class="portfolio-image">
                                    <div class="placeholder-image" style="background: linear-gradient(135deg, #000000 0%, var(--primary-color) 100%); height: 300px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                        🎉
                                    </div>
                                </div>
                                <div class="portfolio-content">
                                    <h3>Corporate Gala</h3>
                                    <p class="portfolio-type">Event Planning</p>
                                    <p>Full event coordination for 200+ guests including venue selection, decor, entertainment, and vendor management.</p>
                                </div>
                            </div>

                            <!-- Portfolio Item 3 -->
                            <div class="portfolio-item" data-category="combined">
                                <div class="portfolio-image">
                                    <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-color) 0%, #000000 100%); height: 300px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                        ✨
                                    </div>
                                </div>
                                <div class="portfolio-content">
                                    <h3>Wedding Celebration</h3>
                                    <p class="portfolio-type">Combined Services</p>
                                    <p>Complete wedding planning and catering for 150 guests. Custom menu, full event coordination, and seamless execution.</p>
                                </div>
                            </div>

                            <!-- Portfolio Item 4 -->
                            <div class="portfolio-item" data-category="private-chef">
                                <div class="portfolio-image">
                                    <div class="placeholder-image" style="background: linear-gradient(135deg, #000000 0%, var(--primary-color) 100%); height: 300px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                        🎂
                                    </div>
                                </div>
                                <div class="portfolio-content">
                                    <h3>Anniversary Celebration</h3>
                                    <p class="portfolio-type">Private Chef Service</p>
                                    <p>Elegant 50th anniversary dinner with personalized menu reflecting the couple's favorite dishes from their travels.</p>
                                </div>
                            </div>

                            <!-- Portfolio Item 5 -->
                            <div class="portfolio-item" data-category="event-planning">
                                <div class="portfolio-image">
                                    <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-color) 0%, #000000 100%); height: 300px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                        🎨
                                    </div>
                                </div>
                                <div class="portfolio-content">
                                    <h3>Themed Birthday Party</h3>
                                    <p class="portfolio-type">Event Planning</p>
                                    <p>Complete theme development, decor, and coordination for a milestone birthday celebration with 80 guests.</p>
                                </div>
                            </div>

                            <!-- Portfolio Item 6 -->
                            <div class="portfolio-item" data-category="combined">
                                <div class="portfolio-image">
                                    <div class="placeholder-image" style="background: linear-gradient(135deg, #000000 0%, var(--primary-color) 100%); height: 300px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                        🏆
                                    </div>
                                </div>
                                <div class="portfolio-content">
                                    <h3>Premium Corporate Event</h3>
                                    <p class="portfolio-type">Combined Services</p>
                                    <p>Luxury event experience with premium catering, exclusive venue, and white-glove service for VIP clients.</p>
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

<?php get_footer(); ?>