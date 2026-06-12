# Spark/Emergence Artifact Hygiene Manifest

generated: 2026-06-02T06:57:45-05:00

## Policy

- Classification only.
- No deletes.
- No moves.
- Salvage before pruning.
- Commit only this manifest/task/status capture unless explicitly promoted.

## Counts

- untracked_total: 103
- untracked_spark: 47
- untracked_emergence: 38
- untracked_tmp_or_build: 13

## Keep Candidates

Likely useful proof/report artifacts that document battle sim or Emergence behavior.

- `_reports/emergence_battle_story_cinematics_106.txt`
- `_reports/emergence_character_battle_handoff_098.txt`
- `_reports/emergence_character_battle_handoff_098b.txt`
- `_reports/emergence_character_battle_handoff_098c.txt`
- `_reports/emergence_character_battle_handoff_098d.txt`
- `_reports/emergence_character_generator_render_072.md.html`
- `_reports/emergence_character_profile_display_089.txt`
- `_reports/emergence_character_sheet_output_081.md.domain.json`
- `_reports/emergence_character_sheet_output_081.md.flavor.json`
- `_reports/emergence_character_sheet_output_081.md.html`
- `_reports/emergence_conversion_funnel_report_114.txt`
- `_reports/emergence_conversion_funnel_report_114b.txt`
- `_reports/emergence_domain_pass_behavior_078.md.fixture.json`
- `_reports/emergence_flavor_privacy_unlock_routing_088.txt`
- `_reports/emergence_flavor_privacy_unlock_routing_088b.txt`
- `_reports/emergence_full_handoff_browser_smoke_100.txt`
- `_reports/emergence_hostinger_image_env_095.txt`
- `_reports/emergence_hostinger_image_env_095c.txt`
- `_reports/emergence_hostinger_image_env_095d.txt`
- `_reports/emergence_portrait_prompt_quality_fixtures_108.txt`
- `_reports/emergence_premium_hero_image_provider_093.txt`
- `_reports/emergence_premium_hero_image_provider_093b.txt`
- `_reports/emergence_premium_portrait_design_controls_107.txt`
- `_reports/emergence_premium_portrait_design_controls_107b.txt`
- `_reports/emergence_premium_portrait_design_controls_107c.txt`
- `_reports/emergence_premium_portrait_design_controls_107d.txt`
- `_reports/emergence_scan_no_reload_browser_smoke_112.txt`
- `_reports/emergence_scan_no_reload_browser_smoke_112b.txt`
- `_reports/emergence_scan_submit_state_reset_110.txt`
- `_reports/spark_battle_commit_show_023.txt`
- `_reports/spark_battle_engine_repository_seam_013.txt`
- `_reports/spark_battle_engine_smoke_failure_014.err`
- `_reports/spark_battle_engine_smoke_failure_014.md`
- `_reports/spark_battle_engine_smoke_failure_014.txt`
- `_reports/spark_battle_php_files_011.txt`
- `_reports/spark_battle_repository_payload_014.txt`
- `_reports/spark_battle_shortcode_hits_011.txt`
- `_reports/spark_battle_shortcode_inspection_011.md`
- `_reports/spark_battle_shortcode_render_015.err`
- `_reports/spark_battle_shortcode_render_015.txt`
- `_reports/spark_battle_sim_hardening_001.md`
- `_reports/spark_battle_sim_protocol_inventory_007.json`
- `_reports/spark_battle_sim_protocol_inventory_007.md`
- `_reports/spark_battle_sim_protocol_inventory_008.json`
- `_reports/spark_battle_sim_protocol_inventory_008.md`
- `_reports/spark_emergence_artifact_hygiene_status_before_001.txt`
- `_reports/spark_protocol_adapter_smoke_009.txt`
- `_reports/spark_protocol_battle_sim_integration_006.md`
- `_reports/spark_protocol_demo_005.txt`
- `_reports/spark_protocol_exchange_ceiling_005.md`
- `_reports/spark_protocol_exchange_tests_005.txt`
- `_reports/spark_protocol_full_tests_005.txt`
- `_reports/spark_protocol_shortcode_engine_012.txt`
- `_reports/spark_shortcode_battle_start_failure_016.md`
- `_reports/spark_shortcode_engine_direct_016.err`
- `_reports/spark_shortcode_engine_direct_016.txt`
- `_reports/spark_shortcode_exception_verify_017.err`
- `_reports/spark_shortcode_exception_verify_017.md`
- `_reports/spark_shortcode_exception_verify_017.txt`
- `_reports/spark_shortcode_main_snip_016.txt`
- `_reports/spark_shortcode_render_stub_fix_018.err`
- `_reports/spark_shortcode_render_stub_fix_018.txt`
- `_reports/spark_shortcode_repository_payload_016.txt`
- `_reports/spark_shortcode_result_shape_019.err`
- `_reports/spark_shortcode_result_shape_019.txt`
- `_reports/spark_shortcode_template_compat_020.err`
- `_reports/spark_shortcode_template_compat_020.txt`
- `runtime/spark-battle-sim/`
- `runtime/tasks/emergence/harden_spark_battle_sim_proxy_001.yaml`

