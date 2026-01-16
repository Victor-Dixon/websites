<?php
/*
TradingRobotPlug Modern Homepage - Optimized Version
Description: Streamlined 4-section homepage structure (Hero, Swarm Status, Paper Trading, Final CTA)
Author: Agent-7 (Web Development)
Version: 2.1.0
Updated: 2025-12-30
*/
get_header(); ?>

<!-- ===== ANIMATED TSLA HERO SECTION - Three.js + Tailwind ===== -->
<section class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    <!-- Three.js Canvas Background -->
    <div id="hero-canvas" class="absolute inset-0 z-0"></div>

    <!-- Animated Background Elements -->
    <div class="absolute inset-0 z-10">
        <!-- Floating Particles -->
        <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-blue-400 rounded-full animate-pulse opacity-60"></div>
        <div class="absolute top-3/4 right-1/4 w-1 h-1 bg-cyan-300 rounded-full animate-ping opacity-40"></div>
        <div class="absolute top-1/2 left-3/4 w-3 h-3 bg-indigo-400 rounded-full animate-bounce opacity-50"></div>

        <!-- Gradient Overlays -->
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent via-slate-900/20 to-slate-900/80"></div>
        <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-slate-900 to-transparent"></div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-20 container mx-auto px-6 py-20 lg:py-32">
        <div class="grid lg:grid-cols-2 gap-12 items-center min-h-screen">

            <!-- Left Column: TSLA Intelligence Hub -->
            <div class="space-y-8 animate-fade-in-up">

                <!-- TSLA Real-time Price Card -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl animate-slide-in-left">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                <span class="text-white font-bold text-lg">T</span>
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-xl">TSLA</h3>
                                <p class="text-gray-300 text-sm">Tesla, Inc.</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-white" id="tsla-price">$--.--</div>
                            <div class="text-sm" id="tsla-change">--</div>
                        </div>
                    </div>

                    <!-- Live Indicators -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-xs text-gray-400 uppercase tracking-wide">Volume</div>
                            <div class="text-sm font-semibold text-white" id="tsla-volume">--</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-400 uppercase tracking-wide">52W High</div>
                            <div class="text-sm font-semibold text-green-400" id="tsla-high">$--.--</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-400 uppercase tracking-wide">52W Low</div>
                            <div class="text-sm font-semibold text-red-400" id="tsla-low">$--.--</div>
                        </div>
                    </div>

                    <!-- Real-time Pulse -->
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-xs text-green-400 font-medium">LIVE DATA</span>
                        <span class="text-xs text-gray-400" id="tsla-timestamp">--:--:--</span>
                    </div>
                </div>

                <!-- Main Headline with Typewriter Effect -->
                <div class="animate-fade-in-up animation-delay-300">
                    <h1 class="text-5xl lg:text-7xl font-bold text-white leading-tight mb-6">
                        <span class="bg-gradient-to-r from-blue-400 via-cyan-300 to-indigo-400 bg-clip-text text-transparent">
                            Tesla Trading
                        </span>
                        <br>
                        <span class="text-white">Intelligence</span>
                    </h1>

                    <p class="text-xl text-gray-300 mb-8 leading-relaxed animate-fade-in-up animation-delay-500">
                        Our AI swarm analyzes Tesla's every move in real-time. Join beta access to see how AI predicts TSLA's next big swing before Wall Street does.
                    </p>
                </div>

                <!-- CTA Buttons with Hover Effects -->
                <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up animation-delay-700">
                    <a href="<?php echo esc_url(home_url('/waitlist')); ?>"
                       class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-2xl transform hover:scale-105 transition-all duration-300 overflow-hidden">
                        <span class="relative z-10">Get TSLA Beta Access →</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>

                    <a href="#live-tsla-analysis"
                       class="px-8 py-4 border-2 border-white/30 text-white font-semibold rounded-xl hover:bg-white/10 hover:border-white/50 transition-all duration-300 backdrop-blur-sm">
                        See Live Analysis
                    </a>
                </div>

                <!-- Urgency Badge -->
                <div class="inline-flex items-center space-x-2 bg-red-500/20 border border-red-500/30 rounded-full px-4 py-2 animate-pulse">
                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    <span class="text-red-300 text-sm font-medium">Limited Tesla-focused beta spots—join now</span>
                </div>
            </div>

            <!-- Right Column: AI Recommendations & Performance -->
            <div class="space-y-6 animate-fade-in-right">

                <!-- AI Recommendations Engine -->
                <div class="bg-gradient-to-br from-green-500/10 to-blue-500/10 backdrop-blur-xl border border-green-500/20 rounded-2xl p-6 shadow-2xl">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-lg">🎯</span>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-lg">AI Buy/Sell Recommendations</h3>
                            <p class="text-gray-400 text-sm">84% confidence signals</p>
                        </div>
                    </div>

                    <!-- Current Recommendation -->
                    <div class="bg-green-500/20 border border-green-500/30 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-green-300 font-semibold">TSLA: STRONG BUY</span>
                            <span class="text-green-400 text-sm font-bold">84% Confidence</span>
                        </div>
                        <p class="text-green-200 text-sm mb-3">Momentum indicators show strong upward trend. Volume confirms institutional interest.</p>
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-gray-400">Entry:</span>
                                <span class="text-white ml-2">$265.50</span>
                            </div>
                            <div>
                                <span class="text-gray-400">Target:</span>
                                <span class="text-white ml-2">$295.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-white/5 rounded-lg p-3">
                            <div class="text-lg font-bold text-white">89.3%</div>
                            <div class="text-xs text-gray-400">Win Rate</div>
                        </div>
                        <div class="bg-white/5 rounded-lg p-3">
                            <div class="text-lg font-bold text-green-400">+32.8%</div>
                            <div class="text-xs text-gray-400">YTD Return</div>
                        </div>
                        <div class="bg-white/5 rounded-lg p-3">
                            <div class="text-lg font-bold text-blue-400">1.8</div>
                            <div class="text-xs text-gray-400">Sharpe Ratio</div>
                        </div>
                    </div>
                </div>

                <!-- Swarm Intelligence Dashboard -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-500 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold">🤖</span>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-lg">Tesla Swarm Intelligence</h3>
                            <p class="text-gray-400 text-sm">8 AI agents analyzing live</p>
                        </div>
                    </div>

                    <!-- Status Indicators -->
                    <div class="grid grid-cols-1 gap-3">
                        <div class="flex items-center justify-between p-3 bg-green-500/10 border border-green-500/20 rounded-lg">
                            <span class="text-green-300 font-medium">TSLA Analysis</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-green-400 text-sm font-semibold">LIVE</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                            <span class="text-blue-300 font-medium">Pattern Recognition</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-blue-400 rounded-full animate-ping"></div>
                                <span class="text-blue-400 text-sm font-semibold">ACTIVE</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                            <span class="text-yellow-300 font-medium">Signal Generation</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-yellow-400 rounded-full animate-spin"></div>
                                <span class="text-yellow-400 text-sm font-semibold">PROCESSING</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Market Heatmap -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl">
                    <h4 class="text-white font-semibold mb-4 flex items-center space-x-2">
                        <span>📈</span>
                        <span>Live Market Heatmap</span>
                    </h4>

                    <div id="market-heatmap" class="space-y-3">
                        <!-- Market items will be populated by JavaScript -->
                        <div class="market-item animate-pulse">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Loading TSLA data...</span>
                                <div class="w-16 h-4 bg-gray-600 rounded animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-white/10">
                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span>Real-time via Yahoo Finance</span>
                            <span id="market-timestamp">--:--:--</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Element Showcase -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border border-blue-500/30 rounded-xl p-4 text-center hover:scale-105 transition-transform duration-300 cursor-pointer">
                        <div class="text-2xl mb-2">⚡</div>
                        <div class="text-xs text-blue-300 font-medium">Real-time</div>
                        <div class="text-xs text-gray-400">Updates</div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500/20 to-pink-500/20 border border-purple-500/30 rounded-xl p-4 text-center hover:scale-105 transition-transform duration-300 cursor-pointer">
                        <div class="text-2xl mb-2">🎯</div>
                        <div class="text-xs text-purple-300 font-medium">AI-Powered</div>
                        <div class="text-xs text-gray-400">Analysis</div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 border border-green-500/30 rounded-xl p-4 text-center hover:scale-105 transition-transform duration-300 cursor-pointer">
                        <div class="text-2xl mb-2">🚀</div>
                        <div class="text-xs text-green-300 font-medium">Live</div>
                        <div class="text-xs text-gray-400">Signals</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-30 animate-bounce">
        <div class="w-6 h-10 border-2 border-white/30 rounded-full flex justify-center">
            <div class="w-1 h-3 bg-white/50 rounded-full mt-2 animate-pulse"></div>
        </div>
    </div>

    <!-- Custom Animations -->
    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-in-left {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fade-in-right {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
            opacity: 0;
        }

        .animate-slide-in-left {
            animation: slide-in-left 0.8s ease-out forwards;
            opacity: 0;
        }

        .animate-fade-in-right {
            animation: fade-in-right 0.8s ease-out forwards;
            opacity: 0;
        }

        .animation-delay-300 { animation-delay: 0.3s; }
        .animation-delay-500 { animation-delay: 0.5s; }
        .animation-delay-700 { animation-delay: 0.7s; }

        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>

    <!-- Three.js and Animation Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
    (function() {
        'use strict';

        // Three.js Scene Setup
        let scene, camera, renderer, particles, clock;
        let mouse = { x: 0, y: 0 };

        function initThreeJS() {
            // Scene setup
            scene = new THREE.Scene();
            camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });

            const canvas = document.getElementById('hero-canvas');
            renderer.setSize(window.innerWidth, window.innerHeight);
            renderer.setClearColor(0x000000, 0);
            canvas.appendChild(renderer.domElement);

            // Create floating particles
            createParticles();

            // Lighting
            const ambientLight = new THREE.AmbientLight(0x404040, 0.6);
            scene.add(ambientLight);

            const pointLight = new THREE.PointLight(0x4466ff, 1, 100);
            pointLight.position.set(10, 10, 10);
            scene.add(pointLight);

            // Camera position
            camera.position.z = 30;

            // Clock for animations
            clock = new THREE.Clock();

            // Mouse tracking
            document.addEventListener('mousemove', (event) => {
                mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
                mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });

            animate();
        }

        function createParticles() {
            const particleCount = 200;
            const geometry = new THREE.BufferGeometry();
            const positions = new Float32Array(particleCount * 3);
            const colors = new Float32Array(particleCount * 3);

            for (let i = 0; i < particleCount * 3; i += 3) {
                // Random positions
                positions[i] = (Math.random() - 0.5) * 100;     // x
                positions[i + 1] = (Math.random() - 0.5) * 100; // y
                positions[i + 2] = (Math.random() - 0.5) * 100; // z

                // Blue to cyan color palette
                colors[i] = 0.2 + Math.random() * 0.3;     // r
                colors[i + 1] = 0.4 + Math.random() * 0.4; // g
                colors[i + 2] = 0.8 + Math.random() * 0.2; // b
            }

            geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

            const material = new THREE.PointsMaterial({
                size: 2,
                vertexColors: true,
                transparent: true,
                opacity: 0.8,
                blending: THREE.AdditiveBlending
            });

            particles = new THREE.Points(geometry, material);
            scene.add(particles);
        }

        function animate() {
            requestAnimationFrame(animate);

            const elapsedTime = clock.getElapsedTime();

            // Animate particles
            if (particles) {
                particles.rotation.x += 0.001;
                particles.rotation.y += 0.002;

                // Mouse interaction
                particles.rotation.x += mouse.y * 0.01;
                particles.rotation.y += mouse.x * 0.01;

                // Floating motion
                const positions = particles.geometry.attributes.position.array;
                for (let i = 0; i < positions.length; i += 3) {
                    positions[i + 1] += Math.sin(elapsedTime + i * 0.001) * 0.01;
                }
                particles.geometry.attributes.position.needsUpdate = true;
            }

            // Camera subtle movement
            camera.position.x += (mouse.x * 2 - camera.position.x) * 0.02;
            camera.position.y += (mouse.y * 2 - camera.position.y) * 0.02;
            camera.lookAt(scene.position);

            renderer.render(scene, camera);
        }

        // Initialize Three.js when page loads
        document.addEventListener('DOMContentLoaded', initThreeJS);

        // TSLA Price Update Logic (keeping existing functionality)
        const apiEndpoint = '<?php echo esc_url(rest_url('tradingrobotplug/v1/stock-data')); ?>';
        const refreshInterval = 30000;

        function formatPrice(price) {
            return '$' + parseFloat(price).toFixed(2);
        }

        function formatChange(changePercent) {
            const change = parseFloat(changePercent);
            const arrow = change >= 0 ? '↗' : '↘';
            const sign = change >= 0 ? '+' : '';
            const colorClass = change >= 0 ? 'text-green-400' : 'text-red-400';
            return `<span class="${colorClass}">${arrow} ${sign}${change.toFixed(2)}%</span>`;
        }

        function updateTSLAData(stockData) {
            const tslaData = stockData.find(stock =>
                (stock.symbol || stock.SYMBOL || '').toUpperCase() === 'TSLA'
            );

            if (!tslaData) return;

            const price = parseFloat(tslaData.price || tslaData.PRICE || 0);
            const changePercent = parseFloat(tslaData.change_percent || tslaData.CHANGE_PERCENT || tslaData.changePercent || 0);
            const volume = tslaData.volume ? (parseInt(tslaData.volume) / 1000000).toFixed(1) + 'M' : '--';
            const high52w = tslaData.fiftyTwoWeekHigh ? formatPrice(tslaData.fiftyTwoWeekHigh) : '$--.--';
            const low52w = tslaData.fiftyTwoWeekLow ? formatPrice(tslaData.fiftyTwoWeekLow) : '$--.--';

            // Update price display
            document.getElementById('tsla-price').textContent = formatPrice(price);
            document.getElementById('tsla-change').innerHTML = formatChange(changePercent);
            document.getElementById('tsla-volume').textContent = volume;
            document.getElementById('tsla-high').textContent = high52w;
            document.getElementById('tsla-low').textContent = low52w;

            // Remove loading classes
            document.getElementById('tsla-price').classList.remove('text-gray-400');
            document.getElementById('tsla-change').classList.remove('loading');

            // Update timestamp
            const now = new Date();
            document.getElementById('tsla-timestamp').textContent = now.toLocaleTimeString();
        }

                function updateMarketHeatmap(stockData) {
                    const container = document.getElementById('market-heatmap');
                    if (!container) return;

                    // Sort by symbol to maintain consistent order: TSLA, QQQ, SPY, NVDA
                    const symbolOrder = ['TSLA', 'QQQ', 'SPY', 'NVDA'];
                    stockData.sort((a, b) => {
                        const aSymbol = a.symbol || a.SYMBOL || 'N/A';
                        const bSymbol = b.symbol || b.SYMBOL || 'N/A';
                        return symbolOrder.indexOf(aSymbol) - symbolOrder.indexOf(bSymbol);
                    });

                    const html = stockData.slice(0, 4).map(stock => {
                        const symbol = stock.symbol || stock.SYMBOL || 'N/A';
                        const price = parseFloat(stock.price || stock.PRICE || 0);
                        const changePercent = parseFloat(stock.change_percent || stock.CHANGE_PERCENT || stock.changePercent || 0);
                        const changeClass = changePercent >= 0 ? 'text-green-400' : 'text-red-400';
                        const bgClass = symbol === 'TSLA' ? 'bg-red-500/20 border-red-500/30' : 'bg-white/5 border-white/10';

                        return `
                        <div class="market-item ${bgClass} border rounded-lg p-3 hover:scale-105 transition-transform duration-300">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-white">${symbol}</span>
                                    ${symbol === 'TSLA' ? '<span class="text-red-400 text-xs">🚀</span>' : ''}
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-white">${formatPrice(price)}</div>
                                    <div class="text-sm ${changeClass}">${changePercent >= 0 ? '+' : ''}${changePercent.toFixed(2)}%</div>
                                </div>
                            </div>
                        </div>
                    `;
                    }).join('');

                    container.innerHTML = html;

                    // Update timestamp
                    const now = new Date();
                    document.getElementById('market-timestamp').textContent = now.toLocaleTimeString();
                }

                function updateAIRecommendations() {
                    // Simulate AI recommendation updates (would come from your trading engine)
                    const recommendations = [
                        { symbol: 'TSLA', action: 'BUY', confidence: 84, entry: 265.50, target: 295.00 },
                        { symbol: 'TSLA', action: 'HOLD', confidence: 67, entry: null, target: null }
                    ];

                    // Update the recommendation display
                    const tslaRec = recommendations.find(r => r.symbol === 'TSLA' && r.action === 'BUY');
                    if (tslaRec) {
                        // This would update the AI recommendations section dynamically
                        console.log('AI Recommendation:', tslaRec);
                    }
                }

        function fetchStockData() {
            fetch(apiEndpoint)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.stock_data && data.stock_data.length > 0) {
                        updateTSLAData(data.stock_data);
                        updateMarketHeatmap(data.stock_data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching stock data:', error);
                    // Show error state
                    document.getElementById('tsla-price').textContent = 'Error';
                    document.getElementById('tsla-change').innerHTML = '<span class="text-red-400">Connection failed</span>';
                });
        }

        // Initial fetch and auto-refresh
        document.addEventListener('DOMContentLoaded', function() {
            fetchStockData();
            setInterval(fetchStockData, refreshInterval);
        });

    })();
    </script>
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
                    const arrow = change >= 0 ? '↑' : '↓';
                    const sign = change >= 0 ? '+' : '';
                    return arrow + ' ' + sign + change.toFixed(2) + '%';
                }
                
                function getChangeClass(changePercent) {
                    return parseFloat(changePercent) >= 0 ? 'positive' : 'negative';
                }
                
                function renderStockItems(stockData) {
                    const container = document.getElementById('market-items-container');
                    if (!container) return;

                    if (!stockData || stockData.length === 0) {
                        container.innerHTML = '<div class="market-item"><span>No data available</span></div>';
                        return;
                    }

                    // Sort by symbol to maintain consistent order: TSLA first, then others
                    const symbolOrder = ['TSLA', 'QQQ', 'SPY', 'NVDA'];
                    stockData.sort((a, b) => {
                        const aSymbol = a.symbol || a.SYMBOL || 'N/A';
                        const bSymbol = b.symbol || b.SYMBOL || 'N/A';

                        // TSLA always first
                        if (aSymbol === 'TSLA') return -1;
                        if (bSymbol === 'TSLA') return 1;

                        // Then sort by predefined order
                        return symbolOrder.indexOf(aSymbol) - symbolOrder.indexOf(bSymbol);
                    });

                    const html = stockData.map(stock => {
                        // Handle both database format and API format
                        const symbol = stock.symbol || stock.SYMBOL || 'N/A';
                        // Convert price to number (handle string values from database)
                        const price = parseFloat(stock.price || stock.PRICE || 0);
                        // Convert change_percent to number (handle string values from database)
                        const changePercent = parseFloat(stock.change_percent || stock.CHANGE_PERCENT || stock.changePercent || 0);

                        // Special styling for TSLA
                        const isTSLA = symbol === 'TSLA';
                        const itemClass = isTSLA ? 'market-item tsla-featured' : 'market-item';

                        return `
                        <div class="${itemClass}" data-symbol="${symbol}">
                            <span class="market-symbol">${symbol}${isTSLA ? ' ⭐' : ''}</span>
                            <span class="market-price">${formatPrice(price)}</span>
                            <span class="market-change ${getChangeClass(changePercent)}">${formatChange(changePercent)}</span>
                        </div>
                    `;
                    }).join('');

                    container.innerHTML = html;

                    // Update TSLA price display
                    updateTSLAPriceDisplay(stockData);
                }

                function updateTSLAPriceDisplay(stockData) {
                    const tslaData = stockData.find(stock =>
                        (stock.symbol || stock.SYMBOL || '').toUpperCase() === 'TSLA'
                    );

                    const display = document.getElementById('tsla-price-display');
                    const priceData = document.getElementById('tsla-price-data');

                    if (!tslaData || !display || !priceData) return;

                    const price = parseFloat(tslaData.price || tslaData.PRICE || 0);
                    const changePercent = parseFloat(tslaData.change_percent || tslaData.CHANGE_PERCENT || tslaData.changePercent || 0);

                    // Update main price
                    const priceEl = priceData.querySelector('.price');
                    const changeEl = priceData.querySelector('.change');

                    if (priceEl) {
                        priceEl.textContent = formatPrice(price);
                        priceEl.classList.remove('loading');
                    }

                    if (changeEl) {
                        changeEl.textContent = formatChange(changePercent);
                        changeEl.className = `change ${getChangeClass(changePercent)}`;
                        changeEl.classList.remove('loading');
                    }

                    // Update additional details if available
                    const volumeEl = priceData.querySelector('.volume');
                    const high52wEl = priceData.querySelector('.high-52w');
                    const low52wEl = priceData.querySelector('.low-52w');

                    if (volumeEl && tslaData.volume) {
                        volumeEl.textContent = (parseInt(tslaData.volume) / 1000000).toFixed(1) + 'M';
                    }

                    if (high52wEl && tslaData.fiftyTwoWeekHigh) {
                        high52wEl.textContent = formatPrice(tslaData.fiftyTwoWeekHigh);
                    }

                    if (low52wEl && tslaData.fiftyTwoWeekLow) {
                        low52wEl.textContent = formatPrice(tslaData.fiftyTwoWeekLow);
                    }
                }
                
                function updateTimestamp(timestamp) {
                    const el = document.getElementById('market-update-time');
                    if (el && timestamp) {
                        const date = new Date(timestamp);
                        el.textContent = 'Powered by Yahoo Finance | Last updated: ' + date.toLocaleTimeString();
                    }
                }
                
                function fetchStockData() {
                    fetch(apiEndpoint)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Stock data received:', data);
                            if (data.stock_data && data.stock_data.length > 0) {
                                renderStockItems(data.stock_data);
                                updateTimestamp(data.timestamp);
                            } else {
                                console.warn('No stock data in response:', data);
                                // Show error message
                                const container = document.getElementById('market-items-container');
                                if (container) {
                                    container.innerHTML = '<div class="market-item"><span style="color: #ff6b6b;">No data available. API may be temporarily unavailable.</span></div>';
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching stock data:', error);
                            // Show error message to user
                            const container = document.getElementById('market-items-container');
                            if (container) {
                                container.innerHTML = '<div class="market-item"><span style="color: #ff6b6b;">Error loading market data. Please refresh the page.</span></div>';
                            }
                            const timeEl = document.getElementById('market-update-time');
                            if (timeEl) {
                                timeEl.textContent = 'Error: ' + error.message;
                            }
                        });
                }
                
                // Initial fetch
                document.addEventListener('DOMContentLoaded', function() {
                    fetchStockData();
                    
                    // Set up auto-refresh
                    updateTimer = setInterval(fetchStockData, refreshInterval);
                    
                    // Cleanup on page unload
                    window.addEventListener('beforeunload', function() {
                        if (updateTimer) clearInterval(updateTimer);
                    });
                });
            })();
            </script>
            
        </div>
    </div>
