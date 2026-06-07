# Cross-Repo Website Asset Inspection

generated=2026-06-07T05:52:18.707947+00:00
canonical_websites=/data/data/com.termux/files/home/projects/websites
canonical_asset_count=1500

## Summary

```json
{
  "canonical_owner": 1,
  "delete_ready_after_final_review": 2,
  "preserve_reference_only_or_ignore": 5,
  "salvage_required_or_convert_to_reference": 12,
  "salvage_small_delta_then_delete_review": 3
}
```

## Repo Decisions

| Decision | Assets | DupHash | SameName | Missing | Dirty | Repo | Reason |
|---|---:|---:|---:|---:|---:|---|---|
| canonical_owner | 1500 | 0 | 0 | 0 | 7 | `/data/data/com.termux/files/home/projects/websites` | websites owns canonical site/static assets |
| delete_ready_after_final_review | 1 | 1 | 0 | 0 | 0 | `/data/data/com.termux/files/home/projects/bible-application` | site assets appear already represented in websites by hash/name |
| delete_ready_after_final_review | 39 | 36 | 3 | 0 | 0 | `/data/data/com.termux/files/home/projects/TradingRobotPlugWeb` | site assets appear already represented in websites by hash/name |
| preserve_reference_only_or_ignore | 145 | 2 | 1 | 142 | 0 | `/data/data/com.termux/files/home/projects/AgentTools` | toolbelt |
| preserve_reference_only_or_ignore | 1500 | 15 | 13 | 1472 | 1 | `/data/data/com.termux/files/home/projects/DreamVault` | operator vault |
| preserve_reference_only_or_ignore | 649 | 0 | 0 | 649 | 0 | `/data/data/com.termux/files/home/projects/projectscanner` | toolbelt |
| preserve_reference_only_or_ignore | 5 | 0 | 0 | 5 | 0 | `/data/data/com.termux/files/home/projects/socialmediamanager` | workflow tool |
| preserve_reference_only_or_ignore | 2 | 0 | 0 | 2 | 0 | `/data/data/com.termux/files/home/projects/trade_analyzer` | trading tool |
| salvage_required_or_convert_to_reference | 3 | 0 | 0 | 3 | 1 | `/data/data/com.termux/files/home/projects/discord_teks_tester` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 12 | 0 | 1 | 11 | 36 | `/data/data/com.termux/files/home/projects/Dream.os-Core` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 12 | 0 | 1 | 11 | 36 | `/data/data/com.termux/files/home/projects/DreamOS` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 8 | 0 | 0 | 8 | 1918 | `/data/data/com.termux/files/home/projects/DreamTradeData` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 73 | 5 | 33 | 35 | 0 | `/data/data/com.termux/files/home/projects/FreeRideInvestor` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 229 | 89 | 53 | 87 | 0 | `/data/data/com.termux/files/home/projects/FreerideinvestorWebsite` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 39 | 0 | 1 | 38 | 0 | `/data/data/com.termux/files/home/projects/HomeSchool_Mastery` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 2 | 0 | 0 | 2 | 0 | `/data/data/com.termux/files/home/projects/ProfessorSama` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 2 | 0 | 0 | 2 | 0 | `/data/data/com.termux/files/home/projects/stocktwits-analyzer` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 74 | 0 | 0 | 74 | 0 | `/data/data/com.termux/files/home/projects/The-emergence-` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 14 | 0 | 0 | 14 | 2 | `/data/data/com.termux/files/home/projects/Thea` | repo contains site/static assets not proven canonical in websites |
| salvage_required_or_convert_to_reference | 3 | 0 | 0 | 3 | 8 | `/data/data/com.termux/files/home/projects/TROOP` | repo contains site/static assets not proven canonical in websites |
| salvage_small_delta_then_delete_review | 3 | 0 | 2 | 1 | 0 | `/data/data/com.termux/files/home/projects/contract-leads` | mostly represented in websites; small unmatched site delta needs review |
| salvage_small_delta_then_delete_review | 2 | 0 | 1 | 1 | 0 | `/data/data/com.termux/files/home/projects/FocusForge` | mostly represented in websites; small unmatched site delta needs review |
| salvage_small_delta_then_delete_review | 4 | 1 | 1 | 2 | 0 | `/data/data/com.termux/files/home/projects/gpt_automation` | mostly represented in websites; small unmatched site delta needs review |

## Top Assets By Repo

### AgentTools

