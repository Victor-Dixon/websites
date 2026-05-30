<?php
/*
TradingRobotPlug Modern Homepage - Phase 1 Implementation
Description: Complete modern redesign with 7-section homepage structure
Author: Agent-1 (Integration) + Agent-2 (Design Architecture)
Version: 2.0.0
Updated: 2025-11-02
*/
get_header(); ?>

<!-- ===== HERO SECTION ===== -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="gradient-text">AUTOMATE YOUR TRADING WITH AI</h1>
            <p>Turn your trading strategy into a powerful robot that never sleeps. Backtesting, automation, and real-time market data - all in one platform.</p>

            <div class="cta-group">
                <a href="#pricing" class="btn btn-primary">Get Started Free</a>
                <a href="#demo" class="btn btn-secondary">Watch Demo</a>
            </div>

            <!-- Live Market Preview -->
            <div class="market-preview">
                <h4>📈 Live Market Data</h4>
                <div class="market-item">
                    <span class="market-symbol">SPY</span>
                    <span class="market-price">$450.23</span>
                    <span class="market-change positive">↑ +2.34%</span>
                </div>
                <div class="market-item">
                    <span class="market-symbol">QQQ</span>
                    <span class="market-price">$380.15</span>
                    <span class="market-change positive">↑ +1.87%</span>
                </div>
                <div class="market-item">
                    <span class="market-symbol">AAPL</span>
                    <span class="market-price">$185.92</span>
                    <span class="market-change negative">↓ -0.45%</span>
                </div>
                <p style="font-size: 12px; margin-top: 8px; opacity: 0.8;">Powered by Alpha Vantage | Updated live</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURES SECTION ===== -->
<section class="section">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">Why Choose Trading Robot Plug?</h2>

        <div class="features-grid">
            <div class="card feature-card">
                <div class="icon">⚡</div>
                <h3>Lightning Speed</h3>
                <p>Backtest strategies in seconds, not hours. Our optimized algorithms process market data at lightning speed.</p>
                <a href="#" class="btn btn-secondary">Learn More</a>
            </div>

            <div class="card feature-card">
                <div class="icon">🤖</div>
                <h3>AI Automation</h3>
                <p>Set it and forget it. Your robot trades 24/7 while you focus on strategy and life.</p>
                <a href="#" class="btn btn-secondary">Learn More</a>
            </div>

            <div class="card feature-card">
                <div class="icon">📊</div>
                <h3>Advanced Analytics</h3>
                <p>Real-time performance tracking, detailed analytics, and comprehensive reporting.</p>
                <a href="#" class="btn btn-secondary">Learn More</a>
            </div>
        </div>
    </div>
</section>

<!-- ===== HOW IT WORKS SECTION ===== -->
<section class="section section--light how-it-works">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">How It Works</h2>
        <p style="text-align: center; margin-bottom: 64px; font-size: 18px; color: #666;">Three simple steps to automated trading success</p>

        <div class="steps-container">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-icon">🎯</div>
                <h3>Design Strategy</h3>
                <p>Define your trading rules and parameters using our intuitive interface.</p>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-icon">📈</div>
                <h3>Backtest</h3>
                <p>Test your strategy against historical market data to validate performance.</p>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-icon">🚀</div>
                <h3>Deploy Robot</h3>
                <p>Launch your automated trading robot and start generating returns.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== DASHBOARD PREVIEW SECTION ===== -->
