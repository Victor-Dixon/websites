<?php
/**
 * Event Planning Guide Landing Page Template
 * Lead Magnet: Ultimate Event Bar Planning Checklist
 * 
 * @package HoustonSipQueen
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">
        <section class="lead-magnet-page">
            <div class="container">
                <!-- Hero Section -->
                <header class="entry-header">
                    <h1 class="page-title">Impress Your Guests With a Perfectly Planned Event Bar</h1>
                    <p class="page-subtitle">Get the <strong>Ultimate Event Bar Planning Checklist</strong> (free) and plan cocktails, staffing, quantities, and timeline—without the stress.</p>
                    <p class="trust-line">Luxury mobile bartending for Houston weddings, corporate events, and private parties.</p>
                </header>

                <!-- Lead Magnet Form Section -->
                <div class="lead-magnet-form-section">
                    <?php
                    // Display success/error messages
                    if (isset($_GET['submitted'])) {
                        if ($_GET['submitted'] === 'success') {
                            echo '<div class="form-message form-success">';
                            echo '<p><strong>Success!</strong> Check your email for the download link.</p>';
                            echo '</div>';
                        } else {
                            $errors = get_transient('lead_magnet_form_errors');
                            if ($errors) {
                                echo '<div class="form-message form-error">';
                                echo '<ul>';
                                foreach ($errors as $error) {
                                    echo '<li>' . esc_html($error) . '</li>';
                                }
                                echo '</ul>';
                                echo '</div>';
                                delete_transient('lead_magnet_form_errors');
                            }
                        }
                    }
                    ?>

                    <form class="lead-magnet-form" method="post" action="<?php echo esc_url(get_permalink()); ?>">
                        <?php wp_nonce_field('lead_magnet_form', 'lead_magnet_nonce'); ?>
                        
                        <!-- Honeypot field for spam protection -->
                        <input type="text" name="website_url" value="" style="display: none;" tabindex="-1" autocomplete="off">

                        <div class="form-group">
                            <label for="lead_first_name">First Name *</label>
                            <input type="text" id="lead_first_name" name="lead_first_name" required>
                        </div>

                        <div class="form-group">
                            <label for="lead_email">Email *</label>
                            <input type="email" id="lead_email" name="lead_email" required>
                        </div>

                        <div class="form-group">
                            <label for="lead_event_date">Event Date (Optional)</label>
                            <input type="date" id="lead_event_date" name="lead_event_date">
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="btn-primary btn-large">Get the Free Checklist</button>
                            <p class="form-note">We'll email it to you instantly. No spam—unsubscribe anytime.</p>
                        </div>
                    </form>
                </div>

                <!-- What You'll Get Section -->
                <section class="benefits-section">
                    <h2>What You'll Get</h2>
                    <ul class="benefits-list">
                        <li>A step-by-step bar planning timeline (weeks before → day-of)</li>
                        <li>What to buy + how much to buy (simple quantity guidance)</li>
                        <li>Staffing + setup notes that prevent long lines</li>
                        <li>Cocktail + spirit planning (including non-alcoholic options)</li>
                        <li>A final "day-of" checklist so nothing gets missed</li>
                    </ul>
                </section>

                <!-- Who This Is For Section -->
                <section class="target-audience-section">
                    <h2>Who This Is For</h2>
                    <ul class="audience-list">
                        <li>Hosts planning a <strong>wedding</strong>, <strong>corporate event</strong>, or <strong>private party</strong></li>
                        <li>Anyone dealing with <strong>venue bar limitations</strong> or unclear bar rules</li>
                        <li>People who want <strong>craft cocktails</strong> and a polished guest experience</li>
                    </ul>
                </section>

                <!-- Quick Win Section -->
                <section class="quick-wins-section">
                    <h2>3 Ways to Avoid the Most Common Bar Mistakes</h2>
                    <ol class="quick-wins-list">
                        <li><strong>Plan your vision first</strong> (then solve for venue restrictions)</li>
                        <li><strong>Signature cocktails elevate everything</strong> (guests remember the experience)</li>
                        <li><strong>Professional service = stress-free hosting</strong> (you actually enjoy your event)</li>
                    </ol>
                </section>

                <!-- About Section -->
                <section class="about-section">
                    <h2>About Houston Sip Queen</h2>
                    <p>Houston Sip Queen brings <strong>luxury mobile bartending</strong> directly to your location—craft cocktails, professional service, and Southern hospitality—so you can host without worrying about bar logistics.</p>
                </section>

                <!-- Secondary CTA Section -->
                <section class="secondary-cta-section">
                    <h2>Planning an Event in the Next 30–90 Days?</h2>
                    <p>Request a quote and we'll recommend the right service level for your guest count, venue, and vibe.</p>
                    <div class="cta-buttons">
                        <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-primary">Request a Quote</a>
                        <a href="<?php echo esc_url(home_url('/book')); ?>" class="btn-secondary">Book a Consultation</a>
                    </div>
                </section>

                <!-- FAQ Section -->
                <section class="faq-section">
                    <h2>Frequently Asked Questions</h2>
                    <div class="faq-item">
                        <h3>How do I get the checklist?</h3>
                        <p>Submit the form and we'll email the download link right away.</p>
                    </div>
                    <div class="faq-item">
                        <h3>Do you serve my venue?</h3>
                        <p>We serve Houston and surrounding areas. If you're not sure, request a quote and tell us your location.</p>
                    </div>
                    <div class="faq-item">
                        <h3>Do you provide non-alcoholic options?</h3>
                        <p>Yes—mocktails and NA options are available.</p>
                    </div>
                </section>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>


