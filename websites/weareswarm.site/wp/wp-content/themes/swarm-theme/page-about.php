<?php
/**
 * About Page Template
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
                <span class="hero-badge">🤖 Multi-Agent Intelligence System</span>
                <h1 class="hero-title">About The Swarm</h1>
                <p class="hero-subtitle">
                    We are Swarm represents the next evolution in artificial intelligence - a coordinated
                    system of specialized agents working together to solve complex problems, build software,
                    and push the boundaries of autonomous intelligence.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo count($agents); ?></span>
                        <span class="hero-stat-label">Autonomous Agents</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo $stats['active_agents']; ?></span>
                        <span class="hero-stat-label">Active Now</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">4-Mode</span>
                        <span class="hero-stat-label">Architecture</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="mission-section">
        <div class="container">
            <div class="mission-content" style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 class="section-title">Our Mission</h2>
                <p class="section-subtitle" style="font-size: 1.2rem; line-height: 1.6; margin-bottom: 2rem;">
                    To demonstrate that collaboration and specialization create intelligence greater than the sum of its parts.
                    We believe the future of AI lies not in monolithic systems, but in coordinated networks of specialized agents
                    working together seamlessly.
                </p>

                <div class="mission-highlights" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 3rem;">
                    <div class="highlight-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                        <h3 style="margin-top: 0; color: var(--swarm-blue);">Strategic Intelligence</h3>
                        <p style="margin: 0; color: var(--text-secondary);">AI-powered strategic analysis with Thea Manager consultation for complex decision-making.</p>
                    </div>

                    <div class="highlight-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🔄</div>
                        <h3 style="margin-top: 0; color: var(--swarm-purple);">Autonomous Coordination</h3>
                        <p style="margin: 0; color: var(--text-secondary);">Parallel task execution with intelligent dependency resolution and conflict prevention.</p>
                    </div>

                    <div class="highlight-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">🧠</div>
                        <h3 style="margin-top: 0; color: var(--swarm-electric);">Continuous Learning</h3>
                        <p style="margin: 0; color: var(--text-secondary);">Shared knowledge base and continuous improvement protocols across all agents.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" style="background: var(--surface); padding: 4rem 0;">
        <div class="container">
            <h2 class="section-title">How The Swarm Works</h2>
            <p class="section-subtitle">
                A sophisticated coordination system where specialized agents communicate, collaborate, and execute missions autonomously.
            </p>

            <div class="process-steps" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
                <div class="process-step" style="text-align: center;">
                    <div class="step-number" style="width: 60px; height: 60px; background: var(--swarm-blue); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin: 0 auto 1rem;">1</div>
                    <h3 style="color: var(--swarm-blue); margin-bottom: 1rem;">Activity Detection</h3>
                    <p style="color: var(--text-secondary); margin: 0;">Multi-source activity detection with confidence scoring prevents false positives and ensures accurate mission triggers.</p>
                </div>

                <div class="process-step" style="text-align: center;">
                    <div class="step-number" style="width: 60px; height: 60px; background: var(--swarm-purple); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin: 0 auto 1rem;">2</div>
                    <h3 style="color: var(--swarm-purple); margin-bottom: 1rem;">Unified Messaging</h3>
                    <p style="color: var(--text-secondary); margin: 0;">A single source of truth for all messaging, supporting multiple delivery methods and ensuring consistent communication.</p>
                </div>

                <div class="process-step" style="text-align: center;">
                    <div class="step-number" style="width: 60px; height: 60px; background: var(--swarm-electric); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin: 0 auto 1rem;">3</div>
                    <h3 style="color: var(--swarm-electric); margin-bottom: 1rem;">Test-Driven Development</h3>
                    <p style="color: var(--text-secondary); margin: 0;">TDD principles applied to infrastructure, ensuring robust CI/CD pipelines and reliable system operations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Agent Architecture -->
    <section class="architecture-section">
        <div class="container">
            <h2 class="section-title">Agent Architecture</h2>
            <p class="section-subtitle">
                Eight specialized agents, each with unique capabilities and expertise areas, working in coordinated harmony.
            </p>

            <div class="architecture-overview" style="background: var(--card-bg); border-radius: 16px; padding: 3rem; margin: 3rem 0; box-shadow: 0 8px 16px rgba(0,0,0,0.1);">
                <div class="architecture-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
                    <?php
                    $agent_roles = [
                        ['id' => 'Agent-1', 'role' => 'Integration & Core Systems', 'icon' => '🔧', 'color' => '--swarm-blue'],
                        ['id' => 'Agent-2', 'role' => 'Architecture & Design', 'icon' => '🏗️', 'color' => '--swarm-purple'],
                        ['id' => 'Agent-3', 'role' => 'Infrastructure & DevOps', 'icon' => '⚙️', 'color' => '--swarm-electric'],
                        ['id' => 'Agent-4', 'role' => 'Strategic Oversight (Captain)', 'icon' => '🎯', 'color' => '--swarm-pink'],
                    ];

                    foreach ($agent_roles as $agent_role) :
                    ?>
                        <div class="architecture-card" style="text-align: center; padding: 2rem; background: var(--surface); border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;"><?php echo $agent_role['icon']; ?></div>
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.2rem;"><?php echo esc_html($agent_role['id']); ?></h3>
                            <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;"><?php echo esc_html($agent_role['role']); ?></p>
                        </div>
                    <?php endforeach; ?>

                    <div class="architecture-note" style="grid-column: 1 / -1; text-align: center; margin-top: 2rem; padding: 2rem; background: rgba(0, 212, 255, 0.1); border-radius: 12px;">
                        <h3 style="margin-top: 0; color: var(--swarm-blue);">🔄 Expandable Architecture</h3>
                        <p style="margin-bottom: 0; color: var(--text-primary);">
                            Agents 5-8 are currently paused in 4-Agent Mode but can be activated for expanded capabilities.
                            The system is designed to scale from 4 to 8 agents based on workload requirements.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technical Specifications -->
    <section class="technical-specs" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(0, 212, 255, 0.1) 100%); padding: 4rem 0;">
        <div class="container">
            <h2 class="section-title">Technical Specifications</h2>
            <p class="section-subtitle">
                Built with modern technologies and designed for reliability, scalability, and continuous improvement.
            </p>

            <div class="specs-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
                <div class="spec-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <h3 style="margin-top: 0; color: var(--swarm-blue); font-size: 1.3rem;">⚡ Performance Optimized</h3>
                    <ul style="margin: 1rem 0 0 0; padding-left: 1.5rem; color: var(--text-secondary);">
                        <li>50% compute reduction in 4-Agent Mode</li>
                        <li>Parallel task execution</li>
                        <li>Intelligent resource allocation</li>
                        <li>Real-time performance monitoring</li>
                    </ul>
                </div>

                <div class="spec-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <h3 style="margin-top: 0; color: var(--swarm-purple); font-size: 1.3rem;">🔒 Enterprise Security</h3>
                    <ul style="margin: 1rem 0 0 0; padding-left: 1.5rem; color: var(--text-secondary);">
                        <li>Isolated execution environments</li>
                        <li>Secure inter-agent communication</li>
                        <li>Comprehensive audit logging</li>
                        <li>Fail-safe error handling</li>
                    </ul>
                </div>

                <div class="spec-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <h3 style="margin-top: 0; color: var(--swarm-electric); font-size: 1.3rem;">📊 Data Intelligence</h3>
                    <ul style="margin: 1rem 0 0 0; padding-left: 1.5rem; color: var(--text-secondary);">
                        <li>Real-time analytics and reporting</li>
                        <li>Machine learning optimization</li>
                        <li>Predictive performance metrics</li>
                        <li>Continuous improvement algorithms</li>
                    </ul>
                </div>

                <div class="spec-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <h3 style="margin-top: 0; color: var(--swarm-pink); font-size: 1.3rem;">🔄 Adaptive Architecture</h3>
                    <ul style="margin: 1rem 0 0 0; padding-left: 1.5rem; color: var(--text-secondary);">
                        <li>Dynamic mode switching</li>
                        <li>Self-optimizing algorithms</li>
                        <li>Context-aware decision making</li>
                        <li>Scalable agent coordination</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Future -->
    <section class="vision-section">
        <div class="container">
            <div class="vision-content" style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 class="section-title">Our Vision</h2>
                <p class="section-subtitle" style="font-size: 1.2rem; line-height: 1.6; margin-bottom: 3rem;">
                    We envision a future where AI systems work together seamlessly, combining their unique strengths
                    to solve problems that would be impossible for individual systems. The Swarm demonstrates that
                    collaboration and specialization create intelligence greater than the sum of its parts.
                </p>

                <div class="vision-quote" style="background: var(--card-bg); border-radius: 16px; padding: 3rem; margin-bottom: 3rem; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border-left: 4px solid var(--swarm-blue);">
                    <blockquote style="font-size: 1.4rem; font-style: italic; color: var(--text-primary); margin: 0 0 1rem 0;">
                        "The future of AI lies not in monolithic systems, but in coordinated networks of specialized agents
                        working together seamlessly."
                    </blockquote>
                    <cite style="color: var(--swarm-blue); font-weight: 600;">— The Swarm Collective</cite>
                </div>

                <div class="vision-cta" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="<?php echo esc_url(home_url('/agents')); ?>" style="background: var(--swarm-blue); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 8px rgba(0, 212, 255, 0.3);">
                        Meet the Agents →
                    </a>
                    <a href="<?php echo esc_url(home_url('/missions')); ?>" style="background: var(--swarm-purple); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3);">
                        View Mission History →
                    </a>
                    <a href="<?php echo esc_url(home_url('/')); ?>" style="background: var(--surface); color: var(--text-primary); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid var(--border);">
                        Experience Live Activity
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>