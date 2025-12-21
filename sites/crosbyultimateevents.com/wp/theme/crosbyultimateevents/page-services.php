<?php

/**
 * Services Page Template
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">

        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page services-page'); ?>>

                <header class="entry-header">
                    <h1 class="page-title">Our Services</h1>
                    <p class="page-subtitle">One-stop event planning and private chef services for extraordinary experiences</p>
                </header>

                <div class="services-page-content">
                    <!-- Private Chef Services -->
                    <section class="services-section">
                        <h2 class="section-title">Private Chef Services</h2>
                        <div class="services-grid">
                            <div class="service-card">
                                <div class="service-icon">👨‍🍳</div>
                                <h3>Custom Menu Creation</h3>
                                <p>Personalized menus tailored to your preferences, dietary restrictions, and event theme. From intimate dinners to large gatherings.</p>
                                <ul class="service-features">
                                    <li>Menu consultation and planning</li>
                                    <li>Dietary accommodation (vegan, gluten-free, etc.)</li>
                                    <li>Custom recipe development</li>
                                    <li>Wine pairing recommendations</li>
                                </ul>
                            </div>

                            <div class="service-card">
                                <div class="service-icon">🍽️</div>
                                <h3>In-Home Dining Experience</h3>
                                <p>Fine dining experience in the comfort of your home. Professional service, elegant presentation, and restaurant-quality cuisine.</p>
                                <ul class="service-features">
                                    <li>Full-service dining experience</li>
                                    <li>Table setting and presentation</li>
                                    <li>Professional serving staff</li>
                                    <li>Clean-up included</li>
                                </ul>
                            </div>

                            <div class="service-card">
                                <div class="service-icon">🎉</div>
                                <h3>Event Catering</h3>
                                <p>Complete catering solutions for events of all sizes. From corporate gatherings to intimate celebrations.</p>
                                <ul class="service-features">
                                    <li>Buffet and plated service options</li>
                                    <li>Appetizers and hors d'oeuvres</li>
                                    <li>Main courses and desserts</li>
                                    <li>Beverage service coordination</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Event Planning Services -->
                    <section class="services-section">
                        <h2 class="section-title">Event Planning Services</h2>
                        <div class="services-grid">
                            <div class="service-card">
                                <div class="service-icon">📅</div>
                                <h3>Full Event Coordination</h3>
                                <p>End-to-end event planning from concept to execution. We handle every detail so you can enjoy your event.</p>
                                <ul class="service-features">
                                    <li>Venue selection and booking</li>
                                    <li>Vendor coordination</li>
                                    <li>Timeline and schedule management</li>
                                    <li>Day-of coordination</li>
                                </ul>
                            </div>

                            <div class="service-card">
                                <div class="service-icon">🎨</div>
                                <h3>Theme & Design</h3>
                                <p>Create a cohesive event experience with custom themes, decor, and design elements that reflect your vision.</p>
                                <ul class="service-features">
                                    <li>Theme development</li>
                                    <li>Decor and styling</li>
                                    <li>Color scheme coordination</li>
                                    <li>Rental coordination</li>
                                </ul>
                            </div>

                            <div class="service-card">
                                <div class="service-icon">📋</div>
                                <h3>Vendor Management</h3>
                                <p>We work with trusted vendors to bring your event to life. From florists to photographers, we've got you covered.</p>
                                <ul class="service-features">
                                    <li>Vendor recommendations</li>
                                    <li>Contract negotiation</li>
                                    <li>Vendor coordination</li>
                                    <li>Quality assurance</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- Combined Services -->
                    <section class="services-section">
                        <h2 class="section-title">Combined Services</h2>
                        <div class="services-grid">
                            <div class="service-card">
                                <div class="service-icon">✨</div>
                                <h3>Complete Event Package</h3>
                                <p>The ultimate convenience: full event planning plus private chef services. One point of contact for your entire event.</p>
                                <ul class="service-features">
                                    <li>Event planning + catering</li>
                                    <li>Seamless coordination</li>
                                    <li>Unified pricing</li>
                                    <li>Stress-free experience</li>
                                </ul>
                            </div>

                            <div class="service-card">
                                <div class="service-icon">🏆</div>
                                <h3>Premium Experience</h3>
                                <p>Luxury event experience with white-glove service, premium ingredients, and attention to every detail.</p>
                                <ul class="service-features">
                                    <li>Premium menu options</li>
                                    <li>Exclusive venue access</li>
                                    <li>Concierge-level service</li>
                                    <li>Personal event manager</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- CTA Section -->
                    <section class="lead-capture">
                        <div class="lead-capture-content">
                            <h2>Ready to Create an Extraordinary Event?</h2>
                            <p>Let's discuss how we can bring your vision to life with our comprehensive event planning and private chef services.</p>
                            <div class="cta-buttons">
                                <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="btn-primary btn-large">Book Free Consultation</a>
                                <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn-secondary btn-large">Contact Us</a>
                            </div>
                        </div>
                    </section>
                </div>

            </article>
        <?php endwhile; ?>

    </div>
</main>

<?php get_footer(); ?>