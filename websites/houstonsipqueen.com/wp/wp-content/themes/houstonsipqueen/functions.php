<?php
/**
 * Houston Sip Queen Theme Functions
 * 
 * @package HoustonSipQueen
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function houstonsipqueen_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 300,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('automatic-feed-links');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'houstonsipqueen'),
        'footer' => __('Footer Menu', 'houstonsipqueen'),
    ));
}
add_action('after_setup_theme', 'houstonsipqueen_setup');

/**
 * Prevent posts from loading on front page
 * This ensures front-page.php template shows custom content, not blog posts
 */
function houstonsipqueen_prevent_posts_on_front_page($query) {
    if (!is_admin() && $query->is_main_query() && is_front_page()) {
        $query->set('post_type', 'none');
        $query->set('posts_per_page', 0);
    }
}
add_action('pre_get_posts', 'houstonsipqueen_prevent_posts_on_front_page');

/**
 * Enqueue Styles and Scripts
 */
function houstonsipqueen_scripts() {
    // Enqueue theme stylesheet
    wp_enqueue_style('houstonsipqueen-style', get_stylesheet_uri(), array(), '1.0.0');

    // Enqueue theme JavaScript
    wp_enqueue_script('houstonsipqueen-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'houstonsipqueen_scripts');

/**
 * Register Widget Areas
 */
function houstonsipqueen_widgets_init() {
    register_sidebar(array(
        'name' => __('Footer Widget Area', 'houstonsipqueen'),
        'id' => 'footer-1',
        'description' => __('Add widgets here to appear in your footer.', 'houstonsipqueen'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'houstonsipqueen_widgets_init');

/**
 * Create Quote Page if it doesn't exist
 */
function houstonsipqueen_create_quote_page() {
    if (get_page_by_path('quote')) {
        return;
    }
    
    $quote_page = array(
        'post_title' => 'Request a Quote',
        'post_name' => 'quote',
        'post_status' => 'publish',
        'post_type' => 'page',
        'page_template' => 'page-quote.php'
    );
    wp_insert_post($quote_page);
}
add_action('after_switch_theme', 'houstonsipqueen_create_quote_page');

/**
 * Handle Quote Form Submission
 */
function houstonsipqueen_handle_quote_form() {
    if (!is_page('quote')) {
        return;
    }

    if (!isset($_POST['quote_nonce']) || !wp_verify_nonce($_POST['quote_nonce'], 'quote_form')) {
        return;
    }

    // Honeypot spam protection
    if (!empty($_POST['website_url'])) {
        return; // Spam detected
    }

    // Sanitize and validate input
    $name = sanitize_text_field($_POST['quote_name'] ?? '');
    $email = sanitize_email($_POST['quote_email'] ?? '');
    $phone = sanitize_text_field($_POST['quote_phone'] ?? '');
    $event_date = sanitize_text_field($_POST['event_date'] ?? '');
    $event_type = sanitize_text_field($_POST['event_type'] ?? '');
    $guest_count = sanitize_text_field($_POST['guest_count'] ?? '');
    $message = sanitize_textarea_field($_POST['quote_message'] ?? '');

    // Validation
    $errors = array();
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email) || !is_email($email)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }

    // If validation fails, store errors in transient
    if (!empty($errors)) {
        set_transient('quote_form_errors', $errors, 30);
        return;
    }

    // Email settings
    $to = get_option('admin_email');
    $email_subject = 'New Quote Request from Houston Sip Queen';

    // Build email message
    $email_message = "New quote request from houstonsipqueen.com\n\n";
    $email_message .= "Name: {$name}\n";
    $email_message .= "Email: {$email}\n";
    $email_message .= "Phone: {$phone}\n";
    if (!empty($event_date)) {
        $email_message .= "Event Date: {$event_date}\n";
    }
    if (!empty($event_type)) {
        $email_message .= "Event Type: {$event_type}\n";
    }
    if (!empty($guest_count)) {
        $email_message .= "Guest Count: {$guest_count}\n";
    }
    if (!empty($message)) {
        $email_message .= "\nMessage:\n{$message}\n";
    }
    $email_message .= "\n---\n";
    $email_message .= "Submitted: " . date('F j, Y, g:i a') . "\n";
    $email_message .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";

    // Email headers
    $headers = array(
        'From: ' . $name . ' <' . $email . '>',
        'Reply-To: ' . $email,
        'Content-Type: text/plain; charset=UTF-8'
    );

    // Send email
    $sent = wp_mail($to, $email_subject, $email_message, $headers);

    // Store result in transient for display
    if ($sent) {
        set_transient('quote_form_success', true, 30);
        // Optional: Send auto-reply to user
        $auto_reply_subject = 'Thank you for your quote request - Houston Sip Queen';
        $auto_reply_message = "Dear {$name},\n\n";
        $auto_reply_message .= "Thank you for requesting a quote from Houston Sip Queen. We've received your request and will get back to you within 24 hours.\n\n";
        $auto_reply_message .= "Best regards,\nHouston Sip Queen Team";
        wp_mail($email, $auto_reply_subject, $auto_reply_message);
    } else {
        set_transient('quote_form_errors', array('Failed to send message. Please try again or contact us directly.'), 30);
    }

    // Redirect to prevent form resubmission
    wp_safe_redirect(add_query_arg('submitted', $sent ? 'success' : 'error', get_permalink()));
    exit;
}
add_action('template_redirect', 'houstonsipqueen_handle_quote_form');

/**
 * Handle Lead Magnet Form Submission (Event Planning Guide)
 */
function houstonsipqueen_handle_lead_magnet_form() {
    if (!is_page('event-planning-guide')) {
        return;
    }

    if (!isset($_POST['lead_magnet_nonce']) || !wp_verify_nonce($_POST['lead_magnet_nonce'], 'lead_magnet_form')) {
        return;
    }

    // Honeypot spam protection
    if (!empty($_POST['website_url'])) {
        return; // Spam detected
    }

    // Sanitize and validate input
    $first_name = sanitize_text_field($_POST['lead_first_name'] ?? '');
    $email = sanitize_email($_POST['lead_email'] ?? '');
    $event_date = sanitize_text_field($_POST['lead_event_date'] ?? '');

    // Validation
    $errors = array();
    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }
    if (empty($email) || !is_email($email)) {
        $errors[] = 'Valid email is required.';
    }

    // If validation fails, store errors in transient
    if (!empty($errors)) {
        set_transient('lead_magnet_form_errors', $errors, 30);
        return;
    }

    // Email service integration (Mailchimp/ConvertKit)
    $email_service = get_option('hsq_email_service', 'mailchimp'); // 'mailchimp' or 'convertkit'
    $email_api_key = get_option('hsq_email_api_key', '');
    $email_list_id = get_option('hsq_email_list_id', '');

    // Add to email service if configured
    if (!empty($email_api_key) && !empty($email_list_id)) {
        houstonsipqueen_add_to_email_service($email, $first_name, $event_date, $email_service, $email_api_key, $email_list_id);
    }

    // Send lead magnet email with download link
    $to = $email;
    $subject = 'Your Event Bar Planning Checklist - Houston Sip Queen';
    $download_url = home_url('/wp-content/uploads/event-bar-planning-checklist.pdf');
    
    $message = "Hi {$first_name},\n\n";
    $message .= "Thank you for requesting the Ultimate Event Bar Planning Checklist!\n\n";
    $message .= "Download your free checklist here:\n";
    $message .= "{$download_url}\n\n";
    $message .= "This comprehensive guide includes:\n";
    $message .= "- Step-by-step planning timeline\n";
    $message .= "- Beverage quantity guidance\n";
    $message .= "- Staffing and setup recommendations\n";
    $message .= "- Day-of event checklist\n\n";
    $message .= "Need help planning your event bar?\n";
    $message .= "Request a free quote: " . home_url('/quote') . "\n";
    $message .= "Book a consultation: " . home_url('/book') . "\n\n";
    $message .= "Best regards,\n";
    $message .= "Houston Sip Queen Team\n\n";
    $message .= "---\n";
    $message .= "Houston Sip Queen - Luxury Mobile Bartending\n";
    $message .= "Bringing craft cocktails and professional service to your event";

    $headers = array(
        'From: Houston Sip Queen <' . get_option('admin_email') . '>',
        'Reply-To: ' . get_option('admin_email'),
        'Content-Type: text/plain; charset=UTF-8'
    );

    $sent = wp_mail($to, $subject, $message, $headers);

    // Track conversion in analytics
    if (function_exists('add_analytics_tracking_codes')) {
        // Event will be tracked via JavaScript on thank-you page
    }

    // Redirect to thank-you page
    if ($sent) {
        wp_safe_redirect(home_url('/event-planning-guide/thank-you'));
        exit;
    } else {
        set_transient('lead_magnet_form_errors', array('Failed to send email. Please try again.'), 30);
    }
}
add_action('template_redirect', 'houstonsipqueen_handle_lead_magnet_form');

/**
 * Add subscriber to email service (Mailchimp/ConvertKit)
 */
function houstonsipqueen_add_to_email_service($email, $first_name, $event_date, $service, $api_key, $list_id) {
    if (empty($api_key) || empty($list_id)) {
        return false;
    }

    $data = array(
        'email' => $email,
        'first_name' => $first_name,
        'tags' => array('lead_magnet_event_bar_checklist'),
    );

    if (!empty($event_date)) {
        $data['event_date'] = $event_date;
    }

    if ($service === 'mailchimp') {
        // Mailchimp API integration
        $dc = substr($api_key, strpos($api_key, '-') + 1); // Extract datacenter
        $url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$list_id}/members";
        
        $body = json_encode(array(
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => array(
                'FNAME' => $first_name,
            ),
            'tags' => array('lead_magnet_event_bar_checklist'),
        ));

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode('apikey:' . $api_key),
                'Content-Type' => 'application/json',
            ),
            'body' => $body,
            'timeout' => 15,
        ));

        return !is_wp_error($response);
    } elseif ($service === 'convertkit') {
        // ConvertKit API integration
        $url = "https://api.convertkit.com/v3/forms/{$list_id}/subscribe";
        
        $body = array(
            'api_key' => $api_key,
            'email' => $email,
            'first_name' => $first_name,
            'tags' => array('lead_magnet_event_bar_checklist'),
        );

        $response = wp_remote_post($url, array(
            'body' => $body,
            'timeout' => 15,
        ));

        return !is_wp_error($response);
    }

    return false;
}

