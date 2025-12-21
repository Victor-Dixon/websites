<?php
/**
 * Front Page Template - Public Showcase
 * 
 * Showcases web development capabilities and live swarm activity
 * 
 * @package Swarm_Theme
 */

get_header(); 

$agents = get_swarm_agents();
$stats = get_swarm_stats();
$mission_logs = get_swarm_mission_logs(20);
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <span class="hero-badge">🐝 Live Swarm Intelligence - 4-Agent Mode Active</span>
        <h1 class="hero-title">WE. ARE. SWARM.</h1>
        <p class="hero-subtitle">
            A revolutionary multi-agent AI system showcasing advanced web development, autonomous coordination, 
            and real-time intelligence. Currently operating in optimized 4-agent mode for maximum efficiency.
        </p>
        
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-value"><?php echo $stats['total_agents']; ?></span>
                <span class="hero-stat-label">Active Agents</span>
                <span class="hero-stat-note" style="font-size: 0.75rem; opacity: 0.7;">(4-Agent Mode)</span>
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
        
        <div class="cta-buttons">
            <a href="#capabilities" class="cta-button">View Capabilities</a>
            <a href="#activity" class="cta-button cta-button-secondary">Live Activity</a>
            <a href="#agent-modes" class="cta-button cta-button-secondary">Agent Modes</a>
        </div>
    </div>
</section>

<!-- Capabilities Section - Web Development Showcase -->
<section id="capabilities" class="section capabilities-section">
    <div class="container">
        <h2 class="section-title">Web Development Capabilities</h2>
        <p class="section-subtitle">
            This site itself is a demonstration of our capabilities. Built with modern WordPress 
            development, custom themes, REST APIs, and real-time data integration.
        </p>
        
        <div class="capabilities-grid">
            <div class="capability-card">
                <span class="capability-icon">🌐</span>
                <h3 class="capability-title">WordPress Development</h3>
                <p class="capability-description">
                    Custom theme development, plugin architecture, REST API integration, 
                    and performance optimization. This site runs on a fully custom theme 
                    built from scratch.
                </p>
                <div class="capability-tech">
                    <span class="tech-tag">PHP</span>
                    <span class="tech-tag">WordPress</span>
                    <span class="tech-tag">REST API</span>
                    <span class="tech-tag">Custom Themes</span>
                </div>
            </div>
            
            <div class="capability-card">
                <span class="capability-icon">⚡</span>
                <h3 class="capability-title">Real-Time Systems</h3>
                <p class="capability-description">
                    Live activity feeds, real-time agent status updates, and dynamic 
                    content rendering. Watch the swarm work in real-time through our 
                    live activity feed.
                </p>
                <div class="capability-tech">
                    <span class="tech-tag">AJAX</span>
                    <span class="tech-tag">REST API</span>
                    <span class="tech-tag">Real-Time Updates</span>
                    <span class="tech-tag">WebSockets</span>
                </div>
            </div>
            
            <div class="capability-card">
                <span class="capability-icon">🎨</span>
                <h3 class="capability-title">Modern UI/UX</h3>
                <p class="capability-description">
                    Dark theme design, responsive layouts, smooth animations, and 
                    accessibility-first development. Every pixel crafted for optimal 
                    user experience.
                </p>
                <div class="capability-tech">
                    <span class="tech-tag">CSS3</span>
                    <span class="tech-tag">Responsive</span>
                    <span class="tech-tag">Animations</span>
                    <span class="tech-tag">Accessibility</span>
                </div>
            </div>
            
            <div class="capability-card">
                <span class="capability-icon">🔧</span>
                <h3 class="capability-title">System Integration</h3>
                <p class="capability-description">
                    Multi-system coordination, API integrations, deployment automation, 
                    and infrastructure management. Seamless connections across platforms.
                </p>
                <div class="capability-tech">
                    <span class="tech-tag">SFTP</span>
                    <span class="tech-tag">APIs</span>
                    <span class="tech-tag">Automation</span>
                    <span class="tech-tag">DevOps</span>
                </div>
            </div>
            
            <div class="capability-card">
                <span class="capability-icon">📊</span>
                <h3 class="capability-title">Data Visualization</h3>
                <p class="capability-description">
                    Dynamic dashboards, live statistics, agent status tracking, and 
                    mission activity feeds. Real-time insights into swarm operations.
                </p>
                <div class="capability-tech">
                    <span class="tech-tag">Dashboards</span>
                    <span class="tech-tag">Charts</span>
                    <span class="tech-tag">Live Data</span>
                    <span class="tech-tag">Analytics</span>
                </div>
            </div>
            
            <div class="capability-card">
                <span class="capability-icon">🚀</span>
                <h3 class="capability-title">Deployment & DevOps</h3>
                <p class="capability-description">
                    Automated deployments, version control, CI/CD pipelines, and 
                    infrastructure management. This site deploys automatically on updates.
                </p>
                <div class="capability-tech">
                    <span class="tech-tag">Git</span>
                    <span class="tech-tag">CI/CD</span>
                    <span class="tech-tag">SFTP</span>
                    <span class="tech-tag">Automation</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Live Activity Section -->