| File | Size |
|---|---:|
| `dependencies.json` | 131445 |
| `docker-compose.yml` | 492 |
| `package-lock.json` | 161350 |
| `package.json` | 340 |
| `passdown.json` | 2741 |
| `tools_inventory.json` | 180300 |
| `tsconfig.base.json` | 359 |
| `.cursor/mcp.json` | 369 |
| `docs/TOOLBELT_SURFACE_AUDIT.json` | 153697 |
| `docs/LEGACY_TOOL_MIGRATION_MATRIX.json` | 51298 |
| `integration/claude_desktop_config.json` | 247 |
| `integration/cursor_config.json` | 247 |
| `mcp_servers/all_mcp_servers.json` | 4289 |
| `swarm_brain/knowledge_base.json` | 2673 |
| `tools/audit_config.json` | 255 |
| `tools/dadudekc_font_fix_css.php` | 2386 |
| `tools/oauth_token_checker.ts` | 1742 |
| `tools/agent_cycle_v2_status_schema.json` | 8399 |
| `tools/passdown.json` | 1653 |
| `tools/session_transition_passdown_template.json` | 2637 |
| `tools_v2/tool_registry.lock.json` | 13599 |
| `tools/discord-architect/package.json` | 550 |
| `tools/discord-architect/package-lock.json` | 12601 |
| `tools/examples/birthday_website_updates.json` | 4860 |
| `tools/examples/prismblossom_birthday_workflow.json` | 5692 |

### Dream.os-Core

| File | Size |
|---|---:|
| `manifest.json` | 4282 |
| `.workspace_state.json` | 83 |
| `03_execution/changes_2025-08-10_phase2-contract.json` | 1601 |
| `99_manifest/selection_manifest.json` | 3801 |
| `contracts/message_schema.json` | 1581 |
| `_ops/reports/tree_noise_prune_20260504_014306.json` | 99884 |
| `_ops/reports/tree_noise_prune_20260504_014333.json` | 99940 |
| `_ops/reports/finalize_tree_cleanup_20260504_014544.json` | 3932 |
| `src/core/schemas/bus_message.schema.json` | 1751 |
| `contracts/cursor_bridge/cursor_feedback_schema.json` | 1397 |
| `contracts/cursor_bridge/gpt_command_schema.json` | 1865 |
| `.github/workflows/ci.yml` | 4827 |

### DreamOS

| File | Size |
|---|---:|
| `manifest.json` | 4282 |
| `.workspace_state.json` | 83 |
| `03_execution/changes_2025-08-10_phase2-contract.json` | 1601 |
| `99_manifest/selection_manifest.json` | 3801 |
| `contracts/message_schema.json` | 1581 |
| `_ops/reports/tree_noise_prune_20260504_014306.json` | 99884 |
| `_ops/reports/tree_noise_prune_20260504_014333.json` | 99940 |
| `_ops/reports/finalize_tree_cleanup_20260504_014544.json` | 3932 |
| `src/core/schemas/bus_message.schema.json` | 1751 |
| `contracts/cursor_bridge/cursor_feedback_schema.json` | 1397 |
| `contracts/cursor_bridge/gpt_command_schema.json` | 1865 |
| `.github/workflows/ci.yml` | 4827 |

### DreamTradeData

| File | Size |
|---|---:|
| `manifests/2026-05-15.json` | 119560 |
| `manifests/2026-05-16.json` | 126273 |
| `manifests/2026-05-18.json` | 190526 |
| `manifests/2026-05-19.json` | 52429 |
| `manifests/2026-05-22.json` | 366023 |
| `manifests/2026-06-04.json` | 438907 |
| `manifests/2026-06-05.json` | 540562 |
| `manifests/2026-06-06.json` | 860868 |

### DreamVault

| File | Size |
|---|---:|
| `training_config.json` | 696 |
| `configs/ingest.yaml` | 2026 |
| `models/demo_tokenized_data.json` | 1221 |
| `models/model_metadata.json` | 700 |
| `models/training_report.json` | 623 |
| `events/20260506_194316_cleanup_complete.json` | 691 |
| `events/latest.json` | 691 |
| `events/20260506_194317_cleanup_complete.json` | 691 |
| `events/20260506_195921_cleanup_complete.json` | 691 |
| `events/20260506_195921_unit_test_event.json` | 263 |
| `events/20260506_195944_cleanup_complete.json` | 691 |
| `events/20260506_195945_unit_test_event.json` | 263 |
| `events/20260506_200044_cleanup_complete.json` | 691 |
| `events/20260506_200044_unit_test_event.json` | 263 |
| `events/20260509_194937_unit_test_event.json` | 263 |
| `events/20260509_195005_cleanup_complete.json` | 691 |
| `events/20260509_204941_unit_test_event.json` | 263 |
| `events/20260509_205002_cleanup_complete.json` | 691 |
| `schemas/event_schema.json` | 833 |
| `schemas/cpc_runtime_packet.schema.json` | 448 |
| `governance/canonical_authority_registry.yaml` | 1309 |
| `governance/task_capability_unlock.schema.json` | 818 |
| `discord_architect/package.json` | 550 |
| `discord_architect/package-lock.json` | 12601 |
| `sites/production/websites/weareswarm.online/index.html` | 8964 |

