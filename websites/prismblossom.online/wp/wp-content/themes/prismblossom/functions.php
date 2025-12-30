<?php

/**
 * PrismBlossom Theme Functions
 * 
 * @package PrismBlossom
 * @version 1.0.0
 */

// Theme setup
function prismblossom_setup()
{
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'prismblossom'),
    ));
}
add_action('after_setup_theme', 'prismblossom_setup');

// Enqueue styles and scripts
function prismblossom_scripts()
{
    // Main stylesheet (theme root style.css)
    wp_enqueue_style('prismblossom-style', get_stylesheet_uri(), array(), '1.0.0');

    // Google Fonts with fallback
    wp_enqueue_style('prismblossom-fonts', 'https://fonts.googleapis.com/css2?family=Rubik+Doodle+Shadow&family=Permanent+Marker&family=Rubik+Bubbles&display=swap', array(), null);

    // jQuery (WordPress includes it, but ensure it's available)
    wp_enqueue_script('jquery');

    // Main JavaScript
    wp_enqueue_script('prismblossom-script', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.0.0', true);

    // Localize script for AJAX
    wp_localize_script('prismblossom-script', 'prismblossomAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('prismblossom_nonce')
    ));

    // Add purple background to homepage
    $purple_bg_css = "
        html { background: #bf00ff !important; height: 100% !important; }
        body { 
            background: #bf00ff !important;
            background: linear-gradient(135deg, #bf00ff 0%, #9d00ff 50%, #7d00ff 100%) !important;
            background-attachment: fixed !important;
            min-height: 100vh !important;
        }
        body.page-template-page-carmyn,
        body.page-template-page-guestbook,
        body.page-template-page-invitation,
        body.page-template-page-birthday-fun {
            background: inherit !important;
        }
    ";
    wp_add_inline_style('southwestsecret-style', $purple_bg_css);

    // Add inline CSS for text rendering fixes - Enhanced to fix spacing issues
    $text_rendering_css = "
        body, body * {
            font-family: 'Rubik Bubbles', 'Arial', 'Helvetica Neue', 'Helvetica', sans-serif !important;
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
            text-size-adjust: 100% !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
            font-feature-settings: 'liga' 0 !important;
            font-variant-ligatures: none !important;
            font-variant: normal !important;
        }
        /* Fix for specific text rendering issues */
        h1, h2, h3, h4, h5, h6, p, span, div, a, label, button {
            letter-spacing: 0 !important;
            word-spacing: 0.1em !important;
            font-feature-settings: 'liga' 0 !important;
            font-variant-ligatures: none !important;
        }
        /* Ensure no font loading causes spacing issues */
        @font-face {
            font-display: swap;
        }
    ";
    wp_add_inline_style('prismblossom-style', $text_rendering_css);
}
add_action('wp_enqueue_scripts', 'prismblossom_scripts');

// Custom post type for Screw Tapes (optional for future expansion)
function prismblossom_register_tape_post_type()
{
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
add_action('init', 'prismblossom_register_tape_post_type');

// Add meta box for YouTube ID
function prismblossom_add_youtube_meta_box()
{
    add_meta_box(
        'prismblossom_youtube',
        'YouTube Video ID',
        'prismblossom_youtube_meta_box_callback',
        'screw_tape',
        'side'
    );
}
add_action('add_meta_boxes', 'prismblossom_add_youtube_meta_box');

function prismblossom_youtube_meta_box_callback($post)
{
    wp_nonce_field('prismblossom_save_youtube', 'prismblossom_youtube_nonce');
    $value = get_post_meta($post->ID, '_youtube_id', true);
    echo '<input type="text" name="youtube_id" value="' . esc_attr($value) . '" placeholder="e.g., oYqlfb2sghc" style="width:100%;" />';
}

function prismblossom_save_youtube_meta($post_id)
{
    if (!isset($_POST['prismblossom_youtube_nonce'])) return;
    if (!wp_verify_nonce($_POST['prismblossom_youtube_nonce'], 'prismblossom_save_youtube')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['youtube_id'])) {
        update_post_meta($post_id, '_youtube_id', sanitize_text_field($_POST['youtube_id']));
    }
}
add_action('save_post', 'prismblossom_save_youtube_meta');

// Aria page creation removed - no longer needed

// Create Carmyn page on theme activation
function prismblossom_create_carmyn_page()
{
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
add_action('after_switch_theme', 'prismblossom_create_carmyn_page');

// Add Carmyn and Invitation links to navigation menu (Aria removed)
function prismblossom_add_artist_menu_items($items, $args)
{
    // Only add to primary menu
    if ($args->theme_location == 'primary') {
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

        $carmyn_item = '<li><a href="' . esc_url($carmyn_url) . '">Carmyn</a></li>';
        $invitation_item = '<li><a href="' . esc_url($invitation_url) . '">Invitation</a></li>';

        // Find Submit link and insert Carmyn and Invitation after it
        $submit_pos = stripos($items, '>Submit<');
        if ($submit_pos !== false) {
            // Find the closing </a></li> after Submit
            $after_submit = strpos($items, '</a></li>', $submit_pos);
            if ($after_submit !== false) {
                $after_submit += 9; // Length of '</a></li>'
                $items = substr($items, 0, $after_submit) . $carmyn_item . $invitation_item . substr($items, $after_submit);
            }
        } else {
            // If no Submit link found, just append to end
            $items .= $carmyn_item . $invitation_item;
        }
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'prismblossom_add_artist_menu_items', 10, 2);

// Remove unwanted pages from menus (even if they exist in WP)
function prismblossom_filter_nav_menu_objects($sorted_menu_items, $args)
{
    if (!is_array($sorted_menu_items)) {
        return $sorted_menu_items;
    }

    $blocked_slugs = array(
        'agents',
        'live-activity',
        'capabilities',
        'about',
        'aria',
    );

    $filtered = array();
    foreach ($sorted_menu_items as $item) {
        $url = isset($item->url) ? (string) $item->url : '';

        $path = (string) wp_parse_url($url, PHP_URL_PATH);
        $path = trim($path, '/');

        $last_segment = $path !== '' ? basename($path) : '';
        if ($last_segment !== '' && in_array($last_segment, $blocked_slugs, true)) {
            continue;
        }

        $filtered[] = $item;
    }

    return $filtered;
}
add_filter('wp_nav_menu_objects', 'prismblossom_filter_nav_menu_objects', 10, 2);

// Block direct access to unwanted pages (redirect to home)
function prismblossom_block_unwanted_pages()
{
    if (!is_page()) {
        return;
    }

    if (is_page(array('agents', 'live-activity', 'capabilities', 'about', 'aria'))) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('template_redirect', 'prismblossom_block_unwanted_pages');

// Remove unwanted menu items from navigation - ENHANCED
function prismblossom_remove_menu_items($items, $args)
{
    // Only filter primary menu
    if (isset($args->theme_location) && $args->theme_location == 'primary') {
        // Items to remove (case-insensitive matching with variations)
        $items_to_remove = array(
            'Capabilities',
            'Capabilitie',
            'Capability',
            'Live Activity',
            'Live Activity',
            'LiveActivity',
            'Agent',
            'Agents',
            'Agent',
            'Aria',
            'Aria',
            'aria'
        );

        foreach ($items_to_remove as $item_to_remove) {
            // More robust regex to match menu items with the text
            $patterns = array(
                '/<li[^>]*>.*?<a[^>]*>.*?' . preg_quote($item_to_remove, '/') . '.*?<\/a>.*?<\/li>/is',
                '/<li[^>]*>.*?<a[^>]*>' . preg_quote($item_to_remove, '/') . '<\/a>.*?<\/li>/is',
                '/<li[^>]*>.*?<a[^>]*>.*?' . preg_quote(ucfirst($item_to_remove), '/') . '.*?<\/a>.*?<\/li>/is',
                '/<li[^>]*>.*?<a[^>]*>.*?' . preg_quote(strtolower($item_to_remove), '/') . '.*?<\/a>.*?<\/li>/is'
            );

            foreach ($patterns as $pattern) {
                $items = preg_replace($pattern, '', $items);
            }
        }

        // Clean up any empty list items
        $items = preg_replace('/<li[^>]*>\s*<\/li>/is', '', $items);
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'prismblossom_remove_menu_items', 20, 2);

// Alternative approach: Filter menu objects before rendering - ENHANCED
function prismblossom_filter_menu_objects($sorted_menu_items, $args)
{
    // Only filter primary menu
    if (isset($args->theme_location) && $args->theme_location == 'primary') {
        $items_to_remove = array('Capabilities', 'Capabilitie', 'Live Activity', 'Agent', 'Agents', 'Aria');

        foreach ($sorted_menu_items as $key => $item) {
            $item_title_lower = strtolower(trim($item->title));
            $item_slug_lower = isset($item->post_name) ? strtolower(trim($item->post_name)) : '';

            // Remove items by exact title match (case-insensitive)
            foreach ($items_to_remove as $remove_item) {
                $remove_item_lower = strtolower(trim($remove_item));

                // Check title
                if ($item_title_lower === $remove_item_lower || stripos($item_title_lower, $remove_item_lower) !== false) {
                    unset($sorted_menu_items[$key]);
                    break;
                }

                // Check slug/post_name
                if (!empty($item_slug_lower)) {
                    if (
                        $item_slug_lower === $remove_item_lower ||
                        $item_slug_lower === str_replace(' ', '-', $remove_item_lower) ||
                        stripos($item_slug_lower, $remove_item_lower) !== false
                    ) {
                        unset($sorted_menu_items[$key]);
                        break;
                    }
                }
            }
        }
    }
    return $sorted_menu_items;
}
add_filter('wp_nav_menu_objects', 'prismblossom_filter_menu_objects', 999, 2); // Higher priority

/**
 * Delete unwanted pages (Capabilities, Live Activity, Agents, Aria)
 * Runs on theme activation and admin_init to ensure they're removed
 */
function prismblossom_delete_unwanted_pages()
{
    // Pages to delete
    $pages_to_delete = array(
        'capabilities',
        'live-activity',
        'liveactivity',
        'agent',
        'agents',
        'aria'
    );

    foreach ($pages_to_delete as $page_slug) {
        $page = get_page_by_path($page_slug);
        if ($page) {
            // Force delete (bypass trash)
            wp_delete_post($page->ID, true);
        }

        // Also try case variations
        $page = get_page_by_path(ucfirst($page_slug));
        if ($page) {
            wp_delete_post($page->ID, true);
        }
    }

    // Also search all pages by title (not using 'title' parameter as it's not reliable)
    $titles_to_delete = array('Capabilities', 'Live Activity', 'Agent', 'Agents', 'Aria');
    $all_pages = get_pages(array(
        'post_status' => 'any', // Include all statuses
        'number' => -1,
        'post_type' => 'page'
    ));

    foreach ($all_pages as $page) {
        $page_title_lower = strtolower(trim($page->post_title));
        $page_slug_lower = strtolower(trim($page->post_name));

        // Check if title or slug matches unwanted pages
        foreach ($titles_to_delete as $unwanted_title) {
            $unwanted_title_lower = strtolower(trim($unwanted_title));

            if (
                $page_title_lower === $unwanted_title_lower ||
                stripos($page_title_lower, $unwanted_title_lower) !== false ||
                in_array($page_slug_lower, $pages_to_delete)
            ) {

                wp_delete_post($page->ID, true); // Force delete
                break;
            }
        }
    }

    // Clear menu cache
    wp_cache_delete('alloptions', 'options');
}
/**
 * Remove unwanted menu items from WordPress menus
 */
function prismblossom_remove_unwanted_menu_items()
{
    $menu_ids_to_check = array('primary', 'Primary Menu', 1); // Common menu locations/IDs

    $items_to_remove = array('Capabilities', 'Capabilitie', 'Live Activity', 'Agent', 'Agents', 'Aria');

    foreach ($menu_ids_to_check as $menu_id) {
        $menu = wp_get_nav_menu_object($menu_id);
        if (!$menu && is_numeric($menu_id)) {
            $menus = wp_get_nav_menus();
            if (isset($menus[$menu_id - 1])) {
                $menu = $menus[$menu_id - 1];
            }
        }

        if ($menu) {
            $menu_items = wp_get_nav_menu_items($menu->term_id);

            if ($menu_items) {
                foreach ($menu_items as $menu_item) {
                    $title_lower = strtolower(trim($menu_item->title));

                    foreach ($items_to_remove as $unwanted_item) {
                        $unwanted_lower = strtolower(trim($unwanted_item));

                        if (
                            $title_lower === $unwanted_lower ||
                            stripos($title_lower, $unwanted_lower) !== false
                        ) {
                            wp_delete_post($menu_item->ID, true); // Delete menu item
                            break;
                        }
                    }
                }
            }
        }
    }

    // Also check all menus
    $all_menus = wp_get_nav_menus();
    foreach ($all_menus as $menu) {
        $menu_items = wp_get_nav_menu_items($menu->term_id);

        if ($menu_items) {
            foreach ($menu_items as $menu_item) {
                $title_lower = strtolower(trim($menu_item->title));

                foreach ($items_to_remove as $unwanted_item) {
                    $unwanted_lower = strtolower(trim($unwanted_item));

                    if (
                        $title_lower === $unwanted_lower ||
                        stripos($title_lower, $unwanted_lower) !== false
                    ) {
                        wp_delete_post($menu_item->ID, true);
                        break;
                    }
                }
            }
        }
    }
}

add_action('after_switch_theme', 'prismblossom_delete_unwanted_pages');
add_action('admin_init', 'prismblossom_delete_unwanted_pages');
add_action('template_redirect', 'prismblossom_delete_unwanted_pages', 1); // Run on every page load

add_action('after_switch_theme', 'prismblossom_remove_unwanted_menu_items');
add_action('admin_init', 'prismblossom_remove_unwanted_menu_items');
add_action('template_redirect', 'prismblossom_remove_unwanted_menu_items', 1);

// ============================================
// GUESTBOOK FUNCTIONALITY
// ============================================

// Create guestbook database table on theme activation
function prismblossom_create_guestbook_table()
{
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
add_action('after_switch_theme', 'prismblossom_create_guestbook_table');

// Handle guestbook form submission
function prismblossom_handle_guestbook_submission()
{
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
            'status' => 'approved'  // Auto-approve messages so they appear immediately
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
add_action('admin_post_submit_guestbook_entry', 'prismblossom_handle_guestbook_submission');
add_action('admin_post_nopriv_submit_guestbook_entry', 'prismblossom_handle_guestbook_submission');

// AJAX handler for guestbook submission
function prismblossom_ajax_guestbook_submission()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'guestbook_submit')) {
        wp_send_json_error('Security check failed');
        return;
    }

    // Sanitize input
    $guest_name = isset($_POST['guest_name']) ? sanitize_text_field($_POST['guest_name']) : '';
    $message = isset($_POST['guest_message']) ? sanitize_textarea_field($_POST['guest_message']) : '';

    // Validate input
    if (empty($guest_name) || empty($message)) {
        wp_send_json_error('Name and message are required');
        return;
    }

    // Insert into database
    global $wpdb;
    $table_name = $wpdb->prefix . 'guestbook_entries';

    $result = $wpdb->insert(
        $table_name,
        array(
            'guest_name' => $guest_name,
            'message' => $message,
            'status' => 'approved'  // Auto-approve messages so they appear immediately
        ),
        array('%s', '%s', '%s')
    );

    if ($result) {
        // Return the new entry data so it can be displayed immediately
        $entry_id = $wpdb->insert_id;
        $new_entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $entry_id));

        wp_send_json_success(array(
            'message' => 'Message submitted successfully',
            'entry' => array(
                'id' => $new_entry->id,
                'guest_name' => $new_entry->guest_name,
                'message' => $new_entry->message,
                'created_at' => $new_entry->created_at,
                'date_formatted' => date('M j, Y', strtotime($new_entry->created_at))
            )
        ));
    } else {
        wp_send_json_error('Database error');
    }
}
add_action('wp_ajax_prismblossom_submit_guestbook', 'prismblossom_ajax_guestbook_submission');
add_action('wp_ajax_nopriv_prismblossom_submit_guestbook', 'prismblossom_ajax_guestbook_submission');

// Add admin menu for guestbook management
function prismblossom_guestbook_admin_menu()
{
    add_menu_page(
        'Guestbook',
        'Guestbook',
        'manage_options',
        'guestbook',
        'prismblossom_guestbook_admin_page',
        'dashicons-format-chat',
        30
    );
}
add_action('admin_menu', 'prismblossom_guestbook_admin_menu');

// Guestbook admin page
function prismblossom_guestbook_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'guestbook_entries';

    // Handle bulk actions
    if (isset($_POST['bulk_action']) && isset($_POST['entry_ids']) && check_admin_referer('guestbook_bulk_action')) {
        $bulk_action = $_POST['bulk_action'];
        $entry_ids = array_map('intval', $_POST['entry_ids']);
        $deleted = 0;

        foreach ($entry_ids as $entry_id) {
            if ($bulk_action === 'delete') {
                $wpdb->delete($table_name, array('id' => $entry_id), array('%d'));
                $deleted++;
            } elseif ($bulk_action === 'approve') {
                $wpdb->update(
                    $table_name,
                    array('status' => 'approved'),
                    array('id' => $entry_id),
                    array('%s'),
                    array('%d')
                );
            } elseif ($bulk_action === 'reject') {
                $wpdb->update(
                    $table_name,
                    array('status' => 'rejected'),
                    array('id' => $entry_id),
                    array('%s'),
                    array('%d')
                );
            }
        }

        if ($bulk_action === 'delete') {
            echo '<div class="notice notice-success"><p>' . $deleted . ' message(s) deleted!</p></div>';
        } else {
            echo '<div class="notice notice-success"><p>Bulk action completed!</p></div>';
        }
    }

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
        <p>Review and manage birthday messages from visitors.</p>

        <form method="post" id="guestbook-bulk-form">
            <?php wp_nonce_field('guestbook_bulk_action'); ?>

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector" class="screen-reader-text">Select bulk action</label>
                    <select name="bulk_action" id="bulk-action-selector">
                        <option value="">Bulk Actions</option>
                        <option value="delete">Delete</option>
                        <option value="approve">Approve</option>
                        <option value="reject">Reject</option>
                    </select>
                    <input type="submit" class="button action" value="Apply" onclick="return confirm('Are you sure you want to perform this bulk action?');">
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all">
                        </td>
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
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="entry_ids[]" value="<?php echo $entry->id; ?>">
                                </th>
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
                            <td colspan="7">No guestbook entries yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>

    <style>
        .status-pending {
            color: #ffa500;
            font-weight: bold;
        }

        .status-approved {
            color: #00ff00;
            font-weight: bold;
        }

        .status-rejected {
            color: #ff0000;
            font-weight: bold;
        }

        .tablenav {
            margin: 6px 0 4px;
        }

        .bulkactions {
            padding: 8px 0;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('cb-select-all');
            const checkboxes = document.querySelectorAll('input[name="entry_ids[]"]');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                });
            }
        });
    </script>
