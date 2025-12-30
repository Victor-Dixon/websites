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
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Impress Your Guests with Luxury Mobile Bartending in Houston</h1>
                <p class="hero-subtitle">For event hosts who want craft cocktails without venue limitations, we bring premium bar service directly to your location.</p>
                <div class="hero-ctas">
                    <a href="<?php echo esc_url(home_url('/event-planning-guide')); ?>" class="btn-primary btn-large">Get Your Free Event Planning Guide</a>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-secondary btn-large">Request a Quote</a>
                </div>
                <p class="hero-urgency">Limited Spring Availability - Book Now</p>
            </div>
        </div>
    </section>

    <!-- Positioning Statement Section -->
    <section class="positioning-section">
        <div class="container">
            <h2>For Houston Event Hosts Who Want Luxury Bartending</h2>
            <p class="positioning-text">
                For Houston event hosts (weddings, corporate, private parties) struggling with venue bar limitations, 
                we eliminate the stress and deliver impressive experiences with craft cocktails and professional service.
            </p>
            <div class="outcomes-grid">
                <div class="outcome-item">
                    <h3>Your Outcome:</h3>
                    <ul>
                        <li>Guests rave about the cocktails</li>
                        <li>You enjoy your event (stress-free hosting)</li>
                        <li>Professional service throughout</li>
                    </ul>
                </div>
                <div class="outcome-item">
                    <h3>We Eliminate:</h3>
                    <ul>
                        <li>Venue bar limitations and restrictions</li>
                        <li>Lack of expertise in craft cocktails</li>
                        <li>Stress of coordinating bar service</li>
                        <li>Basic catering that doesn't match event importance</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Offer Ladder Section -->
    <section class="offer-ladder-section">
        <div class="container">
            <h2>Our Service Levels</h2>
            <div class="offer-ladder">
                <div class="offer-tier">
                    <div class="tier-number">1</div>
                    <h3>Free Event Planning Guide</h3>
                    <p>Ultimate Event Bar Planning Checklist (PDF)</p>
                    <a href="<?php echo esc_url(home_url('/event-planning-guide')); ?>" class="btn-link">Get Free Guide →</a>
                </div>
                <div class="offer-tier">
                    <div class="tier-number">2</div>
                    <h3>Free Consultation</h3>
                    <p>Complimentary event consultation and custom quote</p>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Request Quote →</a>
                </div>
                <div class="offer-tier">
                    <div class="tier-number">3</div>
                    <h3>Basic Mobile Bar Service</h3>
                    <p>Professional mobile bar service for small to medium events</p>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Learn More →</a>
                </div>
                <div class="offer-tier">
                    <div class="tier-number">4</div>
                    <h3>Premium Luxury Package</h3>
                    <p>Luxury mobile bar experience with craft cocktails</p>
                    <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-link">Get Premium Quote →</a>
                </div>
                <div class="offer-tier">
                    <div class="tier-number">5</div>
                    <h3>Full-Service Event Coordination</h3>
                    <p>Complete event coordination with luxury bar service</p>
                    <a href="<?php echo esc_url(home_url('/book')); ?>" class="btn-link">Schedule Consultation →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <h2>Our Services</h2>
            <div class="services-grid">
                <div class="service-item">
                    <h3>Weddings</h3>
                    <p>Make your special day unforgettable with luxury mobile bar service and craft cocktails.</p>
                </div>
                <div class="service-item">
                    <h3>Corporate Events</h3>
                    <p>Impress clients and colleagues with professional mobile bartending for your corporate gatherings.</p>
                </div>
                <div class="service-item">
                    <h3>Private Parties</h3>
                    <p>Celebrate birthdays, anniversaries, and special occasions with premium bar service.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section -->
    <section class="social-proof-section">
        <div class="container">
            <h2>What Our Clients Say</h2>
            <div class="testimonials-grid">
                <blockquote class="testimonial">
                    <p>"They made our event effortless—and the cocktails were unreal. Our guests are still talking about it!"</p>
                    <cite>— Sarah M., Houston Wedding</cite>
                </blockquote>
                <blockquote class="testimonial">
                    <p>"Professional, elegant, and stress-free. Houston Sip Queen exceeded all our expectations."</p>
                    <cite>— Corporate Event Planner, Houston</cite>
                </blockquote>
                <blockquote class="testimonial">
                    <p>"The craft cocktails were a hit! Best decision we made for our event."</p>
                    <cite>— Private Party Host, Houston</cite>
                </blockquote>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Impress Your Guests?</h2>
            <p>Get started with your free event planning guide or request a custom quote today.</p>
            <div class="cta-buttons">
                <a href="<?php echo esc_url(home_url('/event-planning-guide')); ?>" class="btn-primary btn-large">Get Free Planning Guide</a>
                <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-secondary btn-large">Request a Quote</a>
            </div>
            <p class="cta-phone">Or call us: <a href="tel:+1-XXX-XXX-XXXX" class="phone-link">(XXX) XXX-XXXX</a></p>
        </div>
    </section>

    <!-- Content Area (for WordPress editor content) -->
    <!-- Removed posts loop - homepage should not show blog posts -->
</main>

<?php get_footer(); ?>

