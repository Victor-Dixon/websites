<?php
class Portfolio_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    public function add_admin_menu() {
        add_menu_page('Portfolio Showcase', 'Portfolio Showcase', 'manage_options', 'portfolio-showcase', array($this, 'admin_page'), 'dashicons-portfolio');
    }
    public function admin_page() {
        echo '<div class="wrap"><h1>Portfolio Showcase</h1><p>Manage portfolio projects and showcase.</p></div>';
    }
}