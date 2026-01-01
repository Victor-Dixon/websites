<?php
/**
 * QUICK SETUP SCRIPT
 * Run this once to set up everything quickly
 * 
 * Access via: yoursite.com/wp-content/plugins/freeride-automated-trading-plan/SETUP_NOW.php
 * 
 * DELETE THIS FILE AFTER SETUP FOR SECURITY!
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('You must be an administrator to run this script.');
}

echo '<h1>FreeRide Automated Trading Plan - Quick Setup</h1>';

// Step 1: Create pages
echo '<h2>Step 1: Creating Pages...</h2>';

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
    array(
        'title' => 'Test Trading Plans',
        'slug' => 'test-trading-plans',
        'content' => file_get_contents(__DIR__ . '/templates/frontend/test-page.php'),
    ),
);

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
            echo '<p>✅ Created page: <a href="' . get_permalink($page_id) . '" target="_blank">' . $page_data['title'] . '</a></p>';
            
            // Set premium signup page in options
            if ($page_data['slug'] === 'premium-signup') {
                update_option('fratp_premium_signup_page', $page_id);
            }
        } else {
            echo '<p>❌ Failed to create: ' . $page_data['title'] . '</p>';
        }
    } else {
        echo '<p>ℹ️ Page already exists: <a href="' . get_permalink($existing->ID) . '" target="_blank">' . $page_data['title'] . '</a></p>';
    }
}

// Step 2: Create user roles
echo '<h2>Step 2: Creating User Roles...</h2>';
require_once(__DIR__ . '/includes/class-fratp-membership.php');
FRATP_Membership::create_user_roles();
echo '<p>✅ User roles created</p>';

// Step 3: Create database tables
echo '<h2>Step 3: Creating Database Tables...</h2>';
require_once(__DIR__ . '/includes/class-fratp-database.php');
FRATP_Database::create_tables();
echo '<p>✅ Database tables created</p>';

// Step 4: Schedule cron
echo '<h2>Step 4: Scheduling Daily Generation...</h2>';
if (!wp_next_scheduled('fratp_daily_plan_generation')) {
    $schedule_time = strtotime('today 9:30 AM');
    if ($schedule_time < time()) {
        $schedule_time = strtotime('tomorrow 9:30 AM');
    }
    wp_schedule_event($schedule_time, 'daily', 'fratp_daily_plan_generation');
    echo '<p>✅ Cron job scheduled for: ' . date('Y-m-d H:i:s', $schedule_time) . '</p>';
} else {
    $next = wp_next_scheduled('fratp_daily_plan_generation');
    echo '<p>ℹ️ Cron already scheduled for: ' . date('Y-m-d H:i:s', $next) . '</p>';
}

// Step 5: Generate test plan
echo '<h2>Step 5: Generating Test Plan...</h2>';
if (class_exists('FRATP_Plan_Generator')) {
    require_once(__DIR__ . '/includes/class-fratp-plan-generator.php');
    require_once(__DIR__ . '/includes/class-fratp-strategy-calculator.php');
    require_once(__DIR__ . '/includes/class-fratp-market-data.php');
    
    $plan_generator = new FRATP_Plan_Generator();
    $plan = $plan_generator->generate_plan('TSLA');
    
    if (is_wp_error($plan)) {
        echo '<p>❌ Error generating plan: ' . $plan->get_error_message() . '</p>';
        echo '<p><strong>Note:</strong> You need to configure API keys in Settings first!</p>';
    } else {
        echo '<p>✅ Test plan generated for TSLA</p>';
        
        // Create TBOW post if enabled
        if (get_option('fratp_create_tbow_posts', true)) {
            require_once(__DIR__ . '/includes/class-fratp-tbow-generator.php');
            $plugin = FRATP_Plugin::get_instance();
            $reflection = new ReflectionClass($plugin);
            $method = $reflection->getMethod('create_tbow_post');
            $method->setAccessible(true);
            $post_id = $method->invoke($plugin, $plan);
            
            if ($post_id && !is_wp_error($post_id)) {
                echo '<p>✅ TBOW post created: <a href="' . get_permalink($post_id) . '" target="_blank">View Post</a></p>';
            }
        }
    }
} else {
    echo '<p>⚠️ Plan generator not loaded. Make sure plugin is activated.</p>';
}

// Summary
echo '<hr>';
echo '<h2>✅ Setup Complete!</h2>';
echo '<h3>View Your Pages:</h3>';
echo '<ul>';
echo '<li><a href="' . home_url('/premium-signup') . '" target="_blank">Premium Signup Page</a></li>';
echo '<li><a href="' . home_url('/trading-plans') . '" target="_blank">Trading Plans Page</a></li>';
echo '<li><a href="' . home_url('/test-trading-plans') . '" target="_blank">Test Page (All Shortcodes)</a></li>';
echo '</ul>';

echo '<h3>Next Steps:</h3>';
echo '<ol>';
echo '<li>Go to <strong>Trading Plans → Settings</strong> and configure API keys</li>';
echo '<li>Set your stock symbols (default: TSLA)</li>';
echo '<li>Test the premium signup page</li>';
echo '<li>Generate a plan manually or wait for daily generation</li>';
echo '<li><strong>DELETE THIS FILE (SETUP_NOW.php) FOR SECURITY!</strong></li>';
echo '</ol>';

echo '<p><strong>⚠️ IMPORTANT: Delete this file after setup!</strong></p>';



