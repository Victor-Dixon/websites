# Patch WeAreSwarm Crosby Proof Nav

generated=2026-06-06T17:16:58-05:00
root=/data/data/com.termux/files/home/projects/websites

== PRECHECK ==
TARGET=PASS:/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html
== PATCH CROSBY PROOF DETAIL NAV ==
== LOCAL VERIFY ==
      <a href="/">Command Center</a> ·
      <a href="/feed/">Feed</a> ·
      <a href="/projects/">Projects</a> ·
      <a href="/tasks/">Tasks</a> ·
      <a href="/projects/crosbyultimateevents/">Crosby Proof</a> ·
      <a href="/dreamos-services/">Services</a>
== DEPLOY SINGLE PAGE ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
REMOTE_DEPLOY=PASS
== LIVE VERIFY ALL NAV ==
--- https://weareswarm.online/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Crosby Proof
--- https://weareswarm.online/feed/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Crosby Proof
--- https://weareswarm.online/projects/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Crosby Proof
--- https://weareswarm.online/projects/crosbyultimateevents/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Crosby Proof
--- https://weareswarm.online/tasks/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Crosby Proof
--- https://weareswarm.online/crosbyultimateevents/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Crosby Proof
== GIT STATUS ==
 M _deploy/weareswarm/crosbyultimateevents/index.html
 M _deploy/weareswarm/feed/index.html
 M _deploy/weareswarm/index.html
 M _deploy/weareswarm/projects/crosbyultimateevents/index.html
 M _deploy/weareswarm/projects/index.html
?? _deploy/weareswarm/tasks/
?? data/reports/websites/create_weareswarm_tasks_command_route_20260606_171527.md
?? data/reports/websites/emergence/tmp/
?? data/reports/websites/normalize_weareswarm_nav_20260606_171614.md
?? data/reports/websites/patch_weareswarm_crosby_proof_nav_20260606_171658.md
?? runtime/tasks/websites/deploy_spark_assets_native_sftp_001.yaml
?? runtime/tasks/websites/deploy_spark_generator_fail_open_assets_001.yaml
?? runtime/tasks/websites/discover_remote_path_and_deploy_spark_assets_001.yaml
?? runtime/tasks/websites/fix_exact_spark_generator_route_cache_001.yaml
?? runtime/tasks/websites/fix_spark_generate_payload_hardening_001.yaml
?? runtime/tasks/websites/fix_spark_os_static_button_handlers_001.yaml
?? runtime/tasks/websites/patch_client_payload_hardening_allow_answers_001.yaml
?? runtime/tasks/websites/point_generate_ctas_to_versioned_spark_route_001.yaml
?? runtime/tasks/websites/revert_versioned_spark_route_drift_001.yaml
?? runtime/tasks/websites/verify_exact_canonical_spark_generator_route_001.yaml
== CLOSEOUT ==
STATUS=WEARESWARM_CROSBY_PROOF_NAV_PATCHED
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/patch_weareswarm_crosby_proof_nav_20260606_171658.md
