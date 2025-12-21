<?php
/**
 * Test Page Template - Use this to quickly test the plugin
 * Create a page and add this content to see everything working
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- TEST PAGE FOR AUTOMATED TRADING PLAN PLUGIN -->
<!-- Copy this content into a WordPress page to test -->

<h1>FreeRide Automated Trading Plan - Test Page</h1>

<h2>1. Membership Status</h2>
<?php echo do_shortcode('[fratp_membership_status]'); ?>

<hr>

<h2>2. Premium Signup (Sales Funnel)</h2>
<?php echo do_shortcode('[fratp_premium_signup]'); ?>

<hr>

<h2>3. Trading Plans List</h2>
<?php echo do_shortcode('[fratp_plans_list]'); ?>

<hr>

<h2>4. Individual Daily Plan (TSLA)</h2>
<?php echo do_shortcode('[fratp_daily_plan symbol="TSLA"]'); ?>

<hr>

<h2>5. Strategy Status (TSLA)</h2>
<?php echo do_shortcode('[fratp_strategy_status symbol="TSLA"]'); ?>

<hr>

<p><strong>Note:</strong> If you see "Access Denied" messages, that's expected! The access control is working. You need to be logged in as a premium member to see full plans.</p>

<p><strong>To test as admin:</strong> Admins automatically have access to all plans.</p>

<p><strong>To test premium access:</strong> 
1. Create a test user
2. Go to Users → Edit User
3. Change role to "Premium Member"
4. Log in as that user
5. You should now see full plans
</p>



