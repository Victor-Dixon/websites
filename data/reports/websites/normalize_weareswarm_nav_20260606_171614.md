# Normalize WeAreSwarm Navigation

generated=2026-06-06T17:16:15-05:00
root=/data/data/com.termux/files/home/projects/websites

== PRECHECK FILES ==
FILE=PASS:/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html
FILE=PASS:/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/feed/index.html
FILE=PASS:/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/index.html
FILE=PASS:/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html
FILE=PASS:/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/tasks/index.html
FILE=PASS:/data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/crosbyultimateevents/index.html
== NORMALIZE NAV BLOCKS ==
== LOCAL NAV VERIFY ==
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/index.html ---
        <a href="/feed/">Feed</a>
        <a href="/projects/">Projects</a>
        <a href="/tasks/">Tasks</a>
        <a href="/projects/crosbyultimateevents/">Crosby Proof</a>
        <a href="/dreamos-services/">Services</a>
        <a class="btn primary" href="/feed/">View closeout feed</a>
        <a class="btn" href="/projects/">View project proof</a>
            <a class="btn primary" href="/projects/crosbyultimateevents/">Open proof card</a>
            <a class="btn primary" href="/projects/crosbyultimateevents/">View project proof</a>
            <a class="btn" href="/feed/">Open feed</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/feed/index.html ---
        <a href="/feed/">Feed</a>
        <a href="/projects/">Projects</a>
        <a href="/tasks/">Tasks</a>
        <a href="/projects/crosbyultimateevents/">Crosby Proof</a>
        <a href="/dreamos-services/">Services</a>
        <a class="btn" href="/projects/">Project proof cards</a>
              <a class="btn" href="/projects/crosbyultimateevents/">View Crosby proof</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/index.html ---
        <a href="/feed/">Feed</a>
        <a href="/projects/">Projects</a>
        <a href="/tasks/">Tasks</a>
        <a href="/projects/crosbyultimateevents/">Crosby Proof</a>
        <a href="/dreamos-services/">Services</a>
            <a class="btn primary" href="/projects/crosbyultimateevents/">View proof card</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/projects/crosbyultimateevents/index.html ---
    <p><a href="/projects/">← Projects</a></p>
      <p><a href="/feed/">WeAreSwarm proof feed</a></p>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/tasks/index.html ---
        <a href="/feed/">Feed</a>
        <a href="/projects/">Projects</a>
        <a href="/tasks/">Tasks</a>
        <a href="/projects/crosbyultimateevents/">Crosby Proof</a>
        <a href="/dreamos-services/">Services</a>
--- /data/data/com.termux/files/home/projects/websites/_deploy/weareswarm/crosbyultimateevents/index.html ---
  <link rel="canonical" href="/projects/crosbyultimateevents/">
  <p>Redirecting to <a href="/projects/crosbyultimateevents/">Crosby Ultimate Events project proof</a>.</p>
    <a href="/feed/">Feed</a> ·
    <a href="/projects/">Projects</a> ·
    <a href="/tasks/">Tasks</a> ·
    <a href="/projects/crosbyultimateevents/">Crosby Proof</a>
== DEPLOY TO WEARESWARM.ONLINE ==
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
** WARNING: connection is not using a post-quantum key exchange algorithm.
** This session may be vulnerable to "store now, decrypt later" attacks.
** The server may need to be upgraded. See https://openssh.com/pq.html
REMOTE_DEPLOY=PASS
== LIVE NAV VERIFY ==
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
NAV_ITEM=FAIL:Command Center
