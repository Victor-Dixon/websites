<?php
/**
 * FreeRideInvestor V2 Theme Functions
 *
 * @package FreeRideInvestor_V2
 * @version 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function freerideinvestor_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Enable support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Register navigation menu
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'freerideinvestor'),
        'footer'  => __('Footer Menu', 'freerideinvestor'),
    ));

    // Enable support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Enable support for custom background
    add_theme_support('custom-background', array(
        'default-color' => 'f8f9fa',
    ));

    // Enable support for custom header
    add_theme_support('custom-header', array(
        'default-image' => '',
        'width'         => 1200,
        'height'        => 400,
        'flex-width'    => true,
        'flex-height'   => true,
    ));
}
add_action('after_setup_theme', 'freerideinvestor_setup');

/**
 * Enqueue scripts and styles
 */
function freerideinvestor_scripts() {
    // Theme stylesheet
    wp_enqueue_style('freerideinvestor-style', get_stylesheet_uri(), array(), '2.0.0');

    // Google Fonts
    wp_enqueue_style('freerideinvestor-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', array(), null);

    // Theme JavaScript
    wp_enqueue_script('freerideinvestor-script', get_template_directory_uri() . '/js/theme.js', array('jquery'), '2.0.0', true);

    // Localize script for AJAX
    wp_localize_script('freerideinvestor-script', 'freerideinvestor_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('freerideinvestor_ajax_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'freerideinvestor_scripts');

/**
 * Register widget areas
 */
function freerideinvestor_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'freerideinvestor'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here to appear in your sidebar.', 'freerideinvestor'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'freerideinvestor'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets here to appear in your footer.', 'freerideinvestor'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'freerideinvestor_widgets_init');

/**
 * Custom excerpt length
 */
function freerideinvestor_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'freerideinvestor_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function freerideinvestor_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'freerideinvestor_excerpt_more');

/**
 * Add custom classes to body
 */
function freerideinvestor_body_classes($classes) {
    // Add class if sidebar is active
    if (is_active_sidebar('sidebar-1')) {
        $classes[] = 'has-sidebar';
    }

    // Add class for page templates
    if (is_page_template()) {
        $classes[] = 'page-template';
    }

    return $classes;
}
add_filter('body_class', 'freerideinvestor_body_classes');

/**
 * Custom navigation menu walker to remove duplicate links
 */
class FreeRideInvestor_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $class_names = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * Remove duplicate navigation menu items (improved)
 */
function freerideinvestor_remove_duplicate_menu_items($items, $args) {
    if (empty($items)) {
        return $items;
    }

    $seen = array();
    $filtered = array();

    foreach ($items as $key => $item) {
        // Check by both URL and title to catch duplicates
        $identifier = $item->url . '|' . $item->title;
        
        if (!isset($seen[$identifier])) {
            $seen[$identifier] = true;
            $filtered[] = $item;
        }
    }

    return $filtered;
}
add_filter('wp_nav_menu_objects', 'freerideinvestor_remove_duplicate_menu_items', 10, 2);

/**
 * Create Daily Plans category on theme activation
 */
function freerideinvestor_create_daily_plans_category() {
    if (!term_exists('daily-plans', 'category')) {
        wp_insert_term(
            'Daily Plans',
            'category',
            array(
                'description' => 'Daily trading plans and journal entries',
                'slug' => 'daily-plans'
            )
        );
    }
}
add_action('after_setup_theme', 'freerideinvestor_create_daily_plans_category');

/**
 * Hide SEO drafting blocks from public view
 */
function freerideinvestor_hide_seo_blocks($content) {
    // Remove common SEO drafting patterns
    $patterns = array(
        '/Target Keywords:.*?(\n|$)/i',
        '/Meta Description:.*?(\n|$)/i',
        '/Schema:.*?(\n|$)/i',
        '/SEO Notes:.*?(\n|$)/i',
        '/<p>Target Keywords:.*?<\/p>/i',
        '/<p>Meta Description:.*?<\/p>/i',
    );
    
    foreach ($patterns as $pattern) {
        $content = preg_replace($pattern, '', $content);
    }
    
    return $content;
}
add_filter('the_content', 'freerideinvestor_hide_seo_blocks', 20);

