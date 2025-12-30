<?php
/**
 * Proof Metrics Calculator
 * SSOT: Dynamically calculate proof metrics from actual data
 *
 * @package DaDudeKC
 */

/**
 * Get proof metrics dynamically
 * 
 * @return array Array of metric key => value pairs
 */
function dadudekc_get_proof_metrics() {
    // Count AI Agents (from experiments or projects)
    $agent_experiments = get_posts([
        'post_type' => 'experiment',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'experiment_status',
                'value' => 'live',
                'compare' => '=',
            ],
        ],
    ]);
    $ai_agents_count = count($agent_experiments);
    
    // Count Revenue Sites (from projects with 'revenue-site' tag or meta)
    $revenue_sites = get_posts([
        'post_type' => 'project',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'project_category',
                'value' => 'revenue-site',
                'compare' => '=',
            ],
        ],
    ]);
    $revenue_sites_count = count($revenue_sites);
    
    // Calculate average sprint delivery (from projects with delivery_time meta)
    $projects_with_delivery = get_posts([
        'post_type' => 'project',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'project_delivery_time',
                'compare' => 'EXISTS',
            ],
        ],
    ]);
    $total_delivery_time = 0;
    $delivery_count = 0;
    foreach ($projects_with_delivery as $project) {
        $delivery_time = get_post_meta($project->ID, 'project_delivery_time', true);
        if ($delivery_time) {
            $total_delivery_time += intval($delivery_time);
            $delivery_count++;
        }
    }
    $avg_delivery = $delivery_count > 0 ? round($total_delivery_time / $delivery_count) : 72;
    $avg_delivery_display = $avg_delivery . 'h';
    
    // Check for 24/7 automation (from experiments with 'automation' status)
    $automation_experiments = get_posts([
        'post_type' => 'experiment',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'experiment_status',
                'value' => 'live',
                'compare' => '=',
            ],
            [
                'key' => 'experiment_type',
                'value' => 'automation',
                'compare' => '=',
            ],
        ],
    ]);
    $automation_running = count($automation_experiments) > 0 ? '24/7' : '0';
    
    return [
        'ai_agents' => $ai_agents_count ?: 8, // Fallback to 8 if no data
        'revenue_sites' => $revenue_sites_count ?: 4, // Fallback to 4 if no data
        'avg_delivery' => $avg_delivery_display,
        'automation' => $automation_running,
    ];
}

/**
 * Get shipped systems dynamically
 * 
 * @return array Array of system names
 */
function dadudekc_get_shipped_systems() {
    $shipped_projects = get_posts([
        'post_type' => 'project',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'project_status',
                'value' => 'shipped',
                'compare' => '=',
            ],
        ],
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
    
    $systems = [];
    foreach ($shipped_projects as $project) {
        $systems[] = $project->post_title;
    }
    
    // Fallback to default if no data
    if (empty($systems)) {
        return [
            'AI Agent Swarm (8 specialized agents)',
            'TradingRobotPlug Dashboard',
            'FreeRideInvestor Platform',
            'WeAreSwarm Build-in-Public Feed',
        ];
    }
    
    return $systems;
}

/**
 * Get active experiments dynamically
 * 
 * @return array Array of experiment names
 */
function dadudekc_get_active_experiments() {
    $active_experiments = get_posts([
        'post_type' => 'experiment',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [
            [
                'key' => 'experiment_status',
                'value' => ['live', 'in-progress'],
                'compare' => 'IN',
            ],
        ],
        'orderby' => 'date',
        'order' => 'DESC',
    ]);
    
    $experiments = [];
    foreach ($active_experiments as $experiment) {
        $experiments[] = $experiment->post_title;
    }
    
    // Fallback to default if no data
    if (empty($experiments)) {
        return [
            'Autonomous agent coordination',
            'Paper trading validation system',
            'Real-time performance dashboards',
            'Discord bot automation',
        ];
    }
    
    return $experiments;
}

