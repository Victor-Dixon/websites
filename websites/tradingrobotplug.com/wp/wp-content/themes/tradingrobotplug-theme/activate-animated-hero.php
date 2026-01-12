<?php
/**
 * Activate Animated Hero Homepage
 *
 * This script switches the homepage template to use the animated hero version.
 * Run this once to activate the WOW effect homepage.
 *
 * Usage: Visit this file in your browser, then delete it for security.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    die('Direct access not allowed');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You do not have sufficient permissions to access this page.');
}

// Get current page template
$page_id = get_option('page_on_front');
if (!$page_id) {
    $page_id = get_option('page_for_posts');
}

$current_template = get_post_meta($page_id, '_wp_page_template', true);

$message = '';
$status = '';

// If this is a POST request, update the template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activate_animated'])) {
    // Update the homepage template to use our animated version
    update_post_meta($page_id, '_wp_page_template', 'front-page-animated.php');

    $message = '✅ Animated hero homepage activated successfully!';
    $status = 'success';

    // Clear any cached data
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Animated Hero - TradingRobotPlug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px; }
        .warning { color: #856404; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; margin-bottom: 20px; }
        .btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
        .preview { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 5px; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
        .feature { padding: 20px; background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%); border-radius: 8px; border: 1px solid #e9ecef; }
        .feature h3 { margin-top: 0; color: #495057; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Activate Animated Hero Homepage</h1>

        <?php if ($message): ?>
            <div class="success">
                <?php echo esc_html($message); ?>
            </div>
        <?php endif; ?>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong> Delete this file after activation for security reasons.
        </div>

        <div class="preview">
            <h2>✨ What's the Animated Hero?</h2>
            <p>Your new homepage features:</p>
            <div class="features">
                <div class="feature">
                    <h3>🎨 Gradient Animations</h3>
                    <p>Beautiful animated gradients that shift and move</p>
                </div>
                <div class="feature">
                    <h3>⚡ Floating Particles</h3>
                    <p>Subtle floating elements that create depth</p>
                </div>
                <div class="feature">
                    <h3>⌨️ Typewriter Effect</h3>
                    <p>Dynamic text that types itself on load</p>
                </div>
                <div class="feature">
                    <h3>🎯 Enhanced CTAs</h3>
                    <p>Pulsing call-to-action buttons with hover effects</p>
                </div>
                <div class="feature">
                    <h3>📊 Live Market Data</h3>
                    <p>Enhanced market preview with smooth animations</p>
                </div>
                <div class="feature">
                    <h3>📱 Mobile Optimized</h3>
                    <p>Responsive design that works on all devices</p>
                </div>
            </div>
        </div>

        <div class="preview">
            <h2>📋 Current Status</h2>
            <p><strong>Homepage ID:</strong> <?php echo esc_html($page_id); ?></p>
            <p><strong>Current Template:</strong> <?php echo esc_html($current_template ?: 'default'); ?></p>
            <p><strong>Target Template:</strong> front-page-animated.php</p>
        </div>

        <?php if ($status !== 'success'): ?>
            <form method="post">
                <button type="submit" name="activate_animated" class="btn">
                    🚀 Activate Animated Hero Homepage
                </button>
            </form>
        <?php else: ?>
            <div style="text-align: center; margin: 40px 0;">
                <h3>🎉 Activation Complete!</h3>
                <p>Your homepage now features the animated hero section.</p>
                <a href="<?php echo esc_url(home_url()); ?>" class="btn" target="_blank">
                    👁️ View Homepage
                </a>
            </div>
        <?php endif; ?>

        <div class="preview" style="margin-top: 40px; background: #fff3cd; border: 1px solid #ffeaa7;">
            <h3>🛡️ Security Reminder</h3>
            <p><strong>Important:</strong> After activation, please delete this file (<code>activate-animated-hero.php</code>) from your server for security reasons.</p>
        </div>
    </div>
</body>
</html>