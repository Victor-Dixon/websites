<?php
/**
 * Content Filters and Modifications
 *
 * Filters for content processing and modifications
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add alt text to images that don't have it
 */
function digitaldreamscape_add_image_alt_text($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Pattern to match img tags without alt attribute
    $pattern = '/<img([^>]*?)\/?>/i';

    $content = preg_replace_callback($pattern, function($matches) {
        $img_tag = $matches[0];

        // Check if alt attribute already exists
        if (preg_match('/alt=["\'][^"\']*["\']/', $img_tag)) {
            return $img_tag;
        }

        // Add default alt text
        $alt_text = 'Digital Dreamscape artifact';

        // Try to extract meaningful alt from surrounding context or post title
        if (is_singular()) {
            $alt_text = get_the_title();
        }

        // Insert alt attribute before closing >
        $img_tag = preg_replace('/\/?>$/', ' alt="' . esc_attr($alt_text) . '"$0', $img_tag);

        return $img_tag;
    }, $content);

    return $content;
}
add_filter('the_content', 'digitaldreamscape_add_image_alt_text', 20);

/**
 * Add loading="lazy" to images for performance
 */
function digitaldreamscape_add_lazy_loading($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Add loading="lazy" to img tags
    $content = preg_replace('/<img([^>]*?)\/?>/i', '<img$1 loading="lazy"$2>', $content);

    return $content;
}
add_filter('the_content', 'digitaldreamscape_add_lazy_loading', 10);

/**
 * Filter excerpt length for artifacts
 */
function digitaldreamscape_excerpt_length($length) {
    if (is_archive() || is_home()) {
        return 25; // Shorter excerpts for archive pages
    }
    return $length;
}
add_filter('excerpt_length', 'digitaldreamscape_excerpt_length', 999);

/**
 * Custom excerpt more text
 */
function digitaldreamscape_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'digitaldreamscape_excerpt_more');

/**
 * Add custom classes to body
 */
function digitaldreamscape_body_classes($classes) {
    // Add theme name
    $classes[] = 'digitaldreamscape-theme';

    // Add dark mode class
    $classes[] = 'dark-theme';

    // Add page-specific classes
    if (is_page_template('page-blog.php')) {
        $classes[] = 'world-archive';
    }

    if (is_category()) {
        $classes[] = 'questline-page';
    }

    // Add filter classes for styling
    $current_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
    $current_questline = isset($_GET['questline']) ? sanitize_text_field($_GET['questline']) : '';
    $current_state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';

    if ($current_type) {
        $classes[] = 'filter-type-' . $current_type;
    }

    if ($current_questline) {
        $classes[] = 'filter-questline';
    }

    if ($current_state) {
        $classes[] = 'filter-state-' . $current_state;
    }

    return $classes;
}
add_filter('body_class', 'digitaldreamscape_body_classes');

/**
 * Modify archive queries to support artifact filters
 */
function digitaldreamscape_modify_archive_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Handle blog archive page
        if ($query->is_archive() && isset($query->query_vars['pagename']) && $query->query_vars['pagename'] === 'blog') {
            $query->set('post_type', 'post');
            $query->set('posts_per_page', 12);

            // Apply filters
            $current_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
            $current_questline = isset($_GET['questline']) ? sanitize_text_field($_GET['questline']) : '';
            $current_state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
            $current_search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

            $meta_query = array();

            if ($current_type) {
                $meta_query[] = array(
                    'key' => 'artifact_type',
                    'value' => $current_type,
                    'compare' => '='
                );
            }

            if ($current_state) {
                $meta_query[] = array(
                    'key' => 'artifact_state',
                    'value' => $current_state,
                    'compare' => '='
                );
            }

            if (!empty($meta_query)) {
                $query->set('meta_query', $meta_query);
            }

            if ($current_questline) {
                $query->set('category_name', $current_questline);
            }

            if ($current_search) {
                $query->set('s', $current_search);
            }
        }
    }
}
add_action('pre_get_posts', 'digitaldreamscape_modify_archive_query');

/**
 * Add canonical URLs for filtered pages
 */
function digitaldreamscape_canonical_url($canonical_url) {
    if (is_page_template('page-blog.php') || (is_archive() && isset($_GET['type']))) {
        $url_parts = parse_url($canonical_url);
        $query_params = array();

        // Preserve filter parameters
        $filters = array('type', 'questline', 'state', 's');
        foreach ($filters as $filter) {
            if (isset($_GET[$filter]) && !empty($_GET[$filter])) {
                $query_params[$filter] = sanitize_text_field($_GET[$filter]);
            }
        }

        if (!empty($query_params)) {
            $canonical_url = add_query_arg($query_params, $canonical_url);
        }
    }

    return $canonical_url;
}
add_filter('get_canonical_url', 'digitaldreamscape_canonical_url');

/**
 * Add Open Graph meta tags for better social sharing
 */
function digitaldreamscape_open_graph_meta() {
    if (is_page_template('page-blog.php') || is_archive()) {
        $title = 'World Archive';
        $description = 'Episodes, canon, artifacts, and unfinished quests.';
        $url = get_permalink();

        // Add filter context to title and description
        if (isset($_GET['type'])) {
            $type = sanitize_text_field($_GET['type']);
            $title = "World Archive: " . ucfirst($type);
            $description = digitaldreamscape_get_filter_description($type);
        } elseif (isset($_GET['questline'])) {
            $questline = sanitize_text_field($_GET['questline']);
            $title = "World Archive: " . get_term_by('slug', $questline, 'category')->name ?? $questline;
            $description = digitaldreamscape_get_questline_synopsis($questline);
        } elseif (isset($_GET['state'])) {
            $state = sanitize_text_field($_GET['state']);
            $title = "World Archive: " . ucfirst($state) . " Artifacts";
            $description = digitaldreamscape_get_state_description($state);
        }

        echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
        echo '<meta property="og:type" content="website" />' . "\n";
    }
}
add_action('wp_head', 'digitaldreamscape_open_graph_meta', 5);