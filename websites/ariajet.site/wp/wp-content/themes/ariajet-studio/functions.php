<?php
/**
 * AriaJet Studio Theme Functions
 * 
 * A premium, calm, and inviting WordPress theme.
 * 
 * @package AriaJet_Studio
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Constants
 */
define('ARIAJET_STUDIO_VERSION', '1.0.0');
define('ARIAJET_STUDIO_DIR', get_template_directory());
define('ARIAJET_STUDIO_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function ariajet_studio_setup() {
    // Translation support
    load_theme_textdomain('ariajet-studio', ARIAJET_STUDIO_DIR . '/languages');
    
    // Theme features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    // Allow comments on pages (needed for About page comments)
    add_post_type_support('page', 'comments');
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    
    // Editor color palette - matches our calm, pretty colors
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => __('Ink', 'ariajet-studio'),
            'slug'  => 'ink',
            'color' => '#2D2A26',
        ),
        array(
            'name'  => __('Cream', 'ariajet-studio'),
            'slug'  => 'cream',
            'color' => '#FFFBF7',
        ),
        array(
            'name'  => __('Coral', 'ariajet-studio'),
            'slug'  => 'coral',
            'color' => '#FF8A6C',
        ),
        array(
            'name'  => __('Sage', 'ariajet-studio'),
            'slug'  => 'sage',
            'color' => '#B8D4B8',
        ),
        array(
            'name'  => __('Lavender', 'ariajet-studio'),
            'slug'  => 'lavender',
            'color' => '#D4C8E8',
        ),
        array(
            'name'  => __('Sky', 'ariajet-studio'),
            'slug'  => 'sky',
            'color' => '#C8E4F0',
        ),
    ));
    
    // Navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Navigation', 'ariajet-studio'),
        'footer'  => __('Footer Navigation', 'ariajet-studio'),
    ));
    
    // Content width
    $GLOBALS['content_width'] = 1200;
}
add_action('after_setup_theme', 'ariajet_studio_setup');

/**
 * Enqueue Styles and Scripts
 */
