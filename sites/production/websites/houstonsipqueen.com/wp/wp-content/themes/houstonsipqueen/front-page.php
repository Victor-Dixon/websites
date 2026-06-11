<?php
/**
 * Front Page Template - Homepage
 * 
 * @package HoustonSipQueen
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-content">
                <p class="hero-eyebrow">Luxury Mobile Bartending · Houston</p>
                <h1 class="hero-title">Sip pretty. <em>Party smooth.</em></h1>
                <p class="hero-subtitle">For brides and hosts who want a bar that feels as intentional as the rest of the celebration&mdash;polished service, signature sips, and a look that photographs beautifully.</p>
                <div class="hero-ctas">
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-primary btn-large">Request Wedding Quote</a>
                    <a href="#why-brides" class="btn-secondary btn-large">Why Brides Choose Us</a>
                </div>
                <span class="hero-tagline">Bridal-forward · Houston luxury</span>
            </div>
            <aside class="bridal-panel" id="bridal">
                <p class="hero-eyebrow bridal">For Your Wedding Day</p>
                <h2 class="bridal-title">Your wedding bar, elevated.</h2>
                <p>From rehearsal dinners to reception toasts&mdash;curated cocktails, champagne-forward pours, and bar styling that matches your palette.</p>
                <div class="bridal-placeholder" aria-hidden="true">Bridal bar styling · champagne tower · signature cocktail menu</div>
                <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-primary">Plan My Wedding Bar</a>
            </aside>
        </div>
    </section>

    <!-- Why Brides Choose HSQ -->
    <section class="why-brides-section" id="why-brides">
        <div class="container">
            <h2>Why Brides Choose Houston Sip Queen</h2>
            <p class="section-intro">Not another generic bartender listing&mdash;a bar experience designed to feel feminine, elevated, and unmistakably yours.</p>
            <div class="why-brides-grid">
                <div class="why-brides-item">
                    <h3>Curated, not copied</h3>
                    <p>Menus shaped around your colors, season, and celebration&mdash;not a one-size-fits-all drink list.</p>
                </div>
                <div class="why-brides-item">
                    <h3>Photo-ready bar styling</h3>
                    <p>Glassware, garnishes, and a polished setup that belongs in your wedding photos.</p>
                </div>
                <div class="why-brides-item">
                    <h3>You enjoy your day</h3>
                    <p>We handle service flow and breakdown so you are hosting&mdash;not managing the bar.</p>
                </div>
                <div class="why-brides-item">
                    <h3>Local, mobile, flexible</h3>
                    <p>Venue limitations, backyard receptions, and private estates&mdash;we bring the bar to you.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Positioning Statement Section -->
    <section class="positioning-section">
        <div class="container">
            <h2>For Houston Event Hosts Who Want Luxury Bartending</h2>
            <p class="positioning-text">
                For Houston event hosts (weddings, corporate, private parties) who want elevated bar service without venue limitations,
                we bring polished mobile bartending directly to your location.
            </p>
            <div class="outcomes-grid">
                <div class="outcome-item">
                    <h3>Your Outcome:</h3>
                    <ul>
                        <li>Guests enjoy craft cocktails and attentive service</li>
                        <li>You host with confidence&mdash;we handle the bar</li>
                        <li>Professional, feminine, luxury presentation throughout</li>
                    </ul>
                </div>
                <div class="outcome-item">
                    <h3>We Eliminate:</h3>
                    <ul>
                        <li>Venue bar limitations and restrictions</li>
                        <li>Stress of coordinating bar staff and setup</li>
                        <li>Generic drink service that doesn't match your event</li>
                        <li>Last-minute bar planning guesswork</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages-section">
        <div class="container">
            <h2>Houston Sip Queen Packages</h2>
            <p class="section-intro">Starting-at pricing for private events. Final quotes depend on guest count, service hours, and menu selections.</p>
            <div class="packages-grid">
                <div class="package-card">
                    <h3>Queen Pour</h3>
                    <p class="package-price">Starting at <strong>$175&ndash;$250</strong></p>
                    <p>Essential mobile bar service for intimate gatherings&mdash;professional bartending, polished setup, and crowd-pleasing pours.</p>
                    <ul>
                        <li>Ideal for small parties and showers</li>
                        <li>Streamlined bar menu</li>
                        <li>Professional mobile setup</li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Book Queen Pour &rarr;</a>
                </div>
                <div class="package-card package-featured">
                    <h3>Sip Queen Experience</h3>
                    <p class="package-price">Starting at <strong>$300&ndash;$450</strong></p>
                    <p>Our signature experience&mdash;elevated cocktails, curated menu options, and luxury service for memorable celebrations.</p>
                    <ul>
                        <li>Weddings, milestones, and upscale private events</li>
                        <li>Custom cocktail and mocktail options</li>
                        <li>Signature menu available</li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Book Sip Queen Experience &rarr;</a>
                </div>
                <div class="package-card">
                    <h3>Royal Event Bar</h3>
                    <p class="package-price">Starting at <strong>$600+</strong></p>
                    <p>Full-scale luxury mobile bar for larger events&mdash;expanded service hours, premium presentation, and elevated menu design.</p>
                    <ul>
                        <li>Corporate galas and large private events</li>
                        <li>Extended service and staffing</li>
                        <li>Signature menu and mocktail program</li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Book Royal Event Bar &rarr;</a>
                </div>
            </div>
            <?php houstonsipqueen_alcohol_policy_notice(); ?>
        </div>
    </section>

    <!-- Offer Ladder Section -->
    <section class="offer-ladder-section">
        <div class="container">
            <h2>How Booking Works</h2>
            <div class="offer-ladder">
                <div class="offer-tier">
                    <div class="tier-number">1</div>
                    <h3>Free Event Planning Guide</h3>
                    <p>Ultimate Event Bar Planning Checklist (PDF)</p>
                    <a href="<?php echo esc_url(home_url('/event-planning-guide')); ?>" class="btn-link">Get Free Guide &rarr;</a>
                </div>
                <div class="offer-tier">
                    <div class="tier-number">2</div>
                    <h3>Request a Quote</h3>
                    <p>Share your event details&mdash;we respond within 24 hours</p>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Book Your Event &rarr;</a>
                </div>
                <div class="offer-tier">
                    <div class="tier-number">3</div>
                    <h3>Consult &amp; Confirm</h3>
                    <p>Menu, timeline, and service plan tailored to your event</p>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Start Booking &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <h2>Events We Serve</h2>
            <div class="services-grid">
                <div class="service-item">
                    <h3>Weddings</h3>
                    <p>Elegant mobile bar service and craft cocktails for your celebration.</p>
                </div>
                <div class="service-item">
                    <h3>Corporate Events</h3>
                    <p>Professional bartending that impresses clients and colleagues.</p>
                </div>
                <div class="service-item">
                    <h3>Private Parties</h3>
                    <p>Birthdays, anniversaries, and celebrations with luxury bar service.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Section (no fabricated testimonials) -->
    <section class="trust-section">
        <div class="container">
            <h2>Why Hosts Book Houston Sip Queen</h2>
            <div class="trust-grid">
                <div class="trust-item">
                    <h3>Luxury Mobile Service</h3>
                    <p>We bring a polished, fully equipped bar experience to your venue&mdash;no venue bar required.</p>
                </div>
                <div class="trust-item">
                    <h3>Houston-Focused</h3>
                    <p>Rooted in Houston with a feminine, elevated brand voice hosts recognize and trust.</p>
                </div>
                <div class="trust-item">
                    <h3>Menu Flexibility</h3>
                    <p>Craft cocktails, mocktails, and signature menus tailored to your event style and guest list.</p>
                </div>
                <div class="trust-item">
                    <h3>Clear Policies</h3>
                    <p>Transparent service terms: host-provided alcohol, legal-age guests, professional standards.</p>
                </div>
            </div>
            <div class="instagram-cta">
                <p>See our work and event inspiration on Instagram</p>
                <a href="https://www.instagram.com/houston_sipqueen/" class="btn-secondary btn-large" target="_blank" rel="noopener noreferrer">Follow @houston_sipqueen</a>
            </div>
        </div>
    </section>

    <!-- Security Partner Advantage -->
    <section class="security-partner-section" id="security">
        <div class="container">
            <div class="security-card">
                <p class="hero-eyebrow">Security Partner Advantage</p>
                <h2>Discounted security coordination when regulations require it.</h2>
                <p>Some private events in Texas and the Houston area must meet security requirements set by law, local ordinance, or venue policy. For qualifying bookings, Houston Sip Queen can connect you with vetted security partners at a coordinated discount.</p>
                <ul>
                    <li>Help identifying when your event type or venue may require licensed security</li>
                    <li>Introduction to independent security partners (not provided directly by Houston Sip Queen)</li>
                    <li>Coordinated pricing for hosts booking bar service through Houston Sip Queen</li>
                </ul>
                <p class="security-disclaimer"><strong>Important:</strong> Event hosts remain responsible for confirming and meeting all legal, venue, and permit requirements. Houston Sip Queen provides mobile bartending service only; security services are arranged through independent licensed partners.</p>
                <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-primary btn-large">Request Quote with Security Coordination</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Book Your Event?</h2>
            <p>Tell us about your date, guest count, and vision&mdash;we'll recommend the right package.</p>
            <div class="cta-buttons">
                <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-primary btn-large">Book Your Private Event</a>
                <a href="<?php echo esc_url(home_url('/event-planning-guide')); ?>" class="btn-secondary btn-large">Get Free Planning Guide</a>
            </div>
            <?php houstonsipqueen_alcohol_policy_notice(); ?>
            <p class="cta-phone">Questions? Email <a href="mailto:houstonsipqueen@gmail.com" class="phone-link">houstonsipqueen@gmail.com</a></p>
        </div>
    </section>
</main>

<?php get_footer(); ?>
