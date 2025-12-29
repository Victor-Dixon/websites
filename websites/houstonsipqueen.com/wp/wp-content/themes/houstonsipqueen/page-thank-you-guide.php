<?php
/**
 * Thank-You Page Template - Event Planning Guide
 * 
 * @package HoustonSipQueen
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">
        <section class="thank-you-page">
            <div class="container">
                <!-- Hero Section -->
                <header class="entry-header">
                    <h1 class="page-title">Your Checklist Is On Its Way</h1>
                    <p class="page-subtitle">We just emailed you the download link. If you don't see it in 2–3 minutes, check spam/promotions.</p>
                </header>

                <!-- Download Section -->
                <section class="download-section">
                    <h2>Instant Access</h2>
                    <p>If you want instant access, download here:</p>
                    <a href="<?php echo esc_url(home_url('/wp-content/uploads/event-bar-planning-checklist.pdf')); ?>" 
                       class="btn-primary btn-large" 
                       download="Event-Bar-Planning-Checklist.pdf">
                        Download Checklist PDF
                    </a>
                </section>

                <!-- Next Best Step Section -->
                <section class="next-step-section">
                    <h2>Want a Bar Plan That Fits Your Venue, Guest Count, and Vibe?</h2>
                    <p>Book a quick consultation or request a quote. We'll recommend the right service level and share next steps.</p>
                    <div class="cta-buttons">
                        <a href="<?php echo esc_url(home_url('/book')); ?>" class="btn-primary btn-large">Book a Consultation</a>
                        <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-secondary btn-large">Request a Quote</a>
                    </div>
                </section>

                <!-- Social Proof Section -->
                <section class="social-proof-section">
                    <blockquote class="testimonial">
                        <p>"They made our event effortless—and the cocktails were unreal."</p>
                        <cite>— Client Name, Houston</cite>
                    </blockquote>
                </section>

                <!-- What Happens Next Section -->
                <section class="process-section">
                    <h2>What Happens Next</h2>
                    <ol class="process-list">
                        <li>
                            <strong>You tell us your event basics</strong><br>
                            (date, location, guest count)
                        </li>
                        <li>
                            <strong>We propose bar options</strong><br>
                            (cocktail menu + staffing)
                        </li>
                        <li>
                            <strong>You reserve your date</strong><br>
                            (deposit + confirmation)
                        </li>
                    </ol>
                </section>

                <!-- Footer CTA -->
                <section class="footer-cta-section">
                    <p>Prefer to talk now? <a href="tel:+1-XXX-XXX-XXXX" class="phone-link">Call/Text (XXX) XXX-XXXX</a></p>
                </section>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>