### FocusForge

| File | Size |
|---|---:|
| `config/project_config.yaml` | 1348 |
| `gui/themes/__init__.py` | 28 |

### FreeRideInvestor

| File | Size |
|---|---:|
| `Mastering TSLA Order Flow-Beyond Big Size – The 5 Advanced Signals You’re Ignoring.html` | 2688 |
| `TSLA Order Book 101-Spotting Big Size Like a Pro.html` | 3676 |
| `TSLA Trading Cheat Sheet-Reading Big Size and Making Moves.html` | 3069 |
| `Why I Let the Order Book Do the Talking on TSLA.html` | 3012 |
| `admin-tools-page.php` | 3019 |
| `category-tbow-tactic.php` | 4242 |
| `comments.php` | 1968 |
| `custom.css` | 0 |
| `footer.php` | 2698 |
| `functions.php` | 31737 |
| `header.php` | 490 |
| `home.php` | 8940 |
| `index.php` | 8699 |
| `single.php` | 674 |
| `style.css` | 715 |
| `subscribe.php` | 1446 |
| `template-tbow-tactics.php` | 3324 |
| `.project/tasks.json` | 298 |
| `css/_base.scss` | 239 |
| `css/_bootstrap.scss` | 260 |
| `css/_buttons.scss` | 877 |
| `css/_cards.scss` | 857 |
| `css/_dark.scss` | 460 |
| `css/_footer.scss` | 293 |
| `css/_functions.scss` | 186 |

### FreerideinvestorWebsite

| File | Size |
|---|---:|
| `1.html` | 28620 |
| `1.json` | 342 |
| `Plugins/db_connect.php` | 449 |
| `Plugins/fetch_trades.php` | 247 |
| `Plugins/store_trade.php` | 1174 |
| `freerideinvestor-theme/admin-tools-page.php` | 2962 |
| `freerideinvestor-theme/category-tbow-tactic.php` | 4147 |
| `freerideinvestor-theme/comments.php` | 1898 |
| `freerideinvestor-theme/custom.css` | 0 |
| `freerideinvestor-theme/footer.php` | 2698 |
| `freerideinvestor-theme/functions.php` | 47506 |
| `freerideinvestor-theme/header.php` | 470 |
| `freerideinvestor-theme/home.php` | 8940 |
| `freerideinvestor-theme/index.php` | 8511 |
| `freerideinvestor-theme/plugin-health-check.php` | 9059 |
| `freerideinvestor-theme/single.php` | 674 |
| `freerideinvestor-theme/style.css` | 686 |
| `freerideinvestor-theme/subscribe.php` | 1412 |
| `freerideinvestor-theme/template-market-news.php` | 1829 |
| `freerideinvestor-theme/template-tbow-tactics.php` | 3241 |
| `website_posts/1st_Freeride_Investor_Sample_Cheatsheet.html` | 505 |
| `website_posts/50_Script_Ideas_for_FreeRideInvestor.html` | 2699 |
| `website_posts/Analyzing_a_Tesla_Options_Trade_in_Real-Time.html` | 5348 |
| `website_posts/Analyzing_a_Tesla_Options_Trade_in_Real_Time.html` | 5348 |
| `website_posts/Backtest_Similar_Setups.html` | 5468 |

### HomeSchool_Mastery

