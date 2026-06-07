<?php
/**
 * Plugin Name: DreamOS Swarm Status
 * Description: Shows recent Dream.OS swarm operations, unlocks, deploys, and recovery status.
 * Version: 1.0.0
 * Author: Dream.OS
 */

if (!defined('ABSPATH')) {
    exit;
}

function dreamos_swarm_status_generated_path() {
    $candidates = array(
        dirname(__DIR__, 3) . '/data/swarm-status.generated.json',
        dirname(__DIR__, 4) . '/runtime/content/weareswarm.site/data/swarm-status.generated.json',
    );

    foreach ($candidates as $path) {
        if (is_readable($path)) {
            return $path;
        }
    }

    return null;
}

function dreamos_swarm_status_fallback() {
    return array(
        'updated_at' => gmdate('c'),
        'headline' => 'The swarm is online.',
        'recent_unlocks' => array(
            array(
                'title' => 'Website Admin Unlocked',
                'detail' => 'Dream.OS can now deploy and verify managed portfolio websites through SSH/FTP lanes.',
                'status' => 'unlocked',
            ),
            array(
                'title' => 'WeAreSwarm Theme Activated',
                'detail' => 'Custom WordPress theme deployed and activated through SSH + WP-CLI.',
                'status' => 'done',
            ),
            array(
                'title' => 'Portfolio Registry Built',
                'detail' => 'Controlled domains were discovered, classified, and moved into a recovery matrix.',
                'status' => 'done',
            ),
            array(
                'title' => 'DigitalDreamscape Restored',
                'detail' => 'Static deploy verified with live markers.',
                'status' => 'done',
            ),
        ),
        'active_operations' => array(
            array('name' => 'Portfolio Recovery', 'state' => 'active', 'next' => 'Restore and classify all controlled domains.'),
            array('name' => 'Website Admin System', 'state' => 'active', 'next' => 'Promote SSH deploy, FTP deploy, live verification, and registry logic into durable modules.'),
            array('name' => 'Outreach Gate', 'state' => 'blocked', 'next' => 'Start only after the portfolio surface proves the swarm can recover its own sites.'),
        ),
        'unfinished_tasks' => array(
            array('task' => 'Restore WeAreSwarm as live operations board', 'progress' => '80%', 'next' => 'Publish skill tree, plans, and active lanes.'),
            array('task' => 'Portfolio domain recovery', 'progress' => '35%', 'next' => 'Audit WordPress vs static-first fit for each controlled domain.'),
            array('task' => 'Website admin subsystem', 'progress' => '55%', 'next' => 'Convert scripts into durable Dream.OS modules with tests.'),
            array('task' => 'Outreach pack', 'progress' => '20%', 'next' => 'Resume after public proof surface is complete.'),
        ),
        'developer_profile' => array(
            'name' => 'Victor Dixon',
            'role' => 'Dream.OS architect / automation builder / multi-agent systems operator',
            'summary' => 'Victor builds self-healing automation systems for repo recovery, website deployment, homeschool tooling, trading workflows, and personal productivity infrastructure.',
            'operating_style' => array('execution-first', 'trust-but-verify', 'salvage before deletion', 'small safe lanes', 'TDD where it matters'),
        ),
        'projects' => array(
            array('name' => 'WeAreSwarm Theme Unification', 'state' => 'active', 'proof' => 'Command center, feed, projects, tasks, profile, live ops, and skill tree share the Dream.OS wow shell and unified nav.'),
            array('name' => 'Route Recovery', 'state' => 'operational', 'proof' => 'Flat static deploys fixed www/apex redirect loops on /projects/, /feed/, /tasks/, /profile/, and /live-ops/.'),
            array('name' => 'Website Admin', 'state' => 'active', 'proof' => 'SSH deploy, WP-CLI activation, canonical website source, live verification.'),
            array('name' => 'DigitalDreamscape Restored', 'state' => 'restored', 'proof' => 'Static deploy verified through portfolio website admin lane.'),
            array('name' => 'WeAreSwarm Live Ops', 'state' => 'active', 'proof' => 'Custom theme, REST status plugin, Skill Tree, Operator Profile, Live Ops page.'),
            array('name' => 'Portfolio Registry', 'state' => 'building', 'proof' => 'Controlled domains classified and recovery matrix generated.'),
            array('name' => 'Repo Rescue', 'state' => 'advanced', 'proof' => 'Scan, classify, salvage, promote, verify, and commit workflow.'),
            array('name' => 'Verification Gates', 'state' => 'operational', 'proof' => 'Every lane ends with live markers, reports, and closeout packets.'),
        ),
        'skill_tree' => array(
            array('skill' => 'Website Admin', 'level' => 'Unlocked', 'capabilities' => array('SSH deploy', 'FTP deploy', 'WordPress theme activation', 'REST status plugin', 'live marker verification')),
            array('skill' => 'Repo Rescue', 'level' => 'Advanced', 'capabilities' => array('scan', 'classify', 'salvage', 'promote', 'verify', 'commit')),
            array('skill' => 'Swarm Runtime', 'level' => 'Building', 'capabilities' => array('task artifacts', 'runtime scripts', 'operator reports', 'portfolio registry')),
            array('skill' => 'Automation Ops', 'level' => 'Advanced', 'capabilities' => array('terminal lanes', 'CPC reports', 'verification gates', 'closeout packets')),
        ),
    );
}

