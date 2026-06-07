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
    'icon' => '01',
    'summary' => 'Access, deploy, observe, and recover production surfaces.',
    'nodes' => array('Website Admin','Portfolio Recovery','Verification Gates'),
  ),
  'Runtime Engine' => array(
    'icon' => '02',
    'summary' => 'Turn chaotic work into repeatable agent lanes and artifacts.',
    'nodes' => array('Swarm Runtime','Automation Ops','DreamOS Runtime'),
  ),
  'Recovery Loop' => array(
    'icon' => '03',
    'summary' => 'Find buried value, rescue repos, and ship proof safely.',
    'nodes' => array('Repo Rescue','Operator Intelligence Layer','Swarm Memory'),
  ),
  'Revenue Frontier' => array(
    'icon' => '04',
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
  'Distributed Swarm Mesh' => 'Coordinated agent lanes that can run across sites and systems.',
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
$building_count = 0;
foreach ($all_nodes as $label) {
  $level = strtolower($skill_lookup[$label]['level'] ?? ($fallback_levels[$label] ?? 'Unlocked'));
  if (strpos($level, 'unlock') !== false || strpos($level, 'advanced') !== false) {
    $unlocked_count++;
  } else {
    $building_count++;
  }
}

$total_count = count($all_nodes);
$unlock_percent = $total_count > 0 ? round(($unlocked_count / $total_count) * 100) : 0;
?><!doctype html>
<html lang="en">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Swarm Skill Tree | WeAreSwarm</title>
<?php wp_head(); ?>
<style>
:root{--bg:#02040b;--bg2:#06101f;--panel:#08111f;--panel2:#0d1728;--line:#234165;--line2:#315f86;--text:#f5fbff;--muted:#9cb2d6;--cyan:#00f5ff;--green:#38ff9c;--purple:#a855f7;--pink:#ff3df2;--amber:#ffb020;--red:#ff4d6d}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:var(--bg);color:var(--text);overflow-x:hidden}
body:before{content:"";position:fixed;inset:0;background:radial-gradient(circle at var(--mx,70%) var(--my,20%),rgba(0,245,255,.22),transparent 20rem),radial-gradient(circle at 12% 12%,rgba(168,85,247,.24),transparent 28rem),radial-gradient(circle at 90% 88%,rgba(56,255,156,.12),transparent 24rem),linear-gradient(180deg,var(--bg),#030712 48%,#010208);pointer-events:none}
body:after{content:"";position:fixed;inset:0;background-image:linear-gradient(rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px);background-size:54px 54px;mask-image:radial-gradient(circle at center,black,transparent 78%);opacity:.72;pointer-events:none}
.stars{position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:1}
.stars i{position:absolute;width:2px;height:2px;border-radius:50%;background:var(--cyan);box-shadow:0 0 16px var(--cyan);opacity:.72;animation:drift 14s linear infinite}
.stars i:nth-child(1){left:8%;top:18%;animation-duration:19s}.stars i:nth-child(2){left:18%;top:74%;animation-duration:16s}.stars i:nth-child(3){left:35%;top:12%;animation-duration:22s}.stars i:nth-child(4){left:48%;top:64%;animation-duration:18s}.stars i:nth-child(5){left:62%;top:23%;animation-duration:15s}.stars i:nth-child(6){left:76%;top:80%;animation-duration:21s}.stars i:nth-child(7){left:88%;top:34%;animation-duration:17s}.stars i:nth-child(8){left:94%;top:10%;animation-duration:24s}
.wrap{max-width:1540px;margin:auto;padding:26px 18px 80px;position:relative;z-index:2}
.shell{border:1px solid rgba(0,245,255,.22);border-radius:34px;background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,.026));box-shadow:0 44px 120px #000b,0 0 80px rgba(0,245,255,.08);overflow:hidden;position:relative}
.shell:before{content:"";position:absolute;inset:0;border-radius:inherit;padding:1px;background:linear-gradient(135deg,rgba(0,245,255,.75),transparent 22%,rgba(168,85,247,.42) 52%,transparent 70%,rgba(56,255,156,.65));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;pointer-events:none}
header{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:18px 22px;border-bottom:1px solid rgba(35,65,101,.82);background:rgba(2,4,11,.78);backdrop-filter:blur(22px);position:sticky;top:0;z-index:20}
.brand{font-weight:950;letter-spacing:.12em;text-transform:uppercase;display:flex;align-items:center;gap:10px}
.brand:before{content:"";width:12px;height:12px;border-radius:50%;background:var(--green);box-shadow:0 0 22px var(--green)}
.topnav{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.topnav a{color:var(--text);text-decoration:none;border:1px solid rgba(49,95,134,.72);border-radius:999px;padding:9px 13px;background:#ffffff08;font-weight:800;font-size:.9rem;transition:.2s ease}
.topnav a:hover{border-color:var(--cyan);box-shadow:0 0 22px #00f5ff4d;color:white;transform:translateY(-1px)}
.hero{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(340px,.9fr);gap:30px;padding:58px 44px 34px;position:relative}
.hero:after{content:"";position:absolute;left:7%;right:7%;bottom:0;height:1px;background:linear-gradient(90deg,transparent,var(--cyan),var(--purple),transparent);opacity:.55}
.eyebrow{display:inline-flex;align-items:center;gap:10px;color:var(--green);letter-spacing:.18em;text-transform:uppercase;font-size:.78rem;font-weight:950;margin-bottom:16px}
.eyebrow:before{content:"";width:42px;height:1px;background:var(--green);box-shadow:0 0 14px var(--green)}
h1{font-size:clamp(3.4rem,8vw,8.6rem);line-height:.78;letter-spacing:-.095em;margin:0 0 22px;text-shadow:0 0 42px #00f5ff38}
.grad{display:block;background:linear-gradient(100deg,#fff 0%,var(--cyan) 34%,var(--purple) 68%,var(--green));-webkit-background-clip:text;background-clip:text;color:transparent}
p{color:var(--muted);font-size:1.08rem;line-height:1.68}
.hero-copy{max-width:760px}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:28px}
.btn{display:inline-flex;align-items:center;gap:9px;text-decoration:none;border-radius:999px;padding:13px 17px;font-weight:950;border:1px solid rgba(0,245,255,.5);color:#06101f;background:linear-gradient(135deg,var(--cyan),var(--green));box-shadow:0 14px 40px rgba(0,245,255,.18)}
.btn.ghost{color:var(--text);background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.18);box-shadow:none}
.hud{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;align-content:start}
.hud-card{border:1px solid rgba(49,95,134,.82);background:linear-gradient(180deg,rgba(8,17,31,.88),rgba(6,16,31,.58));border-radius:22px;padding:18px;position:relative;overflow:hidden;min-height:132px}
.hud-card:before{content:"";position:absolute;inset:-60% -30% auto auto;width:140px;height:140px;border-radius:50%;background:rgba(0,245,255,.13);filter:blur(6px)}
.hud-card:after{content:"";position:absolute;inset:auto 13px 13px auto;width:44px;height:2px;background:var(--cyan);box-shadow:0 0 16px var(--cyan)}
.hud-card strong{display:block;color:var(--cyan);font-size:2.25rem;line-height:1;margin-bottom:10px}
.hud-card span{color:var(--muted);font-weight:750}
.progress-ring{grid-column:1/-1;display:grid;grid-template-columns:auto 1fr;gap:18px;align-items:center}
.ring{--p:<?php echo (int) $unlock_percent; ?>;width:118px;aspect-ratio:1;border-radius:50%;display:grid;place-items:center;background:conic-gradient(var(--green) calc(var(--p)*1%),rgba(255,255,255,.08) 0);box-shadow:0 0 36px rgba(56,255,156,.18)}
.ring:before{content:"";position:absolute;width:84px;aspect-ratio:1;border-radius:50%;background:#07101d}
.ring strong{position:relative;color:var(--green);font-size:1.8rem}
.tree-wrap{padding:30px 38px 52px;position:relative}
.tree-head{display:flex;justify-content:space-between;align-items:end;gap:24px;margin-bottom:24px}
.tree-head h2{font-size:clamp(1.8rem,4vw,3.2rem);margin:0;letter-spacing:-.04em}
.legend{display:flex;gap:10px;flex-wrap:wrap}.legend span{border:1px solid rgba(255,255,255,.14);border-radius:999px;padding:8px 10px;background:#ffffff08;color:var(--muted);font-weight:800;font-size:.82rem}.legend b{color:var(--cyan)}
.tree{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;position:relative}
.tree:before{content:"";position:absolute;left:4%;right:4%;top:88px;height:2px;background:linear-gradient(90deg,transparent,var(--cyan),var(--purple),var(--green),transparent);box-shadow:0 0 24px rgba(0,245,255,.35);opacity:.5}
.tier{position:relative;border:1px solid rgba(49,95,134,.74);border-radius:28px;background:linear-gradient(180deg,rgba(13,23,40,.94),rgba(8,17,31,.72));padding:20px;min-height:500px;overflow:hidden}
.tier:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 50% 0,rgba(0,245,255,.14),transparent 46%);pointer-events:none}
.tier-top{position:relative;display:grid;grid-template-columns:auto 1fr;gap:14px;align-items:center;margin-bottom:18px}
.tier-icon{width:50px;height:50px;border-radius:16px;display:grid;place-items:center;color:#06101f;background:linear-gradient(135deg,var(--cyan),var(--green));font-weight:1000;box-shadow:0 16px 40px rgba(0,245,255,.18)}
.tier h3{margin:0;font-size:1.12rem;letter-spacing:.05em;text-transform:uppercase}.tier-desc{grid-column:1/-1;margin:0;color:var(--muted);font-size:.92rem}
.node{border:1px solid #274466;background:linear-gradient(180deg,#081525,#050d18);border-radius:20px;padding:17px;margin-top:15px;position:relative;transition:.24s ease;box-shadow:inset 0 0 24px #0007,0 10px 35px #0004}
.node:hover{transform:translateY(-7px) scale(1.015);border-color:var(--cyan);box-shadow:0 20px 70px #00f5ff24,inset 0 0 34px rgba(0,245,255,.05)}
.node:before{content:"";position:absolute;left:-10px;top:25px;width:16px;height:16px;border-radius:50%;background:var(--cyan);box-shadow:0 0 22px var(--cyan)}
.node:after{content:"";position:absolute;left:-2px;top:33px;width:2px;height:calc(100% + 15px);background:linear-gradient(var(--cyan),transparent);opacity:.38}
.node:last-child:after{display:none}
.state{display:inline-flex;font-size:.68rem;letter-spacing:.12em;font-weight:1000;border-radius:999px;padding:6px 10px;margin-bottom:11px;background:#ffffff10}
.unlocked{border-color:rgba(56,255,156,.55)}.unlocked .state,.unlocked:before{color:var(--green);background:rgba(56,255,156,.12)}.unlocked:before{background:var(--green)}
.building{border-color:rgba(168,85,247,.58)}.building .state,.building:before{color:var(--purple);background:rgba(168,85,247,.15)}.building:before{background:var(--purple);box-shadow:0 0 22px var(--purple)}
.locked{opacity:.68}.locked .state,.locked:before{color:var(--amber);background:rgba(255,176,32,.14)}.locked:before{background:var(--amber);box-shadow:0 0 22px var(--amber)}
.node h4{margin:0 0 8px;font-size:1.08rem}
.node p{font-size:.9rem;margin:0}.cap{display:flex;flex-wrap:wrap;gap:7px;margin-top:13px}.cap span{font-size:.72rem;color:#cce6ff;border:1px solid rgba(255,255,255,.12);background:#ffffff08;border-radius:999px;padding:6px 8px}
.mission{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding:0 38px 42px}
.mission-card{border:1px solid rgba(49,95,134,.74);border-radius:26px;background:linear-gradient(135deg,rgba(0,245,255,.09),rgba(168,85,247,.08));padding:24px;position:relative;overflow:hidden}
.mission-card h2{margin:0 0 12px;font-size:1.6rem}.mission-card ul{margin:14px 0 0;padding:0;list-style:none}.mission-card li{padding:10px 0;border-top:1px solid rgba(255,255,255,.09);color:var(--muted)}.mission-card strong{color:var(--text)}
.footerbar{display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap;border-top:1px solid var(--line);padding:18px 22px;color:var(--muted);font-size:.9rem;background:#030712aa}
@media(max-width:1180px){.hero{grid-template-columns:1fr}.tree{grid-template-columns:repeat(2,1fr)}.mission{grid-template-columns:1fr}}
@media(max-width:720px){.wrap{padding:12px 10px 48px}.shell{border-radius:24px}header{align-items:flex-start;flex-direction:column;position:relative}.hero{padding:34px 20px 24px}.hud{grid-template-columns:1fr}.tree-wrap{padding:24px 18px 34px}.tree{grid-template-columns:1fr}.tree:before{display:none}.tier{min-height:auto}.mission{padding:0 18px 30px}h1{font-size:3.7rem}.tree-head{align-items:flex-start;flex-direction:column}}
@media(prefers-reduced-motion:no-preference){.stars i{animation-name:drift}.node,.tier,.hud-card{animation:rise .58s ease both}.tier:nth-child(2){animation-delay:.07s}.tier:nth-child(3){animation-delay:.14s}.tier:nth-child(4){animation-delay:.21s}@keyframes rise{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}@keyframes drift{from{transform:translate3d(0,0,0);opacity:.1}50%{opacity:.9}to{transform:translate3d(34px,-70px,0);opacity:.1}}}
</style>
</head>
<body <?php body_class(); ?>>
<div class="stars" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
<div class="wrap">
  <div class="shell">
    <header>
      <div class="brand">We Are Swarm</div>
      <?php get_template_part('nav'); ?>
    </header>

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
          <div class="ring"><strong><?php echo (int) $unlock_percent; ?>%</strong></div>
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
            $match = $skill_lookup[$label] ?? null;
            if (!$match) {
              $match = array('skill'=>$label,'level'=> $fallback_levels[$label] ?? 'Unlocked','capabilities'=>array($node_copy[$label] ?? 'planned capability lane'));
            }
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

    <div class="footerbar">
      <span>Dream.OS capability map</span>
      <span>Unlocked through live execution, not theory</span>
    </div>
  </div>
</div>
<?php wp_footer(); ?>
<script>
(() => {
  const root = document.documentElement;
  window.addEventListener('pointermove', (event) => {
    root.style.setProperty('--mx', `${(event.clientX / window.innerWidth) * 100}%`);
    root.style.setProperty('--my', `${(event.clientY / window.innerHeight) * 100}%`);
  }, {passive: true});
})();
</script>
</body>
</html>
