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

    // Enqueue theme JavaScript (if needed)
    // wp_enqueue_script('crosbyultimateevents-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'crosbyultimateevents_scripts');

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


// Create consultation page
function crosbyultimateevents_create_consultation_page() {
    if (get_page_by_path('consultation')) return;
    $consultation_page = array(
        'post_title' => 'consultation',
        'post_name' => 'consultation',
        'post_status' => 'publish',
        'post_type' => 'page',
        'page_template' => 'page-consultation.php'
    );
    wp_insert_post($consultation_page);
}
add_action('after_switch_theme', 'crosbyultimateevents_create_consultation_page');

/**
 * Handle Contact Form Submission
 */
function crosbyultimateevents_handle_contact_form() {
    // Only process on contact page
    if (!is_page('contact')) {
        return;
    }

    // Check if form was submitted
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'contact_form')) {
        return;
    }

    // Honeypot spam protection
    if (!empty($_POST['website_url'])) {
        return; // Spam detected
    }

    // Sanitize and validate input
    $name = sanitize_text_field($_POST['contact_name'] ?? '');
    $email = sanitize_email($_POST['contact_email'] ?? '');
    $phone = sanitize_text_field($_POST['contact_phone'] ?? '');
    $subject = sanitize_text_field($_POST['contact_subject'] ?? '');
    $message = sanitize_textarea_field($_POST['contact_message'] ?? '');

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
        set_transient('contact_form_errors', $errors, 30);
        return;
    }

    // Email settings
    $to = get_option('admin_email'); // Can be changed to specific email
    $email_subject = 'New Contact Form Submission: ' . ucfirst(str_replace('-', ' ', $subject));
    
    // Build email message
    $email_message = "New contact form submission from crosbyultimateevents.com\n\n";
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
        set_transient('contact_form_success', true, 30);
        // Optional: Send auto-reply to user
        $auto_reply_subject = 'Thank you for contacting Crosby Ultimate Events';
        $auto_reply_message = "Dear {$name},\n\n";
        $auto_reply_message .= "Thank you for reaching out to Crosby Ultimate Events. We've received your message and will get back to you within 24 hours.\n\n";
        $auto_reply_message .= "Best regards,\nCrosby Ultimate Events Team";
        wp_mail($email, $auto_reply_subject, $auto_reply_message);
    } else {
        set_transient('contact_form_errors', array('Failed to send message. Please try again or contact us directly.'), 30);
    }

    // Redirect to prevent form resubmission
    wp_safe_redirect(add_query_arg('submitted', $sent ? 'success' : 'error', get_permalink()));
    exit;
}
add_action('template_redirect', 'crosbyultimateevents_handle_contact_form');


/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "Crosby Ultimate Events - Premier event planning and coordination services. Creating unforgettable experiences for weddings, corporate events, and special occasions.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "Crosby Ultimate Events - Premier Event Planning & Coordination";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "Crosby Ultimate Events - Premier Event Planning & Coordination";
    return $title_parts;
}, 10, 1);


/**
 * Add Strict-Transport-Security (HSTS) header
 * Forces browsers to use HTTPS for all future requests
 */
