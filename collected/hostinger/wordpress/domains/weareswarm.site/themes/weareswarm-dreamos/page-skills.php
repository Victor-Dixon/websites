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
  'Command Core' => array(
    'num' => '01',
    'title' => 'Access, deploy, observe, recover',
    'summary' => 'Production access, deploy hygiene, and live-site triage lanes.',
    'nodes' => array('Website Admin','Portfolio Recovery','Verification Gates'),
  ),
  'Runtime Engine' => array(
    'num' => '02',
    'title' => 'Repeatable agent lanes',
    'summary' => 'Turn chaotic work into repeatable agent lanes and artifacts.',
    'nodes' => array('Swarm Runtime','Automation Ops','DreamOS Runtime'),
  ),
  'Recovery Loop' => array(
    'num' => '03',
    'title' => 'Rescue repos, ship proof',
    'summary' => 'Find buried value, rescue repos, and ship proof safely.',
    'nodes' => array('Repo Rescue','Operator Intelligence Layer','Swarm Memory'),
  ),
  'Revenue Frontier' => array(
    'num' => '04',
    'title' => 'Proof into leverage',
    'summary' => 'Convert proof into outreach, offers, and operating leverage.',
    'nodes' => array('Distributed Swarm Mesh','Outreach Engine','Revenue Automation'),
  ),
);

$fallback_levels = array(
  'Distributed Swarm Mesh' => 'Building',
  'Outreach Engine' => 'Building',
  'Operator Intelligence Layer' => 'Building',
  'Swarm Memory' => 'Building',
  'Revenue Automation' => 'Building',
  'DreamOS Runtime' => 'Building',
);

