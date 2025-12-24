<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\functions.php
Description: Core functions and theme setup for The Trading Robot Plug theme, including REST API endpoints for data fetching and AI integrations.
Version: 1.1.0
Author: Victor Dixon
*/

// Theme Setup: Enhanced for Trading Robot Plug
function my_custom_theme_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption'
    ));
    add_theme_support('automatic-feed-links');

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
        'footer' => __('Footer Menu', 'my-custom-theme'),
    ));
}

add_action('after_setup_theme', 'my_custom_theme_setup');

// Enqueue theme styles and scripts
function my_custom_theme_scripts()
{
    wp_enqueue_style('my-custom-theme-style', get_stylesheet_uri());
    wp_enqueue_style('my-custom-theme-custom-css', get_template_directory_uri() . '/assets/css/custom.css', array(), '1.0.0');
    wp_enqueue_script('my-custom-theme-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'my_custom_theme_scripts');

// REST API: Fetch Alpha Vantage Data
function fetch_alpha_vantage_data()
{
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/alpha_vantage_fetcher.py');
    $output = shell_exec($command);

    if ($output === null) {
        error_log('Alpha Vantage data fetch failed.');
        return new WP_Error('no_data', 'Failed to fetch data from Alpha Vantage', array('status' => 500));
    }

    return json_decode($output, true);
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchdata', array(
        'methods' => 'GET',
        'callback' => 'fetch_alpha_vantage_data',
        'permission_callback' => '__return_true', // Public access for charts
    ));
});

// REST API: Fetch Polygon Data
function fetch_polygon_data()
{
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/polygon_fetcher.py');
    $output = shell_exec($command);

    if ($output === null) {
        error_log('Polygon data fetch failed.');
        return new WP_Error('no_data', 'Failed to fetch data from Polygon', array('status' => 500));
    }

    return json_decode($output, true);
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchpolygondata', array(
        'methods' => 'GET',
        'callback' => 'fetch_polygon_data',
        'permission_callback' => '__return_true', // Public access for charts
    ));
});

// REST API: Fetch Real-Time Data
function fetch_real_time_data()
{
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/real_time_fetcher.py');
    $output = shell_exec($command);

    if ($output === null) {
        error_log('Real-time data fetch failed.');
        return new WP_Error('no_data', 'Failed to fetch real-time data', array('status' => 500));
    }

    return json_decode($output, true);
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchrealtime', array(
        'methods' => 'GET',
        'callback' => 'fetch_real_time_data',
        'permission_callback' => '__return_true', // Public access for charts
    ));
});

// REST API: Fetch Trading Signals
function fetch_trading_signals()
{
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/fetch_trading_signals.py');
    $output = shell_exec($command);

    if ($output === null) {
        error_log('Trading signals fetch failed.');
        return new WP_Error('trading_signals_error', 'Error executing trading signals script.', array('status' => 500));
    }

    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Invalid JSON response from trading signals script.');
        return new WP_Error('trading_signals_error', 'Invalid response from trading signals script.', array('status' => 500));
    }

    return $data;
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchsignals', array(
        'methods' => 'GET',
        'callback' => 'fetch_trading_signals',
        'permission_callback' => function () {
            return current_user_can('edit_posts'); // Adjust capability as necessary
        },
    ));
});

// REST API: Fetch AI Suggestions
function fetch_ai_suggestions()
{
    $features = 'close, volume, RSI, SMA_10'; // Adjust features as necessary
    $command = escapeshellcmd("python3 " . get_template_directory() . "/scripts/openai_utils.py suggest_new_features \"$features\"");
    $output = shell_exec($command);

    if ($output === null) {
        error_log('AI suggestions fetch failed.');
        return new WP_Error('ai_suggestions_error', 'Error executing OpenAI suggestions script.', array('status' => 500));
    }

    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Invalid JSON response from OpenAI suggestions script.');
        return new WP_Error('ai_suggestions_error', 'Invalid response from OpenAI suggestions script.', array('status' => 500));
    }

    return $data;
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchaisuggestions', array(
        'methods' => 'GET',
        'callback' => 'fetch_ai_suggestions',
        'permission_callback' => function () {
            return current_user_can('edit_posts'); // Adjust capability as necessary
        },
    ));
});

