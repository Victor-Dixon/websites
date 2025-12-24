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
                    <h1 class="page-title">Request a Quote</h1>
                    <p class="page-subtitle">Tell us about your event and we'll provide a custom quote</p>
                </header>

                <div class="quote-form-section">
                    <?php
                    // Display success/error messages
                    if (isset($_GET['submitted'])) {
                        if ($_GET['submitted'] === 'success') {
                            echo '<div class="form-message form-success">';
                            echo '<p><strong>Thank you!</strong> We\'ve received your quote request and will contact you within 24 hours.</p>';
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
                        
                        <!-- Honeypot field for spam protection -->
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
                            <div class="form-group">
                                <label for="quote_phone">Phone Number *</label>
                                <input type="tel" id="quote_phone" name="quote_phone" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>Event Details</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="event_date">Event Date</label>
                                    <input type="date" id="event_date" name="event_date">
                                </div>
                                <div class="form-group">
                                    <label for="event_type">Event Type</label>
                                    <select id="event_type" name="event_type">
                                        <option value="">Select Event Type</option>
                                        <option value="wedding">Wedding</option>
                                        <option value="corporate">Corporate Event</option>
                                        <option value="birthday">Birthday Party</option>
                                        <option value="anniversary">Anniversary</option>
                                        <option value="holiday">Holiday Party</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="guest_count">Expected Guest Count</label>
                                <input type="number" id="guest_count" name="guest_count" min="1" placeholder="e.g., 50">
                            </div>
                            <div class="form-group">
                                <label for="quote_message">Tell us about your event</label>
                                <textarea id="quote_message" name="quote_message" rows="5" placeholder="Any special requirements, themes, or preferences..."></textarea>
                            </div>
                        </div>

                        <div class="form-submit">
                            <button type="submit" class="btn-primary btn-large">Submit Quote Request</button>
                            <p class="form-note">We respect your privacy. Your information will only be used to contact you about your quote request.</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>