/**
 * Custom comment form
 */
function freerideinvestor_comment_form_defaults($defaults) {
    $defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . _x('Comment', 'noun') . '</label><br /><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>';
    $defaults['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />';
    $defaults['submit_field'] = '<p class="form-submit">%1$s %2$s</p>';

    return $defaults;
}
add_filter('comment_form_defaults', 'freerideinvestor_comment_form_defaults');

/**
 * Security enhancements
 */
function freerideinvestor_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'freerideinvestor_security_headers');

/**
 * Performance optimizations
 */
function freerideinvestor_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'freerideinvestor_disable_emojis');

/**
 * Remove query strings from static resources
 */
function freerideinvestor_remove_query_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'freerideinvestor_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'freerideinvestor_remove_query_strings', 15, 1);

/**
 * Enable shortcodes in widgets
 */
add_filter('widget_text', 'do_shortcode');

/**
 * Custom login logo
 */
function freerideinvestor_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/login-logo.png);
            height: 100px;
            width: 320px;
            background-size: contain;
            background-repeat: no-repeat;
            padding-bottom: 30px;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'freerideinvestor_login_logo');

/**
 * =============================================================================
 * MEMBERSHIP & PAYWALL SYSTEM
 * =============================================================================
 */

/**
 * Membership Levels
 */
define('MEMBERSHIP_FREE', 'free');
define('MEMBERSHIP_BASIC', 'basic');
define('MEMBERSHIP_PREMIUM', 'premium');
define('MEMBERSHIP_PRO', 'pro');

/**
 * Initialize Membership System
 */
function freerideinvestor_membership_init() {
    // Add membership capabilities
    freerideinvestor_add_membership_roles();

    // Register membership pages
    freerideinvestor_register_membership_pages();

    // Add membership meta boxes
    add_action('add_meta_boxes', 'freerideinvestor_add_membership_meta_boxes');

    // Save membership restrictions
    add_action('save_post', 'freerideinvestor_save_membership_restrictions');

    // Check content access
    add_action('wp', 'freerideinvestor_check_content_access');

    // Add membership scripts/styles
    add_action('wp_enqueue_scripts', 'freerideinvestor_membership_scripts');

    // AJAX handlers for membership
    add_action('wp_ajax_freerideinvestor_upgrade_membership', 'freerideinvestor_upgrade_membership_ajax');
    add_action('wp_ajax_nopriv_freerideinvestor_upgrade_membership', 'freerideinvestor_upgrade_membership_ajax');
}
add_action('init', 'freerideinvestor_membership_init');

/**
 * Add Membership User Roles
 */
function freerideinvestor_add_membership_roles() {
    // Free Member (default)
    add_role(
        'free_member',
        __('Free Member', 'freerideinvestor'),
        array(
            'read' => true,
            'level_0' => true,
        )
    );

    // Basic Member
    add_role(
        'basic_member',
        __('Basic Member', 'freerideinvestor'),
        array(
            'read' => true,
            'level_1' => true,
        )
    );

    // Premium Member
    add_role(
        'premium_member',
        __('Premium Member', 'freerideinvestor'),
        array(
            'read' => true,
            'level_2' => true,
        )
    );

    // Pro Member
    add_role(
        'pro_member',
        __('Pro Member', 'freerideinvestor'),
        array(
            'read' => true,
            'level_3' => true,
        )
    );
}

/**
 * Register Membership Pages
 */