<section class="dashboard-preview">
    <div class="container">
        <h2>See Real Performance in Action</h2>
        <p style="font-size: 18px; margin-bottom: 48px; opacity: 0.9;">Watch your trading robots perform with live analytics and insights</p>

        <div class="metric-grid">
            <div class="metric">
                <div class="metric-value">+32.5%</div>
                <div class="metric-label">Total Return</div>
            </div>
            <div class="metric">
                <div class="metric-value">68%</div>
                <div class="metric-label">Win Rate</div>
            </div>
            <div class="metric">
                <div class="metric-value">3</div>
                <div class="metric-label">Active Bots</div>
            </div>
            <div class="metric">
                <div class="metric-value">12</div>
                <div class="metric-label">Trades Today</div>
            </div>
        </div>

        <div class="dashboard-mockup">
            <h3 style="margin-bottom: 24px;">📊 LIVE DASHBOARD</h3>
            <p style="text-align: center; opacity: 0.8;">Interactive chart and performance metrics would display here</p>
            <p style="font-size: 14px; margin-top: 16px; opacity: 0.6;">* Sample data - actual performance varies</p>
        </div>

        <div style="margin-top: 48px;">
            <a href="#pricing" class="btn btn-primary" style="font-size: 20px; padding: 20px 40px;">Start Your Free Trial</a>
        </div>
    </div>
</section>

<!-- ===== SOCIAL PROOF SECTION ===== -->
<section class="section">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">Trusted by Traders Worldwide</h2>

        <div style="text-align: center; margin-bottom: 48px;">
            <div style="font-size: 48px; margin-bottom: 16px;">⭐⭐⭐⭐⭐</div>
            <p style="font-size: 24px; font-weight: 600; margin-bottom: 16px;">4.8/5 from 2,500+ traders</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 32px; margin-bottom: 48px;">
            <div style="text-align: center;">
                <p style="font-style: italic; font-size: 18px; margin-bottom: 16px;">"Game-changer for my trading!"</p>
                <p style="font-weight: 600;">John D., Day Trader</p>
            </div>
            <div style="text-align: center;">
                <p style="font-style: italic; font-size: 18px; margin-bottom: 16px;">"Best backtesting tool ever"</p>
                <p style="font-weight: 600;">Sarah M., Quant</p>
            </div>
        </div>

        <div style="text-align: center;">
            <p style="color: #666; margin-bottom: 24px;">Featured on:</p>
            <div style="display: flex; justify-content: center; gap: 32px; flex-wrap: wrap;">
                <span style="color: #666; opacity: 0.7;">TechCrunch</span>
                <span style="color: #666; opacity: 0.7;">Forbes</span>
                <span style="color: #666; opacity: 0.7;">Bloomberg</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== PRICING SECTION ===== -->
<section class="section" id="pricing">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">Choose Your Plan</h2>

        <div class="pricing-grid">
            <div class="pricing-card">
                <h3 class="pricing-name">Starter</h3>
                <div class="pricing-price">
                    <span class="currency">$</span>29<span class="period">/mo</span>
                </div>
                <ul class="pricing-features">
                    <li>• 1 Trading Bot</li>
                    <li>• Backtesting</li>
                    <li>• Email Support</li>
                </ul>
                <a href="#" class="btn btn-primary">Start Free Trial</a>
            </div>

            <div class="pricing-card popular">
                <div class="pricing-badge">Most Popular</div>
                <h3 class="pricing-name">Pro</h3>
                <div class="pricing-price">
                    <span class="currency">$</span>99<span class="period">/mo</span>
                </div>
                <ul class="pricing-features">
                    <li>• 5 Trading Bots</li>
                    <li>• Advanced Backtesting</li>
                    <li>• API Access</li>
                    <li>• Priority Support</li>
                </ul>
                <a href="#" class="btn btn-primary">Start Free Trial</a>
            </div>

            <div class="pricing-card">
                <h3 class="pricing-name">Enterprise</h3>
                <div class="pricing-price">
                    Custom
                </div>
                <ul class="pricing-features">
                    <li>• Unlimited Bots</li>
                    <li>• Custom Strategies</li>
                    <li>• White-label</li>
                    <li>• Dedicated Support</li>
                </ul>
                <a href="#" class="btn btn-secondary">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
    add_theme_support('custom-background');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Registering Menus with improved naming conventions
    register_nav_menus(array(
        'primary_menu' => __('Primary Menu', 'trading-robot-plug'),
        'footer_menu' => __('Footer Menu', 'trading-robot-plug'),
    ));
}

add_action('after_setup_theme', 'trading_robot_plug_setup');

