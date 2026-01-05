<?php
/**
 * Membership and Access Control Class
 * Handles premium membership, access control, and sales funnel
 */

if (!defined('ABSPATH')) {
    exit;
}

class FRATP_Membership {
    
    /**
     * User roles
     */
    const ROLE_FREE = 'fratp_free';
    const ROLE_PREMIUM = 'fratp_premium';
    
    /**
     * Initialize membership system
     */
    public static function init() {
        // Create custom user roles on activation
        add_action('init', array(__CLASS__, 'create_user_roles'));
        
        // Check access before displaying plans
        add_filter('fratp_can_view_plan', array(__CLASS__, 'check_plan_access'), 10, 2);
        
        // Add membership status to user meta
        add_action('user_register', array(__CLASS__, 'set_default_membership'));
    }
    
    /**
     * Create custom user roles
     */
    public static function create_user_roles() {
        // Free member role
        add_role(
            self::ROLE_FREE,
            __('Free Member', 'freeride-automated-trading-plan'),
            array(
                'read' => true,
                'fratp_view_free_plans' => true,
            )
        );
        
        // Premium member role
        add_role(
            self::ROLE_PREMIUM,
            __('Premium Member', 'freeride-automated-trading-plan'),
            array(
                'read' => true,
                'fratp_view_free_plans' => true,
                'fratp_view_premium_plans' => true,
                'fratp_view_all_plans' => true,
            )
        );
    }
    
    /**
     * Set default membership for new users
     */
    public static function set_default_membership($user_id) {
        $user = new WP_User($user_id);
        if (!in_array(self::ROLE_PREMIUM, $user->roles)) {
            $user->set_role(self::ROLE_FREE);
            update_user_meta($user_id, 'fratp_membership_type', 'free');
            update_user_meta($user_id, 'fratp_membership_start_date', current_time('mysql'));
        }
    }
    
    /**
     * Upgrade user to premium
     */
    public static function upgrade_to_premium($user_id) {
        $user = new WP_User($user_id);
        $user->set_role(self::ROLE_PREMIUM);
        update_user_meta($user_id, 'fratp_membership_type', 'premium');
        update_user_meta($user_id, 'fratp_membership_start_date', current_time('mysql'));
        update_user_meta($user_id, 'fratp_premium_start_date', current_time('mysql'));
        
        // Log upgrade
        do_action('fratp_user_upgraded', $user_id);
    }
    
    /**
     * Check if user has premium access
     */
    public static function is_premium($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!$user_id) {
            return false;
        }
        
        $user = new WP_User($user_id);
        return in_array(self::ROLE_PREMIUM, $user->roles) || 
               user_can($user_id, 'fratp_view_premium_plans');
    }
    
    /**
     * Check if user is logged in
     */
    public static function is_logged_in() {
        return is_user_logged_in();
    }
    
    /**
     * Check plan access based on membership
     */
    public static function check_plan_access($can_view, $plan) {
        // If no plan, deny access
        if (!$plan) {
            return false;
        }
        
        // Admin can always view
        if (current_user_can('manage_options')) {
            return true;
        }
        
        // Check if plan is premium (you can add logic here to mark plans as premium)
        $is_premium_plan = self::is_premium_plan($plan);
        
        if ($is_premium_plan) {
            // Only premium members can view premium plans
            return self::is_premium();
        } else {
            // Free plans - logged in users can view
            return self::is_logged_in();
        }
    }
    
    /**
     * Check if plan is marked as premium
     */
    private static function is_premium_plan($plan) {
        // You can add logic here - for now, all plans are premium
        // Or check plan meta, date, symbol, etc.
        return true; // All plans require premium for now
    }
    
    /**
     * Get membership status for current user
     */
    public static function get_membership_status() {
        if (!is_user_logged_in()) {
            return array(
                'type' => 'none',
                'status' => 'not_logged_in',
                'message' => __('Please log in to view trading plans', 'freeride-automated-trading-plan'),
            );
        }
        
        $user_id = get_current_user_id();
        $is_premium = self::is_premium($user_id);
        
        if ($is_premium) {
            return array(
                'type' => 'premium',
                'status' => 'active',
                'message' => __('You have premium access to all trading plans', 'freeride-automated-trading-plan'),
            );
        } else {
            return array(
                'type' => 'free',
                'status' => 'limited',
                'message' => __('Upgrade to premium for full access to daily trading plans', 'freeride-automated-trading-plan'),
            );
        }
    }
    
    /**
     * Get premium signup URL
     */
    public static function get_premium_signup_url() {
        $page = get_option('fratp_premium_signup_page');
        if ($page) {
            return get_permalink($page);
        }
        return home_url('/premium-signup');
    }
    
    /**
     * Get login URL
     */
    public static function get_login_url() {
        $page = get_option('fratp_login_page');
        if ($page) {
            return get_permalink($page);
        }
        return wp_login_url();
    }
}

