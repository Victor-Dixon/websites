<?php
/**
 * AriaJet - Specialized Animated Hero
 * Theme: 2D Gaming & Interactive Entertainment
 * Audience: Gamers, Game Developers, Entertainment Seekers
 */
?>

<style>
/* AriaJet Gaming Theme - Neon Gaming Colors */
@keyframes gameGlow {
    0%, 100% { box-shadow: 0 0 20px rgba(255, 0, 255, 0.3); }
    50% { box-shadow: 0 0 40px rgba(255, 0, 255, 0.6), 0 0 60px rgba(255, 0, 255, 0.3); }
}

@keyframes pixelFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-8px) rotate(5deg); }
    50% { transform: translateY(-15px) rotate(0deg); }
    75% { transform: translateY(-8px) rotate(-5deg); }
}

@keyframes gameParticles {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 1; }
    100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
}

@keyframes retroScan {
    0% { transform: translateX(-100%); }
    50% { transform: translateX(100%); }
    100% { transform: translateX(100%); }
}

.hero-game-glow { animation: gameGlow 2s ease-in-out infinite; }
.pixel-float { animation: pixelFloat 3s ease-in-out infinite; }
.game-particles { animation: gameParticles 5s linear infinite; }
.retro-scan { animation: retroScan 4s ease-in-out infinite; }

/* Gaming-themed gradients */
.gaming-gradient {
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #ff00ff 50%, #00ffff 75%, #0f0f23 100%);
    background-size: 400% 400%;
    animation: gradient-shift 8s ease infinite;
}

/* Retro gaming elements */
.retro-grid {
    background-image:
        linear-gradient(rgba(255, 0, 255, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 255, 255, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

/* Game character sprites (simplified) */
.game-sprite {
    width: 32px;
    height: 32px;
    background: #ff00ff;
    border-radius: 4px;
    position: relative;
}

.game-sprite::before {
    content: '';
    position: absolute;
    top: 8px;
    left: 8px;
    width: 16px;
    height: 16px;
    background: #00ffff;
    border-radius: 2px;
}
</style>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden retro-grid gaming-gradient">

    <!-- CRT Scan Line Effect -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent retro-scan opacity-60"></div>
    </div>

    <!-- Floating Game Elements -->
    <div class="absolute top-24 left-24 pixel-float">
        <div class="game-sprite"></div>
    </div>
    <div class="absolute top-36 right-32 pixel-float" style="animation-delay: 0.5s;">
        <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-500 rounded transform rotate-45"></div>
    </div>
    <div class="absolute bottom-40 left-32 pixel-float" style="animation-delay: 1s;">
        <div class="text-4xl">🎮</div>
    </div>
    <div class="absolute bottom-32 right-24 pixel-float" style="animation-delay: 1.5s;">
        <div class="text-3xl">🕹️</div>
    </div>

    <!-- Particle Effects -->
    <div class="game-particles absolute w-2 h-2 bg-pink-400 rounded-full top-full left-1/4"></div>
    <div class="game-particles absolute w-3 h-3 bg-cyan-400 rounded-full top-full left-1/2" style="animation-delay: 1s;"></div>
    <div class="game-particles absolute w-2 h-2 bg-purple-400 rounded-full top-full left-3/4" style="animation-delay: 2s;"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Gaming Headline with Retro Feel -->
        <div class="mb-8">
            <div class="inline-block px-4 py-2 bg-pink-500/20 border border-pink-500/40 rounded text-pink-400 text-sm font-semibold mb-6 animate-pulse font-mono">
                >>> 2D GAMES LOADING...
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6 font-mono">
                <span class="bg-gradient-to-r from-pink-400 via-cyan-500 to-purple-500 bg-clip-text text-transparent">
                    AriaJet
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-pink-400">
                        Gaming Studio
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed font-mono">
                Crafting pixel-perfect 2D games that bring retro gaming magic to modern screens.
                Play, create, and explore in our universe of interactive entertainment.
            </p>
        </div>

        <!-- Gaming-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-none font-semibold text-white no-underline hero-game-glow font-mono border-2 border-pink-400"
               href="<?php echo esc_url(home_url('/games')); ?>" role="button">
                > PLAY NOW
            </a>
            <a class="cta-button-enhanced px-8 py-4 border-2 border-cyan-400/50 rounded-none font-semibold text-lg hover:bg-cyan-400/10 hover:border-cyan-400 transition-all duration-300 text-white no-underline font-mono"
               href="#game-dev" role="button">
                > LEARN TO CODE GAMES
            </a>
        </div>

        <!-- Game Showcase -->
        <div class="bg-black/40 backdrop-blur-lg rounded-none p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border-2 border-pink-500/30 font-mono">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">🎮 FEATURED GAMES</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center bg-gradient-to-b from-pink-900/50 to-purple-900/50 p-4 rounded-none border border-pink-500/30">
                    <div class="text-4xl mb-2">🚀</div>
                    <div class="font-bold text-white text-lg">Space Explorer</div>
                    <div class="text-sm text-cyan-400">2D Adventure</div>
                    <div class="text-xs text-gray-400 mt-2">★★★★★ (4.8)</div>
                </div>
                <div class="text-center bg-gradient-to-b from-cyan-900/50 to-blue-900/50 p-4 rounded-none border border-cyan-500/30">
                    <div class="text-4xl mb-2">🧩</div>
                    <div class="font-bold text-white text-lg">Puzzle Master</div>
                    <div class="text-sm text-pink-400">Brain Teaser</div>
                    <div class="text-xs text-gray-400 mt-2">★★★★☆ (4.6)</div>
                </div>
                <div class="text-center bg-gradient-to-b from-purple-900/50 to-pink-900/50 p-4 rounded-none border border-purple-500/30">
                    <div class="text-4xl mb-2">⚔️</div>
                    <div class="font-bold text-white text-lg">Retro Quest</div>
                    <div class="text-sm text-yellow-400">RPG Adventure</div>
                    <div class="text-xs text-gray-400 mt-2">★★★★★ (4.9)</div>
                </div>
            </div>
        </div>

        <!-- Gaming Stats -->
        <div class="bg-white/5 backdrop-blur-lg rounded-none p-6 hero-fade-in-up max-w-2xl mx-auto border-2 border-cyan-400/30 font-mono">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center justify-center">
                <span class="mr-2 text-pink-400">📊</span> GAME STATS
            </h4>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-pink-400 pixel-float">15</div>
                    <div class="text-xs text-gray-400">GAMES RELEASED</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-cyan-400 pixel-float" style="animation-delay: 0.5s;">50K+</div>
                    <div class="text-xs text-gray-400">PLAYERS</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-400 pixel-float" style="animation-delay: 1s;">4.7★</div>
                    <div class="text-xs text-gray-400">AVG RATING</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-yellow-400 pixel-float" style="animation-delay: 1.5s;">24/7</div>
                    <div class="text-xs text-gray-400">SERVERS UP</div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <div class="inline-block bg-black/50 px-4 py-2 rounded-none border border-pink-500/30">
                    <span class="text-cyan-400 font-mono text-sm">SYSTEM STATUS: ONLINE</span>
                    <span class="inline-block w-2 h-2 bg-green-400 rounded-full ml-2 animate-pulse"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Retro Gaming Grid Overlay -->
    <div class="absolute inset-0 pointer-events-none opacity-5">
        <div class="absolute inset-0 retro-grid"></div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-8 h-8 border-2 border-pink-400 border-t-transparent rounded-none transform rotate-45 animate-spin" style="animation-duration: 2s;"></div>
    </div>
</section>