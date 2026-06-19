# MaskZero Quiz Migration Audit

Generated: 20260613_164804

## Intent

MaskZero quiz should use the same quiz page from the dadudekc.site migration.

The stale /spark-generator/ route should be replaced, aliased, or rebuilt from that migrated quiz source.

## Public Route Probes

### maskzero.site/spark-generator
```
HTTP/2 200 
content-type: text/html
last-modified: Fri, 12 Jun 2026 12:57:45 GMT
etag: "db44-6a2c0249-86a5a084b744a186;;;"
accept-ranges: bytes
content-length: 56132
date: Sat, 13 Jun 2026 16:48:04 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
x-spark-site: dadudekc.site
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

```

### maskzero.site/quiz
```
HTTP/2 404 
content-type: text/html
last-modified: Tue, 22 Apr 2025 07:41:12 GMT
etag: "119f-68074818-3e2104893eb140be;;;"
accept-ranges: bytes
content-length: 4511
date: Sat, 13 Jun 2026 16:48:04 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

```

### dadudekc.site quiz candidates
```
HTTP/2 301 
date: Sat, 13 Jun 2026 16:48:04 GMT
server: LiteSpeed
location: https://maskzero.site/quiz/
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

HTTP/2 404 
content-type: text/html
last-modified: Tue, 22 Apr 2025 07:41:12 GMT
etag: "119f-68074818-3e2104893eb140be;;;"
accept-ranges: bytes
content-length: 4511
date: Sat, 13 Jun 2026 16:48:04 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

HTTP/2 301 
date: Sat, 13 Jun 2026 16:48:05 GMT
server: LiteSpeed
location: https://maskzero.site/spark-generator/
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

HTTP/2 200 
content-type: text/html
last-modified: Fri, 12 Jun 2026 12:57:45 GMT
etag: "db44-6a2c0249-86a5a084b744a186;;;"
accept-ranges: bytes
content-length: 56132
date: Sat, 13 Jun 2026 16:48:05 GMT
server: LiteSpeed
platform: hostinger
panel: hpanel
content-security-policy: upgrade-insecure-requests
x-spark-site: dadudekc.site
alt-svc: h3=":443"; ma=2592000, h3-29=":443"; ma=2592000, h3-Q050=":443"; ma=2592000, h3-Q046=":443"; ma=2592000, h3-Q043=":443"; ma=2592000, quic=":443"; ma=2592000; v="43,46"

```

