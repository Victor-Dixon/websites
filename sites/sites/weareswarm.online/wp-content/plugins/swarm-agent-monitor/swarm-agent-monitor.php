<?php
/**
 * Plugin Name: Swarm Agent Monitor
 * Description: Displays real-time agent activity and devlogs from the swarm system
 * Version: 1.0.0
 * Author: Swarm System
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SwarmAgentMonitor {

    public function __construct() {
        add_action('init', array($this, 'init'));
        add_shortcode('swarm_agent_activity', array($this, 'render_agent_activity'));
    }

    public function init() {
        // Register custom post type for agent activity
        register_post_type('agent_activity', array(
            'labels' => array(
                'name' => 'Agent Activities',
                'singular_name' => 'Agent Activity',
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
        ));

        // Schedule cron job to sync agent data
        if (!wp_next_scheduled('sync_agent_data')) {
            wp_schedule_event(time(), 'hourly', 'sync_agent_data');
        }
        add_action('sync_agent_data', array($this, 'sync_agent_data'));
    }

    public function sync_agent_data() {
        // Path to agent activity data (would be configured per installation)
        $agent_data_path = '/path/to/agent_cellphone_v2_repository/website_data/agent_activity/';

        if (!is_dir($agent_data_path)) {
            return;
        }

        // Scan for agent activity files
        $files = glob($agent_data_path . '*_latest_devlog.json');

        foreach ($files as $file) {
            $this->process_agent_devlog($file);
        }
    }

    private function process_agent_devlog($json_file) {
        $data = json_decode(file_get_contents($json_file), true);
        if (!$data) return;

        $agent_id = $data['agent_id'];
        $content = $data['content'];
        $timestamp = strtotime($data['timestamp']);

        // Create or update WordPress post
        $post_title = sprintf('%s Activity - %s',
            $agent_id,
            date('M j, Y H:i', $timestamp)
        );

        $post_data = array(
            'post_title' => $post_title,
            'post_content' => wp_kses_post($this->format_devlog_content($content)),
            'post_type' => 'agent_activity',
            'post_status' => 'publish',
            'post_date' => date('Y-m-d H:i:s', $timestamp),
            'meta_input' => array(
                'agent_id' => $agent_id,
                'word_count' => $data['word_count'],
                'line_count' => $data['line_count'],
                'activity_timestamp' => $data['timestamp']
            )
        );

        // Check if post already exists
        $existing_posts = get_posts(array(
            'post_type' => 'agent_activity',
            'meta_key' => 'activity_timestamp',
            'meta_value' => $data['timestamp'],
            'posts_per_page' => 1
        ));

        if (empty($existing_posts)) {
            wp_insert_post($post_data);
        }
    }

    private function format_devlog_content($content) {
        // Convert markdown-style formatting to HTML
        $content = str_replace('# ', '<h2>', $content);
        $content = str_replace('## ', '<h3>', $content);
        $content = str_replace('### ', '<h4>', $content);
        $content = preg_replace('/\n- (.+)/', "\n<li>$1</li>", $content);
        $content = str_replace("\n\n", "</p><p>", $content);

        return '<div class="agent-devlog"><p>' . $content . '</p></div>';
    }

    public function render_agent_activity($atts) {
        ob_start();

        $atts = shortcode_atts(array(
            'agent' => '',
            'limit' => 10,
        ), $atts);

        $args = array(
            'post_type' => 'agent_activity',
            'posts_per_page' => $atts['limit'],
            'orderby' => 'date',
            'order' => 'DESC'
        );

        if (!empty($atts['agent'])) {
            $args['meta_key'] = 'agent_id';
            $args['meta_value'] = $atts['agent'];
        }

        $activities = new WP_Query($args);

        if ($activities->have_posts()) {
            echo '<div class="swarm-agent-activities">';
            echo '<h3>🟢 Swarm Agent Activity Monitor</h3>';

            while ($activities->have_posts()) {
                $activities->the_post();
                $agent_id = get_post_meta(get_the_ID(), 'agent_id', true);
                $word_count = get_post_meta(get_the_ID(), 'word_count', true);

                echo '<div class="agent-activity-item">';
                echo '<h4>' . esc_html(get_the_title()) . '</h4>';
                echo '<div class="agent-meta">';
                echo '<span class="agent-id">🤖 ' . esc_html($agent_id) . '</span>';
                echo '<span class="word-count">📝 ' . intval($word_count) . ' words</span>';
                echo '<span class="timestamp">🕒 ' . get_the_date('M j, Y H:i') . '</span>';
                echo '</div>';
                echo '<div class="activity-content">';
                the_content();
                echo '</div>';
                echo '</div>';
            }

            echo '</div>';
        } else {
            echo '<div class="swarm-agent-activities">';
            echo '<p>🔄 No recent agent activity to display.</p>';
            echo '<p><em>Agent activities will appear here as the swarm system posts devlogs.</em></p>';
            echo '</div>';
        }

        wp_reset_postdata();
        return ob_get_clean();
    }
}

// Initialize the plugin
new SwarmAgentMonitor();

// Add some basic CSS
add_action('wp_head', function() {
    ?>
    <style>
        .swarm-agent-activities { font-family: 'Courier New', monospace; max-width: 800px; margin: 20px 0; }
        .agent-activity-item { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .agent-activity-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .agent-meta { display: flex; gap: 15px; font-size: 0.9em; color: #666; margin-bottom: 10px; }
        .activity-content { line-height: 1.6; }
        .activity-content h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
        .activity-content h3 { color: #34495e; }
        .activity-content li { margin-left: 20px; }
    </style>
    <?php
});
?>