function freerideinvestor_register_membership_pages() {
    // Register membership page templates
    $membership_pages = array(
        'page-membership.php' => 'Membership',
        'page-login.php' => 'Login',
        'page-register.php' => 'Register',
        'page-account.php' => 'My Account',
        'page-pricing.php' => 'Pricing',
        'page-payment.php' => 'Payment',
        'page-thank-you.php' => 'Thank You',
    );

    foreach ($membership_pages as $template => $title) {
        if (!get_page_by_title($title)) {
            wp_insert_post(array(
                'post_title' => $title,
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'meta_input' => array(
                    '_wp_page_template' => $template,
                ),
            ));
        }
    }
}

/**
 * Get User Membership Level
 */
function freerideinvestor_get_user_membership($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return MEMBERSHIP_FREE;
    }

    $user = get_userdata($user_id);
    $roles = $user->roles;

    // Determine membership level from roles
    if (in_array('pro_member', $roles)) {
        return MEMBERSHIP_PRO;
    } elseif (in_array('premium_member', $roles)) {
        return MEMBERSHIP_PREMIUM;
    } elseif (in_array('basic_member', $roles)) {
        return MEMBERSHIP_BASIC;
    } else {
        return MEMBERSHIP_FREE;
    }
}

/**
 * Get Membership Level Hierarchy
 */
function freerideinvestor_get_membership_hierarchy() {
    return array(
        MEMBERSHIP_FREE => array(
            'name' => 'Free',
            'level' => 0,
            'price' => 0,
            'features' => array(
                'Basic trading strategies',
                'Community access',
                'Educational content',
            ),
        ),
        MEMBERSHIP_BASIC => array(
            'name' => 'Basic',
            'level' => 1,
            'price' => 29.99,
            'features' => array(
                'Advanced strategies',
                'Real-time alerts',
                'Performance reports',
                'Email support',
            ),
        ),
        MEMBERSHIP_PREMIUM => array(
            'name' => 'Premium',
            'level' => 2,
            'price' => 79.99,
            'features' => array(
                'Institutional strategies',
                'Portfolio management',
                'Priority support',
                'API access',
                'Custom alerts',
            ),
        ),
        MEMBERSHIP_PRO => array(
            'name' => 'Pro',
            'level' => 3,
            'price' => 199.99,
            'features' => array(
                'All Premium features',
                'Direct strategy access',
                'Personal account manager',
                'Custom strategy development',
                'White-label solutions',
            ),
        ),
    );
}

/**
 * Check if User Can Access Content
 */
function freerideinvestor_can_access_content($required_level, $user_id = null) {
    $user_level = freerideinvestor_get_user_membership($user_id);
    $hierarchy = freerideinvestor_get_membership_hierarchy();

    $user_level_num = $hierarchy[$user_level]['level'];
    $required_level_num = $hierarchy[$required_level]['level'];

    return $user_level_num >= $required_level_num;
}

/**
 * Add Membership Meta Boxes
 */
function freerideinvestor_add_membership_meta_boxes() {
    add_meta_box(
        'freerideinvestor_membership',
        'Membership Restrictions',
        'freerideinvestor_membership_meta_box_callback',
        array('post', 'page'),
        'side',
        'default'
    );
}

/**
 * Membership Meta Box Callback
 */
function freerideinvestor_membership_meta_box_callback($post) {
    wp_nonce_field('freerideinvestor_membership_nonce', 'freerideinvestor_membership_nonce');

    $restricted_level = get_post_meta($post->ID, '_freerideinvestor_restricted_level', true);
    $hierarchy = freerideinvestor_get_membership_hierarchy();

    echo '<p><strong>Restrict access to:</strong></p>';
    echo '<select name="freerideinvestor_restricted_level" style="width: 100%;">';
    echo '<option value="">Everyone (no restriction)</option>';

    foreach ($hierarchy as $level => $data) {
        $selected = ($restricted_level === $level) ? 'selected' : '';
        echo "<option value=\"$level\" $selected>{$data['name']} Members & Above</option>";
    }

    echo '</select>';
    echo '<p><small>Content will be hidden behind a paywall for users below this membership level.</small></p>';
}

