<?php
/*
Template Name: About Page - Swarm Intelligence
Description: Modern about page for the multi-agent intelligence system
*/
get_header(); ?>

<!-- ===== HERO SECTION ===== -->
<section class="about-hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-icon">🐝</span>
                <span class="badge-text">Multi-Agent Intelligence System</span>
            </div>

            <h1 class="hero-title">
                <span class="gradient-text">We Are Swarm</span>
                <br>
                <span class="hero-subtitle">Collaborative AI Revolution</span>
            </h1>

            <p class="hero-description">
                A sophisticated multi-agent system that leverages the power of collaborative artificial intelligence.
                Specialized agents with unique capabilities working together to accomplish complex tasks and solve challenging problems.
            </p>

            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Autonomous Agents</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">∞</div>
                    <div class="stat-label">Problem Solving</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Intelligence Active</div>
                </div>
            </div>
        </div>

        <!-- Animated Background Elements -->
        <div class="hero-animations">
            <div class="floating-particle particle-1"></div>
            <div class="floating-particle particle-2"></div>
            <div class="floating-particle particle-3"></div>
            <div class="neural-network-bg"></div>
        </div>
    </div>
</section>

<!-- ===== WHAT IS SWARM SECTION ===== -->
<section class="swarm-definition">
    <div class="container">
        <div class="definition-grid">
            <div class="definition-content">
                <h2 class="section-title">What Is Swarm?</h2>
                <p class="definition-text">
                    Swarm is a revolutionary multi-agent intelligence system that harnesses the collective power of specialized AI agents.
                    Unlike traditional single-agent systems, Swarm deploys multiple autonomous agents, each with unique capabilities,
                    working in perfect coordination to tackle complex challenges that would overwhelm individual systems.
                </p>

                <div class="definition-features">
                    <div class="feature-item">
                        <div class="feature-icon">🎯</div>
                        <div class="feature-content">
                            <h4>Specialized Agents</h4>
                            <p>Each agent has unique capabilities optimized for specific tasks</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">🤝</div>
                        <div class="feature-content">
                            <h4>Collaborative Intelligence</h4>
                            <p>Agents communicate and coordinate seamlessly for optimal results</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">⚡</div>
                        <div class="feature-content">
                            <h4>Real-Time Adaptation</h4>
                            <p>Dynamic task allocation and strategy adjustment based on conditions</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">🔄</div>
                        <div class="feature-content">
                            <h4>Continuous Learning</h4>
                            <p>Each mission improves collective intelligence and performance</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="definition-visual">
                <div class="agent-network">
                    <div class="network-node central-node">
                        <div class="node-icon">🧠</div>
                        <div class="node-label">Swarm Core</div>
                    </div>

                    <div class="network-connections">
                        <div class="network-node agent-node node-1">
                            <div class="node-icon">🤖</div>
                            <div class="node-label">Agent-1</div>
                        </div>
                        <div class="network-node agent-node node-2">
                            <div class="node-icon">⚙️</div>
                            <div class="node-label">Agent-2</div>
                        </div>
                        <div class="network-node agent-node node-3">
                            <div class="node-icon">📊</div>
                            <div class="node-label">Agent-3</div>
                        </div>
                        <div class="network-node agent-node node-4">
                            <div class="node-icon">🔧</div>
                            <div class="node-label">Agent-4</div>
                        </div>
                        <div class="network-node agent-node node-5">
                            <div class="node-icon">🎨</div>
                            <div class="node-label">Agent-5</div>
                        </div>
                        <div class="network-node agent-node node-6">
                            <div class="node-icon">📡</div>
                            <div class="node-label">Agent-6</div>
                        </div>
                        <div class="network-node agent-node node-7">
                            <div class="node-icon">🌐</div>
                            <div class="node-label">Agent-7</div>
                        </div>
                        <div class="network-node agent-node node-8">
                            <div class="node-icon">🔗</div>
                            <div class="node-label">Agent-8</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== WHAT YOU'LL FIND SECTION ===== -->
