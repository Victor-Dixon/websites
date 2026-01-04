<?php
/**
 * REST API Endpoints
 *
 * External API endpoints for artifact promotion
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API endpoints for external promotion
 */
function digitaldreamscape_register_api_endpoints() {
    register_rest_route('digitaldreamscape/v1', '/promote-artifact', [
        'methods' => 'POST',
        'callback' => 'digitaldreamscape_api_promote_artifact',
        'permission_callback' => 'digitaldreamscape_api_permissions_check'
    ]);

    register_rest_route('digitaldreamscape/v1', '/questlines', [
        'methods' => 'GET',
        'callback' => 'digitaldreamscape_api_get_questlines',
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('digitaldreamscape/v1', '/artifacts', [
        'methods' => 'GET',
        'callback' => 'digitaldreamscape_api_get_artifacts',
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'digitaldreamscape_register_api_endpoints');

/**
 * API permission check
 */
function digitaldreamscape_api_permissions_check($request) {
    // Add your authentication logic here
    return current_user_can('edit_posts');
}

/**
 * API endpoint to promote artifacts
 */
function digitaldreamscape_api_promote_artifact($request) {
    $data = $request->get_json_params();

    $result = digitaldreamscape_promote_artifact($data);

    if (is_wp_error($result)) {
        return new WP_Error('promotion_failed', $result->get_error_message(), ['status' => 400]);
    }

    return [
        'success' => true,
        'post_id' => $result,
        'message' => 'Artifact promoted successfully'
    ];
}

/**
 * API endpoint to get questlines
 */
function digitaldreamscape_api_get_questlines($request) {
    return digitaldreamscape_get_active_questlines();
}

/**
 * API endpoint to get artifacts with filtering
 */
function digitaldreamscape_api_get_artifacts($request) {
    $params = $request->get_params();

    $args = [
        'post_type' => 'post',
        'posts_per_page' => $params['per_page'] ?? 10,
        'paged' => $params['page'] ?? 1
    ];

    // Apply filters
    if (!empty($params['artifact_type'])) {
        $args['meta_query'][] = [
            'key' => 'artifact_type',
            'value' => $params['artifact_type'],
            'compare' => '='
        ];
    }

    if (!empty($params['artifact_state'])) {
        $args['meta_query'][] = [
            'key' => 'artifact_state',
            'value' => $params['artifact_state'],
            'compare' => '='
        ];
    }

    if (!empty($params['questline'])) {
        $args['meta_query'][] = [
            'key' => 'questline',
            'value' => $params['questline'],
            'compare' => '='
        ];
    }

    if (!empty($params['search'])) {
        $args['s'] = $params['search'];
    }

    $query = new WP_Query($args);
    $artifacts = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $artifacts[] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'permalink' => get_permalink(),
                'artifact_type' => get_post_meta(get_the_ID(), 'artifact_type', true),
                'artifact_state' => get_post_meta(get_the_ID(), 'artifact_state', true),
                'questline' => get_post_meta(get_the_ID(), 'questline', true),
                'era' => get_post_meta(get_the_ID(), 'era', true),
                'date' => get_the_date('c'),
                'modified' => get_the_modified_date('c')
            ];
        }
        wp_reset_postdata();
    }

    return [
        'artifacts' => $artifacts,
        'total' => $query->found_posts,
        'pages' => $query->max_num_pages,
        'current_page' => $query->query_vars['paged']
    ];
}

/**
 * Promote internal content to public artifact
 */
function digitaldreamscape_promote_artifact($artifact_data) {
    // Required fields
    if (empty($artifact_data['title'])) {
        return new WP_Error('missing_required', 'Title is required');
    }

    // Default artifact type
    $artifact_type = $artifact_data['artifact_type'] ?? 'episode';

    // Prepare post data
    $post_data = [
        'post_title' => $artifact_data['title'],
        'post_content' => $artifact_data['content'] ?? '',
        'post_excerpt' => $artifact_data['excerpt'] ?? '',
        'post_status' => 'publish',
        'post_type' => 'post',
        'post_category' => $artifact_data['categories'] ?? []
    ];

    // Create or update post
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // Set artifact metadata
    $metadata_fields = [
        'artifact_type' => $artifact_type,
        'artifact_state' => $artifact_data['artifact_state'] ?? 'active',
        'questline' => $artifact_data['questline'] ?? '',
        'era' => $artifact_data['era'] ?? date('Y'),
        'source_system' => $artifact_data['source_system'] ?? 'manual',
        'agent_id' => $artifact_data['agent_id'] ?? '',
        'quest_progress' => $artifact_data['quest_progress'] ?? '',
        'internal_source' => $artifact_data['internal_source'] ?? '',
        'canon_weight' => $artifact_data['canon_weight'] ?? 1,
        'canonical' => ($artifact_type === 'canon' || ($artifact_data['canonical'] ?? 'false') === 'true') ? 'true' : 'false'
    ];

    foreach ($metadata_fields as $field => $value) {
        if (isset($artifact_data[$field])) {
            $value = $artifact_data[$field];
        }
        if (!empty($value)) {
            update_post_meta($post_id, $field, $value);
        }
    }

    // Set featured image if provided
    if (!empty($artifact_data['featured_image'])) {
        set_post_thumbnail($post_id, $artifact_data['featured_image']);
    }

    return $post_id;
}

/**
 * Get active questlines with progress
 */
function digitaldreamscape_get_active_questlines() {
    global $wpdb;

    $questlines = $wpdb->get_results("
        SELECT
            meta_value as questline,
            COUNT(*) as total_artifacts,
            SUM(CASE WHEN meta_key = 'artifact_state' AND meta_value = 'resolved' THEN 1 ELSE 0 END) as resolved_count
        FROM {$wpdb->postmeta}
        WHERE meta_key IN ('questline', 'artifact_state')
        AND post_id IN (
            SELECT ID FROM {$wpdb->posts}
            WHERE post_status = 'publish'
            AND post_type = 'post'
        )
        GROUP BY meta_value
        HAVING questline IS NOT NULL
        ORDER BY resolved_count DESC, total_artifacts DESC
    ");

    $result = [];
    foreach ($questlines as $ql) {
        $result[$ql->questline] = [
            'total' => $ql->total_artifacts,
            'resolved' => $ql->resolved_count,
            'progress' => $ql->resolved_count . '/' . $ql->total_artifacts
        ];
    }

    return $result;
}

/**
 * Get artifacts by type and filters
 */
function digitaldreamscape_get_artifacts($args = []) {
    $defaults = [
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_query' => [],
        'tax_query' => []
    ];

    $query_args = wp_parse_args($args, $defaults);

    // Add artifact type filter
    if (!empty($args['artifact_type'])) {
        $query_args['meta_query'][] = [
            'key' => 'artifact_type',
            'value' => $args['artifact_type'],
            'compare' => '='
        ];
    }

    // Add questline filter
    if (!empty($args['questline'])) {
        $query_args['meta_query'][] = [
            'key' => 'questline',
            'value' => $args['questline'],
            'compare' => '='
        ];
    }

    // Add state filter
    if (!empty($args['artifact_state'])) {
        $query_args['meta_query'][] = [
            'key' => 'artifact_state',
            'value' => $args['artifact_state'],
            'compare' => '='
        ];
    }

    return new WP_Query($query_args);
}