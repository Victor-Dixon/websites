<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * CSS/JS Enqueue
 * - Keep style.css for WP theme header.
 * - Enqueue real CSS from /css to stay modular.
 */
add_action( 'wp_enqueue_scripts', function () {

	// Theme header stylesheet (optional: keep empty except header comment)
	$style_path = FRI_THEME_DIR . '/style.css';
	$style_ver  = file_exists( $style_path ) ? (string) filemtime( $style_path ) : FRI_THEME_VER;
	wp_enqueue_style( 'fri-style', get_stylesheet_uri(), array(), $style_ver );

	// Modular CSS
	$css_files = array(
		'00-reset.css',
		'10-tokens.css',
		'20-base.css',
		'30-layout.css',
		'40-components.css',
		'50-wp-blocks.css',
		'90-utilities.css',
		'99-overrides.css',
	);

	$deps = array( 'fri-style' );
	foreach ( $css_files as $css ) {
		$path = FRI_THEME_DIR . '/css/' . $css;
		if ( ! file_exists( $path ) ) { continue; }

		$handle = 'fri-' . sanitize_title( $css );
		$ver    = (string) filemtime( $path );
		wp_enqueue_style( $handle, FRI_THEME_URI . '/css/' . $css, $deps, $ver );
		$deps = array( $handle ); // preserve order
	}

	// JS (create /js dir)
	$js_files = array( 'nav.js', 'scroll.js', 'main.js' );
	foreach ( $js_files as $js ) {
		$path = FRI_THEME_DIR . '/js/' . $js;
		if ( ! file_exists( $path ) ) { continue; }

		wp_enqueue_script(
			'fri-' . sanitize_title( $js ),
			FRI_THEME_URI . '/js/' . $js,
			array(),
			(string) filemtime( $path ),
			true
		);
	}

} );
