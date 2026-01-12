<?php
/**
 * TradingRobotPlug - Specialized Animated Hero
 * Theme: AI Trading Technology & Automation
 * Audience: Traders, Investors, Tech Enthusiasts
 */
?>

<style>
/* TradingRobotPlug Theme - Financial Tech Colors */
@keyframes marketPulse {
    0%, 100% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.3); }
    50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.6), 0 0 60px rgba(34, 197, 94, 0.3); }
}

@keyframes priceTick {
    0% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
    100% { transform: translateY(0); }
}

@keyframes tradingFlow {
    0% { transform: translateX(-100%); }
    25% { transform: translateX(0); }
    75% { transform: translateX(0); }
    100% { transform: translateX(100%); }
}

.hero-market-pulse { animation: marketPulse 2s ease-in-out infinite; }
.price-tick { animation: priceTick 1s ease-in-out infinite; }
.trading-flow { animation: tradingFlow 8s linear infinite; }

/* Trading-specific gradients */
.trading-gradient {
    background: linear-gradient(135deg, #1a365d 0%, #2d3748 25%, #38a169 50%, #3182ce 75%, #1a365d 100%);
    background-size: 400% 400%;
    animation: gradient-shift 10s ease infinite;
}

/* Market indicators */
.market-up { color: #38a169; }
.market-down { color: #e53e3e; }
.market-neutral { color: #718096; }

/* Trading charts animation */
@keyframes chartLine {
    0% { stroke-dashoffset: 1000; }
    100% { stroke-dashoffset: 0; }
}

.chart-line {
    stroke-dasharray: 1000;
    stroke-dashoffset: 1000;
    animation: chartLine 3s ease-in-out forwards;
}
</style>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 trading-gradient">

    <!-- Trading Data Flow Animation -->
    <div class="absolute inset-0 overflow-hidden opacity-30">
        <div class="trading-flow absolute top-20 left-0 right-0 h-px bg-gradient-to-r from-transparent via-green-400 to-transparent"></div>
        <div class="trading-flow absolute top-40 left-0 right-0 h-px bg-gradient-to-r from-transparent via-blue-400 to-transparent" style="animation-delay: 2s;"></div>
        <div class="trading-flow absolute top-60 left-0 right-0 h-px bg-gradient-to-r from-transparent via-purple-400 to-transparent" style="animation-delay: 4s;"></div>
    </div>

    <!-- Floating Trading Elements -->
    <div class="absolute top-24 left-24 w-12 h-12 bg-green-400/20 rounded-full flex items-center justify-center text-green-400 font-bold price-tick">$</div>
    <div class="absolute top-36 right-32 w-12 h-12 bg-blue-400/20 rounded-full flex items-center justify-center text-blue-400 font-bold price-tick">📈</div>
    <div class="absolute bottom-32 left-40 w-12 h-12 bg-purple-400/20 rounded-full flex items-center justify-center text-purple-400 font-bold price-tick">🤖</div>
    <div class="absolute bottom-40 right-20 w-12 h-12 bg-yellow-400/20 rounded-full flex items-center justify-center text-yellow-400 font-bold price-tick">⚡</div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Trading Headline with AI Focus -->
        <div class="mb-8">
            <div class="inline-block px-4 py-2 bg-green-500/10 border border-green-500/30 rounded-full text-green-400 text-sm font-semibold mb-6 animate-pulse">
                🚀 LIVE TRADING ROBOTS ACTIVE
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-500 bg-clip-text text-transparent">
                    AI-Powered
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-green-400">
                        Trading Robots
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                Watch our swarm of AI trading robots execute strategies in real-time.
                Join the waitlist for early access to the future of automated trading.
            </p>
        </div>

        <!-- Trading-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-white no-underline hero-market-pulse"
               href="<?php echo esc_url(home_url('/waitlist')); ?>" role="button">
                🚀 Join Trading Robot Waitlist
            </a>
            <a class="cta-button-enhanced px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="#live-trading" role="button">
                📊 Watch Live Trading
            </a>
        </div>

        <!-- Real-Time Trading Stats -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">🎯 Live Trading Performance</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2 price-tick">+24.7%</div>
                    <div class="text-sm text-gray-400">30-Day Return</div>
                    <div class="text-xs text-green-300 mt-1">Client Growth</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2 price-tick">89.3%</div>
                    <div class="text-sm text-gray-400">Win Rate</div>
                    <div class="text-xs text-blue-300 mt-1">Success Rate</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-400 mb-2 price-tick">47</div>
                    <div class="text-sm text-gray-400">Active Strategies</div>
                    <div class="text-xs text-purple-300 mt-1">AI Algorithms</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-400 mb-2 price-tick">$2.1M</div>
                    <div class="text-sm text-gray-400">Volume Traded</div>
                    <div class="text-xs text-yellow-300 mt-1">24/7 Trading</div>
                </div>
            </div>
        </div>

        <!-- Market Preview with Live Data -->
        <div class="market-preview bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-2xl mx-auto border border-white/10" id="live-market-preview">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center justify-center">
                <span class="mr-2">📈</span> Live Market Data
            </h4>
            <div id="market-items-container" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Stock items loaded dynamically via JavaScript -->
                <div class="market-item loading bg-white/10 rounded-lg p-3 text-center">
                    <span class="market-symbol text-white font-semibold block">TSLA</span>
                    <span class="market-price text-gray-300 block">$248.50</span>
                    <span class="market-change market-up block font-medium">↗ +2.4%</span>
                </div>
                <div class="market-item loading bg-white/10 rounded-lg p-3 text-center">
                    <span class="market-symbol text-white font-semibold block">QQQ</span>
                    <span class="market-price text-gray-300 block">$445.20</span>
                    <span class="market-change market-up block font-medium">↗ +1.8%</span>
                </div>
                <div class="market-item loading bg-white/10 rounded-lg p-3 text-center">
                    <span class="market-symbol text-white font-semibold block">SPY</span>
                    <span class="market-price text-gray-300 block">$542.30</span>
                    <span class="market-change market-neutral block font-medium">→ +0.5%</span>
                </div>
                <div class="market-item loading bg-white/10 rounded-lg p-3 text-center">
                    <span class="market-symbol text-white font-semibold block">NVDA</span>
                    <span class="market-price text-gray-300 block">$875.40</span>
                    <span class="market-change market-up block font-medium">↗ +3.2%</span>
                </div>
            </div>
            <p id="market-update-time" class="text-sm mt-4 opacity-70 text-gray-400 text-center">
                Powered by Yahoo Finance | Updated every 30 seconds
            </p>
        </div>
    </div>

    <!-- Trading Chart Animation -->
    <div class="absolute bottom-20 left-10 right-10 opacity-20">
        <svg viewBox="0 0 800 100" class="w-full h-20">
            <path d="M0,50 Q100,30 200,70 T400,20 T600,80 T800,40"
                  fill="none"
                  stroke="url(#chartGradient)"
                  stroke-width="2"
                  class="chart-line"/>
            <defs>
                <linearGradient id="chartGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#38a169;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#3182ce;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#805ad5;stop-opacity:1" />
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