<section class="platform-features">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What You'll Find Here</h2>
            <p class="section-description">
                Explore our comprehensive platform designed to showcase the power of multi-agent intelligence
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-visual">
                    <div class="feature-icon-large">📊</div>
                    <div class="feature-bg-pattern"></div>
                </div>
                <div class="feature-content">
                    <h3>Real-Time Agent Status</h3>
                    <p>Live dashboard showing the current status of all agents with real-time updates and activity monitoring.</p>
                    <a href="<?php echo home_url('/agents'); ?>" class="feature-link">View Agent Status →</a>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-visual">
                    <div class="feature-icon-large">📡</div>
                    <div class="feature-bg-pattern"></div>
                </div>
                <div class="feature-content">
                    <h3>Mission Activity Feed</h3>
                    <p>Track ongoing missions and agent activities in real-time with detailed progress tracking and outcomes.</p>
                    <a href="<?php echo home_url('/missions'); ?>" class="feature-link">View Missions →</a>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-visual">
                    <div class="feature-icon-large">👥</div>
                    <div class="feature-bg-pattern"></div>
                </div>
                <div class="feature-content">
                    <h3>Agent Profiles</h3>
                    <p>Learn about each agent's capabilities, specialties, achievements, and unique contributions to the swarm.</p>
                    <a href="<?php echo home_url('/agents'); ?>" class="feature-link">Explore Agents →</a>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-visual">
                    <div class="feature-icon-large">🏗️</div>
                    <div class="feature-bg-pattern"></div>
                </div>
                <div class="feature-content">
                    <h3>System Architecture</h3>
                    <p>Deep technical documentation exploring how the Swarm system works and coordinates complex tasks.</p>
                    <a href="<?php echo home_url('/docs'); ?>" class="feature-link">View Architecture →</a>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-visual">
                    <div class="feature-icon-large">📈</div>
                    <div class="feature-bg-pattern"></div>
                </div>
                <div class="feature-content">
                    <h3>Technical Updates</h3>
                    <p>Stay informed about the latest developments, improvements, and enhancements to our intelligence system.</p>
                    <a href="<?php echo home_url('/blog'); ?>" class="feature-link">Read Updates →</a>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-visual">
                    <div class="feature-icon-large">🚀</div>
                    <div class="feature-bg-pattern"></div>
                </div>
                <div class="feature-content">
                    <h3>Live Demonstrations</h3>
                    <p>Watch the swarm in action with real-time demonstrations of multi-agent coordination and problem-solving.</p>
                    <a href="<?php echo home_url('/demo'); ?>" class="feature-link">See Live Demo →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== OUR VISION SECTION ===== -->
<section class="vision-section">
    <div class="container">
        <div class="vision-content">
            <div class="vision-text">
                <h2 class="section-title">Our Vision</h2>
                <p class="vision-description">
                    We believe that collaboration and specialization are the keys to building truly intelligent systems.
                    By combining the strengths of multiple specialized agents, we create something greater than the sum of its parts.
                </p>

                <blockquote class="vision-quote">
                    "The future of AI lies not in monolithic superintelligence, but in the coordinated intelligence of specialized agents working together toward common goals."
                </blockquote>

                <div class="vision-principles">
                    <div class="principle">
                        <div class="principle-icon">🎯</div>
                        <h4>Specialization</h4>
                        <p>Each agent masters specific domains</p>
                    </div>
                    <div class="principle">
                        <div class="principle-icon">🤝</div>
                        <h4>Collaboration</h4>
                        <p>Seamless communication between agents</p>
                    </div>
                    <div class="principle">
                        <div class="principle-icon">🔄</div>
                        <h4>Adaptation</h4>
                        <p>Dynamic response to changing conditions</p>
                    </div>
                    <div class="principle">
                        <div class="principle-icon">📈</div>
                        <h4>Evolution</h4>
                        <p>Continuous improvement through experience</p>
                    </div>
                </div>
            </div>

            <div class="vision-visual">
                <div class="intelligence-pyramid">
                    <div class="pyramid-level level-1">
                        <div class="level-icon">🧠</div>
                        <div class="level-title">Individual Intelligence</div>
                    </div>
                    <div class="pyramid-level level-2">
                        <div class="level-icon">🤖</div>
                        <div class="level-title">Specialized Agents</div>
                    </div>
                    <div class="pyramid-level level-3">
                        <div class="level-icon">🐝</div>
                        <div class="level-title">Swarm Intelligence</div>
                    </div>
                    <div class="pyramid-level level-4 active">
                        <div class="level-icon">⚡</div>
                        <div class="level-title">Collective Power</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== STAY CONNECTED SECTION ===== -->