$node_copy = array(
  'Website Admin' => 'Production access, deploy hygiene, and live-site triage.',
  'Portfolio Recovery' => 'Turn broken domains into usable proof surfaces.',
  'Verification Gates' => 'Every lane closes with evidence, status, and rollback context.',
  'Swarm Runtime' => 'Task artifacts, runtime scripts, and operator reports.',
  'Automation Ops' => 'Terminal lanes, CPC reports, closeouts, and repeatable checks.',
  'DreamOS Runtime' => 'A durable command layer for multi-agent execution.',
  'Repo Rescue' => 'Scan, classify, salvage, promote, verify, commit.',
  'Operator Intelligence Layer' => 'Dashboards that expose risk, momentum, and next actions.',
  'Swarm Memory' => 'Persistent work history that makes the next agent smarter.',
  'Distributed Swarm Mesh' => 'Coordinated agent lanes across sites and systems.',
  'Outreach Engine' => 'Proof-backed services, lead lists, and follow-up loops.',
  'Revenue Automation' => 'Offer pipelines tied directly to shipped operational proof.',
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

function dreamos_skill_node_class($level) {
  $level = strtolower($level ?? 'building');
  if (strpos($level, 'unlock') !== false || strpos($level, 'advanced') !== false) {
    return strpos($level, 'advanced') !== false ? 'advanced' : 'unlocked';
  }
  if (strpos($level, 'lock') !== false) {
    return 'locked';
  }
  return 'building';
}
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Swarm Skill Tree | WeAreSwarm</title>
  <meta name="description" content="Public capability map for Dream.OS — skills unlocked through live recoveries, deploys, and verification gates.">
  <link rel="canonical" href="https://www.weareswarm.site/skill-tree">
  <style>
    :root {
      --bg: #0f1115;
      --panel: #171b22;
      --text: #f4f7fb;
      --muted: #aab4c0;
      --line: #2a3240;
      --accent: #7cf7c9;
      --accent2: #8ab4ff;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: radial-gradient(circle at top, #1d2533, var(--bg));
      color: var(--text);
      line-height: 1.55;
    }
    a { color: inherit; }
    .site-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 16px 24px;
      border-bottom: 1px solid var(--line);
      background: rgba(15,17,21,.92);
    }
    .logo { font-weight: 800; letter-spacing: .06em; text-transform: uppercase; text-decoration: none; font-size: .95rem; }
    .logo span { color: var(--accent); }
    .site-nav { display: flex; flex-wrap: wrap; gap: 8px; }
    .site-nav a {
      text-decoration: none;
      color: var(--muted);
      font-size: .9rem;
      font-weight: 600;
      padding: 8px 14px;
      border-radius: 999px;
      border: 1px solid transparent;
    }
    .site-nav a:hover { color: var(--text); border-color: var(--line); }
    .site-nav a.active { color: var(--accent); border-color: rgba(124,247,201,.35); background: rgba(124,247,201,.12); }
    .wrap { max-width: 1120px; margin: 0 auto; padding: 40px 20px 56px; }
    .hero {
      padding: 48px;
      border: 1px solid var(--line);
      border-radius: 28px;
      background: linear-gradient(135deg, rgba(124,247,201,.10), rgba(138,180,255,.08)), var(--panel);
    }
    .eyebrow { color: var(--accent); font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    h1 { font-size: clamp(2.4rem, 7vw, 5.2rem); line-height: .95; margin: 18px 0; }
    h2 { font-size: 1.35rem; margin-top: 0; }
    h3 { font-size: 1.05rem; margin: 0 0 8px; }
    p { color: var(--muted); font-size: 1.05rem; }
    .cta {
      display: inline-block;
      margin-top: 20px;
      padding: 14px 20px;
      border-radius: 999px;
      background: var(--accent);
      color: #07110d;
      font-weight: 800;
      text-decoration: none;
    }
    .stats {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
      margin-top: 28px;
    }
    .stat {
      padding: 20px;
      border: 1px solid var(--line);
      border-radius: 22px;
      background: rgba(23,27,34,.86);
    }
    .stat strong {
      display: block;
      font-size: 2rem;
      color: var(--accent);
      line-height: 1;
      margin-bottom: 8px;
    }
    .stat span { color: var(--muted); font-size: .95rem; }
    .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin-top: 28px; }
    .card {
      padding: 24px;
      border: 1px solid var(--line);
      border-radius: 22px;
      background: rgba(23,27,34,.86);
    }
    .card > p { margin-top: 0; font-size: .98rem; }
    .tier-num { color: var(--accent2); font-weight: 700; font-size: .85rem; letter-spacing: .06em; }
    .nodes { list-style: none; margin: 18px 0 0; padding: 0; }
    .node {
      padding: 16px 0;
      border-top: 1px solid var(--line);
    }
    .node:first-child { border-top: 0; padding-top: 0; }
    .node p { margin: 6px 0 0; font-size: .95rem; }
    .badge {
      display: inline-block;
      font-size: .72rem;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
      border-radius: 999px;
      padding: 5px 10px;
      margin-bottom: 8px;
    }
    .unlocked .badge, .advanced .badge { color: var(--accent); background: rgba(124,247,201,.12); }
    .building .badge { color: var(--accent2); background: rgba(138,180,255,.12); }
    .locked .badge { color: var(--muted); background: rgba(170,180,192,.12); }
    .caps { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .caps span {
      font-size: .78rem;
      color: var(--muted);
      border: 1px solid var(--line);
      border-radius: 999px;
      padding: 5px 9px;
    }
    .proof { border-left: 4px solid var(--accent); }
    .split { display: grid; grid-template-columns: 1.1fr .9fr; gap: 24px; margin-top: 28px; }
    ul { color: var(--muted); padding-left: 20px; }
    code { color: var(--accent2); }
    footer { padding: 32px 20px; color: var(--muted); text-align: center; }
    footer a { color: var(--accent2); }
    @media (max-width: 800px) {
      .hero { padding: 28px; }
      .stats, .grid, .split { grid-template-columns: 1fr; }
      .site-header { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>
  <header class="site-header">
    <a class="logo" href="https://www.weareswarm.site/"><span>We</span>AreSwarm</a>
    <nav class="site-nav" aria-label="Primary">
      <a href="/dreamos-services/">Services</a>
      <a href="/skill-tree" class="active">Skill Tree</a>
      <a href="/">Hub</a>
      <a href="/index.php">Command Center</a>
      <a href="/wp-json/dreamos/v1/status">API</a>
    </nav>
  </header>
  <main class="wrap">
    <section class="hero">
      <div class="eyebrow">WeAreSwarm × Dream.OS</div>
      <h1>Swarm Skill Tree</h1>
      <p>
        The public capability map for Dream.OS. Every node is earned through live recoveries,
        deploys, verification gates, operator workflows, and proof shipped in the open.
      </p>
      <a class="cta" href="/wp-json/dreamos/v1/status">View live API</a>
    </section>

    <section class="stats" aria-label="Skill tree summary">
      <article class="stat">
        <strong><?php echo (int) $unlock_percent; ?>%</strong>
        <span>nodes unlocked or advanced</span>
      </article>
      <article class="stat">
        <strong><?php echo (int) $unlocked_count; ?>/<?php echo (int) $total_count; ?></strong>
        <span>verified capabilities</span>
      </article>
      <article class="stat">
        <strong>04</strong>
        <span>power branches mapped</span>
      </article>
      <article class="stat">
        <strong>LIVE</strong>
        <span>status plugin connected</span>
      </article>
    </section>

    <section class="grid" aria-label="Skill branches">
      <?php foreach ($tiers as $tier => $tier_data): ?>
      <article class="card">
        <div class="tier-num"><?php echo esc_html($tier_data['num']); ?> — <?php echo esc_html($tier); ?></div>
        <h2><?php echo esc_html($tier_data['title']); ?></h2>
        <p><?php echo esc_html($tier_data['summary']); ?></p>
        <ul class="nodes">
          <?php foreach ($tier_data['nodes'] as $label):
            $match = $skill_lookup[$label] ?? array(
              'skill' => $label,
              'level' => $fallback_levels[$label] ?? 'Unlocked',
              'capabilities' => array(),
            );
            $level = $match['level'] ?? 'Building';
            $class = dreamos_skill_node_class($level);
            $capabilities = $match['capabilities'] ?? array();
          ?>
          <li class="node <?php echo esc_attr($class); ?>">
            <span class="badge"><?php echo esc_html($level); ?></span>
            <h3><?php echo esc_html($match['skill']); ?></h3>
            <p><?php echo esc_html($node_copy[$label] ?? implode(' • ', $capabilities)); ?></p>
            <?php if (!empty($capabilities)): ?>
            <div class="caps">
              <?php foreach (array_slice($capabilities, 0, 4) as $capability): ?>
              <span><?php echo esc_html($capability); ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </article>
      <?php endforeach; ?>
    </section>

    <section class="split">
      <article class="card proof">
        <h2>Next Unlock</h2>
        <p><strong style="color:var(--text)">Distributed Swarm Mesh</strong> — turn proven recovery and deploy lanes into coordinated multi-agent execution across domains, dashboards, and closeout feeds.</p>
        <ul>
          <li><code>Trigger:</code> multiple agents repair, verify, and report without losing context.</li>
          <li><code>Proof:</code> public site repairs, branch reports, and live HTTP checks.</li>
        </ul>
      </article>
      <article class="card">
        <h2>Operator Signal</h2>
        <p>No fake roadmap. The skill tree advances when production work ships.</p>
        <ul>
          <li>Website recovery, Dream.OS runtime, proof-backed automation offers.</li>
          <li>If it cannot be verified, it is not unlocked.</li>
        </ul>
      </article>
    </section>
  </main>
  <footer>
    Dream.OS capability map by <a href="https://www.weareswarm.site/">WeAreSwarm</a>.
  </footer>
</body>
</html>