// REST API: Query Stock Data
function query_stock_data()
{
    global $wpdb;
    $symbol = isset($_GET['symbol']) ? sanitize_text_field($_GET['symbol']) : '';
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

    if (!$symbol || !$start_date || !$end_date) {
        return new WP_Error('missing_data', 'Missing required parameters', array('status' => 400));
    }

    $query = $wpdb->prepare(
        "SELECT * FROM stock_data WHERE symbol = %s AND date BETWEEN %s AND %s ORDER BY date",
        $symbol,
        $start_date,
        $end_date
    );
    $results = $wpdb->get_results($query, ARRAY_A);

    if (empty($results)) {
        return new WP_Error('no_data', 'No data found for the given parameters', array('status' => 404));
    }

    return $results;
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/querystockdata', array(
        'methods' => 'GET',
        'callback' => 'query_stock_data',
        'permission_callback' => '__return_true', // Public access for charts
    ));
});

// Include the admin theme options
require get_template_directory() . '/admin/theme-options.php';

// Include custom shortcodes
require get_template_directory() . '/admin/shortcodes.php';

// Fix REST API permissions for public chart access (Agent-3 Infrastructure Fix)
add_filter('rest_authentication_errors', function ($result) {
    // Allow public access to trading data endpoints
    if (!empty($result)) {
        return $result;
    }

    // Check if this is a trading data endpoint
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($request_uri, '/tradingrobotplug/v1/') !== false) {
        // Allow public access to trading endpoints for charts
        return true;
    }

    return $result;
});

// Template include filter for missing pages (Agent-6 Site Audit Fix)
// Enhanced template loading fix added below
add_filter('template_include', function ($template) {
    if (is_404() && isset($_SERVER['REQUEST_URI'])) {
        $request_uri = trim($_SERVER['REQUEST_URI'], '/');
        $page_templates = array(
            'products' => 'page-products.php',
            'features' => 'page-features.php',
            'pricing' => 'page-pricing.php',
            'blog' => 'page-blog.php',
            'ai-swarm' => 'page-ai-swarm.php',
            'contact' => 'page-contact.php',
        );

        if (isset($page_templates[$request_uri])) {
            $new_template = locate_template($page_templates[$request_uri]);
            if ($new_template) {
                // Set up WordPress query to treat this as a page
                global $wp_query;
                $wp_query->is_404 = false;
                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                $wp_query->queried_object = (object) array(
                    'post_type' => 'page',
                    'post_name' => $request_uri,
                );
                return $new_template;
            }
        }
    }
    return $template;
});

/**
 * Add Google Analytics 4 and Facebook Pixel tracking codes
 * Generated by batch_analytics_setup.py
 */
function add_analytics_tracking_codes() {
    // Google Analytics 4 (GA4)
        echo '<!-- Google Analytics 4 (GA4) -->\n';
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>\n';
        echo '<script>\n';
        echo 'window.dataLayer = window.dataLayer || [];\n';
        echo 'function gtag(){dataLayer.push(arguments);}\n';
        echo 'gtag(\'js\', new Date());\n';
        echo 'gtag(\'config\', \'G-XXXXXXXXXX\', {\n';
        echo '\'page_path\': window.location.pathname,\n';
        echo '\'page_title\': document.title,\n';
        echo '});\n';
        echo '// Custom Events Tracking\n';
        echo '// Track waitlist_submit event\n';
        echo 'gtag("event", "waitlist_submit", {\n';
        echo '"event_category": "engagement",\n';
        echo '"event_label": "waitlist_submit"\n';
        echo '});\n';
        echo '// Track contact_form_submit event\n';
        echo 'gtag("event", "contact_form_submit", {\n';
        echo '"event_category": "engagement",\n';
        echo '"event_label": "contact_form_submit"\n';
        echo '});\n';
        echo '</script>\n';
        echo '<!-- End GA4 -->\n';
    
    // Facebook Pixel
        echo '<!-- Facebook Pixel Code -->\n';
        echo '<script>\n';
        echo '!function(f,b,e,v,n,t,s)\n';
        echo '{{if(f.fbq)return;n=f.fbq=function(){{n.callMethod?\n';
        echo 'n.callMethod.apply(n,arguments):n.queue.push(arguments)}};\n';
        echo 'if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';\n';
        echo 'n.queue=[];t=b.createElement(e);t.async=!0;\n';
        echo 't.src=v;s=b.getElementsByTagName(e)[0];\n';
        echo 's.parentNode.insertBefore(t,s)}}(window, document,\'script\',\n';
        echo '\'https://connect.facebook.net/en_US/fbevents.js\');\n';
        echo 'fbq(\'init\', \'YOUR_PIXEL_ID\');\n';
        echo 'fbq(\'track\', \'PageView\');\n';
        echo '</script>\n';
        echo '<noscript>\n';
        echo '<img height="1" width="1" style="display:none"\n';
        echo 'src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1"/>\n';
        echo '</noscript>\n';
        echo '<!-- End Facebook Pixel Code -->\n';
}
add_action('wp_head', 'add_analytics_tracking_codes', 99);


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

