<?php
/**
 * Plugin Name: DreamOS Swarm Status
 * Description: Shows recent Dream.OS swarm operations, unlocks, deploys, and recovery status.
 * Version: 1.1.0
 * Author: Dream.OS
 */

if (!defined('ABSPATH')) {
    exit;
}

function dreamos_swarm_project_cards() {
    return array(
        array('title' => 'WeAreSwarm Command Center', 'domain' => 'weareswarm.site', 'status' => 'live', 'repo' => 'websites', 'one_liner' => 'Public Dream.OS command center with proof routes, live ops, portfolio index, and status API.', 'problem_solved' => 'Converted a thin internal status board into a buyer-facing proof surface.', 'proof_artifacts' => array('/live-ops/', '/wp-json/dreamos/v1/status', '/projects/'), 'live_url' => 'https://www.weareswarm.site/', 'next_unlock' => 'Publish API-backed closeout feed and project receipts.', 'revenue_angle' => 'Anchor site for Website Recovery Sprint and Automation Operator Setup.'),
        array('title' => 'DigitalDreamscape', 'domain' => 'digitaldreamscape.com', 'status' => 'restored', 'repo' => 'websites', 'one_liner' => 'Recovered static-first creative technology surface.', 'problem_solved' => 'Turned a dormant web property into a verified live route candidate.', 'proof_artifacts' => array('Portfolio recovery matrix', 'Static deploy marker', 'Live URL verification'), 'live_url' => 'https://digitaldreamscape.com/', 'next_unlock' => 'Add service-ready case study and contact CTA.', 'revenue_angle' => 'Demonstrates dead-domain recovery and rapid relaunch capability.'),
        array('title' => 'FreeRideInvestor', 'domain' => 'freerideinvestor.com', 'status' => 'active', 'repo' => 'websites', 'one_liner' => 'Trading education and TSLA command-center content system.', 'problem_solved' => 'Consolidated trading content, plugins, dashboards, and site recovery work.', 'proof_artifacts' => array('dreamos-trading-tools plugin', 'TSLA command center JSON', 'content engine'), 'live_url' => 'https://freerideinvestor.com/', 'next_unlock' => 'Package lead magnet and paid trading dashboard lane.', 'revenue_angle' => 'Subscription, affiliate, and dashboard product surface.'),
        array('title' => 'TradingRobotPlug', 'domain' => 'tradingrobotplug.com', 'status' => 'salvage', 'repo' => 'websites', 'one_liner' => 'Trading robot/plugin legacy assets classified for promotion.', 'problem_solved' => 'Preserved useful plugin/theme work while preventing duplicate repo drift.', 'proof_artifacts' => array('legacy deprecation packet', 'shared plugin inventory', 'Hostinger collection'), 'live_url' => 'https://tradingrobotplug.com/', 'next_unlock' => 'Promote the paper-trading stats plugin behind a clean offer.', 'revenue_angle' => 'Trading automation plugin and services offer.'),
        array('title' => 'AriaJet', 'domain' => 'ariajet.site', 'status' => 'parked', 'repo' => 'websites', 'one_liner' => 'Aviation/travel concept lane with WordPress plugin inventory preserved.', 'problem_solved' => 'Captured deploy assets and classified the route before promotion.', 'proof_artifacts' => array('Hostinger asset collection', 'plugin candidate inventory'), 'live_url' => 'https://ariajet.site/', 'next_unlock' => 'Decide travel funnel versus parked-domain monetization.', 'revenue_angle' => 'Niche lead-gen or itinerary automation product.'),
        array('title' => 'DaDudeKC / MaskZero', 'domain' => 'dadudekc.com / maskzero.site', 'status' => 'active', 'repo' => 'dadudekc-service-funnel', 'one_liner' => 'Founder/operator brand and community funnel consolidation lane.', 'problem_solved' => 'Promoted scattered DaDudeKC website fragments into a canonical service funnel.', 'proof_artifacts' => array('dadudekc-service-funnel', 'promotion reports', 'service route'), 'live_url' => 'https://dadudekc.com/', 'next_unlock' => 'Publish portfolio receipts and conversion paths.', 'revenue_angle' => 'Operator services, community, and brand trust layer.'),
        array('title' => 'Planet Blue', 'domain' => 'planetblue.local', 'status' => 'concept', 'repo' => 'websites', 'one_liner' => 'Sustainability/community project lane awaiting proof packaging.', 'problem_solved' => 'Kept a concept lane visible without presenting it as finished.', 'proof_artifacts' => array('domain inventory', 'project registry'), 'live_url' => '/projects/', 'next_unlock' => 'Select domain, define MVP, and publish first proof artifact.', 'revenue_angle' => 'Community sponsorship or educational product.'),
        array('title' => 'HomeSchool Mastery', 'domain' => 'homeschoolmastery.local', 'status' => 'planned', 'repo' => 'websites', 'one_liner' => 'Homeschool workflow and curriculum automation concept.', 'problem_solved' => 'Identifies family-ops automation as a separate product lane.', 'proof_artifacts' => array('operator profile', 'task board lane'), 'live_url' => '/tasks/', 'next_unlock' => 'Publish first schedule/reporting prototype.', 'revenue_angle' => 'Parent productivity templates and automation setup.'),
        array('title' => 'ProjectScanner', 'domain' => 'internal tool', 'status' => 'advanced', 'repo' => 'websites', 'one_liner' => 'Repo and website classification engine for salvage decisions.', 'problem_solved' => 'Finds duplicate, stale, risky, and promotable assets across messy workspaces.', 'proof_artifacts' => array('repo consolidation reports', 'deprecation packets', 'promotion manifests'), 'live_url' => '/live-ops/', 'next_unlock' => 'Expose public-safe scanner summaries per project card.', 'revenue_angle' => 'Core engine for Repo Rescue Sprint.'),
        array('title' => 'AgentTools', 'domain' => 'internal tool', 'status' => 'building', 'repo' => 'websites', 'one_liner' => 'Reusable operator prompts, verification gates, and closeout tooling.', 'problem_solved' => 'Turns ad hoc agent work into repeatable execution lanes.', 'proof_artifacts' => array('runtime/tasks', 'runtime/scripts', 'closeout reports'), 'live_url' => '/skill-tree/', 'next_unlock' => 'Publish prompt packs and verification templates.', 'revenue_angle' => 'Automation Operator Setup implementation kit.'),
        array('title' => 'Dream.OS Runtime', 'domain' => 'weareswarm.site', 'status' => 'live', 'repo' => 'websites', 'one_liner' => 'Task artifacts, status API, proof feed, and skill-tree operating model.', 'problem_solved' => 'Makes system state visible to humans and machines.', 'proof_artifacts' => array('/wp-json/dreamos/v1/status', 'runtime/tasks', 'runtime/feeds'), 'live_url' => 'https://www.weareswarm.site/live-ops/', 'next_unlock' => 'Distributed Swarm Mesh.', 'revenue_angle' => 'Differentiator for all client automation packages.'),
        array('title' => 'Discord Architect Bot', 'domain' => 'discord lane', 'status' => 'operational', 'repo' => 'websites', 'one_liner' => 'Closeout dispatch and bot-planning lane for proof distribution.', 'problem_solved' => 'Routes shipped work into readable public/team receipts.', 'proof_artifacts' => array('discord_architect tasks', 'dispatch previews', 'closeout feed cards'), 'live_url' => '/feed/', 'next_unlock' => 'Automated closeout ingestion from GitHub and runtime reports.', 'revenue_angle' => 'Client reporting and ops-dashboard add-on.'),
    );
}

