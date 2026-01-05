<?php
/**
 * Plugin Name: Theme File Editor API
 * Plugin URI: https://github.com/your-repo/theme-file-editor-api
 * Description: Adds REST API endpoint for programmatic theme file editing. Enables automated deployment of theme files via REST API.
 * Version: 1.0.0
 * Author: Agent-7 (Web Development Specialist)
 * Author URI: https://github.com/your-repo
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: theme-file-editor-api
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package ThemeFileEditorAPI
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 */
class Theme_File_Editor_API {
    
    /**
     * Plugin version
     */
    const VERSION = '1.0.0';
    
    /**
     * REST API namespace
     */
    const REST_NAMESPACE = 'theme-file-editor/v1';
    
    /**
     * Initialize plugin
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route(
            self::REST_NAMESPACE,
            '/update-file',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_theme_file'),
                'permission_callback' => array($this, 'check_permissions'),
                'args' => array(
                    'theme' => array(
                        'required' => true,
                        'type' => 'string',
                        'validate_callback' => array($this, 'validate_theme'),
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'file' => array(
                        'required' => true,
                        'type' => 'string',
                        'validate_callback' => array($this, 'validate_file'),
                        'sanitize_callback' => 'sanitize_file_name',
                    ),
                    'content' => array(
                        'required' => true,
                        'type' => 'string',
                    ),
                ),
            )
        );
        
        register_rest_route(
            self::REST_NAMESPACE,
            '/get-file',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_theme_file'),
                'permission_callback' => array($this, 'check_permissions'),
                'args' => array(
                    'theme' => array(
                        'required' => true,
                        'type' => 'string',
                        'validate_callback' => array($this, 'validate_theme'),
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'file' => array(
                        'required' => true,
                        'type' => 'string',
                        'validate_callback' => array($this, 'validate_file'),
                        'sanitize_callback' => 'sanitize_file_name',
                    ),
                ),
            )
        );
    }
    
    /**
     * Check if user has permission to edit theme files
     *
     * @param WP_REST_Request $request Request object
     * @return bool|WP_Error
     */
    public function check_permissions($request) {
        // Check if user is authenticated
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_not_authenticated',
                __('You must be logged in to edit theme files.', 'theme-file-editor-api'),
                array('status' => 401)
            );
        }
        
        // Check if user has edit_theme_options capability
        if (!current_user_can('edit_theme_options')) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to edit theme files.', 'theme-file-editor-api'),
                array('status' => 403)
            );
        }
        
        // Check if file editing is allowed
        if (!defined('DISALLOW_FILE_EDIT') || DISALLOW_FILE_EDIT) {
            return new WP_Error(
                'rest_file_edit_disabled',
                __('File editing is disabled on this site.', 'theme-file-editor-api'),
                array('status' => 403)
            );
        }
        
        return true;
    }
    
    /**
     * Validate theme name
     *
     * @param string $theme Theme name
     * @param WP_REST_Request $request Request object
     * @param string $param Parameter name
     * @return bool
     */
    public function validate_theme($theme, $request, $param) {
        $theme_obj = wp_get_theme($theme);
        return $theme_obj->exists();
    }
    
    /**
     * Validate file path
     *
     * @param string $file File path
     * @param WP_REST_Request $request Request object
     * @param string $param Parameter name
     * @return bool
     */
    public function validate_file($file, $request, $param) {
        // Prevent directory traversal
        if (strpos($file, '..') !== false) {
            return false;
        }
        
        // Only allow PHP files
        $allowed_extensions = array('php', 'css', 'js', 'txt');
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        
        return in_array(strtolower($extension), $allowed_extensions);
    }
    
    /**
     * Update theme file
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function update_theme_file($request) {
        $theme = $request->get_param('theme');
        $file = $request->get_param('file');
        $content = $request->get_param('content');
        
        // Get theme directory
        $theme_obj = wp_get_theme($theme);
        $theme_dir = $theme_obj->get_stylesheet_directory();
        $file_path = $theme_dir . '/' . $file;
        
        // Security: Ensure file is within theme directory
        $real_file_path = realpath($file_path);
        $real_theme_dir = realpath($theme_dir);
        
        if (!$real_file_path || strpos($real_file_path, $real_theme_dir) !== 0) {
            return new WP_Error(
                'rest_invalid_file_path',
                __('Invalid file path.', 'theme-file-editor-api'),
                array('status' => 400)
            );
        }
        
        // Check if file exists or is writable
        if (file_exists($file_path) && !is_writable($file_path)) {
            return new WP_Error(
                'rest_file_not_writable',
                __('File is not writable.', 'theme-file-editor-api'),
                array('status' => 403)
            );
        }
        
        // Check if directory is writable
        $file_dir = dirname($file_path);
        if (!is_writable($file_dir)) {
            return new WP_Error(
                'rest_directory_not_writable',
                __('Directory is not writable.', 'theme-file-editor-api'),
                array('status' => 403)
            );
        }
        
        // Write file
        $result = file_put_contents($file_path, $content);
        
        if ($result === false) {
            return new WP_Error(
                'rest_file_write_failed',
                __('Failed to write file.', 'theme-file-editor-api'),
                array('status' => 500)
            );
        }
        
        // Clear any relevant caches
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => __('File updated successfully.', 'theme-file-editor-api'),
                'file' => $file,
                'theme' => $theme,
                'bytes_written' => $result,
            ),
            200
        );
    }
    
    /**
     * Get theme file content
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function get_theme_file($request) {
        $theme = $request->get_param('theme');
        $file = $request->get_param('file');
        
        // Get theme directory
        $theme_obj = wp_get_theme($theme);
        $theme_dir = $theme_obj->get_stylesheet_directory();
        $file_path = $theme_dir . '/' . $file;
        
        // Security: Ensure file is within theme directory
        $real_file_path = realpath($file_path);
        $real_theme_dir = realpath($theme_dir);
        
        if (!$real_file_path || strpos($real_file_path, $real_theme_dir) !== 0) {
            return new WP_Error(
                'rest_invalid_file_path',
                __('Invalid file path.', 'theme-file-editor-api'),
                array('status' => 400)
            );
        }
        
        // Check if file exists
        if (!file_exists($file_path)) {
            return new WP_Error(
                'rest_file_not_found',
                __('File not found.', 'theme-file-editor-api'),
                array('status' => 404)
            );
        }
        
        // Read file
        $content = file_get_contents($file_path);
        
        if ($content === false) {
            return new WP_Error(
                'rest_file_read_failed',
                __('Failed to read file.', 'theme-file-editor-api'),
                array('status' => 500)
            );
        }
        
        return new WP_REST_Response(
            array(
                'success' => true,
                'file' => $file,
                'theme' => $theme,
                'content' => $content,
                'size' => strlen($content),
            ),
            200
        );
    }
    
    /**
     * Display admin notices
     */
    public function admin_notices() {
        // Check if DISALLOW_FILE_EDIT is set
        if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <strong>Theme File Editor API:</strong>
                    <?php esc_html_e('File editing is disabled. This plugin will not work until DISALLOW_FILE_EDIT is removed from wp-config.php.', 'theme-file-editor-api'); ?>
                </p>
            </div>
            <?php
        }
    }
}

// Initialize plugin
new Theme_File_Editor_API();




