<?php
/**
 * Swarm Core Theme Functions
 * Core functionality for Swarm Intelligence Platform
 */

function swarm_core_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'swarm-core-theme'),
        'footer' => __('Footer Menu', 'swarm-core-theme'),
    ));
}
add_action('after_setup_theme', 'swarm_core_theme_setup');

function swarm_core_theme_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('swarm-core-style', get_stylesheet_uri(), array(), '2.0.0');

    // Enqueue Google Fonts (Inter)
    wp_enqueue_style('inter-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    // Enqueue main JavaScript
    wp_enqueue_script('swarm-core-js', get_template_directory_uri() . '/js/swarm-core.js', array('jquery'), '2.0.0', true);

    // Add theme version to prevent caching issues
    wp_localize_script('swarm-core-js', 'swarmTheme', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('swarm_theme_nonce'),
        'theme_url' => get_template_directory_uri()
    ));
}
add_action('wp_enqueue_scripts', 'swarm_core_theme_scripts');

// Custom post types for swarm intelligence
function swarm_register_post_types() {
    register_post_type('swarm_agent', array(
        'labels' => array(
            'name' => __('Swarm Agents', 'swarm-core-theme'),
            'singular_name' => __('Swarm Agent', 'swarm-core-theme'),
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-groups',
        'show_in_rest' => true,
    ));

    register_post_type('swarm_project', array(
        'labels' => array(
            'name' => __('Swarm Projects', 'swarm-core-theme'),
            'singular_name' => __('Swarm Project', 'swarm-core-theme'),
        ),
        'public' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'menu_icon' => 'dashicons-portfolio',
        'show_in_rest' => true,
    ));
}
add_action('init', 'swarm_register_post_types');

// Swarm intelligence shortcodes
function swarm_agent_grid_shortcode($atts) {
    $atts = shortcode_atts(array('limit' => 6), $atts);
    $agents = get_posts(array(
        'post_type' => 'swarm_agent',
        'posts_per_page' => $atts['limit']
    ));

    ob_start();
    ?>
    <div class="swarm-agent-grid">
        <?php foreach ($agents as $agent): ?>
            <div class="agent-card">
                <?php if (has_post_thumbnail($agent->ID)): ?>
                    <div class="agent-thumbnail">
                        <?php echo get_the_post_thumbnail($agent->ID, 'medium'); ?>
                    </div>
                <?php endif; ?>
                <h3><?php echo get_the_title($agent->ID); ?></h3>
                <div class="agent-excerpt">
                    <?php echo get_the_excerpt($agent->ID); ?>
                </div>
                <a href="<?php echo get_permalink($agent->ID); ?>" class="agent-link">Learn More</a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('swarm_agents', 'swarm_agent_grid_shortcode');