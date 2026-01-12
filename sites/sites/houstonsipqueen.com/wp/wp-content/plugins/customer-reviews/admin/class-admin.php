<?php
class Review_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    public function add_admin_menu() {
        add_submenu_page('edit.php?post_type=review', 'Review Settings', 'Settings', 'manage_options', 'review-settings', array($this, 'settings_page'));
    }
    public function settings_page() {
        echo '<div class="wrap"><h1>Customer Reviews Settings</h1><p>Review management configuration.</p></div>';
    }
}