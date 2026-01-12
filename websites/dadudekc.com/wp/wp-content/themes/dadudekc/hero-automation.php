<?php
/**
 * Dadu Dek C - Automation Systems Hero
 * Theme: Automation & Development Services
 * Audience: Teams & Businesses needing automation solutions
 */
?>

<style>
/* Dadu Dek C Automation Theme - Professional & Tech Colors */
@keyframes codeFlow {
    0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
    50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.6), 0 0 60px rgba(59, 130, 246, 0.3); }
}

@keyframes automationPulse {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes systemConnect {
    0% { opacity: 0.3; }
    50% { opacity: 1; }
    100% { opacity: 0.3; }
}

@keyframes codeStream {
    0% { transform: translateX(-100%); }
    25% { transform: translateX(0%); }
    75% { transform: translateX(0%); }
    100% { transform: translateX(100%); }
}

.hero-code-flow { animation: codeFlow 2s ease-in-out infinite; }
.automation-pulse { animation: automationPulse 3s ease-in-out infinite; }
.system-connect { animation: systemConnect 4s ease-in-out infinite; }
.code-stream { animation: codeStream 8s linear infinite; }

/* Typewriter Effect */
@keyframes typewriter {
    from { width: 0; }
    to { width: 100%; }
}

.hero-typewriter {
    animation: typewriter 3s steps(40, end) 1s both;
}

/* Fade In Up Animation */
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

.hero-fade-in-up {
    animation: fadeInUp 1s ease-out forwards;
    opacity: 0;
}

/* Gradient Animation */
@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Automation-themed gradients */
.automation-gradient {
    background: linear-gradient(135deg, #1e293b 0%, #334155 25%, #3b82f6 50%, #10b981 75%, #1e293b 100%);
    background-size: 400% 400%;
    animation: gradient-shift 12s ease infinite;
}

/* Code elements */
.code-line {
    position: absolute;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    color: rgba(59, 130, 246, 0.6);
    white-space: nowrap;
}

/* Automation icons */
.automation-gear {
    animation: automationPulse 4s ease-in-out infinite;
}

.automation-circuit::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image:
        radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.3) 2px, transparent 2px),
        radial-gradient(circle at 75% 25%, rgba(16, 185, 129, 0.3) 2px, transparent 2px),
        radial-gradient(circle at 25% 75%, rgba(139, 92, 246, 0.3) 2px, transparent 2px),
        radial-gradient(circle at 75% 75%, rgba(236, 72, 153, 0.3) 2px, transparent 2px);
    background-size: 40px 40px;
    animation: system-connect 6s ease-in-out infinite;
}
</style>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 automation-gradient">

    <!-- Code Stream Animation -->
    <div class="absolute top-32 left-0 right-0 h-px bg-gradient-to-r from-transparent via-blue-400 to-transparent code-stream opacity-40"></div>
    <div class="absolute top-48 left-0 right-0 h-px bg-gradient-to-r from-transparent via-green-400 to-transparent code-stream opacity-40" style="animation-delay: 2s;"></div>
    <div class="absolute top-64 left-0 right-0 h-px bg-gradient-to-r from-transparent via-purple-400 to-transparent code-stream opacity-40" style="animation-delay: 4s;"></div>

    <!-- Floating Code Lines -->
    <div class="code-line top-24 left-32 automation-pulse">function automateWorkflow() {</div>
    <div class="code-line top-36 right-40 automation-pulse" style="animation-delay: 1s;">const efficiency = hours * 0.7;</div>
    <div class="code-line bottom-40 left-48 automation-pulse" style="animation-delay: 2s;">return optimizedProcess;</div>

    <!-- Circuit Board Background -->
    <div class="absolute inset-0 automation-circuit opacity-10"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Automation Hero Headline -->
        <div class="mb-8">
            <div class="inline-block px-4 py-2 bg-blue-500/10 border border-blue-500/30 rounded-full text-blue-400 text-sm font-semibold mb-6 animate-pulse">
                🤖 AUTOMATION SYSTEMS SPECIALIST
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="bg-gradient-to-r from-blue-400 via-green-500 to-purple-500 bg-clip-text text-transparent">
                    <?php echo dadudekc_get_positioning_line(); ?>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                Transform your team's productivity with custom automation systems.
                From workflow optimization to data processing pipelines, I build solutions
                that eliminate repetitive tasks and scale your operations.
            </p>
        </div>

        <!-- Automation-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-white no-underline hero-code-flow"
               href="<?php echo esc_url(home_url('/services')); ?>" role="button">
                🚀 View Automation Services
            </a>
            <a class="cta-button-enhanced px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="<?php echo esc_url(home_url('/contact')); ?>" role="button">
                💬 Discuss Your Project
            </a>
        </div>

        <!-- Automation Impact Stats -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">⚡ Automation Results</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2 automation-pulse">70%</div>
                    <div class="text-sm text-gray-400">Time Saved</div>
                    <div class="text-xs text-blue-300 mt-1">Average per client</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2 automation-pulse">50+</div>
                    <div class="text-sm text-gray-400">Systems Built</div>
                    <div class="text-xs text-green-300 mt-1">Custom solutions</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2 automation-pulse">24/7</div>
                    <div class="text-sm text-gray-400">Operation</div>
                    <div class="text-xs text-purple-300 mt-1">Always running</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-pink-400 mb-2 automation-pulse">100%</div>
                    <div class="text-sm text-gray-400">Success Rate</div>
                    <div class="text-xs text-pink-300 mt-1">Client satisfaction</div>
                </div>
            </div>
        </div>

        <!-- Service Categories -->
        <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-4xl mx-auto border border-white/10">
            <h4 class="text-xl font-semibold text-white mb-6 flex items-center justify-center">
                <span class="mr-2">🔧</span> Automation Solutions
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-900/50 to-cyan-900/50 p-6 rounded-lg border border-blue-500/30 hover:border-blue-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-3xl mb-3 automation-pulse">⚙️</div>
                    <div class="font-bold text-white text-lg mb-2">Workflow Automation</div>
                    <div class="text-sm text-gray-400">Streamline repetitive processes and eliminate manual tasks</div>
                </div>

                <div class="bg-gradient-to-br from-green-900/50 to-blue-900/50 p-6 rounded-lg border border-green-500/30 hover:border-green-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-3xl mb-3 automation-pulse" style="animation-delay: 0.5s;">📊</div>
                    <div class="font-bold text-white text-lg mb-2">Data Processing</div>
                    <div class="text-sm text-gray-400">Build pipelines for data collection, analysis, and reporting</div>
                </div>

                <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/50 p-6 rounded-lg border border-purple-500/30 hover:border-purple-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-3xl mb-3 automation-pulse" style="animation-delay: 1s;">🔗</div>
                    <div class="font-bold text-white text-lg mb-2">System Integration</div>
                    <div class="text-sm text-gray-400">Connect disparate tools and create unified workflows</div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-lg text-gray-300 mb-4"><?php echo dadudekc_get_positioning_question(); ?></p>
                <a href="#contact" class="inline-block px-6 py-3 bg-gradient-to-r from-blue-500 to-green-500 rounded-full font-semibold text-white hover:from-blue-600 hover:to-green-600 transition-all duration-300 transform hover:scale-105">
                    <?php echo dadudekc_get_positioning_action(); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Tech Grid Background -->
    <div class="absolute bottom-32 left-10 right-10 opacity-20">
        <svg viewBox="0 0 800 60" class="w-full h-12">
            <defs>
                <linearGradient id="techFlow" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#10b981;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                </linearGradient>
            </defs>
            <!-- Tech flow lines -->
            <path d="M0,20 Q150,40 300,20 T500,40 T700,20 T800,30"
                  fill="none"
                  stroke="url(#techFlow)"
                  stroke-width="1"
                  opacity="0.6"
                  class="animate-pulse"/>
        </svg>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>