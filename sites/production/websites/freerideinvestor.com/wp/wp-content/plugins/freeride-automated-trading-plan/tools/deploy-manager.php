<?php
/**
 * Plugin Deployment & Management Tool
 * 
 * This tool helps you:
 * - Deploy plugin to server
 * - Verify installation
 * - Check plugin status
 * - Generate test plans
 * - Manage settings
 * 
 * Access: yoursite.com/wp-content/plugins/freeride-automated-trading-plan/tools/deploy-manager.php
 * 
 * DELETE THIS FILE AFTER SETUP FOR SECURITY!
 */

// Load WordPress
$wp_load_paths = array(
    '../../../wp-load.php',
    '../../../../wp-load.php',
    '../../../../../wp-load.php',
);

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        require_once(__DIR__ . '/' . $path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('Could not load WordPress. Make sure this file is in the correct location.');
}

// Security check
if (!current_user_can('manage_options')) {
    die('You must be an administrator to use this tool.');
}

// Handle actions
$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'status';
$message = '';
$message_type = 'info';

if (isset($_POST['deploy_action'])) {
    check_admin_referer('fratp_deploy_action');
    
    switch ($_POST['deploy_action']) {
        case 'verify_files':
            $message = verify_plugin_files();
            $message_type = 'success';
            break;
        case 'create_pages':
            $message = create_required_pages();
            $message_type = 'success';
            break;
        case 'generate_test_plan':
            $symbol = isset($_POST['symbol']) ? sanitize_text_field($_POST['symbol']) : 'TSLA';
            $message = generate_test_plan($symbol);
            $message_type = 'success';
            break;
        case 'check_status':
            $message = check_plugin_status();
            $message_type = 'info';
            break;
    }
}

function verify_plugin_files() {
    $plugin_dir = plugin_dir_path(__FILE__) . '../';
    $required_files = array(
        'freeride-automated-trading-plan.php',
        'includes/class-fratp-strategy-calculator.php',
        'includes/class-fratp-market-data.php',
        'includes/class-fratp-plan-generator.php',
        'includes/class-fratp-database.php',
        'includes/class-fratp-tbow-generator.php',
        'includes/class-fratp-membership.php',
    );
    
    $missing = array();
    foreach ($required_files as $file) {
        if (!file_exists($plugin_dir . $file)) {
            $missing[] = $file;
        }
    }
    
    if (empty($missing)) {
        return '✅ All required plugin files are present!';
    } else {
        return '❌ Missing files: ' . implode(', ', $missing);
    }
}

function create_required_pages() {
    $pages = array(
        array(
            'title' => 'Premium Signup',
            'slug' => 'premium-signup',
            'content' => '[fratp_premium_signup]',
        ),
        array(
            'title' => 'Trading Plans',
            'slug' => 'trading-plans',
            'content' => '[fratp_plans_list]',
        ),
    );
    
    $created = 0;
    foreach ($pages as $page_data) {
        $existing = get_page_by_path($page_data['slug']);
        if (!$existing) {
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_name' => $page_data['slug'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
            ));
            if ($page_id) {
                $created++;
                if ($page_data['slug'] === 'premium-signup') {
                    update_option('fratp_premium_signup_page', $page_id);
                }
            }
        }
    }
    
    return "✅ Created {$created} page(s). Pages already existed: " . (count($pages) - $created);
}

function generate_test_plan($symbol) {
    if (!class_exists('FRATP_Plan_Generator')) {
        return '❌ Plugin classes not loaded. Make sure plugin is activated.';
    }
    
    $plan_generator = new FRATP_Plan_Generator();
    $plan = $plan_generator->generate_plan($symbol);
    
    if (is_wp_error($plan)) {
        return '❌ Error: ' . $plan->get_error_message();
    }
    
    $tbow_created = '';
    if (get_option('fratp_create_tbow_posts', true)) {
        $plugin = FRATP_Plugin::get_instance();
        $reflection = new ReflectionClass($plugin);
        $method = $reflection->getMethod('create_tbow_post');
        $method->setAccessible(true);
        $post_id = $method->invoke($plugin, $plan);
        if ($post_id && !is_wp_error($post_id)) {
            $tbow_created = " | TBOW Post ID: {$post_id}";
        }
    }
    
    return "✅ Test plan generated for {$symbol}! Signal: {$plan['signal']}{$tbow_created}";
}