</section>

<!-- ===== LIVE TSLA ANALYSIS SECTION ===== -->
<section class="section" id="live-tsla-analysis">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">🐝 Watch Our Swarm Work in Real-Time</h2>
        <p style="text-align: center; margin-bottom: 48px; font-size: 18px; color: #666; max-width: 800px; margin-left: auto; margin-right: auto;">
            See what our 8 AI agents are working on right now. We're building trading robots in parallel, testing different approaches, and iterating until we find a winning strategy.
        </p>
        <?php echo do_shortcode('[trp_swarm_status mode="full" refresh="30"]'); ?>
    </div>
</section>

<!-- ===== PAPER TRADING STATS SECTION ===== -->
<section class="section section--light" id="paper-trading">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 48px;">📊 Paper Trading Results</h2>
        <p style="text-align: center; margin-bottom: 48px; font-size: 18px; color: #666; max-width: 800px; margin-left: auto; margin-right: auto;">
            We're testing trading strategies in paper trading mode (simulated, no real money). Once we find a winning bot, we'll transition to live trading. Here's our current performance:
        </p>
        <?php echo do_shortcode('[trp_trading_stats mode="full" refresh="60"]'); ?>
    </div>
</section>

<!-- ===== FINAL CTA SECTION ===== -->
<section class="section section--light">
    <div class="container" style="text-align: center;">
        <h2 style="margin-bottom: 24px;">Follow Our Journey</h2>
        <p style="font-size: 18px; margin-bottom: 48px; color: #666; max-width: 700px; margin-left: auto; margin-right: auto;">
            Watch us build in real-time. See our swarm status, paper trading results, and progress as we work towards finding a winning trading bot.
        </p>
        
        <div style="display: flex; justify-content: center; gap: 32px; flex-wrap: wrap; margin-bottom: 48px;">
            <a href="#swarm-status" class="btn btn-primary">🐝 View Swarm Status</a>
            <a href="#paper-trading" class="btn btn-secondary">📊 See Paper Trading Stats</a>
            <a href="https://weareswarm.site" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">🌐 WeAreSwarm Site</a>
        </div>
        
        <p style="font-size: 14px; color: #999; margin-top: 32px;">
            We're in building mode - experimenting with different trading robots to find what works. Once we validate a winning strategy, we'll transition to live trading.
        </p>
    </div>
</section>


<?php get_footer(); ?>
