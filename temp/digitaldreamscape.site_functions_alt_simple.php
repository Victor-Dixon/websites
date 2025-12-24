<?php
/**
 * Digital Dreamscape Theme Functions
 * 
 * Living, narrative-driven AI world theme
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function digitaldreamscape_setup() {
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
        'primary' => __('Primary Menu', 'digitaldreamscape'),
        'footer' => __('Footer Menu', 'digitaldreamscape'),
    ));
}
add_action('after_setup_theme', 'digitaldreamscape_setup');

/**
 * Enqueue Styles and Scripts
 */
function digitaldreamscape_scripts() {
    // Enqueue theme stylesheet
    wp_enqueue_style('digitaldreamscape-style', get_stylesheet_uri(), array(), '2.0.0');

    // Enqueue theme JavaScript
    wp_enqueue_script('digitaldreamscape-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '2.0.0', true);
    
    // Add Digital Dreamscape context to page
    wp_localize_script('digitaldreamscape-script', 'dreamscapeContext', array(
        'isEpisode' => is_single(),
        'isArchive' => is_archive(),
        'narrativeMode' => true,
    ));
}
add_action('wp_enqueue_scripts', 'digitaldreamscape_scripts');

/**
 * Register Widget Areas
 */
function digitaldreamscape_widgets_init() {
    register_sidebar(array(
        'name' => __('Sidebar', 'digitaldreamscape'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'digitaldreamscape'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'digitaldreamscape_widgets_init');

/**
 * Add Digital Dreamscape meta description to posts
 */
function digitaldreamscape_post_meta_description() {
    if (is_single()) {
        $description = 'Digital Dreamscape is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. This episode is part of the persistent simulation of self + system.';
        echo '<meta name="description" content="' . esc_attr($description) . '">';
    }
}
add_action('wp_head', 'digitaldreamscape_post_meta_description');


/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "Digital Dreamscape - A living, narrative-driven AI world. Explore the intersection of technology, creativity, and autonomous agent civilization.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "Digital Dreamscape - Living Narrative-Driven AI World & Technology";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "Digital Dreamscape - Living Narrative-Driven AI World & Technology";
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
    $new_content = preg_replace('/^<!DOCTYPE.+
/**
 * Add missing alt text to images for SEO and accessibility
 */
function add_missing_image_alt($attr, $attachment = null) {
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = ucwords(strtolower(trim($name)));
            $attr['alt'] = $name ? $name : 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_image_alt', 10, 2);

?>/', '', str_replace(array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $new_content));
    
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
            $html = preg_replace('/(<img[^>]+)(\s*\/
/**
 * Add missing alt text to images for SEO and accessibility
 */
function add_missing_image_alt($attr, $attachment = null) {
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = ucwords(strtolower(trim($name)));
            $attr['alt'] = $name ? $name : 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_image_alt', 10, 2);

?>)/i', '$1 alt="' . esc_attr($alt_text) . '"$2', $html);
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

/**
 * Add missing alt text to images for SEO and accessibility
 */
function add_missing_image_alt($attr, $attachment = null) {
    if (empty($attr['alt']) && $attachment) {
        $filename = get_post_meta($attachment->ID, '_wp_attached_file', true);
        if ($filename) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = str_replace(array('-', '_'), ' ', $name);
            $name = ucwords(strtolower(trim($name)));
            $attr['alt'] = $name ? $name : 'Image';
        }
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_missing_image_alt', 10, 2);
