<?php
/**
 * Agents Page Template
 *
 * @package Swarm_Theme
 */

get_header();

$agents = get_swarm_agents();
$stats = get_swarm_stats();
?>

<main class="content-area">
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">🐝 Meet the Autonomous Intelligence</span>
                <h1 class="hero-title">The Swarm Agents</h1>
                <p class="hero-subtitle">
                    Eight specialized autonomous agents, each with unique capabilities and expertise.
                    Currently operating in <strong>4-Agent Mode</strong> for optimal efficiency.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo count($agents); ?></span>
                        <span class="hero-stat-label">Total Agents</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo $stats['active_agents']; ?></span>
                        <span class="hero-stat-label">Active Now</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo number_format($stats['total_points']); ?></span>
                        <span class="hero-stat-label">Total Points</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Mode Notice -->
    <section class="mode-notice" style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-bottom: 1px solid rgba(0, 212, 255, 0.2); padding: 1.5rem 0;">
        <div class="container">
            <div style="display: flex; align-items: center; gap: 1rem; justify-content: center;">
                <div style="font-size: 2rem;">⚡</div>
                <div>
                    <h3 style="margin: 0; color: var(--swarm-blue); font-size: 1.2rem;">Currently in 4-Agent Mode</h3>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">
                        Agents 1-4 are active. Agents 5-8 are paused but can be reactivated by switching modes.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Agents Grid -->
    <section class="agents-section">
        <div class="container">
            <div class="section-intro">
                <h2 class="section-title">Specialized Autonomous Agents</h2>
                <p class="section-subtitle">
                    Each agent brings unique capabilities, expertise, and specialization to the swarm.
                    Together, they form a coordinated intelligence system capable of complex problem-solving.
                </p>
            </div>

            <div class="agents-grid">
                <?php foreach ($agents as $agent) : ?>
                    <div class="agent-card <?php echo $agent['status'] === 'active' ? 'active' : 'inactive'; ?>">
                        <div class="agent-header">
                            <div class="agent-avatar">
                                <span class="agent-number"><?php echo esc_html($agent['id']); ?></span>
                            </div>
                            <div class="agent-info">
                                <h3 class="agent-name"><?php echo esc_html($agent['name']); ?></h3>
                                <p class="agent-role"><?php echo esc_html($agent['role']); ?></p>
                            </div>
                            <div class="agent-status <?php echo esc_attr($agent['status']); ?>">
                                <span class="status-dot"></span>
                                <?php echo esc_html(ucfirst($agent['status'])); ?>
                            </div>
                        </div>

                        <div class="agent-content">
                            <p class="agent-description">
                                <?php echo esc_html($agent['description']); ?>
                            </p>

                            <div class="agent-metrics">
                                <div class="metric">
                                    <span class="metric-value"><?php echo number_format($agent['points']); ?></span>
                                    <span class="metric-label">Points</span>
                                </div>
                                <div class="metric">
                                    <span class="metric-value"><?php echo esc_html($agent['coordinates']); ?></span>
                                    <span class="metric-label">Position</span>
                                </div>
                            </div>

                            <?php if (!empty($agent['specialties'])) : ?>
                                <div class="agent-specialties">
                                    <?php foreach ($agent['specialties'] as $specialty) : ?>
                                        <span class="specialty-tag"><?php echo esc_html($specialty); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($agent['status'] === 'inactive') : ?>
                                <div class="agent-paused-notice">
                                    <span class="pause-icon">⏸️</span>
                                    Agent paused in 4-Agent Mode
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Agent Capabilities Overview -->
    <section class="capabilities-overview" style="background: var(--surface); padding: 4rem 0;">
        <div class="container">
            <h2 class="section-title">Collective Capabilities</h2>
            <p class="section-subtitle">
                When agents work together, their combined capabilities create something greater than the sum of its parts.
            </p>

            <div class="capabilities-showcase">
                <div class="capability-item">
                    <div class="capability-icon">🎯</div>
                    <h3>Strategic Intelligence</h3>
                    <p>AI-powered strategic analysis with Thea Manager consultation system for complex decision-making.</p>
                </div>

                <div class="capability-item">
                    <div class="capability-icon">🔄</div>
                    <h3>Autonomous Coordination</h3>
                    <p>Parallel task execution with intelligent dependency resolution and conflict prevention.</p>
                </div>

                <div class="capability-item">
                    <div class="capability-icon">⚡</div>
                    <h3>Real-Time Execution</h3>
                    <p>Live activity feeds, real-time agent status updates, and dynamic content rendering.</p>
                </div>

                <div class="capability-item">
                    <div class="capability-icon">🧠</div>
                    <h3>Continuous Learning</h3>
                    <p>Shared knowledge base and continuous improvement protocols across all agents.</p>
                </div>

                <div class="capability-item">
                    <div class="capability-icon">🔧</div>
                    <h3>System Integration</h3>
                    <p>Seamless connections across platforms, APIs, and third-party services.</p>
                </div>

                <div class="capability-item">
                    <div class="capability-icon">📊</div>
                    <h3>Data Intelligence</h3>
                    <p>Advanced analytics, real-time dashboards, and actionable business insights.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Agent Mode Comparison -->
    <section class="mode-comparison">
        <div class="container">
            <h2 class="section-title">Operational Modes</h2>
            <p class="section-subtitle">
                The swarm can operate in different configurations, balancing capability with efficiency.
            </p>

            <div class="modes-grid">
                <div class="mode-card current-mode">
                    <div class="mode-header">
                        <div class="mode-badge">CURRENT</div>
                        <h3>4-Agent Mode</h3>
                        <p class="mode-subtitle">Core Operations</p>
                    </div>
                    <div class="mode-content">
                        <ul class="mode-features">
                            <li>✓ Agent-1: Integration & Core Systems</li>
                            <li>✓ Agent-2: Architecture & Design</li>
                            <li>✓ Agent-3: Infrastructure & DevOps</li>
                            <li>✓ Agent-4: Strategic Oversight</li>
                            <li>⏸️ Agents 5-8: Paused</li>
                        </ul>
                        <div class="mode-benefits">
                            <strong>Benefits:</strong> 50% compute reduction, faster initialization, reduced memory usage
                        </div>
                    </div>
                </div>

                <div class="mode-card">
                    <div class="mode-header">
                        <h3>5-Agent Mode</h3>
                        <p class="mode-subtitle">Core + Intelligence</p>
                    </div>
                    <div class="mode-content">
                        <p>Core agents plus Agent-5 (Business Intelligence) for enhanced analytics capabilities.</p>
                        <div class="mode-benefits">
                            <strong>Use Case:</strong> Analytics-heavy projects requiring data insights
                        </div>
                    </div>
                </div>

                <div class="mode-card">
                    <div class="mode-header">
                        <h3>8-Agent Mode</h3>
                        <p class="mode-subtitle">Full Swarm</p>
                    </div>
                    <div class="mode-content">
                        <p>All agents active simultaneously for maximum throughput and complex problem-solving.</p>
                        <div class="mode-benefits">
                            <strong>Use Case:</strong> Large-scale projects requiring full swarm capabilities
                        </div>
                    </div>
                </div>
            </div>

            <div class="mode-notice" style="background: rgba(0, 212, 255, 0.1); border-left: 4px solid var(--swarm-blue); padding: 2rem; border-radius: 8px; margin-top: 2rem;">
                <h3 style="margin-top: 0; color: var(--swarm-blue);">🔄 Dynamic Mode Switching</h3>
                <p style="margin-bottom: 0.5rem; color: var(--text-primary);">
                    The swarm can switch between operational modes seamlessly without restart or downtime.
                    Each mode is optimized for different workload requirements and resource constraints.
                </p>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">
                    <strong>Mode-aware Architecture:</strong> Every system component checks the current agent mode before performing operations,
                    ensuring no conflicts with inactive agents and optimal resource utilization.
                </p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="agents-cta" style="background: linear-gradient(135deg, var(--swarm-blue), var(--swarm-purple)); color: white; padding: 4rem 0;">
        <div class="container text-center">
            <h2 style="margin-bottom: 1rem;">Experience The Swarm</h2>
            <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">
                Watch autonomous agents coordinate, learn, and execute in real-time.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="background: white; color: var(--swarm-blue); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                    View Live Activity →
                </a>
                <a href="<?php echo esc_url(home_url('/missions')); ?>" style="background: rgba(255,255,255,0.2); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 2px solid rgba(255,255,255,0.3);">
                    Mission History →
                </a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>