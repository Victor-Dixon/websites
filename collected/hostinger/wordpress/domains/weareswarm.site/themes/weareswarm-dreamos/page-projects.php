<?php
/*
Template Name: Project Consolidation Board
*/
$status = function_exists('dreamos_swarm_status_data') ? dreamos_swarm_status_data() : array();
$project_board = $status['project_board'] ?? array();
$projects = $status['projects'] ?? array();
$buckets = $project_board['buckets'] ?? array();

$bucket_labels = array(
  'canonical_core' => 'Canonical Core',
  'website' => 'Websites',
  'toolbelt' => 'Toolbelt',
  'promotion_candidate' => 'Promotion Candidates',
  'unknown' => 'Unknown / Needs Review',
  'archive_candidate' => 'Archive Candidates',
);

$bucket_order = array_keys($bucket_labels);
$grouped = array();
foreach ($bucket_order as $kind) {
  $grouped[$kind] = array();
}
foreach ($projects as $project) {
  $kind = $project['kind'] ?? 'unknown';
  if (!isset($grouped[$kind])) {
    $grouped[$kind] = array();
  }
  $grouped[$kind][] = $project;
}

$dreamos_title = 'Project Consolidation Board | WeAreSwarm';
$dreamos_active = 'projects';
$dreamos_canonical = 'https://www.weareswarm.site/projects/';
$dreamos_footer_left = 'Classify before consolidation.';
$dreamos_footer_right = 'GitHub is public source of truth; local folders are candidates until promoted.';

include get_template_directory() . '/inc/shell-head.php';
?>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">DreamVault Inventory / Public Consolidation</span>
        <h1><span>Project</span><span class="grad">Consolidation Board</span></h1>
        <p>GitHub repos, desktop projects, and laptop scans classified into canonical, website, toolbelt, promotion, archive, and review lanes.</p>
        <div class="hero-actions">
          <a class="btn" href="/tasks/">Open task queue</a>
          <a class="btn ghost" href="/feed/">View closeout feed</a>
        </div>
      </div>
      <aside class="hud">
        <div class="hud-card"><strong><?php echo count($projects); ?></strong><span>projects inventoried</span></div>
        <div class="hud-card"><strong><?php echo count($buckets); ?></strong><span>classification buckets</span></div>
        <div class="hud-card"><strong>LIVE</strong><span>generated project board</span></div>
        <div class="hud-card"><strong>OPS</strong><span>planner tasks stay on /tasks/</span></div>
      </aside>
    </section>

    <?php foreach ($bucket_order as $kind):
      $items = $grouped[$kind] ?? array();
      if (!$items) {
        continue;
      }
      $label = $bucket_labels[$kind];
      $count = $buckets[$kind] ?? count($items);
    ?>
    <section class="section">
      <span class="eyebrow"><?php echo esc_html(strtoupper($kind)); ?></span>
      <h2><?php echo esc_html($label); ?> (<?php echo (int) $count; ?>)</h2>
      <div class="grid-3">
        <?php foreach ($items as $project):
          $state = strtolower($project['state'] ?? 'needs_review');
        ?>
        <article class="card">
          <span class="badge <?php echo esc_attr($state); ?>"><?php echo esc_html($state); ?></span>
          <h3><?php echo esc_html($project['name'] ?? 'Project'); ?></h3>
          <?php if (!empty($project['repo'])): ?>
          <p><strong style="color:var(--text)">Repo:</strong> <?php echo esc_html($project['repo']); ?></p>
          <?php endif; ?>
          <p><?php echo esc_html($project['proof'] ?? 'Inventory lane recorded.'); ?></p>
          <?php if (!empty($project['action'])): ?>
          <p><strong style="color:var(--text)">Action:</strong> <?php echo esc_html($project['action']); ?></p>
          <?php endif; ?>
          <?php if (!empty($project['next'])): ?>
          <p><strong style="color:var(--text)">Next:</strong> <?php echo esc_html($project['next']); ?></p>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endforeach; ?>

    <section class="section">
      <span class="eyebrow">Operating Model</span>
      <h2>How consolidation works</h2>
      <div class="mission-card">
        <ul>
          <li><strong>GitHub:</strong> public source of truth for repo identity, visibility, and promotion status.</li>
          <li><strong>Desktop / laptop:</strong> local scans feed candidates; promotion requires manifests and verification.</li>
          <li><strong>DreamVault:</strong> governance decides canonical, stale, promote, archive, and public visibility.</li>
          <li><strong>WeAreSwarm:</strong> public proof board for what exists, what is active, and what proof exists.</li>
          <li><strong>/tasks/:</strong> active DreamVault execution queue from planner status, separate from this inventory board.</li>
        </ul>
      </div>
    </section>
<?php include get_template_directory() . '/inc/shell-foot.php'; ?>