function ariajet_studio_scripts() {
    // Main stylesheet
    wp_enqueue_style(
        'ariajet-studio-style',
        get_stylesheet_uri(),
        array(),
        ARIAJET_STUDIO_VERSION
    );
    
    // Games styles
    wp_enqueue_style(
        'ariajet-studio-games',
        ARIAJET_STUDIO_URI . '/css/games.css',
        array('ariajet-studio-style'),
        ARIAJET_STUDIO_VERSION
    );
    
    // Main JavaScript
    wp_enqueue_script(
        'ariajet-studio-main',
        ARIAJET_STUDIO_URI . '/js/main.js',
        array(),
        ARIAJET_STUDIO_VERSION,
        true
    );
    
    // Games JavaScript
    wp_enqueue_script(
        'ariajet-studio-games',
        ARIAJET_STUDIO_URI . '/js/games.js',
        array('ariajet-studio-main'),
        ARIAJET_STUDIO_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'ariajet_studio_scripts');

/**
 * Register Game Post Type
 */
function ariajet_studio_register_game_post_type() {
    $labels = array(
        'name'               => _x('Games', 'post type', 'ariajet-studio'),
        'singular_name'      => _x('Game', 'post type', 'ariajet-studio'),
        'menu_name'          => _x('Games', 'admin menu', 'ariajet-studio'),
        'add_new'            => _x('Add New', 'game', 'ariajet-studio'),
        'add_new_item'       => __('Add New Game', 'ariajet-studio'),
        'edit_item'          => __('Edit Game', 'ariajet-studio'),
        'new_item'           => __('New Game', 'ariajet-studio'),
        'view_item'          => __('View Game', 'ariajet-studio'),
        'search_items'       => __('Search Games', 'ariajet-studio'),
        'not_found'          => __('No games found', 'ariajet-studio'),
        'not_found_in_trash' => __('No games found in trash', 'ariajet-studio'),
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'games', 'with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-games',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
    );
    
    register_post_type('game', $args);
}
add_action('init', 'ariajet_studio_register_game_post_type');

/**
 * Register Game Category Taxonomy
 */
function ariajet_studio_register_game_taxonomy() {
    register_taxonomy('game_category', 'game', array(
        'labels' => array(
            'name'          => _x('Categories', 'taxonomy', 'ariajet-studio'),
            'singular_name' => _x('Category', 'taxonomy', 'ariajet-studio'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'game-category'),
    ));
}
add_action('init', 'ariajet_studio_register_game_taxonomy');

/**
 * Game Meta Boxes
 */
function ariajet_studio_game_meta_boxes() {
    add_meta_box(
        'ariajet_studio_game_details',
        __('Game Details', 'ariajet-studio'),
        'ariajet_studio_game_meta_callback',
        'game',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ariajet_studio_game_meta_boxes');

function ariajet_studio_game_meta_callback($post) {
    wp_nonce_field('ariajet_studio_game_meta', 'ariajet_studio_game_nonce');
    
    $game_url = get_post_meta($post->ID, '_ariajet_game_url', true);
    $game_type = get_post_meta($post->ID, '_ariajet_game_type', true);
    $game_status = get_post_meta($post->ID, '_ariajet_game_status', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="ariajet_game_url"><?php _e('Game URL', 'ariajet-studio'); ?></label></th>
            <td>
                <input type="url" id="ariajet_game_url" name="ariajet_game_url" 
                       value="<?php echo esc_attr($game_url); ?>" class="large-text">
                <p class="description"><?php _e('Link to the playable game', 'ariajet-studio'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_type"><?php _e('Type', 'ariajet-studio'); ?></label></th>
            <td>
                <select id="ariajet_game_type" name="ariajet_game_type">
                    <option value=""><?php _e('Select type', 'ariajet-studio'); ?></option>
                    <option value="2d" <?php selected($game_type, '2d'); ?>><?php _e('2D Game', 'ariajet-studio'); ?></option>
                    <option value="puzzle" <?php selected($game_type, 'puzzle'); ?>><?php _e('Puzzle', 'ariajet-studio'); ?></option>
                    <option value="adventure" <?php selected($game_type, 'adventure'); ?>><?php _e('Adventure', 'ariajet-studio'); ?></option>
                    <option value="platformer" <?php selected($game_type, 'platformer'); ?>><?php _e('Platformer', 'ariajet-studio'); ?></option>
                    <option value="arcade" <?php selected($game_type, 'arcade'); ?>><?php _e('Arcade', 'ariajet-studio'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_status"><?php _e('Status', 'ariajet-studio'); ?></label></th>
            <td>
                <select id="ariajet_game_status" name="ariajet_game_status">
                    <option value="published" <?php selected($game_status, 'published'); ?>><?php _e('Published', 'ariajet-studio'); ?></option>
                    <option value="beta" <?php selected($game_status, 'beta'); ?>><?php _e('Beta', 'ariajet-studio'); ?></option>
                    <option value="development" <?php selected($game_status, 'development'); ?>><?php _e('In Development', 'ariajet-studio'); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function ariajet_studio_save_game_meta($post_id) {
    if (!isset($_POST['ariajet_studio_game_nonce']) || 
        !wp_verify_nonce($_POST['ariajet_studio_game_nonce'], 'ariajet_studio_game_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    $fields = array(
        'ariajet_game_url'    => '_ariajet_game_url',
        'ariajet_game_type'   => '_ariajet_game_type',
        'ariajet_game_status' => '_ariajet_game_status',
    );
    
    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            $value = ($field === 'ariajet_game_url') 
                ? esc_url_raw($_POST[$field]) 
                : sanitize_text_field($_POST[$field]);
            update_post_meta($post_id, $meta_key, $value);
        }
    }
}
add_action('save_post_game', 'ariajet_studio_save_game_meta');

/**
 * Custom Body Classes
 */
function ariajet_studio_body_classes($classes) {
    $classes[] = 'ariajet-studio';
    
    if (is_singular('game')) {
        $classes[] = 'single-game-page';
    }
    
    if (is_post_type_archive('game')) {
        $classes[] = 'game-archive-page';
    }
    
    return $classes;
}
add_filter('body_class', 'ariajet_studio_body_classes');

/**
 * Customize Excerpt Length
 */
function ariajet_studio_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'ariajet_studio_excerpt_length');

/**
 * Customize Excerpt More
 */
function ariajet_studio_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'ariajet_studio_excerpt_more');

/**
 * Add menu item icons via filter (for nav menus)
 */
function ariajet_studio_nav_menu_icons($items, $args) {
    if ($args->theme_location === 'primary') {
        // Add icons based on menu item titles
        $icons = array(
            'Home'   => '🏠',
            'Games'  => '🎮',
            'About'  => '✨',
            'Blog'   => '📝',
            'Contact' => '💌',
        );
        
        foreach ($items as $item) {
            $title = trim(strip_tags($item->title));
            if (isset($icons[$title])) {
                $item->title = '<span class="nav-icon">' . $icons[$title] . '</span> ' . $item->title;
            }
        }
    }
    
    return $items;
}
add_filter('wp_nav_menu_objects', 'ariajet_studio_nav_menu_icons', 10, 2);

/**
 * Rewrite a "Capabilities" nav item to Home (/).
 * (Menu labels usually live in the WordPress database.)
 */
function ariajet_studio_fix_capabilities_menu_item($items, $args) {
    if (!isset($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }

    foreach ($items as $item) {
        $title = trim(wp_strip_all_tags($item->title));
        $url = isset($item->url) ? trim((string) $item->url) : '';
        $is_dead_link = ($url === '' || $url === '#' || strcasecmp($url, 'javascript:void(0)') === 0);

        // If a menu item is labeled "Capabilities" or "Agents", make it Home → /
        if (strcasecmp($title, 'Capabilities') === 0 || strcasecmp($title, 'Agents') === 0) {
            $item->title = __('Home', 'ariajet-studio');
            $item->url = home_url('/');
            continue;
        }

        // If a menu item is labeled "Home" but points to a dead link, fix it.
        if (strcasecmp($title, 'Home') === 0 && $is_dead_link) {
            $item->url = home_url('/');
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'ariajet_studio_fix_capabilities_menu_item', 9, 2);

/**
 * Force comments open on the About page so the form is usable.
 */
function ariajet_studio_force_about_comments_open($open, $post_id) {
    $slug = (string) get_post_field('post_name', $post_id);
    if (strcasecmp($slug, 'about') === 0) {
        return true;
    }
    return $open;
}
add_filter('comments_open', 'ariajet_studio_force_about_comments_open', 10, 2);


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
    
    // Map page slugs to templates (customize per site)
    $page_templates = array(
        // Add site-specific page templates here
        // Example: 'about' => 'page-templates/page-about.php',
        // Example: 'blog' => 'page-templates/page-blog.php',
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

