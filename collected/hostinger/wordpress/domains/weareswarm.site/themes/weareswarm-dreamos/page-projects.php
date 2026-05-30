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
?><!doctype html>
<html lang="en">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Projects and Proof | WeAreSwarm</title>
<?php wp_head(); ?>
<style>
:root{--bg:#040816;--panel:#09101d;--panel2:#10192a;--line:#1d3357;--text:#eef3ff;--muted:#8da2c0;--cyan:#00e5ff;--green:#3cffb3;--purple:#a855f7;--amber:#ffb020}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,system-ui,sans-serif;background:radial-gradient(circle at top right,rgba(0,229,255,.14),transparent 30rem),radial-gradient(circle at bottom left,rgba(168,85,247,.18),transparent 30rem),var(--bg);color:var(--text);overflow-x:hidden}
body:before{content:"";position:fixed;inset:0;background-image:linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);background-size:42px 42px;mask-image:radial-gradient(circle at center,black,transparent 80%);pointer-events:none}
.shell{max-width:1450px;margin:auto;padding:24px;position:relative;z-index:2}
.frame{border:1px solid var(--line);border-radius:28px;overflow:hidden;background:rgba(9,16,29,.78);backdrop-filter:blur(18px);box-shadow:0 30px 100px #000a}
.topbar{display:flex;justify-content:space-between;align-items:center;padding:18px 24px;border-bottom:1px solid var(--line);background:#050b16dd;backdrop-filter:blur(18px)}
.brand{font-size:1rem;font-weight:900;letter-spacing:.18em;text-transform:uppercase}
.topnav{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.topnav a{color:var(--text);text-decoration:none;border:1px solid var(--line);border-radius:999px;padding:8px 12px;background:#ffffff08;font-weight:700;font-size:.9rem}
.topnav a:hover{border-color:var(--cyan);box-shadow:0 0 18px #00e5ff33}
main{padding:34px}
.hero{display:grid;grid-template-columns:1.1fr .9fr;gap:24px;margin-bottom:24px}
.panel{position:relative;overflow:hidden;border:1px solid var(--line);background:linear-gradient(180deg,var(--panel2),var(--panel));border-radius:24px;padding:24px}
.panel:before{content:"";position:absolute;top:0;left:0;width:100%;height:2px;background:linear-gradient(90deg,var(--cyan),transparent)}
.eyebrow{display:inline-block;color:var(--green);font-size:.76rem;font-weight:900;letter-spacing:.16em;text-transform:uppercase;margin-bottom:16px}
h1{font-size:clamp(3.2rem,8vw,6.6rem);line-height:.88;margin:0 0 18px;letter-spacing:-.08em;text-shadow:0 0 50px #00e5ff33}
h2{font-size:1.65rem;margin:0 0 12px}
p{color:var(--muted);font-size:1.06rem;line-height:1.7}
.stats{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
.metric{font-size:2.7rem;font-weight:950;color:var(--cyan);line-height:1}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.card{position:relative;overflow:hidden;border:1px solid var(--line);background:#08101d;border-radius:22px;padding:20px;transition:.22s ease;min-height:220px}
.card:hover{transform:translateY(-5px);border-color:var(--cyan);box-shadow:0 16px 50px #00e5ff22}
.badge{display:inline-flex;padding:6px 10px;border-radius:999px;font-size:.72rem;font-weight:900;letter-spacing:.1em;margin-bottom:14px;text-transform:uppercase;background:rgba(60,255,179,.12);color:var(--green)}
.badge.building{background:rgba(168,85,247,.14);color:var(--purple)}
.badge.restored,.badge.operational{background:rgba(0,229,255,.13);color:var(--cyan)}
.timeline{margin-top:24px;display:grid;gap:12px}
.row{display:grid;grid-template-columns:180px 1fr;gap:18px;border:1px solid var(--line);border-radius:18px;padding:16px;background:#ffffff06}
.row strong{color:var(--cyan)}
.footer{padding:18px 24px;border-top:1px solid var(--line);display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;color:var(--muted);font-size:.88rem}
@media(max-width:1050px){.hero{grid-template-columns:1fr}.grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:720px){.topbar{flex-direction:column;align-items:flex-start}.grid,.stats,.row{grid-template-columns:1fr}main{padding:18px}h1{font-size:3.5rem}}
</style>
</head>
<body <?php body_class(); ?>>
<div class="shell">
<div class="frame">
<header class="topbar">
  <div class="brand">Proof Surface</div>
  <?php get_template_part('nav'); ?>
</header>

<main>
<section class="hero">
  <div class="panel">
    <span class="eyebrow">Recovered Systems / Public Receipts</span>
    <h1>Projects and Proof</h1>
    <p>Not claims. Proof lanes. Each project exists because Dream.OS recovered, deployed, verified, or operationalized a real system.</p>
  </div>
  <aside class="stats">
    <div class="panel"><div class="metric"><?php echo count($projects); ?></div><p>proof cards tracked</p></div>
    <div class="panel"><div class="metric">LIVE</div><p>canonical deploy source active</p></div>
    <div class="panel"><div class="metric">WP</div><p>WordPress theme + plugin controlled</p></div>
    <div class="panel"><div class="metric">SSH</div><p>server-side deploy lane unlocked</p></div>
  </aside>
</section>

<section class="grid">
<?php foreach ($projects as $project):
  $state = strtolower($project['state'] ?? 'active');
?>
  <article class="card">
    <span class="badge <?php echo esc_attr($state); ?>"><?php echo esc_html($state); ?></span>
    <h2><?php echo esc_html($project['name'] ?? 'Project'); ?></h2>
    <p><?php echo esc_html($project['proof'] ?? ($project['detail'] ?? 'Proof lane recorded.')); ?></p>
  </article>
<?php endforeach; ?>
</section>

<section class="panel" style="margin-top:24px">
  <span class="eyebrow">Recovery Timeline</span>
  <h2>What changed publicly</h2>
  <div class="timeline">
    <div class="row"><strong>Website Admin</strong><span>Unlocked SSH deploys, WordPress theme activation, REST status plugin, and canonical source deploy.</span></div>
    <div class="row"><strong>WeAreSwarm</strong><span>Converted from broken/default WordPress state into a live command center, skill tree, profile, live ops, and proof surface.</span></div>
    <div class="row"><strong>DigitalDreamscape</strong><span>Static deploy verified through the portfolio website admin workflow.</span></div>
    <div class="row"><strong>Portfolio Registry</strong><span>Controlled domains discovered and recovery order defined.</span></div>
  </div>
</section>
</main>

<footer class="footer">
<span>Proof before outreach.</span>
<span>Dream.OS recovery lanes produce public receipts.</span>
</footer>
</div>
</div>
<?php wp_footer(); ?>
</body>
</html>