## Local Dadudekc Quiz Candidates
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/archive-note.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/archive.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/archive-project.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/DYNAMIC_CONTENT_SYSTEM.md
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/font-corruption-fix.css
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/footer.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/front-page.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/functions.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/header.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/home.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/functions/proof-metrics.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/experiment.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/icp-definition.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/note.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/offer-ladder.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/project.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/resume-item.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/index.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-blog.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-contact.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-idea-lab.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-now.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-portfolio.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/search.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/single-note.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/single.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/single-project.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/style.css
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/experiments-feed.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/icp-definition.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/offer-ladder.php
/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/project-demos.php
/workspace/collected/hostinger/wordpress/domains/freerideinvestor.com/plugins/trading-plans-automator/includes/class-plan-generator.php
/workspace/dadudekc-service-funnel/dadudekc website/wp-content/themes/dadudekc/style.css
/workspace/data/reports/websites/classify_spark_emergence_drift_20260606_172418.md
/workspace/data/reports/websites/emergence/add_spark_collector_card_renderer_001.md
/workspace/data/reports/websites/emergence/add_spark_os_browser_truth_harness_001.md
/workspace/data/reports/websites/emergence/build_clean_spark_os_static_page_001.md
/workspace/data/reports/websites/emergence/bust_spark_generator_asset_cache_001.md
/workspace/data/reports/websites/emergence/connect_emergence_spark_loop_explicit_001.md
/workspace/data/reports/websites/emergence/deploy_emergence_spark_loop_live_001.md
/workspace/data/reports/websites/emergence/deploy_spark_assets_all_aliases_and_verify_001.md
/workspace/data/reports/websites/emergence/diagnose_spark_generator_blank_001.md
/workspace/data/reports/websites/emergence/discover_spark_protocol_spine_001.md
/workspace/data/reports/websites/emergence/fix_canonical_spark_select_options_clickability_001.md
/workspace/data/reports/websites/emergence/fix_exact_spark_generator_route_cache_safe_001.md
/workspace/data/reports/websites/emergence/fix_original_spark_renderer_q11_branch_001.md
/workspace/data/reports/websites/emergence/fix_spark_quiz_freeze_observer_loop_001.md
/workspace/data/reports/websites/emergence/fix_spark_two_pass_layout_and_dossier_gate_001.md
/workspace/data/reports/websites/emergence/gate_final_dossier_single_button_after_quiz_001.md
/workspace/data/reports/websites/emergence/hide_public_spark_recovery_blockers_001.md
/workspace/data/reports/websites/emergence/inspect_and_fix_spark_q11_renderer_001.md
/workspace/data/reports/websites/emergence/move_spark_fail_open_to_runtime_plugin_001.md
/workspace/data/reports/websites/emergence/patch_spark_generator_fail_open_001.md
/workspace/data/reports/websites/emergence/polish_spark_generator_mobile_fields_001.md
/workspace/data/reports/websites/emergence/rebuild_spark_generator_clean_page_090_001.md
/workspace/data/reports/websites/emergence/recover_spark_hardening_patch_safely_001.md
/workspace/data/reports/websites/emergence/recover_versioned_spark_route_no_perl_001.md
/workspace/data/reports/websites/emergence/replace_spark_quiz_with_canonical_renderer_001.md
/workspace/data/reports/websites/emergence/restore_original_spark_generator_url_static_001.md
/workspace/data/reports/websites/emergence/revert_spark_generator_to_last_interactive_085_001.md
/workspace/data/reports/websites/emergence/show_final_dossier_only_after_quiz_complete_001.md
/workspace/data/reports/websites/emergence/smoke_spark_generator_after_asset_fix_001.md
/workspace/data/reports/websites/emergence/verify_canonical_spark_generator_slash_visible_ux_001.md
/workspace/data/reports/websites/emergence/verify_spark_protocol_live_spine_001.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164740.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md
/workspace/data/reports/websites/spark_account_system_scaffold_clean_20260606_173522.md
/workspace/_hostinger_build/dist/spark-battle-sim-install-ready-001.zip
/workspace/_hostinger_plan/dadudekc/install_checklist.md
/workspace/_reports/custom_spark_battle_participant_099.md
/workspace/_reports/dadudekc_spark_preview_pages_064.md
/workspace/_reports/emergence-character-generator_071b.tar.gz
/workspace/_reports/emergence-character-generator_076.tar.gz
/workspace/_reports/emergence-character-generator_079.tar.gz
/workspace/_reports/emergence-character-generator_080.tar.gz
/workspace/_reports/emergence-character-generator_081.tar.gz
/workspace/_reports/emergence-character-generator_082b.tar.gz
/workspace/_reports/emergence-character-generator_082.tar.gz
/workspace/_reports/emergence-character-generator_083.tar.gz
/workspace/_reports/emergence-character-generator_084b.tar.gz
/workspace/_reports/emergence-character-generator_085.tar.gz
/workspace/_reports/emergence-character-generator_086.tar.gz
/workspace/_reports/emergence-character-generator_088.tar.gz
/workspace/_reports/emergence-character-generator_089.tar.gz
/workspace/_reports/emergence-character-generator_090.tar.gz
/workspace/_reports/emergence-character-generator_091.tar.gz
/workspace/_reports/emergence-character-generator_092.tar.gz
/workspace/_reports/emergence-character-generator_093.tar.gz
/workspace/_reports/emergence-character-generator_094d.tar.gz
/workspace/_reports/emergence-character-generator_095d.tar.gz
/workspace/_reports/emergence-character-generator_098e.tar.gz
/workspace/_reports/emergence-character-generator_101.tar.gz
/workspace/_reports/emergence-character-generator_103.tar.gz
/workspace/_reports/emergence-character-generator_105.tar.gz
/workspace/_reports/emergence-character-generator_107c.tar.gz
/workspace/_reports/emergence-character-generator_109.tar.gz
/workspace/_reports/emergence-character-generator_110b.tar.gz
/workspace/_reports/emergence-character-generator_111.tar.gz
/workspace/_reports/emergence-character-generator_113.tar.gz
/workspace/_reports/emergence-character-generator_115.tar.gz
/workspace/_reports/emergence_character_generator_demo_071.md
/workspace/_reports/emergence_character_generator_render_072.md.html
/workspace/_reports/emergence_generated_spark_portrait_card_090.md
/workspace/_reports/emergence_generator_answer_labels_082b.md
/workspace/_reports/emergence_generator_answer_labels_082.md
/workspace/_reports/emergence_premium_spark_os_redesign_001.md
/workspace/_reports/force_dadudekc_public_root_spark_os_001.md
/workspace/_reports/restore_spark_emergence_quarantine_001.sh
/workspace/_reports/runtime_spark_battle_sim_candidate_file_review_001.md
/workspace/_reports/runtime_spark_battle_sim_candidate_inspection_001.md
/workspace/_reports/shareable_spark_handoff_token_101.md
/workspace/_reports/spark_battle_engine_smoke_failure_014.md
/workspace/_reports/spark_battle_shortcode_commit_023.err
/workspace/_reports/spark_battle_shortcode_final_022.err
/workspace/_reports/spark_battle_shortcode_inspection_011.md
/workspace/_reports/spark-battle-sim-0.1.0.zip
/workspace/_reports/spark-battle-sim-0.2.0-spark-protocol.zip
/workspace/_reports/spark-battle-sim_098e.tar.gz
/workspace/_reports/spark-battle-sim_099.tar.gz
/workspace/_reports/spark-battle-sim_101.tar.gz
/workspace/_reports/spark-battle-sim_103.tar.gz
/workspace/_reports/spark-battle-sim_106b.tar.gz
/workspace/_reports/spark-battle-sim_111.tar.gz
/workspace/_reports/spark-battle-sim_115.tar.gz
/workspace/_reports/spark_battle_sim_hardening_001.md
/workspace/_reports/spark_battle_sim_hostinger_install_097.md
/workspace/_reports/spark_battle_sim_install_ready_001.md
/workspace/_reports/spark_battle_sim_plugin_001.md
/workspace/_reports/spark_battle_sim_protocol_commit_023.md
/workspace/_reports/spark_battle_sim_protocol_inventory_007.md
/workspace/_reports/spark_battle_sim_protocol_inventory_008.md
/workspace/_reports/spark_battle_sim_protocol_package_022.md
/workspace/_reports/spark_emergence_artifact_hygiene_001.md
/workspace/_reports/spark_emergence_quarantine_001.md
/workspace/_reports/spark_functionality_theme_integration_001.md
/workspace/_reports/spark_protocol_adapter_smoke_010.md
/workspace/_reports/spark_protocol_battle_sim_integration_006.md
/workspace/_reports/spark_protocol_character_generation_port_076.md
/workspace/_reports/spark_protocol_exchange_ceiling_005.md
/workspace/_reports/spark-protocol-patched-005.zip
/workspace/_reports/spark_shortcode_battle_start_failure_016.md
/workspace/_reports/spark_shortcode_exception_verify_017.md
/workspace/_reports/spark_shortcode_template_compat_020.html
/workspace/_reports/spark_shortcode_template_compat_020.md
/workspace/runtime/account_system/spark_account_contract.py
/workspace/runtime/content/dadudekc.site/index.html
/workspace/runtime/content/maskzero.site/assets/js/spark-auth-nav.js
/workspace/runtime/content/maskzero.site/character-generator.html
/workspace/runtime/deploy/sites/maskzero.site/custom_spark_battle_participant_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_character_generator_demo_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_generated_spark_portrait_card_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_generator_answer_labels_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/shareable_spark_handoff_token_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_battle_sim_hostinger_install_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_preview_pages_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_protocol_character_generation_port_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_protocol_product_map.yaml
/workspace/runtime/manifests/spark_emergence_drift_classification_20260606_172418.json
/workspace/runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css
/workspace/runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js
/workspace/runtime/plugins/emergence-character-generator/assets/spark-protocol-v85-domain-key.json
/workspace/runtime/plugins/emergence-character-generator/emergence-character-generator.php
/workspace/runtime/plugins/spark-battle-sim/bin/spark-battle-engine-smoke.php
/workspace/runtime/plugins/spark-battle-sim/bin/spark-battle-shortcode-render-smoke.php
/workspace/runtime/plugins/spark-battle-sim/bin/spark-protocol-adapter-smoke.php
/workspace/runtime/plugins/spark-battle-sim/includes/spark-protocol-autoload.php
/workspace/runtime/plugins/spark-battle-sim/spark-battle-sim.php
/workspace/runtime/quarantine/legacy_runtime_risk_reports_001/_reports__spark_battle_engine_smoke_failure_014.err
/workspace/runtime/quarantine/legacy_runtime_risk_reports_001/_reports__spark_battle_shortcode_render_015.err
/workspace/runtime/quarantine/legacy_runtime_risk_reports_001/_reports__spark_shortcode_engine_direct_016.err
/workspace/runtime/quarantine/legacy_runtime_risk_reports_001/_reports__spark_shortcode_exception_verify_017.err
/workspace/runtime/quarantine/legacy_runtime_risk_reports_001/_reports__spark_shortcode_render_stub_fix_018.err
/workspace/runtime/quarantine/legacy_runtime_risk_reports_001/_reports__spark_shortcode_result_shape_019.err
/workspace/runtime/quarantine/legacy_runtime_risk_reports_001/_reports__spark_shortcode_template_compat_020.err
/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/dreamos_emergence_theme_restore_routes_001/spark.html
/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/dreamos_theme_placeholder_guard_repair_001/spark.html
/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/emergence_mobile_finish_pass_001/spark.html
/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/emergence_mobile_visual_system_repair_001/spark.html
/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/force_dadudekc_public_root_spark_os_001/spark.html
/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/remove_duplicate_emergence_page_shell_001/spark.html
/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/spark_functionality_theme_integration_001/spark.html
/workspace/runtime/scripts/smoke_emergence_character_generator_public_path.py
/workspace/runtime/scripts/smoke_emergence_generated_spark_portrait_card.py
/workspace/runtime/spark-battle-sim/client/sparkBattleClient.js
/workspace/runtime/tasks/add_emergence_generated_spark_portrait_card_090.yaml
/workspace/runtime/tasks/add_spark_battle_sim_plugin_001.yaml
/workspace/runtime/tasks/classify_spark_emergence_artifacts_001.yaml
/workspace/runtime/tasks/emergence/force_dadudekc_public_root_spark_os_001.yaml
/workspace/runtime/tasks/emergence/harden_spark_battle_sim_proxy_001.yaml
/workspace/runtime/tasks/emergence/integrate_spark_protocol_battle_sim_006.yaml
/workspace/runtime/tasks/emergence/polish_spark_functionality_theme_integration_001.yaml
/workspace/runtime/tasks/emergence/redesign_emergence_premium_spark_os_001.yaml
/workspace/runtime/tasks/inspect_runtime_spark_battle_sim_candidate_001.yaml
/workspace/runtime/tasks/package_spark_battle_sim_install_ready_001.yaml
/workspace/runtime/tasks/quarantine_spark_emergence_generated_artifacts_001.yaml
/workspace/runtime/tasks/show_runtime_spark_battle_sim_candidate_files_001.yaml
/workspace/runtime/tasks/websites/add_spark_collector_card_renderer_001.yaml
/workspace/runtime/tasks/websites/add_spark_os_browser_truth_harness_001.yaml
/workspace/runtime/tasks/websites/build_clean_spark_os_static_page_001.yaml
/workspace/runtime/tasks/websites/bust_spark_generator_asset_cache_001.yaml
/workspace/runtime/tasks/websites/connect_emergence_spark_loop_explicit_001.yaml
/workspace/runtime/tasks/websites/deploy_emergence_spark_loop_live_001.yaml
/workspace/runtime/tasks/websites/deploy_spark_assets_all_aliases_and_verify_001.yaml
/workspace/runtime/tasks/websites/deploy_spark_assets_native_sftp_001.yaml
/workspace/runtime/tasks/websites/deploy_spark_generator_fail_open_assets_001.yaml
/workspace/runtime/tasks/websites/diagnose_spark_generator_blank_001.yaml
/workspace/runtime/tasks/websites/discover_remote_path_and_deploy_spark_assets_001.yaml
/workspace/runtime/tasks/websites/discover_spark_protocol_spine_001.yaml
/workspace/runtime/tasks/websites/fix_canonical_spark_select_options_clickability_001.yaml
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_001.yaml
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_safe_001.yaml
/workspace/runtime/tasks/websites/fix_original_spark_renderer_q11_branch_001.yaml
/workspace/runtime/tasks/websites/fix_spark_generate_payload_hardening_001.yaml
/workspace/runtime/tasks/websites/fix_spark_os_static_button_handlers_001.yaml
/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml
/workspace/runtime/tasks/websites/fix_spark_two_pass_layout_and_dossier_gate_001.yaml
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml
/workspace/runtime/tasks/websites/hide_public_spark_recovery_blockers_001.yaml
/workspace/runtime/tasks/websites/inspect_and_fix_spark_q11_renderer_001.yaml
/workspace/runtime/tasks/websites/move_spark_fail_open_to_runtime_plugin_001.yaml
/workspace/runtime/tasks/websites/patch_spark_generator_fail_open_001.yaml
/workspace/runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml
/workspace/runtime/tasks/websites/polish_spark_generator_mobile_fields_001.yaml
/workspace/runtime/tasks/websites/rebuild_spark_generator_clean_page_090_001.yaml
/workspace/runtime/tasks/websites/recover_spark_hardening_patch_safely_001.yaml
/workspace/runtime/tasks/websites/recover_versioned_spark_route_no_perl_001.yaml
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml
/workspace/runtime/tasks/websites/restore_original_spark_generator_url_static_001.yaml
/workspace/runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml
/workspace/runtime/tasks/websites/show_final_dossier_only_after_quiz_complete_001.yaml
/workspace/runtime/tasks/websites/smoke_spark_generator_after_asset_fix_001.yaml
/workspace/runtime/tasks/websites/spark_account_system_scaffold_001.yaml
/workspace/runtime/tasks/websites/verify_canonical_spark_generator_slash_visible_ux_001.yaml
/workspace/runtime/tasks/websites/verify_exact_canonical_spark_generator_route_001.yaml
/workspace/runtime/tasks/websites/verify_spark_protocol_live_spine_001.yaml
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/map-grid.json
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/world.json
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/index.html
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/meridian-dispatch.css
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/meridian-dispatch.js
/workspace/tests/test_spark_account_contract.py
/workspace/tests/websites/test_spark_os_static_browser_truth.py

