<?php
/**
 * Template Tag Functions
 *
 * Helper functions for use in templates
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get artifact type icon
 *
 * @param string $type Artifact type
 * @return string Icon emoji
 */
function digitaldreamscape_get_artifact_icon($type) {
    $icons = [
        'episode' => '🎭',
        'artifact' => '🔮',
        'canon' => '📜',
        'devlog' => '⚙️'
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
function digitaldreamscape_display_artifact_meta($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $type = get_post_meta($post_id, 'artifact_type', true);
    $questline = get_post_meta($post_id, 'questline', true);
    $state = get_post_meta($post_id, 'artifact_state', true);
    $agent = get_post_meta($post_id, 'agent_id', true);
    $progress = get_post_meta($post_id, 'quest_progress', true);
    $era = get_post_meta($post_id, 'era', true);

    if ($type) {
        echo '<div class="artifact-meta">';

        if ($type) {
            echo '<span class="artifact-type">' . digitaldreamscape_get_artifact_icon($type) . ' ' . ucfirst($type) . '</span>';
        }

        if ($questline) {
            echo '<span class="artifact-questline">Questline: ' . esc_html($questline) . '</span>';
        }

        if ($era) {
            echo '<span class="artifact-era">Era: ' . esc_html($era) . '</span>';
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

/**
 * Get questline synopsis
 *
 * @param string $slug Questline slug
 * @return string Synopsis text
 */
function digitaldreamscape_get_questline_synopsis($slug) {
    $synopses = [
        'debt-purge' => 'Tracking the systematic elimination of technical debt. Every fix, regression, and optimization becomes part of the permanent record. This questline explores what survives the purge and what gets rebuilt.',
        'system-architecture' => 'The evolution of system design patterns. From initial implementations to optimized architectures, this questline documents the emergence of stable, scalable solutions.',
        'world-building' => 'The construction of Digital Dreamscape itself. Meta-narratives, system integrations, and the emergence of autonomous agents that maintain the world.',
        'trading-domain' => 'The FreeRide Investor questline. Risk-first trading systems, emotional discipline frameworks, and the automation of consistent execution.',
        'agent-development' => 'The evolution of AI agents within the swarm. Coordination protocols, specialization patterns, and the emergence of autonomous behavior.'
    ];

    return $synopses[$slug] ?? "Questline tracking the development and evolution of {$slug}. Artifacts document progress, challenges, and breakthroughs in this domain.";
}

/**
 * Get filter description for SEO
 *
 * @param string $type Filter type
 * @return string Description
 */
function digitaldreamscape_get_filter_description($type) {
    $descriptions = [
        'canon' => 'sacred entries. stable lore. no noise. these are the permanent artifacts that define the digital dreamscape.',
        'episode' => 'narrative fragments. world snapshots. these entries capture specific moments in the simulation\'s evolution.',
        'artifact' => 'discovered objects. system outputs. tools, fixes, and creations that emerged from the world.',
        'devlog' => 'builder\'s notes. raw telemetry. direct from the development trenches of digital dreamscape.'
    ];
    return $descriptions[$type] ?? '';
}

/**
 * Get questline description for SEO
 *
 * @param string $questline Questline slug
 * @return string Description
 */
function digitaldreamscape_get_questline_description($questline_slug) {
    $questline = get_term_by('slug', $questline_slug, 'category');
    if (!$questline) return '';

    $post_count = $questline->count;
    $description = "Questline: {$questline->name}. {$post_count} artifacts. ";

    // Add questline-specific description
    if (stripos($questline->name, 'debt') !== false) {
        $description .= "Tracking the purge of technical debt. Fixes, regressions, and what survived the optimization.";
    } elseif (stripos($questline->name, 'system') !== false) {
        $description .= "System architecture evolution. Patterns, constraints, and emergent behaviors.";
    } elseif (stripos($questline->name, 'world') !== false) {
        $description .= "The construction of Digital Dreamscape itself. Meta-narratives and autonomous agents.";
    } elseif (stripos($questline->name, 'trading') !== false) {
        $description .= "FreeRide Investor questline. Risk-first trading and emotional discipline.";
    } elseif (stripos($questline->name, 'agent') !== false) {
        $description .= "AI agent evolution. Coordination protocols and autonomous behavior.";
    } else {
        $description .= "Ongoing questline in the digital dreamscape. Follow the artifacts for progress updates.";
    }

    return $description;
}

/**
 * Get state description for SEO
 *
 * @param string $state State
 * @return string Description
 */
function digitaldreamscape_get_state_description($state) {
    $descriptions = [
        'active' => 'living artifacts. unresolved loops. these entries represent current quests and open problems.',
        'resolved' => 'completed quests. closed loops. artifacts that reached their conclusion or stable state.',
        'abandoned' => 'forgotten paths. abandoned branches. historical artifacts that were set aside or superseded.'
    ];
    return $descriptions[$state] ?? '';
}

/**
 * Print the archive title with filter context
 */
function digitaldreamscape_archive_title() {
    $title = 'World Archive';

    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_date()) {
        if (is_year()) {
            $title = get_the_date('Y');
        } elseif (is_month()) {
            $title = get_the_date('F Y');
        } elseif (is_day()) {
            $title = get_the_date('F j, Y');
        }
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    }

    echo esc_html($title);
}

/**
 * Print the archive description with filter context
 */
function digitaldreamscape_archive_description() {
    $description = 'Episodes, canon, artifacts, and unfinished quests.';

    if (is_category()) {
        $description = category_description();
        if (!$description) {
            $description = digitaldreamscape_get_questline_synopsis(get_query_var('category_name'));
        }
    } elseif (is_tag()) {
        $description = tag_description();
    }

    echo esc_html($description);
}