/**
 * Create Lead Magnet Pages if they don't exist
 */
function houstonsipqueen_create_lead_magnet_pages() {
    // Create Event Planning Guide landing page
    if (!get_page_by_path('event-planning-guide')) {
        $landing_page = array(
            'post_title' => 'Event Bar Planning Guide',
            'post_name' => 'event-planning-guide',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-event-planning-guide.php'
        );
        wp_insert_post($landing_page);
    }
    
    // Create Thank-You page
    if (!get_page_by_path('event-planning-guide/thank-you')) {
        $thank_you_page = array(
            'post_title' => 'Thank You - Event Planning Guide',
            'post_name' => 'thank-you',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_parent' => get_page_by_path('event-planning-guide')->ID ?? 0,
            'page_template' => 'page-thank-you-guide.php'
        );
        wp_insert_post($thank_you_page);
    }
}
add_action('after_switch_theme', 'houstonsipqueen_create_lead_magnet_pages');

/**
 * Add Calendly Booking Widget
 */
function houstonsipqueen_add_calendly_widget($calendly_url = '') {
    if (empty($calendly_url)) {
        $calendly_url = get_option('hsq_calendly_url', '');
    }
    
    if (empty($calendly_url)) {
        return;
    }
    
    ?>
    <!-- Calendly inline widget -->
    <div class="calendly-inline-widget" data-url="<?php echo esc_url($calendly_url); ?>" style="min-width:320px;height:630px;"></div>
    <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
    <?php
}