function check_plugin_status() {
    $status = array();
    
    // Check if plugin is active
    if (is_plugin_active('freeride-automated-trading-plan/freeride-automated-trading-plan.php')) {
        $status[] = '✅ Plugin is ACTIVE';
    } else {
        $status[] = '❌ Plugin is NOT ACTIVE';
    }
    
    // Check database tables
    global $wpdb;
    $table_name = $wpdb->prefix . 'fratp_trading_plans';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name;
    if ($table_exists) {
        $status[] = '✅ Database table exists';
    } else {
        $status[] = '❌ Database table missing';
    }
    
    // Check cron job
    $next_run = wp_next_scheduled('fratp_daily_plan_generation');
    if ($next_run) {
        $status[] = '✅ Cron job scheduled: ' . date('Y-m-d H:i:s', $next_run);
    } else {
        $status[] = '❌ Cron job not scheduled';
    }
    
    // Check API keys
    $av_key = get_option('fratp_alpha_vantage_key');
    $fh_key = get_option('fratp_finnhub_key');
    if ($av_key || $fh_key) {
        $status[] = '✅ API key(s) configured';
    } else {
        $status[] = '⚠️ No API keys configured';
    }
    
    // Check user roles
    $free_role = get_role('fratp_free');
    $premium_role = get_role('fratp_premium');
    if ($free_role && $premium_role) {
        $status[] = '✅ User roles created';
    } else {
        $status[] = '❌ User roles missing';
    }
    
    return implode('<br>', $status);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>FreeRide Trading Plan - Deployment Manager</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #0073aa; margin-top: 0; }
        .section { margin: 30px 0; padding: 20px; background: #f9f9f9; border-radius: 5px; }
        .button { display: inline-block; padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; margin: 5px; border: none; cursor: pointer; }
        .button:hover { background: #005a87; }
        .button-secondary { background: #666; }
        .status-box { padding: 15px; background: #e7f5e7; border-left: 4px solid #00a32a; margin: 20px 0; }
        .error-box { padding: 15px; background: #ffe6e6; border-left: 4px solid #d63638; margin: 20px 0; }
        .info-box { padding: 15px; background: #f0f8ff; border-left: 4px solid #0073aa; margin: 20px 0; }
        form { margin: 15px 0; }
        input[type="text"] { padding: 8px; width: 200px; margin: 5px; }
        .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0; }
        .quick-link { padding: 15px; background: #f0f8ff; border-radius: 5px; text-align: center; }
        .quick-link a { color: #0073aa; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 FreeRide Automated Trading Plan - Deployment Manager</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $message_type === 'error' ? 'error-box' : ($message_type === 'success' ? 'status-box' : 'info-box'); ?>">
                <?php echo wp_kses_post($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>Quick Actions</h2>
            <div class="quick-links">
                <div class="quick-link">
                    <h3>Plugin Status</h3>
                    <form method="post">
                        <?php wp_nonce_field('fratp_deploy_action'); ?>
                        <input type="hidden" name="deploy_action" value="check_status">
                        <button type="submit" class="button">Check Status</button>
                    </form>
                </div>
                
                <div class="quick-link">
                    <h3>Verify Files</h3>
                    <form method="post">
                        <?php wp_nonce_field('fratp_deploy_action'); ?>
                        <input type="hidden" name="deploy_action" value="verify_files">
                        <button type="submit" class="button">Verify All Files</button>
                    </form>
                </div>
                
                <div class="quick-link">
                    <h3>Create Pages</h3>
                    <form method="post">
                        <?php wp_nonce_field('fratp_deploy_action'); ?>
                        <input type="hidden" name="deploy_action" value="create_pages">
                        <button type="submit" class="button">Create Required Pages</button>
                    </form>
                </div>
                
                <div class="quick-link">
                    <h3>Generate Test Plan</h3>
                    <form method="post">
                        <?php wp_nonce_field('fratp_deploy_action'); ?>
                        <input type="hidden" name="deploy_action" value="generate_test_plan">
                        <input type="text" name="symbol" value="TSLA" placeholder="Stock Symbol">
                        <button type="submit" class="button">Generate</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>Plugin Information</h2>
            <p><strong>Plugin Name:</strong> FreeRide Automated Trading Plan</p>
            <p><strong>Version:</strong> 1.0.0</p>
            <p><strong>Location:</strong> <?php echo esc_html(plugin_dir_path(__FILE__) . '../'); ?></p>
            <p><strong>Plugin URL:</strong> <?php echo esc_url(plugin_dir_url(__FILE__) . '../'); ?></p>
        </div>
        
        <div class="section">
            <h2>WordPress Admin Links</h2>
            <p>
                <a href="<?php echo admin_url('plugins.php'); ?>" class="button" target="_blank">View Plugins Page</a>
                <a href="<?php echo admin_url('admin.php?page=fratp-settings'); ?>" class="button" target="_blank">Plugin Settings</a>
                <a href="<?php echo admin_url('admin.php?page=fratp-daily-plans'); ?>" class="button" target="_blank">Daily Plans</a>
                <a href="<?php echo admin_url('admin.php?page=fratp-preview'); ?>" class="button" target="_blank">Preview Templates</a>
            </p>
        </div>
        
        <div class="section">
            <h2>Frontend Pages</h2>
            <p>
                <a href="<?php echo home_url('/premium-signup'); ?>" class="button" target="_blank">Premium Signup</a>
                <a href="<?php echo home_url('/trading-plans'); ?>" class="button" target="_blank">Trading Plans</a>
            </p>
        </div>
        
        <div class="section">
            <h2>Current Status</h2>
            <?php echo wp_kses_post(check_plugin_status()); ?>
        </div>
        
        <div class="info-box" style="margin-top: 30px;">
            <strong>⚠️ Security Note:</strong> Delete this file after setup is complete for security!
            <br><code><?php echo esc_html(__FILE__); ?></code>
        </div>
    </div>
</body>
</html>