| File | Size |
|---|---:|
| `app.html` | 49559 |
| `quiz-engine.js` | 6823 |
| `server.js` | 30119 |
| `tests/quiz-engine.test.js` | 1995 |
| `lessons_lan/plugins/teks_daily_training/plugin.json` | 462 |
| `lessons_lan/app/templates/_lesson_ai_coach.html` | 4725 |
| `lessons_lan/app/templates/admin_edit_lesson.html` | 1846 |
| `lessons_lan/app/templates/admin_feedback.html` | 1581 |
| `lessons_lan/app/templates/admin_home.html` | 1762 |
| `lessons_lan/app/templates/admin_lessons.html` | 4907 |
| `lessons_lan/app/templates/admin_user.html` | 1106 |
| `lessons_lan/app/templates/adventure.html` | 2850 |
| `lessons_lan/app/templates/base.html` | 4753 |
| `lessons_lan/app/templates/boss.html` | 2583 |
| `lessons_lan/app/templates/boss_result.html` | 2171 |
| `lessons_lan/app/templates/feedback.html` | 1522 |
| `lessons_lan/app/templates/games_hub.html` | 3856 |
| `lessons_lan/app/templates/lesson.html` | 2125 |
| `lessons_lan/app/templates/lesson_discount_dash.html` | 9719 |
| `lessons_lan/app/templates/lesson_fraction_battle.html` | 5201 |
| `lessons_lan/app/templates/lesson_games.html` | 3789 |
| `lessons_lan/app/templates/lesson_text_detective.html` | 10550 |
| `lessons_lan/app/templates/login.html` | 1200 |
| `lessons_lan/app/templates/practice.html` | 2957 |
| `lessons_lan/app/templates/practice_result.html` | 1346 |

### ProfessorSama

| File | Size |
|---|---:|
| `dashboard.html` | 39765 |
| `.github/workflows/ci.yml` | 1282 |

### TROOP

| File | Size |
|---|---:|
| `IT_HUB/Templates/mysql_flexible_server_template.json` | 3352 |
| `IT_HUB/monitoring/alert_rules.json` | 311 |
| `.github/workflows/ci-cd.yml` | 2623 |

### The-emergence-

| File | Size |
|---|---:|
| `package-lock.json` | 12814 |
| `package.json` | 2197 |
| `tests/answer_choices.test.js` | 835 |
| `tests/quiz_contract.test.js` | 1833 |
| `tests/ai/profile_flavor_reader.test.js` | 1566 |
| `tests/e2e/adaptive_discord_model.e2e.test.js` | 1355 |
| `tests/e2e/adaptive_quiz_stress_percentile.e2e.test.js` | 5079 |
| `tests/e2e/derived_question_pointer.e2e.test.js` | 754 |
| `tests/e2e/discord_session_integrity.e2e.test.js` | 1487 |
| `tests/e2e/quiz_flow.e2e.test.js` | 1534 |
| `tests/e2e/quiz_session_state_machine.e2e.test.js` | 3422 |
| `tests/e2e/quiz_view_self_heal.e2e.test.js` | 752 |
| `tests/e2e/scored_sheet_flow.e2e.test.js` | 1153 |
| `tests/e2e/stale_safe_button_quiz.e2e.test.js` | 2602 |
| `tests/scoring/adaptive_engine_full.test.js` | 2223 |
| `tests/scoring/adaptive_progress.test.js` | 1851 |
| `tests/scoring/adaptive_quiz.test.js` | 2125 |
| `tests/scoring/percentile_distribution.test.js` | 791 |
| `tests/scoring/percentile_floor.test.js` | 512 |
| `tests/scoring/scoring_engine.test.js` | 3122 |
| `tests/scoring/v5_scoring_contract.test.js` | 2242 |
| `runtime/config/server.config.yaml` | 142 |
| `runtime/tasks/spark_protocol_absorb_quizbot_into_node_001.yaml` | 579 |
| `runtime/tasks/spark_protocol_foundation_bootstrap_001.yaml` | 404 |
| `runtime/reports/deterministic_battle_runtime_001.json` | 754 |

### Thea

| File | Size |
|---|---:|
| `.pre-commit-config.yaml` | 559 |
| `docker-compose.yml` | 2829 |
| `config/agents.yaml` | 904 |
| `config/discord_config.json` | 1375 |
| `config/dream_dataset_utilization.yaml` | 7746 |
| `config/prompts.yaml` | 700 |
| `data/demo_export.json` | 555 |
| `demos/discord_integration/config/bot_config.yaml` | 1050 |
| `demos/discord_integration/config/permissions.yaml` | 1960 |
| `.github/workflows/deploy-weaponizer.yml` | 5616 |
| `.github/workflows/layout-check.yml` | 625 |
| `.github/workflows/parity-check.yml` | 800 |
| `.github/workflows/refresh_chatgpt_cookies.yml` | 1118 |
| `.github/workflows/weaponization-tests.yml` | 8242 |

### TradingRobotPlugWeb

