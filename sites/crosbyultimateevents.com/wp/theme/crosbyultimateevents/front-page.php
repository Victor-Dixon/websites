<?php

/**
 * Front Page Template
 * 
 * Custom homepage template with sales funnel elements
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

get_header(); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <?php
        // Get A/B test variant content
        $hero_content = crosby_get_hero_content();
        ?>
        <h1><?php echo esc_html($hero_content['headline']); ?></h1>
        <p class="hero-subtitle"><?php echo esc_html($hero_content['subtitle']); ?></p>
        <div class="hero-cta">
            <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="btn-primary btn-large">Book Your Consultation</a>
            <a href="<?php echo esc_url(home_url('/services')); ?>" class="btn-secondary btn-large">Explore Our Services</a>
        </div>
    </div>
</section>

<main class="site-main">

    <!-- Value Proposition Section -->
    <section class="value-proposition">
        <div class="container">
            <h2 class="section-title">Why Choose Crosby Ultimate Events?</h2>
            <div class="value-grid">
                <div class="value-item">
                    <div class="value-icon">🎯</div>
                    <h3>Personalized Service</h3>
                    <p>Custom menus and tailored experiences designed specifically for your event and preferences</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">⭐</div>
                    <h3>Premium Quality</h3>
                    <p>Exceptional cuisine with attention to detail and professional execution at every step</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">🤝</div>
                    <h3>Dual Expertise</h3>
                    <p>Unique combination of culinary artistry and event planning for seamless experiences</p>
                </div>
                <div class="value-item">
                    <div class="value-icon">✨</div>
                    <h3>Attention to Detail</h3>
                    <p>Meticulous planning and execution ensuring every aspect exceeds expectations</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview Section -->
    <section class="services-overview">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">🍽️</div>
                    <h3>Private Chef Services</h3>
                    <p>In-home dining experiences, multi-course fine dining, custom menus, and cooking classes</p>
                    <ul class="service-features">
                        <li>In-Home Dining Experiences</li>
                        <li>Cooking Classes</li>
                        <li>Meal Prep Services</li>
                        <li>Dietary Restriction Accommodations</li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/services#private-chef')); ?>" class="btn-outline">Learn More</a>
                </div>

                <div class="service-card">
                    <div class="service-icon">🎉</div>
                    <h3>Event Planning Services</h3>
                    <p>Full-service event coordination, vendor management, and day-of coordination</p>
                    <ul class="service-features">
                        <li>Full Event Coordination</li>
                        <li>Corporate Events</li>
                        <li>Private Parties & Celebrations</li>
                        <li>Intimate Weddings</li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/services#event-planning')); ?>" class="btn-outline">Learn More</a>
                </div>

                <div class="service-card">
                    <div class="service-icon">🎁</div>
                    <h3>Service Packages</h3>
                    <p>Combined services for complete event solutions</p>
                    <ul class="service-features">
                        <li>Intimate Dining Experience ($800-$1,500)</li>
                        <li>Celebration Package ($3,000-$8,000)</li>
                        <li>Corporate Experience ($2,000-$10,000)</li>
                        <li>Custom Packages Available</li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/services#packages')); ?>" class="btn-outline">View Packages</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Lead Capture Section -->
    <section class="lead-capture">
        <div class="container">
            <div class="lead-capture-content">
                <h2>Plan Your Perfect Event</h2>
                <p>Get started with a free consultation to discuss your vision and explore how we can bring it to life</p>
                <div class="lead-form-container">
                    <form class="consultation-form" action="<?php echo esc_url(home_url('/consultation')); ?>" method="get">
                        <div class="form-row">
                            <input type="text" name="name" placeholder="Your Name" required>
                            <input type="email" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="form-row">
                            <input type="tel" name="phone" placeholder="Phone Number" required>
                            <select name="event_type" required>
                                <option value="">Event Type</option>
                                <option value="private-chef">Private Chef Service</option>
                                <option value="event-planning">Event Planning</option>
                                <option value="combined">Combined Services</option>
                                <option value="corporate">Corporate Event</option>
                                <option value="wedding">Wedding</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <textarea name="message" placeholder="Tell us about your event vision..." rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn-primary btn-block">Request Free Consultation</button>
                        <p class="form-note">We'll respond within 24 hours to schedule your consultation</p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section -->
    <section class="social-proof">
        <div class="container">
            <h2 class="section-title">What Our Clients Say</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-rating">★★★★★</div>
                    <p class="testimonial-text">"Crosby Ultimate Events transformed our anniversary dinner into an unforgettable experience. The attention to detail and culinary expertise exceeded all expectations."</p>
                    <p class="testimonial-author">— Sarah & Michael, Anniversary Celebration</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-rating">★★★★★</div>
                    <p class="testimonial-text">"The best corporate event we've ever hosted. Professional service, exceptional food, and seamless coordination from start to finish."</p>
                    <p class="testimonial-author">— Corporate Client</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-rating">★★★★★</div>
                    <p class="testimonial-text">"Our intimate wedding was absolutely perfect. The combination of event planning and chef services made everything stress-free and beautiful."</p>
                    <p class="testimonial-author">— Wedding Couple</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="final-cta">
        <div class="container">
            <h2>Ready to Create an Extraordinary Experience?</h2>
            <p>Let's discuss how we can make your next event unforgettable</p>
            <div class="cta-buttons">
                <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="btn-primary btn-large">Book Your Consultation</a>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn-secondary btn-large">Contact Us</a>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>