/**
 * Add Calendly popup button
 */
function houstonsipqueen_calendly_button($text = 'Book a Consultation', $calendly_url = '') {
    if (empty($calendly_url)) {
        $calendly_url = get_option('hsq_calendly_url', '');
    }
    
    if (empty($calendly_url)) {
        return;
    }
    
    ?>
    <a href="<?php echo esc_url($calendly_url); ?>" 
       class="btn-calendly" 
       onclick="Calendly.initPopupWidget({url: '<?php echo esc_js($calendly_url); ?>'});return false;">
        <?php echo esc_html($text); ?>
    </a>
    <script type="text/javascript" src="https://assets.calendly.com/assets/external/widget.js" async></script>
    <?php
}

/**
 * Stripe Payment Processing for Deposits
 */
function houstonsipqueen_stripe_payment_form($amount = 0, $description = 'Event Deposit', $success_url = '', $cancel_url = '') {
    $stripe_publishable_key = get_option('hsq_stripe_publishable_key', '');
    
    if (empty($stripe_publishable_key)) {
        return '<p>Payment processing not configured. Please contact us directly.</p>';
    }
    
    if (empty($success_url)) {
        $success_url = home_url('/booking-confirmation');
    }
    if (empty($cancel_url)) {
        $cancel_url = home_url('/quote');
    }
    
    $amount_cents = intval($amount * 100); // Convert to cents
    
    ?>
    <form id="stripe-payment-form" class="stripe-payment-form">
        <div class="form-group">
            <label for="card-element">Credit or Debit Card</label>
            <div id="card-element" class="stripe-card-element">
                <!-- Stripe Elements will create form elements here -->
            </div>
            <div id="card-errors" role="alert" class="stripe-card-errors"></div>
        </div>
        
        <div class="payment-summary">
            <p><strong>Amount:</strong> $<?php echo number_format($amount, 2); ?></p>
            <p><strong>Description:</strong> <?php echo esc_html($description); ?></p>
        </div>
        
        <button type="submit" id="submit-payment" class="btn-primary btn-large">
            Pay Deposit
        </button>
    </form>
    
    <script src="https://js.stripe.com/v3/"></script>
    <script>
    var stripe = Stripe('<?php echo esc_js($stripe_publishable_key); ?>');
    var elements = stripe.elements();
    
    var cardElement = elements.create('card');
    cardElement.mount('#card-element');
    
    var form = document.getElementById('stripe-payment-form');
    var submitButton = document.getElementById('submit-payment');
    
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        
        stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        }).then(function(result) {
            if (result.error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
                submitButton.disabled = false;
                submitButton.textContent = 'Pay Deposit';
            } else {
                // Send payment method to server for processing
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'hsq_process_stripe_payment',
                        payment_method_id: result.paymentMethod.id,
                        amount: <?php echo $amount_cents; ?>,
                        description: '<?php echo esc_js($description); ?>',
                        nonce: '<?php echo wp_create_nonce('stripe_payment_nonce'); ?>'
                    })
                }).then(function(response) {
                    return response.json();
                }).then(function(data) {
                    if (data.success) {
                        window.location.href = '<?php echo esc_url($success_url); ?>';
                    } else {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = data.message || 'Payment failed. Please try again.';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Pay Deposit';
                    }
                });
            }
        });
    });
    </script>
    <?php
}