## Local MaskZero Target Candidates
/workspace/data/reports/domain_migration/maskzero_home_migration_20260612_082218.txt
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164740.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md
/workspace/runtime/content/maskzero.site/assets/js/spark-auth-nav.js
/workspace/runtime/content/maskzero.site/battles.html
/workspace/runtime/content/maskzero.site/battle-simulator.html
/workspace/runtime/content/maskzero.site/battles/index.html
/workspace/runtime/content/maskzero.site/character-generator.html
/workspace/runtime/content/maskzero.site/client-preview.html
/workspace/runtime/content/maskzero.site/create-hero/index.html
/workspace/runtime/content/maskzero.site/dispatch/index.html
/workspace/runtime/content/maskzero.site/home.html
/workspace/runtime/content/maskzero.site/how-it-works/index.html
/workspace/runtime/content/maskzero.site/index.html
/workspace/runtime/content/maskzero.site/login/index.html
/workspace/runtime/content/maskzero.site/meridian-map/index.html
/workspace/runtime/content/maskzero.site/missions/index.html
/workspace/runtime/content/maskzero.site/origin-rules/index.html
/workspace/runtime/content/maskzero.site/protocol.html
/workspace/runtime/content/maskzero.site/roster-rules/index.html
/workspace/runtime/content/maskzero.site/spark-generator/index.html
/workspace/runtime/content/maskzero.site/spark-os/index.html
/workspace/runtime/content/maskzero.site/the-emergence.html
/workspace/runtime/deploy/sites/maskzero.site/client_theme_update_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/custom_spark_battle_participant_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_admin_event_dashboard_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_battle_story_cinematics_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_cg_public_path_smoke_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_character_battle_handoff_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_character_generator_demo_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_character_profile_display_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_character_sheet_output_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_conversion_funnel_report_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_disguised_answer_labels_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_domain_pass_plugin_syntax_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_flavor_phase_transition_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_flavor_power_selection_pass_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_flavor_privacy_unlock_routing_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_full_handoff_browser_smoke_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_generated_spark_portrait_card_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_generator_answer_labels_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_homepage_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_hostinger_image_env_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_openai_premium_image_provider_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_openai_provider_error_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_portrait_prompt_preview_polish_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_portrait_prompt_quality_fixtures_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_premium_hero_image_provider_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_premium_portrait_design_controls_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_premium_portrait_prompt_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_privacy_safe_event_tracking_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_protocol_v85_question_bank_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_public_demo_hardening_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_public_scoring_privacy_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_public_share_card_ui_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_public_site_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_saved_character_browser_smoke_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_saved_character_records_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_scan_no_reload_browser_smoke_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_scan_submit_state_reset_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_token_handoff_browser_smoke_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/emergence_totality_observation_portrait_prompt_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/shareable_spark_handoff_token_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_battle_sim_hostinger_install_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_preview_pages_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_protocol_character_generation_port_manifest.yaml
/workspace/runtime/deploy/sites/maskzero.site/spark_protocol_product_map.yaml
/workspace/runtime/deploy/sites/maskzero.site/wp_public_routing_manifest.yaml
/workspace/runtime/tasks/maskzero_home_migration_001.yaml
/workspace/tests/websites/test_maskzero_domain_routing.py
/workspace/tests/websites/test_maskzero_missions_route.py

