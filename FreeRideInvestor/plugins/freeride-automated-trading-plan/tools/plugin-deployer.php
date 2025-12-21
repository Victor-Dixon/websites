<?php
/**
 * Plugin Deployment Tool - Command Line & Web Interface
 * 
 * Deploys plugin files, verifies installation, and manages setup
 * 
 * Usage (CLI): php plugin-deployer.php --action=deploy
 * Usage (Web): yoursite.com/wp-content/plugins/freeride-automated-trading-plan/tools/plugin-deployer.php
 */

class FRATP_Deployer {
    
    private $plugin_dir;
    private $wp_plugins_dir;
    
    public function __construct() {
        $this->plugin_dir = dirname(dirname(__FILE__));
        $this->wp_plugins_dir = WP_PLUGIN_DIR;
    }
    
    /**
     * Deploy plugin to WordPress plugins directory
     */
    public function deploy() {
        $source = $this->plugin_dir;
        $destination = $this->wp_plugins_dir . '/freeride-automated-trading-plan';
        
        // Ensure destination exists
        if (!is_dir($destination)) {
            wp_mkdir_p($destination);
        }
        
        // Copy all files
        $this->copy_directory($source, $destination);
        
        return "✅ Plugin deployed to: {$destination}";
    }
    
    /**
     * Verify plugin installation
     */
    public function verify() {
        $checks = array();
        
        // Check main file
        $main_file = $this->wp_plugins_dir . '/freeride-automated-trading-plan/freeride-automated-trading-plan.php';
        $checks['main_file'] = file_exists($main_file) ? '✅' : '❌';
        
        // Check required directories
        $dirs = array('includes', 'templates', 'assets');
        foreach ($dirs as $dir) {
            $path = $this->wp_plugins_dir . '/freeride-automated-trading-plan/' . $dir;
            $checks[$dir] = is_dir($path) ? '✅' : '❌';
        }
        
        // Check if plugin is active
        $checks['active'] = is_plugin_active('freeride-automated-trading-plan/freeride-automated-trading-plan.php') ? '✅ Active' : '❌ Not Active';
        
        return $checks;
    }
    
    /**
     * Setup plugin (create tables, pages, etc.)
     */
    public function setup() {
        $results = array();
        
        // Create database tables
        if (class_exists('FRATP_Database')) {
            FRATP_Database::create_tables();
            $results[] = '✅ Database tables created';
        }
        
        // Create user roles
        if (class_exists('FRATP_Membership')) {
            FRATP_Membership::create_user_roles();
            $results[] = '✅ User roles created';
        }
        
        // Create pages
        $pages = $this->create_required_pages();
        $results[] = "✅ Created {$pages} page(s)";
        
        // Schedule cron
        if (!wp_next_scheduled('fratp_daily_plan_generation')) {
            $schedule_time = strtotime('today 9:30 AM');
            if ($schedule_time < time()) {
                $schedule_time = strtotime('tomorrow 9:30 AM');
            }
            wp_schedule_event($schedule_time, 'daily', 'fratp_daily_plan_generation');
            $results[] = '✅ Cron job scheduled';
        }
        
        return implode('<br>', $results);
    }
    
    /**
     * Create required pages
     */
    private function create_required_pages() {
        $pages = array(
            array('title' => 'Premium Signup', 'slug' => 'premium-signup', 'content' => '[fratp_premium_signup]'),
            array('title' => 'Trading Plans', 'slug' => 'trading-plans', 'content' => '[fratp_plans_list]'),
        );
        
        $created = 0;
        foreach ($pages as $page_data) {
            if (!get_page_by_path($page_data['slug'])) {
                wp_insert_post(array(
                    'post_title' => $page_data['title'],
                    'post_name' => $page_data['slug'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                ));
                $created++;
            }
        }
        
        return $created;
    }
    
    /**
     * Copy directory recursively
     */
    private function copy_directory($source, $dest) {
        if (!is_dir($dest)) {
            wp_mkdir_p($dest);
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $dest_path = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!is_dir($dest_path)) {
                    wp_mkdir_p($dest_path);
                }
            } else {
                copy($item, $dest_path);
            }
        }
    }
}

// Web interface
if (php_sapi_name() !== 'cli') {
    // Load WordPress
    $wp_load_paths = array('../../../wp-load.php', '../../../../wp-load.php');
    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists(__DIR__ . '/' . $path)) {
            require_once(__DIR__ . '/' . $path);
            $wp_loaded = true;
            break;
        }
    }
    
    if (!$wp_loaded || !current_user_can('manage_options')) {
        die('Access denied.');
    }
    
    $deployer = new FRATP_Deployer();
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'verify';
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Plugin Deployer</title>
        <style>
            body { font-family: Arial; max-width: 800px; margin: 40px auto; padding: 20px; }
            .button { display: inline-block; padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; margin: 5px; border-radius: 4px; }
            .result { padding: 15px; margin: 10px 0; background: #f0f8ff; border-left: 4px solid #0073aa; }
        </style>
    </head>
    <body>
        <h1>Plugin Deployer</h1>
        <p>
            <a href="?action=deploy" class="button">Deploy Plugin</a>
            <a href="?action=verify" class="button">Verify Installation</a>
            <a href="?action=setup" class="button">Run Setup</a>
        </p>
        
        <?php
        if ($action === 'deploy') {
            echo '<div class="result">' . $deployer->deploy() . '</div>';
        } elseif ($action === 'verify') {
            $checks = $deployer->verify();
            echo '<div class="result"><h3>Verification Results:</h3><ul>';
            foreach ($checks as $check => $status) {
                echo '<li>' . esc_html($check) . ': ' . esc_html($status) . '</li>';
            }
            echo '</ul></div>';
        } elseif ($action === 'setup') {
            echo '<div class="result">' . $deployer->setup() . '</div>';
        }
        ?>
    </body>
    </html>
    <?php
    exit;
}

// CLI interface
if (php_sapi_name() === 'cli') {
    // Parse command line arguments
    $options = getopt('', array('action:'));
    $action = isset($options['action']) ? $options['action'] : 'help';
    
    // Load WordPress (adjust path as needed)
    require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');
    
    $deployer = new FRATP_Deployer();
    
    switch ($action) {
        case 'deploy':
            echo $deployer->deploy() . "\n";
            break;
        case 'verify':
            $checks = $deployer->verify();
            foreach ($checks as $check => $status) {
                echo "{$check}: {$status}\n";
            }
            break;
        case 'setup':
            echo $deployer->setup() . "\n";
            break;
        default:
            echo "Usage: php plugin-deployer.php --action=deploy|verify|setup\n";
    }
}



