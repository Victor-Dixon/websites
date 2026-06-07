<?php
/*
Template Name: Swarm Skill Tree
*/
$status = function_exists('dreamos_swarm_status_data') ? dreamos_swarm_status_data() : array();
$skills = $status['skill_tree'] ?? array(
  array('skill'=>'Website Admin','level'=>'Unlocked','capabilities'=>array('SSH deploy','FTP deploy','WP-CLI activation','REST status plugin','live verification')),
  array('skill'=>'Repo Rescue','level'=>'Advanced','capabilities'=>array('scan','classify','salvage','promote','verify','commit')),
  array('skill'=>'Swarm Runtime','level'=>'Building','capabilities'=>array('task artifacts','runtime scripts','operator reports','portfolio registry')),
  array('skill'=>'Automation Ops','level'=>'Advanced','capabilities'=>array('terminal lanes','CPC reports','verification gates','closeout packets')),
);
$tiers = array(
  'Command Core' => array('icon'=>'01','summary'=>'Access, deploy, observe, and recover production surfaces.','nodes'=>array('Website Admin','Portfolio Recovery','Verification Gates')),
  'Runtime Engine' => array('icon'=>'02','summary'=>'Turn chaotic work into repeatable agent lanes and artifacts.','nodes'=>array('Swarm Runtime','Automation Ops','DreamOS Runtime')),
  'Recovery Loop' => array('icon'=>'03','summary'=>'Find buried value, rescue repos, and ship proof safely.','nodes'=>array('Repo Rescue','Operator Intelligence Layer','Swarm Memory')),
  'Revenue Frontier' => array('icon'=>'04','summary'=>'Convert proof into outreach, offers, and operating leverage.','nodes'=>array('Distributed Swarm Mesh','Outreach Engine','Revenue Automation')),
);
$fallback_levels = array('Distributed Swarm Mesh'=>'Building','Outreach Engine'=>'Building','Operator Intelligence Layer'=>'Building','Swarm Memory'=>'Building','Revenue Automation'=>'Building','DreamOS Runtime'=>'Building');
$node_copy = array(
  'Website Admin'=>'Production access, deploy hygiene, and live-site triage.',
  'Portfolio Recovery'=>'Turn broken domains into usable proof surfaces.',
  'Verification Gates'=>'Every lane closes with evidence, status, and rollback context.',
  'Swarm Runtime'=>'Task artifacts, runtime scripts, and operator reports.',
  'Automation Ops'=>'Terminal lanes, CPC reports, closeouts, and repeatable checks.',
  'DreamOS Runtime'=>'A durable command layer for multi-agent execution.',
  'Repo Rescue'=>'Scan, classify, salvage, promote, verify, commit.',
  'Operator Intelligence Layer'=>'Dashboards that expose risk, momentum, and next actions.',
  'Swarm Memory'=>'Persistent work history that makes the next agent smarter.',
  'Distributed Swarm Mesh'=>'Coordinated agent lanes that can run across sites and systems.',
  'Outreach Engine'=>'Proof-backed services, lead lists, and follow-up loops.',
  'Revenue Automation'=>'Offer pipelines tied directly to shipped operational proof.',
);
$skill_lookup = array();
foreach ($skills as $skill) {
  if (!empty($skill['skill'])) {
    $skill_lookup[$skill['skill']] = $skill;
  }
}
$all_nodes = array();
foreach ($tiers as $tier) {
  foreach ($tier['nodes'] as $node) {
    $all_nodes[] = $node;
  }
}
$unlocked_count = 0;
foreach ($all_nodes as $label) {
  $level = strtolower($skill_lookup[$label]['level'] ?? ($fallback_levels[$label] ?? 'Unlocked'));
  if (strpos($level, 'unlock') !== false || strpos($level, 'advanced') !== false) {
    $unlocked_count++;
  }
}
$total_count = count($all_nodes);
$unlock_percent = $total_count > 0 ? round(($unlocked_count / $total_count) * 100) : 0;

$dreamos_title = 'Swarm Skill Tree | WeAreSwarm';
$dreamos_active = 'skill-tree';
$dreamos_canonical = 'https://www.weareswarm.site/skill-tree';
$dreamos_page_css = file_get_contents(get_template_directory() . '/assets/skill-tree.css');
$dreamos_page_css .= ':root{--unlock-p:' . (int) $unlock_percent . '}';

