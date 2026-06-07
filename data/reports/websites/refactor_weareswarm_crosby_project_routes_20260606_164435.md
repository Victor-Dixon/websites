# Refactor WeAreSwarm Crosby Project Routes

generated=2026-06-06T16:44:35-05:00
root=/data/data/com.termux/files/home/projects/websites

== WRITE HOMEPAGE WITH CROSBY CARD SECTION ==
== WRITE PROJECTS INDEX ==
== WRITE CROSBY DETAIL UNDER /projects/crosbyultimateevents/ ==
== WRITE COMPATIBILITY REDIRECT OLD ROUTE ==
== LOCAL VERIFY ==
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html:40:        <a href="/feed/">Proof Feed</a>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html:47:      <h1>We ship useful systems, not vague promises.</h1>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html:51:        <a class="btn" href="/feed/">Open proof feed</a>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html:60:          <h3>Crosby Ultimate Events</h3>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html:64:            <a class="btn primary" href="/projects/crosbyultimateevents/">View proof card</a>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html:70:          <span class="tag">Proof feed</span>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/index.html:18:      <h2>Crosby Ultimate Events</h2>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/index.html:20:      <p><a class="btn" href="/projects/crosbyultimateevents/">View Crosby proof card</a></p>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html:5:  <title>Crosby Ultimate Events | WeAreSwarm Project Proof</title>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html:17:    <h1>Crosby Ultimate Events moved from broken WordPress to live lead capture.</h1>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html:46:      <p><a href="/feed/">WeAreSwarm proof feed</a></p>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/crosbyultimateevents/index.html:5:  <meta http-equiv="refresh" content="0; url=/projects/crosbyultimateevents/">
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/crosbyultimateevents/index.html:6:  <link rel="canonical" href="/projects/crosbyultimateevents/">
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/crosbyultimateevents/index.html:7:  <title>Redirecting | Crosby Ultimate Events</title>
/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/crosbyultimateevents/index.html:10:  <p>Redirecting to <a href="/projects/crosbyultimateevents/">Crosby Ultimate Events project proof</a>.</p>
== DEPLOY TO WEARESWARM.ONLINE ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
REMOTE_DEPLOY=PASS
== LIVE VERIFY ==
--- https://weareswarm.online/ ---
      <h1>We ship useful systems, not vague promises.</h1>
          <h3>Crosby Ultimate Events</h3>
          <span class="tag">Proof feed</span>
--- https://weareswarm.online/projects/ ---
      <h2>Crosby Ultimate Events</h2>
--- https://weareswarm.online/projects/crosbyultimateevents/ ---
  <title>Crosby Ultimate Events | WeAreSwarm Project Proof</title>
    <h1>Crosby Ultimate Events moved from broken WordPress to live lead capture.</h1>
    <p>OWNER=Jacori Crosby</p>
--- https://weareswarm.online/crosbyultimateevents/ ---
  <title>Redirecting | Crosby Ultimate Events</title>
  <p>Redirecting to <a href="/projects/crosbyultimateevents/">Crosby Ultimate Events project proof</a>.</p>
== GIT STATUS ==
 M _deploy/weareswarm/crosbyultimateevents/index.html
 M _deploy/weareswarm/index.html
 M _deploy/weareswarm/projects/index.html
?? _deploy/weareswarm/projects/crosbyultimateevents/
?? data/reports/websites/emergence/tmp/
?? data/reports/websites/refactor_weareswarm_crosby_project_routes_20260606_164435.md
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
STATUS=WEARESWARM_CROSBY_PROJECT_CARD_REFACTORED
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/refactor_weareswarm_crosby_project_routes_20260606_164435.md
