<?php
/**
 * Front Page Template for Houston Sip Queen Theme
 * Displays the luxury mobile bartending hero and service offerings
 */

get_header();
?>

<?php
// Include the luxury hero section
get_template_part('template-parts/hero', 'luxury');
?>

<main id="primary" class="site-main">
    <div class="container">

        <!-- Signature Cocktails Section -->
        <section class="signature-cocktails py-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Signature Cocktails
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Handcrafted cocktails that elevate every occasion with Southern elegance and premium spirits
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- French 75 -->
                <div class="cocktail-card">
                    <div class="cocktail-image">
                        <div class="cocktail-glass">🥂</div>
                    </div>
                    <div class="cocktail-content">
                        <h3 class="cocktail-title">French 75</h3>
                        <p class="cocktail-description">
                            Classic champagne cocktail with gin, lemon juice, and a touch of simple syrup.
                            Light, refreshing, and perfectly balanced for any celebration.
                        </p>
                        <div class="cocktail-meta">
                            <span class="meta-item">Spirit: Gin</span>
                            <span class="meta-item">Sweetness: Semi-Dry</span>
                        </div>
                    </div>
                </div>

                <!-- Southern Belle -->
                <div class="cocktail-card">
                    <div class="cocktail-image">
                        <div class="cocktail-glass">🍸</div>
                    </div>
                    <div class="cocktail-content">
                        <h3 class="cocktail-title">Southern Belle</h3>
                        <p class="cocktail-description">
                            Bourbon-based cocktail with peach puree, mint, and a hint of honey.
                            A taste of Southern hospitality in every sip.
                        </p>
                        <div class="cocktail-meta">
                            <span class="meta-item">Spirit: Bourbon</span>
                            <span class="meta-item">Sweetness: Balanced</span>
                        </div>
                    </div>
                </div>

                <!-- Midnight Rose -->
                <div class="cocktail-card">
                    <div class="cocktail-image">
                        <div class="cocktail-glass">🍷</div>
                    </div>
                    <div class="cocktail-content">
                        <h3 class="cocktail-title">Midnight Rose</h3>
                        <p class="cocktail-description">
                            Elegant vodka martini with rosewater, cranberry, and fresh lime.
                            Sophisticated and unforgettable for special occasions.
                        </p>
                        <div class="cocktail-meta">
                            <span class="meta-item">Spirit: Vodka</span>
                            <span class="meta-item">Sweetness: Dry</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Luxury Packages Section -->
        <section class="luxury-packages py-16 bg-gray-50">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Luxury Packages
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Complete mobile bartending experiences tailored to your event's elegance and sophistication
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Signature Experience -->
                <div class="package-card">
                    <div class="package-header">
                        <h3 class="package-title">Signature Experience</h3>
                        <div class="package-price">$1,500</div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li>Professional mobile bar setup</li>
                            <li>8 signature cocktails</li>
                            <li>Premium glassware & garnishes</li>
                            <li>3 hours of service</li>
                            <li>Up to 50 guests</li>
                        </ul>
                        <a href="#quote" class="btn package-btn">Get Quote</a>
                    </div>
                </div>

                <!-- Premium Package -->
                <div class="package-card package-featured">
                    <div class="package-header">
                        <h3 class="package-title">Premium Package</h3>
                        <div class="package-price">$2,200</div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li>Everything in Signature</li>
                            <li>Custom cocktail menu</li>
                            <li>Non-alcoholic options</li>
                            <li>4 hours of service</li>
                            <li>Up to 75 guests</li>
                            <li>Setup & breakdown included</li>
                        </ul>
                        <a href="#quote" class="btn package-btn">Get Quote</a>
                    </div>
                </div>

                <!-- Luxury Affair -->
                <div class="package-card">
                    <div class="package-header">
                        <h3 class="package-title">Luxury Affair</h3>
                        <div class="package-price">$3,500</div>
                    </div>
                    <div class="package-content">
                        <ul class="package-features">
                            <li>Everything in Premium</li>
                            <li>Premium spirit selection</li>
                            <li>Mixologist consultation</li>
                            <li>5+ hours of service</li>
                            <li>Unlimited guests</li>
                            <li>Photo booth integration</li>
                        </ul>
                        <a href="#quote" class="btn package-btn">Get Quote</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Service Areas & Events Section -->
        <section class="service-areas py-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Events We Elevate
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    From intimate gatherings to grand celebrations, we bring the bar to you
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="event-type-card">
                    <div class="event-icon">💒</div>
                    <h3>Weddings</h3>
                    <p>Romantic celebrations with custom cocktails</p>
                </div>
                <div class="event-type-card">
                    <div class="event-icon">🎉</div>
                    <h3>Corporate Events</h3>
                    <p>Professional mixology for business gatherings</p>
                </div>
                <div class="event-type-card">
                    <div class="event-icon">🏰</div>
                    <h3>Private Parties</h3>
                    <p>Exclusive experiences for special occasions</p>
                </div>
                <div class="event-type-card">
                    <div class="event-icon">🌅</div>
                    <h3>Outdoor Events</h3>
                    <p>Mobile service for gardens, pools, and venues</p>
                </div>
            </div>

            <div class="service-area-map mt-12">
                <h3 class="text-2xl font-bold text-center text-gray-900 mb-8">Service Area</h3>
                <div class="map-placeholder">
                    <div class="map-icon">📍</div>
                    <p class="map-text">Serving Houston, TX and surrounding areas with luxury mobile bartending</p>
                    <p class="map-subtext">Travel fees may apply for locations outside the Houston metro area</p>
                </div>
            </div>
        </section>

        <!-- Quote Form Section -->
        <section class="quote-section py-16 bg-gray-50" id="quote">
            <div class="container">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="quote-content">
                        <h2 class="text-4xl font-bold text-gray-900 mb-6">
                            Let's Create Your Perfect Event
                        </h2>
                        <p class="text-xl text-gray-600 mb-8">
                            Every celebration deserves exceptional cocktails. Tell us about your event,
                            and we'll craft a custom experience that exceeds your expectations.
                        </p>
                        <div class="quote-features">
                            <div class="feature-item">
                                <div class="feature-icon">✨</div>
                                <div class="feature-text">
                                    <strong>Custom Menus</strong>
                                    <p>Tailored cocktail selections for your theme</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">🎯</div>
                                <div class="feature-text">
                                    <strong>Professional Service</strong>
                                    <p>Experienced mixologists and flawless execution</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">💎</div>
                                <div class="feature-text">
                                    <strong>Luxury Experience</strong>
                                    <p>Premium spirits, elegant presentation, unforgettable memories</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="quote-form-container">
                        <form class="quote-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="submit_quote">

                            <div class="form-group">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input">
                            </div>

                            <div class="form-group">
                                <label for="event_type" class="form-label">Event Type</label>
                                <select id="event_type" name="event_type" class="form-select">
                                    <option value="">Select Event Type</option>
                                    <option value="wedding">Wedding</option>
                                    <option value="corporate">Corporate Event</option>
                                    <option value="private">Private Party</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="event_date" class="form-label">Event Date</label>
                                <input type="date" id="event_date" name="event_date" class="form-input">
                            </div>

                            <div class="form-group">
                                <label for="guest_count" class="form-label">Expected Guest Count</label>
                                <input type="number" id="guest_count" name="guest_count" class="form-input" min="1">
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label">Tell us about your event</label>
                                <textarea id="message" name="message" class="form-textarea" rows="4" placeholder="Describe your vision, preferred cocktails, theme, etc."></textarea>
                            </div>

                            <button type="submit" class="btn form-submit-btn">Send Quote Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </div><!-- .container -->