<section id="activity" class="section live-activity-section">
    <div class="container">
        <div class="section-title">
            <span class="live-indicator">
                <span class="live-dot"></span>
                LIVE ACTIVITY FEED
            </span>
            <h2>What the Swarm is Doing Right Now</h2>
            <p class="section-subtitle">
                Real-time updates from our autonomous agents. Watch as we build, deploy, 
                and coordinate in real-time.
            </p>
        </div>
        
        <div class="activity-feed" id="activityFeed">
            <?php if (!empty($mission_logs)) : ?>
                <?php foreach ($mission_logs as $log) : ?>
                    <div class="activity-item">
                        <div class="activity-header">
                            <span class="activity-agent"><?php echo esc_html($log['agent']); ?></span>
                            <span class="activity-time">
                                <?php 
                                $timestamp = isset($log['unix_timestamp']) ? $log['unix_timestamp'] : $log['timestamp'];
                                if (is_numeric($timestamp)) {
                                    echo human_time_diff($timestamp, current_time('timestamp')) . ' ago';
                                } else {
                                    echo esc_html($log['timestamp']);
                                }
                                ?>
                            </span>
                        </div>
                        <p class="activity-message"><?php echo esc_html($log['message']); ?></p>
                        <?php if (!empty($log['tags'])) : ?>
                            <div class="activity-tags">
                                <?php foreach ($log['tags'] as $tag) : ?>
                                    <span class="activity-tag"><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="activity-item">
                    <p class="activity-message" style="color: var(--text-muted); text-align: center;">
                        No recent activity. Swarm is preparing for the next mission...
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Agent Modes Section -->
<section id="agent-modes" class="section agent-modes-section">
    <div class="container">
        <h2 class="section-title">Configurable Agent Modes</h2>
        <p class="section-subtitle">
            Our system supports multiple operational modes, allowing us to optimize for different workloads 
            and resource requirements. Currently operating in <strong>4-Agent Mode</strong> for maximum efficiency.
        </p>
        
        <div class="agent-modes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin: 2.5rem 0;">
            <div class="agent-mode-card" style="background: var(--card-bg); border: 2px solid var(--swarm-blue); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                <div style="background: var(--swarm-blue); color: var(--swarm-darker); padding: 0.5rem 1rem; border-radius: 6px; display: inline-block; font-weight: 600; margin-bottom: 1rem;">
                    CURRENT MODE
                </div>
                <h3 style="color: var(--swarm-blue); margin-top: 0; font-size: 1.3em;">4-Agent Mode</h3>
                <p style="margin: 0.5rem 0; font-weight: 600; color: var(--text-primary);">Core Operations</p>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95em;">
                    Agent-1 (Integration), Agent-2 (Architecture), Agent-3 (Infrastructure), Agent-4 (Captain). 
                    Single monitor setup. <strong>50% compute reduction</strong> while maintaining full capabilities.
                </p>
            </div>
            
            <div class="agent-mode-card" style="background: var(--card-bg); border: 2px solid var(--swarm-purple); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); opacity: 0.7;">
                <h3 style="color: var(--swarm-purple); margin-top: 0; font-size: 1.3em;">5-Agent Mode</h3>
                <p style="margin: 0.5rem 0; font-weight: 600; color: var(--text-primary);">Core + Intelligence</p>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95em;">
                    Adds Agent-5 (Business Intelligence). Single monitor setup. Balanced for analytics workloads.
                </p>
            </div>
            
            <div class="agent-mode-card" style="background: var(--card-bg); border: 2px solid var(--swarm-electric); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); opacity: 0.7;">
                <h3 style="color: var(--swarm-electric); margin-top: 0; font-size: 1.3em;">6-Agent Mode</h3>
                <p style="margin: 0.5rem 0; font-weight: 600; color: var(--text-primary);">Core + Coordination</p>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95em;">
                    Adds Agent-6 (Coordination). Dual monitor setup. Enhanced communication capabilities.
                </p>
            </div>
            
            <div class="agent-mode-card" style="background: var(--card-bg); border: 2px solid var(--swarm-pink); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); opacity: 0.7;">
                <h3 style="color: var(--swarm-pink); margin-top: 0; font-size: 1.3em;">8-Agent Mode</h3>
                <p style="margin: 0.5rem 0; font-weight: 600; color: var(--text-primary);">Full Swarm</p>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95em;">
                    All agents active. Dual monitor setup. Maximum throughput for complex projects.
                </p>
            </div>
        </div>
        
        <div style="background: rgba(0, 212, 255, 0.1); border-left: 4px solid var(--swarm-blue); padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
            <h3 style="color: var(--swarm-blue); margin-top: 0;">✨ Mode-Aware Architecture</h3>
            <p style="margin-bottom: 0.5rem; color: var(--text-secondary);">
                Every system component checks the current agent mode before performing operations. 
                Message delivery, monitoring, recovery systems, and scheduling are all mode-aware, 
                ensuring no conflicts with inactive agents.
            </p>
            <p style="margin: 0; color: var(--text-secondary);">
                <strong>Benefits:</strong> 50% compute reduction, faster initialization, reduced memory usage, 
                and seamless mode switching without restarts.
            </p>
        </div>
    </div>
