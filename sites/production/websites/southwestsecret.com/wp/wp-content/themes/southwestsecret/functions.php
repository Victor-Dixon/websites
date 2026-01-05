<?php
/**
 * SouthWest Secret Theme Functions
 * 
 * @package SouthWestSecret
 * @version 1.0.0
 */

// Theme setup
function southwestsecret_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'southwestsecret'),
    ));
}
add_action('after_setup_theme', 'southwestsecret_setup');

// Enqueue styles and scripts
function southwestsecret_scripts() {
    // Main stylesheet
    wp_enqueue_style('southwestsecret-style', get_template_directory_uri() . '/css/style.css', array(), '1.0.0');
    
    // Google Fonts
    wp_enqueue_style('southwestsecret-fonts', 'https://fonts.googleapis.com/css2?family=Rubik+Doodle+Shadow&family=Permanent+Marker&family=Rubik+Bubbles&display=swap', array(), null);
    
    // Main JavaScript
    wp_enqueue_script('southwestsecret-script', get_template_directory_uri() . '/js/script.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'southwestsecret_scripts');

// Hide "Hello world!" default post
function southwestsecret_hide_default_post($query) {
    if ($query->is_home() && $query->is_main_query()) {
        $query->set('post__not_in', array(1)); // Hide post ID 1 (default "Hello world!" post)
    }
}
add_action('pre_get_posts', 'southwestsecret_hide_default_post');

// Custom post type for Screw Tapes (optional for future expansion)
function southwestsecret_register_tape_post_type() {
    $labels = array(
        'name' => 'Screw Tapes',
        'singular_name' => 'Screw Tape',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Screw Tape',
        'edit_item' => 'Edit Screw Tape',
        'new_item' => 'New Screw Tape',
        'view_item' => 'View Screw Tape',
        'search_items' => 'Search Screw Tapes',
        'not_found' => 'No screw tapes found',
        'not_found_in_trash' => 'No screw tapes found in trash',
        'menu_name' => 'Screw Tapes'
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-audio',
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'tapes'),
    );
    
    register_post_type('screw_tape', $args);
}
add_action('init', 'southwestsecret_register_tape_post_type');

// Add meta box for YouTube ID
function southwestsecret_add_youtube_meta_box() {
    add_meta_box(
        'southwestsecret_youtube',
        'YouTube Video ID',
        'southwestsecret_youtube_meta_box_callback',
        'screw_tape',
        'side'
    );
}
add_action('add_meta_boxes', 'southwestsecret_add_youtube_meta_box');

function southwestsecret_youtube_meta_box_callback($post) {
    wp_nonce_field('southwestsecret_save_youtube', 'southwestsecret_youtube_nonce');
    $value = get_post_meta($post->ID, '_youtube_id', true);
    echo '<input type="text" name="youtube_id" value="' . esc_attr($value) . '" placeholder="e.g., oYqlfb2sghc" style="width:100%;" />';
}

function southwestsecret_save_youtube_meta($post_id) {
    if (!isset($_POST['southwestsecret_youtube_nonce'])) return;
    if (!wp_verify_nonce($_POST['southwestsecret_youtube_nonce'], 'southwestsecret_save_youtube')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    if (isset($_POST['youtube_id'])) {
        update_post_meta($post_id, '_youtube_id', sanitize_text_field($_POST['youtube_id']));
    }
}
add_action('save_post', 'southwestsecret_save_youtube_meta');

// Create Aria page on theme activation
function southwestsecret_create_aria_page() {
    if (get_page_by_path('aria')) {
        return; // Page already exists
    }
    
    $aria_page = array(
        'post_title'    => 'Aria',
        'post_name'     => 'aria',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-aria.php'
    );
    
    wp_insert_post($aria_page);
}

// Run on theme activation
add_action('after_switch_theme', 'southwestsecret_create_aria_page');

// Create Carmyn page on theme activation
function southwestsecret_create_carmyn_page() {
    if (get_page_by_path('carmyn')) {
        return; // Page already exists
    }
    
    $carmyn_page = array(
        'post_title'    => 'Carmyn',
        'post_name'     => 'carmyn',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-carmyn.php'
    );
    
    wp_insert_post($carmyn_page);
}

// Run on theme activation
add_action('after_switch_theme', 'southwestsecret_create_carmyn_page');

// Add Aria, Carmyn, and Invitation links to navigation menu
function southwestsecret_add_artist_menu_items($items, $args) {
    // Only add to primary menu
    if ($args->theme_location == 'primary') {
        // Get Aria page URL
        $aria_page = get_page_by_path('aria');
        if ($aria_page) {
            $aria_url = get_permalink($aria_page->ID);
        } else {
            $aria_url = home_url('/aria');
        }
        
        // Get Carmyn page URL
        $carmyn_page = get_page_by_path('carmyn');
        if ($carmyn_page) {
            $carmyn_url = get_permalink($carmyn_page->ID);
        } else {
            $carmyn_url = home_url('/carmyn');
        }
        
        // Get Invitation page URL
        $invitation_page = get_page_by_path('invitation');
        if ($invitation_page) {
            $invitation_url = get_permalink($invitation_page->ID);
        } else {
            $invitation_url = home_url('/invitation');
        }
        
        $aria_item = '<li><a href="' . esc_url($aria_url) . '">Aria</a></li>';
        $carmyn_item = '<li><a href="' . esc_url($carmyn_url) . '">Carmyn</a></li>';
        $invitation_item = '<li><a href="' . esc_url($invitation_url) . '">Invitation</a></li>';
        
        // Find Submit link and insert Aria right after it, then Carmyn, then Invitation
        $submit_pos = stripos($items, '>Submit<');
        if ($submit_pos !== false) {
            // Find the closing </a></li> after Submit
            $after_submit = strpos($items, '</a></li>', $submit_pos);
            if ($after_submit !== false) {
                $after_submit += 9; // Length of '</a></li>'
                $items = substr($items, 0, $after_submit) . $aria_item . $carmyn_item . $invitation_item . substr($items, $after_submit);
            }
        } else {
            // If no Submit link found, just append to end
            $items .= $aria_item . $carmyn_item . $invitation_item;
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'southwestsecret_add_artist_menu_items', 10, 2);

// ============================================
// GUESTBOOK FUNCTIONALITY
// ============================================

// Create guestbook database table on theme activation
function southwestsecret_create_guestbook_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'guestbook_entries';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        guest_name varchar(100) NOT NULL,
        message text NOT NULL,
        status varchar(20) DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'southwestsecret_create_guestbook_table');

// Handle guestbook form submission
function southwestsecret_handle_guestbook_submission() {
    // Verify nonce
    if (!isset($_POST['guestbook_nonce']) || !wp_verify_nonce($_POST['guestbook_nonce'], 'guestbook_submit')) {
        wp_die('Security check failed');
    }
    
    // Sanitize input
    $guest_name = sanitize_text_field($_POST['guest_name']);
    $message = sanitize_textarea_field($_POST['guest_message']);
    
    // Validate input
    if (empty($guest_name) || empty($message)) {
        echo 'error';
        wp_die();
    }
    
    // Insert into database
    global $wpdb;
    $table_name = $wpdb->prefix . 'guestbook_entries';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'guest_name' => $guest_name,
            'message' => $message,
            'status' => 'pending'
        ),
        array('%s', '%s', '%s')
    );
    
    if ($result) {
        echo 'success';
    } else {
        echo 'error';
    }
    
    wp_die();
}
add_action('admin_post_submit_guestbook_entry', 'southwestsecret_handle_guestbook_submission');
add_action('admin_post_nopriv_submit_guestbook_entry', 'southwestsecret_handle_guestbook_submission');

// Add admin menu for guestbook management
function southwestsecret_guestbook_admin_menu() {
    add_menu_page(
        'Guestbook',
        'Guestbook',
        'manage_options',
        'guestbook',
        'southwestsecret_guestbook_admin_page',
        'dashicons-format-chat',
        30
    );
}
add_action('admin_menu', 'southwestsecret_guestbook_admin_menu');

// Guestbook admin page
function southwestsecret_guestbook_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'guestbook_entries';
    
    // Handle approve/reject actions
    if (isset($_GET['action']) && isset($_GET['entry_id']) && check_admin_referer('guestbook_action')) {
        $entry_id = intval($_GET['entry_id']);
        $action = $_GET['action'];
        
        if ($action === 'approve') {
            $wpdb->update(
                $table_name,
                array('status' => 'approved'),
                array('id' => $entry_id),
                array('%s'),
                array('%d')
            );
            echo '<div class="notice notice-success"><p>Message approved!</p></div>';
        } elseif ($action === 'reject') {
            $wpdb->update(
                $table_name,
                array('status' => 'rejected'),
                array('id' => $entry_id),
                array('%s'),
                array('%d')
            );
            echo '<div class="notice notice-success"><p>Message rejected.</p></div>';
        } elseif ($action === 'delete') {
            $wpdb->delete($table_name, array('id' => $entry_id), array('%d'));
            echo '<div class="notice notice-success"><p>Message deleted.</p></div>';
        }
    }
    
    // Get all entries
    $entries = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    
    ?>
    <div class="wrap">
        <h1>Guestbook Management</h1>
        <p>Review and approve birthday messages from visitors.</p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entries) : ?>
                    <?php foreach ($entries as $entry) : ?>
                        <tr>
                            <td><?php echo $entry->id; ?></td>
                            <td><strong><?php echo esc_html($entry->guest_name); ?></strong></td>
                            <td><?php echo esc_html(wp_trim_words($entry->message, 20)); ?></td>
                            <td>
                                <span class="status-<?php echo esc_attr($entry->status); ?>">
                                    <?php echo ucfirst($entry->status); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($entry->created_at)); ?></td>
                            <td>
                                <?php if ($entry->status === 'pending') : ?>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=guestbook&action=approve&entry_id=' . $entry->id), 'guestbook_action'); ?>" class="button button-primary">Approve</a>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=guestbook&action=reject&entry_id=' . $entry->id), 'guestbook_action'); ?>" class="button">Reject</a>
                                <?php endif; ?>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=guestbook&action=delete&entry_id=' . $entry->id), 'guestbook_action'); ?>" class="button button-link-delete" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6">No guestbook entries yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <style>
        .status-pending { color: #ffa500; font-weight: bold; }
        .status-approved { color: #00ff00; font-weight: bold; }
        .status-rejected { color: #ff0000; font-weight: bold; }
    </style>
    <?php
}

// Create Guestbook page on theme activation
function southwestsecret_create_guestbook_page() {
    if (get_page_by_path('guestbook')) {
        return; // Page already exists
    }
    
    $guestbook_page = array(
        'post_title'    => 'Guestbook',
        'post_name'     => 'guestbook',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-guestbook.php'
    );
    
    wp_insert_post($guestbook_page);
}
add_action('after_switch_theme', 'southwestsecret_create_guestbook_page');

// Create Birthday Fun page on theme activation
function southwestsecret_create_birthday_fun_page() {
    if (get_page_by_path('birthday-fun')) {
        return; // Page already exists
    }
    
    $birthday_fun_page = array(
        'post_title'    => 'Birthday Fun',
        'post_name'     => 'birthday-fun',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-birthday-fun.php'
    );
    
    wp_insert_post($birthday_fun_page);
}
add_action('after_switch_theme', 'southwestsecret_create_birthday_fun_page');

// Create Invitation page on theme activation
function southwestsecret_create_invitation_page() {
    if (get_page_by_path('invitation')) {
        return; // Page already exists
    }
    
    $invitation_page = array(
        'post_title'    => 'Birthday Invitation',
        'post_name'     => 'invitation',
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'page_template' => 'page-invitation.php'
    );
    
    wp_insert_post($invitation_page);
}
add_action('after_switch_theme', 'southwestsecret_create_invitation_page');

// ============================================
// FUTURE BLOG STRUCTURE (Not implemented yet)
// ============================================

// Register blog post type for future use (commented out until needed)
/*
function southwestsecret_register_blog_post_type() {
    $labels = array(
        'name' => 'Blog Posts',
        'singular_name' => 'Blog Post',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Blog Post',
        'edit_item' => 'Edit Blog Post',
        'new_item' => 'New Blog Post',
        'view_item' => 'View Blog Post',
        'search_items' => 'Search Blog Posts',
        'not_found' => 'No blog posts found',
        'not_found_in_trash' => 'No blog posts found in trash',
        'menu_name' => 'Blog'
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-edit',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'rewrite' => array('slug' => 'blog'),
    );
    
    register_post_type('blog_post', $args);
}
// add_action('init', 'southwestsecret_register_blog_post_type'); // Uncomment when ready to implement blog
*/


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

