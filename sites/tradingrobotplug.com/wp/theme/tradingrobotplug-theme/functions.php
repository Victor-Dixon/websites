<?php
/**
 * Theme functions and definitions
 */

if ( ! defined( 'TRP_THEME_VERSION' ) ) {
	define( 'TRP_THEME_VERSION', '1.0.0' );
}

function trp_enqueue_scripts() {
	wp_enqueue_style( 'trp-style', get_stylesheet_uri(), array(), TRP_THEME_VERSION );
    
    // Add Google Fonts
    wp_enqueue_style( 'trp-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'trp_enqueue_scripts' );

function trp_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	
    register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'tradingrobotplug' ),
        'footer'  => __( 'Footer Menu', 'tradingrobotplug' ),
	) );
}
add_action( 'after_setup_theme', 'trp_setup' );
