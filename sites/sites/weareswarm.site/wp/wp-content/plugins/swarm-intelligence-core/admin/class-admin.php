<?php
class Swarm_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    public function add_admin_menu() {
        add_menu_page('Swarm Intelligence', 'Swarm Intelligence', 'manage_options', 'swarm-intelligence', array($this, 'admin_page'), 'dashicons-groups');
    }
    public function admin_page() {
        echo '<div class="wrap"><h1>Swarm Intelligence Core</h1><p>Manage swarm agents and coordination.</p></div>';
    }
}