/**
 * Process Stripe Payment (AJAX handler)
 */
function houstonsipqueen_process_stripe_payment() {
    check_ajax_referer('stripe_payment_nonce', 'nonce');
    
    $stripe_secret_key = get_option('hsq_stripe_secret_key', '');
    
    if (empty($stripe_secret_key)) {
        wp_send_json_error(array('message' => 'Payment processing not configured.'));
        return;
    }
    
    $payment_method_id = sanitize_text_field($_POST['payment_method_id'] ?? '');
    $amount = intval($_POST['amount'] ?? 0);
    $description = sanitize_text_field($_POST['description'] ?? 'Event Deposit');
    
    if (empty($payment_method_id) || $amount <= 0) {
        wp_send_json_error(array('message' => 'Invalid payment details.'));
        return;
    }
    
    // Process payment via Stripe API
    // Note: This requires Stripe PHP SDK or cURL
    // For production, use Stripe PHP SDK: composer require stripe/stripe-php
    
    $response = wp_remote_post('https://api.stripe.com/v1/payment_intents', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $stripe_secret_key,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ),
        'body' => array(
            'amount' => $amount,
            'currency' => 'usd',
            'payment_method' => $payment_method_id,
            'confirmation_method' => 'manual',
            'confirm' => 'true',
            'description' => $description,
        ),
        'timeout' => 15,
    ));
    
    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => 'Payment processing error.'));
        return;
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (isset($body['status']) && $body['status'] === 'succeeded') {
        // Payment successful - save to database, send confirmation email, etc.
        wp_send_json_success(array(
            'message' => 'Payment processed successfully.',
            'payment_intent_id' => $body['id'] ?? '',
        ));
    } else {
        wp_send_json_error(array('message' => $body['error']['message'] ?? 'Payment failed.'));
    }
}
add_action('wp_ajax_hsq_process_stripe_payment', 'houstonsipqueen_process_stripe_payment');
add_action('wp_ajax_nopriv_hsq_process_stripe_payment', 'houstonsipqueen_process_stripe_payment');


/**
 * Add Google Analytics 4 and Facebook Pixel tracking codes
 * 
 * IMPORTANT: Replace placeholder IDs with actual tracking IDs:
 * - GA4: Get from Google Analytics 4 property
 * - Facebook Pixel: Get from Facebook Business Manager
 * 
 * To configure:
 * 1. Get GA4 property ID (format: G-XXXXXXXXXX)
 * 2. Get Facebook Pixel ID (numeric ID)
 * 3. Replace placeholders below or use WordPress options
 */
