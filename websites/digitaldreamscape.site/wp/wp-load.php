<?php
/**
 * Bootstrap WordPress for Digital Dreamscape automatic systems
 */

// Define ABSPATH
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

// Load wp-config.php
if (file_exists(ABSPATH . 'wp-config.php')) {
    require_once ABSPATH . 'wp-config.php';
} else {
    // Fallback configuration
    define('WP_DEBUG', false);
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
    define('WP_CONTENT_URL', 'wp-content');
}

// Minimal WordPress core functions for promotion scripts
if (!function_exists('wp_load_translations_early')) {
    function wp_load_translations_early() {}
}

// Constants
if (!defined('OBJECT')) {
    define('OBJECT', 'OBJECT');
}
if (!defined('ARRAY_A')) {
    define('ARRAY_A', 'ARRAY_A');
}
if (!defined('ARRAY_N')) {
    define('ARRAY_N', 'ARRAY_N');
}

if (!function_exists('wp_not_installed')) {
    function wp_not_installed() {
        return false; // Assume WordPress is "installed"
    }
}

if (!function_exists('wp_installing')) {
    function wp_installing() {
        return false;
    }
}

// Database connection (minimal implementation)
if (!class_exists('wpdb')) {
    class wpdb {
        public $prefix = 'wp_';
        public $posts = 'wp_posts';
        public $postmeta = 'wp_postmeta';

        public function __construct() {
            // For file-based storage, we'll use JSON files instead of database
        }

        public function prepare($query, ...$args) {
            return $query; // Simplified
        }

        public function get_results($query, $output = OBJECT) {
            // Return empty results for now - promotion scripts will handle data
            return [];
        }

        public function insert($table, $data) {
            return 1; // Mock successful insert
        }

        public function update($table, $data, $where) {
            return 1; // Mock successful update
        }
    }

    $wpdb = new wpdb();
}

// Post functions (minimal implementation)
if (!function_exists('wp_insert_post')) {
    function wp_insert_post($postarr, $wp_error = false) {
        // For the automatic systems, we'll store posts as files
        static $post_counter = 1;

        $post_id = $post_counter++;
        $post_data = [
            'ID' => $post_id,
            'post_title' => $postarr['post_title'] ?? '',
            'post_content' => $postarr['post_content'] ?? '',
            'post_excerpt' => $postarr['post_excerpt'] ?? '',
            'post_status' => $postarr['post_status'] ?? 'publish',
            'post_type' => $postarr['post_type'] ?? 'post',
            'post_date' => current_time('mysql'),
            'post_modified' => current_time('mysql')
        ];

        // Store post data as JSON file
        $posts_dir = ABSPATH . 'wp-content/posts/';
        if (!is_dir($posts_dir)) {
            mkdir($posts_dir, 0755, true);
        }

        file_put_contents($posts_dir . "post-{$post_id}.json", json_encode($post_data, JSON_PRETTY_PRINT));

        return $post_id;
    }
}

if (!function_exists('get_post_meta')) {
    function get_post_meta($post_id, $key = '', $single = false) {
        $meta_file = ABSPATH . "wp-content/meta/post-{$post_id}-meta.json";

        if (!file_exists($meta_file)) {
            return $single ? '' : [];
        }

        $meta = json_decode(file_get_contents($meta_file), true);

        if ($key) {
            return $meta[$key] ?? ($single ? '' : []);
        }

        return $meta;
    }
}

if (!function_exists('update_post_meta')) {
    function update_post_meta($post_id, $meta_key, $meta_value) {
        $meta_file = ABSPATH . "wp-content/meta/post-{$post_id}-meta.json";

        $meta_dir = dirname($meta_file);
        if (!is_dir($meta_dir)) {
            mkdir($meta_dir, 0755, true);
        }

        $existing_meta = [];
        if (file_exists($meta_file)) {
            $existing_meta = json_decode(file_get_contents($meta_file), true) ?: [];
        }

        $existing_meta[$meta_key] = $meta_value;
        file_put_contents($meta_file, json_encode($existing_meta, JSON_PRETTY_PRINT));

        return true;
    }
}

if (!function_exists('get_posts')) {
    function get_posts($args = []) {
        $posts_dir = ABSPATH . 'wp-content/posts/';
        $posts = [];

        if (!is_dir($posts_dir)) {
            return $posts;
        }

        $files = glob($posts_dir . 'post-*.json');
        foreach ($files as $file) {
            $post_data = json_decode(file_get_contents($file), true);
            if ($post_data) {
                $posts[] = (object) $post_data;
            }
        }

        // Sort by date descending
        usort($posts, function($a, $b) {
            return strtotime($b->post_date) - strtotime($a->post_date);
        });

        // Apply limits
        $numberposts = $args['numberposts'] ?? -1;
        if ($numberposts > 0) {
            $posts = array_slice($posts, 0, $numberposts);
        }

        return $posts;
    }
}

if (!function_exists('wp_count_posts')) {
    function wp_count_posts($type = 'post', $perm = '') {
        $posts = get_posts();
        return (object) ['publish' => count($posts)];
    }
}

