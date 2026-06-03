# Probe WeAreSwarm Live Deploy Capability 001

## Artifact

- Source: `_deploy/weareswarm/dreamos-services/index.html`
- Manifest: `_deploy/weareswarm/dreamos-services/deploy-manifest.json`
- Target: `weareswarm:/dreamos-services/index.html`
- Guardrail: Do not overwrite homepage.

## Tool Availability

- ssh: /data/data/com.termux/files/usr/bin/ssh
- scp: /data/data/com.termux/files/usr/bin/scp
- sftp: /data/data/com.termux/files/usr/bin/sftp
- rsync: /data/data/com.termux/files/usr/bin/rsync
- curl: /data/data/com.termux/files/usr/bin/curl
- python: /data/data/com.termux/files/usr/bin/python

## Candidate Runtime Scripts

runtime/scripts/__pycache__/dreamos_site_deployer.cpython-313.pyc
runtime/scripts/__pycache__/hostinger_deploy_target_guard.cpython-313.pyc
runtime/scripts/__pycache__/hostinger_static_deploy_guarded.cpython-313.pyc
runtime/scripts/__pycache__/hostinger_wp_manager.cpython-313.pyc
runtime/scripts/__pycache__/validate_website_deploy_modes.cpython-313.pyc
runtime/scripts/audit_hostinger_website_inventory.sh
runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh
runtime/scripts/collect_hostinger_custom_assets_045.sh
runtime/scripts/dreamos_site_deployer.py
runtime/scripts/hostinger_access_preflight.sh
runtime/scripts/hostinger_deploy_target_guard.py
runtime/scripts/hostinger_static_deploy_guarded.py
runtime/scripts/hostinger_wp_manager.py
runtime/scripts/validate_website_deploy_modes.py

## Candidate Env Files

