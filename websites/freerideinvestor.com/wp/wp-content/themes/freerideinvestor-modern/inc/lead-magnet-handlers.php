<?php
/**
 * Lead Magnet Form Handlers
 * Phase 1 P0 Fix - FUN-01
 * Handles roadmap and mindset journal downloads
 *
 * @package SimplifiedTradingTheme
 */

/**
 * Handle Roadmap Download Form Submission
 */
function handle_roadmap_download() {
    // Verify nonce
    if (!isset($_POST['roadmap_nonce']) || !wp_verify_nonce($_POST['roadmap_nonce'], 'roadmap_download_form')) {
        wp_die('Security check failed');
    }

    // Get email
    $email = sanitize_email($_POST['email'] ?? '');
    if (empty($email) || !is_email($email)) {
        wp_redirect(home_url('/roadmap?error=invalid_email'));
        exit;
    }

    // Check agreement
    if (!isset($_POST['agree_to_policy'])) {
        wp_redirect(home_url('/roadmap?error=policy_required'));
        exit;
    }

    // TODO: Add email to mailing list (Mailchimp/ConvertKit integration)
    // TODO: Send welcome email with download link
    
    // Redirect to thank you page
    $redirect = esc_url_raw($_POST['redirect_to'] ?? home_url('/thank-you-roadmap'));
    wp_redirect($redirect);
    exit;
}
add_action('admin_post_roadmap_download', 'handle_roadmap_download');
add_action('admin_post_nopriv_roadmap_download', 'handle_roadmap_download');

/**
 * Handle Mindset Journal Download Form Submission
 */
function handle_mindset_journal_download() {
    // Verify nonce
    if (!isset($_POST['mindset_journal_nonce']) || !wp_verify_nonce($_POST['mindset_journal_nonce'], 'mindset_journal_download_form')) {
        wp_die('Security check failed');
    }

    // Get email
    $email = sanitize_email($_POST['email'] ?? '');
    if (empty($email) || !is_email($email)) {
        wp_redirect(home_url('/mindset-journal?error=invalid_email'));
        exit;
    }

    // Check agreement
    if (!isset($_POST['agree_to_policy'])) {
        wp_redirect(home_url('/mindset-journal?error=policy_required'));
        exit;
    }

    // TODO: Add email to mailing list (Mailchimp/ConvertKit integration)
    // TODO: Send welcome email with download link
    
    // Redirect to thank you page
    $redirect = esc_url_raw($_POST['redirect_to'] ?? home_url('/thank-you-mindset-journal'));
    wp_redirect($redirect);
    exit;
}
add_action('admin_post_mindset_journal_download', 'handle_mindset_journal_download');
add_action('admin_post_nopriv_mindset_journal_download', 'handle_mindset_journal_download');

