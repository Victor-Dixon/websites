<?php
/**
 * Plugin Name: FreeRideInvestor Setup
 * Plugin URI: https://freerideinvestor.com
 * Description: Automated setup for FreeRideInvestor theme - creates pages, menus, and validates configuration.
 * Version: 1.0.0
 * Author: FreeRideInvestor Team
 * Author URI: https://freerideinvestor.com
 * License: GPL v2 or later
 * Text Domain: freerideinvestor-setup
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class FreeRideInvestor_Setup {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_setup_action'));
        add_action('admin_notices', array($this, 'admin_notices'));
        register_activation_hook(__FILE__, array($this, 'activate'));
    }
    
    /**
     * Plugin activation - auto-run setup
     */
    public function activate() {
        $this->setup_pages_and_menu();
        update_option('freerideinvestor_setup_complete', true);
        update_option('freerideinvestor_setup_timestamp', current_time('mysql'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_management_page(
            'FreeRideInvestor Setup',
            'FRI Setup',
            'manage_options',
            'freerideinvestor-setup',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Handle setup actions
     */
    public function handle_setup_action() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'freerideinvestor-setup') {
            return;
        }
        
        if (isset($_GET['action']) && $_GET['action'] === 'setup' && check_admin_referer('fri_setup')) {
            $this->setup_pages_and_menu();
            wp_redirect(admin_url('tools.php?page=freerideinvestor-setup&setup_complete=1'));
            exit;
        }
        
        if (isset($_GET['action']) && $_GET['action'] === 'validate' && check_admin_referer('fri_validate')) {
            $this->validate_setup();
            wp_redirect(admin_url('tools.php?page=freerideinvestor-setup&validation_complete=1'));
            exit;
        }
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        if (isset($_GET['setup_complete'])) {
            echo '<div class="notice notice-success is-dismissible"><p>✅ FreeRideInvestor setup completed successfully!</p></div>';
        }
        if (isset($_GET['validation_complete'])) {
            echo '<div class="notice notice-success is-dismissible"><p>✅ Validation completed. Check results below.</p></div>';
        }
        
        // Check if setup is needed
        if (!get_option('freerideinvestor_setup_complete')) {
            $current_theme = wp_get_theme();
            if ($current_theme->get('Name') === 'FreeRideInvestor Modern') {
                echo '<div class="notice notice-warning is-dismissible">';
                echo '<p><strong>FreeRideInvestor Setup Required:</strong> ';
                echo '<a href="' . admin_url('tools.php?page=freerideinvestor-setup') . '">Run Setup Now</a></p>';
                echo '</div>';
            }
        }
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        $setup_complete = get_option('freerideinvestor_setup_complete', false);
        $validation_results = get_option('freerideinvestor_validation_results', array());
        ?>
        <div class="wrap">
            <h1>🚀 FreeRideInvestor Setup</h1>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Setup Status</h2>
                <?php if ($setup_complete): ?>
                    <p style="color: green;">✅ Setup completed on: <?php echo get_option('freerideinvestor_setup_timestamp'); ?></p>
                <?php else: ?>
                    <p style="color: orange;">⚠️ Setup not yet completed</p>
                <?php endif; ?>
                
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=freerideinvestor-setup&action=setup'), 'fri_setup'); ?>" class="button button-primary">
                        Run Setup Now
                    </a>
                    <a href="<?php echo wp_nonce_url(admin_url('tools.php?page=freerideinvestor-setup&action=validate'), 'fri_validate'); ?>" class="button">
                        Validate Setup
                    </a>
                </p>
            </div>
            
            <?php if (!empty($validation_results)): ?>
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>Validation Results</h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Check</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($validation_results as $check => $result): ?>
                        <tr>
                            <td><strong><?php echo esc_html($check); ?></strong></td>
                            <td>
                                <?php if ($result['status'] === 'pass'): ?>
                                    <span style="color: green;">✅ Pass</span>
                                <?php elseif ($result['status'] === 'warning'): ?>
                                    <span style="color: orange;">⚠️ Warning</span>
                                <?php else: ?>
                                    <span style="color: red;">❌ Fail</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($result['message']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>What This Plugin Does</h2>
                <ul>
                    <li>✅ Creates required pages (About, Services, Resources, Blog, Contact, Trading Strategies)</li>
                    <li>✅ Sets up Primary navigation menu</li>
                    <li>✅ Assigns menu to theme location</li>
                    <li>✅ Validates theme configuration</li>
                    <li>✅ Verifies menu functionality</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Setup pages and menu
     */
    public function setup_pages_and_menu() {
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
                    if (isset($page_data['template'])) {
                        update_post_meta($page_id, '_wp_page_template', $page_data['template']);
                    }
                    $created_pages[$slug] = $page_id;
                }
            } else {
                $created_pages[$slug] = $existing_page->ID;
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
            }
        } else {
            // Ensure menu is assigned to location
            $locations = get_theme_mod('nav_menu_locations', array());
            $locations['primary'] = $primary_menu_exists->term_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
    
    /**
     * Validate setup
     */
    public function validate_setup() {
        $results = array();
        
        // Check theme
        $current_theme = wp_get_theme();
        if ($current_theme->get('Name') === 'FreeRideInvestor Modern') {
            $results['Theme Active'] = array(
                'status' => 'pass',
                'message' => 'FreeRideInvestor Modern theme is active'
            );
        } else {
            $results['Theme Active'] = array(
                'status' => 'fail',
                'message' => 'FreeRideInvestor Modern theme is not active. Current: ' . $current_theme->get('Name')
            );
        }
        
        // Check menu
        $menu_locations = get_theme_mod('nav_menu_locations', array());
        if (isset($menu_locations['primary']) && $menu_locations['primary'] > 0) {
            $menu = wp_get_nav_menu_object($menu_locations['primary']);
            if ($menu) {
                $items = wp_get_nav_menu_items($menu->term_id);
                $results['Primary Menu'] = array(
                    'status' => 'pass',
                    'message' => sprintf('Primary menu exists with %d items', count($items))
                );
            } else {
                $results['Primary Menu'] = array(
                    'status' => 'fail',
                    'message' => 'Menu location assigned but menu not found'
                );
            }
        } else {
            $results['Primary Menu'] = array(
                'status' => 'fail',
                'message' => 'Primary menu location not assigned'
            );
        }
        
        // Check required pages
        $required_pages = array('about', 'services', 'resources', 'blog', 'contact', 'trading-strategies');
        $pages_found = 0;
        foreach ($required_pages as $slug) {
            if (get_page_by_path($slug)) {
                $pages_found++;
            }
        }
        
        if ($pages_found === count($required_pages)) {
            $results['Required Pages'] = array(
                'status' => 'pass',
                'message' => sprintf('All %d required pages exist', count($required_pages))
            );
        } else {
            $results['Required Pages'] = array(
                'status' => 'warning',
                'message' => sprintf('Only %d of %d required pages found', $pages_found, count($required_pages))
            );
        }
        
        // Check menu items
        if (isset($menu_locations['primary']) && $menu_locations['primary'] > 0) {
            $menu = wp_get_nav_menu_object($menu_locations['primary']);
            if ($menu) {
                $items = wp_get_nav_menu_items($menu->term_id);
                if (count($items) >= 5) {
                    $results['Menu Items'] = array(
                        'status' => 'pass',
                        'message' => sprintf('Menu has %d items', count($items))
                    );
                } else {
                    $results['Menu Items'] = array(
                        'status' => 'warning',
                        'message' => sprintf('Menu has only %d items (expected 5+)', count($items))
                    );
                }
            }
        }
        
        update_option('freerideinvestor_validation_results', $results);
        return $results;
    }
}

// Initialize plugin
FreeRideInvestor_Setup::get_instance();
