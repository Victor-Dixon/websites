<?php
/*
Template Name: Projects and Proof
*/
$status = function_exists('dreamos_swarm_status_data') ? dreamos_swarm_status_data() : array();
$projects = $status['projects'] ?? array(
  array('name'=>'Website Admin','state'=>'active','proof'=>'SSH deploy, WP-CLI activation, canonical website source, live verification.'),
  array('name'=>'DigitalDreamscape Restored','state'=>'restored','proof'=>'Static deploy verified through portfolio website admin lane.'),
  array('name'=>'WeAreSwarm Live Ops','state'=>'active','proof'=>'Custom theme, REST status plugin, Skill Tree, Operator Profile, Live Ops page.'),
  array('name'=>'Portfolio Registry','state'=>'building','proof'=>'Controlled domains classified and recovery matrix generated.'),
  array('name'=>'Repo Rescue','state'=>'advanced','proof'=>'Scan, classify, salvage, promote, verify, and commit workflow.'),
  array('name'=>'Verification Gates','state'=>'operational','proof'=>'Every lane ends with live markers, reports, and closeout packets.'),
);

$dreamos_title = 'Projects and Proof | WeAreSwarm';
$dreamos_active = 'projects';
$dreamos_canonical = 'https://www.weareswarm.site/projects/';
$dreamos_footer_left = 'Proof before outreach.';
$dreamos_footer_right = 'Dream.OS recovery lanes produce public receipts.';

include get_template_directory() . '/inc/shell-head.php';
?>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">Recovered Systems / Public Receipts</span>
        <h1><span>Projects</span><span class="grad">and Proof</span></h1>
        <p>Not claims. Proof lanes. Each project exists because Dream.OS recovered, deployed, verified, or operationalized a real system.</p>
      </div>
      <aside class="hud">
        <div class="hud-card"><strong><?php echo count($projects); ?></strong><span>proof cards tracked</span></div>
        <div class="hud-card"><strong>LIVE</strong><span>canonical deploy source active</span></div>
        <div class="hud-card"><strong>WP</strong><span>theme + plugin controlled</span></div>
        <div class="hud-card"><strong>SSH</strong><span>server-side deploy lane unlocked</span></div>
      </aside>
    </section>

    <section class="section">
      <div class="grid-3">
        <?php foreach ($projects as $project):
          $state = strtolower($project['state'] ?? 'active');
        ?>
        <article class="card">
          <span class="badge <?php echo esc_attr($state); ?>"><?php echo esc_html($state); ?></span>
          <h3><?php echo esc_html($project['name'] ?? 'Project'); ?></h3>
          <p><?php echo esc_html($project['proof'] ?? ($project['detail'] ?? 'Proof lane recorded.')); ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section">
      <span class="eyebrow">Recovery Timeline</span>
      <h2>What changed publicly</h2>
      <div class="mission-card">
        <ul>
          <li><strong>Website Admin:</strong> unlocked SSH deploys, WordPress theme activation, REST status plugin, and canonical source deploy.</li>
          <li><strong>WeAreSwarm:</strong> converted from broken WordPress state into a live command center, skill tree, profile, live ops, and proof surface.</li>
          <li><strong>DigitalDreamscape:</strong> static deploy verified through the portfolio website admin workflow.</li>
          <li><strong>Portfolio Registry:</strong> controlled domains discovered and recovery order defined.</li>
        </ul>
      </div>
    </section>
<?php include get_template_directory() . '/inc/shell-foot.php'; ?>