./_configs/hostinger_plan_config_013.json
./_deploy/weareswarm/dreamos-services/deploy-manifest.json
./_deploy/weareswarm/dreamos-services/index.html
./_hostinger_build/dist/dreamos-trading-tools-0.1.0.zip
./_hostinger_build/dist/dreamos-trading-tools-0.1.1.zip
./_hostinger_build/dist/freerideinvestor-content-engine-0.1.0.zip
./_hostinger_build/dist/freerideinvestor-static-site-0.1.0.tar.gz
./_hostinger_build/dist/freerideinvestor-static-site-0.1.0.zip
./_hostinger_build/dist/parked_domains/ariajet.site-placeholder-0.1.0.zip
./_hostinger_build/dist/parked_domains/crosbyultimateevents.com-placeholder-0.1.0.zip
./_hostinger_build/dist/parked_domains/houstonsipqueen.com-placeholder-0.1.0.zip
./_hostinger_build/dist/parked_domains/southwestsecret.com-placeholder-0.1.0.zip
./_hostinger_build/dist/spark-battle-sim-install-ready-001.zip
./_hostinger_build/emergence_comic_archive_preview_001.zip
./_hostinger_build/plugins/dadudekc-community-features/README.md
./_hostinger_build/plugins/dadudekc-community-features/dadudekc-community-features.php
./_hostinger_build/plugins/dadudekc-community-features/plugin_manifest.json
./_hostinger_build/plugins/dreamos-productivity-widgets/README.md
./_hostinger_build/plugins/dreamos-productivity-widgets/dreamos-productivity-widgets.php
./_hostinger_build/plugins/dreamos-productivity-widgets/plugin_manifest.json
./_hostinger_build/plugins/dreamos-trading-tools/README.md
./_hostinger_build/plugins/dreamos-trading-tools/dreamos-trading-tools.php
./_hostinger_build/plugins/dreamos-trading-tools/plugin_manifest.json
./_hostinger_build/plugins/freerideinvestor-content-engine/README.md
./_hostinger_build/plugins/freerideinvestor-content-engine/freerideinvestor-content-engine.php
./_hostinger_build/plugins/freerideinvestor-content-engine/plugin_manifest.json
./_hostinger_plan/dadudekc/install_checklist.md
./_hostinger_plan/freerideinvestor/hostinger_freeride_install_packet_026.md
./_hostinger_plan/freerideinvestor/hostinger_freeride_install_proof_026.md
./_hostinger_plan/freerideinvestor/install_checklist.md
./_hostinger_plan/freerideinvestor/tsla_command_center_hostinger_install_packet_001.md
./_hostinger_plan/freerideinvestor/upload_payload_static_tsla_command_center_001/tsla-command-center.html
./_hostinger_plan/freerideinvestor/upload_payload_tsla_command_center_001/dreamos-trading-tools-0.1.1.zip
./_hostinger_plan/tradingrobotplug/install_checklist.md
./_reports/emergence_hostinger_image_env_095.txt
./_reports/emergence_hostinger_image_env_095c.txt
./_reports/emergence_hostinger_image_env_095d.txt
./_reports/emergence_hostinger_image_env_095e.md
./_reports/emergence_hostinger_image_env_095e.txt
./_reports/freeride_sales_funnel_deploy_candidate_001.txt
./_reports/freerideinvestor_live_static_deploy_001.txt
./_reports/guarded_static_hostinger_deploy_helper_001.txt
./_reports/hostinger_access_preflight_002b.md
./_reports/hostinger_access_preflight_002b.txt
./_reports/hostinger_custom_asset_candidates_045.txt
./_reports/hostinger_custom_asset_collection_045.md
./_reports/hostinger_custom_asset_hashes_045.txt
./_reports/hostinger_deploy_target_guard_001.txt
./_reports/hostinger_domain_model_and_plugin_stage_002.json
./_reports/hostinger_domain_model_and_plugin_stage_002.md
./_reports/hostinger_manager_smoke_matrix_062.md
./_reports/hostinger_manager_smoke_matrix_062.txt
./_reports/hostinger_plugin_build_003.json
./_reports/hostinger_plugin_build_003.md
./_reports/hostinger_site_install_plan_013.json
./_reports/hostinger_site_install_plan_013.md
./_reports/hostinger_website_fleet_connection_061.md
./_reports/hostinger_wordpress_inventory_044.md
./_reports/hostinger_wordpress_raw_inventory_044.txt
./_reports/hostinger_wp_bootstrap_diagnostics_053.md
./_reports/hostinger_wp_config_bootstrap_054.md
./_reports/hostinger_wp_manager_051.md
./_reports/hostinger_wp_manager_harden_052.md
./_reports/shared_hostinger_plugin_stage_001.json
./_reports/shared_hostinger_plugin_stage_001.md
./_reports/spark_battle_sim_hostinger_install_097.md
./_reports/spark_battle_sim_hostinger_install_097.txt
./_reports/static_tsla_command_center_remote_deploy_001.txt
./_reports/streamlined_emergence_site_deployer_001.md
./_reports/tsla_command_center_hostinger_deploy_diagnosis_001.txt
./_reports/tsla_command_center_hostinger_install_packet_001.txt
./_reports/website_deploy_mode_registry_001.txt
./_reports/websites_hostinger_access_inspection_001.txt
./data/reports/website_promotions/package_weareswarm_dreamos_services_deploy_artifact_001.md
./data/reports/website_promotions/probe_weareswarm_live_deploy_capability_001.md
./data/reports/website_promotions/quarantine_hostinger_build_artifacts_001.json
./data/reports/website_promotions/quarantine_hostinger_build_artifacts_001.md
./data/reports/website_promotions/weareswarm_dreamos_services_deploy_target_001.json
./data/reports/website_promotions/weareswarm_dreamos_services_deploy_target_001.md
./runtime/config/website_deploy_modes.yaml
./runtime/deploy/custom_plugin_preservation_policy.yaml
./runtime/deploy/freerideinvestor_revenue_showcase.yaml
./runtime/deploy/hostinger/dadudekc_com_expired_reclassification_manifest.yaml
./runtime/deploy/hostinger/domain_purpose_decision_matrix_manifest.yaml
./runtime/deploy/hostinger/hostinger_access_registry_manifest.yaml
./runtime/deploy/hostinger/http_500_root_cause_audit_manifest.yaml
./runtime/deploy/hostinger/parked_domain_static_placeholder_pack_manifest.yaml
./runtime/deploy/hostinger/website_inventory_audit_manifest.yaml
./runtime/deploy/hostinger/website_inventory_classification_manifest.yaml
./runtime/deploy/hostinger_connected_sites.yaml
./runtime/deploy/hostinger_custom_asset_collection_manifest.yaml
./runtime/deploy/hostinger_deploy_proof_profile.yaml
./runtime/deploy/hostinger_manager_smoke_matrix.yaml
./runtime/deploy/hostinger_plugin_registry.yaml
./runtime/deploy/hostinger_sites_manifest.yaml
./runtime/deploy/hostinger_theme_registry.yaml
./runtime/deploy/theme_redeployment_policy.yaml
./runtime/scripts/__pycache__/dreamos_site_deployer.cpython-313.pyc
./runtime/scripts/__pycache__/hostinger_deploy_target_guard.cpython-313.pyc
./runtime/scripts/__pycache__/hostinger_static_deploy_guarded.cpython-313.pyc
./runtime/scripts/__pycache__/hostinger_wp_manager.cpython-313.pyc
./runtime/scripts/__pycache__/validate_website_deploy_modes.cpython-313.pyc
./runtime/scripts/audit_hostinger_website_inventory.sh
./runtime/scripts/ci_deploy_hostinger_freeride_plugins_028.sh
./runtime/scripts/collect_hostinger_custom_assets_045.sh
./runtime/scripts/dreamos_site_deployer.py
./runtime/scripts/hostinger_access_preflight.sh
./runtime/scripts/hostinger_deploy_target_guard.py
./runtime/scripts/hostinger_static_deploy_guarded.py
./runtime/scripts/hostinger_wp_manager.py
./runtime/scripts/validate_website_deploy_modes.py
./runtime/tasks/audit_hostinger_website_inventory_001.yaml
./runtime/tasks/emergence/add_streamlined_emergence_site_deployer_001.yaml
./runtime/tasks/emergence/deploy_emergence_comic_archive_production_001.yaml
./runtime/tasks/emergence/deploy_emergence_comic_archive_with_deployer_001.yaml
./runtime/tasks/emergence/deploy_emergence_comic_archive_with_deployer_002.yaml
./runtime/tasks/emergence/deploy_emergence_comic_archive_wp_page_001.yaml
./runtime/tasks/emergence/deploy_emergence_comic_archive_wp_page_002.yaml
./runtime/tasks/emergence/deploy_emergence_comic_archive_wp_page_003.yaml
./runtime/tasks/hostinger/add_guarded_static_hostinger_deploy_helper_001.yaml
./runtime/tasks/hostinger/add_hostinger_deploy_target_guard_001.yaml
./runtime/tasks/hostinger/add_website_deploy_mode_registry_001.yaml
./runtime/tasks/package_weareswarm_dreamos_services_deploy_artifact_001.yaml
./runtime/tasks/quarantine_hostinger_build_artifacts_001.yaml
./runtime/tasks/trading/add_tsla_command_center_hostinger_install_packet_001.yaml
./tools/create_hostinger_plan.sh

## Status

PROBE_ONLY
