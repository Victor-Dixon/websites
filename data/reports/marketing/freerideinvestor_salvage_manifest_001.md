# FreeRideInvestor Salvage Manifest 001

- Status: `PASS`
- Theme root: `FreerideinvestorWebsite/_salvage/freerideinvestor-theme`
- Files scanned: `91`
- Canonical decision: `clean_rebuild_with_plugin_salvage`

## Decision Counts
- `archive`: `50`
- `archive_candidate`: `14`
- `preserve_candidate`: `25`
- `rebuild_clean`: `1`
- `replace_with_clean_funnel`: `1`

## Preserve Candidates
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/plugin-health-check.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/template-market-news.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/class-simplifiedtradingtheme-custom-widget.php` reason=`matches_trading_or_plugin_term, theme_include_or_plugin_logic`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/custom-shortcodes.php` reason=`matches_trading_or_plugin_term, theme_include_or_plugin_logic`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/plugin-testing.php` reason=`matches_trading_or_plugin_term, theme_include_or_plugin_logic`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/js/checklist-dashboard.js` reason=`frontend_asset_review, matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/js/pomodoro.js` reason=`frontend_asset_review, matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-checklist-dashboard.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-dashboard.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-interactive-charts.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-pomodoro.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-real-time-market-insights.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-smartstock-showcase.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-stock-research.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-strategy-showcase.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-test-fintech-plugin.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-trading-strategies.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/template-parts/single-trading_strategy.php` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/scripts/freeride-journal/app/chain_of_thought_reasoner.py` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/scripts/freeride-journal/app/main.py` reason=`matches_trading_or_plugin_term`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/cli-commands/stock-data-cli.php` reason=`matches_trading_or_plugin_term, theme_include_or_plugin_logic`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/cron-jobs/stock-updates.php` reason=`matches_trading_or_plugin_term, theme_include_or_plugin_logic`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/meta-boxes/stock-symbol-meta-box.php` reason=`matches_trading_or_plugin_term, theme_include_or_plugin_logic`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/inc/taxonomies/stock-category.php` reason=`matches_trading_or_plugin_term, theme_include_or_plugin_logic`
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/assets/js/page-smartstock-showcase.php` reason=`frontend_asset_review, matches_trading_or_plugin_term`

## Rebuild Clean Candidates
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-Trading-Journal.php` reason=`matches_trading_or_plugin_term, trading_journal_surface`

## Replace Candidates
- `FreerideinvestorWebsite/_salvage/freerideinvestor-theme/page-templates/page-front-page.php` reason=`old_homepage_template`

## Recommended Clean Site Shape
- Homepage: sales funnel for agent-powered trading journal.
- Product proof: TSLA behavior replay scorecard.
- Workflow core: intake → replay → scorecard → rule candidate → Discord/operator card.
- Preserve only plugins/tools that support the workflow.

## Next
1. Create clean `runtime/content/freerideinvestor.com/` site root.
2. Add funnel as `index.html` or WordPress front-page package.
3. Extract day trade planner/custom plugin candidates into a separate salvage bundle.
4. Do not continue old theme as canonical.