function add_hsts_header() {
    if (is_ssl() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}
add_action('send_headers', 'add_hsts_header', 1);


/**
 * CSS fix to hide extra H1 headings (SEO best practice: only 1 H1 per page)
 */
function hide_extra_h1_headings() {
    echo '<style>
        /* Hide all H1s except the first one */
        h1:not(:first-of-type) {
            display: none !important;
        }
        /* Alternative: Convert visually but keep semantic structure */
        body h1:nth-of-type(n+2) {
            font-size: 0;
            height: 0;
            overflow: hidden;
            visibility: hidden;
        }
    </style>';
}
add_action('wp_head', 'hide_extra_h1_headings', 999);


/**
 * Automatically add alt text to images missing it
 * Improves SEO and accessibility
 */
function add_missing_alt_text($attr, $attachment = null) {
    // If alt text is missing or empty, generate from filename
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            // Generate alt text from filename
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = preg_replace('/[0-9]+[-_]?/', '', $name); // Remove leading numbers
            $name = preg_replace('/[-_]/', ' ', $name); // Replace dashes/underscores
            $name = preg_replace('/\s+/', ' ', $name); // Normalize spaces
            $name = ucwords(strtolower($name)); // Capitalize words
            $attr['alt'] = $name ?: 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_alt_text', 10, 2);

/**
 * Add alt text to images in content (post/page content)
 */
function add_alt_to_content_images($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Find all img tags without alt attribute or with empty alt
    $pattern = '/<img([^>]*?)(?:alt=["']([^"']*)["'])?([^>]*?)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $before_alt = $matches[1];
        $alt_value = isset($matches[2]) ? $matches[2] : '';
        $after_alt = $matches[3];
        
        // If alt is missing or empty, generate from src
        if (empty($alt_value)) {
            // Extract src
            if (preg_match('/src=["']([^"']+)["']/', $matches[0], $src_match)) {
                $src = $src_match[1];
                $filename = basename(parse_url($src, PHP_URL_PATH));
                $name = pathinfo($filename, PATHINFO_FILENAME);
                $name = preg_replace('/[0-9]+[-_]?/', '', $name);
                $name = preg_replace('/[-_]/', ' ', $name);
                $name = preg_replace('/\s+/', ' ', $name);
                $name = ucwords(strtolower($name));
                $alt_value = $name ?: 'Image';
            } else {
                $alt_value = 'Image';
            }
        }
        
        // Reconstruct img tag with alt
        return '<img' . $before_alt . ' alt="' . esc_attr($alt_value) . '"' . $after_alt . '>';
    }, $content);
    
    return $content;
}
add_filter('the_content', 'add_alt_to_content_images', 10, 1);


/**
 * Automatically add alt text to images missing it
 * Improves SEO and accessibility
 */
function add_missing_alt_text($attr, $attachment = null) {
    // If alt text is missing or empty, generate from filename
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            // Generate alt text from filename
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = preg_replace('/[0-9]+[-_]?/', '', $name); // Remove leading numbers
            $name = preg_replace('/[-_]/', ' ', $name); // Replace dashes/underscores
            $name = preg_replace('/\\s+/', ' ', $name); // Normalize spaces
            $name = ucwords(strtolower($name)); // Capitalize words
            $attr['alt'] = $name ?: 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_alt_text', 10, 2);

/**
 * Add alt text to images in content (post/page content)
 */
function add_alt_to_content_images($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Find all img tags without alt attribute or with empty alt
    $pattern = '/<img([^>]*?)(?:alt=["\']([^"\']*)["\'])?([^>]*?)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $before_alt = $matches[1];
        $alt_value = isset($matches[2]) ? $matches[2] : '';
        $after_alt = $matches[3];
        
        // If alt is missing or empty, generate from src
        if (empty($alt_value)) {
            // Extract src
            if (preg_match('/src=["\']([^"\']+)["\']/', $matches[0], $src_match)) {
                $src = $src_match[1];
                $filename = basename(parse_url($src, PHP_URL_PATH));
                $name = pathinfo($filename, PATHINFO_FILENAME);
                $name = preg_replace('/[0-9]+[-_]?/', '', $name);
                $name = preg_replace('/[-_]/', ' ', $name);
                $name = preg_replace('/\\s+/', ' ', $name);
                $name = ucwords(strtolower($name));
                $alt_value = $name ?: 'Image';
            } else {
                $alt_value = 'Image';
            }
        }
        
        // Reconstruct img tag with alt
        return '<img' . $before_alt . ' alt="' . esc_attr($alt_value) . '"' . $after_alt . '>';
    }, $content);
    
    return $content;
}
add_filter('the_content', 'add_alt_to_content_images', 10, 1);

<?php
/**
 * Add Missing Alt Text to Images - Added by Agent-7
 * Automatically adds descriptive alt text to images that are missing it
 */

// Add alt text to images in post content when missing
function add_missing_alt_text_to_content($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Use DOMDocument to parse HTML
    if (!class_exists('DOMDocument')) {
        return $content;
    }
    
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $images = $dom->getElementsByTagName('img');
    
    foreach ($images as $img) {
        $alt = $img->getAttribute('alt');
        
        // If alt is empty or missing, generate from image attributes
        if (empty($alt) || $alt === '') {
            // Try to get alt from title attribute
            $title = $img->getAttribute('title');
            if (!empty($title)) {
                $img->setAttribute('alt', $title);
            } else {
                // Try to get from src filename
                $src = $img->getAttribute('src');
                if (!empty($src)) {
                    $filename = basename($src);
                    $name_without_ext = preg_replace('/\.[^.\s]{3,4}$/', '', $filename);
                    $name_formatted = str_replace(['-', '_'], ' ', $name_without_ext);
                    $name_formatted = ucwords($name_formatted);
                    if (!empty($name_formatted)) {
                        $img->setAttribute('alt', $name_formatted);
                    } else {
                        // Default fallback
                        $img->setAttribute('alt', 'Image');
                    }
                } else {
                    // Default fallback
                    $img->setAttribute('alt', 'Image');
                }
            }
        }
    }
    
    // Get updated HTML
    $new_content = $dom->saveHTML();
    
    // Clean up (remove DOCTYPE, html, body tags added by DOMDocument)
    $new_content = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
    return $new_content;
}
add_filter('the_content', 'add_missing_alt_text_to_content', 20);

// Add alt text to post thumbnails when missing
function add_missing_alt_text_to_thumbnails($html, $post_id, $post_thumbnail_id, $size, $attr) {
    // Check if alt is already set
    if (strpos($html, 'alt=') !== false) {
        // Extract existing alt value
        preg_match('/alt=["']([^"']*)["']/', $html, $matches);
        if (!empty($matches[1])) {
            return $html; // Alt already exists
        }
    }
    
    // Get alt text from attachment meta or post title
    $alt_text = get_post_meta($post_thumbnail_id, '_wp_attachment_image_alt', true);
    
    if (empty($alt_text)) {
        // Try to get from attachment title
        $attachment = get_post($post_thumbnail_id);
        if ($attachment) {
            $alt_text = $attachment->post_title;
        }
        
        if (empty($alt_text)) {
            // Fallback to post title
            $post = get_post($post_id);
            if ($post) {
                $alt_text = $post->post_title . ' - Featured Image';
            } else {
                $alt_text = 'Featured Image';
            }
        }
    }
    
    // Add or update alt attribute
    if (strpos($html, '<img') !== false) {
        if (strpos($html, 'alt=') !== false) {
            // Replace existing empty alt
            $html = preg_replace('/alt=["'][^"']*["']/', 'alt="' . esc_attr($alt_text) . '"', $html);
        } else {
            // Add alt attribute
            $html = preg_replace('/(<img[^>]+)(\s*\/?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
        }
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'add_missing_alt_text_to_thumbnails', 10, 5);

// Add alt text to widget images when missing
function add_missing_alt_text_to_widgets($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Simple regex approach for widget content
    $content = preg_replace_callback(
        '/<img([^>]*?)(?:alt=["']([^"']*)["'])?([^>]*?)>/i',
        function($matches) {
            $before_alt = $matches[1];
            $existing_alt = isset($matches[2]) ? $matches[2] : '';
            $after_alt = $matches[3];
            
            // If alt is empty, try to extract from src or use default
            if (empty($existing_alt)) {
                // Try to get from src
                preg_match('/src=["']([^"']*)["']/', $before_alt . $after_alt, $src_match);
                if (!empty($src_match[1])) {
                    $filename = basename($src_match[1]);
                    $name_without_ext = preg_replace('/\.[^.\s]{3,4}$/', '', $filename);
                    $name_formatted = str_replace(['-', '_'], ' ', $name_without_ext);
                    $name_formatted = ucwords($name_formatted);
                    $alt_text = !empty($name_formatted) ? $name_formatted : 'Image';
                } else {
                    $alt_text = 'Image';
                }
                
                // Add alt attribute
                return '<img' . $before_alt . ' alt="' . esc_attr($alt_text) . '"' . $after_alt . '>';
            }
            
            return $matches[0];
        },
        $content
    );
    
    return $content;
}
add_filter('widget_text', 'add_missing_alt_text_to_widgets', 20);
add_filter('the_excerpt', 'add_missing_alt_text_to_widgets', 20);
