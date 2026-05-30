<?php
namespace TradingRobotPlug;

class User_Manager {
    private $api;

    public function __construct() {
        $this->api = new API_Client();
        
        // Hook into WordPress user registration/login if needed
        add_action('wp_login', [$this, 'sync_user_login'], 10, 2);
        add_action('user_register', [$this, 'sync_user_registration'], 10, 1);
    }

    /**
     * Syncs WordPress login with external platform session
     */
    public function sync_user_login($user_login, $user) {
        // Implementation: Call API to generate a session token for the platform
        // and store it in a cookie or user meta
        $response = $this->api->post('/auth/sync', [
            'email' => $user->user_email,
            'wp_id' => $user->ID
        ]);

        if ($response['success']) {
            update_user_meta($user->ID, 'tradingrobotplug_token', $response['data']['token']);
        }
    }

    /**
     * Creates a user on the external platform when they register on WP
     */
    public function sync_user_registration($user_id) {
        $user = get_userdata($user_id);
        
        $this->api->post('/users/create', [
            'email' => $user->user_email,
            'username' => $user->user_login,
            'wp_id' => $user_id
        ]);
    }

    /**
     * Get the current user's profile from the platform
     */
    public function get_user_profile($user_id) {
        $token = get_user_meta($user_id, 'tradingrobotplug_token', true);
        if (!$token) return false;

        return $this->api->get('/users/me');
    }
}
