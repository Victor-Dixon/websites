<?php
$status = function_exists('dreamos_swarm_status_data')
  ? dreamos_swarm_status_data()
  : array();

$recent_unlocks = $status['recent_unlocks'] ?? array();
$unfinished = $status['unfinished_tasks'] ?? array();
$operations = $status['active_operations'] ?? array();
$skills = $status['skill_tree'] ?? array();
?><!doctype html>
<html lang="en">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dream.OS Command Center | WeAreSwarm</title>
<?php wp_head(); ?>

<style>
:root{
--bg:#040816;
--panel:#09101d;
--panel2:#10192a;
--line:#1d3357;
--text:#eef3ff;
--muted:#8da2c0;
--cyan:#00e5ff;
--green:#3cffb3;
--purple:#a855f7;
--red:#ff5f7a;
}

*{box-sizing:border-box}

body{
margin:0;
font-family:Inter,system-ui,sans-serif;
background:
radial-gradient(circle at top right,rgba(0,229,255,.14),transparent 30rem),
radial-gradient(circle at bottom left,rgba(168,85,247,.18),transparent 30rem),
var(--bg);
color:var(--text);
overflow-x:hidden;
}

body:before{
content:"";
position:fixed;
inset:0;
background-image:
linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px),
linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px);
background-size:42px 42px;
mask-image:radial-gradient(circle at center,black,transparent 80%);
pointer-events:none;
}

.shell{
max-width:1550px;
margin:auto;
padding:24px;
position:relative;
z-index:2;
}

.frame{
border:1px solid var(--line);
border-radius:28px;
overflow:hidden;
background:rgba(9,16,29,.78);
backdrop-filter:blur(18px);
box-shadow:0 30px 100px #000a;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:18px 24px;
border-bottom:1px solid var(--line);
background:#050b16dd;
backdrop-filter:blur(18px);
}

.brand{
font-size:1rem;
font-weight:900;
letter-spacing:.18em;
text-transform:uppercase;
}

main{
display:grid;
grid-template-columns:1.2fr .8fr;
gap:24px;
padding:30px;
}

.hero{
padding:18px 8px;
}

.eyebrow{
display:inline-block;
color:var(--green);
font-size:.76rem;
font-weight:900;
letter-spacing:.16em;
text-transform:uppercase;
margin-bottom:16px;
}

.hero h1{
font-size:clamp(4rem,10vw,8rem);
line-height:.88;
margin:0 0 18px;
letter-spacing:-.08em;
text-shadow:0 0 50px #00e5ff33;
}

.hero p{
max-width:760px;
font-size:1.15rem;
line-height:1.7;
color:var(--muted);
}

.button-row{
display:flex;
gap:14px;
flex-wrap:wrap;
margin-top:26px;
}

.button-row a{
text-decoration:none;
padding:14px 18px;
border-radius:16px;
font-weight:800;
border:1px solid var(--line);
color:var(--text);
background:#ffffff08;
transition:.2s ease;
}

.button-row a:hover{
transform:translateY(-2px);
border-color:var(--cyan);
box-shadow:0 0 24px #00e5ff33;
}

.panel-grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:18px;
align-content:start;
}

.panel{
position:relative;
overflow:hidden;
border:1px solid var(--line);
background:linear-gradient(180deg,var(--panel2),var(--panel));
border-radius:24px;
padding:22px;
min-height:180px;
}

.panel:before{
content:"";
position:absolute;
top:0;
left:0;
width:100%;
height:2px;
background:linear-gradient(90deg,var(--cyan),transparent);
}

.panel h3{
margin:0 0 14px;
font-size:1rem;
text-transform:uppercase;
letter-spacing:.08em;
}

.metric{
font-size:3rem;
font-weight:900;
color:var(--cyan);
line-height:1;
margin-bottom:8px;
}

.feed{
margin-top:28px;
display:grid;
grid-template-columns:repeat(3,1fr);
gap:18px;
}

.feed-card{
border:1px solid var(--line);
background:#0b1322;
border-radius:20px;
padding:18px;
transition:.2s ease;
}

.feed-card:hover{
transform:translateY(-4px);
border-color:var(--cyan);
}

.badge{
display:inline-flex;
padding:6px 10px;
border-radius:999px;
font-size:.72rem;
font-weight:900;
letter-spacing:.1em;
margin-bottom:14px;
}

.badge-green{
background:rgba(60,255,179,.12);
color:var(--green);
}

.badge-purple{
background:rgba(168,85,247,.14);
color:var(--purple);
}

.feed-card h4{
margin:0 0 10px;
font-size:1rem;
}

.feed-card p{
margin:0;
font-size:.95rem;
line-height:1.6;
color:var(--muted);
}

.ops{
margin-top:26px;
border:1px solid var(--line);
border-radius:24px;
padding:20px;
background:#08101d;
}

.ops-row{
display:flex;
justify-content:space-between;
gap:18px;
padding:14px 0;
border-bottom:1px solid rgba(255,255,255,.06);
}

.ops-row:last-child{
border-bottom:none;
}

.ops-name{
font-weight:800;
}

