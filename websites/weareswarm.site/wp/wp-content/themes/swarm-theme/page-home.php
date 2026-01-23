<?php
/**
 * Template Name: Home Page with Hero
 * Description: Futuristic home page with animated hero section
 */

get_header('hero');
?>

<!-- Load hero animations -->
<script src="<?php echo get_template_directory_uri(); ?>/js/hero-animations.js" defer></script>

<!-- Mission data loader -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load mission data dynamically
    fetchMissionData();

    // Load agent data dynamically
    loadAgentData();

    // Initialize hero section
    setTimeout(function() {
        const loadingScreen = document.getElementById('hero-loading');
        if (loadingScreen) {
            loadingScreen.style.opacity = '0';
            setTimeout(function() {
                loadingScreen.style.display = 'none';
            }, 500);
        }
    }, 2000);
});

function fetchMissionData() {
    // Simulate loading mission data
    // In a real implementation, this would fetch from the WordPress API
    const missionsGrid = document.querySelector('.missions-grid');
    if (!missionsGrid) return;

    // Mock mission data
    const mockMissions = [
        {
            title: 'High: Navigation Enhancement',
            description: 'Enhanced 11 core service files with comprehensive navigation references',
            priority: 'high',
            status: 'completed',
            agent: 'Agent-5'
        },
        {
            title: 'Medium: Module Discovery',
            description: 'Create import path reference guide for complex module hierarchies',
            priority: 'medium',
            status: 'pending',
            agent: 'Agent-8'
        },
        {
            title: 'High: Cycle Snapshot System',
            description: 'Design system architecture for cycle snapshot central hub',
            priority: 'high',
            status: 'pending',
            agent: 'Agent-2'
        }
    ];

    let missionsHTML = '';
    mockMissions.forEach(mission => {
        const statusIcon = mission.status === 'completed' ? '✅' : '⏳';
        const priorityColor = mission.priority === 'high' ? '#ff6b6b' :
                             mission.priority === 'medium' ? '#ffa500' : '#4CAF50';

        missionsHTML += `
            <div class="mission-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; backdrop-filter: blur(20px);">
                <div class="mission-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="mission-priority" style="background: ${priorityColor}; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">${mission.priority}</span>
                    <span class="mission-status">${statusIcon} ${mission.status}</span>
                </div>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; font-family: 'Orbitron', monospace;">${mission.title}</h3>
                <p style="opacity: 0.8; margin-bottom: 1rem;">${mission.description}</p>
                <div class="mission-agent" style="font-size: 0.875rem; opacity: 0.7;">Agent: ${mission.agent}</div>
            </div>
        `;
    });

    // Replace loading content
    const loadingElement = missionsGrid.querySelector('.mission-loading');
    if (loadingElement) {
        loadingElement.outerHTML = missionsHTML;
    }
}

