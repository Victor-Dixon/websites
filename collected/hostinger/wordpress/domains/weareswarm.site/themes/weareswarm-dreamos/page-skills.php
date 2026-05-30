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
?><!doctype html>
<html lang="en">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Swarm Skill Tree | WeAreSwarm</title>
<?php wp_head(); ?>
<style>
:root{--bg:#030712;--panel:#08111f;--panel2:#0d1728;--line:#1f3357;--text:#eef4ff;--muted:#93a4c3;--cyan:#00f5ff;--green:#38ff9c;--purple:#a855f7;--amber:#ffb020}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:radial-gradient(circle at 72% 22%,rgba(0,245,255,.20),transparent 24rem),radial-gradient(circle at 15% 20%,rgba(168,85,247,.20),transparent 26rem),var(--bg);color:var(--text);overflow-x:hidden}
body:before{content:"";position:fixed;inset:0;background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(circle at center,black,transparent 82%);pointer-events:none}
.wrap{max-width:1440px;margin:auto;padding:26px 18px 80px;position:relative;z-index:2}
.shell{border:1px solid var(--line);border-radius:28px;background:linear-gradient(180deg,rgba(255,255,255,.07),rgba(255,255,255,.025));box-shadow:0 30px 90px #0009;overflow:hidden}
header{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:18px 22px;border-bottom:1px solid var(--line);background:rgba(3,7,18,.82);backdrop-filter:blur(18px)}
.brand{font-weight:950;letter-spacing:.08em;text-transform:uppercase}
.topnav{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.topnav a{color:var(--text);text-decoration:none;border:1px solid var(--line);border-radius:999px;padding:8px 12px;background:#ffffff08;font-weight:700;font-size:.9rem}
.topnav a:hover{border-color:var(--cyan);box-shadow:0 0 18px #00f5ff44}
.hero{display:grid;grid-template-columns:1.05fr .95fr;gap:26px;padding:46px 38px 28px}
.eyebrow{display:inline-block;color:var(--green);letter-spacing:.15em;text-transform:uppercase;font-size:.78rem;font-weight:900;margin-bottom:14px}
h1{font-size:clamp(3.2rem,8vw,7.2rem);line-height:.86;letter-spacing:-.08em;margin:0 0 20px;text-shadow:0 0 40px #00f5ff33}
p{color:var(--muted);font-size:1.08rem;line-height:1.68}
.hud{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;align-content:start}
.hud-card{border:1px solid var(--line);background:rgba(8,17,31,.78);border-radius:18px;padding:18px;position:relative;overflow:hidden}
.hud-card:after{content:"";position:absolute;inset:auto 10px 10px auto;width:40px;height:2px;background:var(--cyan);box-shadow:0 0 16px var(--cyan)}
.hud-card strong{display:block;color:var(--cyan);font-size:1.8rem}
.tree{padding:24px 38px 44px;display:grid;grid-template-columns:repeat(4,1fr);gap:18px;position:relative}
.tier{position:relative;border:1px solid var(--line);border-radius:24px;background:linear-gradient(180deg,rgba(13,23,40,.92),rgba(8,17,31,.75));padding:20px;min-height:420px}
.tier h2{margin:0 0 14px;font-size:1.2rem;letter-spacing:.04em;text-transform:uppercase}
.node{border:1px solid #274466;background:#07101d;border-radius:18px;padding:16px;margin-top:14px;position:relative;transition:.22s ease;box-shadow:inset 0 0 24px #0006}
.node:hover{transform:translateY(-5px);border-color:var(--cyan);box-shadow:0 16px 50px #00f5ff20}
.node:before{content:"";position:absolute;left:-9px;top:24px;width:14px;height:14px;border-radius:50%;background:var(--cyan);box-shadow:0 0 20px var(--cyan)}
.state{display:inline-flex;font-size:.7rem;letter-spacing:.1em;font-weight:950;border-radius:999px;padding:5px 9px;margin-bottom:10px;background:#ffffff10}
.unlocked{border-color:#1d8f64}.unlocked .state,.unlocked:before{color:var(--green);background:rgba(56,255,156,.12)}
.building{border-color:#7442c8}.building .state,.building:before{color:var(--purple);background:rgba(168,85,247,.14)}
.locked{opacity:.58}.locked .state,.locked:before{color:var(--amber);background:rgba(255,176,32,.14)}
.node h3{margin:0 0 8px;font-size:1.04rem}
.node p{font-size:.92rem;margin:0}
.connector{height:2px;background:linear-gradient(90deg,var(--cyan),transparent);opacity:.45;margin:12px 0}
.footerbar{display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap;border-top:1px solid var(--line);padding:18px 22px;color:var(--muted);font-size:.9rem;background:#030712aa}
@media(max-width:1100px){.hero{grid-template-columns:1fr}.tree{grid-template-columns:repeat(2,1fr)}}
@media(max-width:700px){header{align-items:flex-start;flex-direction:column}.tree{grid-template-columns:1fr;padding:18px}.hero{padding:28px 18px}.hud{grid-template-columns:1fr}h1{font-size:3.5rem}}
@media(prefers-reduced-motion:no-preference){.node{animation:rise .5s ease both}.node:nth-child(2){animation-delay:.04s}.node:nth-child(3){animation-delay:.08s}@keyframes rise{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}}
</style>
</head>
<body <?php body_class(); ?>>
<div class="wrap">
  <div class="shell">
    <header>
      <div class="brand">We Are Swarm</div>
      <?php get_template_part('nav'); ?>
    </header>

    <section class="hero">
      <div>
        <span class="eyebrow">Capability Graph</span>
        <h1>Swarm Skill Tree</h1>
        <p>Every node represents a capability unlocked through real recovery lanes, deploys, verification gates, and operator workflows. This is the public tech tree for Dream.OS.</p>
      </div>
      <aside class="hud">
        <div class="hud-card"><strong>04</strong><span>core branches mapped</span></div>
        <div class="hud-card"><strong>LIVE</strong><span>status plugin connected</span></div>
        <div class="hud-card"><strong>SSH</strong><span>deploy lane operational</span></div>
        <div class="hud-card"><strong>WP-CLI</strong><span>page workflow unlocked</span></div>
      </aside>
    </section>

    <section class="tree" aria-label="DreamOS skill tree">
      <?php
      $tiers = array(
        'Infrastructure' => array('Website Admin','Portfolio Recovery','Distributed Swarm Mesh'),
        'Runtime' => array('Swarm Runtime','Automation Ops','DreamOS Runtime'),
        'Recovery' => array('Repo Rescue','Verification Gates','Outreach Engine'),
        'Intelligence' => array('Operator Intelligence Layer','Swarm Memory','Revenue Automation'),
      );
      foreach ($tiers as $tier => $wanted): ?>
        <div class="tier">
          <h2><?php echo esc_html($tier); ?></h2>
          <?php foreach ($wanted as $label):
            $match = null;
            foreach ($skills as $skill) {
              if (($skill['skill'] ?? '') === $label) { $match = $skill; break; }
            }
            if (!$match) {
              $match = array('skill'=>$label,'level'=> in_array($label,array('Distributed Swarm Mesh','Outreach Engine','Operator Intelligence Layer','Swarm Memory','Revenue Automation'),true) ? 'Building' : 'Unlocked','capabilities'=>array('planned capability lane'));
            }
            $level = strtolower($match['level'] ?? 'building');
            $class = str_contains($level,'unlock') || str_contains($level,'advanced') ? 'unlocked' : (str_contains($level,'lock') ? 'locked' : 'building');
          ?>
            <article class="node <?php echo esc_attr($class); ?>">
              <span class="state"><?php echo esc_html(strtoupper($match['level'] ?? 'BUILDING')); ?></span>
              <h3><?php echo esc_html($match['skill']); ?></h3>
              <div class="connector"></div>
              <p><?php echo esc_html(implode(' • ', $match['capabilities'] ?? array())); ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </section>

    <div class="footerbar">
      <span>Dream.OS capability map</span>
      <span>Unlocked through live execution, not theory</span>
    </div>
  </div>
</div>
<?php wp_footer(); ?>
</body>
</html>