function dreamos_swarm_status_data() {
    $data = dreamos_swarm_status_fallback();
    $generated_path = dreamos_swarm_status_generated_path();

    if ($generated_path) {
        $raw = file_get_contents($generated_path);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $data = array_merge($data, $decoded);
            if (!empty($decoded['generated_at'])) {
                $data['updated_at'] = $decoded['generated_at'];
            }
        }
    }

    return apply_filters('dreamos_swarm_status_data', $data);
}

function dreamos_swarm_status_rest() {
    register_rest_route('dreamos/v1', '/status', array(
        'methods' => 'GET',
        'callback' => function () {
            return rest_ensure_response(dreamos_swarm_status_data());
        },
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'dreamos_swarm_status_rest');

function dreamos_swarm_status_shortcode() {
    $data = dreamos_swarm_status_data();
    ob_start();
    ?>
    <section class="dreamos-liveops">
      <div class="dreamos-section-head">
        <p class="eyebrow">Live Swarm Operations</p>
        <h2><?php echo esc_html($data['headline']); ?></h2>
        <p>Recent unlocks, recovery lanes, and active system work are published here as proof that the swarm is operating.</p>
      </div>

      <div class="dreamos-grid">
        <?php foreach ($data['recent_unlocks'] as $unlock): ?>
          <article class="dreamos-op-card">
            <span class="dreamos-status"><?php echo esc_html(strtoupper($unlock['status'])); ?></span>
            <h3><?php echo esc_html($unlock['title']); ?></h3>
            <p><?php echo esc_html($unlock['detail']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>

      <h3>Active Plans</h3>
      <div class="dreamos-ops-table">
        <?php foreach ($data['active_operations'] as $op): ?>
          <div class="dreamos-op-row">
            <strong><?php echo esc_html($op['name']); ?></strong>
            <span><?php echo esc_html(strtoupper($op['state'])); ?></span>
            <p><?php echo esc_html($op['next']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <h3>Unfinished Tasks</h3>
      <div class="dreamos-ops-table">
        <?php foreach ($data['unfinished_tasks'] as $task): ?>
          <div class="dreamos-op-row">
            <strong><?php echo esc_html($task['task'] ?? ($task['name'] ?? 'Task')); ?></strong>
            <span><?php echo esc_html($task['progress'] ?? strtoupper($task['state'] ?? 'OPEN')); ?></span>
            <p><?php echo esc_html($task['next']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <h3>Developer Profile</h3>
      <div class="dreamos-op-card">
        <h3><?php echo esc_html($data['developer_profile']['name']); ?></h3>
        <p><strong><?php echo esc_html($data['developer_profile']['role']); ?></strong></p>
        <p><?php echo esc_html($data['developer_profile']['summary']); ?></p>
        <p><?php echo esc_html(implode(' • ', $data['developer_profile']['operating_style'])); ?></p>
      </div>

      <h3>Swarm Skill Tree</h3>
      <div class="dreamos-grid">
        <?php foreach ($data['skill_tree'] as $skill): ?>
          <article class="dreamos-op-card">
            <span class="dreamos-status"><?php echo esc_html(strtoupper($skill['level'])); ?></span>
            <h3><?php echo esc_html($skill['skill']); ?></h3>
            <p><?php echo esc_html(implode(' • ', $skill['capabilities'])); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('dreamos_swarm_status', 'dreamos_swarm_status_shortcode');
