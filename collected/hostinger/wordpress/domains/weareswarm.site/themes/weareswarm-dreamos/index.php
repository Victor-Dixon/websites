<?php
$status = function_exists('dreamos_swarm_status_data') ? dreamos_swarm_status_data() : array();
$recent_unlocks = $status['recent_unlocks'] ?? array();
$unfinished = $status['unfinished_tasks'] ?? array();
$operations = $status['active_operations'] ?? array();
$skills = $status['skill_tree'] ?? array();

$dreamos_title = 'Dream.OS Command Center | WeAreSwarm';
$dreamos_active = 'home';
$dreamos_canonical = 'https://www.weareswarm.site/';
$dreamos_footer_left = 'Systems that recover, verify, and ship.';
$dreamos_footer_right = 'Dream.OS autonomous operations layer';

include get_template_directory() . '/inc/shell-head.php';
?>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">Live Autonomous Operations</span>
        <h1><span>Watch the</span><span class="grad">Swarm Work</span></h1>
        <p>Dream.OS is a live automation swarm for repo recovery, website deployment, orchestration workflows, trading systems, homeschool infrastructure, and autonomous operational tooling.</p>
        <div class="hero-actions">
          <a class="btn" href="/skill-tree">Open Skill Tree</a>
          <a class="btn ghost" href="/projects/">View Proof</a>
          <a class="btn ghost" href="/profile/">Operator Profile</a>
          <a class="btn ghost" href="/live-ops/">Live Operations</a>
        </div>
      </div>
      <aside class="hud">
        <div class="hud-card"><strong>LIVE</strong><span>SSH deploys, WP-CLI, REST status APIs online</span></div>
        <div class="hud-card"><strong><?php echo count($skills); ?></strong><span>skill nodes tracked in registry</span></div>
        <div class="hud-card"><strong><?php echo count($operations); ?></strong><span>active operations running</span></div>
        <div class="hud-card"><strong><?php echo count($unfinished); ?></strong><span>unfinished lanes in queue</span></div>
      </aside>
    </section>

    <section class="section">
      <span class="eyebrow">Recent Unlocks</span>
      <h2>What shipped recently</h2>
      <div class="grid-3">
        <?php foreach (array_slice($recent_unlocks, 0, 3) as $unlock): ?>
        <article class="card">
          <span class="badge"><?php echo esc_html($unlock['status'] ?? 'LIVE'); ?></span>
          <h3><?php echo esc_html($unlock['title'] ?? 'Unlock'); ?></h3>
          <p><?php echo esc_html($unlock['detail'] ?? ''); ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section split">
      <div>
        <span class="eyebrow">Operations Board</span>
        <h2>Active operations</h2>
        <?php foreach ($operations as $op): ?>
        <div class="ops-row">
          <div>
            <strong><?php echo esc_html($op['name'] ?? 'Operation'); ?></strong>
            <p><?php echo esc_html($op['next'] ?? ''); ?></p>
          </div>
          <span class="state-<?php echo esc_attr(strtolower($op['state'] ?? 'active')); ?>"><?php echo esc_html($op['state'] ?? 'active'); ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <aside>
        <span class="eyebrow">Task Queue</span>
        <h2>Unfinished lanes</h2>
        <?php foreach (array_slice($unfinished, 0, 4) as $task): ?>
        <div class="card" style="margin-bottom:12px">
          <strong><?php echo esc_html($task['task'] ?? 'Task'); ?></strong>
          <p><?php echo esc_html($task['progress'] ?? '0%'); ?> complete</p>
        </div>
        <?php endforeach; ?>
      </aside>
    </section>

    <section class="section">
      <span class="eyebrow">Capability Branches</span>
      <h2>Tracked skills</h2>
      <div class="hero-actions">
        <?php foreach (array_slice($skills, 0, 8) as $skill): ?>
        <span class="pill"><?php echo esc_html($skill['skill'] ?? 'Capability'); ?></span>
        <?php endforeach; ?>
      </div>
    </section>
<?php include get_template_directory() . '/inc/shell-foot.php'; ?>