// Category functions
if (!function_exists('get_categories')) {
    function get_categories($args = []) {
        // Return mock categories based on questlines
        return [
            (object) ['slug' => 'technical-debt', 'name' => 'Technical Debt', 'count' => 1],
            (object) ['slug' => 'system-automation', 'name' => 'System Automation', 'count' => 1],
            (object) ['slug' => 'narrative-authority', 'name' => 'Narrative Authority', 'count' => 1],
            (object) ['slug' => 'world-building', 'name' => 'World Building', 'count' => 1],
        ];
    }
}

if (!function_exists('get_term_by')) {
    function get_term_by($field, $value, $taxonomy) {
        $categories = get_categories();
        foreach ($categories as $cat) {
            if ($cat->$field === $value) {
                return $cat;
            }
        }
        return false;
    }
}

// Time functions
if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        return date($type === 'mysql' ? 'Y-m-d H:i:s' : 'U');
    }
}

if (!function_exists('human_time_diff')) {
    function human_time_diff($from, $to = '') {
        if (empty($to)) {
            $to = time();
        }
        $diff = (int) abs($to - $from);

        if ($diff < 60) {
            return $diff . ' seconds ago';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' hours ago';
        } else {
            return floor($diff / 86400) . ' days ago';
        }
    }
}

// URL functions
if (!function_exists('home_url')) {
    function home_url($path = '') {
        $base = 'http://localhost/digitaldreamscape.site'; // Adjust as needed
        return $base . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

// Utility functions
if (!function_exists('wp_parse_args')) {
    function wp_parse_args($args, $defaults = []) {
        return array_merge($defaults, $args);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return trim(strip_tags($str));
    }
}

if (!function_exists('sanitize_title')) {
    function sanitize_title($title) {
        $title = strip_tags($title);
        $title = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        $title = preg_replace('/\s+/', '-', $title);
        return strtolower(trim($title, '-'));
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return ($thing instanceof WP_Error);
    }
}

if (!function_exists('wp_delete_post')) {
    function wp_delete_post($post_id, $force_delete = false) {
        // Remove post file
        $post_file = ABSPATH . 'wp-content/posts/post-' . $post_id . '.json';
        if (file_exists($post_file)) {
            unlink($post_file);
        }

        // Remove meta file
        $meta_file = ABSPATH . 'wp-content/meta/post-' . $post_id . '-meta.json';
        if (file_exists($meta_file)) {
            unlink($meta_file);
        }

        return true;
    }
}

if (!function_exists('get_the_title')) {
    function get_the_title($post = 0) {
        if (is_object($post)) {
            return $post->post_title ?? '';
        }
        return '';
    }
}

if (!function_exists('get_the_content')) {
    function get_the_content($post = 0) {
        if (is_object($post)) {
            return $post->post_content ?? '';
        }
        return '';
    }
}

if (!function_exists('get_the_excerpt')) {
    function get_the_excerpt($post = 0) {
        if (is_object($post)) {
            return $post->post_excerpt ?? '';
        }
        return '';
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink($post) {
        $post_id = is_object($post) ? $post->ID : $post;
        return home_url("/?p={$post_id}");
    }
}

if (!function_exists('get_the_time')) {
    function get_the_time($format = 'U', $post = null) {
        return time(); // Simplified
    }
}

if (!function_exists('get_the_date')) {
    function get_the_date($format = '', $post = null) {
        return date($format ?: 'F j, Y');
    }
}

// User functions
if (!function_exists('get_the_author_meta')) {
    function get_the_author_meta($field, $user_id = false) {
        return 'Digital Dreamscape'; // Default author
    }
}

// Error handling
if (!class_exists('WP_Error')) {
    class WP_Error {
        public $errors = [];
        public $error_data = [];

        public function __construct($code = '', $message = '', $data = '') {
            if (!empty($code)) {
                $this->errors[$code][] = $message;
                if (!empty($data)) {
                    $this->error_data[$code] = $data;
                }
            }
        }

        public function get_error_message($code = '') {
            if (empty($code)) {
                $codes = array_keys($this->errors);
                $code = $codes[0];
            }
            return isset($this->errors[$code][0]) ? $this->errors[$code][0] : '';
        }
    }
}

// Query functions
if (!class_exists('WP_Query')) {
    class WP_Query {
        public $posts = [];
        public $found_posts = 0;

        public function __construct($args = []) {
            $this->posts = get_posts($args);
            $this->found_posts = count($this->posts);
        }

        public function have_posts() {
            return !empty($this->posts);
        }

        public function the_post() {
            global $post;
            $post = array_shift($this->posts);
        }
    }
}

// Theme functions
if (!function_exists('get_template_directory')) {
    function get_template_directory() {
        return ABSPATH . 'wp-content/themes/digitaldreamscape';
    }
}

if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri() {
        return 'wp-content/themes/digitaldreamscape';
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {
        // No-op for CLI scripts
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {
        // No-op for CLI scripts
    }
}

if (!function_exists('add_action')) {
    function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
        // No-op for CLI scripts
    }
}

if (!function_exists('do_action')) {
    function do_action($tag, ...$args) {
        // No-op for CLI scripts
    }
}

// Include core functions (always needed)
$core_functions = get_template_directory() . '/core-functions.php';
if (file_exists($core_functions)) {
    require_once $core_functions;
}

// Include the theme functions
$theme_functions = get_template_directory() . '/functions.php';
if (file_exists($theme_functions)) {
    require_once $theme_functions;
}

// Bootstrap complete
define('WPINC', 'wp-includes');
if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

?>