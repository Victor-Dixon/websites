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
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="ariajet_game_url"><?php _e('Game URL', 'ariajet'); ?></label></th>
            <td>
                <input type="url" id="ariajet_game_url" name="ariajet_game_url" 
                       value="<?php echo esc_attr($game_url); ?>" 
                       class="regular-text" 
                       placeholder="https://ariajet.site/games/game-name.html" />
                <p class="description"><?php _e('URL to the game HTML file', 'ariajet'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_type"><?php _e('Game Type', 'ariajet'); ?></label></th>
            <td>
                <select id="ariajet_game_type" name="ariajet_game_type">
                    <option value="2d" <?php selected($game_type, '2d'); ?>>2D Game</option>
                    <option value="puzzle" <?php selected($game_type, 'puzzle'); ?>>Puzzle</option>
                    <option value="adventure" <?php selected($game_type, 'adventure'); ?>>Adventure</option>
                    <option value="survival" <?php selected($game_type, 'survival'); ?>>Survival</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_status"><?php _e('Status', 'ariajet'); ?></label></th>
            <td>
                <select id="ariajet_game_status" name="ariajet_game_status">
                    <option value="published" <?php selected($game_status, 'published'); ?>>Published</option>
                    <option value="beta" <?php selected($game_status, 'beta'); ?>>Beta</option>
                    <option value="development" <?php selected($game_status, 'development'); ?>>In Development</option>
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
function ariajet_add_custom_style() {
    $custom_css = "/* AriaJet Modern Design - Custom CSS */\n:root {\n    --primary-color: #0066cc;\n    --secondary-color: #00a8e8;\n    --accent-color: #ff6b6b;\n    --text-dark: #2c3e50;\n    --text-light: #7f8c8d;\n    --bg-light: #f8f9fa;\n    --white: #ffffff;\n    --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\n}\n\n.site-header {\n    background: var(--white) !important;\n    box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;\n    position: fixed !important;\n    width: 100% !important;\n    top: 0 !important;\n    z-index: 1000 !important;\n    transition: all 0.3s ease !important;\n}\n\n.site-header .site-branding {\n    font-size: 1.8rem !important;\n    font-weight: bold !important;\n    background: var(--gradient) !important;\n    -webkit-background-clip: text !important;\n    -webkit-text-fill-color: transparent !important;\n    background-clip: text !important;\n}\n\n.main-navigation a {\n    text-decoration: none !important;\n    color: var(--text-dark) !important;\n    font-weight: 500 !important;\n    transition: color 0.3s ease !important;\n}\n\n.main-navigation a:hover {\n    color: var(--primary-color) !important;\n}\n\n.hero-section,\n.entry-header,\n.page-header {\n    background: var(--gradient) !important;\n    color: var(--white) !important;\n    padding: 150px 2rem 100px !important;\n    text-align: center !important;\n    margin-top: 70px !important;\n}\n\n.hero-section h1,\n.entry-header h1,\n.page-header h1 {\n    font-size: 3.5rem !important;\n    margin-bottom: 1rem !important;\n    font-weight: 700 !important;\n    color: var(--white) !important;\n    animation: fadeInUp 0.8s ease !important;\n}\n\n.cta-button,\n.wp-block-button__link,\n.button {\n    display: inline-block !important;\n    padding: 1rem 2.5rem !important;\n    background: var(--white) !important;\n    color: var(--primary-color) !important;\n    text-decoration: none !important;\n    border-radius: 50px !important;\n    font-weight: 600 !important;\n    transition: transform 0.3s ease, box-shadow 0.3s ease !important;\n    border: none !important;\n}\n\n.cta-button:hover,\n.wp-block-button__link:hover,\n.button:hover {\n    transform: translateY(-3px) !important;\n    box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;\n}\n\n@keyframes fadeInUp {\n    from {\n        opacity: 0;\n        transform: translateY(30px);\n    }\n    to {\n        opacity: 1;\n        transform: translateY(0);\n    }\n}\n\nbody {\n    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;\n    line-height: 1.6 !important;\n    color: var(--text-dark) !important;\n}\n\nhtml {\n    scroll-behavior: smooth !important;\n}\n\n@media (max-width: 768px) {\n    .hero-section h1,\n    .entry-header h1,\n    .page-header h1 {\n        font-size: 2.5rem !important;\n    }\n}\n\n";
    wp_add_inline_style('wp-block-library', $custom_css);
}
add_action('wp_enqueue_scripts', 'ariajet_add_custom_style');