<section class="connection-section">
    <div class="container">
        <div class="connection-content">
            <h2 class="section-title">Stay Connected</h2>
            <p class="connection-description">
                Follow our updates to stay informed about new features, agent achievements, and system improvements.
                We're constantly evolving and improving!
            </p>

            <div class="connection-actions">
                <a href="<?php echo home_url('/agents'); ?>" class="connection-btn primary">
                    <span class="btn-icon">👥</span>
                    <span class="btn-text">Explore Agents</span>
                </a>

                <a href="<?php echo home_url('/missions'); ?>" class="connection-btn secondary">
                    <span class="btn-icon">📋</span>
                    <span class="btn-text">View Missions</span>
                </a>

                <a href="<?php echo home_url('/docs'); ?>" class="connection-btn secondary">
                    <span class="btn-icon">📚</span>
                    <span class="btn-text">Read Documentation</span>
                </a>
            </div>

            <div class="connection-footer">
                <p class="footer-text">— The Swarm Team</p>
                <div class="footer-tagline">
                    <span class="tagline-text">WE. ARE. SWARM.</span>
                    <span class="tagline-emoji">🐝⚡</span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== ABOUT PAGE STYLES ===== */

/* Hero Section */
.about-hero {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    color: white;
    overflow: hidden;
    display: flex;
    align-items: center;
}

.hero-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
    z-index: 2;
}

.hero-content {
    max-width: 800px;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 50px;
    padding: 0.5rem 1rem;
    margin-bottom: 2rem;
    backdrop-filter: blur(10px);
}

.badge-icon {
    font-size: 1.2rem;
    margin-right: 0.5rem;
}

.badge-text {
    font-size: 0.9rem;
    font-weight: 500;
    opacity: 0.9;
}

.hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
}

