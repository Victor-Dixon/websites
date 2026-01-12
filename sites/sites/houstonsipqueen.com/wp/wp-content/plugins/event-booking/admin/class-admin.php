<?php
class Event_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    public function add_admin_menu() {
        add_submenu_page('edit.php?post_type=event', 'Event Settings', 'Settings', 'manage_options', 'event-settings', array($this, 'settings_page'));
    }
    public function settings_page() {
        echo '<div class="wrap"><h1>Event Booking Settings</h1><p>Event management configuration.</p></div>';
    }
}