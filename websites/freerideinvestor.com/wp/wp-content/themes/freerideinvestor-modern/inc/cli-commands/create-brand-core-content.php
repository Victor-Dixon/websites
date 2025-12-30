<?php
/**
 * Create Brand Core Content for freerideinvestor.com
 * Phase 1 P0 Fixes - BRAND-01, BRAND-02, BRAND-03
 * 
 * Usage: wp eval-file inc/cli-commands/create-brand-core-content.php
 * 
 * @package SimplifiedTradingTheme
 */

// Only run in CLI context - prevent execution during normal WordPress load
// This file should only be executed via: wp eval-file inc/cli-commands/create-brand-core-content.php
if (!defined('WP_CLI') || !WP_CLI || php_sapi_name() !== 'cli') {
    // Don't die() during normal WordPress load - just return silently
    // This allows the file to be included without executing
    return;
}

/**
 * Create Positioning Statement for freerideinvestor.com
 */
function create_freerideinvestor_positioning_statement() {
    // Check if positioning statement already exists
    $existing = get_posts([
        'post_type' => 'positioning_statement',
        'meta_key' => 'site_assignment',
        'meta_value' => 'freerideinvestor.com',
        'posts_per_page' => 1,
        'post_status' => 'any'
    ]);
    
    if (!empty($existing)) {
        $post_id = $existing[0]->ID;
        WP_CLI::warning("Positioning statement already exists (ID: {$post_id}). Updating content...");
    } else {
        $post_id = wp_insert_post([
            'post_title' => 'FreeRide Investor Positioning Statement',
            'post_content' => 'For traders and investors who are tired of generic advice, TikTok trading theatrics, and emotional loops that destroy accounts, we provide disciplined, risk-first trading education and proven TBOW tactics unlike signal services (no context), theory-heavy courses (no execution), or lifestyle gurus (no substance) because we focus on removing downside pressure through systems, discipline, and execution over prediction, building financial freedom without hype.',
            'post_status' => 'publish',
            'post_type' => 'positioning_statement'
        ]);
    }

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, 'target_audience', 'traders and investors tired of generic advice');
        update_post_meta($post_id, 'pain_points', 'generic advice, theory-heavy courses, signal services, emotional loops');
        update_post_meta($post_id, 'unique_value', 'disciplined, risk-first trading education and proven TBOW tactics');
        update_post_meta($post_id, 'differentiation', 'we focus on removing downside pressure through systems, discipline, and execution over prediction, building financial freedom without hype');
        update_post_meta($post_id, 'site_assignment', 'freerideinvestor.com');
        
        WP_CLI::success("Created positioning statement (ID: {$post_id})");
        return $post_id;
    } else {
        $error_msg = is_wp_error($post_id) ? $post_id->get_error_message() : 'Unknown error';
        WP_CLI::error("Failed to create positioning statement: " . $error_msg);
        return false;
    }
}

/**
 * Create Offer Ladder for freerideinvestor.com
 */
function create_freerideinvestor_offer_ladder() {
    $offers = [
        [
            'title' => 'Free TBOW Tactics',
            'level' => 1,
            'name' => 'Free TBOW tactics (blog)',
            'description' => 'Access free trading tactics and strategies on our blog',
            'price' => 'Free',
            'cta_text' => 'Read Blog',
            'cta_url' => '/blog'
        ],
        [
            'title' => 'Free Resources',
            'level' => 2,
            'name' => 'Free resources (roadmap PDF, mindset journal)',
            'description' => 'Download our free trading roadmap PDF and mindset journal',
            'price' => 'Free',
            'cta_text' => 'Get Free Resources',
            'cta_url' => '/resources'
        ],
        [
            'title' => 'Newsletter Subscription',
            'level' => 3,
            'name' => 'Newsletter subscription',
            'description' => 'Get weekly trading insights and TBOW tactics delivered to your inbox',
            'price' => 'Free',
            'cta_text' => 'Subscribe Now',
            'cta_url' => '/newsletter'
        ],
        [
            'title' => 'Premium Membership',
            'level' => 4,
            'name' => 'Premium membership (courses, strategies, cheat sheets)',
            'description' => 'Access premium courses, advanced strategies, and exclusive cheat sheets',
            'price' => '$X/month',
            'cta_text' => 'Join Premium',
            'cta_url' => '/premium'
        ],
        [
            'title' => 'Advanced Coaching',
            'level' => 5,
            'name' => 'Advanced coaching/community',
            'description' => 'Get personalized coaching and join our exclusive trading community',
            'price' => '$X,XXX',
            'cta_text' => 'Learn More',
            'cta_url' => '/coaching'
        ]
    ];

    $created = [];
    foreach ($offers as $offer) {
        $post_id = wp_insert_post([
            'post_title' => $offer['title'],
            'post_content' => $offer['description'],
            'post_status' => 'publish',
            'post_type' => 'offer_ladder'
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, 'ladder_level', $offer['level']);
            update_post_meta($post_id, 'offer_name', $offer['name']);
            update_post_meta($post_id, 'offer_description', $offer['description']);
            update_post_meta($post_id, 'price_point', $offer['price']);
            update_post_meta($post_id, 'cta_text', $offer['cta_text']);
            update_post_meta($post_id, 'cta_url', $offer['cta_url']);
            update_post_meta($post_id, 'site_assignment', 'freerideinvestor.com');
            
            $created[] = $post_id;
            WP_CLI::success("Created offer ladder level {$offer['level']} (ID: {$post_id})");
        } else {
            $error_msg = is_wp_error($post_id) ? $post_id->get_error_message() : 'Unknown error';
            WP_CLI::warning("Failed to create offer ladder level {$offer['level']}: " . $error_msg);
        }
    }

    return $created;
}

/**
 * Create ICP Definition for freerideinvestor.com
 */
function create_freerideinvestor_icp_definition() {
    $post_id = wp_insert_post([
        'post_title' => 'FreeRide Investor Ideal Customer Profile',
        'post_content' => 'For active traders (day/swing traders, $10K-$500K accounts) struggling with inconsistent results, we eliminate guesswork and provide proven trading strategies. Your outcome: consistent edge, reduced losses, trading confidence.',
        'post_status' => 'publish',
        'post_type' => 'icp_definition'
    ]);

    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, 'target_demographic', 'Active traders (day/swing traders, $10K-$500K accounts)');
        update_post_meta($post_id, 'pain_points', 'inconsistent results, guesswork');
        update_post_meta($post_id, 'desired_outcomes', 'consistent edge, reduced losses, trading confidence');
        update_post_meta($post_id, 'site_assignment', 'freerideinvestor.com');
        
        WP_CLI::success("Created ICP definition (ID: {$post_id})");
        return $post_id;
    } else {
        $error_msg = is_wp_error($post_id) ? $post_id->get_error_message() : 'Unknown error';
        WP_CLI::error("Failed to create ICP definition: " . $error_msg);
        return false;
    }
}

// Execute ONLY if run explicitly via WP-CLI eval-file (not during normal WordPress load)
// The early return above prevents execution during normal WordPress load
// This code only runs if the file is executed directly via wp eval-file
if (defined('WP_CLI') && WP_CLI && php_sapi_name() === 'cli') {
    WP_CLI::line('Creating Brand Core content for freerideinvestor.com...');
    
    create_freerideinvestor_positioning_statement();
    create_freerideinvestor_offer_ladder();
    create_freerideinvestor_icp_definition();
    
    WP_CLI::success('Brand Core content creation complete!');
}

