<?php
/**
 * Plugin Name: GitHub Auto Updater for SouthWest Secret
 * Plugin URI: https://github.com/yourusername/southwestsecret
 * Description: Automatically updates WordPress theme from GitHub repository when changes are pushed
 * Version: 1.0.0
 * Author: Agent-1
 * Author URI: https://southwestsecret.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class GitHub_Auto_Updater {
    private $github_repo;
    private $github_token;
    private $theme_slug;
    private $webhook_secret;
    
    public function __construct() {
        $this->theme_slug = 'southwestsecret';
        $this->github_repo = get_option('github_repo_url', '');
        $this->github_token = get_option('github_access_token', '');
        $this->webhook_secret = get_option('github_webhook_secret', '');
        
        // Add settings page
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Register webhook endpoint
        add_action('rest_api_init', array($this, 'register_webhook_endpoint'));
    }
    
    /**
     * Add settings page to WordPress admin
     */
    public function add_settings_page() {
        add_options_page(
            'GitHub Auto Updater Settings',
            'GitHub Updater',
            'manage_options',
            'github-auto-updater',
            array($this, 'settings_page_html')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('github_auto_updater', 'github_repo_url');
        register_setting('github_auto_updater', 'github_access_token');
        register_setting('github_auto_updater', 'github_webhook_secret');
        register_setting('github_auto_updater', 'github_branch', array('default' => 'main'));
    }
    
    /**
     * Settings page HTML
     */
    public function settings_page_html() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('github_auto_updater');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="github_repo_url">GitHub Repository URL</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="github_repo_url" 
                                   name="github_repo_url" 
                                   value="<?php echo esc_attr(get_option('github_repo_url')); ?>" 
                                   class="regular-text"
                                   placeholder="https://github.com/username/southwestsecret">
                            <p class="description">Full GitHub repository URL</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="github_access_token">GitHub Personal Access Token</label>
                        </th>
                        <td>
                            <input type="password" 
                                   id="github_access_token" 
                                   name="github_access_token" 
                                   value="<?php echo esc_attr(get_option('github_access_token')); ?>" 
                                   class="regular-text">
                            <p class="description">Generate at: https://github.com/settings/tokens</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="github_branch">Branch</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="github_branch" 
                                   name="github_branch" 
                                   value="<?php echo esc_attr(get_option('github_branch', 'main')); ?>" 
                                   class="regular-text">
                            <p class="description">Branch to pull from (default: main)</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="github_webhook_secret">Webhook Secret</label>
                        </th>
                        <td>
                            <input type="password" 
                                   id="github_webhook_secret" 
                                   name="github_webhook_secret" 
                                   value="<?php echo esc_attr(get_option('github_webhook_secret')); ?>" 
                                   class="regular-text">
                            <p class="description">Secret key for webhook verification</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Webhook URL</th>
                        <td>
                            <code><?php echo esc_url(rest_url('github-updater/v1/webhook')); ?></code>
                            <p class="description">Use this URL in your GitHub webhook settings</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2>Manual Update</h2>
            <p>Test the update process manually:</p>
            <button type="button" class="button button-secondary" onclick="testUpdate()">Test Update Now</button>
            <div id="update-status"></div>
            
            <script>
            function testUpdate() {
                document.getElementById('update-status').innerHTML = '<p>Updating...</p>';
                
                fetch('<?php echo esc_url(rest_url('github-updater/v1/manual-update')); ?>', {
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('update-status').innerHTML = 
                            '<p style="color: green;">✅ Update successful!</p>';
                    } else {
                        document.getElementById('update-status').innerHTML = 
                            '<p style="color: red;">❌ Update failed: ' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    document.getElementById('update-status').innerHTML = 
                        '<p style="color: red;">❌ Error: ' + error.message + '</p>';
                });
            }
            </script>
        </div>
        <?php
    }
    
    /**
     * Register REST API endpoint for GitHub webhook
     */
    public function register_webhook_endpoint() {
        register_rest_route('github-updater/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_webhook'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('github-updater/v1', '/manual-update', array(
            'methods' => 'POST',
            'callback' => array($this, 'manual_update'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
    }
    
    /**
     * Handle GitHub webhook
     */
    public function handle_webhook($request) {
        // Verify webhook signature
        if (!$this->verify_webhook_signature($request)) {
            return new WP_Error('invalid_signature', 'Invalid webhook signature', array('status' => 403));
        }
        
        $payload = $request->get_json_params();
        
        // Check if this is a push event
        if (!isset($payload['ref']) || $payload['ref'] !== 'refs/heads/' . get_option('github_branch', 'main')) {
            return new WP_REST_Response(array('message' => 'Not the target branch'), 200);
        }
        
        // Perform the update
        $result = $this->perform_update();
        
        if ($result['success']) {
            return new WP_REST_Response($result, 200);
        } else {
            return new WP_Error('update_failed', $result['message'], array('status' => 500));
        }
    }
    
    /**
     * Manual update triggered from admin
     */
    public function manual_update($request) {
        return new WP_REST_Response($this->perform_update(), 200);
    }
    
    /**
     * Verify GitHub webhook signature
     */
    private function verify_webhook_signature($request) {
        $secret = $this->webhook_secret;
        if (empty($secret)) {
            return true; // Skip verification if no secret set
        }
        
        $signature = $request->get_header('X-Hub-Signature-256');
        if (!$signature) {
            return false;
        }
        
        $payload = $request->get_body();
        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($hash, $signature);
    }
    
    /**
     * Perform the actual update from GitHub
     */
    private function perform_update() {
        $repo_url = $this->github_repo;
        $token = $this->github_token;
        $branch = get_option('github_branch', 'main');
        
        if (empty($repo_url)) {
            return array('success' => false, 'message' => 'GitHub repository URL not configured');
        }
        
        // Get theme directory
        $theme_dir = get_theme_root() . '/' . $this->theme_slug;
        
        // Extract owner and repo from URL
        preg_match('/github\.com\/([^\/]+)\/([^\/]+)/', $repo_url, $matches);
        if (count($matches) < 3) {
            return array('success' => false, 'message' => 'Invalid GitHub URL format');
        }
        
        $owner = $matches[1];
        $repo = $matches[2];
        
        // Download ZIP from GitHub
        $zip_url = "https://github.com/{$owner}/{$repo}/archive/refs/heads/{$branch}.zip";
        
        if (!empty($token)) {
            $zip_url .= "?access_token=" . $token;
        }
        
        // Download the ZIP file
        $temp_file = download_url($zip_url);
        
        if (is_wp_error($temp_file)) {
            return array('success' => false, 'message' => 'Failed to download: ' . $temp_file->get_error_message());
        }
        
        // Extract ZIP
        WP_Filesystem();
        global $wp_filesystem;
        
        $extract_result = unzip_file($temp_file, $theme_dir . '_temp');
        @unlink($temp_file);
        
        if (is_wp_error($extract_result)) {
            return array('success' => false, 'message' => 'Failed to extract: ' . $extract_result->get_error_message());
        }
        
        // Move files from extracted folder to theme folder
        $extracted_folder = $theme_dir . '_temp/' . $repo . '-' . $branch;
        
        if (is_dir($extracted_folder)) {
            $wp_filesystem->move($extracted_folder, $theme_dir . '_new', true);
            $wp_filesystem->delete($theme_dir . '_temp', true);
            
            // Backup old theme
            if (is_dir($theme_dir)) {
                $wp_filesystem->move($theme_dir, $theme_dir . '_backup', true);
            }
            
            // Move new theme into place
            $wp_filesystem->move($theme_dir . '_new', $theme_dir, true);
            
            // Clean up backup after successful update
            $wp_filesystem->delete($theme_dir . '_backup', true);
            
            return array('success' => true, 'message' => 'Theme updated successfully from GitHub');
        }
        
        return array('success' => false, 'message' => 'Extracted folder not found');
    }
}

// Initialize the plugin
new GitHub_Auto_Updater();