| File | Size |
|---|---:|
| `TheTradingRobotPlugin/class-thetradingrobotplugin-activator.php` | 2767 |
| `TheTradingRobotPlugin/class-thetradingrobotplugin-admin.php` | 4082 |
| `TheTradingRobotPlugin/class-thetradingrobotplugin-deactivator.php` | 2409 |
| `TheTradingRobotPlugin/class-thetradingrobotplugin-runner.php` | 4439 |
| `TheTradingRobotPlugin/class-thetradingrobotplugin.php` | 2294 |
| `config/config.yaml` | 801 |
| `my-custom-theme/404.php` | 586 |
| `my-custom-theme/archive.php` | 887 |
| `my-custom-theme/comments.php` | 5119 |
| `my-custom-theme/content-none.php` | 706 |
| `my-custom-theme/content-page.php` | 1402 |
| `my-custom-theme/content.php` | 893 |
| `my-custom-theme/footer.php` | 847 |
| `my-custom-theme/frontpage.php` | 6545 |
| `my-custom-theme/functions.php` | 7451 |
| `my-custom-theme/header.php` | 1640 |
| `my-custom-theme/home.php` | 1276 |
| `my-custom-theme/index.php` | 2911 |
| `my-custom-theme/page.php` | 746 |
| `my-custom-theme/search.php` | 1029 |
| `my-custom-theme/sidebar.php` | 518 |
| `my-custom-theme/single.php` | 778 |
| `my-custom-theme/style-rtl.css` | 1205 |
| `my-custom-theme/style.css` | 6473 |
| `my-custom-theme/thetradingrobotplugin.php` | 1816 |

### bible-application

| File | Size |
|---|---:|
| `index.html` | 47737 |

### contract-leads

| File | Size |
|---|---:|
| `.pre-commit-config.yaml` | 201 |
| `config.yaml` | 341 |
| `.project/tasks.json` | 301 |

### discord_teks_tester

| File | Size |
|---|---:|
| `data/students.json` | 244 |
| `data/grade6_teks_ratios_rates_percents.json` | 1249 |
| `data/grade7_teks_foundation_check.json` | 833 |

### gpt_automation

| File | Size |
|---|---:|
| `dependency_cache.json` | 3044 |
| `project_analysis.json` | 2 |
| `.project/tasks.json` | 5042 |
| `.github/workflows/tests.yml` | 714 |

### projectscanner

| File | Size |
|---|---:|
| `project_analysis_projectscanner.json` | 17683 |
| `chatgpt_project_context_projectscanner.json` | 19478 |
| `.projectscanner_cache.json` | 6117 |
| `config/.pre-commit-config.yaml` | 4598 |
| `runtime/reports/project_context__root_.json` | 1554 |
| `runtime/reports/project_context_config.json` | 164 |
| `runtime/reports/project_context_docs.json` | 2046 |
| `runtime/reports/project_context_scripts.json` | 309 |
| `runtime/reports/project_context_src.json` | 7763 |
| `runtime/reports/project_context_.github.json` | 493 |
| `runtime/reports/project_context_tests.json` | 1483 |
| `runtime/reports/project_context_index.json` | 647 |
| `runtime/reports/bridge_analysis_ai_systems.json` | 1305 |
| `runtime/reports/projectscanner_json_artifact_inventory.json` | 12329 |
| `runtime/reports/projectscanner_cleanup_manifest.json` | 280801 |
| `runtime/targets/scan_targets_latest.json` | 1631 |
| `runtime/targets/local_projects_census.json` | 21337 |
| `runtime/targets/local_scan_targets_latest.json` | 36450 |
| `runtime/targets/github_repos_raw.json` | 11226 |
| `runtime/targets/github_inventory.json` | 13935 |
| `runtime/targets/github_scan_targets_latest.json` | 33469 |
| `runtime/tasks/master_project_task_list.json` | 55935 |
| `runtime/tasks/next_up.json` | 7238 |
| `runtime/tasks/projectscanner_scan_tasks.json` | 34028 |
| `runtime/tasks/master_next_up.json` | 4783 |

### socialmediamanager

| File | Size |
|---|---:|
| `stocktwits_cookies.json` | 13090 |
| `data/stocktwits_data.json` | 2 |
| `examples/dreamvault_imports/viral_marketing_tsla_trade_loser.example.draft.json` | 2803 |
| `data/drafts/dreamvault_imports/viral_marketing_20260603_051842_turning_a_winning_tsla_trade_into_a_loser.draft.json` | 2803 |
| `data/drafts/dreamvault_imports/cpc_marketing_20260603_053737_repo_fleet_self_healing_001.draft.json` | 2587 |

### stocktwits-analyzer

| File | Size |
|---|---:|
| `stocktwits_cookies.json` | 13090 |
| `data/stocktwits_data.json` | 2 |

### trade_analyzer

| File | Size |
|---|---:|
| `stock_data_output.html` | 11767 |
| `scripts/config.json` | 3506 |
