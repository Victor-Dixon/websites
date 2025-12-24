<?php
/**
 * AriaJet Theme Functions
 * 
 * Custom WordPress theme for Aria's 2D game showcase
 * 
 * @package AriaJet
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Theme Setup
 */
function ariajet_setup() {
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
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'ariajet'),
        'footer' => __('Footer Menu', 'ariajet'),
    ));
    
    // Set content width
    $GLOBALS['content_width'] = 1200;
}
add_action('after_setup_theme', 'ariajet_setup');

/**
 * Enqueue Scripts and Styles
 */
function ariajet_scripts() {
    // Theme stylesheet
    wp_enqueue_style('ariajet-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Custom styles for game showcase
    wp_enqueue_style('ariajet-games', get_template_directory_uri() . '/css/games.css', array('ariajet-style'), '1.0.0');
    
    // Theme JavaScript
    wp_enqueue_script('ariajet-main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true);
    
    // Game interaction scripts
    wp_enqueue_script('ariajet-games', get_template_directory_uri() . '/js/games.js', array('ariajet-main'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'ariajet_scripts');

/**
 * Register Game Post Type
 */
function ariajet_register_game_post_type() {
    $labels = array(
        'name' => __('Games', 'ariajet'),
        'singular_name' => __('Game', 'ariajet'),
        'add_new' => __('Add New Game', 'ariajet'),
        'add_new_item' => __('Add New Game', 'ariajet'),
        'edit_item' => __('Edit Game', 'ariajet'),
        'new_item' => __('New Game', 'ariajet'),
        'view_item' => __('View Game', 'ariajet'),
        'search_items' => __('Search Games', 'ariajet'),
        'not_found' => __('No games found', 'ariajet'),
        'not_found_in_trash' => __('No games found in Trash', 'ariajet'),
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'games'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-games',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest' => true, // Enable Gutenberg
    );
    
    register_post_type('game', $args);
}
add_action('init', 'ariajet_register_game_post_type');

/**
 * Register Game Categories Taxonomy
 */
function ariajet_register_game_taxonomy() {
    $labels = array(
        'name' => __('Game Categories', 'ariajet'),
        'singular_name' => __('Game Category', 'ariajet'),
        'search_items' => __('Search Categories', 'ariajet'),
        'all_items' => __('All Categories', 'ariajet'),
        'parent_item' => __('Parent Category', 'ariajet'),
        'parent_item_colon' => __('Parent Category:', 'ariajet'),
        'edit_item' => __('Edit Category', 'ariajet'),
        'update_item' => __('Update Category', 'ariajet'),
        'add_new_item' => __('Add New Category', 'ariajet'),
        'new_item_name' => __('New Category Name', 'ariajet'),
    );
    
    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'game-category'),
        'show_in_rest' => true,
    );
    
    register_taxonomy('game_category', array('game'), $args);
}
add_action('init', 'ariajet_register_game_taxonomy');

/**
 * Add Game Meta Boxes
 */
function ariajet_add_game_meta_boxes() {
    add_meta_box(
        'ariajet_game_details',
        __('Game Details', 'ariajet'),
        'ariajet_game_details_callback',
        'game',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ariajet_add_game_meta_boxes');

/**
 * Game Details Meta Box Callback
 */
function ariajet_game_details_callback($post) {
    wp_nonce_field('ariajet_save_game_details', 'ariajet_game_details_nonce');
    
    $game_url = get_post_meta($post->ID, '_ariajet_game_url', true);
    $game_type = get_post_meta($post->ID, '_ariajet_game_type', true);
    $game_status = get_post_meta($post->ID, '_ariajet_game_status', true);
    
    
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>
    <table class="form-table">
        <tr>
            <th><label for="ariajet_game_url"><?php _e('Game URL', 'ariajet'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?></label></th>
            <td>
                <input type="url" id="ariajet_game_url" name="ariajet_game_url" 
                       value="<?php echo esc_attr($game_url); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>" 
                       class="regular-text" 
                       placeholder="https://ariajet.site/games/game-name.html" />
                <p class="description"><?php _e('URL to the game HTML file', 'ariajet'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?></p>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_type"><?php _e('Game Type', 'ariajet'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?></label></th>
            <td>
                <select id="ariajet_game_type" name="ariajet_game_type">
                    <option value="2d" <?php selected($game_type, '2d'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>>2D Game</option>
                    <option value="puzzle" <?php selected($game_type, 'puzzle'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>>Puzzle</option>
                    <option value="adventure" <?php selected($game_type, 'adventure'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>>Adventure</option>
                    <option value="survival" <?php selected($game_type, 'survival'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>>Survival</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_status"><?php _e('Status', 'ariajet'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?></label></th>
            <td>
                <select id="ariajet_game_status" name="ariajet_game_status">
                    <option value="published" <?php selected($game_status, 'published'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>>Published</option>
                    <option value="beta" <?php selected($game_status, 'beta'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>>Beta</option>
                    <option value="development" <?php selected($game_status, 'development'); 
/**
 * Add SEO meta description
 */
function add_meta_description() {
    $description = "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
}
add_action('wp_head', 'add_meta_description', 1);


/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {
    $site_title = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
    if (is_front_page() || is_home()) {
        return $site_title;
    }
    return $site_title . " | " . get_bloginfo('name');
}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {
    $title_parts['title'] = "AriaJet - Premium Private Jet Charter Services | Luxury Travel";
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

?>>In Development</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Game Meta Data
 */
function ariajet_save_game_details($post_id) {
    if (!isset($_POST['ariajet_game_details_nonce']) || 
        !wp_verify_nonce($_POST['ariajet_game_details_nonce'], 'ariajet_save_game_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['ariajet_game_url'])) {
        update_post_meta($post_id, '_ariajet_game_url', esc_url_raw($_POST['ariajet_game_url']));
    }
    
    if (isset($_POST['ariajet_game_type'])) {
        update_post_meta($post_id, '_ariajet_game_type', sanitize_text_field($_POST['ariajet_game_type']));
    }
    
    if (isset($_POST['ariajet_game_status'])) {
        update_post_meta($post_id, '_ariajet_game_status', sanitize_text_field($_POST['ariajet_game_status']));
    }
}
add_action('save_post', 'ariajet_save_game_details');

/**
 * Custom Game Archive Template
 */
function ariajet_get_game_archive_template($template) {
    if (is_post_type_archive('game')) {
        $new_template = locate_template(array('archive-game.php'));
        if ($new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'ariajet_get_game_archive_template');

/**
 * Add Custom Body Classes
 */
function ariajet_body_classes($classes) {
    if (is_post_type_archive('game') || is_singular('game')) {
        $classes[] = 'ariajet-game-page';
    }
    return $classes;
}
add_filter('body_class', 'ariajet_body_classes');






// Add custom modern design CSS


// Enqueue modern design CSS
function ariajet_enqueue_modern_styles() {
    // Try to enqueue external file first
    $css_file = get_template_directory() . '/ariajet-modern.css';
    if (file_exists($css_file)) {
        wp_enqueue_style(
            'ariajet-modern-css',
            get_template_directory_uri() . '/ariajet-modern.css',
            array(),
            '1.0.0'
        );
    } else {
        // Fallback: inline CSS
        $custom_css = "/* AriaJet Modern Design - Complete Style Override */\n\n/* CSS Variables */\n:root {\n    --primary: #667eea;\n    --secondary: #764ba2;\n    --accent: #f093fb;\n    --dark: #2d3748;\n    --light: #f7fafc;\n    --white: #ffffff;\n    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\n    --gradient-hero: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);\n    --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);\n    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);\n    --shadow-lg: 0 10px 25px rgba(0,0,0,0.15);\n}\n\n/* Global Reset & Base Styles */\n* {\n    box-sizing: border-box;\n}\n\nbody {\n    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;\n    line-height: 1.6 !important;\n    color: var(--dark) !important;\n    margin: 0 !important;\n    padding: 0 !important;\n    background: var(--light) !important;\n}\n\n/* Header Styles */\nheader,\n.site-header,\n.wp-site-blocks header,\n.header {\n    background: var(--white) !important;\n    box-shadow: var(--shadow-md) !important;\n    position: fixed !important;\n    top: 0 !important;\n    left: 0 !important;\n    right: 0 !important;\n    width: 100% !important;\n    z-index: 9999 !important;\n    padding: 1rem 2rem !important;\n}\n\nheader .site-title,\n.site-header .site-title,\nheader h1,\n.site-header h1,\n.wp-block-site-title a {\n    font-size: 1.8rem !important;\n    font-weight: 700 !important;\n    background: var(--gradient-primary) !important;\n    -webkit-background-clip: text !important;\n    -webkit-text-fill-color: transparent !important;\n    background-clip: text !important;\n    margin: 0 !important;\n    text-decoration: none !important;\n}\n\n/* Navigation */\nnav,\n.main-navigation,\n.wp-block-navigation,\n.site-navigation {\n    display: flex !important;\n    gap: 2rem !important;\n    align-items: center !important;\n}\n\nnav a,\n.main-navigation a,\n.wp-block-navigation a,\n.site-navigation a {\n    color: var(--dark) !important;\n    text-decoration: none !important;\n    font-weight: 500 !important;\n    transition: color 0.3s ease !important;\n}\n\nnav a:hover,\n.main-navigation a:hover,\n.wp-block-navigation a:hover {\n    color: var(--primary) !important;\n}\n\n/* Hero Section */\n.hero,\n.hero-section,\n.wp-block-cover,\n.wp-block-group.has-background,\n.entry-header,\n.page-header {\n    background: var(--gradient-hero) !important;\n    color: var(--white) !important;\n    padding: 180px 2rem 100px !important;\n    text-align: center !important;\n    margin-top: 70px !important;\n    min-height: 60vh !important;\n    display: flex !important;\n    flex-direction: column !important;\n    justify-content: center !important;\n    align-items: center !important;\n}\n\n.hero h1,\n.hero-section h1,\n.wp-block-cover h1,\n.entry-header h1,\n.page-header h1,\n.wp-block-post-title {\n    font-size: 3.5rem !important;\n    font-weight: 700 !important;\n    color: var(--white) !important;\n    margin: 0 0 1rem 0 !important;\n    text-shadow: 0 2px 10px rgba(0,0,0,0.2) !important;\n}\n\n.hero p,\n.hero-section p,\n.wp-block-cover p {\n    font-size: 1.3rem !important;\n    color: rgba(255,255,255,0.95) !important;\n    margin-bottom: 2rem !important;\n    max-width: 700px !important;\n}\n\n/* Buttons */\n.button,\n.wp-block-button__link,\n.btn,\nbutton,\na.button {\n    display: inline-block !important;\n    padding: 1rem 2.5rem !important;\n    background: var(--white) !important;\n    color: var(--primary) !important;\n    text-decoration: none !important;\n    border-radius: 50px !important;\n    font-weight: 600 !important;\n    border: none !important;\n    cursor: pointer !important;\n    transition: all 0.3s ease !important;\n    box-shadow: var(--shadow-md) !important;\n}\n\n.button:hover,\n.wp-block-button__link:hover,\n.btn:hover,\nbutton:hover {\n    transform: translateY(-3px) !important;\n    box-shadow: var(--shadow-lg) !important;\n}\n\n/* Content Sections */\nmain,\n.content,\n.site-content,\n.wp-block-group {\n    max-width: 1200px !important;\n    margin: 0 auto !important;\n    padding: 80px 2rem !important;\n}\n\nsection,\n.wp-block-group {\n    margin-bottom: 4rem !important;\n}\n\nh2,\n.wp-block-heading,\n.wp-block-post-title {\n    font-size: 2.5rem !important;\n    font-weight: 700 !important;\n    color: var(--dark) !important;\n    margin-bottom: 2rem !important;\n    text-align: center !important;\n}\n\n/* Cards */\n.card,\n.wp-block-column,\narticle,\n.post {\n    background: var(--white) !important;\n    padding: 2.5rem !important;\n    border-radius: 15px !important;\n    box-shadow: var(--shadow-md) !important;\n    transition: transform 0.3s ease, box-shadow 0.3s ease !important;\n}\n\n.card:hover,\n.wp-block-column:hover,\narticle:hover {\n    transform: translateY(-10px) !important;\n    box-shadow: var(--shadow-lg) !important;\n}\n\n/* Grid Layouts */\n.grid,\n.wp-block-columns,\n.columns {\n    display: grid !important;\n    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;\n    gap: 2rem !important;\n    margin: 2rem 0 !important;\n}\n\n/* Footer */\nfooter,\n.site-footer,\n.wp-block-template-part {\n    background: var(--dark) !important;\n    color: var(--white) !important;\n    padding: 3rem 2rem !important;\n    text-align: center !important;\n    margin-top: 4rem !important;\n}\n\nfooter a,\n.site-footer a {\n    color: var(--white) !important;\n}\n\n/* Responsive */\n@media (max-width: 768px) {\n    .hero h1,\n    .hero-section h1,\n    .entry-header h1 {\n        font-size: 2.5rem !important;\n    }\n    \n    .grid,\n    .wp-block-columns {\n        grid-template-columns: 1fr !important;\n    }\n    \n    header,\n    .site-header {\n        padding: 1rem !important;\n    }\n}\n\n/* Smooth Scrolling */\nhtml {\n    scroll-behavior: smooth !important;\n}\n\n/* Animations */\n@keyframes fadeInUp {\n    from {\n        opacity: 0;\n        transform: translateY(30px);\n    }\n    to {\n        opacity: 1;\n        transform: translateY(0);\n    }\n}\n\n.hero,\n.hero-section {\n    animation: fadeInUp 0.8s ease !important;\n}\n\n";
        wp_add_inline_style('wp-block-library', $custom_css);
    }
}
add_action('wp_enqueue_scripts', 'ariajet_enqueue_modern_styles', 999);

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
