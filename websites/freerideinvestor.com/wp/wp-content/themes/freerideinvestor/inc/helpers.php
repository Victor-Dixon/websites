<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Safe avatar helper (prevents fatals when templates call it)
 * Usage: echo dd_safe_avatar( get_the_author_meta('ID'), 56 );
 */
if ( ! function_exists( 'dd_safe_avatar' ) ) {
	function dd_safe_avatar( $user_id = 0, int $size = 56, array $args = array() ): string {
		$user_id = (int) $user_id;
		$defaults = array(
			'class' => 'avatar avatar-' . $size,
			'force_default' => false,
		);
		$args = wp_parse_args( $args, $defaults );

		// If WP can render it, use it.
		if ( $user_id > 0 ) {
			$html = get_avatar( $user_id, $size, '', '', $args );
			if ( is_string( $html ) && trim( $html ) !== '' ) {
				return $html;
			}
		}

		// Hard fallback: inline “initial” badge
		$label = __( 'U', 'freerideinvestor' );
		if ( $user_id > 0 ) {
			$u = get_userdata( $user_id );
			if ( $u && ! empty( $u->display_name ) ) {
				$label = strtoupper( mb_substr( (string) $u->display_name, 0, 1 ) );
			}
		}

		return '<span class="dd-avatar-fallback" aria-hidden="true">' . esc_html( $label ) . '</span>';
	}
}
