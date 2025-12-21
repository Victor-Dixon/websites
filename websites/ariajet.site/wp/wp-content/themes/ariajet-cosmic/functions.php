<?php
/**
 * AriaJet Cosmic Theme Functions
 * 
 * A cosmic adventure-themed WordPress theme for Aria's 2D game showcase.
 * Features animated starfields, neon glow effects, and galaxy-inspired design.
 * 
 * @package AriaJet_Cosmic
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Define Theme Constants
 */
define('ARIAJET_COSMIC_VERSION', '1.0.0');
define('ARIAJET_COSMIC_DIR', get_template_directory());
define('ARIAJET_COSMIC_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function ariajet_cosmic_setup() {
    // Make theme available for translation
    load_theme_textdomain('ariajet-cosmic', ARIAJET_COSMIC_DIR . '/languages');
    
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
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
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    
    // Editor color palette matching theme
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => __('Cosmic Dark', 'ariajet-cosmic'),
            'slug'  => 'cosmic-dark',
            'color' => '#0a0a1a',
        ),
        array(
            'name'  => __('Neon Cyan', 'ariajet-cosmic'),
            'slug'  => 'neon-cyan',
            'color' => '#00fff7',
        ),
        array(
            'name'  => __('Neon Pink', 'ariajet-cosmic'),
            'slug'  => 'neon-pink',
            'color' => '#ff2d95',
        ),
        array(
            'name'  => __('Neon Purple', 'ariajet-cosmic'),
            'slug'  => 'neon-purple',
            'color' => '#bf00ff',
        ),
        array(
            'name'  => __('Neon Blue', 'ariajet-cosmic'),
            'slug'  => 'neon-blue',
            'color' => '#00a8ff',
        ),
        array(
            'name'  => __('Star Gold', 'ariajet-cosmic'),
            'slug'  => 'star-gold',
            'color' => '#ffd700',
        ),
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'ariajet-cosmic'),
        'footer'  => __('Footer Menu', 'ariajet-cosmic'),
        'social'  => __('Social Links', 'ariajet-cosmic'),
    ));
    
    // Set content width
    $GLOBALS['content_width'] = 1280;
}
add_action('after_setup_theme', 'ariajet_cosmic_setup');

/**
 * Enqueue Scripts and Styles
 */
function ariajet_cosmic_scripts() {
    // Theme stylesheet
    wp_enqueue_style(
        'ariajet-cosmic-style',
        get_stylesheet_uri(),
        array(),
        ARIAJET_COSMIC_VERSION
    );
    
    // Games stylesheet
    wp_enqueue_style(
        'ariajet-cosmic-games',
        ARIAJET_COSMIC_URI . '/css/games.css',
        array('ariajet-cosmic-style'),
        ARIAJET_COSMIC_VERSION
    );
    
    // Main JavaScript
    wp_enqueue_script(
        'ariajet-cosmic-main',
        ARIAJET_COSMIC_URI . '/js/main.js',
        array(),
        ARIAJET_COSMIC_VERSION,
        true
    );
    
    // Games JavaScript
    wp_enqueue_script(
        'ariajet-cosmic-games',
        ARIAJET_COSMIC_URI . '/js/games.js',
        array('ariajet-cosmic-main'),
        ARIAJET_COSMIC_VERSION,
        true
    );
    
    // Pass data to JavaScript
    wp_localize_script('ariajet-cosmic-main', 'ariajetCosmic', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'homeUrl' => home_url('/'),
        'i18n'    => array(
            'loading'   => __('Loading...', 'ariajet-cosmic'),
            'error'     => __('Something went wrong!', 'ariajet-cosmic'),
            'playGame'  => __('Play Game', 'ariajet-cosmic'),
        ),
    ));
}
add_action('wp_enqueue_scripts', 'ariajet_cosmic_scripts');

/**
 * Register Game Post Type
 */