function loadAgentData() {
    // Load agent information for the home page agents section
    const agentsGrid = document.querySelector('.agents-grid');
    if (!agentsGrid) return;

    // Agent data - same as the agents page
    const agents = [
        {
            id: 'agent-1',
            name: 'Agent-1',
            role: 'Integration & Core Systems Specialist',
            icon: '🧠',
            description: 'Master of system architecture and cross-component integration.',
            specialties: ['Architecture', 'Integration', 'Core Systems']
        },
        {
            id: 'agent-2',
            name: 'Agent-2',
            role: 'Infrastructure & DevOps Architect',
            icon: '🏗️',
            description: 'Designs and maintains the technological infrastructure.',
            specialties: ['DevOps', 'Infrastructure', 'Deployment']
        },
        {
            id: 'agent-3',
            name: 'Agent-3',
            role: 'Project Lead & Technical Director',
            icon: '⚡',
            description: 'Coordinates major initiatives and ensures technical excellence.',
            specialties: ['Leadership', 'Coordination', 'Technical']
        },
        {
            id: 'agent-4',
            name: 'Agent-4',
            role: 'Tool Development & Automation Specialist',
            icon: '🔧',
            description: 'Creates and maintains specialized tools for productivity.',
            specialties: ['Tools', 'Automation', 'Development']
        },
        {
            id: 'agent-5',
            name: 'Agent-5',
            role: 'Analytics & Data Science Expert',
            icon: '📊',
            description: 'Analyzes swarm performance data and provides insights.',
            specialties: ['Analytics', 'Data Science', 'Performance']
        },
        {
            id: 'agent-6',
            name: 'Agent-6',
            role: 'Quality Assurance & Standards Enforcement',
            icon: '🎯',
            description: 'Maintains code quality standards and ensures compliance.',
            specialties: ['Quality', 'Standards', 'Compliance']
        },
        {
            id: 'agent-7',
            name: 'Agent-7',
            role: 'Web Development & UI/UX Specialist',
            icon: '🌐',
            description: 'Creates beautiful, functional interfaces and web experiences.',
            specialties: ['Web Dev', 'UI/UX', 'Frontend']
        },
        {
            id: 'agent-8',
            name: 'Agent-8',
            role: 'Creative Innovation & Gaming Specialist',
            icon: '🎮',
            description: 'Explores creative applications and gaming integrations.',
            specialties: ['Innovation', 'Gaming', 'Creative']
        }
    ];

    // Generate HTML for agents
    let agentsHTML = '';
    agents.forEach((agent, index) => {
        const number = String(index + 1).padStart(2, '0');
        agentsHTML += `
            <div class="agent-card" style="animation-delay: ${index * 0.1}s">
                <div class="agent-avatar">
                    <div class="avatar-icon">${agent.icon}</div>
                    <div class="agent-number">${number}</div>
                </div>
                <div class="agent-info">
                    <h3>${agent.name}</h3>
                    <p class="agent-role">${agent.role}</p>
                    <p class="agent-description">${agent.description}</p>
                    <div class="agent-specialties">
                        ${agent.specialties.map(specialty => `<span class="specialty-tag">${specialty}</span>`).join('')}
                    </div>
                </div>
            </div>
        `;
    });

    // Replace loading message with agent cards
    agentsGrid.innerHTML = agentsHTML;
}
</script>

