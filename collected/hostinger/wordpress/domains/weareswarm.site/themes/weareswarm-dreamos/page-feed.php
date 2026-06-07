<?php
/*
Template Name: Closeout Feed
*/
$status = function_exists('dreamos_swarm_status_data') ? dreamos_swarm_status_data() : array();
$unlocks = $status['recent_unlocks'] ?? array();
$operations = $status['active_operations'] ?? array();

$dreamos_title = 'Closeout Feed | WeAreSwarm';
$dreamos_active = 'feed';
$dreamos_canonical = 'https://www.weareswarm.site/feed/';
$dreamos_footer_left = 'Build in public';
$dreamos_footer_right = 'Closeout feed — shipped work with receipts';

include get_template_directory() . '/inc/shell-head.php';
?>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">Closeout Feed</span>
        <h1><span>Shipped work</span><span class="grad">with receipts</span></h1>
        <p>Public proof cards for recoveries, deploys, unlocks, and verification gates — published as the swarm closes lanes.</p>
        <div class="hero-actions">
          <a class="btn" href="/projects/">View project proof</a>
          <a class="btn ghost" href="/tasks/">Open task board</a>
        </div>
      </div>
      <aside class="hud">
        <div class="hud-card"><strong><?php echo count($unlocks); ?></strong><span>recent unlock cards</span></div>
        <div class="hud-card"><strong>LIVE</strong><span>feed updated from status plugin</span></div>
        <div class="hud-card"><strong><?php echo count($operations); ?></strong><span>active operations tracked</span></div>
      </aside>
    </section>

    <section class="section">
      <span class="eyebrow">Recent unlocks</span>
      <h2>What closed recently</h2>
      <div class="grid-3">
        <?php foreach ($unlocks as $unlock): ?>
        <article class="card">
          <span class="badge"><?php echo esc_html(strtoupper($unlock['status'] ?? 'LIVE')); ?></span>
          <h3><?php echo esc_html($unlock['title'] ?? 'Unlock'); ?></h3>
          <p><?php echo esc_html($unlock['detail'] ?? ''); ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section">
      <span class="eyebrow">Active lanes</span>
      <h2>Operations in motion</h2>
      <?php foreach ($operations as $op): ?>
      <div class="ops-row">
        <div>
          <strong><?php echo esc_html($op['name'] ?? 'Operation'); ?></strong>
          <p><?php echo esc_html($op['next'] ?? ''); ?></p>
        </div>
        <span class="state-<?php echo esc_attr(strtolower($op['state'] ?? 'active')); ?>"><?php echo esc_html(strtoupper($op['state'] ?? 'active')); ?></span>
      </div>
      <?php endforeach; ?>
    </section>
<?php include get_template_directory() . '/inc/shell-foot.php'; ?>