.ops-state{
font-size:.8rem;
font-weight:900;
letter-spacing:.08em;
text-transform:uppercase;
}

.state-active{color:var(--green)}
.state-blocked{color:var(--red)}

.skill-mini{
margin-top:24px;
display:flex;
flex-wrap:wrap;
gap:12px;
}

.skill-chip{
padding:10px 14px;
border-radius:999px;
border:1px solid var(--line);
background:#ffffff08;
font-size:.9rem;
font-weight:700;
}

.footer{
padding:18px 24px;
border-top:1px solid var(--line);
display:flex;
justify-content:space-between;
flex-wrap:wrap;
gap:12px;
color:var(--muted);
font-size:.88rem;
}

@media(max-width:1100px){
main{
grid-template-columns:1fr;
}
.feed{
grid-template-columns:1fr;
}
}

@media(max-width:760px){
.hero h1{
font-size:3.5rem;
}
.panel-grid{
grid-template-columns:1fr;
}
main{
padding:18px;
}
.topbar{
flex-direction:column;
align-items:flex-start;
gap:14px;
}
}

@media(prefers-reduced-motion:no-preference){
.panel,.feed-card{
animation:fadeUp .45s ease both;
}
.feed-card:nth-child(2){animation-delay:.06s}
.feed-card:nth-child(3){animation-delay:.12s}

@keyframes fadeUp{
from{
opacity:0;
transform:translateY(10px);
}
to{
opacity:1;
transform:none;
}
}
}
</style>
</head>

<body <?php body_class(); ?>>

<div class="shell">
<div class="frame">

<header class="topbar">
<div class="brand">Dream.OS Command Center</div>
<?php get_template_part('nav'); ?>
</header>

<main>

<section class="hero">

<span class="eyebrow">Live Autonomous Operations</span>

<h1>Watch the swarm work.</h1>

<p>
Dream.OS is a live automation swarm for repo recovery,
website deployment, orchestration workflows, trading systems,
homeschool infrastructure, and autonomous operational tooling.
</p>

<div class="button-row">
<a href="/skill-tree">Open Skill Tree</a>
<a href="/projects/">View Proof</a>
<a href="/profile/">Operator Profile</a>
<a href="/live-ops/">Live Operations</a>
</div>

<div class="feed">

<?php foreach(array_slice($recent_unlocks,0,3) as $unlock): ?>
<div class="feed-card">
<span class="badge badge-green">
<?php echo esc_html($unlock['status'] ?? 'LIVE'); ?>
</span>

<h4><?php echo esc_html($unlock['title'] ?? 'Unlock'); ?></h4>

<p><?php echo esc_html($unlock['detail'] ?? ''); ?></p>
</div>
<?php endforeach; ?>

</div>

<div class="ops">
<h3>Active Operations</h3>

<?php foreach($operations as $op): ?>
<div class="ops-row">

<div>
<div class="ops-name">
<?php echo esc_html($op['name'] ?? 'Operation'); ?>
</div>

<div style="color:var(--muted);margin-top:6px;">
<?php echo esc_html($op['next'] ?? ''); ?>
</div>
</div>

<div class="ops-state state-<?php echo strtolower($op['state'] ?? 'active'); ?>">
<?php echo esc_html($op['state'] ?? 'active'); ?>
</div>

</div>
<?php endforeach; ?>

</div>

</section>

<aside>

<div class="panel-grid">

<div class="panel">
<h3>Swarm Status</h3>
<div class="metric">LIVE</div>
<p>SSH deploys, WP-CLI activation, REST status APIs, and runtime verification are online.</p>
</div>

<div class="panel">
<h3>Skill Nodes</h3>
<div class="metric"><?php echo count($skills); ?></div>
<p>Capability branches currently tracked inside the Dream.OS swarm registry.</p>
</div>

<div class="panel">
<h3>Unfinished Tasks</h3>

<?php foreach(array_slice($unfinished,0,3) as $task): ?>
<div style="margin-bottom:16px;">
<div style="display:flex;justify-content:space-between;gap:12px;">
<strong><?php echo esc_html($task['task'] ?? 'Task'); ?></strong>
<span style="color:var(--cyan);font-weight:800;">
<?php echo esc_html($task['progress'] ?? '0%'); ?>
</span>
</div>

<div style="height:8px;border-radius:999px;background:#111b2d;margin-top:10px;overflow:hidden;">
<div style="height:100%;width:<?php echo esc_attr($task['progress'] ?? '0%'); ?>;background:linear-gradient(90deg,var(--cyan),var(--purple));"></div>
</div>
</div>
<?php endforeach; ?>

</div>

<div class="panel">
<h3>Capability Branches</h3>

<div class="skill-mini">
<?php foreach(array_slice($skills,0,8) as $skill): ?>
<div class="skill-chip">
<?php echo esc_html($skill['skill'] ?? 'Capability'); ?>
</div>
<?php endforeach; ?>
</div>

</div>

</div>

</aside>

</main>

<footer class="footer">
<span>Systems that recover, verify, and ship.</span>
<span>Dream.OS autonomous operations layer</span>
</footer>

</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
