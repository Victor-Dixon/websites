<?php
/**
 * Plugin Name: Swarm Heartbeat
 * Description: Displays a live proof card for multi-agent system heartbeat updates.
 * Version: 1.0.0
 * Author: Swarm
 * License: GPLv2 or later
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

const SWARM_HEARTBEAT_OPTION = 'swarm_heartbeat_payload';
const SWARM_HEARTBEAT_SETTINGS = 'swarm_heartbeat_settings';
const SWARM_HEARTBEAT_LOGS = 'swarm_heartbeat_logs';
const SWARM_HEARTBEAT_MAX_LOGS = 20;
const SWARM_HEARTBEAT_VERSION = '1.0.0';

function swarm_heartbeat_default_settings() {
	return array(
		'secret_token'            => wp_generate_password( 32, false, false ),
		'stale_after_minutes'     => 15,
		'degraded_after_minutes'  => 60,
		'show_banner'             => false,
		'show_copy_button'        => true,
		'show_badge'              => true,
	);
}

function swarm_heartbeat_get_settings() {
	$defaults = swarm_heartbeat_default_settings();
	$settings = get_option( SWARM_HEARTBEAT_SETTINGS, array() );
	if ( empty( $settings ) ) {
		return $defaults;
	}
	return wp_parse_args( $settings, $defaults );
}

function swarm_heartbeat_activate() {
	$settings = get_option( SWARM_HEARTBEAT_SETTINGS );
	if ( empty( $settings ) ) {
		add_option( SWARM_HEARTBEAT_SETTINGS, swarm_heartbeat_default_settings() );
	}
}
register_activation_hook( __FILE__, 'swarm_heartbeat_activate' );

function swarm_heartbeat_enqueue_assets() {
	$css_url = plugins_url( 'swarm-heartbeat.css', __FILE__ );
	wp_enqueue_style( 'swarm-heartbeat', $css_url, array(), SWARM_HEARTBEAT_VERSION );

	$settings = swarm_heartbeat_get_settings();
	if ( ! empty( $settings['show_copy_button'] ) ) {
		$js_url = plugins_url( 'swarm-heartbeat.js', __FILE__ );
		wp_enqueue_script( 'swarm-heartbeat', $js_url, array(), SWARM_HEARTBEAT_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'swarm_heartbeat_enqueue_assets' );

function swarm_heartbeat_enqueue_admin_assets( $hook ) {
	if ( 'settings_page_swarm-heartbeat' !== $hook ) {
		return;
	}
	$css_url = plugins_url( 'swarm-heartbeat.css', __FILE__ );
	wp_enqueue_style( 'swarm-heartbeat', $css_url, array(), SWARM_HEARTBEAT_VERSION );
	$js_url = plugins_url( 'swarm-heartbeat.js', __FILE__ );
	wp_enqueue_script( 'swarm-heartbeat', $js_url, array(), SWARM_HEARTBEAT_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'swarm_heartbeat_enqueue_admin_assets' );

function swarm_heartbeat_register_shortcode() {
	add_shortcode( 'swarm_heartbeat', 'swarm_heartbeat_shortcode' );
}
add_action( 'init', 'swarm_heartbeat_register_shortcode' );

function swarm_heartbeat_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'show_banner' => 'false',
		),
		$atts,
		'swarm_heartbeat'
	);
	return swarm_heartbeat_render_card( array( 'force_banner' => filter_var( $atts['show_banner'], FILTER_VALIDATE_BOOLEAN ) ) );
}

function swarm_heartbeat_register_block() {
	wp_register_script(
		'swarm-heartbeat-block',
		plugins_url( 'swarm-heartbeat-block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-editor' ),
		SWARM_HEARTBEAT_VERSION,
		true
	);

	register_block_type(
		'swarm-heartbeat/block',
		array(
			'editor_script'   => 'swarm-heartbeat-block',
			'render_callback' => 'swarm_heartbeat_render_card',
			'attributes'      => array(
				'showBanner' => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
			'title'           => __( 'Swarm Heartbeat', 'swarm-heartbeat' ),
			'category'        => 'widgets',
			'icon'            => 'heart',
		)
	);
}
add_action( 'init', 'swarm_heartbeat_register_block' );

function swarm_heartbeat_get_payload() {
	$payload = get_option( SWARM_HEARTBEAT_OPTION, array() );
	return is_array( $payload ) ? $payload : array();
}

function swarm_heartbeat_format_time( $iso_time ) {
	if ( empty( $iso_time ) ) {
		return array( 'label' => 'Not yet', 'timestamp' => null );
	}
	$timestamp = strtotime( $iso_time );
	if ( ! $timestamp ) {
		return array( 'label' => 'Unknown', 'timestamp' => null );
	}
	return array(
		'label'     => sprintf( '%s ago', human_time_diff( $timestamp, time() ) ),
		'timestamp' => $timestamp,
	);
}

function swarm_heartbeat_status( $payload, $settings ) {
	if ( empty( $payload['last_ping_iso'] ) ) {
		return array( 'status' => 'DEGRADED', 'is_critical' => true );
	}
	$timestamp = strtotime( $payload['last_ping_iso'] );
	if ( ! $timestamp ) {
		return array( 'status' => 'DEGRADED', 'is_critical' => true );
	}
	$minutes = ( time() - $timestamp ) / MINUTE_IN_SECONDS;
	$is_stale = $minutes > (int) $settings['stale_after_minutes'];
	$is_critical = $minutes > (int) $settings['degraded_after_minutes'];
	return array(
		'status'      => $is_stale ? 'DEGRADED' : 'OK',
		'is_critical' => $is_critical,
	);
}

function swarm_heartbeat_render_card( $attributes = array() ) {
	$settings = swarm_heartbeat_get_settings();
	$payload = swarm_heartbeat_get_payload();

	$display_payload = wp_parse_args(
		$payload,
		array(
			'last_ping_iso' => '',
			'agents_active' => 0,
			'last_mission'  => array(
				'title'  => 'Awaiting first mission update',
				'url'    => '',
				'status' => 'pending',
			),
			'last_event'    => array(
				'label' => 'Awaiting first activity event',
				'url'   => '',
			),
		)
	);

	$time_data = swarm_heartbeat_format_time( $display_payload['last_ping_iso'] );
	$status = swarm_heartbeat_status( $display_payload, $settings );

	$force_banner = ! empty( $attributes['showBanner'] ) || ! empty( $attributes['force_banner'] );
	$wrapper_classes = array( 'swarm-heartbeat-card' );
	if ( $force_banner ) {
		$wrapper_classes[] = 'swarm-heartbeat-banner';
	}
	if ( $status['is_critical'] ) {
		$wrapper_classes[] = 'swarm-heartbeat-critical';
	}

	$last_mission_title = isset( $display_payload['last_mission']['title'] ) ? $display_payload['last_mission']['title'] : '';
	$last_mission_url = isset( $display_payload['last_mission']['url'] ) ? $display_payload['last_mission']['url'] : '';
	$last_event_label = isset( $display_payload['last_event']['label'] ) ? $display_payload['last_event']['label'] : '';
	$last_event_url = isset( $display_payload['last_event']['url'] ) ? $display_payload['last_event']['url'] : '';

	$snapshot = sprintf(
		'Swarm Heartbeat: %s | Agents active: %d | Last ping: %s | Last mission: %s | Last event: %s',
		$status['status'],
		(int) $display_payload['agents_active'],
		$display_payload['last_ping_iso'] ? $display_payload['last_ping_iso'] : 'Not yet',
		$last_mission_title ? $last_mission_title : 'Awaiting first mission update',
		$last_event_label ? $last_event_label : 'Awaiting first activity event'
	);

	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-swarm-snapshot="<?php echo esc_attr( $snapshot ); ?>">
		<div class="swarm-heartbeat-header">
			<div>
				<span class="swarm-heartbeat-title">Swarm Heartbeat</span>
				<div class="swarm-heartbeat-subtitle">Live proof of multi-agent activity</div>
			</div>
			<?php if ( ! empty( $settings['show_badge'] ) ) : ?>
				<span class="swarm-heartbeat-badge swarm-heartbeat-badge-<?php echo esc_attr( strtolower( $status['status'] ) ); ?>">
					<?php echo esc_html( $status['status'] ); ?>
				</span>
			<?php endif; ?>
		</div>
		<div class="swarm-heartbeat-grid">
			<div>
				<span class="swarm-heartbeat-label">Last heartbeat</span>
				<strong><?php echo esc_html( $time_data['label'] ); ?></strong>
				<?php if ( ! empty( $display_payload['last_ping_iso'] ) ) : ?>
					<div class="swarm-heartbeat-meta"><?php echo esc_html( $display_payload['last_ping_iso'] ); ?></div>
				<?php else : ?>
					<div class="swarm-heartbeat-meta">We will post the first update shortly.</div>
				<?php endif; ?>
			</div>
			<div>
				<span class="swarm-heartbeat-label">Agents active</span>
				<strong><?php echo esc_html( (int) $display_payload['agents_active'] ); ?></strong>
				<div class="swarm-heartbeat-meta">Real-time swarm availability.</div>
			</div>
			<div>
				<span class="swarm-heartbeat-label">Last mission</span>
				<?php if ( $last_mission_url ) : ?>
					<a href="<?php echo esc_url( $last_mission_url ); ?>" target="_blank" rel="noopener noreferrer">
						<strong><?php echo esc_html( $last_mission_title ); ?></strong>
					</a>
				<?php else : ?>
					<strong><?php echo esc_html( $last_mission_title ); ?></strong>
				<?php endif; ?>
				<div class="swarm-heartbeat-meta">Status: <?php echo esc_html( $display_payload['last_mission']['status'] ?? 'pending' ); ?></div>
			</div>
			<div>
				<span class="swarm-heartbeat-label">Last activity event</span>
				<?php if ( $last_event_url ) : ?>
					<a href="<?php echo esc_url( $last_event_url ); ?>" target="_blank" rel="noopener noreferrer">
						<strong><?php echo esc_html( $last_event_label ); ?></strong>
					</a>
				<?php else : ?>
					<strong><?php echo esc_html( $last_event_label ); ?></strong>
				<?php endif; ?>
				<div class="swarm-heartbeat-meta">Live event stream for the swarm.</div>
			</div>
		</div>
		<div class="swarm-heartbeat-footer">
			<span class="swarm-heartbeat-updated">Last updated: <?php echo esc_html( $time_data['label'] ); ?></span>
			<?php if ( ! empty( $settings['show_copy_button'] ) ) : ?>
				<button type="button" class="swarm-heartbeat-copy">Copy status</button>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function swarm_heartbeat_register_rest_routes() {
	register_rest_route(
		'swarm/v1',
		'/heartbeat',
		array(
			'methods'             => 'POST',
			'callback'            => 'swarm_heartbeat_rest_handler',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'swarm_heartbeat_register_rest_routes' );

function swarm_heartbeat_rest_handler( WP_REST_Request $request ) {
	$settings = swarm_heartbeat_get_settings();
	$token = $request->get_header( 'x-swarm-token' );
	if ( empty( $token ) ) {
		$token = $request->get_param( 'token' );
	}
	if ( empty( $token ) || $token !== $settings['secret_token'] ) {
		return new WP_REST_Response( array( 'message' => 'Invalid token.' ), 401 );
	}

	$data = $request->get_json_params();
	if ( empty( $data ) ) {
		$data = $request->get_body_params();
	}

	$payload = swarm_heartbeat_sanitize_payload( $data );
	$payload['received_at'] = current_time( 'mysql' );
	update_option( SWARM_HEARTBEAT_OPTION, $payload, false );
	
	swarm_heartbeat_log_event( 'rest' );

	return new WP_REST_Response( array( 'message' => 'Heartbeat stored.' ), 200 );
}

function swarm_heartbeat_sanitize_payload( $data ) {
	$payload = array(
		'last_ping_iso' => isset( $data['last_ping_iso'] ) ? sanitize_text_field( $data['last_ping_iso'] ) : '',
		'agents_active' => isset( $data['agents_active'] ) ? (int) $data['agents_active'] : 0,
		'last_mission'  => array(
			'title'  => isset( $data['last_mission']['title'] ) ? sanitize_text_field( $data['last_mission']['title'] ) : 'Awaiting first mission update',
			'url'    => isset( $data['last_mission']['url'] ) ? esc_url_raw( $data['last_mission']['url'] ) : '',
			'status' => isset( $data['last_mission']['status'] ) ? sanitize_text_field( $data['last_mission']['status'] ) : 'pending',
		),
		'last_event'    => array(
			'label' => isset( $data['last_event']['label'] ) ? sanitize_text_field( $data['last_event']['label'] ) : 'Awaiting first activity event',
			'url'   => isset( $data['last_event']['url'] ) ? esc_url_raw( $data['last_event']['url'] ) : '',
		),
	);

	return $payload;
}

function swarm_heartbeat_log_event( $source ) {
	$logs = get_option( SWARM_HEARTBEAT_LOGS, array() );
	if ( ! is_array( $logs ) ) {
		$logs = array();
	}
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	array_unshift(
		$logs,
		array(
			'timestamp' => current_time( 'mysql' ),
			'ip'        => $ip,
			'source'    => $source,
		)
	);
	$logs = array_slice( $logs, 0, SWARM_HEARTBEAT_MAX_LOGS );
	update_option( SWARM_HEARTBEAT_LOGS, $logs, false );
}

function swarm_heartbeat_register_settings() {
	register_setting( 'swarm_heartbeat_settings', SWARM_HEARTBEAT_SETTINGS, 'swarm_heartbeat_sanitize_settings' );
}
add_action( 'admin_init', 'swarm_heartbeat_register_settings' );

function swarm_heartbeat_sanitize_settings( $settings ) {
	$defaults = swarm_heartbeat_default_settings();
	$clean = array();
	$clean['secret_token'] = ! empty( $settings['secret_token'] ) ? sanitize_text_field( $settings['secret_token'] ) : $defaults['secret_token'];
	$clean['stale_after_minutes'] = max( 1, (int) $settings['stale_after_minutes'] );
	$clean['degraded_after_minutes'] = max( $clean['stale_after_minutes'], (int) $settings['degraded_after_minutes'] );
	$clean['show_banner'] = ! empty( $settings['show_banner'] );
	$clean['show_copy_button'] = ! empty( $settings['show_copy_button'] );
	$clean['show_badge'] = ! empty( $settings['show_badge'] );
	return $clean;
}

function swarm_heartbeat_admin_menu() {
	add_options_page(
		'Swarm Heartbeat',
		'Swarm Heartbeat',
		'manage_options',
		'swarm-heartbeat',
		'swarm_heartbeat_settings_page'
	);
}
add_action( 'admin_menu', 'swarm_heartbeat_admin_menu' );

function swarm_heartbeat_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = swarm_heartbeat_get_settings();
	$payload = swarm_heartbeat_get_payload();
	$logs = get_option( SWARM_HEARTBEAT_LOGS, array() );

	if ( isset( $_POST['swarm_heartbeat_manual_update'] ) ) {
		check_admin_referer( 'swarm_heartbeat_manual_update' );
		$manual_payload = array(
			'last_ping_iso' => sanitize_text_field( wp_unslash( $_POST['last_ping_iso'] ?? '' ) ),
			'agents_active' => (int) ( $_POST['agents_active'] ?? 0 ),
			'last_mission'  => array(
				'title'  => sanitize_text_field( wp_unslash( $_POST['last_mission_title'] ?? '' ) ),
				'url'    => esc_url_raw( wp_unslash( $_POST['last_mission_url'] ?? '' ) ),
				'status' => sanitize_text_field( wp_unslash( $_POST['last_mission_status'] ?? '' ) ),
			),
			'last_event'    => array(
				'label' => sanitize_text_field( wp_unslash( $_POST['last_event_label'] ?? '' ) ),
				'url'   => esc_url_raw( wp_unslash( $_POST['last_event_url'] ?? '' ) ),
			),
		);
		$manual_payload = swarm_heartbeat_sanitize_payload( $manual_payload );
		$manual_payload['received_at'] = current_time( 'mysql' );
		update_option( SWARM_HEARTBEAT_OPTION, $manual_payload, false );
		swarm_heartbeat_log_event( 'manual' );
		$payload = $manual_payload;
		echo '<div class="notice notice-success"><p>Heartbeat payload updated.</p></div>';
	}

	$show_preview = isset( $_GET['swarm_preview'] );
	?>
	<div class="wrap">
		<h1>Swarm Heartbeat</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'swarm_heartbeat_settings' );
			$settings_value = get_option( SWARM_HEARTBEAT_SETTINGS, array() );
			?>
			<table class="form-table">
				<tr>
					<th scope="row">Secret token</th>
					<td>
						<input type="text" name="<?php echo esc_attr( SWARM_HEARTBEAT_SETTINGS ); ?>[secret_token]" value="<?php echo esc_attr( $settings['secret_token'] ); ?>" class="regular-text" />
						<p class="description">Send this token with heartbeat POST requests.</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Stale after (minutes)</th>
					<td>
						<input type="number" name="<?php echo esc_attr( SWARM_HEARTBEAT_SETTINGS ); ?>[stale_after_minutes]" value="<?php echo esc_attr( $settings['stale_after_minutes'] ); ?>" min="1" />
					</td>
				</tr>
				<tr>
					<th scope="row">Degraded after (minutes)</th>
					<td>
						<input type="number" name="<?php echo esc_attr( SWARM_HEARTBEAT_SETTINGS ); ?>[degraded_after_minutes]" value="<?php echo esc_attr( $settings['degraded_after_minutes'] ); ?>" min="1" />
					</td>
				</tr>
				<tr>
					<th scope="row">Display toggles</th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( SWARM_HEARTBEAT_SETTINGS ); ?>[show_banner]" value="1" <?php checked( $settings['show_banner'] ); ?> /> Show header banner</label><br />
						<label><input type="checkbox" name="<?php echo esc_attr( SWARM_HEARTBEAT_SETTINGS ); ?>[show_copy_button]" value="1" <?php checked( $settings['show_copy_button'] ); ?> /> Show copy button</label><br />
						<label><input type="checkbox" name="<?php echo esc_attr( SWARM_HEARTBEAT_SETTINGS ); ?>[show_badge]" value="1" <?php checked( $settings['show_badge'] ); ?> /> Show status badge</label>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

		<h2>Manual heartbeat update</h2>
		<form method="post">
			<?php wp_nonce_field( 'swarm_heartbeat_manual_update' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">Last ping ISO</th>
					<td><input type="text" name="last_ping_iso" value="<?php echo esc_attr( $payload['last_ping_iso'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row">Agents active</th>
					<td><input type="number" name="agents_active" value="<?php echo esc_attr( $payload['agents_active'] ?? 0 ); ?>" min="0" /></td>
				</tr>
				<tr>
					<th scope="row">Last mission title</th>
					<td><input type="text" name="last_mission_title" value="<?php echo esc_attr( $payload['last_mission']['title'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row">Last mission URL</th>
					<td><input type="url" name="last_mission_url" value="<?php echo esc_attr( $payload['last_mission']['url'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row">Last mission status</th>
					<td><input type="text" name="last_mission_status" value="<?php echo esc_attr( $payload['last_mission']['status'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row">Last event label</th>
					<td><input type="text" name="last_event_label" value="<?php echo esc_attr( $payload['last_event']['label'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row">Last event URL</th>
					<td><input type="url" name="last_event_url" value="<?php echo esc_attr( $payload['last_event']['url'] ?? '' ); ?>" class="regular-text" /></td>
				</tr>
			</table>
			<button type="submit" name="swarm_heartbeat_manual_update" class="button button-primary">Save heartbeat</button>
			<a class="button" href="<?php echo esc_url( add_query_arg( 'swarm_preview', 1 ) ); ?>">Render preview card</a>
		</form>

		<?php if ( $show_preview ) : ?>
			<h2>Preview</h2>
			<?php echo wp_kses_post( swarm_heartbeat_render_card() ); ?>
		<?php endif; ?>

		<h2>Recent heartbeat logs</h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th>Timestamp</th>
					<th>IP</th>
					<th>Source</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $logs ) ) : ?>
					<tr><td colspan="3">No heartbeat posts yet.</td></tr>
				<?php else : ?>
					<?php foreach ( $logs as $log ) : ?>
						<tr>
							<td><?php echo esc_html( $log['timestamp'] ?? '' ); ?></td>
							<td><?php echo esc_html( $log['ip'] ?? '' ); ?></td>
							<td><?php echo esc_html( $log['source'] ?? '' ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}

function swarm_heartbeat_render_banner() {
	static $rendered = false;
	if ( $rendered ) {
		return;
	}
	$settings = swarm_heartbeat_get_settings();
	if ( empty( $settings['show_banner'] ) ) {
		return;
	}
	$rendered = true;
	echo wp_kses_post( swarm_heartbeat_render_card( array( 'force_banner' => true ) ) );
}
add_action( 'wp_body_open', 'swarm_heartbeat_render_banner' );
add_action( 'wp_footer', 'swarm_heartbeat_render_banner', 1 );

