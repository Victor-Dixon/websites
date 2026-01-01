<?php

/**
 * Admin Settings Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['fratp_save_settings']) && check_admin_referer('fratp_settings_nonce')) {
    // Save all settings
    $settings = array(
        'fratp_ma_short_length',
        'fratp_ma_long_length',
        'fratp_rsi_length',
        'fratp_rsi_overbought',
        'fratp_rsi_oversold',
        'fratp_risk_pct_equity',
        'fratp_stop_pct_price',
        'fratp_target_pct_price',
        'fratp_use_trailing_stop',
        'fratp_trail_offset_pct',
        'fratp_trail_trigger_pct',
        'fratp_stock_symbols',
        'fratp_initial_capital',
        'fratp_alpha_vantage_key',
        'fratp_finnhub_key',
        'fratp_premium_price',
        'fratp_premium_signup_page',
        'fratp_login_page',
    );

    foreach ($settings as $setting) {
        if (isset($_POST[$setting])) {
            update_option($setting, sanitize_text_field($_POST[$setting]));
        }
    }

    // Handle checkboxes
    update_option('fratp_use_trailing_stop', isset($_POST['fratp_use_trailing_stop']));
    update_option('fratp_create_tbow_posts', isset($_POST['fratp_create_tbow_posts']));

    // Handle textarea
    if (isset($_POST['fratp_premium_features'])) {
        update_option('fratp_premium_features', sanitize_textarea_field($_POST['fratp_premium_features']));
    }

    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'freeride-automated-trading-plan') . '</p></div>';
}

// Get current settings
$ma_short = get_option('fratp_ma_short_length', 50);
$ma_long = get_option('fratp_ma_long_length', 200);
$rsi_length = get_option('fratp_rsi_length', 14);
$rsi_overbought = get_option('fratp_rsi_overbought', 60);
$rsi_oversold = get_option('fratp_rsi_oversold', 40);
$risk_pct = get_option('fratp_risk_pct_equity', 0.5);
$stop_pct = get_option('fratp_stop_pct_price', 1.0);
$target_pct = get_option('fratp_target_pct_price', 15.0);
$use_trailing = get_option('fratp_use_trailing_stop', true);
$trail_offset = get_option('fratp_trail_offset_pct', 0.5);
$trail_trigger = get_option('fratp_trail_trigger_pct', 5.0);
$symbols = get_option('fratp_stock_symbols', 'TSLA');
$capital = get_option('fratp_initial_capital', 1000000);
$av_key = get_option('fratp_alpha_vantage_key', '');
$fh_key = get_option('fratp_finnhub_key', '');
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('fratp_settings_nonce'); ?>

        <h2><?php _e('Strategy Parameters', 'freeride-automated-trading-plan'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Short Moving Average', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_ma_short_length" value="<?php echo esc_attr($ma_short); ?>" min="1" required>
                    <p class="description"><?php _e('Period length for short MA (default: 50)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Long Moving Average', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_ma_long_length" value="<?php echo esc_attr($ma_long); ?>" min="1" required>
                    <p class="description"><?php _e('Period length for long MA (default: 200)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('RSI Length', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_rsi_length" value="<?php echo esc_attr($rsi_length); ?>" min="1" required>
                    <p class="description"><?php _e('RSI period length (default: 14)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('RSI Overbought', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_rsi_overbought" value="<?php echo esc_attr($rsi_overbought); ?>" min="0" max="100" required>
                    <p class="description"><?php _e('RSI overbought threshold (default: 60)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('RSI Oversold', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_rsi_oversold" value="<?php echo esc_attr($rsi_oversold); ?>" min="0" max="100" required>
                    <p class="description"><?php _e('RSI oversold threshold (default: 40)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e('Risk Management', 'freeride-automated-trading-plan'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Risk % of Equity', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_risk_pct_equity" value="<?php echo esc_attr($risk_pct); ?>" min="0.1" step="0.1" required>
                    <p class="description"><?php _e('Risk percentage per trade (default: 0.5%)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Stop Loss %', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_stop_pct_price" value="<?php echo esc_attr($stop_pct); ?>" min="0.1" step="0.1" required>
                    <p class="description"><?php _e('Stop loss percentage of price (default: 1.0%)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Profit Target %', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_target_pct_price" value="<?php echo esc_attr($target_pct); ?>" min="0.5" step="0.5" required>
                    <p class="description"><?php _e('Profit target percentage (default: 15.0%)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e('Trailing Stop', 'freeride-automated-trading-plan'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Use Trailing Stop', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="fratp_use_trailing_stop" value="1" <?php checked($use_trailing, true); ?>>
                        <?php _e('Enable trailing stop', 'freeride-automated-trading-plan'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Trail Offset %', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_trail_offset_pct" value="<?php echo esc_attr($trail_offset); ?>" min="0.1" step="0.1" required>
                    <p class="description"><?php _e('Trail offset percentage (default: 0.5%)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Trail Trigger %', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_trail_trigger_pct" value="<?php echo esc_attr($trail_trigger); ?>" min="0.5" step="0.5" required>
                    <p class="description"><?php _e('Trail trigger percentage (default: 5.0%)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e('Trading Configuration', 'freeride-automated-trading-plan'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Stock Symbols', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="text" name="fratp_stock_symbols" value="<?php echo esc_attr($symbols); ?>" required>
                    <p class="description"><?php _e('Comma-separated list of symbols (e.g., TSLA, AAPL)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Initial Capital', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="number" name="fratp_initial_capital" value="<?php echo esc_attr($capital); ?>" min="0" step="1000" required>
                    <p class="description"><?php _e('Starting capital for position sizing (default: 1,000,000)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e('TBOW Integration', 'freeride-automated-trading-plan'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Create TBOW Posts', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="fratp_create_tbow_posts" value="1" <?php checked(get_option('fratp_create_tbow_posts', false), true); ?>>
                        <?php _e('Automatically create TBOW tactic posts from trading plans', 'freeride-automated-trading-plan'); ?>
                    </label>
                    <p class="description"><?php _e('When enabled, each daily plan will be published as a TBOW tactic post', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('TBOW Category', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="text" name="fratp_tbow_category" value="<?php echo esc_attr(get_option('fratp_tbow_category', 'tbow-tactic')); ?>" required>
                    <p class="description"><?php _e('WordPress category slug for TBOW posts (default: tbow-tactic)', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
        </table>

        <h2><?php _e('API Keys', 'freeride-automated-trading-plan'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Alpha Vantage API Key', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="text" name="fratp_alpha_vantage_key" value="<?php echo esc_attr($av_key); ?>" class="regular-text">
                    <p class="description"><?php _e('Get your free API key at <a href="https://www.alphavantage.co/support/#api-key" target="_blank">alphavantage.co</a>', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Finnhub API Key', 'freeride-automated-trading-plan'); ?></th>
                <td>
                    <input type="text" name="fratp_finnhub_key" value="<?php echo esc_attr($fh_key); ?>" class="regular-text">
                    <p class="description"><?php _e('Get your free API key at <a href="https://finnhub.io/register" target="_blank">finnhub.io</a>', 'freeride-automated-trading-plan'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Save Settings', 'freeride-automated-trading-plan'), 'primary', 'fratp_save_settings'); ?>
    </form>

    <h2><?php _e('Membership & Sales Funnel', 'freeride-automated-trading-plan'); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('Premium Price', 'freeride-automated-trading-plan'); ?></th>
            <td>
                <input type="text" name="fratp_premium_price" value="<?php echo esc_attr(get_option('fratp_premium_price', '29.99')); ?>" required>
                <p class="description"><?php _e('Monthly subscription price (e.g., 29.99)', 'freeride-automated-trading-plan'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Premium Features', 'freeride-automated-trading-plan'); ?></th>
            <td>
                <textarea name="fratp_premium_features" rows="5" class="large-text"><?php echo esc_textarea(get_option('fratp_premium_features', 'Daily trading plans, Real-time signals, Risk management tools, Options strategies')); ?></textarea>
                <p class="description"><?php _e('Comma-separated list of premium features to display on signup page', 'freeride-automated-trading-plan'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Premium Signup Page', 'freeride-automated-trading-plan'); ?></th>
            <td>
                <?php
                wp_dropdown_pages(array(
                    'name' => 'fratp_premium_signup_page',
                    'selected' => get_option('fratp_premium_signup_page'),
                    'show_option_none' => __('Select a page...', 'freeride-automated-trading-plan'),
                ));
                ?>
                <p class="description"><?php _e('Page with [fratp_premium_signup] shortcode', 'freeride-automated-trading-plan'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Login Page', 'freeride-automated-trading-plan'); ?></th>
            <td>
                <?php
                wp_dropdown_pages(array(
                    'name' => 'fratp_login_page',
                    'selected' => get_option('fratp_login_page'),
                    'show_option_none' => __('Select a page...', 'freeride-automated-trading-plan'),
                ));
                ?>
                <p class="description"><?php _e('Page for user login', 'freeride-automated-trading-plan'); ?></p>
            </td>
        </tr>
    </table>

    <h2><?php _e('Automatic Daily Generation', 'freeride-automated-trading-plan'); ?></h2>
    <div class="notice notice-info">
        <p>
            <strong><?php _e('Cron Status:', 'freeride-automated-trading-plan'); ?></strong>
            <?php
            $next_run = wp_next_scheduled('fratp_daily_plan_generation');
            if ($next_run) {
                printf(
                    __('Next plan generation scheduled for: %s', 'freeride-automated-trading-plan'),
                    date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_run)
                );
            } else {
                _e('Cron job not scheduled. Plans will be generated daily at 9:30 AM EST.', 'freeride-automated-trading-plan');
            }
            ?>
        </p>
        <p>
            <?php _e('Plans are automatically generated daily for all configured stock symbols. Each plan is published as a TBOW tactic post (if enabled) and stored in the database for premium members to access.', 'freeride-automated-trading-plan'); ?>
        </p>
    </div>

    <hr>

    <h2><?php _e('Manual Plan Generation', 'freeride-automated-trading-plan'); ?></h2>
    <p><?php _e('Generate a trading plan manually for testing:', 'freeride-automated-trading-plan'); ?></p>
    <form id="fratp-manual-generate">
        <?php wp_nonce_field('fratp_nonce', 'nonce'); ?>
        <input type="text" name="symbol" value="TSLA" placeholder="Stock Symbol" required>
        <label>
            <input type="checkbox" name="create_tbow" value="1" checked>
            <?php _e('Create TBOW Post', 'freeride-automated-trading-plan'); ?>
        </label>
        <button type="submit" class="button"><?php _e('Generate Plan', 'freeride-automated-trading-plan'); ?></button>
    </form>
    <div id="fratp-generate-result" style="margin-top: 20px;"></div>
</div>

<script>
    jQuery(document).ready(function($) {
        $('#fratp-manual-generate').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $result = $('#fratp-generate-result');

            $result.html('<p>Generating plan...</p>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'fratp_generate_plan',
                    nonce: $form.find('[name="nonce"]').val(),
                    symbol: $form.find('[name="symbol"]').val(),
                    create_tbow: $form.find('[name="create_tbow"]').is(':checked') ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        $result.html('<div class="notice notice-success"><p>Plan generated successfully!</p></div>');
                    } else {
                        $result.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $result.html('<div class="notice notice-error"><p>Error generating plan.</p></div>');
                }
            });
        });
    });
</script>