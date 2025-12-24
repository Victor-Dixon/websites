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