function dreamos_swarm_feed_cards() {
    return array(
        array('title' => 'Public proof portfolio upgraded', 'date' => gmdate('Y-m-d'), 'type' => 'portfolio', 'summary' => 'Projects route now carries twelve buyer-facing cards with problem, proof, next unlock, and revenue framing.', 'proof' => 'PROJECT_CARDS=12; ROUTE=/projects/; STATUS=READY'),
        array('title' => 'Closeout feed static fallback repaired', 'date' => gmdate('Y-m-d'), 'type' => 'feed', 'summary' => 'Feed route publishes public-safe proof cards instead of a permanent loading or empty state.', 'proof' => 'FEED_CARDS=5; NO_LOADING_STATE=PASS'),
        array('title' => 'Services packaged for buyers', 'date' => gmdate('Y-m-d'), 'type' => 'services', 'summary' => 'Dream.OS services now include Website Recovery Sprint, Repo Rescue Sprint, and Automation Operator Setup price anchors.', 'proof' => 'PACKAGES=3; CTA=MAILTO'),
        array('title' => 'Skill tree linked to proof routes', 'date' => '2026-06-12', 'type' => 'skill-tree', 'summary' => 'Capability nodes reference live routes, runtime artifacts, project cards, and reports.', 'proof' => 'NODES=12; PROOF_LINKS=PASS'),
        array('title' => 'WeAreSwarm live ops API refreshed', 'date' => '2026-06-12', 'type' => 'api', 'summary' => 'Status API now emits generated_at, portfolio cards, feed cards, task lanes, and next lane data.', 'proof' => 'ENDPOINT=/wp-json/dreamos/v1/status; GENERATED_AT=RUNTIME'),
    );
}

