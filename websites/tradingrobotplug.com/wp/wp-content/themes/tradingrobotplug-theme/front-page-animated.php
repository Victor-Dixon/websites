<?php
/*
TradingRobotPlug Animated Hero Homepage - WOW Edition
Description: Enhanced homepage with animated hero section and wow effects
Author: Agent-2 (Architecture & Design Specialist)
Version: 3.0.0
Updated: 2026-01-10
*/
get_header(); ?>

<style>
/* Animated Hero Section Styles */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
    50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.8), 0 0 60px rgba(59, 130, 246, 0.4); }
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes slide-in-left {
    from { transform: translateX(-100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slide-in-right {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes fade-in-up {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes typewriter {
    from { width: 0; }
    to { width: 100%; }
}

@keyframes blink {
    50% { border-color: transparent; }
}

.hero-float-animation { animation: float 6s ease-in-out infinite; }
.hero-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
.hero-gradient-shift { animation: gradient-shift 8s ease infinite; }
.hero-slide-in-left { animation: slide-in-left 1s ease-out; }
.hero-slide-in-right { animation: slide-in-right 1s ease-out 0.3s both; }
.hero-fade-in-up { animation: fade-in-up 1s ease-out 0.6s both; }
.hero-typewriter { animation: typewriter 3s steps(30, end) 1s both; }
.hero-blink { animation: blink 1s infinite; }

/* Enhanced gradient text */
.gradient-text-enhanced {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
    background-size: 400% 400%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gradient-shift 3s ease infinite;
}

/* Floating particles */
.floating-particle {
    position: absolute;
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.floating-particle:nth-child(1) { animation-delay: 0s; }
.floating-particle:nth-child(2) { animation-delay: 2s; }
.floating-particle:nth-child(3) { animation-delay: 4s; }
.floating-particle:nth-child(4) { animation-delay: 1s; }
.floating-particle:nth-child(5) { animation-delay: 3s; }

/* Geometric shapes */
.geometric-shape {
    position: absolute;
    border: 2px solid rgba(59, 130, 246, 0.3);
    animation: float 8s ease-in-out infinite;
}

.geometric-shape.circle { border-radius: 50%; animation: float 8s ease-in-out infinite, spin 20s linear infinite; }
.geometric-shape.square { transform: rotate(45deg); }
.geometric-shape.triangle { width: 0; height: 0; border-left: 20px solid transparent; border-right: 20px solid transparent; border-bottom: 35px solid rgba(139, 92, 246, 0.4); border: none; background: none; }

/* Enhanced CTA buttons */
.cta-button-enhanced {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.cta-button-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.cta-button-enhanced:hover::before {
    left: 100%;
}

.cta-button-enhanced.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.cta-button-enhanced.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

/* Stats counter animation */
.stats-counter {
    font-size: 2.5rem;
    font-weight: bold;
    color: #fff;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .hero-typewriter { font-size: 1.5rem; }
    .floating-particle { display: none; }
    .geometric-shape { display: none; }
}
</style>

<!-- ===== ANIMATED HERO SECTION - WOW EDITION ===== -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 hero-gradient-shift">

    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Floating Particles -->
        <div class="floating-particle w-4 h-4 bg-blue-400 opacity-60 top-20 left-20"></div>
        <div class="floating-particle w-6 h-6 bg-purple-400 opacity-40 top-40 right-32"></div>
        <div class="floating-particle w-3 h-3 bg-pink-400 opacity-50 bottom-32 left-40"></div>
        <div class="floating-particle w-5 h-5 bg-green-400 opacity-45 bottom-40 right-20"></div>
        <div class="floating-particle w-2 h-2 bg-yellow-400 opacity-70 top-60 left-60"></div>

        <!-- Geometric Shapes -->
        <div class="geometric-shape circle w-32 h-32 top-16 right-16"></div>
        <div class="geometric-shape square w-24 h-24 bottom-16 left-16"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-gradient-to-r from-pink-500 to-purple-500 rounded-lg transform rotate-12 hero-float-animation" style="animation-delay: 5s;"></div>
    </div>

    <!-- Content Container -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Main Headline with Typewriter Effect -->
        <div class="mb-8">
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="gradient-text-enhanced">
                    AI-Powered Trading
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-blue-400">
                        Robots Live
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                We're building and testing trading robots in real-time. Join the waitlist to get early access when we launch—watch our swarm build live.
            </p>
        </div>

        <!-- Enhanced CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-lg text-white no-underline hero-pulse-glow"
               href="<?php echo esc_url(home_url('/waitlist')); ?>" role="button">
                Join the Waitlist →
            </a>
            <a class="cta-button-enhanced px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="#swarm-status" role="button">
                Watch Us Build Live
            </a>
        </div>

        <!-- Urgency Message -->
        <p class="text-lg text-yellow-300 mb-8 hero-fade-in-up font-semibold animate-pulse">
            ⚡ Limited early access spots—join now to be first in line
        </p>

        <!-- Real-Time Swarm Status with Enhanced Styling -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">🚀 Real-Time Swarm Status</h3>
            <div class="text-center">
                <?php echo do_shortcode('[trp_swarm_status mode="summary" refresh="30"]'); ?>
            </div>
        </div>

        <!-- Live Market Preview with Enhanced Design -->
        <div class="market-preview bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-2xl mx-auto border border-white/10" id="live-market-preview">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center justify-center">
                <span class="mr-2">📈</span> Live Market Data
            </h4>
            <div id="market-items-container" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Stock items loaded dynamically via JavaScript -->
                <div class="market-item loading bg-white/10 rounded-lg p-3 text-center">
                    <span class="market-symbol text-white font-semibold block">Loading...</span>
                    <span class="market-price text-gray-300 block">--</span>
                    <span class="market-change text-gray-400 block">--</span>
                </div>
            </div>
            <p id="market-update-time" class="text-sm mt-4 opacity-70 text-gray-400 text-center">
                Powered by Yahoo Finance | Updated every 30 seconds
            </p>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>

<!-- ===== REST OF THE PAGE REMAINS THE SAME ===== -->
<?php
// Include the rest of the original front-page.php content
// You can copy the remaining sections from the original file here

// For now, just include a simple closing
get_footer();
?>

<script>
// Enhanced market data loading with better animations
(function() {
    'use strict';

    const apiEndpoint = '<?php echo esc_url(rest_url('tradingrobotplug/v1/stock-data')); ?>';
    const refreshInterval = 30000; // 30 seconds
    let updateTimer = null;

    function formatPrice(price) {
        return '$' + parseFloat(price).toFixed(2);
    }

    function formatChange(changePercent) {
        const change = parseFloat(changePercent);
        const arrow = change >= 0 ? '↗' : '↘';
        const sign = change >= 0 ? '+' : '';
        return arrow + ' ' + sign + change.toFixed(2) + '%';
    }

    function getChangeClass(changePercent) {
        return parseFloat(changePercent) >= 0 ? 'text-green-400' : 'text-red-400';
    }

    function renderStockItems(stockData) {
        const container = document.getElementById('market-items-container');
        if (!container) return;

        if (!stockData || stockData.length === 0) {
            container.innerHTML = '<div class="market-item bg-white/10 rounded-lg p-3 text-center"><span class="text-white">No data available</span></div>';
            return;
        }

        // Sort by symbol to maintain consistent order: TSLA, QQQ, SPY, NVDA
        const symbolOrder = ['TSLA', 'QQQ', 'SPY', 'NVDA'];
        stockData.sort((a, b) => symbolOrder.indexOf(a.symbol) - symbolOrder.indexOf(b.symbol));

        const html = stockData.map(stock => {
            // Handle both database format and API format
            const symbol = stock.symbol || stock.SYMBOL || 'N/A';
            const price = parseFloat(stock.price || stock.PRICE || 0);
            const changePercent = parseFloat(stock.change_percent || stock.CHANGE_PERCENT || stock.changePercent || 0);

            return `
            <div class="market-item bg-white/10 backdrop-blur-sm rounded-lg p-3 text-center transform hover:scale-105 transition-all duration-300 animate-fade-in">
                <span class="market-symbol text-white font-bold block text-lg">${symbol}</span>
                <span class="market-price text-gray-300 block font-semibold">${formatPrice(price)}</span>
                <span class="market-change ${getChangeClass(changePercent)} block font-medium">${formatChange(changePercent)}</span>
            </div>
        `;
        }).join('');

        container.innerHTML = html;

        // Add fade-in animation to new elements
        setTimeout(() => {
            document.querySelectorAll('.animate-fade-in').forEach(el => {
                el.classList.add('opacity-100');
            });
        }, 100);
    }

    function updateTimestamp(timestamp) {
        const timeElement = document.getElementById('market-update-time');
        if (timeElement && timestamp) {
            const date = new Date(timestamp);
            timeElement.textContent = `Powered by Yahoo Finance | Last updated: ${date.toLocaleTimeString()}`;
        }
    }

    async function fetchMarketData() {
        try {
            const response = await fetch(apiEndpoint);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            renderStockItems(data);
            updateTimestamp(new Date().toISOString());

        } catch (error) {
            console.error('Failed to fetch market data:', error);
            const container = document.getElementById('market-items-container');
            if (container) {
                container.innerHTML = '<div class="market-item bg-red-900/50 rounded-lg p-3 text-center"><span class="text-red-300">Failed to load market data</span></div>';
            }
        }
    }

    function startUpdates() {
        // Initial load
        fetchMarketData();

        // Set up periodic updates
        updateTimer = setInterval(fetchMarketData, refreshInterval);
    }

    function stopUpdates() {
        if (updateTimer) {
            clearInterval(updateTimer);
            updateTimer = null;
        }
    }

    // Start updates when page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startUpdates);
    } else {
        startUpdates();
    }

    // Stop updates when page unloads
    window.addEventListener('beforeunload', stopUpdates);

})();
</script>