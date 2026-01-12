<?php
namespace TradingRobotPlug;

class Admin {
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'Trading Robot Plug', 
            'Trading Robot', 
            'manage_options', 
            $this->plugin_name, 
            [$this, 'display_plugin_setup_page'], 
            'dashicons-chart-line', 
            6
        );

        add_submenu_page(
            $this->plugin_name,
            'Settings',
            'Settings',
            'manage_options',
            $this->plugin_name . '-settings',
            [$this, 'display_settings_page']
        );
    }

    public function display_plugin_setup_page() {
        require_once TRADINGROBOTPLUG_PLUGIN_DIR . 'admin/partials/admin-display.php';
    }

    public function display_settings_page() {
        // Simple settings form
        if (isset($_POST['tradingrobotplug_api_key'])) {
            update_option('tradingrobotplug_api_key', sanitize_text_field($_POST['tradingrobotplug_api_key']));
            echo '<div class="updated"><p>Settings saved</p></div>';
        }

        $api_key = get_option('tradingrobotplug_api_key', '');
        
        echo '<div class="wrap">';
        echo '<h1>Trading Robot Plug Settings</h1>';
        echo '<form method="post" action="">';
        echo '<table class="form-table">';
        echo '<tr valign="top">';
        echo '<th scope="row">API Key</th>';
        echo '<td><input type="text" name="tradingrobotplug_api_key" value="' . esc_attr($api_key) . '" class="regular-text" /></td>';
        echo '</tr>';
        echo '</table>';
        echo submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, TRADINGROBOTPLUG_PLUGIN_URL . 'admin/css/admin.css', [], $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, TRADINGROBOTPLUG_PLUGIN_URL . 'admin/js/admin.js', ['jquery'], $this->version, false);
    }
}
