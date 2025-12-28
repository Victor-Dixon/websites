<?php

/**
 * Crosby Ultimate Events Theme Functions
 * 
 * @package CrosbyUltimateEvents
 * @since 1.0.0
 */

/**
 * Theme Setup
 */
function crosbyultimateevents_setup()
{
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
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'crosbyultimateevents'),
        'footer' => __('Footer Menu', 'crosbyultimateevents'),
    ));

    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'crosbyultimateevents_setup');

/**
 * Enqueue Styles and Scripts
 */
function crosbyultimateevents_scripts()
{
    // Enqueue theme stylesheet
    wp_enqueue_style('crosbyultimateevents-style', get_stylesheet_uri(), array(), '1.0.0');

    // Enqueue theme JavaScript
    wp_enqueue_script('crosbyultimateevents-portfolio-filter', get_template_directory_uri() . '/js/portfolio-filter.js', array(), '1.0.0', true);
    
    // Add critical text rendering fix - loads after all plugins
    wp_add_inline_style('crosbyultimateevents-style', '
        /* CRITICAL FIX: Text rendering issues - ensure proper word spacing */
        * {
            word-spacing: normal !important;
        }
        body, p, span, div, a, li, h1, h2, h3, h4, h5, h6, 
        td, th, label, input, textarea, select, button, 
        .site-title, .main-navigation, .hero-content, 
        .value-item, .service-card, .lead-capture-content {
            word-spacing: normal !important;
            letter-spacing: normal !important;
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }
        /* Override any negative word-spacing from plugins */
        .main-navigation a {
            word-spacing: normal !important;
        }
        .btn-primary, .btn-secondary, .btn-outline {
            word-spacing: normal !important;
        }
    ');
}
add_action('wp_enqueue_scripts', 'crosbyultimateevents_scripts', 999); // High priority to load after plugins

/**
 * Register Widget Areas
 */
function crosbyultimateevents_widgets_init()
{
    register_sidebar(array(
        'name' => __('Sidebar', 'crosbyultimateevents'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'crosbyultimateevents'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));

    register_sidebar(array(
        'name' => __('Footer Widget Area', 'crosbyultimateevents'),
        'id' => 'footer-1',
        'description' => __('Add widgets here to appear in your footer.', 'crosbyultimateevents'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'crosbyultimateevents_widgets_init');

/**
 * Custom Excerpt Length
 */
function crosbyultimateevents_excerpt_length($length)
{
    return 30;
}
add_filter('excerpt_length', 'crosbyultimateevents_excerpt_length');

/**
 * Custom Excerpt More
 */
function crosbyultimateevents_excerpt_more($more)
{
    return '...';
}
add_filter('excerpt_more', 'crosbyultimateevents_excerpt_more');

/**
 * Add Custom Post Types (Optional - for services, events, etc.)
 */
function crosbyultimateevents_custom_post_types()
{
    // Services Post Type
    register_post_type('service', array(
        'labels' => array(
            'name' => __('Services', 'crosbyultimateevents'),
            'singular_name' => __('Service', 'crosbyultimateevents'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-food',
    ));

    // Events Post Type
    register_post_type('event', array(
        'labels' => array(
            'name' => __('Events', 'crosbyultimateevents'),
            'singular_name' => __('Event', 'crosbyultimateevents'),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-calendar-alt',
    ));
}
add_action('init', 'crosbyultimateevents_custom_post_types');


/**
 * Default menu fallback (used by wp_nav_menu fallback_cb).
 */
function crosbyultimateevents_default_menu()
{
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
    echo '<li><a href="' . esc_url(home_url('/services')) . '">Services</a></li>';
    echo '<li><a href="' . esc_url(home_url('/portfolio')) . '">Portfolio</a></li>';
    echo '<li><a href="' . esc_url(home_url('/blog')) . '">Blog</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">Contact</a></li>';
    echo '</ul>';
}

/**
 * Create/ensure key pages exist and have correct templates.
 *
 * Note: WordPress stores page templates in post meta `_wp_page_template`;
 * passing `page_template` into `wp_insert_post()` is ignored.
 */
function crosbyultimateevents_ensure_page(string $slug, string $title, string $template = 'default'): int
{
    $existing = get_page_by_path($slug);
    if ($existing instanceof WP_Post) {
        // Ensure template is correct
        $current_template = (string) get_post_meta($existing->ID, '_wp_page_template', true);
        if ($template && $current_template !== $template) {
            update_post_meta($existing->ID, '_wp_page_template', $template);
        }
        return (int) $existing->ID;
    }

    $page_id = wp_insert_post(array(
        'post_title'  => $title,
        'post_name'   => $slug,
        'post_status' => 'publish',
        'post_type'   => 'page',
    ));

    if (!is_wp_error($page_id) && $template) {
        update_post_meta((int) $page_id, '_wp_page_template', $template);
    }

    return is_wp_error($page_id) ? 0 : (int) $page_id;
}

/**
 * Ensure core site pages exist on theme activation.
 */
function crosbyultimateevents_create_core_pages(): void
{
    crosbyultimateevents_ensure_page('consultation', 'Consultation', 'page-consultation.php');
    crosbyultimateevents_ensure_page('services', 'Services', 'page-services.php');
    crosbyultimateevents_ensure_page('portfolio', 'Portfolio', 'page-portfolio.php');
    crosbyultimateevents_ensure_page('contact', 'Contact', 'page-contact.php');
    crosbyultimateevents_ensure_page('blog', 'Blog', 'page-blog.php');
}
add_action('after_switch_theme', 'crosbyultimateevents_create_core_pages');

/**
 * Handle Contact Form Submission
 */
function crosbyultimateevents_handle_contact_form()
{
    // Process on contact page or consultation page
    if (!is_page('contact') && !is_page('consultation')) {
        return;
    }

    // Check if form was submitted (contact form or consultation form)
    $is_contact_form = isset($_POST['contact_nonce']);
    $is_consultation_form = isset($_POST['consultation_nonce']);

    if (!$is_contact_form && !$is_consultation_form) {
        return;
    }

    // Nonce verification:
    // - Contact uses: contact_form
    // - Consultation can originate from the consultation page OR the front page lead form.
    $nonce_valid = false;
    if ($is_contact_form) {
        $nonce_valid = wp_verify_nonce($_POST['contact_nonce'], 'contact_form');
    } else {
        $nonce_value = $_POST['consultation_nonce'] ?? '';
        $nonce_valid = wp_verify_nonce($nonce_value, 'consultation_request') || wp_verify_nonce($nonce_value, 'front_page_consultation');
    }

    if (!$nonce_valid) {
        return;
    }

    // Honeypot spam protection
    if (!empty($_POST['website_url'])) {
        return; // Spam detected
    }

    // Sanitize and validate input (handle both contact and consultation forms)
    $name = '';
    if (!empty($_POST['contact_name'])) {
        $name = sanitize_text_field($_POST['contact_name']);
    } elseif (!empty($_POST['name'])) {
        $name = sanitize_text_field($_POST['name']);
    } elseif (!empty($_POST['first_name']) || !empty($_POST['last_name'])) {
        $first_name = !empty($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = !empty($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $name = trim($first_name . ' ' . $last_name);
    }

    $email = sanitize_email($_POST['contact_email'] ?? ($_POST['email'] ?? ''));
    $phone = sanitize_text_field($_POST['contact_phone'] ?? ($_POST['phone'] ?? ''));
    $subject = sanitize_text_field($_POST['contact_subject'] ?? ($_POST['event_type'] ?? ''));
    $message = sanitize_textarea_field($_POST['contact_message'] ?? ($_POST['message'] ?? ''));

    // Validation
    $errors = array();
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email) || !is_email($email)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($subject)) {
        $errors[] = 'Subject is required.';
    }
    if (empty($message)) {
        $errors[] = 'Message is required.';
    }

    // If validation fails, store errors in transient
    if (!empty($errors)) {
        if ($is_contact_form) {
            set_transient('contact_form_errors', $errors, 30);
        } else {
            set_transient('consultation_form_errors', $errors, 30);
        }
        return;
    }

    // Email settings
    $to = get_option('admin_email'); // Can be changed to specific email
    $email_subject = 'New Contact Form Submission: ' . ucfirst(str_replace('-', ' ', $subject));

    // Build email message
    $email_message = ($is_contact_form ? "New contact form submission from crosbyultimateevents.com\n\n" : "New consultation request from crosbyultimateevents.com\n\n");
    $email_message .= "Name: {$name}\n";
    $email_message .= "Email: {$email}\n";
    if (!empty($phone)) {
        $email_message .= "Phone: {$phone}\n";
    }
    $email_message .= "Subject: " . ucfirst(str_replace('-', ' ', $subject)) . "\n\n";
    $email_message .= "Message:\n{$message}\n\n";
    $email_message .= "---\n";
    $email_message .= "Submitted: " . date('F j, Y, g:i a') . "\n";
    $email_message .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";

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
        if ($is_contact_form) {
            set_transient('contact_form_success', true, 30);
        } else {
            set_transient('consultation_form_success', true, 30);
        }
        // Optional: Send auto-reply to user
        $auto_reply_subject = 'Thank you for contacting Crosby Ultimate Events';
        $auto_reply_message = "Dear {$name},\n\n";
        $auto_reply_message .= "Thank you for reaching out to Crosby Ultimate Events. We've received your message and will get back to you within 24 hours.\n\n";
        $auto_reply_message .= "Best regards,\nCrosby Ultimate Events Team";
        wp_mail($email, $auto_reply_subject, $auto_reply_message);
    } else {
        $fallback_error = array('Failed to send message. Please try again or contact us directly.');
        if ($is_contact_form) {
            set_transient('contact_form_errors', $fallback_error, 30);
        } else {
            set_transient('consultation_form_errors', $fallback_error, 30);
        }
    }

    // Redirect to prevent form resubmission
    wp_safe_redirect(add_query_arg('submitted', $sent ? 'success' : 'error', get_permalink()));
    exit;
}
add_action('template_redirect', 'crosbyultimateevents_handle_contact_form');

/**
 * Include Custom Post Types
 */
require_once get_template_directory() . '/inc/post-types/icp-definition.php';
require_once get_template_directory() . '/inc/post-types/offer-ladder.php';

/**
 * Include A/B Test Functionality
 */
require_once get_template_directory() . '/ab-test-hero-headline.php';

/**
 * Fix Text Rendering Issues
 * Ensures proper word spacing and prevents text corruption
 */
function crosbyultimateevents_fix_text_rendering($content) {
    // Fix common text rendering issues
    $fixes = array(
        // Fix missing spaces (if any exist in content)
        '/cro\s+byultimateevent/i' => 'crosbyultimateevents',
        '/Con\s+ultation/i' => 'Consultation',
        '/ervice\s+/i' => 'service ',
        '/comprehen\s+ive/i' => 'comprehensive',
        '/occa\s+ion/i' => 'occasion',
        '/di\s+cu\s+/i' => 'discuss ',
        '/re\s+pond/i' => 'respond',
        '/chedule/i' => 'schedule',
        '/hour\s+/i' => 'hours ',
        '/tarted/i' => 'started',
        '/vi\s+ion/i' => 'vision',
        '/Flawle\s+/i' => 'Flawless',
        '/Re\s+triction/i' => 'Restriction',
        '/Partie\s+/i' => 'Parties',
        '/cu\s+tom/i' => 'custom',
        '/cla\s+e/i' => 'class',
        '/cour\s+e/i' => 'course',
    );
    
    // Only apply fixes if broken patterns are found (to avoid over-processing)
    $has_issues = false;
    foreach (array_keys($fixes) as $pattern) {
        if (preg_match($pattern, $content)) {
            $has_issues = true;
            break;
        }
    }
    
    if ($has_issues) {
        foreach ($fixes as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
    }
    
    return $content;
}
// Apply to content with high priority to run after other filters
add_filter('the_content', 'crosbyultimateevents_fix_text_rendering', 999);
add_filter('the_title', 'crosbyultimateevents_fix_text_rendering', 999);
add_filter('bloginfo', 'crosbyultimateevents_fix_text_rendering', 999);

/**
 * Event Inquiry Form Handler - Tier 1 Quick Win WEB-04
 */
add_action('admin_post_handle_event_inquiry', 'handle_event_inquiry');
add_action('admin_post_nopriv_handle_event_inquiry', 'handle_event_inquiry');

function handle_event_inquiry() {
    // Verify nonce
    if (!isset($_POST['event_inquiry_nonce']) || !wp_verify_nonce($_POST['event_inquiry_nonce'], 'event_inquiry_form')) {
        wp_die('Security check failed');
    }
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_die('Invalid email address');
    }
    
    // Process email (add to mailing list, send notification, etc.)
    // Example: wp_mail($admin_email, 'New Event Inquiry', 'Email: ' . $email);
    // TODO: Integrate with email marketing platform or CRM
    
    // Redirect to thank you page or consultation page
    wp_redirect(home_url('/consultation?source=front_page&email=' . urlencode($email)));
    exit;
}