function ariajet_cosmic_register_game_post_type() {
    $labels = array(
        'name'               => _x('Games', 'post type general name', 'ariajet-cosmic'),
        'singular_name'      => _x('Game', 'post type singular name', 'ariajet-cosmic'),
        'menu_name'          => _x('Games', 'admin menu', 'ariajet-cosmic'),
        'add_new'            => _x('Add New', 'game', 'ariajet-cosmic'),
        'add_new_item'       => __('Add New Game', 'ariajet-cosmic'),
        'edit_item'          => __('Edit Game', 'ariajet-cosmic'),
        'new_item'           => __('New Game', 'ariajet-cosmic'),
        'view_item'          => __('View Game', 'ariajet-cosmic'),
        'view_items'         => __('View Games', 'ariajet-cosmic'),
        'search_items'       => __('Search Games', 'ariajet-cosmic'),
        'not_found'          => __('No games found', 'ariajet-cosmic'),
        'not_found_in_trash' => __('No games found in Trash', 'ariajet-cosmic'),
        'all_items'          => __('All Games', 'ariajet-cosmic'),
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'show_in_rest'       => true, // Enable Gutenberg
        'query_var'          => true,
        'rewrite'            => array('slug' => 'games', 'with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-games',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
    );
    
    register_post_type('game', $args);
}
add_action('init', 'ariajet_cosmic_register_game_post_type');

/**
 * Register Game Categories Taxonomy
 */
function ariajet_cosmic_register_game_taxonomy() {
    $labels = array(
        'name'              => _x('Game Categories', 'taxonomy general name', 'ariajet-cosmic'),
        'singular_name'     => _x('Game Category', 'taxonomy singular name', 'ariajet-cosmic'),
        'search_items'      => __('Search Categories', 'ariajet-cosmic'),
        'all_items'         => __('All Categories', 'ariajet-cosmic'),
        'parent_item'       => __('Parent Category', 'ariajet-cosmic'),
        'parent_item_colon' => __('Parent Category:', 'ariajet-cosmic'),
        'edit_item'         => __('Edit Category', 'ariajet-cosmic'),
        'update_item'       => __('Update Category', 'ariajet-cosmic'),
        'add_new_item'      => __('Add New Category', 'ariajet-cosmic'),
        'new_item_name'     => __('New Category Name', 'ariajet-cosmic'),
        'menu_name'         => __('Categories', 'ariajet-cosmic'),
    );
    
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'game-category'),
    );
    
    register_taxonomy('game_category', array('game'), $args);
}
add_action('init', 'ariajet_cosmic_register_game_taxonomy');

/**
 * Add Game Meta Boxes
 */
