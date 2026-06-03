# Legacy Runtime Reports Review 001

- Generated: `2026-06-03T16:55:42`
- Status: `REVIEWED`
- Item count: `25`

## Unlock

The largest remaining websites blocker, `_reports/`, is now classified into keep/quarantine/deeper-review lanes.

## Decision Counts

- `KEEP_CANDIDATE`: `16`
- `QUARANTINE_CANDIDATE`: `9`

## Items

### `_reports/dadudekc_block_template_override_repair_001.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable website repair/promotion report evidence
- Risk flags: `none`
- Size: `868`

### `_reports/dadudekc_theme_placeholder_cleanup_002.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable website repair/promotion report evidence
- Risk flags: `none`
- Size: `675`

### `_reports/dreamos_theme_placeholder_guard_repair_001.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable website repair/promotion report evidence
- Risk flags: `none`
- Size: `918`

### `_reports/emergence_character_generator_render_072.md.html`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `73106`

### `_reports/emergence_character_sheet_output_081.md.html`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `possible_sensitive_surface`
- Size: `132065`

### `_reports/emergence_page_design_registry_001.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `660`

### `_reports/emergence_premium_spark_os_redesign_001.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `911`

### `_reports/force_dadudekc_public_root_spark_os_001.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `754`

### `_reports/freeride_wp_config_repair_056.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable website repair/promotion report evidence
- Risk flags: `none`
- Size: `5771`

### `_reports/remote_configure_image_env_095d.sh`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `possible_sensitive_surface, shell_script_in_reports`
- Size: `3087`

### `_reports/spark_battle_engine_smoke_failure_014.err`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `error_log`
- Size: `0`

### `_reports/spark_battle_engine_smoke_failure_014.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `633`

### `_reports/spark_battle_shortcode_inspection_011.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `758`

### `_reports/spark_battle_shortcode_render_015.err`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `error_log`
- Size: `30`

### `_reports/spark_battle_sim_protocol_inventory_007.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `3291`

### `_reports/spark_battle_sim_protocol_inventory_008.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `31524`

### `_reports/spark_protocol_battle_sim_integration_006.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `723`

### `_reports/spark_protocol_exchange_ceiling_005.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `797`

### `_reports/spark_shortcode_battle_start_failure_016.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `590`

### `_reports/spark_shortcode_engine_direct_016.err`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `error_log`
- Size: `0`

### `_reports/spark_shortcode_exception_verify_017.err`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `error_log`
- Size: `0`

### `_reports/spark_shortcode_exception_verify_017.md`
- Decision: `KEEP_CANDIDATE`
- Reason: durable Emergence/Spark report evidence useful for site/plugin reconstruction
- Risk flags: `none`
- Size: `811`

### `_reports/spark_shortcode_render_stub_fix_018.err`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `error_log`
- Size: `0`

### `_reports/spark_shortcode_result_shape_019.err`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `error_log`
- Size: `0`

### `_reports/spark_shortcode_template_compat_020.err`
- Decision: `QUARANTINE_CANDIDATE`
- Reason: report contains error/script/sensitive-surface signals; preserve outside active source
- Risk flags: `error_log`
- Size: `0`

## Next Lanes

### commit_legacy_runtime_keep_reports_001
- TARGET: KEEP_CANDIDATE _reports artifacts
- ACTION: stage only keep reports plus review metadata
- VERIFY: cached scope excludes quarantine and deeper-review reports
- COMMIT: Commit legacy runtime reports

### quarantine_legacy_runtime_risk_reports_001
- TARGET: QUARANTINE_CANDIDATE _reports artifacts
- ACTION: move risk/error/script reports into runtime/quarantine with manifest
- VERIFY: originals cleared, manifest references all moved files
- COMMIT: Quarantine legacy runtime risk reports
