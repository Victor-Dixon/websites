<?php
/**
 * Hero Section Template Part for Crosby Ultimate Events Theme
 * Displays the animated ultimate frisbee hero section
 */
?>

<style>
/* Crosby Ultimate Events Theme - Ultimate Frisbee Hero */
@keyframes frisbeeThrow {
    0% { transform: translateX(-20px) rotate(0deg); }
    50% { transform: translateX(50px) translateY(-30px) rotate(180deg); }
    100% { transform: translateX(120px) translateY(0px) rotate(360deg); }
}

@keyframes crowdWave {
    0%, 100% { transform: translateY(0px); }
    25% { transform: translateY(-5px); }
    50% { transform: translateY(0px); }
    75% { transform: translateY(5px); }
}

@keyframes fieldGlow {
    0%, 100% { box-shadow: 0 0 20px rgba(82, 183, 136, 0.3); }
    50% { box-shadow: 0 0 40px rgba(82, 183, 136, 0.6), 0 0 60px rgba(82, 183, 136, 0.3); }
}

@keyframes communityConnect {
    0% { opacity: 0.3; transform: scale(0.8); }
    50% { opacity: 1; transform: scale(1.1); }
    100% { opacity: 0.3; transform: scale(0.8); }
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

.hero-field-glow { animation: fieldGlow 2s ease-in-out infinite; }
.frisbee-throw { animation: frisbeeThrow 3s ease-in-out infinite; }
.crowd-wave { animation: crowdWave 2s ease-in-out infinite; }
.community-connect { animation: communityConnect 4s ease-in-out infinite; }
.fade-in-up { animation: fadeInUp 0.6s ease-out; }

/* Ultimate frisbee-themed gradients */
.events-gradient {
    background: linear-gradient(135deg, #1a472a 0%, #2d6a4f 25%, #52b788 50%, #74c69d 75%, #1a472a 100%);
    background-size: 400% 400%;
    animation: gradient-shift 12s ease infinite;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Ultimate field markings (simplified) */
.field-markings {
    position: relative;
    background: repeating-linear-gradient(
        0deg,
        transparent,
        transparent 38px,
        rgba(255, 255, 255, 0.05) 38px,
        rgba(255, 255, 255, 0.05) 42px
    );
}

.field-markings::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-50%);
}

/* Frisbee disc */
.frisbee {
    width: 40px;
    height: 40px;
    background: radial-gradient(circle at 30% 30%, #fff, #52b788);
    border-radius: 50%;
    position: relative;
    border: 2px solid #2d6a4f;
}

.frisbee::before {
    content: '';
    position: absolute;
    top: 8px;
    left: 8px;
    width: 24px;
    height: 24px;
    background: radial-gradient(circle at 40% 40%, #52b788, #1a472a);
    border-radius: 50%;
}

.cta-button-enhanced.primary {
    background: linear-gradient(135deg, #52b788, #74c69d);
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
    box-shadow: 0 10px 25px rgba(82, 183, 136, 0.3);
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
    border-right: 4px solid #52b788;
    white-space: nowrap;
    animation: typewriter 2s steps(40, end), blink-caret 0.75s step-end infinite;
}

@keyframes blink-caret {
    from, to { border-color: transparent; }
    50% { border-color: #52b788; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-events h1 {
        font-size: 3rem !important;
    }

    .hero-events p {
        font-size: 1.25rem !important;
    }

    .frisbee {
        width: 32px;
        height: 32px;
    }
}
</style>

<section class="hero-events relative min-h-screen flex items-center justify-center overflow-hidden field-markings events-gradient">

    <!-- Ultimate Field Elements -->
    <div class="absolute inset-0 opacity-20">
        <!-- Field boundaries -->
        <div class="absolute top-20 left-20 right-20 h-1 bg-white/30"></div>
        <div class="absolute bottom-20 left-20 right-20 h-1 bg-white/30"></div>
        <div class="absolute left-20 top-20 bottom-20 w-1 bg-white/30"></div>
        <div class="absolute right-20 top-20 bottom-20 w-1 bg-white/30"></div>

        <!-- Center line -->
        <div class="absolute top-1/2 left-20 right-20 h-0.5 bg-white/50"></div>
    </div>

    <!-- Floating Frisbees and Players -->
    <div class="absolute top-32 left-32 frisbee-throw">
        <div class="frisbee"></div>
    </div>
    <div class="absolute top-40 right-40 frisbee-throw" style="animation-delay: 1s;">
        <div class="frisbee"></div>
    </div>
    <div class="absolute bottom-40 left-40 frisbee-throw" style="animation-delay: 2s;">
        <div class="frisbee"></div>
    </div>

    <!-- Crowd elements -->
    <div class="absolute top-24 left-16 crowd-wave">👥</div>
    <div class="absolute top-28 right-24 crowd-wave" style="animation-delay: 0.5s;">🏃‍♂️</div>
    <div class="absolute bottom-32 left-28 crowd-wave" style="animation-delay: 1s;">🤾‍♀️</div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <!-- Ultimate Frisbee Headline -->
        <div class="mb-8">

            <div class="inline-block px-4 py-2 bg-green-500/20 border border-green-500/40 rounded-full text-green-400 text-sm font-semibold mb-6 animate-pulse">
                🥏 ULTIMATE FRISBEE EVENTS
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6">
                <span class="bg-gradient-to-r from-green-400 via-blue-500 to-teal-500 bg-clip-text text-transparent">
                    Crosby Ultimate
                </span>
                <br>
                <span class="relative inline-block text-white">
                    <span class="hero-typewriter overflow-hidden whitespace-nowrap border-r-4 border-green-400">
                        Events
                    </span>
                </span>
            </h1>

            <p class="text-xl md:text-2xl lg:text-3xl text-gray-300 mb-8 hero-fade-in-up max-w-4xl mx-auto leading-relaxed">
                Where passion meets precision. Join our community of ultimate frisbee players
                for tournaments, clinics, and events that bring the spirit of the game to life.
            </p>
        </div>

        <!-- Event-Focused CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center mb-12 hero-fade-in-up">
            <a class="cta-button-enhanced primary px-8 py-4 rounded-full font-semibold text-white no-underline hero-field-glow"
               href="<?php echo esc_url(home_url('/events')); ?>" role="button">
                🥏 View Upcoming Events
            </a>
            <a class="cta-button-enhanced secondary px-8 py-4 border-2 border-white/30 rounded-full font-semibold text-lg hover:bg-white/10 hover:border-white/60 transition-all duration-300 text-white no-underline"
               href="#join-community" role="button">
                👥 Join Our Community
            </a>
        </div>

        <!-- Event Statistics -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 hero-fade-in-up max-w-4xl mx-auto border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">🏆 Community Impact</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2 community-connect">200+</div>
                    <div class="text-sm text-gray-400">Active Players</div>
                    <div class="text-xs text-green-300 mt-1">Growing Community</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-400 mb-2 community-connect">15</div>
                    <div class="text-sm text-gray-400">Tournaments/Year</div>
                    <div class="text-xs text-blue-300 mt-1">Major Events</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-teal-400 mb-2 community-connect">50+</div>
                    <div class="text-sm text-gray-400">Teams Supported</div>
                    <div class="text-xs text-teal-300 mt-1">All Skill Levels</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-yellow-400 mb-2 community-connect">10</div>
                    <div class="text-sm text-gray-400">Years Running</div>
                    <div class="text-xs text-yellow-300 mt-1">Established Legacy</div>
                </div>
            </div>
        </div>

        <!-- Featured Events -->
        <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-6 hero-fade-in-up max-w-4xl mx-auto border border-white/10">
            <h4 class="text-xl font-semibold text-white mb-6 flex items-center justify-center">
                <span class="mr-2">📅</span> Featured Events
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-green-900/50 to-blue-900/50 p-4 rounded-lg border border-green-500/30 hover:border-green-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-lg font-bold text-white mb-2">Spring Championship</div>
                    <div class="text-sm text-green-300 mb-1">🥏 Tournament</div>
                    <div class="text-xs text-gray-400">April 15-17, 2026</div>
                    <div class="text-xs text-yellow-400 mt-2">32 Teams Registered</div>
                </div>

                <div class="bg-gradient-to-br from-blue-900/50 to-teal-900/50 p-4 rounded-lg border border-blue-500/30 hover:border-blue-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-lg font-bold text-white mb-2">Beginner Clinic</div>
                    <div class="text-sm text-blue-300 mb-1">🎓 Training</div>
                    <div class="text-xs text-gray-400">Every Saturday</div>
                    <div class="text-xs text-yellow-400 mt-2">Learn the Fundamentals</div>
                </div>

                <div class="bg-gradient-to-br from-teal-900/50 to-green-900/50 p-4 rounded-lg border border-teal-500/30 hover:border-teal-400/50 transition-all duration-300 transform hover:scale-105">
                    <div class="text-lg font-bold text-white mb-2">Community Pickup</div>
                    <div class="text-sm text-teal-300 mb-1">🤝 Social</div>
                    <div class="text-xs text-gray-400">Wednesdays 6-8 PM</div>
                    <div class="text-xs text-yellow-400 mt-2">All Welcome!</div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="#all-events" class="text-green-400 hover:text-green-300 text-sm transition-colors duration-300">
                    View all events →
                </a>
            </div>
        </div>
    </div>

    <!-- Frisbee flight path animation -->
    <div class="absolute bottom-40 left-10 right-10 opacity-30">
        <svg viewBox="0 0 800 100" class="w-full h-20">
            <defs>
                <linearGradient id="frisbeePath" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#52b788;stop-opacity:0.8" />
                    <stop offset="50%" style="stop-color:#74c69d;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#52b788;stop-opacity:0.8" />
                </linearGradient>
            </defs>
            <path d="M50,80 Q200,20 400,60 T700,30"
                  fill="none"
                  stroke="url(#frisbeePath)"
                  stroke-width="2"
                  stroke-dasharray="10,5"
                  class="animate-pulse"/>
            <!-- Animated frisbee along path -->
            <circle r="4" fill="#52b788" class="frisbee-throw">
                <animateMotion dur="4s" repeatCount="indefinite">
                    <path d="M50,80 Q200,20 400,60 T700,30"/>
                </animateMotion>
            </circle>
        </svg>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>