/**
 * Save Membership Restrictions
 */
function freerideinvestor_save_membership_restrictions($post_id) {
    if (!isset($_POST['freerideinvestor_membership_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['freerideinvestor_membership_nonce'], 'freerideinvestor_membership_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['freerideinvestor_restricted_level'])) {
        update_post_meta($post_id, '_freerideinvestor_restricted_level', sanitize_text_field($_POST['freerideinvestor_restricted_level']));
    }
}

/**
 * Check Content Access on Page Load
 */
function freerideinvestor_check_content_access() {
    if (is_admin() || !is_singular()) {
        return;
    }

    global $post;
    if (!$post) {
        return;
    }

    $restricted_level = get_post_meta($post->ID, '_freerideinvestor_restricted_level', true);

    if ($restricted_level && !freerideinvestor_can_access_content($restricted_level)) {
        // Show paywall instead of content
        add_filter('the_content', 'freerideinvestor_paywall_content');
        add_filter('the_title', 'freerideinvestor_paywall_title');
    }
}

/**
 * Paywall Content Filter
 */
function freerideinvestor_paywall_content($content) {
    $user_membership = freerideinvestor_get_user_membership();
    $hierarchy = freerideinvestor_get_membership_hierarchy();
    $current_level = $hierarchy[$user_membership];
    $restricted_level = get_post_meta(get_the_ID(), '_freerideinvestor_restricted_level', true);
    $required_level = $hierarchy[$restricted_level];

    ob_start();
    ?>
    <div class="freerideinvestor-paywall">
        <div class="paywall-header">
            <h2>🚫 Premium Content</h2>
            <p>This content requires a <strong><?php echo esc_html($required_level['name']); ?> Membership</strong> or higher.</p>
        </div>

        <div class="paywall-content">
            <div class="current-membership">
                <h3>Your Current Membership</h3>
                <div class="membership-badge membership-<?php echo esc_attr($user_membership); ?>">
                    <?php echo esc_html($current_level['name']); ?> Member
                </div>
            </div>

            <div class="upgrade-options">
                <h3>Upgrade to Access This Content</h3>
                <div class="pricing-cards">
                    <?php if ($user_membership !== MEMBERSHIP_PREMIUM && $user_membership !== MEMBERSHIP_PRO): ?>
                    <div class="pricing-card recommended">
                        <h4><?php echo esc_html($required_level['name']); ?> Membership</h4>
                        <div class="price">$<?php echo esc_html($required_level['price']); ?><span>/month</span></div>
                        <ul>
                            <?php foreach ($required_level['features'] as $feature): ?>
                            <li><?php echo esc_html($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="/pricing" class="btn btn-primary">Upgrade Now</a>
                    </div>
                    <?php endif; ?>

                    <div class="pricing-card">
                        <h4>Compare All Plans</h4>
                        <p>See all membership options and features</p>
                        <a href="/pricing" class="btn btn-secondary">View Pricing</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .freerideinvestor-paywall {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: #f8fafc;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }

    .paywall-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .paywall-header h2 {
        color: #e53e3e;
        margin-bottom: 1rem;
    }

    .current-membership {
        text-align: center;
        margin-bottom: 2rem;
    }

    .membership-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        color: white;
    }

    .membership-free { background: #a0aec0; }
    .membership-basic { background: #4299e1; }
    .membership-premium { background: #48bb78; }
    .membership-pro { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

    .pricing-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .pricing-card {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }

    .pricing-card.recommended {
        border: 3px solid #48bb78;
        position: relative;
    }

    .pricing-card.recommended::before {
        content: 'RECOMMENDED';
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: #48bb78;
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .pricing-card h4 {
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #48bb78;
        margin-bottom: 1rem;
    }

    .price span {
        font-size: 1rem;
        color: #718096;
    }

    .pricing-card ul {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
        text-align: left;
    }

    .pricing-card li {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f7fafc;
        color: #4a5568;
    }

    .pricing-card li:last-child {
        border-bottom: none;
    }
    </style>
    <?php

    return ob_get_clean();
}

/**
 * Paywall Title Filter
 */
function freerideinvestor_paywall_title($title) {
    $restricted_level = get_post_meta(get_the_ID(), '_freerideinvestor_restricted_level', true);

    if ($restricted_level) {
        return '🔒 ' . $title . ' (Premium Content)';
    }

    return $title;
}

/**
 * Add Membership Scripts
 */
function freerideinvestor_membership_scripts() {
    wp_enqueue_script(
        'freerideinvestor-membership',
        get_template_directory_uri() . '/js/membership.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script('freerideinvestor-membership', 'freerideinvestor_membership', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('freerideinvestor_membership_nonce'),
        'user_membership' => freerideinvestor_get_user_membership(),
        'membership_levels' => freerideinvestor_get_membership_hierarchy(),
    ));
}

/**
 * AJAX Handler for Membership Upgrade
 */
function freerideinvestor_upgrade_membership_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'freerideinvestor_membership_nonce')) {
        wp_die('Security check failed');
    }

    $user_id = get_current_user_id();
    $new_level = sanitize_text_field($_POST['membership_level']);

    if (!$user_id) {
        wp_send_json_error('User not logged in');
        return;
    }

    // Update user role based on membership level
    $user = new WP_User($user_id);
    $user->set_role(freerideinvestor_get_role_for_membership($new_level));

    // Store membership level in user meta
    update_user_meta($user_id, 'freerideinvestor_membership_level', $new_level);
    update_user_meta($user_id, 'freerideinvestor_membership_start', current_time('mysql'));

    wp_send_json_success(array(
        'message' => 'Membership upgraded successfully',
        'new_level' => $new_level,
    ));
}

/**
 * Get WordPress Role for Membership Level
 */
function freerideinvestor_get_role_for_membership($membership_level) {
    $role_map = array(
        MEMBERSHIP_FREE => 'subscriber',
        MEMBERSHIP_BASIC => 'basic_member',
        MEMBERSHIP_PREMIUM => 'premium_member',
        MEMBERSHIP_PRO => 'pro_member',
    );

    return isset($role_map[$membership_level]) ? $role_map[$membership_level] : 'subscriber';
}

/**
 * Shortcode for Membership Content
 */
function freerideinvestor_membership_content_shortcode($atts, $content = '') {
    $atts = shortcode_atts(array(
        'level' => MEMBERSHIP_FREE,
        'message' => 'This content requires a higher membership level.',
    ), $atts);

    if (freerideinvestor_can_access_content($atts['level'])) {
        return do_shortcode($content);
    } else {
        return '<div class="membership-required">' . esc_html($atts['message']) . ' <a href="/pricing">Upgrade Now</a></div>';
    }
}
add_shortcode('membership_content', 'freerideinvestor_membership_content_shortcode');

/**
 * Display User Membership Status
 */
function freerideinvestor_display_membership_status() {
    if (!is_user_logged_in()) {
        return;
    }

    $user_membership = freerideinvestor_get_user_membership();
    $hierarchy = freerideinvestor_get_membership_hierarchy();
    $current = $hierarchy[$user_membership];

    ob_start();
    ?>
    <div class="user-membership-status">
        <div class="membership-badge membership-<?php echo esc_attr($user_membership); ?>">
            <?php echo esc_html($current['name']); ?> Member
        </div>
        <?php if ($user_membership !== MEMBERSHIP_PRO): ?>
        <a href="/pricing" class="upgrade-link">Upgrade</a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Remove the temporary autologin system
 */
function freerideinvestor_disable_autologin() {
    // Remove any autologin functionality
    remove_action('init', 'some_autologin_function'); // Replace with actual function name if exists

    // Add security headers
    if (!is_admin()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('init', 'freerideinvestor_disable_autologin');