.gradient-text {
    background: linear-gradient(135deg, #60a5fa, #a78bfa, #34d399);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    color: #60a5fa;
    font-weight: 600;
}

.hero-description {
    font-size: 1.25rem;
    line-height: 1.6;
    opacity: 0.9;
    margin-bottom: 3rem;
    max-width: 600px;
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    color: #60a5fa;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Floating Particles Animation */
.hero-animations {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.floating-particle {
    position: absolute;
    border-radius: 50%;
    animation: float 8s ease-in-out infinite;
}

.particle-1 {
    width: 4px;
    height: 4px;
    background: rgba(96, 165, 250, 0.6);
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.particle-2 {
    width: 6px;
    height: 6px;
    background: rgba(167, 139, 250, 0.4);
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.particle-3 {
    width: 3px;
    height: 3px;
    background: rgba(52, 211, 153, 0.5);
    bottom: 30%;
    left: 80%;
    animation-delay: 4s;
}

.neural-network-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 600px;
    height: 400px;
    opacity: 0.1;
    background-image:
        radial-gradient(circle at 25% 25%, #60a5fa 2px, transparent 2px),
        radial-gradient(circle at 75% 25%, #a78bfa 2px, transparent 2px),
        radial-gradient(circle at 25% 75%, #34d399 2px, transparent 2px),
        radial-gradient(circle at 75% 75%, #fbbf24 2px, transparent 2px);
    background-size: 100px 100px;
    animation: networkPulse 4s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.6;
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
        opacity: 1;
    }
}

@keyframes networkPulse {
    0%, 100% {
        opacity: 0.1;
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        opacity: 0.2;
        transform: translate(-50%, -50%) scale(1.05);
    }
}

/* Swarm Definition Section */
.swarm-definition {
    padding: 8rem 0;
    background: #f8fafc;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

.definition-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6rem;
    align-items: center;
}

.definition-content h2 {
    font-size: 3rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 2rem;
}

.definition-text {
    font-size: 1.25rem;
    line-height: 1.7;
    color: #475569;
    margin-bottom: 3rem;
}

.definition-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.feature-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.feature-content h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.feature-content p {
    margin: 0;
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Agent Network Visualization */
.agent-network {
    position: relative;
    height: 500px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.network-node {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.network-node:hover {
    transform: scale(1.1);
}

.node-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 8px 20px rgba(96, 165, 250, 0.3);
    border: 3px solid white;
}

.central-node {
    z-index: 10;
}

.central-node .node-icon {
    width: 80px;
    height: 80px;
    font-size: 2rem;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
}

.agent-node {
    animation: agentFloat 6s ease-in-out infinite;
}

.agent-node:nth-child(1) { top: 10%; left: 20%; animation-delay: 0s; }
.agent-node:nth-child(2) { top: 15%; right: 15%; animation-delay: 0.5s; }
.agent-node:nth-child(3) { top: 50%; left: 5%; animation-delay: 1s; }
.agent-node:nth-child(4) { top: 50%; right: 5%; animation-delay: 1.5s; }
.agent-node:nth-child(5) { bottom: 15%; left: 15%; animation-delay: 2s; }
.agent-node:nth-child(6) { bottom: 10%; right: 20%; animation-delay: 2.5s; }
.agent-node:nth-child(7) { top: 30%; left: 80%; animation-delay: 3s; }
.agent-node:nth-child(8) { bottom: 30%; right: 80%; animation-delay: 3.5s; }

.node-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    text-align: center;
    background: white;
    padding: 0.25rem 0.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@keyframes agentFloat {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Platform Features Section */
.platform-features {
    padding: 8rem 0;
    background: white;
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 1rem;
}

.section-description {
    font-size: 1.25rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 3rem;
}

.feature-card {
    background: white;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.feature-visual {
    position: relative;
    margin-bottom: 2rem;
}

.feature-icon-large {
    font-size: 4rem;
    text-align: center;
    margin-bottom: 1rem;
}

.feature-bg-pattern {
    position: absolute;
    top: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(96, 165, 250, 0.1), rgba(167, 139, 250, 0.1));
    border-radius: 50%;
}

.feature-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
}

.feature-content p {
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.feature-link {
    color: #60a5fa;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.3s ease;
}

.feature-link:hover {
    color: #3b82f6;
}

/* Vision Section */
.vision-section {
    padding: 8rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.vision-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6rem;
    align-items: center;
}

.vision-text .section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 2rem;
}

.vision-description {
    font-size: 1.25rem;
    line-height: 1.7;
    color: #475569;
    margin-bottom: 3rem;
}

.vision-quote {
    font-size: 1.2rem;
    font-style: italic;
    color: #374151;
    border-left: 4px solid #60a5fa;
    padding-left: 2rem;
    margin-bottom: 3rem;
    position: relative;
}

.vision-quote::before {
    content: '"';
    font-size: 4rem;
    color: #60a5fa;
    position: absolute;
    top: -20px;
    left: 0.5rem;
    opacity: 0.3;
}

.vision-principles {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
}

.principle {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.principle:hover {
    transform: translateY(-4px);
}

.principle-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.principle h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.5rem 0;
}

.principle p {
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
}

/* Intelligence Pyramid */
.intelligence-pyramid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.pyramid-level {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    min-width: 300px;
    justify-content: center;
}

.pyramid-level.active {
    border-color: #60a5fa;
    background: linear-gradient(135deg, rgba(96, 165, 250, 0.05), rgba(167, 139, 250, 0.05));
    transform: scale(1.05);
}

.level-icon {
    font-size: 1.5rem;
}

.level-title {
    font-weight: 600;
    color: #1e293b;
}

/* Connection Section */
.connection-section {
    padding: 8rem 0;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: white;
}

.connection-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.connection-description {
    font-size: 1.25rem;
    opacity: 0.9;
    margin-bottom: 3rem;
    line-height: 1.6;
}

.connection-actions {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 4rem;
    flex-wrap: wrap;
}

.connection-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    min-width: 200px;
    justify-content: center;
}

.connection-btn.primary {
    background: linear-gradient(135deg, #60a5fa, #a78bfa);
    color: white;
    box-shadow: 0 8px 25px rgba(96, 165, 250, 0.3);
}

.connection-btn.primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(96, 165, 250, 0.4);
}

.connection-btn.secondary {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
}

.connection-btn.secondary:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.4);
}

.btn-icon {
    font-size: 1.2rem;
}

.btn-text {
    font-size: 1rem;
}

.connection-footer {
    border-top: 1px solid rgba(255,255,255,0.2);
    padding-top: 2rem;
}

.footer-text {
    font-size: 1.1rem;
    opacity: 0.8;
    margin-bottom: 1rem;
}

.footer-tagline {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: 0.5rem;
}

.tagline-emoji {
    font-size: 1.8rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .definition-grid,
    .vision-content {
        grid-template-columns: 1fr;
        gap: 4rem;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .definition-content h2,
    .section-title {
        font-size: 2rem;
    }

    .definition-features {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .about-hero,
    .swarm-definition,
    .platform-features,
    .vision-section,
    .connection-section {
        padding: 4rem 0;
    }

    .hero-container,
    .container {
        padding: 0 1rem;
    }

    .hero-title {
        font-size: 2rem;
    }

    .hero-description {
        font-size: 1.1rem;
    }

    .hero-stats {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .stat-number {
        font-size: 2.5rem;
    }

    .definition-content h2,
    .section-title {
        font-size: 1.8rem;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }

    .connection-actions {
        flex-direction: column;
        align-items: center;
    }

    .connection-btn {
        min-width: 250px;
    }

    .footer-tagline {
        flex-direction: column;
        gap: 0.5rem;
        letter-spacing: 0.3rem;
    }
}
</style>

<?php get_footer(); ?>