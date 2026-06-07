<?php
/*
Template Name: Victor Operator Profile
*/
?><!doctype html>
<html lang="en">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Victor Dixon | Dream.OS Operator</title>
<?php wp_head(); ?>
<style>
body{margin:0;font-family:system-ui,sans-serif;background:#050816;color:#eef4ff}
.wrap{max-width:1120px;margin:auto;padding:44px 22px 90px}
.card{background:#101827;border:1px solid #26374d;border-radius:28px;padding:30px;margin:18px 0}
h1{font-size:clamp(3rem,8vw,6rem);line-height:.9;letter-spacing:-.07em;margin:0 0 20px}
h2{font-size:2rem;margin:0 0 12px}
p,li{color:#b8c5d8;line-height:1.65;font-size:1.08rem}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.pill{display:inline-block;border:1px solid #26374d;border-radius:999px;padding:8px 12px;color:#8effb5;margin:6px 6px 0 0}
a{color:#63e6ff}
@media(max-width:800px){.grid{grid-template-columns:1fr}}

    .topnav{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .topnav a{color:var(--text,#eef4ff);text-decoration:none;border:1px solid var(--line,#26374d);border-radius:999px;padding:8px 12px;background:#ffffff08}
    .topnav a:hover{border-color:var(--accent,#63e6ff);box-shadow:0 0 18px #63e6ff33}

</style>
</head>
<body <?php body_class(); ?>>
<header style="max-width:1120px;margin:auto;padding:22px"><div class="brand">We Are Swarm</div><?php get_template_part('nav'); ?></header>
<main class="wrap">
  <section class="card">
    <h1>Victor Dixon</h1>
    <p><strong>Dream.OS architect / automation builder / multi-agent systems operator.</strong></p>
    <p>I build self-healing automation systems for codebases, website recovery, repo consolidation, homeschool tools, trading workflows, and operator-grade productivity infrastructure.</p>
    <a href="/skill-tree">View the Swarm Skill Tree →</a>
  </section>

  <section class="grid">
    <div class="card">
      <h2>Operating Style</h2>
      <span class="pill">execution-first</span>
      <span class="pill">trust-but-verify</span>
      <span class="pill">salvage before deletion</span>
      <span class="pill">small safe lanes</span>
      <span class="pill">TDD when it matters</span>
    </div>

    <div class="card">
      <h2>What I Build</h2>
      <ul>
        <li>Dream.OS runtime lanes and task artifacts</li>
        <li>Portfolio website admin and recovery systems</li>
        <li>Repo rescue, cleanup, and promotion pipelines</li>
        <li>Automation dashboards and live status surfaces</li>
      </ul>
    </div>
  </section>

  <section class="card">
    <h2>Current Focus</h2>
    <p>Turn WeAreSwarm into a public proof surface: live operations, unlocked capabilities, active plans, recovered websites, and machine-readable Dream.OS status.</p>
  </section>
</main>
<?php wp_footer(); ?>
</body>
</html>