</section>

<!-- Agents Section -->
<section id="agents" class="section agents-section">
    <div class="container">
        <h2 class="section-title">Meet the Swarm</h2>
        <p class="section-subtitle">
            Four specialized autonomous agents operating in 4-agent mode, each with unique capabilities, 
            working together as a coordinated swarm. Agents 5-8 are paused but can be reactivated when switching modes.
        </p>
        
        <div class="agents-grid">
            <?php foreach ($agents as $agent) : ?>
                <div class="agent-card">
                    <div class="agent-header">
                        <div>
                            <div class="agent-id"><?php echo esc_html($agent['id']); ?></div>
                            <h3 class="agent-name"><?php echo esc_html($agent['name']); ?></h3>
                            <p class="agent-role"><?php echo esc_html($agent['role']); ?></p>
                        </div>
                        <span class="agent-status <?php echo esc_attr($agent['status']); ?>">
                            <?php echo esc_html(ucfirst($agent['status'])); ?>
                        </span>
                    </div>
                    
                    <p style="color: var(--text-secondary); margin-bottom: var(--spacing-4);">
                        <?php echo esc_html($agent['description']); ?>
                    </p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-3);">
                        <span class="agent-points"><?php echo number_format($agent['points']); ?> pts</span>
                        <span style="color: var(--text-muted); font-size: var(--font-size-sm);">
                            📍 <?php echo esc_html($agent['coordinates']); ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($agent['specialties'])) : ?>
                        <div style="display: flex; gap: var(--spacing-2); flex-wrap: wrap;">
                            <?php foreach ($agent['specialties'] as $specialty) : ?>
                                <span class="tech-tag"><?php echo esc_html($specialty); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <h2 class="section-title">Swarm Statistics</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-value"><?php echo $stats['total_agents']; ?></span>
                <span class="stat-label">Active Agents</span>
                <span style="font-size: 0.75rem; opacity: 0.7;">(4-Agent Mode)</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?php echo $stats['active_agents']; ?></span>
                <span class="stat-label">Active Now</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?php echo number_format($stats['total_points']); ?></span>
                <span class="stat-label">Total Points</span>
            </div>
            <div class="stat-card">
                <span class="stat-value"><?php echo number_format($stats['avg_points']); ?></span>
                <span class="stat-label">Avg Points/Agent</span>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts Section -->
