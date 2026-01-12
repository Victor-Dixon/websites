<?php
/**
 * DigitalDreamscape - Specialized Animated Hero
 * Theme: Digital Art, Design & Creative Technology
 * Audience: Artists, Designers, Creative Professionals
 */
?>

<style>
/* DigitalDreamscape Theme - Creative & Artistic Colors */
@keyframes creativeSpark {
    0%, 100% { box-shadow: 0 0 20px rgba(147, 51, 234, 0.3); }
    50% { box-shadow: 0 0 40px rgba(147, 51, 234, 0.6), 0 0 60px rgba(147, 51, 234, 0.3); }
}

@keyframes artBrush {
    0% { transform: rotate(0deg) scale(1); }
    25% { transform: rotate(90deg) scale(1.1); }
    50% { transform: rotate(180deg) scale(0.9); }
    75% { transform: rotate(270deg) scale(1.1); }
    100% { transform: rotate(360deg) scale(1); }
}

@keyframes colorWave {
    0% { background-position: 0% 50%; }
    25% { background-position: 100% 50%; }
    50% { background-position: 100% 100%; }
    75% { background-position: 0% 100%; }
    100% { background-position: 0% 50%; }
}

@keyframes inspirationFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-12px) rotate(120deg); }
    66% { transform: translateY(-6px) rotate(240deg); }
}

@keyframes canvasDraw {
    0% { stroke-dashoffset: 1000; }
    100% { stroke-dashoffset: 0; }
}

.hero-creative-spark { animation: creativeSpark 2s ease-in-out infinite; }
.art-brush { animation: artBrush 4s ease-in-out infinite; }
.inspiration-float { animation: inspirationFloat 6s ease-in-out infinite; }
.canvas-draw { animation: canvasDraw 3s ease-in-out forwards; }