function ariajet_cosmic_add_game_meta_boxes() {
    add_meta_box(
        'ariajet_cosmic_game_details',
        __('Game Details', 'ariajet-cosmic'),
        'ariajet_cosmic_game_details_callback',
        'game',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ariajet_cosmic_add_game_meta_boxes');

/**
 * Game Details Meta Box Callback
 */
function ariajet_cosmic_game_details_callback($post) {
    wp_nonce_field('ariajet_cosmic_save_game_details', 'ariajet_cosmic_game_nonce');
    
    $game_url = get_post_meta($post->ID, '_ariajet_game_url', true);
    $game_type = get_post_meta($post->ID, '_ariajet_game_type', true);
    $game_status = get_post_meta($post->ID, '_ariajet_game_status', true);
    $game_difficulty = get_post_meta($post->ID, '_ariajet_game_difficulty', true);
    ?>
    <table class="form-table ariajet-cosmic-meta-table">
        <tr>
            <th><label for="ariajet_game_url"><?php _e('Game URL', 'ariajet-cosmic'); ?></label></th>
            <td>
                <input type="url" 
                       id="ariajet_game_url" 
                       name="ariajet_game_url" 
                       value="<?php echo esc_attr($game_url); ?>" 
                       class="large-text" 
                       placeholder="https://ariajet.site/games/game-name.html" />
                <p class="description"><?php _e('URL to the game HTML file', 'ariajet-cosmic'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_type"><?php _e('Game Type', 'ariajet-cosmic'); ?></label></th>
            <td>
                <select id="ariajet_game_type" name="ariajet_game_type">
                    <option value=""><?php _e('Select Type', 'ariajet-cosmic'); ?></option>
                    <option value="2d" <?php selected($game_type, '2d'); ?>><?php _e('2D Game', 'ariajet-cosmic'); ?></option>
                    <option value="puzzle" <?php selected($game_type, 'puzzle'); ?>><?php _e('Puzzle', 'ariajet-cosmic'); ?></option>
                    <option value="adventure" <?php selected($game_type, 'adventure'); ?>><?php _e('Adventure', 'ariajet-cosmic'); ?></option>
                    <option value="survival" <?php selected($game_type, 'survival'); ?>><?php _e('Survival', 'ariajet-cosmic'); ?></option>
                    <option value="platformer" <?php selected($game_type, 'platformer'); ?>><?php _e('Platformer', 'ariajet-cosmic'); ?></option>
                    <option value="arcade" <?php selected($game_type, 'arcade'); ?>><?php _e('Arcade', 'ariajet-cosmic'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_status"><?php _e('Status', 'ariajet-cosmic'); ?></label></th>
            <td>
                <select id="ariajet_game_status" name="ariajet_game_status">
                    <option value="published" <?php selected($game_status, 'published'); ?>><?php _e('Published', 'ariajet-cosmic'); ?></option>
                    <option value="beta" <?php selected($game_status, 'beta'); ?>><?php _e('Beta', 'ariajet-cosmic'); ?></option>
                    <option value="development" <?php selected($game_status, 'development'); ?>><?php _e('In Development', 'ariajet-cosmic'); ?></option>
                    <option value="coming-soon" <?php selected($game_status, 'coming-soon'); ?>><?php _e('Coming Soon', 'ariajet-cosmic'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="ariajet_game_difficulty"><?php _e('Difficulty', 'ariajet-cosmic'); ?></label></th>
            <td>
                <select id="ariajet_game_difficulty" name="ariajet_game_difficulty">
                    <option value=""><?php _e('Select Difficulty', 'ariajet-cosmic'); ?></option>
                    <option value="easy" <?php selected($game_difficulty, 'easy'); ?>><?php _e('Easy', 'ariajet-cosmic'); ?></option>
                    <option value="medium" <?php selected($game_difficulty, 'medium'); ?>><?php _e('Medium', 'ariajet-cosmic'); ?></option>
                    <option value="hard" <?php selected($game_difficulty, 'hard'); ?>><?php _e('Hard', 'ariajet-cosmic'); ?></option>
                    <option value="expert" <?php selected($game_difficulty, 'expert'); ?>><?php _e('Expert', 'ariajet-cosmic'); ?></option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Game Meta Data
 */
function ariajet_cosmic_save_game_details($post_id) {
    // Verify nonce
    if (!isset($_POST['ariajet_cosmic_game_nonce']) || 
        !wp_verify_nonce($_POST['ariajet_cosmic_game_nonce'], 'ariajet_cosmic_save_game_details')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save meta fields
    $fields = array(
        'ariajet_game_url'        => '_ariajet_game_url',
        'ariajet_game_type'       => '_ariajet_game_type',
        'ariajet_game_status'     => '_ariajet_game_status',
        'ariajet_game_difficulty' => '_ariajet_game_difficulty',
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
add_action('save_post_game', 'ariajet_cosmic_save_game_details');

/**
 * Add Custom Body Classes
 */
function ariajet_cosmic_body_classes($classes) {
    // Add cosmic theme class
    $classes[] = 'ariajet-cosmic-theme';
    
    // Add game-specific classes
    if (is_post_type_archive('game') || is_singular('game')) {
        $classes[] = 'ariajet-game-page';
    }
    
    // Add animation class
    $classes[] = 'cosmic-animations-enabled';
    
    return $classes;
}
add_filter('body_class', 'ariajet_cosmic_body_classes');

/**
 * Customize Excerpt Length
 */
function ariajet_cosmic_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'ariajet_cosmic_excerpt_length');

/**
 * Customize Excerpt More
 */
function ariajet_cosmic_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'ariajet_cosmic_excerpt_more');

/**
 * Register Widget Areas
 */
function ariajet_cosmic_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'ariajet-cosmic'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here for the sidebar.', 'ariajet-cosmic'),
        'before_widget' => '<section id="%1$s" class="widget cosmic-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Widgets', 'ariajet-cosmic'),
        'id'            => 'footer-widgets',
        'description'   => __('Add widgets here for the footer area.', 'ariajet-cosmic'),
        'before_widget' => '<section id="%1$s" class="widget cosmic-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'ariajet_cosmic_widgets_init');

/**
 * Add Admin Styles for Meta Boxes
 */
function ariajet_cosmic_admin_styles() {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'game') {
        echo '<style>
            .ariajet-cosmic-meta-table th { width: 150px; }
            .ariajet-cosmic-meta-table select { min-width: 200px; }
        </style>';
    }
}
add_action('admin_head', 'ariajet_cosmic_admin_styles');
