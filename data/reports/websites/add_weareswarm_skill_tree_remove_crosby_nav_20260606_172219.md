# Add WeAreSwarm Skill Tree and Remove Crosby Global Nav

generated=2026-06-06T17:22:19-05:00
root=/data/data/com.termux/files/home/projects/websites

== WRITE SKILL TREE PAGE ==
== NORMALIZE GLOBAL NAV REMOVE CROSBY PROOF ADD SKILL TREE ==
== LOCAL VERIFY ==
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html ---
6:  <title>WeAreSwarm | Dream.OS Command Center</title>
116:        <a href="/" aria-current="page">Command Center</a>
117:        <a href="/feed/">Feed</a>
118:        <a href="/projects/">Projects</a>
119:        <a href="/tasks/">Tasks</a>
120:        <a href="/skill-tree/">Skill Tree</a>
121:        <a href="/dreamos-services/">Services</a>
126:      <div class="eyebrow">Dream.OS Command Center</div>
175:          <span class="tag">Tasks</span>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/feed/index.html ---
6:  <title>Closeout Feed | WeAreSwarm</title>
119:        <a href="/">Command Center</a>
120:        <a href="/feed/" aria-current="page">Feed</a>
121:        <a href="/projects/">Projects</a>
122:        <a href="/tasks/">Tasks</a>
123:        <a href="/skill-tree/">Skill Tree</a>
124:        <a href="/dreamos-services/">Services</a>
129:      <div class="eyebrow">Dream.OS Closeout Feed</div>
152:            <h3>WeAreSwarm reframed as Dream.OS Command Center</h3>
154:              <span class="tag">Command Center</span>
232:      <h2>Feed lanes</h2>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/index.html ---
6:  <title>Projects | WeAreSwarm</title>
122:        <a href="/">Command Center</a>
123:        <a href="/feed/">Feed</a>
124:        <a href="/projects/" aria-current="page">Projects</a>
125:        <a href="/tasks/">Tasks</a>
126:        <a href="/skill-tree/">Skill Tree</a>
127:        <a href="/dreamos-services/">Services</a>
164:            <span class="tag">Command Center</span>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/tasks/index.html ---
6:  <title>Tasks | WeAreSwarm</title>
122:        <a href="/">Command Center</a>
123:        <a href="/feed/">Feed</a>
124:        <a href="/projects/">Projects</a>
125:        <a href="/tasks/" aria-current="page">Tasks</a>
126:        <a href="/skill-tree/">Skill Tree</a>
127:        <a href="/dreamos-services/">Services</a>
157:          <h3>WeAreSwarm Command Center</h3>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/skill-tree/index.html ---
6:  <title>Skill Tree | WeAreSwarm</title>
47:        <a href="/">Command Center</a>
48:        <a href="/feed/">Feed</a>
49:        <a href="/projects/">Projects</a>
50:        <a href="/tasks/">Tasks</a>
51:        <a href="/skill-tree/" aria-current="page">Skill Tree</a>
52:        <a href="/dreamos-services/">Services</a>
57:      <div class="eyebrow">Dream.OS Skill Tree</div>
58:      <h1>Capabilities unlocked by shipped work.</h1>
141:      Skill Tree: Dream.OS capabilities earned through verified closeouts.
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html ---
34:      <a href="/">Command Center</a>
35:      <a href="/feed/">Feed</a>
36:      <a href="/projects/">Projects</a>
37:      <a href="/tasks/">Tasks</a>
38:      <a href="/skill-tree/">Skill Tree</a>
39:      <a href="/dreamos-services/">Services</a>
== ASSERT GLOBAL NAV HAS NO CROSBY PROOF ==
GLOBAL_NAV_CROSBY_REMOVED=PASS
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
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Skill Tree
NAV_ITEM=PASS:Services
--- https://weareswarm.online/feed/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Skill Tree
NAV_ITEM=PASS:Services
--- https://weareswarm.online/projects/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Skill Tree
NAV_ITEM=PASS:Services
--- https://weareswarm.online/tasks/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Skill Tree
NAV_ITEM=PASS:Services
--- https://weareswarm.online/skill-tree/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Skill Tree
NAV_ITEM=PASS:Services
--- https://weareswarm.online/projects/crosbyultimateevents/ ---
NAV_ITEM=PASS:Command Center
NAV_ITEM=PASS:Feed
NAV_ITEM=PASS:Projects
NAV_ITEM=PASS:Tasks
NAV_ITEM=PASS:Skill Tree
NAV_ITEM=PASS:Services
== LIVE SKILL TREE CONTENT VERIFY ==
      <div class="eyebrow">Dream.OS Skill Tree</div>
      <h1>Capabilities unlocked by shipped work.</h1>
      <h2>Unlocked capabilities</h2>
      <h2>Next unlocks</h2>
      <h2>Locked until proven</h2>
== GIT STATUS ==
 M _deploy/weareswarm/crosbyultimateevents/index.html
 M _deploy/weareswarm/feed/index.html
 M _deploy/weareswarm/index.html
 M _deploy/weareswarm/projects/crosbyultimateevents/index.html
 M _deploy/weareswarm/projects/index.html
 M _deploy/weareswarm/tasks/index.html
?? _deploy/weareswarm/skill-tree/
?? data/reports/websites/add_weareswarm_skill_tree_remove_crosby_nav_20260606_172219.md
?? data/reports/websites/emergence/tmp/
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
STATUS=WEARESWARM_SKILL_TREE_ADDED_CROSBY_NAV_REMOVED
REPORT=/data/data/com.termux/files/home/projects/websites/data/reports/websites/add_weareswarm_skill_tree_remove_crosby_nav_20260606_172219.md
