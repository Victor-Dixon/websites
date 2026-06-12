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

/**
 * Prevent www <-> apex canonical redirect loops on static Dream.OS routes.
 */
function weareswarm_dreamos_redirect_canonical($redirect_url, $requested_url) {
    if (!$redirect_url) {
        return $redirect_url;
    }

    $host = wp_parse_url($requested_url, PHP_URL_HOST);
    $path = wp_parse_url($requested_url, PHP_URL_PATH);
    if (!$host || !$path) {
        return $redirect_url;
    }

    $static = array('/projects', '/profile', '/live-ops', '/feed', '/tasks', '/skill-tree');
    $normalized = rtrim($path, '/') ?: '/';
    if (!in_array($normalized, $static, true)) {
        return $redirect_url;
    }

    $from_www = ($host === 'www.weareswarm.site');
    $to_apex = (strpos($redirect_url, 'https://weareswarm.site') === 0);
    $from_apex = ($host === 'weareswarm.site');
    $to_www = (strpos($redirect_url, 'https://www.weareswarm.site') === 0);

    if (($from_www && $to_apex) || ($from_apex && $to_www)) {
        return false;
    }

    return $redirect_url;
}
add_filter('redirect_canonical', 'weareswarm_dreamos_redirect_canonical', 10, 2);
