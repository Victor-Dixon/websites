# Hostinger Domain Model + Plugin Stage 002

root: `/data/data/com.termux/files/home/projects/websites`
stage: `/data/data/com.termux/files/home/projects/websites/_shared_plugins`

## Domain Models

### freerideinvestor

- repo: `FreerideinvestorWebsite`
- folder: `/data/data/com.termux/files/home/projects/websites/FreerideinvestorWebsite`
- business_model: media brand, trading discipline content, email capture, education funnel
- rebuild_theme_from_scratch: `True`
- theme_reference: `/data/data/com.termux/files/home/projects/websites/FreerideinvestorWebsite/_salvage/freerideinvestor-theme`
- canonical_plugins:
  - `freerideinvestor-content-engine`
  - `dreamos-trading-tools`
  - `dreamos-productivity-widgets`

### tradingrobotplug

- repo: `TradingRobotPlugWeb`
- folder: `/data/data/com.termux/files/home/projects/websites/TradingRobotPlugWeb`
- business_model: trading tools demo, data fetcher proof, productized analytics lead capture
- rebuild_theme_from_scratch: `True`
- theme_reference: `/data/data/com.termux/files/home/projects/websites/TradingRobotPlugWeb/_salvage/my-custom-theme`
- canonical_plugins:
  - `dreamos-trading-tools`

### dadudekc

- repo: `DaDudeKC-Website`
- folder: `/data/data/com.termux/files/home/projects/websites/DaDudeKC-Website`
- business_model: personal/local brand, community features, legacy portfolio
- rebuild_theme_from_scratch: `True`
- theme_reference: `/data/data/com.termux/files/home/projects/websites/DaDudeKC-Website/themes_rebuild_reference/dadudekc`
- canonical_plugins:
  - `dadudekc-community-features`

## Plugin Buckets

### dadudekc-community-features

- path: `/data/data/com.termux/files/home/projects/websites/_shared_plugins/dadudekc-community-features`
- file_count: 2

- `_shared_plugins/dadudekc-community-features/DaDudeKC-Website/_salvage/dadudekc website/wp-content/plugins/community-features/forums.php`
- `_shared_plugins/dadudekc-community-features/DaDudeKC-Website/plugins/community-features/forums.php`

### dreamos-productivity-widgets

- path: `/data/data/com.termux/files/home/projects/websites/_shared_plugins/dreamos-productivity-widgets`
- file_count: 4

- `_shared_plugins/dreamos-productivity-widgets/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/js/checklist-dashboard.js`
- `_shared_plugins/dreamos-productivity-widgets/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/js/pomodoro.js`
- `_shared_plugins/dreamos-productivity-widgets/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-checklist-dashboard.php`
- `_shared_plugins/dreamos-productivity-widgets/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-pomodoro.php`

### dreamos-trading-tools

- path: `/data/data/com.termux/files/home/projects/websites/_shared_plugins/dreamos-trading-tools`
- file_count: 19

- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/Plugins/order_execution.py`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/assets/js/page-smartstock-showcase.php`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/cli-commands/stock-data-cli.php`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/cron-jobs/stock-updates.php`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/meta-boxes/stock-symbol-meta-box.php`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/taxonomies/stock-category.php`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-fintech-engine.php`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-smartstock-showcase.php`
- `_shared_plugins/dreamos-trading-tools/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-stock-research.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-activator.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-admin.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-deactivator.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin-runner.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/class-thetradingrobotplugin.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-activator-test.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-admin-test.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-deactivator-test.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-runner-test.php`
- `_shared_plugins/dreamos-trading-tools/TradingRobotPlugWeb/_salvage/TheTradingRobotPlugin/tests/class-thetradingrobotplugin-test.php`

### freerideinvestor-content-engine

- path: `/data/data/com.termux/files/home/projects/websites/_shared_plugins/freerideinvestor-content-engine`
- file_count: 8

- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/Auto_blogger/main.py`
- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/Auto_blogger/ui/generate_blog.py`
- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/custom-shortcodes.php`
- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/post-types/cheat-sheet.php`
- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/post-types/free-investor.php`
- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/post-types/tbow-tactics.php`
- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-archive-tools.php`
- `_shared_plugins/freerideinvestor-content-engine/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-education.php`

### review-before-promotion

- path: `/data/data/com.termux/files/home/projects/websites/_shared_plugins/review-before-promotion`
- file_count: 38

- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/admin-tools-page.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/plugin-testing.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/theme-setup.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/js/custom.js`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-community-support.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-dashboard.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-exclusive-events.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-services.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-test-fintech-plugin.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-test-template.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-thank-you.php`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/scripts/freeride-journal/app/chain_of_thought_reasoner.py`
- `_shared_plugins/review-before-promotion/FreerideinvestorWebsite/_salvage/freerideinvestor-theme/template-parts/single-cheat_sheet.php`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/API_interaction.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/Tests/test_API_interaction.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/Tests/test_alpha_vantage_fetcher.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/Tests/test_polygon_fetcher.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/Tests/test_real_time_fetcher.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/alpha_vantage_fetcher.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/data_fetcher.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/polygon_fetcher.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/DataFetchers/real_time_fetcher.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/GUI/concepts/fetcher_gui.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/GUI/data_fetch_tab.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/GUI/gui_module.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Model_Training/hyper_parameter/feature_engineering.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Model_Training/hyper_parameter/hyper_parameter_tuning.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Technical_Indicators/custom_indicators.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Technical_Indicators/trend_indicators.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Technical_Indicators/volatility_indicators.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Utilities/DataStore.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Utilities/config_handling.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Utilities/model_training_utils.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Utilities/open_ai_utils.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Utilities/tests/test_data_store.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/Utilities/tests/test_open_ai_utils.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/Scripts/organize_data.py`
- `_shared_plugins/review-before-promotion/TradingRobotPlugWeb/_salvage/config/config.yaml`

## Promotion Rules

- Themes are reference only.
- Hostinger deployables should be rebuilt as clean plugins from these buckets.
- `review-before-promotion` is not deployable.
- Python trading/data tools should become backend/API services or static-generated outputs, not raw WordPress plugin code.
