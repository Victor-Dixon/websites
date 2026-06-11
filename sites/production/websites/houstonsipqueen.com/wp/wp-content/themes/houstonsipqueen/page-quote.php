<?php
/**
 * Quote Request Page Template
 * 
 * @package HoustonSipQueen
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">
        <section class="quote-page">
            <div class="container">
                <header class="entry-header">
                    <h1 class="page-title">Book Your Private Event</h1>
                    <p class="page-subtitle">Share your event details and we'll respond with package options and a custom quote within 24 hours.</p>
                </header>

                <div class="quote-form-section">
                    <?php
                    if (isset($_GET['submitted'])) {
                        if ($_GET['submitted'] === 'success') {
                            echo '<div class="form-message form-success">';
                            echo '<p><strong>Thank you!</strong> We\'ve received your booking request and will contact you within 24 hours.</p>';
                            echo '</div>';
                        } else {
                            $errors = get_transient('quote_form_errors');
                            if ($errors) {
                                echo '<div class="form-message form-error">';
                                echo '<ul>';
                                foreach ($errors as $error) {
                                    echo '<li>' . esc_html($error) . '</li>';
                                }
                                echo '</ul>';
                                echo '</div>';
                                delete_transient('quote_form_errors');
                            }
                        }
                    }
                    ?>

                    <form class="quote-form" method="post" action="<?php echo esc_url(get_permalink()); ?>">
                        <?php wp_nonce_field('quote_form', 'quote_nonce'); ?>
                        <input type="text" name="website_url" value="" style="display: none;" tabindex="-1" autocomplete="off">

                        <div class="form-section">
                            <h3>Contact Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="quote_name">Name *</label>
                                    <input type="text" id="quote_name" name="quote_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="quote_email">Email *</label>
                                    <input type="email" id="quote_email" name="quote_email" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="quote_phone">Phone *</label>
                                    <input type="tel" id="quote_phone" name="quote_phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="instagram_handle">Instagram Handle</label>
                                    <input type="text" id="instagram_handle" name="instagram_handle" placeholder="@houston_sipqueen">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>Event Details</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="event_date">Event Date *</label>
                                    <input type="date" id="event_date" name="event_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="event_location">Location / ZIP *</label>
                                    <input type="text" id="event_location" name="event_location" placeholder="Venue name or Houston ZIP" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="guest_count">Guest Count *</label>
                                    <input type="number" id="guest_count" name="guest_count" min="1" placeholder="e.g., 50" required>
                                </div>
                                <div class="form-group">
                                    <label for="event_type">Event Type *</label>
                                    <select id="event_type" name="event_type" required>
                                        <option value="">Select Event Type</option>
                                        <option value="wedding">Wedding</option>
                                        <option value="corporate">Corporate Event</option>
                                        <option value="birthday">Birthday Party</option>
                                        <option value="anniversary">Anniversary</option>
                                        <option value="bridal-shower">Bridal Shower</option>
                                        <option value="holiday">Holiday Party</option>
                                        <option value="private">Private Party</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="service_hours">Service Hours *</label>
                                    <select id="service_hours" name="service_hours" required>
                                        <option value="">Select Hours</option>
                                        <option value="2">Up to 2 hours</option>
                                        <option value="3">Up to 3 hours</option>
                                        <option value="4">Up to 4 hours</option>
                                        <option value="5">Up to 5 hours</option>
                                        <option value="6">Up to 6 hours</option>
                                        <option value="6+">6+ hours</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="budget_range">Budget Range</label>
                                    <select id="budget_range" name="budget_range">
                                        <option value="">Select Budget Range</option>
                                        <option value="175-250">$175&ndash;$250 (Queen Pour)</option>
                                        <option value="300-450">$300&ndash;$450 (Sip Queen Experience)</option>
                                        <option value="600+">$600+ (Royal Event Bar)</option>
                                        <option value="unsure">Not sure yet</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>Bar &amp; Menu Preferences</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="alcohol_provided">Alcohol Provided by Host? *</label>
                                    <select id="alcohol_provided" name="alcohol_provided" required>
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No / Not yet</option>
                                        <option value="unsure">Need guidance</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="need_mocktails">Need Mocktails? *</label>
                                    <select id="need_mocktails" name="need_mocktails" required>
                                        <option value="">Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="need_signature_menu">Need Signature Menu? *</label>
                                <select id="need_signature_menu" name="need_signature_menu" required>
                                    <option value="">Select</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                    <option value="unsure">Not sure yet</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="quote_message">Additional Details</label>
                                <textarea id="quote_message" name="quote_message" rows="4" placeholder="Theme, venue notes, cocktail preferences, or timing details..."></textarea>
                            </div>
                        </div>

                        <div class="form-submit">
                            <?php houstonsipqueen_alcohol_policy_notice(); ?>
                            <button type="submit" class="btn-primary btn-large">Submit Booking Request</button>
                            <p class="form-note">We respect your privacy. Your information is used only to respond to your booking request.</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
