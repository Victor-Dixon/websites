<?php
/**
 * Template Tags and Helper Functions
 * 
 * Custom template functions used in template files
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Unified Subheader Strip
 * Consistent tagline + context indicator across all pages
 */
function digitaldreamscape_unified_subheader()
{
    // Determine context based on page type
    $context = '';
    $context_badge = '';

    if (is_front_page() || is_home()) {
        $context = 'Command Hub';
        $context_badge = '[COMMAND HUB]';
    } elseif (is_single()) {
        $context = 'Episode View';
        $context_badge = '[EPISODE VIEW]';
    } elseif (is_archive() || is_category() || is_tag()) {
        $context = 'Episode Archive';
        $context_badge = '[EPISODE ARCHIVE]';
    } else {
        $context = 'Command Hub';
        $context_badge = '[COMMAND HUB]';
    }

?>
    <div class="unified-subheader">
        <div class="subheader-container">
            <div class="subheader-content">
                <div class="subheader-tagline">
                    <span class="tagline-text">Build in Public. Stream & Create.</span>
                </div>
                <div class="subheader-context">
                    <span class="context-badge"><?php echo esc_html($context_badge); ?></span>
                    <span class="context-label"><?php echo esc_html($context); ?></span>
                </div>
            </div>
        </div>
    </div>
<?php
}

// Hook into wp_footer with early priority, but we'll manually call it after header
// This ensures it appears right after the header
add_action('wp_footer', function () {
    // Only output if not already displayed
    if (!did_action('digitaldreamscape_subheader_displayed')) {
        digitaldreamscape_unified_subheader();
        do_action('digitaldreamscape_subheader_displayed');
    }
}, 1);

/**
 * Default menu fallback if no menu is set
 */
function digitaldreamscape_default_menu()
{
?>
    <ul id="primary-menu" class="menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
        <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
        <li><a href="<?php echo esc_url(home_url('/streaming')); ?>">Streaming</a></li>
        <li><a href="<?php echo esc_url(home_url('/community')); ?>">Community</a></li>
        <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
    </ul>
<?php
}

