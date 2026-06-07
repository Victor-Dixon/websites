<?php
/*
Template Name: Task Board
*/
$status = function_exists('dreamos_swarm_status_data') ? dreamos_swarm_status_data() : array();
$tasks = $status['unfinished_tasks'] ?? array();

$dreamos_title = 'Task Board | WeAreSwarm';
$dreamos_active = 'tasks';
$dreamos_canonical = 'https://www.weareswarm.site/tasks/';
$dreamos_footer_left = 'Open execution lanes';
$dreamos_footer_right = 'Unfinished work stays visible until verified';

include get_template_directory() . '/inc/shell-head.php';
?>
    <section class="hero">
      <div class="hero-copy">
        <span class="eyebrow">Task Board</span>
        <h1><span>Open lanes</span><span class="grad">stay visible</span></h1>
        <p>Unfinished Dream.OS tasks with progress, next actions, and verification context — no hidden backlog.</p>
        <div class="hero-actions">
          <a class="btn" href="/feed/">View closeout feed</a>
          <a class="btn ghost" href="/live-ops/">Live operations</a>
        </div>
      </div>
      <aside class="hud">
        <div class="hud-card"><strong><?php echo count($tasks); ?></strong><span>open task lanes</span></div>
        <div class="hud-card"><strong>LIVE</strong><span>synced from status API</span></div>
      </aside>
    </section>

    <section class="section">
      <span class="eyebrow">Unfinished tasks</span>
      <h2>Active backlog</h2>
      <div class="grid-2">
        <?php foreach ($tasks as $task): ?>
        <article class="card">
          <span class="badge building"><?php echo esc_html($task['progress'] ?? strtoupper($task['state'] ?? 'OPEN')); ?></span>
          <h3><?php echo esc_html($task['task'] ?? ($task['name'] ?? 'Task')); ?></h3>
          <p><strong style="color:var(--text)">Next:</strong> <?php echo esc_html($task['next'] ?? 'Define verification gate.'); ?></p>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
<?php include get_template_directory() . '/inc/shell-foot.php'; ?>
