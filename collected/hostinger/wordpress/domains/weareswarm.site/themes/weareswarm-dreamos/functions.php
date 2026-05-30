<?php
/**
 * WeAreSwarm DreamOS theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

function weareswarm_dreamos_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'weareswarm_dreamos_setup');
