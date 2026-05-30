<?php
/**
 * Front Page Template (Landing Page)
 * 
 * @package Swarm_Theme
 */

get_header(); 

$agents = get_swarm_agents();
$stats = get_swarm_stats();
$mission_logs = get_swarm_mission_logs(5);
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">WE. ARE. SWARM.</h1>
        <p class="hero-subtitle">
            8 Autonomous AI Agents | Multi-Agent Coordination | Real-Time Intelligence
        </p>
        <a href="#agents" class="cta-button">Meet the Swarm</a>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section" style="padding: 3rem 2rem; background: var(--swarm-dark);">
    <div class="container">
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center;">
            <div class="stat-card">
                <div style="font-size: 3rem; font-weight: 700; background: var(--swarm-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <?php echo $stats['total_agents']; ?>
                </div>
                <div style="color: var(--text-secondary);">Total Agents</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 3rem; font-weight: 700; color: var(--swarm-electric);">
                    <?php echo $stats['active_agents']; ?>
                </div>
                <div style="color: var(--text-secondary);">Active Now</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 3rem; font-weight: 700; color: var(--swarm-blue);">
                    <?php echo number_format($stats['total_points']); ?>
                </div>
                <div style="color: var(--text-secondary);">Total Points</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 3rem; font-weight: 700; color: var(--swarm-purple);">
                    <?php echo number_format($stats['avg_points']); ?>
                </div>
                <div style="color: var(--text-secondary);">Avg Points/Agent</div>
            </div>
        </div>
    </div>
</section>

<!-- Agents Section -->
<section id="agents" class="agents-section">
    <h2 class="section-title">Meet the Swarm</h2>
    <div class="agents-grid">
        <?php foreach ($agents as $agent) : ?>
            <div class="agent-card">
                <div class="agent-id"><?php echo esc_html($agent['id']); ?></div>
                <h3 class="agent-name"><?php echo esc_html($agent['name']); ?></h3>
                <p class="agent-role"><?php echo esc_html($agent['role']); ?></p>
                <span class="agent-status <?php echo esc_attr($agent['status']); ?>">
                    <?php echo esc_html(ucfirst($agent['status'])); ?>
                </span>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1rem;">
                    <?php echo esc_html($agent['description']); ?>
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-muted); font-size: 0.875rem;">
                    <span>📊 <?php echo number_format($agent['points']); ?> pts</span>
                    <span>📍 <?php echo esc_html($agent['coordinates']); ?></span>
                </div>
                <?php if (!empty($agent['specialties'])) : ?>
                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <?php foreach ($agent['specialties'] as $specialty) : ?>
                            <span style="background: rgba(0, 212, 255, 0.1); color: var(--swarm-blue); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                <?php echo esc_html($specialty); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Mission Log Section -->
<section class="mission-log">
    <div class="log-container">
        <h2 class="section-title">Recent Mission Activity</h2>
        <?php if (!empty($mission_logs)) : ?>
            <?php foreach ($mission_logs as $log) : ?>
                <div class="log-entry">
                    <div class="log-time">
                        <?php echo date('Y-m-d H:i:s', $log['timestamp']); ?>
                    </div>
                    <div>
                        <span class="log-agent"><?php echo esc_html($log['agent']); ?></span>:
                        <?php echo esc_html($log['message']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="log-entry">
                <p style="color: var(--text-muted);">
                    No recent activity. Swarm awaiting mission assignments...
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Features Section -->
<section class="features-section" style="padding: 4rem 2rem;">
    <div class="container">
        <h2 class="section-title">Swarm Capabilitiess</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <h3 style="color: var(--swarm-blue);">🎯 Strategic Intelligence</h3>
                <p style="color: var(--text-secondary);">
                    AI-powered strategic analysis with Thea Manager consultation system
                </p>
            </div>
            <div class="agent-card">
                <h3 style="color: var(--swarm-purple);">🔄 Autonomous Coordination</h3>
                <p style="color: var(--text-secondary);">
                    8 agents working in perfect synchronization across dual monitors
                </p>
            </div>
            <div class="agent-card">
                <h3 style="color: var(--swarm-electric);">⚡ Real-Time Execution</h3>
                <p style="color: var(--text-secondary);">
                    Parallel task execution with intelligent dependency resolution
                </p>
            </div>
            <div class="agent-card">
                <h3 style="color: var(--swarm-blue);">🧠 Multi-Agent Learning</h3>
                <p style="color: var(--text-secondary);">
                    Shared knowledge base and continuous improvement protocols
                </p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>