/* Creative-themed gradients */
.creative-gradient {
    background: linear-gradient(135deg, #0f0f0f 0%, #1a0a2e 25%, #9333ea 50%, #ec4899 75%, #0f0f0f 100%);
    background-size: 400% 400%;
    animation: colorWave 8s ease infinite;
}

/* Artistic canvas texture */
.canvas-texture {
    position: relative;
    background:
        radial-gradient(circle at 20% 50%, rgba(147, 51, 234, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
}

/* Creative symbols */
.creative-palette {
    position: relative;
    width: 40px;
    height: 40px;
    background: conic-gradient(from 0deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57, #ff9ff3);
    border-radius: 50%;
    border: 3px solid #1a0a2e;
}

.creative-palette::after {
    content: '';
    position: absolute;
    top: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 12px solid #1a0a2e;
}
</style>

<section class="relative min-h-screen flex items-center justify-center overflow-hidden canvas-texture creative-gradient">

    <!-- Artistic Floating Elements -->
    <div class="absolute top-24 left-24 inspiration-float">
        <div class="creative-palette"></div>
    </div>
    <div class="absolute top-36 right-32 inspiration-float" style="animation-delay: 1s;">
        <div class="text-4xl">🎨</div>
    </div>
    <div class="absolute bottom-40 left-32 inspiration-float" style="animation-delay: 2s;">
        <div class="text-3xl">✏️</div>
    </div>
    <div class="absolute bottom-32 right-24 inspiration-float" style="animation-delay: 0.5s;">
        <div class="text-3xl">🖼️</div>
    </div>

    <!-- Creative Brush Strokes Animation -->
    <div class="absolute top-40 left-10 right-10 opacity-20">
        <svg viewBox="0 0 800 60" class="w-full h-12">
            <path d="M50,30 Q150,10 250,40 T450,20 T650,50 T750,30"
                  fill="none"
                  stroke="url(#brushGradient)"
                  stroke-width="3"
                  stroke-linecap="round"
                  class="canvas-draw"/>
            <defs>
                <linearGradient id="brushGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#9333ea;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#ec4899;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:1" />
                </linearGradient>
            </defs>
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Creative Design Headline -->
        <div class="mb-8">
            <div class="inline-block px-4 py-2 bg-purple-500/20 border border-purple-500/40 rounded-full text-purple-400 text-sm font-semibold mb-6 animate-pulse">
                🎨 DIGITAL DREAMSCAPE STUDIO
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="bg-gradient-to-r from-purple-400 via-pink-500 to-blue-500 bg-clip-text text-transparent">
                    Digital
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-purple-400">
                        Dreamscape
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                Where imagination meets innovation. We craft digital experiences that inspire,
                engage, and bring creative visions to life through cutting-edge design and technology.
            </p>
        </div>

        <!-- Creative-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-white no-underline hero-creative-spark"
               href="<?php echo esc_url(home_url('/portfolio')); ?>" role="button">
                🎨 View Our Work
            </a>
            <a class="cta-button-enhanced px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="#creative-process" role="button">
                ✨ Start Your Project
            </a>
        </div>

        <!-- Creative Services Showcase -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">🚀 Creative Services</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center bg-gradient-to-br from-purple-900/50 to-pink-900/50 p-6 rounded-lg border border-purple-500/30 hover:border-purple-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl mb-3 art-brush">🎨</div>
                    <div class="font-bold text-white text-lg mb-2">Digital Art</div>
                    <div class="text-sm text-gray-400">Custom illustrations, concept art, and digital paintings</div>
                </div>
                <div class="text-center bg-gradient-to-br from-pink-900/50 to-red-900/50 p-6 rounded-lg border border-pink-500/30 hover:border-pink-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl mb-3 art-brush" style="animation-delay: 0.5s;">💻</div>
                    <div class="font-bold text-white text-lg mb-2">UI/UX Design</div>
                    <div class="text-sm text-gray-400">Intuitive interfaces and user experiences that delight</div>
                </div>
                <div class="text-center bg-gradient-to-br from-blue-900/50 to-purple-900/50 p-6 rounded-lg border border-blue-500/30 hover:border-blue-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl mb-3 art-brush" style="animation-delay: 1s;">🎬</div>
                    <div class="font-bold text-white text-lg mb-2">Motion Graphics</div>
                    <div class="text-sm text-gray-400">Animated content and visual storytelling</div>
                </div>
            </div>
        </div>

        <!-- Creative Stats -->
        <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-3xl mx-auto border border-white/10">
            <h4 class="text-xl font-semibold text-white mb-6 flex items-center justify-center">
                <span class="mr-2">📊</span> Creative Impact
            </h4>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-2 inspiration-float">500+</div>
                    <div class="text-sm text-gray-400">Projects Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-pink-400 mb-2 inspiration-float" style="animation-delay: 0.5s;">98%</div>
                    <div class="text-sm text-gray-400">Client Satisfaction</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-2 inspiration-float" style="animation-delay: 1s;">50+</div>
                    <div class="text-sm text-gray-400">Happy Clients</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-400 mb-2 inspiration-float" style="animation-delay: 1.5s;">24/7</div>
                    <div class="text-sm text-gray-400">Creative Support</div>
                </div>
            </div>

            <!-- Creative Inspiration Quote -->
            <div class="mt-8 p-4 bg-gradient-to-r from-purple-900/50 to-pink-900/50 rounded-lg border border-purple-500/30">
                <blockquote class="text-white italic text-center">
                    "Creativity is intelligence having fun."
                </blockquote>
                <cite class="text-purple-300 text-sm mt-2 block text-right">- Albert Einstein</cite>
            </div>
        </div>
    </div>

    <!-- Artistic Flow Animation -->
    <div class="absolute bottom-32 left-10 right-10 opacity-30">
        <svg viewBox="0 0 800 80" class="w-full h-16">
            <defs>
                <linearGradient id="artisticFlow" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#9333ea;stop-opacity:1" />
                    <stop offset="33%" style="stop-color:#ec4899;stop-opacity:1" />
                    <stop offset="66%" style="stop-color:#3b82f6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#10b981;stop-opacity:1" />
                </linearGradient>
            </defs>
            <!-- Flowing artistic curves -->
            <path d="M0,40 Q100,20 200,50 T400,30 T600,60 T800,40"
                  fill="none"
                  stroke="url(#artisticFlow)"
                  stroke-width="2"
                  opacity="0.6"
                  class="animate-pulse"/>
            <path d="M0,50 Q120,30 240,60 T440,40 T640,70 T800,50"
                  fill="none"
                  stroke="url(#artisticFlow)"
                  stroke-width="1"
                  opacity="0.4"
                  class="animate-pulse"
                  style="animation-delay: 1s;"/>
        </svg>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>