## Content Markers
/workspace/ops/deployment/unified_deployer.py:106:        'dadudekc.com': {
/workspace/ops/deployment/unified_deployer.py:107:            'theme': [base_dir / "overlays" / "wp" / "theme" / "dadudekc"],
/workspace/tests/websites/test_maskzero_missions_route.py:23:        CONTENT / "spark-generator/index.html",
/workspace/ops/deployment/sites.yml:46:  dadudekc.com:
/workspace/ops/deployment/sites.yml:47:    path: websites/dadudekc.com
/workspace/ops/deployment/sites.yml:49:    verify_url: https://dadudekc.com/.well-known/deploy.json
/workspace/_proof/salvage_reviews/_indexed/data__reports__ml_ai_experiments__repo_salvage_20260613__self-evolving-ai__README.md_35c5c69e.md:185:📖 **[Full API Docs](https://dadudekc.com/self-evolving-ai/api/)**
/workspace/_proof/salvage_reviews/_indexed/data__reports__ml_ai_experiments__repo_salvage_20260613__self-evolving-ai__README.md_35c5c69e.md:212:Please report security vulnerabilities to: security@dadudekc.com
/workspace/_proof/salvage_reviews/_indexed/data__reports__ml_ai_experiments__repo_salvage_20260613__self-evolving-ai__README.md_35c5c69e.md:239:- **Documentation**: [dadudekc.com/self-evolving-ai](https://dadudekc.com/self-evolving-ai)
/workspace/_proof/salvage_reviews/_indexed/data__reports__ml_ai_experiments__repo_salvage_20260613__self-evolving-ai__README.md_35c5c69e.md:245:**Built with ❤️ by [DaDudeKC](https://dadudekc.com) | Enterprise-grade software with professional standards**
/workspace/_deploy/weareswarm.online/dreamos-services/index.html:76:          <a class="btn primary" href="mailto:dadudekc@gmail.com?subject=Dream.OS Services">Start a project</a>
/workspace/_deploy/weareswarm.online/dreamos-services/index.html:177:          <a class="btn primary" href="mailto:dadudekc@gmail.com?subject=Dream.OS Services">Email DaDudeKC</a>
/workspace/tests/websites/test_maskzero_domain_routing.py:9:DADUDEKC = ROOT / "runtime/content/dadudekc.site"
/workspace/tests/websites/test_maskzero_domain_routing.py:13:def test_dadudekc_site_is_redirect_shell_to_maskzero_ssot():
/workspace/tests/websites/test_maskzero_domain_routing.py:15:    dadudekc = sites["dadudekc.site"]
/workspace/tests/websites/test_maskzero_domain_routing.py:17:    assert dadudekc["path"] == "runtime/content/dadudekc.site"
/workspace/tests/websites/test_maskzero_domain_routing.py:18:    assert dadudekc["canonical_target"] == "https://maskzero.site"
/workspace/tests/websites/test_maskzero_domain_routing.py:19:    assert dadudekc["deploy_files"] == [
/workspace/tests/websites/test_maskzero_domain_routing.py:20:        "runtime/content/dadudekc.site/.htaccess",
/workspace/tests/websites/test_maskzero_domain_routing.py:21:        "runtime/content/dadudekc.site/index.html",
/workspace/tests/websites/test_maskzero_domain_routing.py:26:def test_dadudekc_redirect_preserves_stale_project_paths():
/workspace/tests/websites/test_maskzero_domain_routing.py:32:    assert "Spark, Emergence, and Mask Zero are the same project" in index
/workspace/tests/websites/test_maskzero_domain_routing.py:39:        MASKZERO / "spark-generator/index.html",
/workspace/tests/websites/test_maskzero_domain_routing.py:49:    dadudekc_pages = [p for p in DADUDEKC.rglob("*") if p.is_file() and p.name not in {".htaccess", "index.html"}]
/workspace/tests/websites/test_maskzero_domain_routing.py:50:    assert not dadudekc_pages, f"dadudekc.site must only contain redirect shell files: {dadudekc_pages}"
/workspace/tests/websites/test_maskzero_domain_routing.py:57:    assert "dadudekc.site:" in deploy_modes
/workspace/tests/websites/test_maskzero_domain_routing.py:59:    assert "redirect_dadudekc_to_maskzero: true" in task
/workspace/tests/websites/test_maskzero_domain_routing.py:60:    assert "preserve_dadudekc_as_separate_site: true" in task
/workspace/tests/websites/test_maskzero_domain_routing.py:67:    assert "<title>MaskZero | Spark Protocol in Meridian City</title>" in index
/workspace/tests/websites/test_maskzero_domain_routing.py:68:    assert '<h1 id="home-title">MaskZero</h1>' in index
/workspace/tests/websites/test_maskzero_domain_routing.py:70:    assert "MaskZero · Spark Protocol v8.6" in index
/workspace/tests/websites/test_maskzero_domain_routing.py:88:    assert not missing, f"Missing MaskZero route sources: {missing}"
/workspace/tests/websites/test_maskzero_domain_routing.py:92:        assert "MaskZero" in html
/workspace/tests/websites/test_maskzero_domain_routing.py:102:        if "dadudekc.site" in path.read_text(encoding="utf-8", errors="ignore").lower()
/workspace/tests/websites/test_maskzero_domain_routing.py:104:    assert not offenders, f"MaskZero public files reference dadudekc.site: {offenders}"
/workspace/_deploy/weareswarm/dreamos-services/index.html:19:      <a class="cta" href="mailto:dadudekc@gmail.com?subject=Dream.OS Services Intake">Start a Dream.OS sprint</a>
/workspace/_deploy/weareswarm/dreamos-services/index.html:23:      <article class="card package"><h2>Website Recovery Sprint</h2><div class="price">$300-$750</div><p>For broken WordPress sites, dead domains, bad routes, and missing proof pages.</p><ul><li>Domain audit</li><li>Broken route repair</li><li>Static fallback</li><li>Public proof page</li><li>Deployment report</li></ul><a class="btn" href="mailto:dadudekc@gmail.com?subject=Website Recovery Sprint">Book recovery</a></article>
/workspace/_deploy/weareswarm/dreamos-services/index.html:24:      <article class="card package"><h2>Repo Rescue Sprint</h2><div class="price">$500-$1,500</div><p>For duplicated repos, salvage decisions, stale assets, and promotion blockers.</p><ul><li>Duplicate repo classification</li><li>Salvage manifest</li><li>Promotion plan</li><li>Test gate</li><li>Cleanup report</li></ul><a class="btn" href="mailto:dadudekc@gmail.com?subject=Repo Rescue Sprint">Rescue a repo</a></article>
/workspace/_deploy/weareswarm/dreamos-services/index.html:25:      <article class="card package"><h2>Automation Operator Setup</h2><div class="price">$1,500-$5,000</div><p>For teams that need prompts, task boards, verification gates, and reporting loops.</p><ul><li>Task board</li><li>Agent prompts</li><li>Verification gates</li><li>Reporting dashboard</li><li>Handoff docs</li></ul><a class="btn" href="mailto:dadudekc@gmail.com?subject=Automation Operator Setup">Install operator system</a></article>
/workspace/_deploy/weareswarm/projects/index.html:6:<article class="card"><span class="tag">active</span><h2>DaDudeKC / MaskZero</h2><p class="meta">dadudekc.com / maskzero.site · dadudekc-service-funnel</p><p>Founder/operator brand and community funnel consolidation lane.</p><p><strong>Problem solved:</strong> Promoted scattered website fragments into a canonical service funnel.</p><div class="proof">Proof: dadudekc-service-funnel · promotion reports · service route</div><p><strong>Next unlock:</strong> Publish portfolio receipts and conversion paths.</p><p><strong>Revenue angle:</strong> Operator services, community, and brand trust layer.</p><a class="btn" href="https://dadudekc.com/">Open proof route</a></article>
/workspace/_deploy/weareswarm/index.html:152:          <h3>Spark / Emergence</h3>
/workspace/_configs/hostinger_plan_config_013.json:37:    "dadudekc": {
/workspace/_configs/hostinger_plan_config_013.json:43:      "hold_reason": "No deployable package yet. dadudekc-community-features is staged but not promoted/package-gated.",
/workspace/_configs/hostinger_plan_config_013.json:46:        "Promote/package dadudekc-community-features only if the site is still worth hosting."
/workspace/README.md:13:- `dadudekc`: personal/local/community legacy brand
/workspace/experiments/freeride-legacy-review/freerideinvestorwebsite/website_posts/50_Script_Ideas_for_FreeRideInvestor.html:41:<p>Empower users with trading tutorials, quizzes, and AI-powered advice.</p>
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/meridian-dispatch.js:5:  const FACTIONS = ["AEGIS", "Sterling", "Gods Armor", "Undercity"];
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/index.html:15:        <a href="/the-emergence/">Emergence</a> ·
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/index.html:16:        <a href="/spark-generator/">Spark Generator</a>
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/world.json:30:      "factions": ["AEGIS", "Sterling", "Gods Armor", "Undercity"],
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/world.json:33:      "independent": [["AEGIS", "Undercity"]]
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json:8:      "faction_hooks": ["AEGIS", "Undercity"],
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json:10:      "rep_changes": { "AEGIS": 1, "Undercity": 1 },
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json:20:      "faction_hooks": ["Sterling", "AEGIS"],
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json:22:      "rep_changes": { "Sterling": 1, "AEGIS": 1 },
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json:45:      "faction_hooks": ["AEGIS"],
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json:47:      "rep_changes": { "AEGIS": 1 },
/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/map-grid.json:85:  "faction_overlays": ["AEGIS", "Sterling", "Gods Armor", "Undercity"]
/workspace/runtime/themes/dreamos-emergence/style.css:2:Theme Name: DreamOS Emergence
/workspace/runtime/themes/dreamos-emergence/style.css:5:Description: Custom Spark OS theme for The Emergence. Preserves WordPress content and plugin functionality under a premium custom design.
/workspace/runtime/themes/dreamos-emergence/style.css:199:/* DreamOS Emergence: restored functionality polish */
/workspace/runtime/themes/dreamos-emergence/style.css:271:/* DreamOS Emergence Mobile Visual System Repair 001
/workspace/runtime/themes/dreamos-emergence/style.css:839:/* DreamOS Emergence Mobile Finish Pass 001
/workspace/runtime/themes/dreamos-emergence/style.css:1127:/* End DreamOS Emergence Mobile Finish Pass 001 */
/workspace/runtime/themes/dreamos-emergence/parts/header.html:8:        <small>The Emergence</small>
/workspace/runtime/themes/dreamos-emergence/parts/header.html:15:      <a href="/spark-generator/">Generate</a>
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.md:9:- `dadudekc.com` is expired hold; do not repair until reacquired.
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.md:42:- dadudekc.com
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.md:57:| dadudekc.com | 000 | expired_domain_hold | expired legacy domain; do not repair until reacquired cheaply | hold | - | ignore for deploy/repair until domain is reacquired |
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.md:58:| maskzero.site | 200 | active_product_host | Emergence / Spark Battle Simulator live prototype host | keep_live | - | continue Emergence launch hardening and browser E2E from supported host |
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.md:71:1. Keep `maskzero.site` live for Emergence work.
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.md:73:3. Hold `dadudekc.com`; expired domain, no repair.
/workspace/runtime/themes/dreamos-emergence/parts/footer.html:4:    <span>The Emergence // Spark OS</span>
/workspace/runtime/themes/dreamos-emergence/functions.php:3: * DreamOS Emergence theme functions.
/workspace/runtime/themes/dreamos-emergence/functions.php:88:    $generator_url = home_url('/spark-generator/?mission=first-awakening');
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.json:27:      "dadudekc.com"
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.json:78:      "site": "dadudekc.com",
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.json:88:      "remote_root": "/home/u996867598/domains/dadudekc.com/public_html",
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.json:105:      "business_purpose": "Emergence / Spark Battle Simulator live prototype host",
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.json:109:      "next_lane": "continue Emergence launch hardening and browser E2E from supported host",
/workspace/runtime/business/domain_inventory/domain_purpose_decision_matrix_005.json:297:    "dadudekc_com_repair_target": false,
/workspace/experiments/digital-dreamscape-original/README.md:6:dadudekc-service-funnel/AI agent/AI/Digital dreamscape
/workspace/experiments/digital-dreamscape-original/README.md:12:original dadudekc / DreamOS prototype theme
/workspace/experiments/digital-dreamscape-original/README.md:13:not MaskZero
/workspace/runtime/docs/emergence_page_design_registry.md:1:# Emergence Page Design Registry
/workspace/runtime/docs/emergence_page_design_registry.md:20:| /spark-generator/ | Main player creation flow | Restored | Redesign as onboarding app |
/workspace/runtime/docs/emergence_page_design_registry.md:25:| /character-generator/ | Legacy generator route | Exists | Redirect or merge into /spark-generator/ |
/workspace/runtime/docs/emergence_page_design_registry.md:33:Purpose: explain The Emergence, route users into Spark creation, and present battle sim as optional downtime mode.
/workspace/runtime/docs/emergence_page_design_registry.md:38:### Spark Generator
/workspace/runtime/docs/emergence_page_design_registry.md:39:Route: /spark-generator/
/workspace/routes/weareswarm.online/dreamos-services/index.html:76:          <a class="btn primary" href="mailto:dadudekc@gmail.com?subject=Dream.OS Services">Start a project</a>
/workspace/routes/weareswarm.online/dreamos-services/index.html:177:          <a class="btn primary" href="mailto:dadudekc@gmail.com?subject=Dream.OS Services">Email DaDudeKC</a>
/workspace/runtime/tasks/websites/verify_spark_protocol_live_spine_001.yaml:9:    - /spark-generator/
/workspace/runtime/tasks/websites/verify_guaranteed_dossier_injector_live_001.yaml:7:  route: /spark-generator/
/workspace/routes/weareswarm/dreamos-services/index.html:19:      <a class="cta" href="mailto:dadudekc@gmail.com?subject=Dream.OS Services Intake">Start a Dream.OS sprint</a>
/workspace/routes/weareswarm/dreamos-services/index.html:23:      <article class="card package"><h2>Website Recovery Sprint</h2><div class="price">$300-$750</div><p>For broken WordPress sites, dead domains, bad routes, and missing proof pages.</p><ul><li>Domain audit</li><li>Broken route repair</li><li>Static fallback</li><li>Public proof page</li><li>Deployment report</li></ul><a class="btn" href="mailto:dadudekc@gmail.com?subject=Website Recovery Sprint">Book recovery</a></article>
/workspace/routes/weareswarm/dreamos-services/index.html:24:      <article class="card package"><h2>Repo Rescue Sprint</h2><div class="price">$500-$1,500</div><p>For duplicated repos, salvage decisions, stale assets, and promotion blockers.</p><ul><li>Duplicate repo classification</li><li>Salvage manifest</li><li>Promotion plan</li><li>Test gate</li><li>Cleanup report</li></ul><a class="btn" href="mailto:dadudekc@gmail.com?subject=Repo Rescue Sprint">Rescue a repo</a></article>
/workspace/routes/weareswarm/dreamos-services/index.html:25:      <article class="card package"><h2>Automation Operator Setup</h2><div class="price">$1,500-$5,000</div><p>For teams that need prompts, task boards, verification gates, and reporting loops.</p><ul><li>Task board</li><li>Agent prompts</li><li>Verification gates</li><li>Reporting dashboard</li><li>Handoff docs</li></ul><a class="btn" href="mailto:dadudekc@gmail.com?subject=Automation Operator Setup">Install operator system</a></article>
/workspace/runtime/tasks/websites/verify_floating_dossier_fab_live_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/verify_exact_canonical_spark_generator_route_001.yaml:6:  url: https://maskzero.site/spark-generator
/workspace/runtime/tasks/websites/verify_client_hardening_inline_live_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/verify_canonical_spark_generator_slash_visible_ux_001.yaml:6:  url: https://maskzero.site/spark-generator/
/workspace/runtime/deploy/sites/maskzero.site/emergence_public_site_manifest.yaml:5:product_name: The Emergence
/workspace/runtime/deploy/sites/maskzero.site/emergence_public_site_manifest.yaml:7:hero_thesis: 'The Emergence began as a machine for answering “Who would win?” and evolved into a world where the answer could include you.'
/workspace/runtime/tasks/websites/spark_account_system_scaffold_001.yaml:4:target: Spark/Emergence account system
/workspace/runtime/tasks/websites/spark_account_system_scaffold_001.yaml:24:    - https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/smoke_spark_generator_after_asset_fix_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/show_final_dossier_only_after_quiz_complete_001.yaml:1:id: show_final_dossier_only_after_quiz_complete_001
/workspace/runtime/tasks/websites/show_final_dossier_only_after_quiz_complete_001.yaml:2:title: Show final dossier only after Spark quiz completion
/workspace/runtime/tasks/websites/show_final_dossier_only_after_quiz_complete_001.yaml:6:  canonical_url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/show_final_dossier_only_after_quiz_complete_001.yaml:10:  - hide all legacy dossier buttons before quiz completion
/workspace/runtime/tasks/websites/show_final_dossier_only_after_quiz_complete_001.yaml:19:commit_message: Show final dossier only after Spark quiz completion
/workspace/runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml:7:  version: 0.8.5-quiz-freeze-observer-fix-001
/workspace/runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml:8:  url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml:10:  - restore Emergence generator plugin PHP/CSS/JS from last known interactive renderer
/workspace/runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml:13:  - verify exact canonical route serves 0.8.5-quiz-freeze-observer-fix-001
/workspace/runtime/tasks/websites/revert_spark_generator_to_last_interactive_085_001.yaml:15:  - live page references 0.8.5-quiz-freeze-observer-fix-001
/workspace/runtime/tasks/websites/restore_original_spark_generator_url_static_001.yaml:6:  url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/restore_original_spark_generator_url_static_001.yaml:7:  physical_path: public_html/spark-generator/index.html
/workspace/runtime/tasks/websites/restore_original_spark_generator_url_static_001.yaml:10:  - build clean static page at original /spark-generator/ URL
/workspace/runtime/tasks/websites/restore_original_spark_generator_url_static_001.yaml:15:  - exact /spark-generator/ serves clean static marker
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:1:id: replace_spark_quiz_with_canonical_renderer_001
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:2:title: Replace Spark quiz with canonical question-bank renderer
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:6:  canonical_url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:7:  asset_version: 0.8.7-canonical-quiz-renderer-001
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:10:  - suppress legacy unstable quiz UI after canonical renderer mounts
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:11:  - render Q1-Q28 directly from EmergenceCG.question_bank
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:16:  - canonical route loads 0.8.7-canonical-quiz-renderer-001
/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml:20:commit_message: Replace Spark quiz with canonical renderer
/workspace/runtime/tasks/websites/recover_versioned_spark_route_no_perl_001.yaml:7:  route: /spark-generator/?spark=v075
/workspace/runtime/tasks/websites/recover_versioned_spark_route_no_perl_001.yaml:14:  - live homepage contains /spark-generator/?spark=v075
/workspace/runtime/tasks/websites/recover_versioned_spark_route_no_perl_001.yaml:15:  - live homepage does not contain Generate CTA href="/spark-generator/"
/workspace/runtime/deploy/sites/maskzero.site/emergence_homepage_manifest.yaml:8:hero_thesis: 'The Emergence began as a machine for answering “Who would win?” and evolved into a world where the answer could include you.'
/workspace/runtime/tasks/websites/recover_canonical_route_and_force_dossier_button_001.yaml:7:  canonical_route: /spark-generator/
/workspace/runtime/tasks/websites/recover_canonical_route_and_force_dossier_button_001.yaml:17:  - live homepage points to /spark-generator/
/workspace/runtime/tasks/websites/recover_canonical_route_and_force_dossier_button_001.yaml:18:  - live homepage does not point to /spark-generator/?spark=v075
/workspace/runtime/tasks/websites/recover_canonical_route_and_force_dossier_button_001.yaml:19:  - canonical /spark-generator/ loads 0.7.6-canonical-dossier-001 assets
/workspace/runtime/tasks/websites/rebuild_spark_generator_clean_page_090_001.yaml:6:  url: https://maskzero.site/spark-generator/
/workspace/experiments/bible-application/index.html:1014:                    connections.push('Physical manifestation of divine will');
/workspace/runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml:7:  from: "/spark-generator/"
/workspace/runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml:8:  to: "/spark-generator/?spark=v075"
/workspace/runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml:10:  - patch repo Emergence content CTAs
/workspace/runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml:15:  - live homepage contains /spark-generator/?spark=v075
/workspace/runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml:16:  - live homepage Generate Your Spark no longer points only to /spark-generator/
/workspace/runtime/tasks/websites/patch_client_payload_hardening_allow_answers_safe_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/patch_client_payload_hardening_allow_answers_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/move_spark_fail_open_to_runtime_plugin_001.yaml:15:  - runtime JS contains DreamOS Spark Generator Fail-Open Guard
/workspace/runtime/tasks/websites/move_spark_fail_open_to_runtime_plugin_001.yaml:16:  - runtime CSS contains DreamOS Spark Generator Fail-Open Visibility Guard
/workspace/runtime/deploy/hostinger_theme_registry.yaml:10:      - dadudekc.com
/workspace/runtime/deploy/hostinger_theme_registry.yaml:16:      - dadudekc.com
/workspace/runtime/deploy/hostinger_theme_registry.yaml:79:  dadudekc:
/workspace/runtime/deploy/hostinger_theme_registry.yaml:84:      - dadudekc.com
/workspace/runtime/deploy/hostinger_theme_registry.yaml:102:      - dadudekc.com
/workspace/runtime/deploy/hostinger_theme_registry.yaml:220:      - dadudekc.com
/workspace/runtime/deploy/hostinger_theme_registry.yaml:237:      - dadudekc.com
/workspace/runtime/deploy/hostinger_theme_registry.yaml:254:      - dadudekc.com
/workspace/runtime/tasks/websites/inspect_and_fix_spark_q11_renderer_001.yaml:6:  canonical_url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/hide_public_spark_recovery_blockers_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/gate_flavor_and_save_character_to_battle_001.yaml:6:  spark_url: https://maskzero.site/spark-generator/
/workspace/runtime/deploy/hostinger_sites_manifest.yaml:214:  dadudekc_com:
/workspace/runtime/deploy/hostinger_sites_manifest.yaml:215:    domain: 'dadudekc.com'
/workspace/runtime/deploy/hostinger_sites_manifest.yaml:218:    wp_root: '/home/u996867598/domains/dadudekc.com/public_html'
/workspace/runtime/deploy/hostinger_sites_manifest.yaml:219:    plugins_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/plugins'
/workspace/runtime/deploy/hostinger_sites_manifest.yaml:220:    themes_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/themes'
/workspace/runtime/deploy/hostinger_sites_manifest.yaml:267:      - slug: 'dadudekc'
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:1:id: gate_final_dossier_single_button_after_quiz_001
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:2:title: Gate final dossier behind completed Spark quiz
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:6:  canonical_url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:7:  asset_version: 0.8.2-quiz-gated-single-dossier-001
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:11:  - require real quiz completion before POST
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:12:  - render incomplete quiz warning instead of fallback dossier
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:17:  - exact canonical route loads 0.8.2-quiz-gated-single-dossier-001
/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml:20:commit_message: Gate final dossier behind completed Spark quiz
/workspace/runtime/tasks/websites/force_legacy_html_route_redirects_001.yaml:2:title: Force legacy Emergence HTML route redirects
/workspace/runtime/tasks/websites/force_legacy_html_route_redirects_001.yaml:8:    - /spark-generator.html -> /spark-generator/
/workspace/runtime/tasks/websites/force_legacy_html_route_redirects_001.yaml:16:  - /spark-generator.html does not land as stale page
/workspace/runtime/tasks/websites/force_legacy_html_route_redirects_001.yaml:17:  - /spark-generator.html reaches /spark-generator/
/workspace/runtime/tasks/websites/force_legacy_html_route_redirects_001.yaml:19:commit_message: Force legacy Emergence HTML route redirects
/workspace/runtime/deploy/hostinger_plugin_registry.yaml:12:      - dadudekc.com
/workspace/runtime/deploy/hostinger_plugin_registry.yaml:76:      - dadudekc.com
/workspace/runtime/deploy/hostinger_plugin_registry.yaml:94:      - dadudekc.com
/workspace/runtime/deploy/hostinger_plugin_registry.yaml:112:      - dadudekc.com
/workspace/runtime/deploy/hostinger_plugin_registry.yaml:142:      - dadudekc.com
/workspace/runtime/deploy/hostinger_plugin_registry.yaml:217:      - dadudekc.com
/workspace/runtime/tasks/websites/fix_spark_two_pass_layout_and_dossier_gate_001.yaml:6:  canonical_url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/fix_spark_two_pass_layout_and_dossier_gate_001.yaml:9:  - collapse hidden inactive quiz panels that create mobile blank space
/workspace/runtime/tasks/websites/fix_spark_two_pass_layout_and_dossier_gate_001.yaml:12:  - show Build Final Dossier only after visible active quiz phase is complete
/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml:1:id: fix_spark_quiz_freeze_observer_loop_001
/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml:2:title: Fix Spark quiz freeze from dossier observer loop
/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml:6:  canonical_url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml:7:  asset_version: 0.8.5-quiz-freeze-observer-fix-001
/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml:17:  - canonical route loads 0.8.5-quiz-freeze-observer-fix-001
/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml:20:commit_message: Fix Spark quiz freeze observer loop
/workspace/runtime/deploy/hostinger_manager_smoke_matrix.yaml:29:  dadudekc_com:
/workspace/runtime/deploy/hostinger_manager_smoke_matrix.yaml:30:    domain: 'dadudekc.com'
/workspace/runtime/deploy/hostinger_manager_smoke_matrix.yaml:31:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/dadudekc.com.env'
/workspace/runtime/tasks/extract_hostinger_ssh_connection_from_emergence_lane_001.yaml:2:title: Extract Hostinger SSH connection from Emergence lane
/workspace/runtime/tasks/extract_hostinger_ssh_connection_from_emergence_lane_001.yaml:8:  Extract SSH/SFTP connection metadata from existing Emergence/dadudekc Hostinger lane without printing secrets.
/workspace/runtime/tasks/websites/fix_spark_generate_payload_hardening_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/fix_original_spark_renderer_q11_branch_001.yaml:6:  url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/fix_original_spark_renderer_q11_branch_001.yaml:8:  baseline: 0.8.5-quiz-freeze-observer-fix-001
/workspace/runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:108:    domain: dadudekc.com
/workspace/runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:109:    slug: dadudekc
/workspace/runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:110:    remote_path: /home/u996867598/domains/dadudekc.com/public_html/wp-content/themes/dadudekc
/workspace/runtime/deploy/hostinger_custom_asset_collection_manifest.yaml:111:    local_path: collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc
/workspace/runtime/tasks/websites/fix_live_wp_emergence_route_links_001.yaml:2:title: Fix live WordPress Emergence route links
/workspace/runtime/tasks/websites/fix_live_wp_emergence_route_links_001.yaml:9:  - run wp-cli search-replace for Emergence routes
/workspace/runtime/tasks/websites/fix_live_wp_emergence_route_links_001.yaml:13:  - /spark-generator.html absent from live pages
/workspace/runtime/tasks/websites/fix_live_wp_emergence_route_links_001.yaml:14:  - /spark-generator/ present in live pages
/workspace/runtime/tasks/websites/fix_live_wp_emergence_route_links_001.yaml:16:commit_message: Fix live WordPress Emergence route links
/workspace/runtime/tasks/websites/fix_live_generate_button_route_all_sources_001.yaml:7:  stale_route: /spark-generator.html
/workspace/runtime/tasks/websites/fix_live_generate_button_route_all_sources_001.yaml:8:  correct_route: /spark-generator/
/workspace/runtime/tasks/websites/fix_live_generate_button_route_all_sources_001.yaml:17:  - live homepage has no spark-generator.html
/workspace/runtime/tasks/websites/fix_live_generate_button_route_all_sources_001.yaml:18:  - live homepage has /spark-generator/
/workspace/runtime/deploy/hostinger_connected_sites.yaml:31:  dadudekc_com:
/workspace/runtime/deploy/hostinger_connected_sites.yaml:32:    domain: 'dadudekc.com'
/workspace/runtime/deploy/hostinger_connected_sites.yaml:33:    env_file_local: '/data/data/com.termux/files/home/projects/websites/runtime/env/hostinger/sites/dadudekc.com.env'
/workspace/runtime/deploy/hostinger_connected_sites.yaml:34:    wp_root: '/home/u996867598/domains/dadudekc.com/public_html'
/workspace/runtime/deploy/hostinger_connected_sites.yaml:35:    plugins_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/plugins'
/workspace/runtime/deploy/hostinger_connected_sites.yaml:36:    themes_dir: '/home/u996867598/domains/dadudekc.com/public_html/wp-content/themes'
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_safe_001.yaml:6:  exact_url: https://maskzero.site/spark-generator
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_safe_001.yaml:7:  canonical_route: /spark-generator
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_safe_001.yaml:16:  - https://maskzero.site/spark-generator loads 0.8.1-canonical-route-nostore-001
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_001.yaml:6:  exact_url: https://maskzero.site/spark-generator
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_001.yaml:7:  canonical_route: /spark-generator
/workspace/runtime/tasks/websites/fix_exact_spark_generator_route_cache_001.yaml:16:  - https://maskzero.site/spark-generator loads 0.8.1-canonical-route-nostore-001
/workspace/runtime/tasks/websites/fix_emergence_wordpress_route_links_001.yaml:2:title: Fix Emergence WordPress route links
/workspace/runtime/tasks/websites/fix_emergence_wordpress_route_links_001.yaml:10:  - verify no stale spark-generator.html CTAs remain
/workspace/runtime/tasks/websites/fix_emergence_wordpress_route_links_001.yaml:13:  - no /spark-generator.html links
/workspace/runtime/tasks/websites/fix_emergence_wordpress_route_links_001.yaml:16:  - /spark-generator/ links present
/workspace/runtime/tasks/websites/fix_emergence_wordpress_route_links_001.yaml:18:commit_message: Fix Emergence WordPress route links
/workspace/runtime/tasks/websites/fix_emergence_plugin_asset_permissions_001.yaml:2:title: Fix Emergence plugin asset permissions
/workspace/runtime/tasks/websites/fix_emergence_plugin_asset_permissions_001.yaml:13:  - live JS contains DreamOS Spark Generator Fail-Open Guard
/workspace/runtime/tasks/websites/fix_emergence_plugin_asset_permissions_001.yaml:14:  - live CSS contains DreamOS Spark Generator Fail-Open Visibility Guard
/workspace/runtime/tasks/websites/fix_emergence_plugin_asset_permissions_001.yaml:16:commit_message: Fix Emergence plugin asset permission verification
/workspace/runtime/tasks/websites/fix_canonical_spark_select_options_clickability_001.yaml:6:  canonical_url: https://maskzero.site/spark-generator/
/workspace/runtime/tasks/websites/discover_spark_protocol_spine_001.yaml:2:title: Discover Spark Protocol spine for Emergence
/workspace/runtime/tasks/websites/discover_spark_protocol_spine_001.yaml:7:  project: Emergence
/workspace/runtime/deploy/hostinger/parked_domain_static_placeholder_pack_manifest.yaml:23:    - dadudekc.com
/workspace/runtime/tasks/emergence/repair_emergence_mobile_visual_system_001.yaml:15:commit_message: Repair Emergence mobile visual system
/workspace/runtime/tasks/websites/discover_remote_path_and_deploy_spark_assets_001.yaml:14:  - live JS contains DreamOS Spark Generator Fail-Open Guard
/workspace/runtime/tasks/websites/discover_remote_path_and_deploy_spark_assets_001.yaml:15:  - live CSS contains DreamOS Spark Generator Fail-Open Visibility Guard
/workspace/runtime/tasks/websites/diagnose_spark_generator_blank_001.yaml:7:  route: /spark-generator/
/workspace/runtime/tasks/websites/diagnose_spark_generator_blank_001.yaml:11:  - probe Emergence REST endpoints
/workspace/runtime/tasks/emergence/repair_dreamos_theme_placeholder_guard_001.yaml:7:  - Keep custom DreamOS Emergence theme active.
/workspace/runtime/tasks/emergence/repair_dreamos_theme_placeholder_guard_001.yaml:13:  - /spark-generator/ returns HTTP 200.
/workspace/runtime/tasks/websites/deploy_spark_generator_fail_open_assets_001.yaml:17:  - live JS contains DreamOS Spark Generator Fail-Open Guard
/workspace/runtime/tasks/websites/deploy_spark_generator_fail_open_assets_001.yaml:18:  - live CSS contains DreamOS Spark Generator Fail-Open Visibility Guard
/workspace/runtime/deploy/hostinger/hostinger_access_registry_manifest.yaml:12:  - dadudekc.com
/workspace/runtime/tasks/emergence/repair_dadudekc_live_theme_content_cache_001.yaml:1:id: repair_dadudekc_live_theme_content_cache_001
/workspace/runtime/tasks/emergence/repair_dadudekc_live_theme_content_cache_001.yaml:15:commit_message: Repair dadudekc live theme/content/cache mismatch
/workspace/runtime/tasks/websites/deploy_spark_assets_native_sftp_001.yaml:12:  - live JS contains DreamOS Spark Generator Fail-Open Guard
/workspace/runtime/tasks/websites/deploy_spark_assets_native_sftp_001.yaml:13:  - live CSS contains DreamOS Spark Generator Fail-Open Visibility Guard
/workspace/runtime/tasks/emergence/repair_dadudekc_block_template_overrides_001.yaml:1:id: repair_dadudekc_block_template_overrides_001
/workspace/runtime/tasks/emergence/repair_dadudekc_block_template_overrides_001.yaml:11:  - Front page is The Emergence page.
/workspace/runtime/tasks/emergence/repair_dadudekc_block_template_overrides_001.yaml:15:commit_message: Repair dadudekc block template overrides
/workspace/runtime/tasks/websites/deploy_spark_assets_all_aliases_and_verify_001.yaml:15:  - remote JS aliases contain DreamOS Spark Generator Fail-Open Guard
/workspace/runtime/tasks/websites/deploy_spark_assets_all_aliases_and_verify_001.yaml:16:  - remote CSS aliases contain DreamOS Spark Generator Fail-Open Visibility Guard
/workspace/runtime/deploy/hostinger/domain_purpose_decision_matrix_manifest.yaml:14:  dadudekc_com_hold_policy: pass
/workspace/runtime/deploy/hostinger/domain_purpose_decision_matrix_manifest.yaml:18:  dadudekc_com_repair_target: false
/workspace/runtime/tasks/emergence/remove_duplicate_emergence_page_shell_001.yaml:6:  - Remove page-level nav/footer from The Emergence content.
/workspace/runtime/tasks/emergence/remove_duplicate_emergence_page_shell_001.yaml:12:  - Root, page, spark-generator, and spark-battle-sim routes return HTTP 200.
/workspace/runtime/tasks/emergence/remove_duplicate_emergence_page_shell_001.yaml:14:commit_message: Remove duplicate Emergence page shell
/workspace/runtime/tasks/websites/deploy_emergence_spark_loop_live_001.yaml:2:title: Deploy Emergence Spark loop and verify live DOM
/workspace/runtime/tasks/websites/deploy_emergence_spark_loop_live_001.yaml:10:  - deploy updated Emergence page content
/workspace/runtime/tasks/websites/deploy_emergence_spark_loop_live_001.yaml:18:commit_message: Deploy Emergence Spark loop live verification
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:1:# MaskZero Quiz Migration Audit
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:7:MaskZero quiz should use the same quiz page from the dadudekc.site migration.
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:9:The stale /spark-generator/ route should be replaced, aliased, or rebuilt from that migrated quiz source.
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:13:### maskzero.site/spark-generator
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:26:x-spark-site: dadudekc.site
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:31:### maskzero.site/quiz
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:47:### dadudekc.site quiz candidates
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:52:location: https://maskzero.site/quiz/
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:73:location: https://maskzero.site/spark-generator/
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:90:x-spark-site: dadudekc.site
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:96:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/archive-note.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:97:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/archive.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:98:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/archive-project.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:99:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/DYNAMIC_CONTENT_SYSTEM.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:100:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/font-corruption-fix.css
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:101:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/footer.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:102:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/front-page.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:103:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/functions.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:104:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/header.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:105:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/home.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:106:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/functions/proof-metrics.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:107:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/experiment.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:108:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/icp-definition.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:109:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/note.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:110:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/offer-ladder.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:111:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/project.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:112:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/inc/post-types/resume-item.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:113:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/index.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:114:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-blog.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:115:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-contact.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:116:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-idea-lab.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:117:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-now.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:118:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:119:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/page-portfolio.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:120:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/search.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:121:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/single-note.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:122:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/single.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:123:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/single-project.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:124:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/style.css
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:125:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/experiments-feed.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:126:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/icp-definition.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:127:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/offer-ladder.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:128:/workspace/collected/hostinger/wordpress/domains/dadudekc.com/themes/dadudekc/template-parts/components/project-demos.php
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:130:/workspace/dadudekc-service-funnel/dadudekc website/wp-content/themes/dadudekc/style.css
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:144:/workspace/data/reports/websites/emergence/fix_spark_quiz_freeze_observer_loop_001.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:146:/workspace/data/reports/websites/emergence/gate_final_dossier_single_button_after_quiz_001.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:155:/workspace/data/reports/websites/emergence/replace_spark_quiz_with_canonical_renderer_001.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:158:/workspace/data/reports/websites/emergence/show_final_dossier_only_after_quiz_complete_001.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:162:/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164740.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:163:/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:164:/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:167:/workspace/_hostinger_plan/dadudekc/install_checklist.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:169:/workspace/_reports/dadudekc_spark_preview_pages_064.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:205:/workspace/_reports/force_dadudekc_public_root_spark_os_001.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:244:/workspace/runtime/content/dadudekc.site/index.html
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:277:/workspace/runtime/quarantine/tmp_runtime_artifacts_001/_tmp/force_dadudekc_public_root_spark_os_001/spark.html
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:286:/workspace/runtime/tasks/emergence/force_dadudekc_public_root_spark_os_001.yaml
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:313:/workspace/runtime/tasks/websites/fix_spark_quiz_freeze_observer_loop_001.yaml
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:315:/workspace/runtime/tasks/websites/gate_final_dossier_single_button_after_quiz_001.yaml
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:325:/workspace/runtime/tasks/websites/replace_spark_quiz_with_canonical_renderer_001.yaml
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:328:/workspace/runtime/tasks/websites/show_final_dossier_only_after_quiz_complete_001.yaml
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:334:/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/map-grid.json
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:335:/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/missions.json
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:336:/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/data/world.json
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:337:/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/index.html
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:338:/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/meridian-dispatch.css
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:339:/workspace/sites/production/websites/dadudekc.site/meridian-dispatch/meridian-dispatch.js
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:343:## Local MaskZero Target Candidates
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:345:/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164740.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:346:/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:347:/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164804.md:365:/workspace/runtime/content/maskzero.site/spark-generator/index.html
/workspace/runtime/tasks/emergence/redesign_emergence_premium_spark_os_001.yaml:13:  - Root URL shows new Emergence design.
/workspace/runtime/tasks/emergence/redesign_emergence_premium_spark_os_001.yaml:16:commit_message: Redesign Emergence homepage as premium Spark OS interface
/workspace/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml:2:title: Deploy Emergence plugin PHP and assets
/workspace/runtime/tasks/websites/deploy_emergence_plugin_php_and_assets_001.yaml:17:commit_message: Deploy Emergence plugin PHP and cache-busted assets
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:1:# MaskZero Quiz Migration Audit
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:7:MaskZero quiz should use the same quiz page from the dadudekc.site migration.
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:9:The stale /spark-generator/ route should be replaced, aliased, or rebuilt from that migrated quiz source.
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:13:### maskzero.site/spark-generator
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:26:x-spark-site: dadudekc.site
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:31:### maskzero.site/quiz
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:47:### dadudekc.site quiz candidates
/workspace/data/reports/websites/maskzero_quiz_migration_audit_20260613_164751.md:52:location: https://maskzero.site/quiz/

## Decision

- dadudekc.site migrated quiz is canonical source.
- Git history confirms the source path was `runtime/content/dadudekc.site/spark-generator/index.html` before commit `b82e44e8bfefa05e96673ed4f66d99f8c897f711` moved the project to `runtime/content/maskzero.site/spark-generator/index.html`.
- Current dadudekc.site repo content is intentionally reduced to redirect shell files, so the repair should copy/rebrand the migrated MaskZero-held source rather than restoring public dadudekc route files.
- maskzero.site/quiz/ should become primary route.
- maskzero.site/spark-generator/ should become alias or rebuilt copy.
- Do not patch deploy output until canonical source and deployer are confirmed.
