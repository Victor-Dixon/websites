<?php
/**
 * PrismBlossom - Specialized Animated Hero
 * Theme: Business Consulting & Professional Services
 * Audience: Businesses, Entrepreneurs, Executives
 */
?>

<style>
/* PrismBlossom Theme - Professional & Corporate Colors */
@keyframes businessGrowth {
    0%, 100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
    50% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.6), 0 0 60px rgba(16, 185, 129, 0.3); }
}

@keyframes chartRise {
    0% { height: 20%; }
    50% { height: 70%; }
    100% { height: 90%; }
}

@keyframes networkConnect {
    0% { opacity: 0.3; transform: scale(0.8); }
    50% { opacity: 1; transform: scale(1.2); }
    100% { opacity: 0.3; transform: scale(0.8); }
}

@keyframes professionalPulse {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

@keyframes strategyFlow {
    0% { transform: translateX(-100%); }
    25% { transform: translateX(0%); }
    75% { transform: translateX(0%); }
    100% { transform: translateX(100%); }
}

.hero-business-growth { animation: businessGrowth 2s ease-in-out infinite; }
.chart-rise { animation: chartRise 2s ease-in-out infinite; }
.network-connect { animation: networkConnect 3s ease-in-out infinite; }
.professional-pulse { animation: professionalPulse 4s ease-in-out infinite; }
.strategy-flow { animation: strategyFlow 6s linear infinite; }

/* Business-themed gradients */
.business-gradient {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #10b981 50%, #3b82f6 75%, #0f172a 100%);
    background-size: 400% 400%;
    animation: gradient-shift 15s ease infinite;
}

/* Professional network visualization */
.network-node {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    animation: networkConnect 4s ease-in-out infinite;
}

.network-node:nth-child(1) { animation-delay: 0s; }
.network-node:nth-child(2) { animation-delay: 0.5s; }
.network-node:nth-child(3) { animation-delay: 1s; }
.network-node:nth-child(4) { animation-delay: 1.5s; }
.network-node:nth-child(5) { animation-delay: 2s; }

/* Business metrics bars */
.metrics-bar {
    position: relative;
    height: 4px;
    background: rgba(16, 185, 129, 0.3);
    border-radius: 2px;
    overflow: hidden;
}

.metrics-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #10b981, #3b82f6);
    border-radius: 2px;
    animation: chartRise 2s ease-in-out infinite;
}