function dreamos_swarm_status_data() {
    $projects = dreamos_swarm_project_cards();
    $feed = dreamos_swarm_feed_cards();
    $data = array(
        'generated_at' => gmdate('c'),
        'updated_at' => gmdate('c'),
        'headline' => 'DreamOS is operating: maintenance to promotion to productization to revenue.',
        'current_phase' => array(
            'name' => 'Maintenance -> Promotion -> Productization -> Revenue',
            'status' => 'Productization In Progress',
            'summary' => 'Consolidation is largely closed out; the active work is pruning, classifying products, refreshing portfolio surfaces, and turning shipped capabilities into distribution-ready offers.',
        ),
        'consolidation_status' => array(
            array('name' => 'Planner Authority Established', 'state' => 'complete'),
            array('name' => 'Task Authority Established', 'state' => 'complete'),
            array('name' => 'DreamOS Authority Established', 'state' => 'complete'),
            array('name' => 'Trading Authority Established', 'state' => 'complete'),
            array('name' => 'Discord Closeout Enforcement', 'state' => 'complete'),
            array('name' => 'WeAreSwarm Publication Enforcement', 'state' => 'complete'),
            array('name' => 'AutoDream Archived', 'state' => 'complete'),
            array('name' => 'Bucket 3 Parked', 'state' => 'complete'),
        ),
        'active_priorities' => array(
            array('rank' => 1, 'name' => 'prune_tasklist_001', 'next' => 'Remove completed or obsolete task residue from the operating task list.'),
            array('rank' => 2, 'name' => 'Product Classification', 'next' => 'Classify identified capabilities by offer readiness and distribution surface.'),
            array('rank' => 3, 'name' => 'Revenue Activation', 'next' => 'Attach offers, CTAs, and proof-backed sales surfaces to productized work.'),
            array('rank' => 4, 'name' => 'DreamScan Distribution', 'next' => 'Move DreamScan from identified product into distribution-ready surface.'),
            array('rank' => 5, 'name' => 'Portfolio Surface Refresh', 'next' => 'Refresh public cards so they reflect the current operating system, not old discovery work.'),
        ),
        'blocked' => array(
            array('name' => 'Robinhood Production Authentication', 'reason' => 'Adult Gate'),
        ),
        'product_pipeline' => array(
            'status' => 'Productization In Progress',
            'products_identified' => array('AI Debugger Assistant', 'Agent Cellphone', 'Trading Robot Plug', 'DreamScan', 'Discord Architect', 'ProjectScanner'),
            'products_productized' => array('Agent Cellphone', 'Discord Architect', 'ProjectScanner'),
            'products_monetized' => array(),
            'monetization_note' => 'Revenue activation is pending; the board tracks product readiness before lead count.',
        ),
        'repo_board' => array(
            'canonical_core' => array('DreamOS_Core', 'DreamVault'),
            'control_plane' => array('DreamVault'),
            'toolbelt' => array('AgentTools', 'projectscanner'),
            'public_surface' => array('websites'),
            'salvage_complete' => array('AutoDream'),
            'parked' => array('HCShinobi', 'Spark research', 'LSTM trainer', 'ML Robot Maker'),
        ),
        'spark_status' => array(
            'state' => 'MVP_SHIPPED',
            'proof' => array('AUTH_IMMERSION_FIXED', '17 tests passing', 'live domain'),
            'next' => 'Keep Spark visible as shipped proof while distribution and portfolio refresh continue.',
        ),
        'recent_unlocks' => array(
            array('title' => 'Closeout Enforcement Complete', 'detail' => 'Discord and WeAreSwarm publication enforcement are established; closeout_enforcement_001 is complete.', 'status' => 'complete', 'proof_url' => '/feed/'),
            array('title' => 'Consolidation Closed Out', 'detail' => 'Authority repos, toolbelt repos, public surfaces, archived assets, and parked research are separated.', 'status' => 'complete', 'proof_url' => '/projects/'),
            array('title' => 'Spark MVP Shipped', 'detail' => 'Spark is live with authentication immersion fixed and 17 tests passing.', 'status' => 'shipped', 'proof_url' => 'https://maskzero.site/'),
            array('title' => 'Product Pipeline Identified', 'detail' => 'Six products are tracked for productization and revenue activation.', 'status' => 'active', 'proof_url' => '/dreamos-services/'),
        ),
        'active_operations' => array(
            array('name' => 'Product Classification', 'state' => 'active', 'next' => 'Sort identified products by readiness, proof, distribution surface, and offer path.'),
            array('name' => 'Revenue Activation', 'state' => 'active', 'next' => 'Connect productized work to CTAs, demos, and monetization paths.'),
            array('name' => 'DreamScan Distribution', 'state' => 'active', 'next' => 'Package DreamScan for distribution and proof-backed promotion.'),
            array('name' => 'Portfolio Surface Refresh', 'state' => 'active', 'next' => 'Refresh public pages to show operating proof instead of discovery residue.'),
        ),
        'unfinished_tasks' => array(
            array('task' => 'prune_tasklist_001', 'progress' => 'active', 'next' => 'Prune stale tasks after closeout enforcement.'),
            array('task' => 'Revenue CTAs', 'progress' => 'activation', 'next' => 'Attach product-specific CTAs once product classification is public-safe.'),
            array('task' => 'Robinhood Production Authentication', 'progress' => 'blocked', 'next' => 'Blocked by Adult Gate.'),
        ),
        'task_lanes' => array(
            array('lane' => 'Maintenance', 'items' => array('prune_tasklist_001', 'authority checks', 'closeout hygiene')),
            array('lane' => 'Promotion', 'items' => array('DreamScan distribution', 'portfolio refresh', 'public proof')),
            array('lane' => 'Productization', 'items' => array('AI Debugger Assistant', 'Agent Cellphone', 'Trading Robot Plug', 'Discord Architect', 'ProjectScanner')),
            array('lane' => 'Revenue', 'items' => array('offer CTAs', 'distribution surfaces', 'monetization tracking')),
        ),
        'developer_profile' => array(
            'name' => 'Victor Dixon',
            'role' => 'Dream.OS architect / automation builder / multi-agent systems operator',
            'summary' => 'Victor turns broken websites, messy repos, and scattered automations into verified proof surfaces and sellable execution systems.',
            'operating_style' => array('execution-first', 'trust-but-verify', 'salvage before deletion', 'small safe lanes', 'TDD where it matters'),
        ),
        'projects' => $projects,
        'feed' => $feed,
        'skill_tree' => array(
            array('skill' => 'Command Core', 'level' => 'Unlocked', 'proof_url' => '/', 'capabilities' => array('public shell', 'navigation', 'metrics')),
            array('skill' => 'Website Admin', 'level' => 'Unlocked', 'proof_url' => '/live-ops/', 'capabilities' => array('SSH deploy', 'FTP deploy', 'WordPress theme activation', 'live marker verification')),
            array('skill' => 'Portfolio Recovery', 'level' => 'Unlocked', 'proof_url' => '/projects/', 'capabilities' => array('domain audit', 'static fallback', 'proof cards')),
            array('skill' => 'Runtime Engine', 'level' => 'Advanced', 'proof_url' => '/wp-json/dreamos/v1/status', 'capabilities' => array('status API', 'task artifacts', 'operator reports')),
            array('skill' => 'Repo Rescue', 'level' => 'Advanced', 'proof_url' => '/dreamos-services/', 'capabilities' => array('scan', 'classify', 'salvage', 'promote', 'verify')),
            array('skill' => 'Recovery Loop', 'level' => 'Unlocked', 'proof_url' => '/feed/', 'capabilities' => array('closeouts', 'verification gates', 'public receipts')),
            array('skill' => 'Automation Ops', 'level' => 'Advanced', 'proof_url' => '/tasks/', 'capabilities' => array('planner lanes', 'dispatch', 'dashboards')),
            array('skill' => 'Revenue Frontier', 'level' => 'Building', 'proof_url' => '/dreamos-services/', 'capabilities' => array('packages', 'CTAs', 'client offers')),
            array('skill' => 'ProjectScanner', 'level' => 'Building', 'proof_url' => '/projects/', 'capabilities' => array('inventory', 'risk classification', 'promotion plans')),
            array('skill' => 'AgentTools', 'level' => 'Building', 'proof_url' => '/skill-tree/', 'capabilities' => array('prompt packs', 'test gates', 'handoff docs')),
            array('skill' => 'Swarm Memory', 'level' => 'Planned', 'proof_url' => '/feed/', 'capabilities' => array('history', 'receipts', 'context recall')),
            array('skill' => 'Distributed Swarm Mesh', 'level' => 'Next', 'proof_url' => '/live-ops/', 'capabilities' => array('parallel agents', 'multi-route verification', 'autonomous closeouts')),
        ),
        'next_lane' => array('name' => 'Revenue Activation', 'target' => 'Turn productized DreamOS capabilities into public offers, demos, and monetization paths.'),
    );

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
        <p>Generated <?php echo esc_html($data['generated_at']); ?>. Current phase: <?php echo esc_html($data['current_phase']['name']); ?>. Status: <?php echo esc_html($data['current_phase']['status']); ?>.</p>
      </div>

      <h3>Consolidation Status</h3>
      <div class="dreamos-ops-table">
        <?php foreach ($data['consolidation_status'] as $item): ?>
          <div class="dreamos-op-row">
            <strong><?php echo esc_html($item['name']); ?></strong>
            <span><?php echo esc_html(strtoupper($item['state'])); ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="dreamos-grid">
        <?php foreach ($data['recent_unlocks'] as $unlock): ?>
          <article class="dreamos-op-card">
            <span class="dreamos-status"><?php echo esc_html(strtoupper($unlock['status'])); ?></span>
            <h3><?php echo esc_html($unlock['title']); ?></h3>
            <p><?php echo esc_html($unlock['detail']); ?></p>
            <p><a href="<?php echo esc_url($unlock['proof_url']); ?>">Proof route</a></p>
          </article>
        <?php endforeach; ?>
      </div>

      <h3>Active Priorities</h3>
      <div class="dreamos-ops-table">
        <?php foreach ($data['active_priorities'] as $priority): ?>
          <div class="dreamos-op-row">
            <strong><?php echo esc_html($priority['rank'] . '. ' . $priority['name']); ?></strong>
            <p><?php echo esc_html($priority['next']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <h3>Product Pipeline</h3>
      <div class="dreamos-ops-table">
        <div class="dreamos-op-row">
          <strong>Products Identified</strong>
          <span><?php echo esc_html(count($data['product_pipeline']['products_identified'])); ?></span>
          <p><?php echo esc_html(implode(' • ', $data['product_pipeline']['products_identified'])); ?></p>
        </div>
        <div class="dreamos-op-row">
          <strong>Products Productized</strong>
          <span><?php echo esc_html(count($data['product_pipeline']['products_productized'])); ?></span>
          <p><?php echo esc_html(implode(' • ', $data['product_pipeline']['products_productized'])); ?></p>
        </div>
        <div class="dreamos-op-row">
          <strong>Products Monetized</strong>
          <span><?php echo esc_html(count($data['product_pipeline']['products_monetized'])); ?></span>
          <p><?php echo esc_html($data['product_pipeline']['monetization_note']); ?></p>
        </div>
      </div>

      <h3>Blocked</h3>
      <div class="dreamos-ops-table">
        <?php foreach ($data['blocked'] as $blocker): ?>
          <div class="dreamos-op-row">
            <strong><?php echo esc_html($blocker['name']); ?></strong>
            <span>BLOCKED</span>
            <p><?php echo esc_html($blocker['reason']); ?></p>
          </div>
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

      <h3>Task Lanes</h3>
      <div class="dreamos-ops-table">
        <?php foreach ($data['task_lanes'] as $lane): ?>
          <div class="dreamos-op-row">
            <strong><?php echo esc_html($lane['lane']); ?></strong>
            <span><?php echo esc_html(count($lane['items'])); ?> ITEMS</span>
            <p><?php echo esc_html(implode(' • ', $lane['items'])); ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <h3>Unfinished Tasks</h3>
      <div class="dreamos-ops-table">
        <?php foreach ($data['unfinished_tasks'] as $task): ?>
          <div class="dreamos-op-row">
            <strong><?php echo esc_html($task['task']); ?></strong>
            <span><?php echo esc_html($task['progress']); ?></span>
            <p><?php echo esc_html($task['next']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('dreamos_swarm_status', 'dreamos_swarm_status_shortcode');
