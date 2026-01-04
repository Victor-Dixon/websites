<?php
/**
 * Custom Meta Fields and Admin Integration
 *
 * Custom fields for World Archive artifacts
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom fields for World Archive artifacts
 */
function digitaldreamscape_register_artifact_meta() {
    // Artifact type classification
    register_meta('post', 'artifact_type', [
        'type' => 'string',
        'description' => 'Type of world artifact: episode, artifact, canon, devlog',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
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
        'description' => 'Current state: active, resolved, canon, ruins',
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

    // Agent attribution
    register_meta('post', 'agent_id', [
        'type' => 'string',
        'description' => 'Agent that generated this artifact (Agent-1, Agent-2, etc.)',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Quest progress
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
        'description' => 'Path to internal source file',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    // Canon weight
    register_meta('post', 'canon_weight', [
        'type' => 'integer',
        'description' => 'Canon significance (1-10)',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint'
    ]);
}
add_action('init', 'digitaldreamscape_register_artifact_meta');

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