/* Professional icons */
.business-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #10b981, #3b82f6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}
</style>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 business-gradient">

    <!-- Professional Network Visualization -->
    <div class="absolute inset-0 opacity-20">
        <!-- Network nodes -->
        <div class="network-node top-32 left-32"></div>
        <div class="network-node top-48 right-40"></div>
        <div class="network-node bottom-48 left-48"></div>
        <div class="network-node bottom-32 right-32"></div>
        <div class="network-node top-64 left-1/2"></div>

        <!-- Connection lines -->
        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <linearGradient id="networkGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:0.5" />
                    <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0.5" />
                </linearGradient>
            </defs>
            <line x1="25" y1="25" x2="75" y2="25" stroke="url(#networkGradient)" stroke-width="0.5"/>
            <line x1="75" y1="25" x2="75" y2="75" stroke="url(#networkGradient)" stroke-width="0.5"/>
            <line x1="75" y1="75" x2="25" y2="75" stroke="url(#networkGradient)" stroke-width="0.5"/>
            <line x1="25" y1="75" x2="25" y2="25" stroke="url(#networkGradient)" stroke-width="0.5"/>
            <line x1="50" y1="15" x2="50" y2="50" stroke="url(#networkGradient)" stroke-width="0.5"/>
        </svg>
    </div>

    <!-- Strategic Flow Animation -->
    <div class="absolute top-24 left-0 right-0 h-px bg-gradient-to-r from-transparent via-emerald-400 to-transparent strategy-flow"></div>
    <div class="absolute top-40 left-0 right-0 h-px bg-gradient-to-r from-transparent via-blue-400 to-transparent strategy-flow" style="animation-delay: 1s;"></div>
    <div class="absolute top-56 left-0 right-0 h-px bg-gradient-to-r from-transparent via-purple-400 to-transparent strategy-flow" style="animation-delay: 2s;"></div>

    <!-- Professional Icons -->
    <div class="absolute top-28 left-24 professional-pulse">
        <div class="business-icon">📊</div>
    </div>
    <div class="absolute top-44 right-28 professional-pulse" style="animation-delay: 0.5s;">
        <div class="business-icon">🎯</div>
    </div>
    <div class="absolute bottom-44 left-32 professional-pulse" style="animation-delay: 1s;">
        <div class="business-icon">🚀</div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Business Consulting Headline -->
        <div class="mb-8">
            <div class="inline-block px-4 py-2 bg-emerald-500/20 border border-emerald-500/40 rounded-full text-emerald-400 text-sm font-semibold mb-6 animate-pulse">
                💼 PROFESSIONAL BUSINESS CONSULTING
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="bg-gradient-to-r from-emerald-400 via-blue-500 to-purple-500 bg-clip-text text-transparent">
                    Prism
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-emerald-400">
                        Blossom
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                Strategic consulting that transforms businesses. We help organizations navigate complexity,
                seize opportunities, and achieve sustainable growth through data-driven insights and proven methodologies.
            </p>
        </div>

        <!-- Business-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-white no-underline hero-business-growth"
               href="<?php echo esc_url(home_url('/consultation')); ?>" role="button">
                📞 Schedule Consultation
            </a>
            <a class="cta-button-enhanced px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="#our-services" role="button">
                📈 View Our Services
            </a>
        </div>

        <!-- Business Impact Metrics -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">📊 Proven Results</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-emerald-400 mb-2 network-connect">250%</div>
                    <div class="text-sm text-gray-400">Average ROI</div>
                    <div class="text-xs text-emerald-300 mt-1">Client Growth</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2 network-connect">50+</div>
                    <div class="text-sm text-gray-400">Companies Served</div>
                    <div class="text-xs text-blue-300 mt-1">Industry Leaders</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2 network-connect">15</div>
                    <div class="text-sm text-gray-400">Years Experience</div>
                    <div class="text-xs text-purple-300 mt-1">Market Expertise</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-pink-400 mb-2 network-connect">99%</div>
                    <div class="text-sm text-gray-400">Success Rate</div>
                    <div class="text-xs text-pink-300 mt-1">Project Delivery</div>
                </div>
            </div>
        </div>

        <!-- Service Offerings -->
        <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-4xl mx-auto border border-white/10">
            <h4 class="text-xl font-semibold text-white mb-6 flex items-center justify-center">
                <span class="mr-2">🎯</span> Strategic Consulting Services
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-emerald-900/50 to-blue-900/50 p-6 rounded-lg border border-emerald-500/30 hover:border-emerald-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="business-icon mx-auto mb-4">📊</div>
                    <div class="font-bold text-white text-lg mb-2">Strategic Planning</div>
                    <div class="text-sm text-gray-400">Develop comprehensive business strategies for sustainable growth</div>
                </div>

                <div class="bg-gradient-to-br from-blue-900/50 to-purple-900/50 p-6 rounded-lg border border-blue-500/30 hover:border-blue-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="business-icon mx-auto mb-4">🚀</div>
                    <div class="font-bold text-white text-lg mb-2">Digital Transformation</div>
                    <div class="text-sm text-gray-400">Modernize operations and leverage emerging technologies</div>
                </div>

                <div class="bg-gradient-to-br from-purple-900/50 to-pink-900/50 p-6 rounded-lg border border-purple-500/30 hover:border-purple-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="business-icon mx-auto mb-4">📈</div>
                    <div class="font-bold text-white text-lg mb-2">Performance Optimization</div>
                    <div class="text-sm text-gray-400">Enhance efficiency and maximize organizational potential</div>
                </div>
            </div>

            <!-- Growth Chart Visualization -->
            <div class="mt-8">
                <h5 class="text-white font-semibold mb-4 text-center">Expected Business Growth Trajectory</h5>
                <div class="flex items-end justify-center space-x-4 h-24">
                    <div class="w-12 bg-gradient-to-t from-emerald-500 to-emerald-300 rounded-t chart-rise"></div>
                    <div class="w-12 bg-gradient-to-t from-blue-500 to-blue-300 rounded-t chart-rise" style="animation-delay: 0.3s;"></div>
                    <div class="w-12 bg-gradient-to-t from-purple-500 to-purple-300 rounded-t chart-rise" style="animation-delay: 0.6s;"></div>
                    <div class="w-12 bg-gradient-to-t from-pink-500 to-pink-300 rounded-t chart-rise" style="animation-delay: 0.9s;"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span>Month 1</span>
                    <span>Month 3</span>
                    <span>Month 6</span>
                    <span>Month 12</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Strategy Flow -->
    <div class="absolute bottom-32 left-10 right-10 opacity-20">
        <svg viewBox="0 0 800 60" class="w-full h-12">
            <defs>
                <linearGradient id="strategyPath" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#3b82f6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                </linearGradient>
            </defs>
            <path d="M0,30 Q150,10 300,40 T500,20 T700,50 T800,30"
                  fill="none"
                  stroke="url(#strategyPath)"
                  stroke-width="2"
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