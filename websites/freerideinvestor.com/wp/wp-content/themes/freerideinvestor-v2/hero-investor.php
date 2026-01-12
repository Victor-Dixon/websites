<?php
/**
 * FreeRideInvestor - Specialized Animated Hero
 * Theme: Investment Education & Financial Freedom
 * Audience: Individual Investors, Beginners, Financial Independence Seekers
 */
?>

<style>
/* FreeRideInvestor Theme - Wealth & Freedom Colors */
@keyframes wealthGlow {
    0%, 100% { box-shadow: 0 0 20px rgba(255, 193, 7, 0.3); }
    50% { box-shadow: 0 0 40px rgba(255, 193, 7, 0.6), 0 0 60px rgba(255, 193, 7, 0.3); }
}

@keyframes moneyFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-10px) rotate(2deg); }
    66% { transform: translateY(-5px) rotate(-2deg); }
}

@keyframes freedomWings {
    0%, 50%, 100% { transform: translateY(0px); }
    25% { transform: translateY(-15px); }
}

@keyframes growthChart {
    0% { height: 20%; }
    50% { height: 80%; }
    100% { height: 60%; }
}

.hero-wealth-glow { animation: wealthGlow 2s ease-in-out infinite; }
.money-float { animation: moneyFloat 4s ease-in-out infinite; }
.freedom-wings { animation: freedomWings 3s ease-in-out infinite; }
.growth-chart { animation: growthChart 2s ease-in-out infinite; }

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

/* Investment-themed gradients */
.investor-gradient {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 25%, #fbbf24 50%, #10b981 75%, #1a202c 100%);
    background-size: 400% 400%;
    animation: gradient-shift 12s ease infinite;
}

/* Financial indicators */
.portfolio-up { color: #10b981; }
.portfolio-down { color: #ef4444; }
.portfolio-neutral { color: #6b7280; }

/* Freedom symbols */
.freedom-symbols {
    position: absolute;
    font-size: 2rem;
    opacity: 0.6;
    animation: freedomWings 4s ease-in-out infinite;
}
</style>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-gray-900 via-amber-900 to-gray-900 investor-gradient">

    <!-- Freedom Symbol Animations -->
    <div class="freedom-symbols top-20 left-20">🕊️</div>
    <div class="freedom-symbols top-40 right-32" style="animation-delay: 1s;">💰</div>
    <div class="freedom-symbols bottom-32 left-40" style="animation-delay: 2s;">📈</div>
    <div class="freedom-symbols bottom-40 right-20" style="animation-delay: 0.5s;">🏖️</div>

    <!-- Floating Money Symbols -->
    <div class="absolute top-32 left-32 w-8 h-8 bg-yellow-400/20 rounded-full flex items-center justify-center text-yellow-400 text-lg money-float">$</div>
    <div class="absolute top-48 right-40 w-8 h-8 bg-green-400/20 rounded-full flex items-center justify-center text-green-400 text-lg money-float" style="animation-delay: 1s;">💵</div>
    <div class="absolute bottom-48 left-48 w-8 h-8 bg-blue-400/20 rounded-full flex items-center justify-center text-blue-400 text-lg money-float" style="animation-delay: 2s;">📊</div>
    <div class="absolute bottom-32 right-32 w-8 h-8 bg-purple-400/20 rounded-full flex items-center justify-center text-purple-400 text-lg money-float" style="animation-delay: 0.5s;">🏠</div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Investment Freedom Headline -->
        <div class="mb-8">
            <div class="inline-block px-4 py-2 bg-yellow-500/10 border border-yellow-500/30 rounded-full text-yellow-400 text-sm font-semibold mb-6 animate-pulse">
                🆓 FINANCIAL FREEDOM AWAITS
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="bg-gradient-to-r from-yellow-400 via-green-500 to-blue-500 bg-clip-text text-transparent">
                    Free Your
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-yellow-400">
                        Money's Potential
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                Transform your financial future with intelligent investing strategies.
                Learn, invest, and achieve the freedom you've always dreamed of.
            </p>
        </div>

        <!-- Investment-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-white no-underline hero-wealth-glow"
               href="<?php echo esc_url(home_url('/start-investing')); ?>" role="button">
                🚀 Start Your Journey to Freedom
            </a>
            <a class="cta-button-enhanced px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="#investment-education" role="button">
                📚 Learn Investment Basics
            </a>
        </div>

        <!-- Investment Success Stories -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">💰 Freedom Achieved</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-400 mb-2 freedom-wings">$1.2M</div>
                    <div class="text-sm text-gray-400">Average Portfolio Growth</div>
                    <div class="text-xs text-green-400 mt-1">in 5 years</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2 freedom-wings">87%</div>
                    <div class="text-sm text-gray-400">Financial Freedom Rate</div>
                    <div class="text-xs text-blue-400 mt-1">among graduates</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2 freedom-wings">50K+</div>
                    <div class="text-sm text-gray-400">Students Empowered</div>
                    <div class="text-xs text-purple-400 mt-1">since 2020</div>
                </div>
            </div>
        </div>

        <!-- Investment Growth Visualization -->
        <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-2xl mx-auto border border-white/10">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center justify-center">
                <span class="mr-2">📈</span> Investment Growth Potential
            </h4>

            <!-- Simple Growth Chart Animation -->
            <div class="flex items-end justify-center space-x-2 h-32 mb-4">
                <div class="w-8 bg-gradient-to-t from-red-500 to-red-300 rounded-t growth-chart" style="animation-delay: 0s;"></div>
                <div class="w-8 bg-gradient-to-t from-yellow-500 to-yellow-300 rounded-t growth-chart" style="animation-delay: 0.3s;"></div>
                <div class="w-8 bg-gradient-to-t from-green-500 to-green-300 rounded-t growth-chart" style="animation-delay: 0.6s;"></div>
                <div class="w-8 bg-gradient-to-t from-blue-500 to-blue-300 rounded-t growth-chart" style="animation-delay: 0.9s;"></div>
                <div class="w-8 bg-gradient-to-t from-purple-500 to-purple-300 rounded-t growth-chart" style="animation-delay: 1.2s;"></div>
            </div>

            <div class="flex justify-between text-xs text-gray-400 mb-4">
                <span>Year 1</span>
                <span>Year 2</span>
                <span>Year 3</span>
                <span>Year 4</span>
                <span>Year 5</span>
            </div>

            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <div class="text-lg font-bold text-green-400">$10K</div>
                    <div class="text-xs text-gray-400">Initial Investment</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-yellow-400">$25K+</div>
                    <div class="text-xs text-gray-400">Potential Growth</div>
                </div>
            </div>

            <p class="text-xs mt-4 opacity-70 text-gray-400 text-center">
                *Hypothetical growth illustration. Past performance ≠ future results.
            </p>
        </div>
    </div>

    <!-- Freedom Path Animation -->
    <div class="absolute bottom-32 left-10 right-10 opacity-30">
        <svg viewBox="0 0 800 80" class="w-full h-16">
            <path d="M0,40 Q100,20 200,50 T400,30 T600,60 T800,40"
                  fill="none"
                  stroke="url(#freedomGradient)"
                  stroke-width="2"
                  opacity="0.6"
                  class="animate-pulse"/>
            <defs>
                <linearGradient id="freedomGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#fbbf24;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#10b981;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                </linearGradient>
            </defs>
        </svg>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>