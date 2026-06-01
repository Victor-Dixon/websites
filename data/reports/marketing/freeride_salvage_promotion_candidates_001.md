# FreeRideInvestor Salvage Promotion Candidates 001

- Status: `PASS`
- Source: `runtime/salvage/freerideinvestor.com/custom-plugin-candidates`
- Files scanned: `26`

## Decision Counts
- `archive`: `5`
- `promote_review`: `3`
- `rewrite`: `18`

## Priority Files
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/custom-shortcodes.php` decision=`rewrite` reason=`strong_workflow_relevance, priority_shortcode_logic_extract_cleanly`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-Trading-Journal.php` decision=`rewrite` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild, priority_trading_journal_surface`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/scripts/freeride-journal/app/main.py` decision=`promote_review` reason=`strong_workflow_relevance, priority_python_journal_logic`

## promote_review
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/js/pomodoro.js` reason=`strong_workflow_relevance`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/plugin-health-check.php` reason=`strong_workflow_relevance`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/scripts/freeride-journal/app/main.py` reason=`strong_workflow_relevance, priority_python_journal_logic`

## rewrite
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/assets/js/page-smartstock-showcase.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/custom-shortcodes.php` reason=`strong_workflow_relevance, priority_shortcode_logic_extract_cleanly`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/meta-boxes/stock-symbol-meta-box.php` reason=`wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/plugin-testing.php` reason=`risk_terms_present, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/js/checklist-dashboard.js` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-Trading-Journal.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild, priority_trading_journal_surface`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-checklist-dashboard.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-dashboard.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-interactive-charts.php` reason=`wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-pomodoro.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-real-time-market-insights.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-smartstock-showcase.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-stock-research.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-strategy-showcase.php` reason=`wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-trading-strategies.php` reason=`strong_workflow_relevance, wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/scripts/freeride-journal/app/chain_of_thought_reasoner.py` reason=`risk_terms_present`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/template-market-news.php` reason=`wordpress_coupled_needs_clean_rebuild`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/template-parts/single-trading_strategy.php` reason=`wordpress_coupled_needs_clean_rebuild`

## archive
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/class-simplifiedtradingtheme-custom-widget.php` reason=`low_signal_archive_review`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/cli-commands/stock-data-cli.php` reason=`low_signal_archive_review`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/cron-jobs/stock-updates.php` reason=`low_signal_archive_review`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/inc/taxonomies/stock-category.php` reason=`low_signal_archive_review`
- `runtime/salvage/freerideinvestor.com/custom-plugin-candidates/page-templates/page-test-fintech-plugin.php` reason=`wordpress_coupled_needs_clean_rebuild, test_or_demo_artifact`

## discard

## Next
- Rewrite the Trading Journal page into the clean FreeRideInvestor workflow.
- Extract reusable Python journal logic if it is not dead/demo code.
- Extract shortcodes as clean components only if they support the new funnel/workflow.