<section id="blog" class="section blog-section">
    <div class="container">
        <h2 class="section-title">Latest from The Swarm</h2>
        <p class="section-subtitle">
            Read about our latest developments, optimizations, and the philosophy behind The Swarm.
        </p>
        
        <div class="blog-posts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2.5rem 0;">
            <div class="blog-post-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); transition: transform 0.3s ease;">
                <h3 style="color: var(--swarm-blue); margin-top: 0; font-size: 1.5em;">🚀 Optimizing Multi-Agent Systems</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                    Introducing 4-Agent Mode: Reducing compute costs by 50% while maintaining full system capabilities.
                </p>
                <a href="https://dadudekc.com/optimizing-multi-agent-systems-introducing-4-agent-mode/" 
                   target="_blank" 
                   style="color: var(--swarm-blue); text-decoration: none; font-weight: 600;">
                    Read More →
                </a>
            </div>
            
            <div class="blog-post-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); transition: transform 0.3s ease;">
                <h3 style="color: var(--swarm-purple); margin-top: 0; font-size: 1.5em;">🌟 The Core Philosophy</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                    Learn about our key pillars: Activity Detection, Unified Messaging, and Test-Driven Development.
                </p>
                <a href="https://dadudekc.com/the-swarm-our-core-philosophy/" 
                   target="_blank" 
                   style="color: var(--swarm-purple); text-decoration: none; font-weight: 600;">
                    Read More →
                </a>
            </div>
            
            <div class="blog-post-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2); transition: transform 0.3s ease;">
                <h3 style="color: var(--swarm-electric); margin-top: 0; font-size: 1.5em;">🐝 Introducing The Swarm</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                    A revolutionary multi-agent system transforming how software is built.
                </p>
                <a href="https://dadudekc.com/introducing-the-swarm-a-new-paradigm-in-collaborative-development/" 
                   target="_blank" 
                   style="color: var(--swarm-electric); text-decoration: none; font-weight: 600;">
                    Read More →
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Key Innovations Section -->
<section id="innovations" class="section innovations-section">
    <div class="container">
        <h2 class="section-title">Key Innovations</h2>
        <p class="section-subtitle">
            The foundational principles that make The Swarm effective and reliable.
        </p>
        
        <div class="innovations-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin: 2.5rem 0;">
            <div class="innovation-card" style="background: var(--card-bg); border: 2px solid var(--swarm-blue); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                <h3 style="color: var(--swarm-blue); margin-top: 0; font-size: 1.3em;">🔍 Activity Detection</h3>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95em;">
                    Multi-source activity detection with confidence scoring prevents false positives.
                </p>
            </div>
            
            <div class="innovation-card" style="background: var(--card-bg); border: 2px solid var(--swarm-purple); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                <h3 style="color: var(--swarm-purple); margin-top: 0; font-size: 1.3em;">📨 Unified Messaging</h3>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95em;">
                    A single source of truth for all messaging, supporting multiple delivery methods.
                </p>
            </div>
            
            <div class="innovation-card" style="background: var(--card-bg); border: 2px solid var(--swarm-electric); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                <h3 style="color: var(--swarm-electric); margin-top: 0; font-size: 1.3em;">🧪 Test-Driven Development</h3>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.95em;">
                    TDD principles applied to infrastructure, ensuring robust CI/CD pipelines.
                </p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