include get_template_directory() . '/inc/shell-head.php';
?>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">Capability Graph</span>
        <h1><span>Swarm</span><span class="grad">Skill Tree</span></h1>
        <p>This is the public capability map for Dream.OS: every node is earned through live recoveries, deploys, verification gates, operator workflows, and proof shipped in the open.</p>
        <div class="hero-actions">
          <a class="btn" href="#tree">Explore nodes →</a>
          <a class="btn ghost" href="/wp-json/dreamos/v1/status">View live API</a>
        </div>
      </div>
      <aside class="hud">
        <div class="hud-card progress-ring">
          <div class="ring" style="--unlock-p:<?php echo (int) $unlock_percent; ?>"><strong><?php echo (int) $unlock_percent; ?>%</strong></div>
          <span><strong><?php echo (int) $unlocked_count; ?>/<?php echo (int) $total_count; ?></strong> nodes unlocked or advanced through verified execution.</span>
        </div>
        <div class="hud-card"><strong>04</strong><span>power branches mapped</span></div>
        <div class="hud-card"><strong>LIVE</strong><span>status plugin connected</span></div>
        <div class="hud-card"><strong>SSH</strong><span>deploy lane operational</span></div>
        <div class="hud-card"><strong>WP-CLI</strong><span>repair workflow unlocked</span></div>
      </aside>
    </section>

    <section class="tree-wrap" id="tree" aria-label="DreamOS skill tree">
      <div class="tree-head">
        <div>
          <span class="eyebrow">Neural Progression</span>
          <h2>From recovery lanes to revenue systems.</h2>
        </div>
        <div class="legend" aria-label="Node state legend">
          <span><b>●</b> Unlocked</span>
          <span><b style="color:var(--purple)">●</b> Building</span>
          <span><b style="color:var(--amber)">●</b> Locked</span>
        </div>
      </div>
      <div class="tree">
      <?php foreach ($tiers as $tier => $tier_data): ?>
        <div class="tier">
          <div class="tier-top">
            <div class="tier-icon"><?php echo esc_html($tier_data['icon']); ?></div>
            <h3><?php echo esc_html($tier); ?></h3>
            <p class="tier-desc"><?php echo esc_html($tier_data['summary']); ?></p>
          </div>
          <?php foreach ($tier_data['nodes'] as $label):
            $match = $skill_lookup[$label] ?? array('skill'=>$label,'level'=>$fallback_levels[$label] ?? 'Unlocked','capabilities'=>array($node_copy[$label] ?? 'planned capability lane'));
            $level = strtolower($match['level'] ?? 'building');
            $class = (strpos($level,'unlock') !== false || strpos($level,'advanced') !== false) ? 'unlocked' : (strpos($level,'lock') !== false ? 'locked' : 'building');
            $capabilities = $match['capabilities'] ?? array();
          ?>
            <article class="node <?php echo esc_attr($class); ?>">
              <span class="state"><?php echo esc_html(strtoupper($match['level'] ?? 'BUILDING')); ?></span>
              <h4><?php echo esc_html($match['skill']); ?></h4>
              <p><?php echo esc_html($node_copy[$label] ?? implode(' • ', $capabilities)); ?></p>
              <div class="cap">
                <?php foreach (array_slice($capabilities, 0, 4) as $capability): ?>
                  <span><?php echo esc_html($capability); ?></span>
                <?php endforeach; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
      </div>
    </section>

    <section class="mission" aria-label="Next unlock missions">
      <div class="mission-card">
        <span class="eyebrow">Next Unlock</span>
        <h2>Distributed Swarm Mesh</h2>
        <p>Turn the proven recovery/deploy lanes into coordinated multi-agent execution across domains, dashboards, and closeout feeds.</p>
        <ul>
          <li><strong>Trigger:</strong> multiple agents can repair, verify, and report without losing context.</li>
          <li><strong>Proof:</strong> public site repairs, branch reports, and live HTTP checks.</li>
        </ul>
      </div>
      <div class="mission-card">
        <span class="eyebrow">Operator Signal</span>
        <h2>Built by live execution.</h2>
        <p>No fake roadmap. The skill tree advances when production work ships: broken websites restored, repos rescued, workflows documented, and verification gates passed.</p>
        <ul>
          <li><strong>Current focus:</strong> website recovery, Dream.OS runtime, and proof-backed automation offers.</li>
          <li><strong>Rule:</strong> if it cannot be verified, it is not unlocked.</li>
        </ul>
      </div>
    </section>
<?php include get_template_directory() . '/inc/shell-foot.php'; ?>
