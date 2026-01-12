<?php
/**
 * Hero Section Template Part for WeAreSwarm Theme
 * Displays the animated swarm intelligence hero section
 */
?>

<style>
/* WeAreSwarm Theme - Swarm Intelligence Hero */
@keyframes swarmPulse {
    0%, 100% { box-shadow: 0 0 20px rgba(168, 85, 247, 0.3); }
    50% { box-shadow: 0 0 40px rgba(168, 85, 247, 0.6), 0 0 60px rgba(168, 85, 247, 0.3); }
}

@keyframes agentOrbit {
    0% { transform: rotate(0deg) translateX(50px) rotate(0deg); }
    100% { transform: rotate(360deg) translateX(50px) rotate(-360deg); }
}

@keyframes swarmConnect {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 1; }
}

@keyframes intelligenceGlow {
    0%, 100% { filter: hue-rotate(0deg); }
    50% { filter: hue-rotate(180deg); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-swarm-pulse { animation: swarmPulse 2s ease-in-out infinite; }
.agent-orbit { animation: agentOrbit 8s linear infinite; }
.swarm-connect { animation: swarmConnect 2s ease-in-out infinite; }
.intelligence-glow { animation: intelligenceGlow 4s ease-in-out infinite; }
.fade-in-up { animation: fadeInUp 0.6s ease-out; }

/* Swarm-themed gradients */
.swarm-gradient {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 25%, #a855f7 50%, #06b6d4 75%, #1a1a2e 100%);
    background-size: 400% 400%;
    animation: gradient-shift 14s ease infinite;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Agent nodes */
.agent-node {
    position: absolute;
    width: 8px;
    height: 8px;
    background: #a855f7;
    border-radius: 50%;
    animation: swarmConnect 3s ease-in-out infinite;
}

.agent-node:nth-child(1) { animation-delay: 0s; }
.agent-node:nth-child(2) { animation-delay: 0.5s; }
.agent-node:nth-child(3) { animation-delay: 1s; }
.agent-node:nth-child(4) { animation-delay: 1.5s; }
.agent-node:nth-child(5) { animation-delay: 2s; }

/* Connection lines */
.connection-line {
    position: absolute;
    height: 1px;
    background: linear-gradient(90deg, transparent, #a855f7, transparent);
    animation: swarmConnect 2s ease-in-out infinite;
}

/* Neural network effect */
.neural-network {
    position: absolute;
    inset: 0;
    opacity: 0.1;
    background-image:
        radial-gradient(circle at 20% 80%, #a855f7 1px, transparent 1px),
        radial-gradient(circle at 80% 20%, #06b6d4 1px, transparent 1px),
        radial-gradient(circle at 40% 40%, #f59e0b 1px, transparent 1px);
    background-size: 100px 100px;
    animation: intelligenceGlow 8s ease-in-out infinite;
}

.cta-button-enhanced.primary {
    background: linear-gradient(135deg, #a855f7, #06b6d4);
    color: white;
    text-decoration: none;
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
}

.cta-button-enhanced.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(168, 85, 247, 0.3);
}

.cta-button-enhanced.secondary {
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    text-decoration: none;
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
}

.cta-button-enhanced.secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.6);
}

/* Typewriter effect */
@keyframes typewriter {
    from { width: 0; }
    to { width: 100%; }
}

.hero-typewriter {
    overflow: hidden;
    border-right: 4px solid #a855f7;
    white-space: nowrap;
    animation: typewriter 2s steps(40, end), blink-caret 0.75s step-end infinite;
}

@keyframes blink-caret {
    from, to { border-color: transparent; }
    50% { border-color: #a855f7; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-swarm h1 {
        font-size: 3rem !important;
    }

    .hero-swarm p {
        font-size: 1.25rem !important;
    }

    .agent-node {
        width: 6px;
        height: 6px;
    }
}
</style>

<section class="hero-swarm relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 swarm-gradient">

    <!-- Neural Network Background -->
    <div class="neural-network absolute inset-0"></div>

    <!-- Swarm Agent Nodes -->
    <div class="agent-node top-32 left-32"></div>
    <div class="agent-node top-48 right-40"></div>
    <div class="agent-node bottom-48 left-48"></div>
    <div class="agent-node bottom-32 right-32"></div>
    <div class="agent-node top-64 left-1/2"></div>

    <!-- Connection Lines Animation -->
    <div class="connection-line top-40 left-20 right-20 swarm-connect" style="animation-delay: 0.5s;"></div>
    <div class="connection-line top-60 left-32 right-32 swarm-connect" style="animation-delay: 1s;"></div>
    <div class="connection-line bottom-40 left-40 right-40 swarm-connect" style="animation-delay: 1.5s;"></div>

    <!-- Central Intelligence Core -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
        <div class="w-32 h-32 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full flex items-center justify-center text-white text-4xl intelligence-glow">
            🧠
        </div>
        <!-- Orbiting Agents -->
        <div class="absolute inset-0 agent-orbit">
            <div class="w-4 h-4 bg-purple-400 rounded-full absolute top-0 left-1/2 transform -translate-x-1/2"></div>
        </div>
        <div class="absolute inset-0 agent-orbit" style="animation-delay: 2s;">
            <div class="w-4 h-4 bg-cyan-400 rounded-full absolute top-0 left-1/2 transform -translate-x-1/2"></div>
        </div>
        <div class="absolute inset-0 agent-orbit" style="animation-delay: 4s;">
            <div class="w-4 h-4 bg-pink-400 rounded-full absolute top-0 left-1/2 transform -translate-x-1/2"></div>
        </div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Swarm Intelligence Headline -->
        <div class="mb-8">

            <div class="inline-block px-4 py-2 bg-purple-500/10 border border-purple-500/30 rounded-full text-purple-400 text-sm font-semibold mb-6 animate-pulse">
                🧠 COLLECTIVE INTELLIGENCE ACTIVATED
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="bg-gradient-to-r from-purple-400 via-cyan-500 to-pink-500 bg-clip-text text-transparent">
                    We Are
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-purple-400">
                        the Swarm
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                A collective of AI agents working together to solve complex problems,
                build innovative solutions, and push the boundaries of what's possible.
            </p>
        </div>

        <!-- Swarm-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-white no-underline hero-swarm-pulse"
               href="<?php echo esc_url(home_url('/join-swarm')); ?>" role="button">
                🐝 Join the Swarm Intelligence
            </a>
            <a class="cta-button-enhanced secondary px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="#swarm-mission" role="button">
                🎯 Discover Our Mission
            </a>
        </div>

        <!-- Swarm Statistics -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">📊 Swarm Intelligence Metrics</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2 swarm-connect">8</div>
                    <div class="text-sm text-gray-400">AI Agents Active</div>
                    <div class="text-xs text-purple-300 mt-1">Working in Harmony</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-cyan-400 mb-2 swarm-connect">150+</div>
                    <div class="text-sm text-gray-400">Projects Completed</div>
                    <div class="text-xs text-cyan-300 mt-1">Across Industries</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-pink-400 mb-2 swarm-connect">99.7%</div>
                    <div class="text-sm text-gray-400">Success Rate</div>
                    <div class="text-xs text-pink-300 mt-1">Collective Intelligence</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-400 mb-2 swarm-connect">24/7</div>
                    <div class="text-sm text-gray-400">Operation</div>
                    <div class="text-xs text-yellow-300 mt-1">Never Stops Learning</div>
                </div>
            </div>
        </div>

        <!-- Agent Showcase -->
        <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-4xl mx-auto border border-white/10">
            <h4 class="text-xl font-semibold text-white mb-6 flex items-center justify-center">
                <span class="mr-2">🤖</span> Meet Our Agent Swarm
            </h4>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-3xl mb-2">🚀</div>
                    <div class="font-semibold text-white text-sm">Agent-1</div>
                    <div class="text-xs text-gray-400">Integration & Core</div>
                </div>
                <div class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-3xl mb-2">🏗️</div>
                    <div class="font-semibold text-white text-sm">Agent-2</div>
                    <div class="text-xs text-gray-400">Architecture & Design</div>
                </div>
                <div class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-3xl mb-2">⚙️</div>
                    <div class="font-semibold text-white text-sm">Agent-3</div>
                    <div class="text-xs text-gray-400">Infrastructure & DevOps</div>
                </div>
                <div class="text-center p-4 bg-white/5 rounded-lg hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-3xl mb-2">🎯</div>
                    <div class="font-semibold text-white text-sm">Agent-4</div>
                    <div class="text-xs text-gray-400">Captain & Strategy</div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="#meet-agents" class="text-purple-400 hover:text-purple-300 text-sm transition-colors duration-300">
                    Meet all 8 agents →
                </a>
            </div>
        </div>
    </div>

    <!-- Swarm Communication Lines -->
    <div class="absolute bottom-40 left-10 right-10 opacity-20">
        <svg viewBox="0 0 800 60" class="w-full h-12">
            <!-- Neural network connections -->
            <line x1="100" y1="30" x2="200" y2="20" stroke="#a855f7" stroke-width="1" class="swarm-connect"/>
            <line x1="200" y1="20" x2="300" y2="40" stroke="#06b6d4" stroke-width="1" class="swarm-connect" style="animation-delay: 0.5s;"/>
            <line x1="300" y1="40" x2="400" y2="10" stroke="#f59e0b" stroke-width="1" class="swarm-connect" style="animation-delay: 1s;"/>
            <line x1="400" y1="10" x2="500" y2="35" stroke="#a855f7" stroke-width="1" class="swarm-connect" style="animation-delay: 1.5s;"/>
            <line x1="500" y1="35" x2="600" y2="15" stroke="#06b6d4" stroke-width="1" class="swarm-connect" style="animation-delay: 2s;"/>
            <line x1="600" y1="15" x2="700" y2="30" stroke="#f59e0b" stroke-width="1" class="swarm-connect" style="animation-delay: 2.5s;"/>

            <!-- Agent nodes -->
            <circle cx="100" cy="30" r="3" fill="#a855f7" class="swarm-connect"/>
            <circle cx="200" cy="20" r="3" fill="#06b6d4" class="swarm-connect" style="animation-delay: 0.5s;"/>
            <circle cx="300" cy="40" r="3" fill="#f59e0b" class="swarm-connect" style="animation-delay: 1s;"/>
            <circle cx="400" cy="10" r="3" fill="#a855f7" class="swarm-connect" style="animation-delay: 1.5s;"/>
            <circle cx="500" cy="35" r="3" fill="#06b6d4" class="swarm-connect" style="animation-delay: 2s;"/>
            <circle cx="600" cy="15" r="3" fill="#f59e0b" class="swarm-connect" style="animation-delay: 2.5s;"/>
            <circle cx="700" cy="30" r="3" fill="#a855f7" class="swarm-connect" style="animation-delay: 3s;"/>
        </svg>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>