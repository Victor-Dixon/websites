<?php
/**
 * Core Digital Dreamscape Functions
 *
 * Essential functions that must be available for both CLI and web contexts
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ============================================
// WORLD ARCHIVE METADATA CONTRACT
// ============================================

/**
 * Register custom fields for World Archive artifacts
 * Compatible with existing metadata system
 */
function digitaldreamscape_register_artifact_meta() {
    // Artifact type classification (maps to existing system)
    register_meta('post', 'artifact_type', [
        'type' => 'string',
        'description' => 'Type of world artifact: episode, artifact, canon, devlog',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Episode number (for episode artifacts)
    register_meta('post', 'episode_number', [
        'type' => 'integer',
        'description' => 'Episode number for episode artifacts',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint'
    ]);

    // Backward compatibility with existing 'canonical' field
    register_meta('post', 'canonical', [
        'type' => 'string',
        'description' => 'Legacy canonical flag (true/false)',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Questline association
    register_meta('post', 'questline', [
        'type' => 'string',
        'description' => 'Associated questline or project',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Artifact state
    register_meta('post', 'artifact_state', [
        'type' => 'string',
        'description' => 'Current state: active, resolved, ruins, canon',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Era/period
    register_meta('post', 'era', [
        'type' => 'string',
        'description' => 'Time period or version (e.g., 2026, v3.0)',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Source system
    register_meta('post', 'source_system', [
        'type' => 'string',
        'description' => 'Internal system source: devlog, tasklist, agent, manual',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Agent attribution (for swarm-generated content)
    register_meta('post', 'agent_id', [
        'type' => 'string',
        'description' => 'Agent that generated this artifact (Agent-1, Agent-2, etc.)',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Quest progress (for task-based artifacts)
    register_meta('post', 'quest_progress', [
        'type' => 'string',
        'description' => 'Progress indicator (e.g., "2/5 complete")',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Internal source reference
    register_meta('post', 'internal_source', [
        'type' => 'string',
        'description' => 'Path to internal source file (for devlogs, task lists, etc.)',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Canon weight (how canonical this artifact is)
    register_meta('post', 'canon_weight', [
        'type' => 'integer',
        'description' => 'Canon significance (1-10, higher = more canonical)',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint'
    ]);
}
add_action('init', 'digitaldreamscape_register_artifact_meta');

// ============================================
// ARTIFACT PROMOTION FUNCTIONS
// ============================================

/**
 * Promote internal content to public artifact
 * Compatible with existing template structure
 *
 * @param array $artifact_data Artifact metadata
 * @return int|WP_Error Post ID on success, error on failure
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

    // Set artifact metadata (compatible with existing system)
    $metadata_fields = [
        'artifact_type' => $artifact_type,
        'episode_number' => $artifact_data['episode_number'] ?? '',
        'artifact_state' => $artifact_data['artifact_state'] ?? 'active',
        'questline' => $artifact_data['questline'] ?? '',
        'era' => $artifact_data['era'] ?? date('Y'),
        'source_system' => $artifact_data['source_system'] ?? 'manual',
        'agent_id' => $artifact_data['agent_id'] ?? '',
        'quest_progress' => $artifact_data['quest_progress'] ?? '',
        'internal_source' => $artifact_data['internal_source'] ?? '',
        'canon_weight' => $artifact_data['canon_weight'] ?? 1,
        // Legacy compatibility
        'canonical' => ($artifact_type === 'canon' || $artifact_data['canonical'] === 'true') ? 'true' : 'false'
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
 * Get artifacts by type and filters
 *
 * @param array $args Query arguments
 * @return WP_Query
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

/**
 * Get active questlines with progress
 *
 * @return array Array of questline data
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
 * Auto-promote devlog to episode artifact
 *
 * @param string $devlog_path Path to devlog file
 * @return int|WP_Error Post ID or error
 */
function digitaldreamscape_promote_devlog($devlog_path) {
    if (!file_exists($devlog_path)) {
        return new WP_Error('file_not_found', 'Devlog file not found');
    }

    $content = file_get_contents($devlog_path);
    $filename = basename($devlog_path);

    // Extract metadata from filename (YYYY-MM-DD_description.md)
    preg_match('/(\d{4}-\d{2}-\d{2})_(.+)\.md$/', $filename, $matches);

    if (empty($matches)) {
        return new WP_Error('invalid_filename', 'Devlog filename must be YYYY-MM-DD_description.md');
    }

    $date = $matches[1];
    $description = str_replace(['_', '-'], ' ', $matches[2]);

    // Extract title from content (first # header)
    preg_match('/^#\s+(.+)$/m', $content, $title_match);
    $title = $title_match[1] ?? $description;

    // Extract excerpt (first paragraph after title)
    preg_match('/^#\s+.+\n\n(.+?)(?:\n\n|---|$)/s', $content, $excerpt_match);
    $excerpt = $excerpt_match[1] ?? substr(strip_tags($content), 0, 200) . '...';

    // Determine questline from content or filename
    $questline = 'general';
    if (preg_match('/questline:\s*(.+)/i', $content, $ql_match)) {
        $questline = trim($ql_match[1]);
    }

    $artifact_data = [
        'title' => $title,
        'content' => $content,
        'excerpt' => $excerpt,
        'artifact_type' => 'episode',
        'questline' => $questline,
        'artifact_state' => 'active',
        'era' => date('Y', strtotime($date)),
        'source_system' => 'devlog',
        'internal_source' => $devlog_path
    ];

    return digitaldreamscape_promote_artifact($artifact_data);
}

/**
 * Auto-promote task list to questline artifacts
 *
 * @param array $tasks Task list data
 * @param string $questline_name Name of the questline
 * @return array Array of created post IDs
 */
function digitaldreamscape_promote_tasklist($tasks, $questline_name) {
    $created_posts = [];

    foreach ($tasks as $task) {
        if (empty($task['title'])) continue;

        $artifact_data = [
            'title' => $task['title'],
            'content' => $task['description'] ?? '',
            'excerpt' => substr($task['description'] ?? $task['title'], 0, 150) . '...',
            'artifact_type' => 'episode',
            'questline' => $questline_name,
            'artifact_state' => $task['completed'] ? 'resolved' : 'active',
            'era' => date('Y'),
            'source_system' => 'tasklist',
            'quest_progress' => $task['progress'] ?? ''
        ];

        $post_id = digitaldreamscape_promote_artifact($artifact_data);
        if (!is_wp_error($post_id)) {
            $created_posts[] = $post_id;
        }
    }

    return $created_posts;
}

// ============================================
// WORLD ARCHIVE DISPLAY FUNCTIONS
// ============================================

/**
 * Get artifact type icon
 *
 * @param string $type Artifact type
 * @return string Icon emoji
 */
function digitaldreamscape_get_artifact_icon($type) {
    $icons = [
        'episode' => '🎭',
        'artifact' => '📦',
        'canon' => '📜',
        'devlog' => '📝'
    ];

    return $icons[$type] ?? '📄';
}

/**
 * Get artifact state styling class
 *
 * @param string $state Artifact state
 * @return string CSS class
 */
function digitaldreamscape_get_state_class($state) {
    $classes = [
        'active' => 'artifact-active',
        'resolved' => 'artifact-resolved',
        'canon' => 'artifact-canon',
        'ruins' => 'artifact-ruins'
    ];

    return $classes[$state] ?? 'artifact-unknown';
}

/**
 * Display artifact metadata in templates
 *
 * @param int $post_id Post ID
 */
function digitaldreamscape_display_artifact_meta($post_id) {
    $type = get_post_meta($post_id, 'artifact_type', true);
    $questline = get_post_meta($post_id, 'questline', true);
    $state = get_post_meta($post_id, 'artifact_state', true);
    $agent = get_post_meta($post_id, 'agent_id', true);
    $progress = get_post_meta($post_id, 'quest_progress', true);

    if ($type) {
        echo '<div class="artifact-meta">';
        echo '<span class="artifact-type">' . digitaldreamscape_get_artifact_icon($type) . ' ' . ucfirst($type) . '</span>';

        if ($questline) {
            echo '<span class="artifact-questline">Questline: ' . esc_html($questline) . '</span>';
        }

        if ($state) {
            echo '<span class="artifact-state ' . digitaldreamscape_get_state_class($state) . '">' . ucfirst($state) . '</span>';
        }

        if ($agent) {
            echo '<span class="artifact-agent">Agent: ' . esc_html($agent) . '</span>';
        }

        if ($progress) {
            echo '<span class="artifact-progress">Progress: ' . esc_html($progress) . '</span>';
        }

        echo '</div>';
    }
}

// ============================================
// ADMIN INTEGRATION
// ============================================

/**
 * Add custom meta boxes for artifact editing
 */
function digitaldreamscape_add_meta_boxes() {
    add_meta_box(
        'artifact_meta_box',
        'World Archive Metadata',
        'digitaldreamscape_artifact_meta_box_callback',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'digitaldreamscape_add_meta_boxes');

/**
 * Meta box callback
 */
function digitaldreamscape_artifact_meta_box_callback($post) {
    wp_nonce_field('digitaldreamscape_artifact_meta', 'artifact_meta_nonce');

    $fields = [
        'artifact_type' => [
            'label' => 'Artifact Type',
            'type' => 'select',
            'options' => ['episode', 'artifact', 'canon', 'devlog']
        ],
        'questline' => [
            'label' => 'Questline',
            'type' => 'text'
        ],
        'artifact_state' => [
            'label' => 'State',
            'type' => 'select',
            'options' => ['active', 'resolved', 'canon', 'ruins']
        ],
        'era' => [
            'label' => 'Era',
            'type' => 'text'
        ],
        'source_system' => [
            'label' => 'Source System',
            'type' => 'select',
            'options' => ['devlog', 'tasklist', 'agent', 'manual']
        ],
        'agent_id' => [
            'label' => 'Agent ID',
            'type' => 'text'
        ],
        'quest_progress' => [
            'label' => 'Quest Progress',
            'type' => 'text'
        ],
        'canon_weight' => [
            'label' => 'Canon Weight (1-10)',
            'type' => 'number',
            'min' => 1,
            'max' => 10
        ]
    ];

    echo '<table class="form-table">';
    foreach ($fields as $key => $field) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<tr>';
        echo '<th><label for="' . $key . '">' . $field['label'] . '</label></th>';
        echo '<td>';

        switch ($field['type']) {
            case 'select':
                echo '<select name="' . $key . '" id="' . $key . '">';
                foreach ($field['options'] as $option) {
                    echo '<option value="' . $option . '" ' . selected($value, $option, false) . '>' . ucfirst($option) . '</option>';
                }
                echo '</select>';
                break;

            case 'number':
                echo '<input type="number" name="' . $key . '" id="' . $key . '" value="' . esc_attr($value) . '"';
                if (isset($field['min'])) echo ' min="' . $field['min'] . '"';
                if (isset($field['max'])) echo ' max="' . $field['max'] . '"';
                echo ' />';
                break;

            default:
                echo '<input type="text" name="' . $key . '" id="' . $key . '" value="' . esc_attr($value) . '" />';
        }

        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

/**
 * Save artifact metadata
 */
function digitaldreamscape_save_artifact_meta($post_id) {
    if (!isset($_POST['artifact_meta_nonce']) ||
        !wp_verify_nonce($_POST['artifact_meta_nonce'], 'digitaldreamscape_artifact_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $fields = [
        'artifact_type', 'questline', 'artifact_state', 'era',
        'source_system', 'agent_id', 'quest_progress', 'canon_weight'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'digitaldreamscape_save_artifact_meta');

// ============================================
// REST API ENDPOINTS
// ============================================

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

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Check if current environment is CLI
 */
function digitaldreamscape_is_cli() {
    return php_sapi_name() === 'cli';
}

/**
 * Get system information for debugging
 */
function digitaldreamscape_system_info() {
    return [
        'php_version' => PHP_VERSION,
        'environment' => php_sapi_name(),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
    ];
}