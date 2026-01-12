<?php
/**
 * Public Class for Beverage Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class Beverage_Public {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
        add_shortcode('beverage_menu', array($this, 'beverage_menu_shortcode'));
    }

    public function enqueue_public_scripts() {
        wp_enqueue_script('beverage-public-js', BEVERAGE_MENU_PLUGIN_URL . 'public/js/public.js', array('jquery'), BEVERAGE_MENU_VERSION, true);
        wp_enqueue_style('beverage-public-css', BEVERAGE_MENU_PLUGIN_URL . 'public/css/public.css', array(), BEVERAGE_MENU_VERSION);
    }

    public function beverage_menu_shortcode($atts) {
        ob_start();
        ?>
        <div class="beverage-menu-container">
            <h2><?php _e('Beverage Menu', 'beverage-menu'); ?></h2>
            <div class="beverage-grid">
                <?php
                $beverages = get_posts(array(
                    'post_type' => 'beverage',
                    'posts_per_page' => -1
                ));
                
                if ($beverages) {
                    foreach ($beverages as $beverage) {
                        echo '<div class="beverage-item">';
                        echo '<h3>' . get_the_title($beverage) . '</h3>';
                        echo '<div class="beverage-content">' . apply_filters('the_content', $beverage->post_content) . '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>' . __('No beverages found.', 'beverage-menu') . '</p>';
                }
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}