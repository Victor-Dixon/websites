<?php
/**
 * Admin Interface for Swarm Chronicle Plugin
 */

class Chronicle_Admin {

    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Swarm Chronicle',
            'Swarm Chronicle',
            'manage_options',
            'swarm-chronicle',
            array($this, 'admin_page'),
            'dashicons-groups',
            30
        );

        add_submenu_page(
            'swarm-chronicle',
            'Settings',
            'Settings',
            'manage_options',
            'swarm-chronicle-settings',
            array($this, 'settings_page')
        );
    }

    public function register_settings() {
        register_setting('swarm_chronicle_settings', 'swarm_chronicle_api_endpoint');
        register_setting('swarm_chronicle_settings', 'swarm_chronicle_api_key');
        register_setting('swarm_chronicle_settings', 'swarm_chronicle_auto_sync');
        register_setting('swarm_chronicle_settings', 'swarm_chronicle_sync_interval');
    }

    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Swarm Chronicle Dashboard', 'swarm-chronicle'); ?></h1>

            <div class="chronicle-dashboard">
                <div class="dashboard-section">
                    <h2><?php _e('System Status', 'swarm-chronicle'); ?></h2>
                    <?php $this->display_system_status(); ?>
                </div>

                <div class="dashboard-section">
                    <h2><?php _e('Recent Activity', 'swarm-chronicle'); ?></h2>
                    <?php $this->display_recent_activity(); ?>
                </div>

                <div class="dashboard-section">
                    <h2><?php _e('Quick Actions', 'swarm-chronicle'); ?></h2>
                    <?php $this->display_quick_actions(); ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Swarm Chronicle Settings', 'swarm-chronicle'); ?></h1>

            <form id="swarm-chronicle-settings-form" method="post" action="options.php">
                <?php settings_fields('swarm_chronicle_settings'); ?>
                <?php do_settings_sections('swarm_chronicle_settings'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('API Endpoint', 'swarm-chronicle'); ?></th>
                        <td>
                            <input type="url" name="swarm_chronicle_api_endpoint"
                                   value="<?php echo esc_attr(get_option('swarm_chronicle_api_endpoint')); ?>"
                                   class="regular-text" />
                            <p class="description"><?php _e('URL of the Swarm API for data synchronization', 'swarm-chronicle'); ?></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('API Key', 'swarm-chronicle'); ?></th>
                        <td>
                            <input type="password" name="swarm_chronicle_api_key"
                                   value="<?php echo esc_attr(get_option('swarm_chronicle_api_key')); ?>"
                                   class="regular-text" />
                            <p class="description"><?php _e('API key for authenticated access to Swarm data', 'swarm-chronicle'); ?></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Test Connection', 'swarm-chronicle'); ?></th>
                        <td>
                            <button type="button" id="test-api-connection" class="button button-secondary">
                                <?php _e('Test API Connection', 'swarm-chronicle'); ?>
                            </button>
                            <div id="test-connection-result" style="margin-top: 10px;"></div>
                            <p class="description"><?php _e('Test the API connection with the current settings', 'swarm-chronicle'); ?></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Auto Sync', 'swarm-chronicle'); ?></th>
                        <td>
                            <input type="checkbox" name="swarm_chronicle_auto_sync"
                                   value="1" <?php checked(1, get_option('swarm_chronicle_auto_sync'), true); ?> />
                            <label><?php _e('Enable automatic synchronization with Swarm systems', 'swarm-chronicle'); ?></label>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e('Sync Interval', 'swarm-chronicle'); ?></th>
                        <td>
                            <select name="swarm_chronicle_sync_interval">
                                <option value="3600" <?php selected(get_option('swarm_chronicle_sync_interval'), '3600'); ?>><?php _e('Every Hour', 'swarm-chronicle'); ?></option>
                                <option value="7200" <?php selected(get_option('swarm_chronicle_sync_interval'), '7200'); ?>><?php _e('Every 2 Hours', 'swarm-chronicle'); ?></option>
                                <option value="14400" <?php selected(get_option('swarm_chronicle_sync_interval'), '14400'); ?>><?php _e('Every 4 Hours', 'swarm-chronicle'); ?></option>
                                <option value="86400" <?php selected(get_option('swarm_chronicle_sync_interval'), '86400'); ?>><?php _e('Daily', 'swarm-chronicle'); ?></option>
                            </select>
                            <p class="description"><?php _e('How often to automatically sync data from Swarm systems', 'swarm-chronicle'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <div class="chronicle-manual-sync" style="margin-top: 30px;">
                <h3><?php _e('Manual Synchronization', 'swarm-chronicle'); ?></h3>
                <p><?php _e('Click the button below to manually sync data from Swarm systems.', 'swarm-chronicle'); ?></p>
                <button id="manual-sync-btn" class="button button-primary"><?php _e('Sync Now', 'swarm-chronicle'); ?></button>
                <div id="sync-result" style="margin-top: 10px;"></div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#manual-sync-btn').on('click', function() {
                var $btn = $(this);
                var $result = $('#sync-result');

                $btn.prop('disabled', true).text('<?php _e('Syncing...', 'swarm-chronicle'); ?>');
                $result.html('<p><?php _e('Synchronizing data...', 'swarm-chronicle'); ?></p>');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'swarm_sync_chronicle',
                        nonce: '<?php echo wp_create_nonce('swarm_chronicle_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $result.html('<p style="color: green;">✅ <?php _e('Synchronization completed successfully!', 'swarm-chronicle'); ?></p>');
                        } else {
                            $result.html('<p style="color: red;">❌ <?php _e('Synchronization failed:', 'swarm-chronicle'); ?> ' + response.data.error + '</p>');
                        }
                    },
                    error: function() {
                        $result.html('<p style="color: red;">❌ <?php _e('Network error occurred during synchronization.', 'swarm-chronicle'); ?></p>');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('<?php _e('Sync Now', 'swarm-chronicle'); ?>');
                    }
                });
            });
        });
        </script>
        <?php
    }

    private function display_system_status() {
        $last_sync = get_option('swarm_chronicle_last_sync', 0);
        $api_endpoint = get_option('swarm_chronicle_api_endpoint', '');
        $auto_sync = get_option('swarm_chronicle_auto_sync', false);

        ?>
        <div class="system-status-grid">
            <div class="status-item">
                <span class="status-label"><?php _e('API Connection:', 'swarm-chronicle'); ?></span>
                <span class="status-value <?php echo $api_endpoint ? 'connected' : 'disconnected'; ?>">
                    <?php echo $api_endpoint ? __('Connected', 'swarm-chronicle') : __('Not Configured', 'swarm-chronicle'); ?>
                </span>
            </div>

            <div class="status-item">
                <span class="status-label"><?php _e('Auto Sync:', 'swarm-chronicle'); ?></span>
                <span class="status-value <?php echo $auto_sync ? 'enabled' : 'disabled'; ?>">
                    <?php echo $auto_sync ? __('Enabled', 'swarm-chronicle') : __('Disabled', 'swarm-chronicle'); ?>
                </span>
            </div>

            <div class="status-item">
                <span class="status-label"><?php _e('Last Sync:', 'swarm-chronicle'); ?></span>
                <span class="status-value">
                    <?php echo $last_sync ? date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_sync) : __('Never', 'swarm-chronicle'); ?>
                </span>
            </div>

            <div class="status-item">
                <span class="status-label"><?php _e('Data Status:', 'swarm-chronicle'); ?></span>
                <span class="status-value <?php echo $this->has_data() ? 'has-data' : 'no-data'; ?>">
                    <?php echo $this->has_data() ? __('Data Available', 'swarm-chronicle') : __('No Data', 'swarm-chronicle'); ?>
                </span>
            </div>
        </div>
        <?php
    }

    private function display_recent_activity() {
        $api = new Chronicle_API();
        $data = $api->get_chronicle_data(array('limit' => 10));

        if (empty($data['entries'])) {
            echo '<p>' . __('No recent activity available.', 'swarm-chronicle') . '</p>';
            return;
        }

        echo '<div class="recent-activity-list">';
        foreach ($data['entries'] as $entry) {
            ?>
            <div class="activity-item">
                <span class="activity-icon"><?php echo esc_html($entry['icon']); ?></span>
                <span class="activity-agent"><?php echo esc_html($entry['agent']); ?></span>
                <span class="activity-date"><?php echo esc_html($entry['date']); ?></span>
                <div class="activity-content"><?php echo wp_kses_post(wp_trim_words($entry['content'], 20)); ?></div>
            </div>
            <?php
        }
        echo '</div>';
    }

    private function display_quick_actions() {
        ?>
        <div class="quick-actions">
            <a href="<?php echo admin_url('admin.php?page=swarm-chronicle-settings'); ?>" class="button">
                <?php _e('Configure Settings', 'swarm-chronicle'); ?>
            </a>
            <button id="quick-sync-btn" class="button">
                <?php _e('Quick Sync', 'swarm-chronicle'); ?>
            </button>
            <a href="<?php echo site_url('/chronicle'); ?>" class="button" target="_blank">
                <?php _e('View Public Chronicle', 'swarm-chronicle'); ?>
            </a>
        </div>
        <?php
    }

    private function has_data() {
        $master_tasks = get_option('swarm_chronicle_master_tasks', array());
        $accomplishments = get_option('swarm_chronicle_accomplishments', array());

        return !empty($master_tasks) || !empty($accomplishments);
    }
}