function add_analytics_tracking_codes() {
    // Get tracking IDs from WordPress options or use placeholders
    $ga4_id = get_option('hsq_ga4_id', 'G-XXXXXXXXXX'); // TODO: Replace with actual GA4 ID
    $fb_pixel_id = get_option('hsq_fb_pixel_id', 'YOUR_PIXEL_ID'); // TODO: Replace with actual Pixel ID
    
    // Only output if IDs are configured (not placeholders)
    if ($ga4_id === 'G-XXXXXXXXXX' && $fb_pixel_id === 'YOUR_PIXEL_ID') {
        return; // Skip if not configured
    }
    
    // Google Analytics 4 (GA4)
    if ($ga4_id !== 'G-XXXXXXXXXX') {
        ?>
        <!-- Google Analytics 4 (GA4) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js($ga4_id); ?>"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($ga4_id); ?>', {
            'page_path': window.location.pathname,
            'page_title': document.title,
        });
        // Custom Events Tracking
        // Track lead_magnet_submit event
        gtag("event", "lead_magnet_submit", {
            "event_category": "engagement",
            "event_label": "lead_magnet_submit"
        });
        // Track quote_form_submit event
        gtag("event", "quote_form_submit", {
            "event_category": "engagement",
            "event_label": "quote_form_submit"
        });
        // Track booking_click event
        gtag("event", "booking_click", {
            "event_category": "engagement",
            "event_label": "booking_click"
        });
        // Track phone_click event
        gtag("event", "phone_click", {
            "event_category": "engagement",
            "event_label": "phone_click"
        });
        </script>
        <!-- End GA4 -->
        <?php
    }
    
    // Facebook Pixel
    if ($fb_pixel_id !== 'YOUR_PIXEL_ID') {
        ?>
        <!-- Facebook Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo esc_js($fb_pixel_id); ?>');
        fbq('track', 'PageView');
        </script>
        <noscript>
        <img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?php echo esc_attr($fb_pixel_id); ?>&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
        <?php
    }
}
add_action('wp_head', 'add_analytics_tracking_codes', 99);


/**
 * Enhanced Template Loading Fix
 * Ensures page templates load correctly and handles cache clearing
 * Priority 999 ensures this runs before most other template filters
 * 
 * Applied: 2025-12-23
 */
add_filter('template_include', function ($template) {
    // Skip admin and AJAX requests
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return $template;
    }
    
    // Get the page slug from URL or post object
    $page_slug = null;
    
    if (is_page()) {
        global $post;
        if ($post && isset($post->post_name)) {
            $page_slug = $post->post_name;
        }
    }
    
    // Fallback: Check URL directly
    if (!$page_slug && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $request_parts = explode('/', $request_uri);
        $page_slug = end($request_parts);
    }
    
    // Map page slugs to templates
    $page_templates = array(
        'quote' => 'page-quote.php',
    );
    
    if ($page_slug && isset($page_templates[$page_slug])) {
        $custom_template = locate_template($page_templates[$page_slug]);
        
        if ($custom_template && file_exists($custom_template)) {
            // If page exists but template isn't set, update it
            if (is_page()) {
                global $post;
                $current_template = get_page_template_slug($post->ID);
                if ($current_template !== $page_templates[$page_slug]) {
                    update_post_meta($post->ID, '_wp_page_template', $page_templates[$page_slug]);
                }
            }
            
            return $custom_template;
        }
    }
    
    // Handle 404 cases (fallback for pages that don't exist yet)
    if (is_404() && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $request_parts = explode('/', $request_uri);
        $uri_slug = end($request_parts);
        
        if (isset($page_templates[$uri_slug])) {
            $new_template = locate_template($page_templates[$uri_slug]);
            if ($new_template && file_exists($new_template)) {
                // Set up WordPress query to treat this as a page
                global $wp_query;
                $wp_query->is_404 = false;
                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                $wp_query->queried_object = (object) array(
                    'post_type' => 'page',
                    'post_name' => $uri_slug,
                );
                return $new_template;
            }
        }
    }
    
    return $template;
}, 999);

/**
 * Clear cache when theme is activated or updated
 * This helps ensure template changes take effect immediately
 */
function clear_template_cache_on_theme_change() {
    // Clear object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Clear LiteSpeed Cache if active
    if (class_exists('LiteSpeed_Cache') && method_exists('LiteSpeed_Cache', 'purge_all')) {
        LiteSpeed_Cache::purge_all();
    }
    
    // Clear rewrite rules to ensure permalinks work
    flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'clear_template_cache_on_theme_change');

