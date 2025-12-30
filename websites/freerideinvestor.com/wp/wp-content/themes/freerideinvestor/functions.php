<?php
/**
 * Theme Functions
 *
 * @package FreeRideInvestor
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'FRI_THEME_VER', '1.0.0' );
define( 'FRI_THEME_DIR', get_stylesheet_directory() );
define( 'FRI_THEME_URI', get_stylesheet_directory_uri() );

$fri_includes = array(
	'/inc/setup.php',
	'/inc/template-tags.php',
	'/inc/helpers.php',
	'/inc/assets.php',
	'/inc/filters/alt-text.php',
);

foreach ( $fri_includes as $file ) {
	$path = FRI_THEME_DIR . $file;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