<?php
}

// Create Guestbook page on theme activation
function prismblossom_create_guestbook_page()
{
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
add_action('after_switch_theme', 'prismblossom_create_guestbook_page');

// Create Birthday Fun page on theme activation
function prismblossom_create_birthday_fun_page()
{
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
add_action('after_switch_theme', 'prismblossom_create_birthday_fun_page');

// Create Invitation page on theme activation
function prismblossom_create_invitation_page()
{
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

    $page_id = wp_insert_post($invitation_page);

    // Set default event details
    if ($page_id) {
        update_post_meta($page_id, '_invitation_date', 'TBD');
        update_post_meta($page_id, '_invitation_time', 'TBD');
        update_post_meta($page_id, '_invitation_location', 'TBD');
        update_post_meta($page_id, '_invitation_rsvp', 'TBD');
    }
}
add_action('after_switch_theme', 'prismblossom_create_invitation_page');

// ============================================
// INVITATION PAGE FUNCTIONALITY
// ============================================

// Add meta box for Invitation page event details
function prismblossom_add_invitation_meta_box()
{
    add_meta_box(
        'prismblossom_invitation_details',
        'Event Details',
        'prismblossom_invitation_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'prismblossom_add_invitation_meta_box');

// Invitation meta box callback
function prismblossom_invitation_meta_box_callback($post)
{
    // Only show on invitation page
    if (get_page_template_slug($post->ID) !== 'page-invitation.php') {
        echo '<p>This meta box is only available on the Invitation page template.</p>';
        return;
    }

    wp_nonce_field('prismblossom_save_invitation_details', 'prismblossom_invitation_nonce');

    $event_date = get_post_meta($post->ID, '_invitation_date', true);
    $event_time = get_post_meta($post->ID, '_invitation_time', true);
    $event_location = get_post_meta($post->ID, '_invitation_location', true);
    $event_rsvp = get_post_meta($post->ID, '_invitation_rsvp', true);

?>
    <table class="form-table">
        <tr>
            <th><label for="invitation_date">Event Date</label></th>
            <td>
                <input type="text" id="invitation_date" name="invitation_date"
                    value="<?php echo esc_attr($event_date); ?>"
                    class="regular-text"
                    placeholder="e.g., December 25, 2025">
                <p class="description">Enter the event date</p>
            </td>
        </tr>
        <tr>
            <th><label for="invitation_time">Event Time</label></th>
            <td>
                <input type="text" id="invitation_time" name="invitation_time"
                    value="<?php echo esc_attr($event_time); ?>"
                    class="regular-text"
                    placeholder="e.g., 7:00 PM">
                <p class="description">Enter the event time</p>
            </td>
        </tr>
        <tr>
            <th><label for="invitation_location">Event Location</label></th>
            <td>
                <input type="text" id="invitation_location" name="invitation_location"
                    value="<?php echo esc_attr($event_location); ?>"
                    class="regular-text"
                    placeholder="e.g., 123 Main Street, City, State">
                <p class="description">Enter the event location</p>
            </td>
        </tr>
        <tr>
            <th><label for="invitation_rsvp">RSVP Information</label></th>
            <td>
                <input type="text" id="invitation_rsvp" name="invitation_rsvp"
                    value="<?php echo esc_attr($event_rsvp); ?>"
                    class="regular-text"
                    placeholder="e.g., RSVP by December 20th">
                <p class="description">Enter RSVP instructions or contact information</p>
            </td>
        </tr>
    </table>
<?php
}

// Save invitation meta box data
function prismblossom_save_invitation_meta($post_id)
{
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verify nonce
    if (
        !isset($_POST['prismblossom_invitation_nonce']) ||
        !wp_verify_nonce($_POST['prismblossom_invitation_nonce'], 'prismblossom_save_invitation_details')
    ) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    // Only save if this is the invitation page template
    if (get_page_template_slug($post_id) !== 'page-invitation.php') {
        return;
    }

    // Save meta fields
    if (isset($_POST['invitation_date'])) {
        update_post_meta($post_id, '_invitation_date', sanitize_text_field($_POST['invitation_date']));
    }
    if (isset($_POST['invitation_time'])) {
        update_post_meta($post_id, '_invitation_time', sanitize_text_field($_POST['invitation_time']));
    }
    if (isset($_POST['invitation_location'])) {
        update_post_meta($post_id, '_invitation_location', sanitize_text_field($_POST['invitation_location']));
    }
    if (isset($_POST['invitation_rsvp'])) {
        update_post_meta($post_id, '_invitation_rsvp', sanitize_text_field($_POST['invitation_rsvp']));
    }
}
add_action('save_post', 'prismblossom_save_invitation_meta');

// AJAX handler for invitation message submission
function prismblossom_ajax_invitation_message_submission()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'invitation_message_submit')) {
        wp_send_json_error('Security check failed');
        return;
    }

    // Sanitize input
    $message_name = isset($_POST['message_name']) ? sanitize_text_field($_POST['message_name']) : '';
    $message_text = isset($_POST['message_text']) ? sanitize_textarea_field($_POST['message_text']) : '';

    // Validate input
    if (empty($message_name) || empty($message_text)) {
        wp_send_json_error('Name and message are required');
        return;
    }

    // Insert into guestbook database (reuse existing guestbook table)
    global $wpdb;
    $table_name = $wpdb->prefix . 'guestbook_entries';

    $result = $wpdb->insert(
        $table_name,
        array(
            'guest_name' => $message_name,
            'message' => $message_text,
            'status' => 'pending'
        ),
        array('%s', '%s', '%s')
    );

    if ($result) {
        wp_send_json_success('Message sent successfully! Thank you for your message.');
    } else {
        wp_send_json_error('Database error. Please try again.');
    }
}
add_action('wp_ajax_prismblossom_submit_invitation_message', 'prismblossom_ajax_invitation_message_submission');
add_action('wp_ajax_nopriv_prismblossom_submit_invitation_message', 'prismblossom_ajax_invitation_message_submission');

// ============================================
// CARMYN PAGE PROFILE FIELDS
// ============================================

function prismblossom_add_carmyn_meta_box()
{
    add_meta_box(
        'prismblossom_carmyn_profile',
        'Carmyn Profile',
        'prismblossom_carmyn_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'prismblossom_add_carmyn_meta_box');

function prismblossom_carmyn_meta_box_callback($post)
{
    if (get_page_template_slug($post->ID) !== 'page-carmyn.php') {
        echo '<p>This meta box is only available on the Carmyn page template.</p>';
        return;
    }

    wp_nonce_field('prismblossom_save_carmyn_profile', 'prismblossom_carmyn_nonce');

    $tagline = get_post_meta($post->ID, '_carmyn_tagline', true);
    $highlights = get_post_meta($post->ID, '_carmyn_highlights', true);
    $lofi_youtube_id = get_post_meta($post->ID, '_carmyn_lofi_youtube_id', true);

    $tagline = $tagline ?: 'Family & friends';
    $highlights = $highlights ?: 'Family, Memories, Music';
    $lofi_youtube_id = $lofi_youtube_id ?: 'sF80I-TQiW0';
?>
    <table class="form-table">
        <tr>
            <th><label for="carmyn_tagline">Tagline</label></th>
            <td>
                <input type="text" id="carmyn_tagline" name="carmyn_tagline"
                    value="<?php echo esc_attr($tagline); ?>"
                    class="regular-text"
                    placeholder="e.g., Family & friends">
                <p class="description">Short line shown under the page title.</p>
            </td>
        </tr>
        <tr>
            <th><label for="carmyn_highlights">Highlights (comma-separated)</label></th>
            <td>
                <input type="text" id="carmyn_highlights" name="carmyn_highlights"
                    value="<?php echo esc_attr($highlights); ?>"
                    class="regular-text"
                    placeholder="e.g., Family, Memories, Music">
                <p class="description">Displayed as badges on the page.</p>
            </td>
        </tr>
        <tr>
            <th><label for="carmyn_lofi_youtube_id">Background Music YouTube Video ID</label></th>
            <td>
                <input type="text" id="carmyn_lofi_youtube_id" name="carmyn_lofi_youtube_id"
                    value="<?php echo esc_attr($lofi_youtube_id); ?>"
                    class="regular-text"
                    placeholder="e.g., sF80I-TQiW0">
                <p class="description">Used by the “Play Music” button. (Video ID only, not full URL.)</p>
            </td>
        </tr>
    </table>
<?php
}

function prismblossom_save_carmyn_meta($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (
        !isset($_POST['prismblossom_carmyn_nonce']) ||
        !wp_verify_nonce($_POST['prismblossom_carmyn_nonce'], 'prismblossom_save_carmyn_profile')
    ) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    if (get_page_template_slug($post_id) !== 'page-carmyn.php') {
        return;
    }

    if (isset($_POST['carmyn_tagline'])) {
        update_post_meta($post_id, '_carmyn_tagline', sanitize_text_field($_POST['carmyn_tagline']));
    }

    if (isset($_POST['carmyn_highlights'])) {
        update_post_meta($post_id, '_carmyn_highlights', sanitize_text_field($_POST['carmyn_highlights']));
    }

    if (isset($_POST['carmyn_lofi_youtube_id'])) {
        update_post_meta($post_id, '_carmyn_lofi_youtube_id', sanitize_text_field($_POST['carmyn_lofi_youtube_id']));
    }
}
add_action('save_post', 'prismblossom_save_carmyn_meta');

// ============================================
// FUTURE BLOG STRUCTURE (Not implemented yet)
// ============================================

// Register blog post type for future use (commented out until needed)
/*
function prismblossom_register_blog_post_type() {
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
// add_action('init', 'prismblossom_register_blog_post_type'); // Uncomment when ready to implement blog
*/
