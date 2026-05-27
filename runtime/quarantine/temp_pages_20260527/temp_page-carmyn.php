<?php
/**
 * Template Name: Carmyn Page
 * Template for Carmyn's dedicated showcase page
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<style>
    /* Carmyn's Page - Merged with Aria's Purple Theme */
    /* Color Palette: #a855f7 (primary purple), #8A2BE2 (electric purple), #7B1FA2 (deep neon purple), #000000 (black), #0A0012 (deep night purple-black), #fcd34d (gold accent) */
    body {
        background: linear-gradient(135deg, #0A0012 0%, #000000 50%, #0A0012 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Purple neon curls decoration */
    .neon-curl {
        position: fixed;
        border: 3px solid #a855f7;
        border-radius: 50%;
        box-shadow: 0 0 20px #a855f7, 0 0 40px #a855f7, 0 0 60px rgba(168, 85, 247, 0.5);
        opacity: 0.8;
        animation: curl-float 6s ease-in-out infinite;
        z-index: 0;
        pointer-events: none;
    }

    .curl-1 {
        width: 200px;
        height: 200px;
        top: 10%;
        left: -50px;
        border-width: 4px;
    }

    .curl-2 {
        width: 150px;
        height: 150px;
        top: 30%;
        right: -30px;
        border-width: 3px;
        animation-delay: 2s;
    }

    .curl-3 {
        width: 180px;
        height: 180px;
        bottom: 20%;
        left: -40px;
        border-width: 3px;
        animation-delay: 4s;
    }

    .curl-4 {
        width: 120px;
        height: 120px;
        bottom: 10%;
        right: -20px;
        border-width: 2px;
        animation-delay: 1s;
    }

    .curl-5 {
        width: 100px;
        height: 100px;
        top: 50%;
        left: 5%;
        border-width: 2px;
        animation-delay: 3s;
    }

    .curl-6 {
        width: 160px;
        height: 160px;
        top: 60%;
        right: 10%;
        border-width: 3px;
        animation-delay: 5s;
    }

    @keyframes curl-float {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.8;
        }
        50% {
            transform: translate(20px, -20px) rotate(180deg);
            opacity: 0.6;
        }
    }

    /* Additional curly SVG paths */
    .curl-svg {
        position: fixed;
        pointer-events: none;
        z-index: 0;
    }

    .curl-path {
        stroke: #a855f7;
        stroke-width: 3;
        fill: none;
        filter: drop-shadow(0 0 10px #a855f7) drop-shadow(0 0 20px rgba(168, 85, 247, 0.8));
        opacity: 0.7;
    }

    .carmyn-section {
        background: rgba(10, 0, 18, 0.8);
        backdrop-filter: blur(10px);
        border: 3px solid #a855f7;
        box-shadow: 0 0 30px rgba(168, 85, 247, 0.3), inset 0 0 20px rgba(168, 85, 247, 0.1);
        position: relative;
        z-index: 1;
    }

    .carmyn-title {
        color: #a855f7;
        text-shadow: 0 0 10px #a855f7, 0 0 20px #a855f7, 0 0 30px rgba(168, 85, 247, 0.8);
        animation: carmyn-glow 2s ease-in-out infinite;
    }

    @keyframes carmyn-glow {
        0%, 100% {
            text-shadow: 0 0 10px #a855f7, 0 0 20px #a855f7, 0 0 30px rgba(168, 85, 247, 0.8);
        }
        50% {
            text-shadow: 0 0 20px #a855f7, 0 0 40px #a855f7, 0 0 60px rgba(168, 85, 247, 1);
        }
    }

    .carmyn-text {
        color: #ffffff;
        text-shadow: 0 0 5px #a855f7, 0 0 10px rgba(168, 85, 247, 0.5);
    }

    .carmyn-music-badge {
        background: rgba(168, 85, 247, 0.2);
        border: 2px solid #a855f7;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 20px;
        display: inline-block;
        margin: 5px;
        box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
        text-shadow: 0 0 5px #a855f7;
    }

    .carmyn-learning-badge {
        background: rgba(252, 211, 77, 0.2);
        border: 2px solid #fcd34d;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 20px;
        display: inline-block;
        margin: 5px;
        box-shadow: 0 0 10px rgba(252, 211, 77, 0.3);
        text-shadow: 0 0 5px #fcd34d;
    }

    /* Ensure content is above curls */
    header, main, footer {
        position: relative;
        z-index: 1;
    }
</style>

<!-- Purple neon curls decoration -->
<div class="neon-curl curl-1"></div>
<div class="neon-curl curl-2"></div>
<div class="neon-curl curl-3"></div>
<div class="neon-curl curl-4"></div>
<div class="neon-curl curl-5"></div>
<div class="neon-curl curl-6"></div>

<!-- SVG curly paths -->
<svg class="curl-svg" style="top: 15%; left: 0; width: 300px; height: 200px;">
    <path class="curl-path" d="M 0,100 Q 50,50 100,100 T 200,100 T 300,100" />
</svg>
<svg class="curl-svg" style="bottom: 15%; right: 0; width: 250px; height: 180px;">
    <path class="curl-path" d="M 250,90 Q 200,40 150,90 T 50,90 T 0,90" />
</svg>
<svg class="curl-svg" style="top: 40%; right: 5%; width: 180px; height: 150px;">
    <path class="curl-path" d="M 0,75 Q 45,25 90,75 T 180,75" />
</svg>
<svg class="curl-svg" style="bottom: 30%; left: 5%; width: 200px; height: 120px;">
    <path class="curl-path" d="M 200,60 Q 150,10 100,60 T 0,60" />
</svg>

<main>
    <!-- Hero Section -->
    <section id="home" class="hero carmyn-section">
        <div class="container">
            <h1 class="graffiti-title carmyn-title">
                <span class="graffiti">CARMYN</span>
            </h1>
            <p class="tagline carmyn-text">Featured DJ</p>
        </div>
    </section>

    <!-- Music Section -->
    <section id="music" class="screw-tapes-library carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">CARMYN</span>
                <span class="bubble-sub">MUSIC</span>
            </h2>
            <div class="carmyn-text" style="text-align: center; margin-top: 30px;">
                <p style="font-size: 1.3em; margin-bottom: 20px;">Music Styles</p>
                <div style="margin: 20px 0;">
                    <span class="carmyn-music-badge">🎵 R&B</span>
                    <span class="carmyn-music-badge">💃 Dance</span>
                    <span class="carmyn-music-badge">🎷 Jazz</span>
                </div>
                <span class="carmyn-learning-badge">🌟 Still Learning - Help me get better!</span>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">ABOUT</span>
                <span class="bubble-sub">CARMYN</span>
            </h2>
            <div class="about-content">
                <div class="carmyn-text" style="text-align: center; max-width: 800px; margin: 0 auto;">
                    <p style="font-size: 1.2em; margin-bottom: 20px;">
                        Welcome to <strong style="color: #a855f7; text-shadow: 0 0 10px #a855f7;">Carmyn's</strong> space on SouthWest Secret!
                    </p>
                    <p style="font-size: 1.1em; margin-bottom: 20px;">
                        I'm a DJ specializing in <strong style="color: #a855f7; text-shadow: 0 0 5px #a855f7;">R&B</strong>, <strong style="color: #a855f7; text-shadow: 0 0 5px #a855f7;">Dance</strong>, and <strong style="color: #a855f7; text-shadow: 0 0 5px #a855f7;">Jazz</strong> music. 
                        I'm still learning and growing as an artist, so I'd love your help getting better!
                    </p>
                    <p style="font-size: 1.1em; margin-top: 30px; color: #ffffff; text-shadow: 0 0 5px #a855f7;">
                        Check back soon for mixes and updates!
                    </p>
                </div>
                <div class="website-link" style="margin-top: 30px; text-align: center;">
                    <a href="<?php echo home_url(); ?>" class="btn-subscribe" style="display: inline-block; background: rgba(168, 85, 247, 0.2); border: 2px solid #a855f7; color: #ffffff; text-shadow: 0 0 5px #a855f7; box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);">
                        Visit SouthWest Secret
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact carmyn-section">
        <div class="container">
            <h2 class="section-title carmyn-title">
                <span class="graffiti-sub">STAY</span>
                <span class="bubble-sub">CONNECTED</span>
            </h2>
            <div class="contact-content">
                <p class="carmyn-text">Subscribe to SouthWest Secret for the latest mixes!</p>
                <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="btn-subscribe" style="background: rgba(168, 85, 247, 0.2); border: 2px solid #a855f7; color: #ffffff; text-shadow: 0 0 5px #a855f7; box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);">
                    Subscribe Now
                </a>
            </div>
        </div>
    </section>
</main>

<!-- Music Player Controls -->
<div id="musicControls" style="position:fixed; bottom:20px; left:20px; z-index:9999;">
    <button id="playBtn" style="background: rgba(168, 85, 247, 0.2); border: 2px solid #a855f7; color: #ffffff; padding: 10px 20px; border-radius: 25px; cursor: pointer; text-shadow: 0 0 5px #a855f7; box-shadow: 0 0 10px rgba(168, 85, 247, 0.3); font-size: 16px; font-weight: bold;">Play Lofi Music</button>
</div>

<!-- Hidden YouTube Player -->
<div id="ytplayer" style="width:1px; height:1px; position:absolute; left:-9999px;"></div>

<script>
    var tag = document.createElement("script");
    tag.src = "https://www.youtube.com/iframe_api";
    document.head.appendChild(tag);

    var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player("ytplayer", {
            videoId: "sF80I-TQiW0",
            playerVars: { autoplay: 1, mute: 1, loop: 1, playlist: "sF80I-TQiW0" }
        });
    }

    document.addEventListener("DOMContentLoaded", function(){
        document.getElementById("playBtn").onclick = function(){
            player.unMute();
            player.playVideo();
            this.textContent = "Pause Music";
            this.onclick = function(){
                if(player.getPlayerState() === 1){
                    player.pauseVideo();
                    this.textContent = "Play Lofi Music";
                } else {
                    player.playVideo();
                    player.unMute();
                    this.textContent = "Pause Music";
                }
            };
        };
    });
</script>

<?php get_footer(); ?>

