<?php
/**
 * Plugin Name: Dadu Dek C Portfolio Plugin
 * Plugin URI: https://dadudekc.com
 * Description: Portfolio showcase plugin for automation systems and development services
 * Version: 1.0.0
 * Author: Dadu Dek C
 * License: GPL v2 or later
 * Text Domain: dadudekc-portfolio
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DaduDekCPortfolioPlugin {

    public function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('init', array($this, 'register_portfolio_post_type'));
        add_action('init', array($this, 'register_portfolio_taxonomies'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('add_meta_boxes', array($this, 'add_portfolio_meta_boxes'));
        add_action('save_post', array($this, 'save_portfolio_meta'));
        add_filter('the_content', array($this, 'add_portfolio_content_filter'));
        add_shortcode('dadudekc_portfolio', array($this, 'portfolio_shortcode'));
    }

    public function register_portfolio_post_type() {
        $labels = array(
            'name'                  => _x('Projects', 'Post type general name', 'dadudekc-portfolio'),
            'singular_name'         => _x('Project', 'Post type singular name', 'dadudekc-portfolio'),
            'menu_name'            => _x('Projects', 'Admin Menu text', 'dadudekc-portfolio'),
            'name_admin_bar'       => _x('Project', 'Add New on Toolbar', 'dadudekc-portfolio'),
            'add_new'              => __('Add New', 'dadudekc-portfolio'),
            'add_new_item'         => __('Add New Project', 'dadudekc-portfolio'),
            'new_item'             => __('New Project', 'dadudekc-portfolio'),
            'edit_item'            => __('Edit Project', 'dadudekc-portfolio'),
            'view_item'            => __('View Project', 'dadudekc-portfolio'),
            'all_items'            => __('All Projects', 'dadudekc-portfolio'),
            'search_items'         => __('Search Projects', 'dadudekc-portfolio'),
            'parent_item_colon'    => __('Parent Project:', 'dadudekc-portfolio'),
            'not_found'            => __('No projects found.', 'dadudekc-portfolio'),
            'not_found_in_trash'   => __('No projects found in Trash.', 'dadudekc-portfolio'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'projects'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest'       => true,
        );

        register_post_type('project', $args);
    }

    public function register_portfolio_taxonomies() {
        // Service Type Taxonomy
        $service_labels = array(
            'name'              => _x('Service Types', 'taxonomy general name', 'dadudekc-portfolio'),
            'singular_name'     => _x('Service Type', 'taxonomy singular name', 'dadudekc-portfolio'),
            'search_items'      => __('Search Service Types', 'dadudekc-portfolio'),
            'all_items'         => __('All Service Types', 'dadudekc-portfolio'),
            'parent_item'       => __('Parent Service Type', 'dadudekc-portfolio'),
            'parent_item_colon' => __('Parent Service Type:', 'dadudekc-portfolio'),
            'edit_item'         => __('Edit Service Type', 'dadudekc-portfolio'),
            'update_item'       => __('Update Service Type', 'dadudekc-portfolio'),
            'add_new_item'      => __('Add New Service Type', 'dadudekc-portfolio'),
            'new_item_name'     => __('New Service Type Name', 'dadudekc-portfolio'),
            'menu_name'         => __('Service Types', 'dadudekc-portfolio'),
        );

        $service_args = array(
            'hierarchical'      => true,
            'labels'            => $service_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'service-type'),
            'show_in_rest'      => true,
        );

        register_taxonomy('service_type', array('project'), $service_args);

        // Technology Taxonomy
        $tech_labels = array(
            'name'              => _x('Technologies', 'taxonomy general name', 'dadudekc-portfolio'),
            'singular_name'     => _x('Technology', 'taxonomy singular name', 'dadudekc-portfolio'),
            'search_items'      => __('Search Technologies', 'dadudekc-portfolio'),
            'all_items'         => __('All Technologies', 'dadudekc-portfolio'),
            'parent_item'       => __('Parent Technology', 'dadudekc-portfolio'),
            'parent_item_colon' => __('Parent Technology:', 'dadudekc-portfolio'),
            'edit_item'         => __('Edit Technology', 'dadudekc-portfolio'),
            'update_item'       => __('Update Technology', 'dadudekc-portfolio'),
            'add_new_item'      => __('Add New Technology', 'dadudekc-portfolio'),
            'new_item_name'     => __('New Technology Name', 'dadudekc-portfolio'),
            'menu_name'         => __('Technologies', 'dadudekc-portfolio'),
        );

        $tech_args = array(
            'hierarchical'      => false,
            'labels'            => $tech_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'technology'),
            'show_in_rest'      => true,
        );

        register_taxonomy('technology', array('project'), $tech_args);
    }

    public function enqueue_scripts() {
        if (is_singular('project') || is_post_type_archive('project')) {
            wp_enqueue_style('dadudekc-portfolio-css', plugin_dir_url(__FILE__) . 'css/portfolio.css', array(), '1.0.0');
            wp_enqueue_script('dadudekc-portfolio-js', plugin_dir_url(__FILE__) . 'js/portfolio.js', array('jquery'), '1.0.0', true);
        }
    }

    public function add_portfolio_meta_boxes() {
        add_meta_box(
            'portfolio_details',
            __('Project Details', 'dadudekc-portfolio'),
            array($this, 'portfolio_meta_box_callback'),
            'project',
            'normal',
            'high'
        );

        add_meta_box(
            'portfolio_metrics',
            __('Project Metrics', 'dadudekc-portfolio'),
            array($this, 'portfolio_metrics_meta_box_callback'),
            'project',
            'normal',
            'high'
        );
    }

    public function portfolio_meta_box_callback($post) {
        wp_nonce_field('portfolio_details_nonce', 'portfolio_details_nonce');

        $one_line_summary = get_post_meta($post->ID, '_project_one_line_summary', true);
        $core_purpose = get_post_meta($post->ID, '_project_core_purpose', true);
        $value_impact = get_post_meta($post->ID, '_project_value_impact', true);
        $tech_stack = get_post_meta($post->ID, '_project_tech_stack', true);
        $project_status = get_post_meta($post->ID, '_project_status', true);
        $unique_angle = get_post_meta($post->ID, '_project_unique_angle', true);
        $next_steps = get_post_meta($post->ID, '_project_next_steps', true);
        $repo_link = get_post_meta($post->ID, '_project_repo_link', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="project_one_line_summary"><?php _e('One-line Summary', 'dadudekc-portfolio'); ?></label></th>
                <td><input type="text" id="project_one_line_summary" name="project_one_line_summary" value="<?php echo esc_attr($one_line_summary); ?>" class="regular-text" placeholder="Brief project description"></td>
            </tr>
            <tr>
                <th><label for="project_core_purpose"><?php _e('Core Purpose', 'dadudekc-portfolio'); ?></label></th>
                <td><textarea id="project_core_purpose" name="project_core_purpose" rows="3" class="large-text"><?php echo esc_textarea($core_purpose); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="project_value_impact"><?php _e('Value / Impact', 'dadudekc-portfolio'); ?></label></th>
                <td><textarea id="project_value_impact" name="project_value_impact" rows="3" class="large-text"><?php echo esc_textarea($value_impact); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="project_tech_stack"><?php _e('Tech Stack', 'dadudekc-portfolio'); ?></label></th>
                <td><input type="text" id="project_tech_stack" name="project_tech_stack" value="<?php echo esc_attr($tech_stack); ?>" class="regular-text" placeholder="Python, PyQt5, AST, Tree-sitter, JSON"></td>
            </tr>
            <tr>
                <th><label for="project_status"><?php _e('Status', 'dadudekc-portfolio'); ?></label></th>
                <td>
                    <select id="project_status" name="project_status">
                        <option value="mvp" <?php selected($project_status, 'mvp'); ?>><?php _e('MVP', 'dadudekc-portfolio'); ?></option>
                        <option value="active" <?php selected($project_status, 'active'); ?>><?php _e('Active', 'dadudekc-portfolio'); ?></option>
                        <option value="archived" <?php selected($project_status, 'archived'); ?>><?php _e('Archived', 'dadudekc-portfolio'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="project_unique_angle"><?php _e('What Makes It Interesting', 'dadudekc-portfolio'); ?></label></th>
                <td><textarea id="project_unique_angle" name="project_unique_angle" rows="3" class="large-text"><?php echo esc_textarea($unique_angle); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="project_next_steps"><?php _e('Next Steps', 'dadudekc-portfolio'); ?></label></th>
                <td><textarea id="project_next_steps" name="project_next_steps" rows="3" class="large-text"><?php echo esc_textarea($next_steps); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="project_repo_link"><?php _e('Repository Link', 'dadudekc-portfolio'); ?></label></th>
                <td><input type="url" id="project_repo_link" name="project_repo_link" value="<?php echo esc_attr($repo_link); ?>" class="regular-text" placeholder="https://github.com/username/repo"></td>
            </tr>
        </table>
        <?php
    }

    public function portfolio_metrics_meta_box_callback($post) {
        wp_nonce_field('portfolio_metrics_nonce', 'portfolio_metrics_nonce');

        $time_saved = get_post_meta($post->ID, '_portfolio_time_saved', true);
        $efficiency_gain = get_post_meta($post->ID, '_portfolio_efficiency_gain', true);
        $cost_savings = get_post_meta($post->ID, '_portfolio_cost_savings', true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="portfolio_time_saved"><?php _e('Time Saved (hours/week)', 'dadudekc-portfolio'); ?></label></th>
                <td><input type="number" id="portfolio_time_saved" name="portfolio_time_saved" value="<?php echo esc_attr($time_saved); ?>" min="0" step="0.5"></td>
            </tr>
            <tr>
                <th><label for="portfolio_efficiency_gain"><?php _e('Efficiency Gain (%)', 'dadudekc-portfolio'); ?></label></th>
                <td><input type="number" id="portfolio_efficiency_gain" name="portfolio_efficiency_gain" value="<?php echo esc_attr($efficiency_gain); ?>" min="0" max="100" step="1"></td>
            </tr>
            <tr>
                <th><label for="portfolio_cost_savings"><?php _e('Cost Savings ($)', 'dadudekc-portfolio'); ?></label></th>
                <td><input type="number" id="portfolio_cost_savings" name="portfolio_cost_savings" value="<?php echo esc_attr($cost_savings); ?>" min="0" step="100"></td>
            </tr>
        </table>
        <?php
    }

    public function save_portfolio_meta($post_id) {
        if (!isset($_POST['portfolio_details_nonce']) || !wp_verify_nonce($_POST['portfolio_details_nonce'], 'portfolio_details_nonce')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save new project fields
        if (isset($_POST['project_one_line_summary'])) {
            update_post_meta($post_id, '_project_one_line_summary', sanitize_text_field($_POST['project_one_line_summary']));
        }

        if (isset($_POST['project_core_purpose'])) {
            update_post_meta($post_id, '_project_core_purpose', sanitize_textarea_field($_POST['project_core_purpose']));
        }

        if (isset($_POST['project_value_impact'])) {
            update_post_meta($post_id, '_project_value_impact', sanitize_textarea_field($_POST['project_value_impact']));
        }

        if (isset($_POST['project_tech_stack'])) {
            update_post_meta($post_id, '_project_tech_stack', sanitize_text_field($_POST['project_tech_stack']));
        }

        if (isset($_POST['project_status'])) {
            update_post_meta($post_id, '_project_status', sanitize_text_field($_POST['project_status']));
        }

        if (isset($_POST['project_unique_angle'])) {
            update_post_meta($post_id, '_project_unique_angle', sanitize_textarea_field($_POST['project_unique_angle']));
        }

        if (isset($_POST['project_next_steps'])) {
            update_post_meta($post_id, '_project_next_steps', sanitize_textarea_field($_POST['project_next_steps']));
        }

        if (isset($_POST['project_repo_link'])) {
            update_post_meta($post_id, '_project_repo_link', esc_url_raw($_POST['project_repo_link']));
        }

        // Save metrics (keeping for backward compatibility)
        if (!isset($_POST['portfolio_metrics_nonce']) || !wp_verify_nonce($_POST['portfolio_metrics_nonce'], 'portfolio_metrics_nonce')) {
            return;
        }

        if (isset($_POST['portfolio_time_saved'])) {
            update_post_meta($post_id, '_portfolio_time_saved', floatval($_POST['portfolio_time_saved']));
        }

        if (isset($_POST['portfolio_efficiency_gain'])) {
            update_post_meta($post_id, '_portfolio_efficiency_gain', intval($_POST['portfolio_efficiency_gain']));
        }

        if (isset($_POST['portfolio_cost_savings'])) {
            update_post_meta($post_id, '_portfolio_cost_savings', floatval($_POST['portfolio_cost_savings']));
        }
    }

    public function add_portfolio_content_filter($content) {
        if (is_singular('project')) {
            global $post;

            $client_name = get_post_meta($post->ID, '_portfolio_client_name', true);
            $project_url = get_post_meta($post->ID, '_portfolio_project_url', true);
            $time_saved = get_post_meta($post->ID, '_portfolio_time_saved', true);
            $efficiency_gain = get_post_meta($post->ID, '_portfolio_efficiency_gain', true);

            $portfolio_info = '';

            if ($client_name || $project_url) {
                $portfolio_info .= '<div class="portfolio-meta bg-gray-50 p-4 rounded-lg mb-6">';
                $portfolio_info .= '<h3 class="text-lg font-semibold mb-3">Project Details</h3>';

                if ($client_name) {
                    $portfolio_info .= '<p><strong>Client:</strong> ' . esc_html($client_name) . '</p>';
                }

                if ($project_url) {
                    $portfolio_info .= '<p><strong>Live Site:</strong> <a href="' . esc_url($project_url) . '" target="_blank" class="text-blue-600 hover:text-blue-800">' . esc_html($project_url) . '</a></p>';
                }

                if ($time_saved) {
                    $portfolio_info .= '<p><strong>Time Saved:</strong> ' . esc_html($time_saved) . ' hours/week</p>';
                }

                if ($efficiency_gain) {
                    $portfolio_info .= '<p><strong>Efficiency Gain:</strong> ' . esc_html($efficiency_gain) . '%</p>';
                }

                $portfolio_info .= '</div>';
            }

            $content = $portfolio_info . $content;
        }

        return $content;
    }

    public function portfolio_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 6,
            'columns' => 3,
            'show_filters' => 'true'
        ), $atts);

        $args = array(
            'post_type' => 'project',
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish'
        );

        $portfolio_query = new WP_Query($args);

        if (!$portfolio_query->have_posts()) {
            return '<p>No portfolio items found.</p>';
        }

        $output = '<div class="dadudekc-portfolio-grid grid grid-cols-1 md:grid-cols-' . esc_attr($atts['columns']) . ' gap-6">';

        while ($portfolio_query->have_posts()) {
            $portfolio_query->the_post();

            $time_saved = get_post_meta(get_the_ID(), '_portfolio_time_saved', true);
            $efficiency_gain = get_post_meta(get_the_ID(), '_portfolio_efficiency_gain', true);

            $output .= '<div class="portfolio-item bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">';
            if (has_post_thumbnail()) {
                $output .= '<div class="portfolio-thumbnail">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</div>';
            }
            $output .= '<div class="p-6">';
            $output .= '<h3 class="text-xl font-semibold mb-2"><a href="' . get_permalink() . '" class="text-gray-900 hover:text-blue-600">' . get_the_title() . '</a></h3>';
            $output .= '<div class="text-gray-600 text-sm mb-3">' . get_the_excerpt() . '</div>';

            if ($time_saved || $efficiency_gain) {
                $output .= '<div class="portfolio-metrics flex flex-wrap gap-2 mb-3">';
                if ($time_saved) {
                    $output .= '<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">' . esc_html($time_saved) . 'h saved/week</span>';
                }
                if ($efficiency_gain) {
                    $output .= '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">+' . esc_html($efficiency_gain) . '% efficiency</span>';
                }
                $output .= '</div>';
            }

            $output .= '<a href="' . get_permalink() . '" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">View Project</a>';
            $output .= '</div></div>';
        }

        $output .= '</div>';

        wp_reset_postdata();

        return $output;
    }
}

// Initialize the plugin
new DaduDekCPortfolioPlugin();

// Activation hook
register_activation_hook(__FILE__, 'dadudekc_portfolio_activate');
function dadudekc_portfolio_activate() {
    // Flush rewrite rules for custom post type
    flush_rewrite_rules();

    // Create Project Scanner as first project
    $project_scanner_id = wp_insert_post(array(
        'post_title' => 'Project Scanner',
        'post_content' => 'A Python tool that scans codebases and transforms repositories into structured JSON data with a PyQt5 GUI interface.',
        'post_status' => 'publish',
        'post_type' => 'project',
        'post_name' => 'project-scanner'
    ));

    if ($project_scanner_id) {
        update_post_meta($project_scanner_id, '_project_one_line_summary', 'A Python tool that scans codebases and transforms repositories into structured JSON data with a PyQt5 GUI interface.');
        update_post_meta($project_scanner_id, '_project_core_purpose', 'Transform codebases into structured, queryable JSON representations. Enables rapid codebase analysis, dependency mapping, and architectural understanding without manual inspection.');
        update_post_meta($project_scanner_id, '_project_value_impact', 'Eliminates hours of manual codebase exploration. Provides instant visibility into project structure, dependencies, and patterns. Essential for onboarding, audits, and technical due diligence.');
        update_post_meta($project_scanner_id, '_project_tech_stack', 'Python, PyQt5, AST, Tree-sitter, JSON');
        update_post_meta($project_scanner_id, '_project_status', 'mvp');
        update_post_meta($project_scanner_id, '_project_unique_angle', 'Repo → structured JSON → GUI. No manual parsing. AST-based analysis with tree-sitter for multi-language support. GUI makes it accessible to non-technical stakeholders.');
        update_post_meta($project_scanner_id, '_project_next_steps', 'Add support for additional languages, implement diff analysis between versions, export to multiple formats (Markdown, HTML reports).');
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'dadudekc_portfolio_deactivate');
function dadudekc_portfolio_deactivate() {
    flush_rewrite_rules();
}