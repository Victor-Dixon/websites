<?php

/**
 * Contact Page Template
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="content-area">

        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page contact-page'); ?>>

                <header class="entry-header">
                    <h1 class="page-title">Get In Touch</h1>
                    <p class="page-subtitle">We'd love to hear from you and discuss how we can make your event extraordinary</p>
                </header>

                <div class="contact-page-content">
                    <div class="contact-info-grid">
                        <!-- Contact Information -->
                        <div class="contact-info-section">
                            <h2>Contact Information</h2>
                            <div class="contact-details">
                                <div class="contact-item">
                                    <div class="contact-icon">📧</div>
                                    <div>
                                        <h3>Email</h3>
                                        <p><a href="mailto:info@crosbyultimateevents.com">info@crosbyultimateevents.com</a></p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">📞</div>
                                    <div>
                                        <h3>Phone</h3>
                                        <p><a href="tel:+19175550198">(917) 555-0198</a></p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">📍</div>
                                    <div>
                                        <h3>Service Area</h3>
                                        <p>New York City, The Hamptons, & Tri-State Area</p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">⏰</div>
                                    <div>
                                        <h3>Hours</h3>
                                        <p>Mon-Sun: 9:00 AM - 8:00 PM</p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">⏰</div>
                                    <div>
                                        <h3>Response Time</h3>
                                        <p>We typically respond within 24 hours</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="contact-form-section">
                            <h2>Send Us a Message</h2>
                            <p>Fill out the form below and we'll get back to you as soon as possible.</p>

                            <?php
                            // Display success/error messages
                            $submitted = isset($_GET['submitted']) ? $_GET['submitted'] : '';
                            $errors = get_transient('contact_form_errors');
                            $success = get_transient('contact_form_success');

                            if ($submitted === 'success' || $success) {
                                delete_transient('contact_form_success');
                                echo '<div class="form-success" style="background: #d4edda; color: #155724; padding: 1.5rem; border-radius: 5px; margin-bottom: 2rem; border-left: 4px solid #28a745;">';
                                echo '<p style="margin: 0;"><strong>✓ Thank you for your message!</strong> We\'ve received your inquiry and will respond within 24 hours.</p>';
                                echo '</div>';
                            }

                            if ($errors && is_array($errors)) {
                                delete_transient('contact_form_errors');
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

                            <form class="contact-form" action="<?php echo esc_url(get_permalink()); ?>" method="post">
                                <?php wp_nonce_field('contact_form', 'contact_nonce'); ?>

                                <!-- Honeypot spam protection (hidden from users) -->
                                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                                    <label for="website_url">Website URL (leave blank)</label>
                                    <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact_name">Name *</label>
                                        <input type="text" id="contact_name" name="contact_name" value="<?php echo isset($_POST['contact_name']) ? esc_attr($_POST['contact_name']) : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_email">Email *</label>
                                        <input type="email" id="contact_email" name="contact_email" value="<?php echo isset($_POST['contact_email']) ? esc_attr($_POST['contact_email']) : ''; ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact_phone">Phone</label>
                                        <input type="tel" id="contact_phone" name="contact_phone" value="<?php echo isset($_POST['contact_phone']) ? esc_attr($_POST['contact_phone']) : ''; ?>" placeholder="(555) 123-4567">
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_subject">Subject *</label>
                                        <select id="contact_subject" name="contact_subject" required>
                                            <option value="">Select Subject</option>
                                            <option value="general" <?php selected(isset($_POST['contact_subject']) && $_POST['contact_subject'] === 'general'); ?>>General Inquiry</option>
                                            <option value="private-chef" <?php selected(isset($_POST['contact_subject']) && $_POST['contact_subject'] === 'private-chef'); ?>>Private Chef Services</option>
                                            <option value="event-planning" <?php selected(isset($_POST['contact_subject']) && $_POST['contact_subject'] === 'event-planning'); ?>>Event Planning</option>
                                            <option value="combined" <?php selected(isset($_POST['contact_subject']) && $_POST['contact_subject'] === 'combined'); ?>>Combined Services</option>
                                            <option value="quote" <?php selected(isset($_POST['contact_subject']) && $_POST['contact_subject'] === 'quote'); ?>>Request Quote</option>
                                            <option value="other" <?php selected(isset($_POST['contact_subject']) && $_POST['contact_subject'] === 'other'); ?>>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contact_message">Message *</label>
                                    <textarea id="contact_message" name="contact_message" rows="6" required placeholder="Tell us about your event or inquiry..."><?php echo isset($_POST['contact_message']) ? esc_textarea($_POST['contact_message']) : ''; ?></textarea>
                                </div>

                                <div class="form-submit">
                                    <button type="submit" class="btn-primary btn-large btn-block">Send Message</button>
                                    <p class="form-note">We respect your privacy. Your information will only be used to respond to your inquiry.</p>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Additional CTA -->
                    <section class="contact-cta">
                        <div class="cta-content">
                            <h2>Prefer to Schedule a Consultation?</h2>
                            <p>Book a free consultation call to discuss your event in detail.</p>
                            <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="btn-primary btn-large">Book Free Consultation</a>
                        </div>
                    </section>
                </div>

            </article>
        <?php endwhile; ?>

    </div>
</main>

<?php get_footer(); ?>