</main><!-- #primary -->

<?php get_footer(); ?>

<style>
/* Additional front page styles */

.cocktail-meta {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    font-size: 0.8rem;
    color: var(--onyx-light);
}

.meta-item {
    background: rgba(201, 162, 106, 0.1);
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
}

.package-header {
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid rgba(201, 162, 106, 0.2);
}

.package-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--onyx);
    margin-bottom: 0.5rem;
}

.package-price {
    font-size: 2rem;
    font-weight: 700;
    color: var(--rosegold);
}

.package-content {
    padding: 2rem;
}

.package-features {
    list-style: none;
    margin-bottom: 2rem;
}

.package-features li {
    padding: 0.5rem 0;
    position: relative;
    padding-left: 1.5rem;
}

.package-features li::before {
    content: '✨';
    position: absolute;
    left: 0;
    color: var(--rosegold);
}

.package-btn {
    width: 100%;
    text-align: center;
}

.event-type-card {
    background: var(--white);
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(11, 11, 15, 0.1);
    border: 1px solid rgba(201, 162, 106, 0.1);
    transition: transform 0.3s ease;
}

.event-type-card:hover {
    transform: translateY(-5px);
}

.event-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.event-type-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--onyx);
    margin-bottom: 0.5rem;
}

.event-type-card p {
    color: var(--onyx-light);
    font-size: 0.9rem;
}

.service-area-map {
    margin-top: 3rem;
}

.map-placeholder {
    background: linear-gradient(135deg, var(--champagne) 0%, var(--rosegold) 100%);
    border-radius: 15px;
    padding: 3rem;
    text-align: center;
    color: var(--onyx);
}

.map-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.map-text {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.map-subtext {
    font-size: 0.9rem;
    opacity: 0.8;
}

.quote-features {
    display: grid;
    gap: 1.5rem;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.feature-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.feature-text strong {
    display: block;
    color: var(--onyx);
    margin-bottom: 0.25rem;
}

.feature-text p {
    color: var(--onyx-light);
    font-size: 0.9rem;
    margin: 0;
}

.form-submit-btn {
    width: 100%;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .signature-cocktails .grid,
    .luxury-packages .grid {
        grid-template-columns: 1fr;
    }

    .service-areas .grid {
        grid-template-columns: 1fr 1fr;
    }

    .quote-section .grid {
        grid-template-columns: 1fr;
        gap: 3rem;
    }

    .quote-features {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .service-areas .grid {
        grid-template-columns: 1fr;
    }
}
</style>