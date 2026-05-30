<?php
/**
 * FreeRideInvestor Pages and Menu Setup
 * 
 * Run this file once via browser or WP-CLI to set up pages and menu.
 * 
 * Browser: https://freerideinvestor.com/wp-content/themes/freerideinvestor-modern/setup-pages-menu.php
 * WP-CLI: wp eval-file wp-content/themes/freerideinvestor-modern/setup-pages-menu.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user has permission (for browser access)
if (!is_admin() && !defined('WP_CLI')) {
    // For browser access, require admin login
    auth_redirect();
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

        if ($primary_menu_id && !is_wp_error($primary_menu_id)) {
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
            $locations = get_theme_mod('nav_menu_locations', array());
            $locations['primary'] = $primary_menu_id;
            set_theme_mod('nav_menu_locations', $locations);

            echo "✅ Assigned primary menu to theme location\n";
        } else {
            echo "❌ Failed to create primary menu\n";
        }
    } else {
        echo "ℹ️  Primary menu already exists\n";
        // Still assign it to the location
        $locations = get_theme_mod('nav_menu_locations', array());
        $locations['primary'] = $primary_menu_exists->term_id;
        set_theme_mod('nav_menu_locations', $locations);
        echo "✅ Assigned existing menu to theme location\n";
    }

    echo "\n🎉 FreeRideInvestor menu and pages setup complete!\n";
    echo "📋 Summary:\n";

    foreach ($created_pages as $slug => $page_id) {
        $page = get_post($page_id);
        $template = get_post_meta($page_id, '_wp_page_template', true);
        echo "   • {$page->post_title} ({$slug}) - Template: " . ($template ?: 'default') . "\n";
    }
}

// Run the setup
if (defined('WP_CLI')) {
    // WP-CLI mode
    freerideinvestor_setup_pages_and_menu();
} else {
    // Browser mode
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>FreeRideInvestor Setup</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .success { color: green; }
            .error { color: red; }
            .info { color: blue; }
            pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <h1>FreeRideInvestor Pages & Menu Setup</h1>
        <pre>
<?php
freerideinvestor_setup_pages_and_menu();
?>
        </pre>
        <p><a href="<?php echo admin_url('nav-menus.php'); ?>">Go to Menus</a> | <a href="<?php echo home_url(); ?>">View Site</a></p>
    </body>
    </html>
    <?php
}
