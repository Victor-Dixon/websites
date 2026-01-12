<?php
class Event_Service_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    public function add_admin_menu() {
        add_menu_page('Event Planning', 'Event Planning', 'manage_options', 'event-planning', array($this, 'admin_page'), 'dashicons-calendar');
    }
    public function admin_page() {
        echo '<div class="wrap"><h1>Event Planning Manager</h1><p>Manage event services and planning.</p></div>';
    }
}