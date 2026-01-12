<?php
/**
 * FreeRideInvestor Menu Setup Script
 *
 * This script helps set up the navigation menu and pages for the FreeRideInvestor site.
 * Run this in the WordPress admin or via WP-CLI.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Setup FreeRideInvestor Pages and Menu
 */
function freerideinvestor_setup_pages_and_menu() {
    // Pages to create
    $pages = array(
        'about' => array(
            'title' => 'About',
            'content' => 'Learn about our mission to democratize trading education and provide institutional-grade strategies to individual traders.',
            'template' => 'page-about.php'
        ),
        'services' => array(
            'title' => 'Services',
            'content' => 'Explore our comprehensive suite of trading tools, strategies, and educational resources designed to help you succeed in the markets.',
            'template' => 'page-services.php'
        ),
        'resources' => array(
            'title' => 'Resources',
            'content' => 'Access our extensive library of trading education, market analysis, tools, and community resources.',
            'template' => 'page-resources.php'
        ),
        'blog' => array(
            'title' => 'Blog',
            'content' => 'Stay updated with the latest market insights, trading strategies, and educational content from our expert team.',
            'template' => 'page-blog.php'
        ),
        'contact' => array(
            'title' => 'Contact',
            'content' => 'Get in touch with our team for questions about our services, partnerships, or trading education programs.',
            'template' => 'page-contact.php'
        ),
        'trading-strategies' => array(
            'title' => 'Trading Strategies',
            'content' => 'Discover proven trading strategies across multiple asset classes including equities, options, and futures.',
            'template' => 'page-trading-strategies.php'
        )
    );

    $created_pages = array();

    // Create pages
    foreach ($pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug);

        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title'    => $page_data['title'],
                'post_content'  => $page_data['content'],
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_name'     => $slug
            ));

            if ($page_id && !is_wp_error($page_id)) {
                // Set page template if it exists
                if (isset($page_data['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                }

                $created_pages[$slug] = $page_id;
                echo "✅ Created page: {$page_data['title']} (ID: {$page_id})\n";
            } else {
                echo "❌ Failed to create page: {$page_data['title']}\n";
            }
        } else {
            $created_pages[$slug] = $existing_page->ID;
            echo "ℹ️  Page already exists: {$page_data['title']} (ID: {$existing_page->ID})\n";

            // Update template if needed
            if (isset($page_data['template'])) {
                update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
            }
        }
    }

    // Setup Primary Menu
    $primary_menu_name = 'Primary Menu';
    $primary_menu_exists = wp_get_nav_menu_object($primary_menu_name);

    if (!$primary_menu_exists) {
        $primary_menu_id = wp_create_nav_menu($primary_menu_name);

        if ($primary_menu_id) {
            echo "✅ Created primary menu: {$primary_menu_name}\n";

            // Add menu items
            $menu_items = array(
                array('title' => 'Home', 'url' => home_url('/'), 'order' => 1),
                array('title' => 'About', 'url' => get_permalink($created_pages['about']), 'order' => 2),
                array('title' => 'Services', 'url' => get_permalink($created_pages['services']), 'order' => 3),
                array('title' => 'Trading Strategies', 'url' => get_permalink($created_pages['trading-strategies']), 'order' => 4),
                array('title' => 'Resources', 'url' => get_permalink($created_pages['resources']), 'order' => 5),
                array('title' => 'Blog', 'url' => get_permalink($created_pages['blog']), 'order' => 6),
                array('title' => 'Contact', 'url' => get_permalink($created_pages['contact']), 'order' => 7),
            );

            foreach ($menu_items as $item) {
                wp_update_nav_menu_item($primary_menu_id, 0, array(
                    'menu-item-title' => $item['title'],
                    'menu-item-url' => $item['url'],
                    'menu-item-status' => 'publish',
                    'menu-item-type' => 'custom',
                    'menu-item-position' => $item['order']
                ));
            }

            // Assign menu to primary location
            set_theme_mod('nav_menu_locations', array(
                'primary' => $primary_menu_id
            ));

            echo "✅ Assigned primary menu to theme location\n";
        } else {
            echo "❌ Failed to create primary menu\n";
        }
    } else {
        echo "ℹ️  Primary menu already exists\n";
    }

    // Setup Footer Menu
    $footer_menu_name = 'Footer Menu';
    $footer_menu_exists = wp_get_nav_menu_object($footer_menu_name);

    if (!$footer_menu_exists) {
        $footer_menu_id = wp_create_nav_menu($footer_menu_name);

        if ($footer_menu_id) {
            echo "✅ Created footer menu: {$footer_menu_name}\n";

            // Add footer menu items
            $footer_items = array(
                array('title' => 'About', 'url' => get_permalink($created_pages['about']), 'order' => 1),
                array('title' => 'Services', 'url' => get_permalink($created_pages['services']), 'order' => 2),
                array('title' => 'Resources', 'url' => get_permalink($created_pages['resources']), 'order' => 3),
                array('title' => 'Contact', 'url' => get_permalink($created_pages['contact']), 'order' => 4),
            );

            foreach ($footer_items as $item) {
                wp_update_nav_menu_item($footer_menu_id, 0, array(
                    'menu-item-title' => $item['title'],
                    'menu-item-url' => $item['url'],
                    'menu-item-status' => 'publish',
                    'menu-item-type' => 'custom',
                    'menu-item-position' => $item['order']
                ));
            }

            // Assign menu to footer location
            $current_locations = get_theme_mod('nav_menu_locations', array());
            $current_locations['footer'] = $footer_menu_id;
            set_theme_mod('nav_menu_locations', $current_locations);

            echo "✅ Assigned footer menu to theme location\n";
        } else {
            echo "❌ Failed to create footer menu\n";
        }
    } else {
        echo "ℹ️  Footer menu already exists\n";
    }

    echo "\n🎉 FreeRideInvestor menu and pages setup complete!\n";
    echo "📋 Summary:\n";

    foreach ($created_pages as $slug => $page_id) {
        $page = get_post($page_id);
        $template = get_post_meta($page_id, '_wp_page_template', true);
        echo "   • {$page->post_title} ({$slug}) - Template: " . ($template ?: 'default') . "\n";
    }

    echo "\n🔧 Next steps:\n";
    echo "   1. Visit Appearance → Menus in WordPress admin to customize menus\n";
    echo "   2. Edit page content using the WordPress editor\n";
    echo "   3. Add custom fields for hero sections and other dynamic content\n";
    echo "   4. Test menu navigation on the frontend\n";
}