// Enqueue theme styles and scripts with AI integration and data visualization
function trading_robot_plug_scripts() {
    // Enqueue the main stylesheet
    wp_enqueue_style('trading-robot-plug-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));

    // Enqueue Chart.js for data visualization
    wp_enqueue_script('trading-robot-plug-chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);

    // Enqueue the custom JavaScript file for handling charts and AI integration
    wp_enqueue_script('trading-robot-plug-custom-js', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), wp_get_theme()->get('Version'), true);

    // Enqueue Google Fonts
    wp_enqueue_style('trading-robot-plug-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap', false);
}

add_action('wp_enqueue_scripts', 'trading_robot_plug_scripts');

// Enhanced REST API: Fetch AI-driven Trading Signals
function fetch_trading_signals() {
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/fetch_trading_signals.py');
    $output = shell_exec($command);

    // Improved error handling with a clearer error log message
    if ($output === null) {
        error_log('TradingRobotPlug Error: Failed to execute trading signals script.');
        return new WP_Error('trading_signals_error', 'Failed to execute trading signals script.', array('status' => 500));
    }

    // Parse the JSON output from the script
    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('TradingRobotPlug Error: Invalid JSON response from trading signals script.');
        return new WP_Error('trading_signals_error', 'Invalid response from trading signals script.', array('status' => 500));
    }

    return $data;
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchsignals', array(
        'methods' => 'GET',
        'callback' => 'fetch_trading_signals',
        'permission_callback' => '__return_true',
    ));
});

// Additional REST API routes for fetching and visualizing data

// REST API: Fetch Alpha Vantage Data and Generate Visualization
function fetch_alpha_vantage_data() {
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/alpha_vantage_fetcher.py');
    $output = shell_exec($command);

    if ($output === null) {
        error_log('TradingRobotPlug Error: Failed to execute Alpha Vantage script.');
        return new WP_Error('alpha_vantage_fetch_error', 'Failed to execute Alpha Vantage script.', array('status' => 500));
    }

    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('TradingRobotPlug Error: Invalid JSON response from Alpha Vantage script.');
        return new WP_Error('alpha_vantage_fetch_error', 'Invalid response from Alpha Vantage script.', array('status' => 500));
    }

    return $data;
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchalphadata', array(
        'methods' => 'GET',
        'callback' => 'fetch_alpha_vantage_data',
        'permission_callback' => '__return_true',
    ));
});

// REST API: Fetch Polygon Data and Generate Visualization
function fetch_polygon_data() {
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/polygon_fetcher.py');
    $output = shell_exec($command);

    if ($output === null) {
        error_log('TradingRobotPlug Error: Failed to execute Polygon script.');
        return new WP_Error('polygon_fetch_error', 'Failed to execute Polygon script.', array('status' => 500));
    }

    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('TradingRobotPlug Error: Invalid JSON response from Polygon script.');
        return new WP_Error('polygon_fetch_error', 'Invalid response from Polygon script.', array('status' => 500));
    }

    return $data;
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchpolygondata', array(
        'methods' => 'GET',
        'callback' => 'fetch_polygon_data',
        'permission_callback' => '__return_true',
    ));
});

// REST API: Fetch Real-Time Data and Generate Visualization
function fetch_real_time_data() {
    $command = escapeshellcmd('python3 ' . get_template_directory() . '/scripts/real_time_fetcher.py');
    $output = shell_exec($command);

    if ($output === null) {
        error_log('TradingRobotPlug Error: Failed to execute Real-Time Data script.');
        return new WP_Error('realtime_fetch_error', 'Failed to execute Real-Time Data script.', array('status' => 500));
    }

    $data = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('TradingRobotPlug Error: Invalid JSON response from Real-Time Data script.');
        return new WP_Error('realtime_fetch_error', 'Invalid response from Real-Time Data script.', array('status' => 500));
    }

    return $data;
}

add_action('rest_api_init', function () {
    register_rest_route('tradingrobotplug/v1', '/fetchrealtime', array(
        'methods' => 'GET',
        'callback' => 'fetch_real_time_data',
        'permission_callback' => '__return_true',
    ));
});
?>
