# DreamOS Trading Tools Audit 008

plugin: `/data/data/com.termux/files/home/projects/websites/_hostinger_build/plugins/dreamos-trading-tools`
source_review: `/data/data/com.termux/files/home/projects/websites/_hostinger_build/plugins/dreamos-trading-tools/source_review`
total_files: 19

## Files

### `source_review/FreerideinvestorWebsite/_salvage/Plugins/order_execution.py`

- suffix: `.py`
- bytes: 6215
- requires_wordpress: `False`
- contains_api_terms: `True`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/assets/js/page-smartstock-showcase.php`

- suffix: `.php`
- bytes: 2623
- requires_wordpress: `False`
- contains_api_terms: `True`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/cli-commands/stock-data-cli.php`

- suffix: `.php`
- bytes: 1711
- requires_wordpress: `False`
- contains_api_terms: `True`
- classes: `[]`
- functions: `['stt_bulk_update_stock_data']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/cron-jobs/stock-updates.php`

- suffix: `.php`
- bytes: 1819
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `[]`
- functions: `['stt_activate_cron', 'stt_deactivate_cron', 'stt_cron_update_stock_data']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `['wp', 'switch_theme', 'stt_hourly_stock_update']`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/meta-boxes/stock-symbol-meta-box.php`

- suffix: `.php`
- bytes: 1988
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `[]`
- functions: `['stt_add_custom_meta_boxes', 'stt_render_stock_symbol_meta_box', 'stt_save_stock_symbol_meta_box_data']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `['add_meta_boxes', 'save_post']`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/taxonomies/stock-category.php`

- suffix: `.php`
- bytes: 1632
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `[]`
- functions: `['simplifiedtheme_register_stock_category']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `['init']`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-fintech-engine.php`

- suffix: `.php`
- bytes: 7690
- requires_wordpress: `False`
- contains_api_terms: `True`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-smartstock-showcase.php`

- suffix: `.php`
- bytes: 3244
- requires_wordpress: `False`
- contains_api_terms: `True`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-stock-research.php`

- suffix: `.php`
- bytes: 745
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `[]`
- functions: `[]`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-activator.php`

- suffix: `.php`
- bytes: 2767
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `['defines', 'TheTradingRobotPlugPlugin_Activator']`
- functions: `['activate']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-admin.php`

- suffix: `.php`
- bytes: 4082
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `[]`
- functions: `['tradingrobotplugin_menu', 'tradingrobotplugin_settings_page', 'tradingrobotplugin_settings_init', 'tradingrobotplugin_options_sanitize', 'tradingrobotplugin_settings_section_callback', 'tradingrobotplugin_default_algorithm_render', 'tradingrobotplugin_data_refresh_interval_render', 'tradingrobotplugin_default_options']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `['admin_menu', 'admin_init', 'admin_init']`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-deactivator.php`

- suffix: `.php`
- bytes: 2409
- requires_wordpress: `False`
- contains_api_terms: `True`
- classes: `['defines', 'TheTradingRobotPlugPlugin_Deactivator']`
- functions: `['deactivate', 'cleanup_temp_data']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-runner.php`

- suffix: `.php`
- bytes: 4439
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `['that', 'that', 'TheTradingRobotPlugPlugin', 'that', 'TheTradingRobotPlugPlugin_Runner']`
- functions: `['run', 'run', 'initialize_trading_algorithms', 'enqueue_scripts_and_styles', 'register_shortcodes', 'display_trading_data', 'register_hooks', 'initialize_plugin']`
- shortcodes: `['trading_robot_data']`
- post_types: `[]`
- actions: `['init', 'wp_enqueue_scripts']`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin.php`

- suffix: `.php`
- bytes: 2294
- requires_wordpress: `True`
- contains_api_terms: `False`
- classes: `['TheTradingRobotPlugPlugin']`
- functions: `['__construct', 'load_dependencies', 'define_admin_hooks', 'define_public_hooks', 'run']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `['admin_menu', 'wp_enqueue_scripts', 'wp_enqueue_scripts']`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-activator-test.php`

- suffix: `.php`
- bytes: 2880
- requires_wordpress: `False`
- contains_api_terms: `True`
- classes: `['TheTradingRobotPlugPlugin_Activator_Test']`
- functions: `['setUp', 'test_activate_creates_table', 'test_activate_sets_default_options', 'test_activate_fails_on_low_php_version', 'test_activate_logs_error_on_table_creation_failure']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-admin-test.php`

- suffix: `.php`
- bytes: 3361
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `['TheTradingRobotPlugPlugin_Admin_Test']`
- functions: `['setUp', 'test_menu_page_added', 'test_settings_page_content', 'tradingrobotplugin_settings_page', 'test_options_sanitization', 'test_default_options_set_on_activation', 'delete_option', 'test_settings_initialization']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-deactivator-test.php`

- suffix: `.php`
- bytes: 4124
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `['TheTradingRobotPlugPlugin_Deactivator_Test']`
- functions: `['setUp', 'test_scheduled_tasks_cleared', 'test_options_deleted_on_deactivation', 'test_temp_data_cleanup', 'test_cleanup_errors_logged']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-runner-test.php`

- suffix: `.php`
- bytes: 3724
- requires_wordpress: `True`
- contains_api_terms: `True`
- classes: `['TheTradingRobotPlugPlugin_Runner_Test']`
- functions: `['setUp', 'test_plugin_run_initializes_components', 'test_initialize_trading_algorithms', 'test_enqueue_scripts_and_styles', 'test_register_shortcodes', 'test_register_hooks']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

### `source_review/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-test.php`

- suffix: `.php`
- bytes: 4071
- requires_wordpress: `True`
- contains_api_terms: `False`
- classes: `['TheTradingRobotPlugPlugin_Test', 'is']`
- functions: `['setUp', 'test_plugin_instantiation_and_dependencies', 'test_define_admin_hooks', 'test_define_public_hooks', 'test_run_loader']`
- shortcodes: `[]`
- post_types: `[]`
- actions: `[]`
- filters: `[]`

## Promotion Gate

- Promote reviewed PHP runtime files only.
- Keep Python execution/data-fetching outside Hostinger plugin runtime unless converted to safe API/static output.
- No API keys in plugin package.
