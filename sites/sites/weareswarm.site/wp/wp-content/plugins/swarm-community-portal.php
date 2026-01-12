<?php
/**
 * Plugin Name: Swarm Community Portal
 * Description: Community features for swarm intelligence platform
 * Version: 1.0.0
 * Author: Swarm Intelligence Team
 */
if (!defined('ABSPATH')) exit;
function swarm_community_portal_init() {
    add_shortcode('swarm_community', 'swarm_community_shortcode');
}
function swarm_community_shortcode() {
    return '<div class="swarm-community"><h3>Swarm Community</h3><p>Connect with fellow swarm intelligence enthusiasts.</p></div>';
}
add_action('plugins_loaded', 'swarm_community_portal_init');