/**
 * Reset menus and pages (for development/testing)
 */
function freerideinvestor_reset_menus_and_pages() {
    // Remove all pages created by this script
    $pages_to_remove = array('about', 'services', 'resources', 'blog', 'contact', 'trading-strategies');

    foreach ($pages_to_remove as $slug) {
        $page = get_page_by_path($slug);
        if ($page) {
            wp_delete_post($page->ID, true);
            echo "🗑️  Deleted page: {$slug}\n";
        }
    }

    // Remove menus
    $menus_to_remove = array('Primary Menu', 'Footer Menu');

    foreach ($menus_to_remove as $menu_name) {
        $menu = wp_get_nav_menu_object($menu_name);
        if ($menu) {
            wp_delete_nav_menu($menu->term_id);
            echo "🗑️  Deleted menu: {$menu_name}\n";
        }
    }

    echo "✅ Reset complete\n";
}

/**
 * WP-CLI Commands
 */
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('freerideinvestor setup', function() {
        freerideinvestor_setup_pages_and_menu();
    });

    WP_CLI::add_command('freerideinvestor reset', function() {
        freerideinvestor_reset_menus_and_pages();
    });
}

/**
 * Admin Notice
 */
function freerideinvestor_admin_notice() {
    if (!get_option('freerideinvestor_menu_setup_complete')) {
        ?>
        <div class="notice notice-info is-dismissible">
            <h3>Welcome to FreeRideInvestor Theme!</h3>
            <p>To set up your site's navigation menus and pages, you can:</p>
            <ol>
                <li>Run the setup script: <code>wp freerideinvestor setup</code> (WP-CLI)</li>
                <li>Or manually create pages and assign them to menus in the WordPress admin</li>
            </ol>
            <p><strong>Required Pages:</strong> About, Services, Resources, Blog, Contact, Trading Strategies</p>
            <p><strong>Page Templates:</strong> Available for each page type in the theme</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'freerideinvestor_admin_notice');

/**
 * Mark setup as complete
 */
function freerideinvestor_mark_setup_complete() {
    update_option('freerideinvestor_menu_setup_complete', true);
}
// Uncomment the line below to mark setup as complete
// freerideinvestor_mark_setup_complete();