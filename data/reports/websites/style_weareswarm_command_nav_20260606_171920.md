# Style WeAreSwarm Command Nav

generated=2026-06-06T17:19:20-05:00
root=/data/data/com.termux/files/home/projects/websites

== PATCH NAV CSS ACROSS STATIC PAGES ==
== LOCAL VERIFY ==
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html ---
20:    /* Dream.OS command nav */
34:      backdrop-filter:blur(16px);
35:      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
52:      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
81:    .links a[aria-current="page"]{
85:      box-shadow:0 0 24px rgba(120,240,195,.24);
98:    .card{background:linear-gradient(180deg,rgba(255,255,255,.058),rgba(255,255,255,.025));border:1px solid var(--line);border-radius:22px;padding:21px;box-shadow:0 18px 44px rgba(0,0,0,.24)}
116:        <a href="/" aria-current="page">Command Center</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/feed/index.html ---
18:    /* Dream.OS command nav */
32:      backdrop-filter:blur(16px);
33:      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
50:      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
79:    .links a[aria-current="page"]{
83:      box-shadow:0 0 24px rgba(120,240,195,.24);
120:        <a href="/feed/" aria-current="page">Feed</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/index.html ---
18:    /* Dream.OS command nav */
32:      backdrop-filter:blur(16px);
33:      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
50:      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
79:    .links a[aria-current="page"]{
83:      box-shadow:0 0 24px rgba(120,240,195,.24);
124:        <a href="/projects/" aria-current="page">Projects</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/tasks/index.html ---
18:    /* Dream.OS command nav */
32:      backdrop-filter:blur(16px);
33:      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
50:      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
79:    .links a[aria-current="page"]{
83:      box-shadow:0 0 24px rgba(120,240,195,.24);
125:        <a href="/tasks/" aria-current="page">Tasks</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html ---
16:    .command-nav{
19:      border-radius:24px;background:rgba(5,7,12,.78);box-shadow:0 18px 60px rgba(0,0,0,.24);
21:    .command-nav a{
26:    .command-nav a:hover{color:var(--text);border-color:rgba(120,240,195,.45);background:rgba(120,240,195,.09);text-decoration:none}
27:    .command-nav a[aria-current="page"]{color:#04100c;background:var(--accent);border-color:var(--accent)}
33:    <nav class="command-nav" aria-label="WeAreSwarm command navigation">
38:      <a href="/projects/crosbyultimateevents/" aria-current="page">Crosby Proof</a>
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
    /* Dream.OS command nav */
      backdrop-filter:blur(16px);
      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
    .links a[aria-current="page"]{
      box-shadow:0 0 24px rgba(120,240,195,.24);
    .card{background:linear-gradient(180deg,rgba(255,255,255,.058),rgba(255,255,255,.025));border:1px solid var(--line);border-radius:22px;padding:21px;box-shadow:0 18px 44px rgba(0,0,0,.24)}
        <a href="/" aria-current="page">Command Center</a>
--- https://weareswarm.online/feed/ ---
    /* Dream.OS command nav */
      backdrop-filter:blur(16px);
      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
    .links a[aria-current="page"]{
      box-shadow:0 0 24px rgba(120,240,195,.24);
        <a href="/feed/" aria-current="page">Feed</a>
--- https://weareswarm.online/projects/ ---
    /* Dream.OS command nav */
      backdrop-filter:blur(16px);
      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
    .links a[aria-current="page"]{
      box-shadow:0 0 24px rgba(120,240,195,.24);
        <a href="/projects/" aria-current="page">Projects</a>
--- https://weareswarm.online/tasks/ ---
    /* Dream.OS command nav */
      backdrop-filter:blur(16px);
      box-shadow:0 18px 60px rgba(0,0,0,.34), inset 0 1px 0 rgba(255,255,255,.05);
      box-shadow:0 0 0 5px rgba(120,240,195,.12),0 0 24px rgba(120,240,195,.68);
    .links a[aria-current="page"]{
      box-shadow:0 0 24px rgba(120,240,195,.24);
        <a href="/tasks/" aria-current="page">Tasks</a>
--- https://weareswarm.online/projects/crosbyultimateevents/ ---
    .command-nav{
      border-radius:24px;background:rgba(5,7,12,.78);box-shadow:0 18px 60px rgba(0,0,0,.24);
    .command-nav a{
    .command-nav a:hover{color:var(--text);border-color:rgba(120,240,195,.45);background:rgba(120,240,195,.09);text-decoration:none}
    .command-nav a[aria-current="page"]{color:#04100c;background:var(--accent);border-color:var(--accent)}
    <nav class="command-nav" aria-label="WeAreSwarm command navigation">
      <a href="/projects/crosbyultimateevents/" aria-current="page">Crosby Proof</a>
== GIT STATUS ==
 M _deploy/weareswarm/feed/index.html
 M _deploy/weareswarm/index.html
 M _deploy/weareswarm/projects/crosbyultimateevents/index.html
 M _deploy/weareswarm/projects/index.html
 M _deploy/weareswarm/tasks/index.html
?? data/reports/websites/emergence/tmp/
?? data/reports/websites/style_weareswarm_command_nav_20260606_171920.md
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
STATUS=WEARESWARM_COMMAND_NAV_STYLED
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/style_weareswarm_command_nav_20260606_171920.md