## Archive Candidates

Large/generated packages, temporary inventory dirs, and build byproducts. Archive before deletion.

- `_hostinger_build/spark-battle-sim/`
- `_reports/backups/`
- `_reports/emergence-character-generator_078.tar.gz`
- `_reports/emergence-character-generator_078b.tar.gz`
- `_reports/emergence-character-generator_095.tar.gz`
- `_reports/emergence-character-generator_095c.tar.gz`
- `_reports/emergence-character-generator_098b.tar.gz`
- `_reports/emergence-character-generator_098d.tar.gz`
- `_reports/spark-battle-sim_098b.tar.gz`
- `_reports/spark-battle-sim_098c.tar.gz`
- `_reports/spark-battle-sim_098d.tar.gz`
- `_reports/tmp_spark_battle_package_022/`
- `_reports/tmp_spark_inventory_008/`
- `_work/`

## Ignore Candidates

Generated build folders and transient work dirs that should probably be added to repo-local ignore after review.

- `_hostinger_build/spark-battle-sim/`
- `_reports/tmp_spark_battle_package_022/`
- `_reports/tmp_spark_inventory_008/`
- `_work/`

## Non Spark/Emergence Untracked

Review separately. Do not mix with this lane.

- `_reports/backups/`
- `_reports/domain_pass_invariants_078.json`
- `_reports/freeride_sales_funnel_deploy_candidate_001.txt`
- `_reports/freeride_theme_homepage_inspection_001.txt`
- `_reports/freeride_tsla_static_403_repair_001.txt`
- `_reports/freeride_wp_config_repair_056.md`
- `_reports/freerideinvestor_live_static_deploy_001.txt`
- `_reports/freerideinvestor_remote_root_inspection_001.txt`
- `_reports/freerideinvestor_static_403_repair_002.txt`
- `_reports/freerideinvestor_static_403_repair_002b.txt`
- `_reports/remote_configure_image_env_095d.sh`
- `_reports/static_tsla_command_center_remote_deploy_001.txt`
- `_reports/tsla_command_center_hostinger_deploy_diagnosis_001.txt`
- `_reports/tsla_snapshot_mode_remote_refresh_001.txt`
- `_reports/website_audit/website_inventory_classification_002.txt`
- `_reports/websites_hostinger_access_inspection_001.txt`
- `runtime/content/maskzero.site/client-preview.freeride-funnel-candidate.html`
- `runtime/scripts/hostinger_wp_manager.py.bak_052`
- `runtime/scripts/rerank_robinhood_contract_identity_raw_sources_001.py`
- `runtime/tasks/trading/rerank_robinhood_contract_identity_raw_sources_001.yaml`
