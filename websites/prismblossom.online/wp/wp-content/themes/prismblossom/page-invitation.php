<?php

/**
 * Template Name: Birthday Invitation
 * 
 * Birthday invitation page
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<style>
    /* Invitation Page Styles - Black and Gold Theme */
    body {
        background: #000000;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    .invitation-section {
        padding: 120px 0 60px;
        min-height: 100vh;
        text-align: center;
    }

    .invitation-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
    }

    .invitation-card {
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        border: 3px solid #FFD700;
        box-shadow: 0 0 30px rgba(255, 215, 0, 0.5), inset 0 0 20px rgba(255, 215, 0, 0.1);
        border-radius: 20px;
        padding: 60px 40px;
        margin-bottom: 40px;
    }

    .invitation-title {
        color: #FFD700;
        text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700, 0 0 30px rgba(255, 215, 0, 0.8);
        font-size: 3rem;
        margin-bottom: 30px;
        animation: glow 2s ease-in-out infinite;
    }

    @keyframes glow {

        0%,
        100% {
            text-shadow: 0 0 10px #FFD700, 0 0 20px #FFD700, 0 0 30px rgba(255, 215, 0, 0.8);
        }

        50% {
            text-shadow: 0 0 20px #FFD700, 0 0 40px #FFD700, 0 0 60px rgba(255, 215, 0, 0.8);
        }
    }

    .invitation-text {
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700, 0 0 10px rgba(255, 215, 0, 0.5);
        font-size: 1.3rem;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .invitation-details {
        background: rgba(0, 0, 0, 0.6);
        border: 2px solid #FFD700;
        border-radius: 15px;
        padding: 30px;
        margin: 30px 0;
    }

    .invitation-details h3 {
        color: #FFD700;
        text-shadow: 0 0 10px #FFD700;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    .invitation-details p {
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
        font-size: 1.2rem;
        margin: 10px 0;
    }

    .invitation-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 40px;
    }

    .btn-invitation {
        background: rgba(0, 0, 0, 0.6);
        border: 2px solid #FFD700;
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        padding: 15px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 1.1rem;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-invitation:hover {
        background: rgba(255, 215, 0, 0.2);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
        transform: translateY(-2px);
    }

    .invitation-emoji {
        font-size: 4rem;
        margin: 20px 0;
        animation: bounce 2s ease-in-out infinite;
        filter: drop-shadow(0 0 10px #FFD700);
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    @media (max-width: 768px) {
        .invitation-container {
            padding: 20px;
        }

        .invitation-card {
            padding: 40px 20px;
        }

        .invitation-title {
            font-size: 2rem;
        }

        .invitation-text {
            font-size: 1.1rem;
        }

        .invitation-buttons {
            flex-direction: column;
        }

        .btn-invitation {
            width: 100%;
        }
    }
</style>

<section class="invitation-section">
    <div class="container">
        <div class="invitation-container">
            <div class="invitation-card">
                <div class="invitation-emoji">🎉</div>
                <h1 class="invitation-title">You're Invited!</h1>
                <p class="invitation-text">
                    Join us for an amazing birthday celebration!
                    We can't wait to celebrate with you.
                </p>

                <div class="invitation-details">
                    <h3>Event Details</h3>
                    <?php
                    // Get event details from post meta (editable in WordPress admin)
                    $event_date = get_post_meta(get_the_ID(), '_invitation_date', true);
                    $event_time = get_post_meta(get_the_ID(), '_invitation_time', true);
                    $event_location = get_post_meta(get_the_ID(), '_invitation_location', true);
                    $event_rsvp = get_post_meta(get_the_ID(), '_invitation_rsvp', true);

                    // Default values if not set
                    $event_date = $event_date ?: 'TBD';
                    $event_time = $event_time ?: 'TBD';
                    $event_location = $event_location ?: 'TBD';
                    $event_rsvp = $event_rsvp ?: 'TBD';
                    ?>
                    <p><strong>Date:</strong> <?php echo esc_html($event_date); ?></p>
                    <p><strong>Time:</strong> <?php echo esc_html($event_time); ?></p>
                    <p><strong>Location:</strong> <?php echo esc_html($event_location); ?></p>
                    <p><strong>RSVP:</strong> <?php echo esc_html($event_rsvp); ?></p>
                </div>

                <div class="invitation-buttons">
                    <a href="#message-form" class="btn-invitation">Leave a Message</a>
                    <a href="#guestbook" class="btn-invitation">View Guestbook</a>
                    <a href="#birthday-fun" class="btn-invitation">Birthday Fun</a>
                    <a href="/carmyn" class="btn-invitation">Visit My Page</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Message Form Section -->
<section id="message-form" class="invitation-section" style="padding-top: 40px;">
    <div class="container">
        <div class="invitation-container">
            <div class="invitation-card">
                <h2 class="invitation-title" style="font-size: 2rem; margin-bottom: 30px;">Send a Message</h2>
                <p class="invitation-text" style="margin-bottom: 30px;">
                    Leave a message for Carmyn! Your message will be sent and can be viewed in the guestbook.
                </p>

                <form id="invitation-message-form" class="invitation-message-form">
                    <?php wp_nonce_field('invitation_message_submit', 'invitation_message_nonce'); ?>
                    <input type="hidden" name="nonce" id="invitation_message_nonce_field" value="<?php echo wp_create_nonce('invitation_message_submit'); ?>">

                    <div class="form-group">
                        <label for="message_name" class="form-label">Your Name</label>
                        <input type="text" id="message_name" name="message_name" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="message_text" class="form-label">Your Message</label>
                        <textarea id="message_text" name="message_text" class="form-textarea" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn-invitation" style="width: 100%; margin-top: 20px;">
                            Send Message
                        </button>
                    </div>

                    <div id="message-form-response" class="form-response" style="display: none; margin-top: 20px;"></div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    /* Message Form Styles */
    .invitation-message-form {
        max-width: 600px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        color: #FFD700;
        text-shadow: 0 0 5px #FFD700;
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 15px;
        background: rgba(0, 0, 0, 0.6);
        border: 2px solid #FFD700;
        border-radius: 10px;
        color: #FFD700;
        font-size: 1rem;
        font-family: inherit;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        transition: all 0.3s ease;
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
        background: rgba(0, 0, 0, 0.8);
    }

    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-response {
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        font-weight: bold;
    }

    .form-response.success {
        background: rgba(0, 255, 0, 0.2);
        border: 2px solid #00FF00;
        color: #00FF00;
        text-shadow: 0 0 5px #00FF00;
    }

    .form-response.error {
        background: rgba(255, 0, 0, 0.2);
        border: 2px solid #FF0000;
        color: #FF0000;
        text-shadow: 0 0 5px #FF0000;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        $('#invitation-message-form').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var responseDiv = $('#message-form-response');
            var submitButton = form.find('button[type="submit"]');

            // Disable submit button
            submitButton.prop('disabled', true).text('Sending...');
            responseDiv.hide();

            // Get form data
            var formData = {
                action: 'prismblossom_submit_invitation_message',
                nonce: $('#invitation_message_nonce_field').val(),
                message_name: $('#message_name').val(),
                message_text: $('#message_text').val()
            };

            // Submit via AJAX
            $.ajax({
                url: prismblossomAjax.ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        responseDiv.removeClass('error').addClass('success')
                            .html('✓ ' + response.data).fadeIn();
                        form[0].reset();
                    } else {
                        responseDiv.removeClass('success').addClass('error')
                            .html('✗ ' + (response.data || 'Error sending message')).fadeIn();
                    }
                },
                error: function() {
                    responseDiv.removeClass('success').addClass('error')
                        .html('✗ Network error. Please try again.').fadeIn();
                },
                complete: function() {
                    submitButton.prop('disabled', false).text('Send Message');
                }
            });
        });
    });
</script>

<?php get_footer(); ?>