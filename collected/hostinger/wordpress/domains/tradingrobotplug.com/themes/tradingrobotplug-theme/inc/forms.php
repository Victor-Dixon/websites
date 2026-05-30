<?php
/**
 * Forms Module
 * Waitlist and contact form handlers
 * 
 * @package TradingRobotPlug
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle waitlist form submission
 */
function trp_handle_waitlist_signup()
{
    // Verify nonce
    if (!isset($_POST['waitlist_nonce']) || !wp_verify_nonce($_POST['waitlist_nonce'], 'waitlist_form')) {
        wp_die('Security check failed');
    }
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_die('Invalid email address');
    }
    
    // Process email (add to mailing list, send notification, etc.)
    // TODO: Integrate with email marketing platform (MailChimp, ConvertKit, etc.)
    // Example: wp_mail($admin_email, 'New Waitlist Signup', 'Email: ' . $email);
    
    // Redirect to thank you page
    wp_redirect(home_url('/thank-you?source=waitlist'));
    exit;
}

add_action('admin_post_handle_waitlist_signup', 'trp_handle_waitlist_signup');
add_action('admin_post_nopriv_handle_waitlist_signup', 'trp_handle_waitlist_signup');

/**
 * Handle contact form submission (Tier 1 Quick Win WEB-04)
 */
function trp_handle_contact_form()
{
    // Verify nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'contact_form')) {
        wp_die('Security check failed');
    }
    
    $email = sanitize_email($_POST['email']);
    
    if (!is_email($email)) {
        wp_die('Invalid email address');
    }
    
    // Process email (add to mailing list, send notification, etc.)
    // TODO: Integrate with email marketing platform
    
    // Redirect to thank you page
    wp_redirect(home_url('/thank-you?source=contact'));
    exit;
}

add_action('admin_post_handle_contact_form', 'trp_handle_contact_form');
add_action('admin_post_nopriv_handle_contact_form', 'trp_handle_contact_form');

