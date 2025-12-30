<?php
/**
 * Plugin Name: Swarm Build Feed
 * Description: Displays public build updates from Swarm devlogs (JSON Feed format)
 * Version: 1.0.0
 * Author: Swarm Collective
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API endpoint for feed
 */
add_action('rest_api_init', function () {
    register_rest_route('swarm/v1', '/feed', array(
        'methods' => 'GET',
        'callback' => 'swarm_get_feed',
        'permission_callback' => '__return_true', // Public endpoint
    ));
});

/**
 * Get feed data (loads from static file or remote URL)
 */
function swarm_get_feed($request) {
    // Try to load from local file first
    $feed_path = get_template_directory() . '/../runtime/feeds/public_build_feed.json';
    
    // Fallback to remote URL if local file doesn't exist
    $feed_url = apply_filters('swarm_feed_url', 'https://weareswarm.online/wp-content/themes/runtime/feeds/public_build_feed.json');
    
    $feed_data = null;
    
    if (file_exists($feed_path)) {
        $feed_data = file_get_contents($feed_path);
    } else {
        // Try to fetch from remote URL
        $response = wp_remote_get($feed_url);
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $feed_data = wp_remote_retrieve_body($response);
        }
    }
    
    if (!$feed_data) {
        return new WP_Error('feed_not_found', 'Feed data not found', array('status' => 404));
    }
    
    $feed = json_decode($feed_data, true);
    if (!$feed) {
        return new WP_Error('feed_invalid', 'Invalid feed data', array('status' => 500));
    }
    
    return rest_ensure_response($feed);
}

/**
 * Shortcode to display feed items
 * Usage: [swarm_build_feed limit="10"]
 */
add_shortcode('swarm_build_feed', 'swarm_build_feed_shortcode');

function swarm_build_feed_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 10,
        'class' => 'swarm-build-feed',
    ), $atts);
    
    // Get feed data
    $request = new WP_REST_Request('GET', '/swarm/v1/feed');
    $response = rest_do_request($request);
    
    if ($response->is_error()) {
        return '<p>Unable to load build feed. Please try again later.</p>';
    }
    
    $feed = $response->get_data();
    $items = array_slice($feed['items'] ?? array(), 0, (int)$atts['limit']);
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr($atts['class']); ?>">
        <h2><?php echo esc_html($feed['title'] ?? 'Build Updates'); ?></h2>
        <p class="swarm-feed-description"><?php echo esc_html($feed['description'] ?? ''); ?></p>
        
        <div class="swarm-feed-items">
            <?php foreach ($items as $item): ?>
                <article class="swarm-feed-item">
                    <header class="swarm-feed-item-header">
                        <h3 class="swarm-feed-item-title">
                            <a href="<?php echo esc_url($item['url'] ?? '#'); ?>">
                                <?php echo esc_html($item['title'] ?? 'Untitled'); ?>
                            </a>
                        </h3>
                        <div class="swarm-feed-item-meta">
                            <span class="swarm-feed-item-author">
                                <?php echo esc_html($item['authors'][0]['name'] ?? 'Unknown'); ?>
                            </span>
                            <span class="swarm-feed-item-date">
                                <?php 
                                $date = $item['date_published'] ?? '';
                                if ($date) {
                                    $timestamp = strtotime($date);
                                    echo esc_html(date_i18n(get_option('date_format'), $timestamp));
                                }
                                ?>
                            </span>
                        </div>
                    </header>
                    
                    <?php if (!empty($item['summary'])): ?>
                        <div class="swarm-feed-item-summary">
                            <?php echo wp_kses_post($item['summary']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($item['content_html'])): ?>
                        <div class="swarm-feed-item-content">
                            <?php echo wp_kses_post(wpautop($item['content_html'])); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($item['tags'])): ?>
                        <div class="swarm-feed-item-tags">
                            <?php foreach ($item['tags'] as $tag): ?>
                                <span class="swarm-feed-tag">#<?php echo esc_html($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($feed['home_page_url'])): ?>
            <footer class="swarm-feed-footer">
                <a href="<?php echo esc_url($feed['home_page_url']); ?>" class="swarm-feed-link">
                    View all updates →
                </a>
            </footer>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue styles for feed display
 */
add_action('wp_enqueue_scripts', 'swarm_build_feed_styles');

function swarm_build_feed_styles() {
    if (has_shortcode(get_post()->post_content ?? '', 'swarm_build_feed')) {
        wp_add_inline_style('swarm-build-feed', '
            .swarm-build-feed {
                max-width: 800px;
                margin: 2rem auto;
                padding: 1rem;
            }
            .swarm-feed-items {
                display: grid;
                gap: 2rem;
            }
            .swarm-feed-item {
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 2rem;
            }
            .swarm-feed-item:last-child {
                border-bottom: none;
            }
            .swarm-feed-item-title a {
                color: #333;
                text-decoration: none;
            }
            .swarm-feed-item-title a:hover {
                text-decoration: underline;
            }
            .swarm-feed-item-meta {
                color: #666;
                font-size: 0.9em;
                margin-top: 0.5rem;
            }
            .swarm-feed-item-meta span {
                margin-right: 1rem;
            }
            .swarm-feed-item-summary {
                margin-top: 1rem;
                font-style: italic;
                color: #555;
            }
            .swarm-feed-item-content {
                margin-top: 1rem;
            }
            .swarm-feed-item-tags {
                margin-top: 1rem;
            }
            .swarm-feed-tag {
                display: inline-block;
                background: #f0f0f0;
                padding: 0.2rem 0.5rem;
                margin-right: 0.5rem;
                border-radius: 3px;
                font-size: 0.85em;
                color: #666;
            }
            .swarm-feed-footer {
                margin-top: 2rem;
                text-align: center;
            }
            .swarm-feed-link {
                color: #0073aa;
                text-decoration: none;
            }
            .swarm-feed-link:hover {
                text-decoration: underline;
            }
        ');
    }
}

