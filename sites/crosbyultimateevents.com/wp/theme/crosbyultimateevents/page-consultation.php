<?php

/**
 * Consultation Page Template
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">

        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page consultation-page'); ?>>

                <header class="entry-header">
                    <h1 class="page-title">Book Your Free Consultation</h1>
                    <p class="page-subtitle">Let's discuss how we can create an extraordinary experience for your event</p>
                </header>

                <div class="consultation-page-content">
                    <div class="consultation-info">
                        <h2>What to Expect</h2>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-icon">📞</div>
                                <h3>Discovery Call</h3>
                                <p>We'll discuss your event vision, preferences, and requirements</p>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">💡</div>
                                <h3>Custom Recommendations</h3>
                                <p>Receive personalized service package recommendations tailored to your needs</p>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">📋</div>
                                <h3>Detailed Proposal</h3>
                                <p>Get a comprehensive proposal with pricing and service details</p>
                            </div>
                            <div class="info-item">
                                <div class="info-icon">✨</div>
                                <h3>No Pressure</h3>
                                <p>Free consultation with no obligation to book</p>
                            </div>
                        </div>
                    </div>

                    <div class="consultation-form-section">
                        <h2>Request Your Consultation</h2>
                        <p>Fill out the form below and we'll contact you within 24 hours to schedule your free consultation.</p>

                        <?php
                        // Display success/error messages from handler redirect
                        $submitted = isset($_GET['submitted']) ? $_GET['submitted'] : '';
                        $errors = get_transient('consultation_form_errors');
                        $success = get_transient('consultation_form_success');

                        if ($submitted === 'success' || $success) {
                            delete_transient('consultation_form_success');
                            echo '<div class="form-success" style="background: #d4edda; color: #155724; padding: 1.5rem; border-radius: 5px; margin-bottom: 2rem; border-left: 4px solid #28a745;">';
                            echo '<p style="margin: 0;"><strong>✓ Thank you!</strong> We\'ve received your consultation request and will contact you within 24 hours.</p>';
                            echo '</div>';
                        }

                        if ($errors && is_array($errors)) {
                            delete_transient('consultation_form_errors');
                            echo '<div class="form-errors" style="background: #f8d7da; color: #721c24; padding: 1.5rem; border-radius: 5px; margin-bottom: 2rem; border-left: 4px solid #dc3545;">';
                            echo '<p style="margin: 0 0 0.5rem 0;"><strong>Please correct the following errors:</strong></p>';
                            echo '<ul style="margin: 0; padding-left: 1.5rem;">';
                            foreach ($errors as $error) {
                                echo '<li>' . esc_html($error) . '</li>';
                            }
                            echo '</ul></div>';
                        } elseif ($submitted === 'error') {
                            echo '<div class="form-errors" style="background: #f8d7da; color: #721c24; padding: 1.5rem; border-radius: 5px; margin-bottom: 2rem; border-left: 4px solid #dc3545;">';
                            echo '<p style="margin: 0;"><strong>Error:</strong> Failed to send message. Please try again or contact us directly.</p>';
                            echo '</div>';
                        }
                        ?>

                        <form class="consultation-form detailed-form" action="<?php echo esc_url(home_url('/consultation')); ?>" method="post">
                            <?php wp_nonce_field('consultation_request', 'consultation_nonce'); ?>

                            <!-- Honeypot spam protection (hidden from users) -->
                            <div style="position: absolute; left: -9999px;" aria-hidden="true">
                                <label for="website_url">Website URL (leave blank)</label>
                                <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="form-section">
                                <h3>Contact Information</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="first_name">First Name *</label>
                                        <input type="text" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name *</label>
                                        <input type="text" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email">Email Address *</label>
                                        <input type="email" id="email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone Number *</label>
                                        <input type="tel" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>Event Details</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="event_type">Event Type *</label>
                                        <select id="event_type" name="event_type" required>
                                            <option value="">Select Event Type</option>
                                            <option value="private-chef">Private Chef Service</option>
                                            <option value="event-planning">Event Planning</option>
                                            <option value="combined">Combined Services</option>
                                            <option value="corporate">Corporate Event</option>
                                            <option value="wedding">Wedding</option>
                                            <option value="anniversary">Anniversary</option>
                                            <option value="birthday">Birthday Celebration</option>
                                            <option value="holiday">Holiday Party</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="event_date">Preferred Event Date</label>
                                        <input type="date" id="event_date" name="event_date">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="guest_count">Expected Guest Count</label>
                                        <input type="number" id="guest_count" name="guest_count" min="1" placeholder="e.g., 20">
                                    </div>
                                    <div class="form-group">
                                        <label for="budget_range">Budget Range</label>
                                        <select id="budget_range" name="budget_range">
                                            <option value="">Select Budget Range</option>
                                            <option value="under-1000">Under $1,000</option>
                                            <option value="1000-3000">$1,000 - $3,000</option>
                                            <option value="3000-5000">$3,000 - $5,000</option>
                                            <option value="5000-10000">$5,000 - $10,000</option>
                                            <option value="over-10000">Over $10,000</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="location">Event Location</label>
                                    <input type="text" id="location" name="location" placeholder="City, State or specific venue">
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>Additional Information</h3>
                                <div class="form-group">
                                    <label for="dietary_restrictions">Dietary Restrictions or Preferences</label>
                                    <textarea id="dietary_restrictions" name="dietary_restrictions" rows="3" placeholder="e.g., vegetarian, gluten-free, allergies"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="message">Tell Us About Your Event Vision *</label>
                                    <textarea id="message" name="message" rows="5" required placeholder="Describe your event vision, style preferences, and any special requests..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="how_heard">How did you hear about us?</label>
                                    <select id="how_heard" name="how_heard">
                                        <option value="">Select One</option>
                                        <option value="google">Google Search</option>
                                        <option value="social-media">Social Media</option>
                                        <option value="referral">Referral</option>
                                        <option value="website">Website</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-submit">
                                <button type="submit" class="btn-primary btn-large btn-block">Submit Consultation Request</button>
                                <p class="form-note">We respect your privacy. Your information will only be used to contact you about your consultation request.</p>
                            </div>
                        </form>
                    </div>
                </div>

            </article>
        <?php endwhile; ?>

    </div>
</main>

<?php get_footer(); ?>