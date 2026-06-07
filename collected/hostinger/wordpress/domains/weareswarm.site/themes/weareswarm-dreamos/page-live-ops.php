<?php
/*
Template Name: Live Ops
*/
$status = function_exists('dreamos_swarm_status_data') ? dreamos_swarm_status_data() : array();
$operations = $status['active_operations'] ?? array();
$unfinished = $status['unfinished_tasks'] ?? array();

$dreamos_title = 'Live Operations | WeAreSwarm';
$dreamos_active = 'live-ops';
$dreamos_canonical = 'https://www.weareswarm.site/live-ops/';
$dreamos_footer_left = 'Live swarm telemetry';
$dreamos_footer_right = 'Human + machine readable ops board';

include get_template_directory() . '/inc/shell-head.php';
?>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">Live Ops</span>
        <h1><span>Operations</span><span class="grad">Board</span></h1>
        <p>Machine-readable and human-readable swarm telemetry from the Dream.OS status plugin and active execution lanes.</p>
        <div class="hero-actions">
          <a class="btn" href="/wp-json/dreamos/v1/status">Open status API</a>
          <a class="btn ghost" href="/">Command Center</a>
        </div>
      </div>
      <aside class="hud">
        <div class="hud-card"><strong>LIVE</strong><span>REST status endpoint connected</span></div>
        <div class="hud-card"><strong><?php echo count($operations); ?></strong><span>active operations</span></div>
        <div class="hud-card"><strong><?php echo count($unfinished); ?></strong><span>open task lanes</span></div>
      </aside>
    </section>

    <section class="section">
      <span class="eyebrow">Runtime Telemetry</span>
      <h2>Active operations</h2>
      <?php if (empty($operations)): ?>
      <article class="card"><p>No active operations returned from status plugin. Endpoint still available for agents and dashboards.</p></article>
      <?php else: ?>
        <?php foreach ($operations as $op): ?>
        <div class="ops-row">
          <div>
            <strong><?php echo esc_html($op['name'] ?? 'Operation'); ?></strong>
            <p><?php echo esc_html($op['next'] ?? ''); ?></p>
          </div>
          <span class="state-<?php echo esc_attr(strtolower($op['state'] ?? 'active')); ?>"><?php echo esc_html($op['state'] ?? 'active'); ?></span>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>

    <section class="section">
      <article class="mission-card">
        <h2>API Endpoint</h2>
        <p><a class="link" href="<?php echo esc_url(home_url('/wp-json/dreamos/v1/status')); ?>">/wp-json/dreamos/v1/status</a></p>
        <?php if (shortcode_exists('dreamos_swarm_status')): ?>
        <div style="margin-top:18px"><?php echo do_shortcode('[dreamos_swarm_status]'); ?></div>
        <?php endif; ?>
      </article>
    </section>
<?php include get_template_directory() . '/inc/shell-foot.php'; ?>
