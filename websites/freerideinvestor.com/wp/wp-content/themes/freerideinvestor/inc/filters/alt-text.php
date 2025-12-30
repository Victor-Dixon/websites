<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

function fri_get_fallback_alt_text( int $post_id ): string {
	$title = get_the_title( $post_id );
	$title = is_string( $title ) ? trim( $title ) : '';
	return $title !== '' ? $title : __( 'Image', 'freerideinvestor' );
}

function fri_add_missing_alt_text_to_content( string $content ): string {
	if ( trim( $content ) === '' ) { return $content; }
	$post_id = get_the_ID();
	if ( ! $post_id ) { return $content; }

	$alt = fri_get_fallback_alt_text( (int) $post_id );

	return (string) preg_replace_callback(
		'/<img\b(?![^>]*\balt=)([^>]*?)>/i',
		function ( $m ) use ( $alt ) {
			$attrs = $m[1] ?? '';
			return '<img alt="' . esc_attr( $alt ) . '"' . $attrs . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'fri_add_missing_alt_text_to_content', 20 );

function fri_add_missing_alt_text_to_thumbnails( string $html, int $post_id, int $thumb_id, $size, $attr ): string {
	if ( $html === '' ) { return $html; }
	if ( preg_match( '/\balt\s*=\s*["\'].*?["\']/i', $html ) ) { return $html; }

	$alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
	$alt = is_string( $alt ) ? trim( $alt ) : '';
	if ( $alt === '' ) { $alt = fri_get_fallback_alt_text( $post_id ); }

	$html = preg_replace(
		'/<img\b(?![^>]*\balt=)([^>]*?)>/i',
		'<img alt="' . esc_attr( $alt ) . '"$1>',
		$html,
		1
	);

	return (string) $html;
}
add_filter( 'post_thumbnail_html', 'fri_add_missing_alt_text_to_thumbnails', 10, 5 );

add_filter( 'the_generator', '__return_empty_string' );