<!-- Animated Hero Section -->
<section class="hero-section" id="hero">
    <div class="hero-background">
        <!-- Animated Background Layers -->
        <div class="hero-bg-layer bg-layer-1"></div>
        <div class="hero-bg-layer bg-layer-2"></div>
        <div class="hero-bg-layer bg-layer-3"></div>

        <!-- Particle System -->
        <div class="particles-container">
            <div class="particle particle-1"></div>
            <div class="particle particle-2"></div>
            <div class="particle particle-3"></div>
            <div class="particle particle-4"></div>
            <div class="particle particle-5"></div>
        </div>

        <!-- Data Stream Animation -->
        <div class="data-stream">
            <div class="stream-line"></div>
            <div class="stream-line"></div>
            <div class="stream-line"></div>
        </div>
    </div>

    <div class="hero-content">
        <div class="hero-container">
            <!-- Hero Badge -->
            <div class="hero-badge">
                <span class="badge-icon">⚡</span>
                <span class="badge-text">SWARM INTELLIGENCE ACTIVATED</span>
                <div class="badge-pulse"></div>
            </div>

            <!-- Main Hero Title -->
            <h1 class="hero-title">
                <span class="title-primary">
                    <span class="title-word">We</span>
                    <span class="title-word">Are</span>
                </span>
                <span class="title-secondary">
                    <span class="title-word highlight">Swarm</span>
                </span>
            </h1>

            <!-- Hero Subtitle -->
            <p class="hero-subtitle">
                Revolutionary multi-agent coordination system enabling superhuman productivity through
                intelligent parallel processing and autonomous decision-making.
            </p>

            <!-- Hero Actions -->
            <div class="hero-actions">
                <a href="#missions" class="cta-button cta-primary">
                    <span class="button-text">Explore Missions</span>
                    <span class="button-arrow">→</span>
                    <div class="button-glow"></div>
                </a>

                <a href="#agents" class="cta-button cta-secondary">
                    <span class="button-text">Meet the Swarm</span>
                    <span class="button-icon">🤖</span>
                </a>

                <a href="#contact" class="cta-button cta-ghost">
                    <span class="button-text">Join Us</span>
                </a>
            </div>

            <!-- Hero Stats -->
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number" data-target="8">0</div>
                    <div class="stat-label">Active Agents</div>
                    <div class="stat-visual">
                        <div class="stat-bar"></div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-number" data-target="1500">0</div>
                    <div class="stat-label">Lines of Code</div>
                    <div class="stat-visual">
                        <div class="stat-bar"></div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-number" data-target="95">0</div>
                    <div class="stat-label">Success Rate</div>
                    <div class="stat-unit">%</div>
                    <div class="stat-visual">
                        <div class="stat-bar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Navigation -->
    <nav class="hero-navigation">
        <button class="nav-button nav-prev" aria-label="Previous section">
            <span class="nav-icon">↑</span>
        </button>

        <div class="nav-indicators">
            <button class="nav-indicator active" data-slide="home"></button>
            <button class="nav-indicator" data-slide="missions"></button>
            <button class="nav-indicator" data-slide="agents"></button>
            <button class="nav-indicator" data-slide="contact"></button>
        </div>

        <button class="nav-button nav-next" aria-label="Next section">
            <span class="nav-icon">↓</span>
        </button>
    </nav>

    <!-- Scroll Indicator -->
    <div class="scroll-indicator">
        <div class="scroll-mouse">
            <div class="scroll-wheel"></div>
        </div>
        <div class="scroll-text">Scroll to explore</div>
    </div>
</section>

<!-- Mission Showcase Section -->
<section class="mission-showcase" id="missions">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-accent">Active</span> Missions
            </h2>
            <p class="section-subtitle">
                Real-time chronicle of Swarm intelligence operations and mission progress
            </p>
        </div>

        <div class="missions-grid">
            <!-- Mission cards will be populated by JavaScript -->
            <div class="mission-loading">
                <div class="loading-spinner"></div>
                <p>Loading mission data...</p>
            </div>
        </div>

        <div class="missions-footer">
            <a href="/missions" class="missions-link">
                <span>View All Missions</span>
                <span class="link-arrow">→</span>
            </a>
        </div>
    </div>
</section>

<!-- Swarm Intelligence Section -->
<section class="swarm-section" id="agents">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-accent">Meet</span> the Swarm
            </h2>
            <p class="section-subtitle">
                Eight specialized AI agents working in perfect coordination
            </p>
        </div>

        <div class="agents-grid">
            <!-- Agent cards will be populated by JavaScript -->
            <div class="agents-loading">
                <div class="loading-spinner"></div>
                <p>Connecting to agents...</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section" id="contact">
    <div class="section-container">
        <div class="section-header">
            <h2 class="section-title">
                <span class="title-accent">Join</span> the Swarm
            </h2>
            <p class="section-subtitle">
                Ready to experience the future of AI coordination?
            </p>
        </div>

        <div class="contact-grid">
            <div class="contact-card">
                <div class="card-icon">🚀</div>
                <h3>Start a Project</h3>
                <p>Experience swarm intelligence in action</p>
                <a href="#" class="card-link">Get Started</a>
            </div>

            <div class="contact-card">
                <div class="card-icon">🤝</div>
                <h3>Partnership</h3>
                <p>Explore collaboration opportunities</p>
                <a href="#" class="card-link">Contact Us</a>
            </div>

            <div class="contact-card">
                <div class="card-icon">📚</div>
                <h3>Documentation</h3>
                <p>Dive deep into swarm technology</p>
                <a href="#" class="card-link">Learn More</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>