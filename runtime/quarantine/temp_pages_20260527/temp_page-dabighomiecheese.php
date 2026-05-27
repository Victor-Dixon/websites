<?php
/**
 * Template Name: DabigHomieCheese Page
 * Template for DabigHomieCheese's dedicated showcase page
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<style>
    /* DabigHomieCheese Page - Merged with Aria's Purple Theme */
    /* Color Palette: #a855f7 (primary purple), #8A2BE2 (electric purple), #7B1FA2 (deep neon purple), #000000 (black), #0A0012 (deep night purple-black), #fcd34d (gold accent) */
    body {
        background: linear-gradient(135deg, #0A0012 0%, #000000 50%, #0A0012 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    .homie-page {
        padding-top: 80px;
    }

    /* Hero Section */
    .homie-hero {
        background: linear-gradient(135deg, #0A0012 0%, #000000 50%, #0A0012 100%);
        padding: 6rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-bottom: 3px solid #a855f7;
        box-shadow: 0 0 30px rgba(168, 85, 247, 0.5);
    }

    .homie-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(ellipse at center, rgba(168, 85, 247, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .homie-title {
        font-family: 'Permanent Marker', cursive;
        font-size: clamp(3rem, 15vw, 8rem);
        color: #a855f7;
        text-shadow: 0 0 20px #a855f7, 0 0 40px #a855f7, 0 0 60px rgba(168, 85, 247, 0.8);
        margin: 0;
        animation: homie-glow 3s ease-in-out infinite;
        position: relative;
        z-index: 1;
        letter-spacing: 5px;
    }

    @keyframes homie-glow {
        0%, 100% {
            text-shadow: 0 0 20px #a855f7, 0 0 40px #a855f7, 0 0 60px rgba(168, 85, 247, 0.8);
        }
        50% {
            text-shadow: 0 0 30px #a855f7, 0 0 60px #a855f7, 0 0 90px rgba(168, 85, 247, 1);
        }
    }

    .homie-subtitle {
        font-size: clamp(1.2rem, 3vw, 2rem);
        color: #ffffff;
        margin-top: 1rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    /* Music Player Section */
    .homie-music-section {
        background: #000000;
        padding: 4rem 2rem;
        border-top: 2px solid #a855f7;
        border-bottom: 2px solid #a855f7;
        box-shadow: 0 0 40px rgba(168, 85, 247, 0.3);
    }

    .music-player-container {
        max-width: 800px;
        margin: 0 auto;
        background: linear-gradient(135deg, #0A0012 0%, #000000 100%);
        border: 2px solid #a855f7;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 0 30px rgba(168, 85, 247, 0.5);
    }

    .audio-player {
        width: 100%;
        margin: 2rem 0;
        background: rgba(168, 85, 247, 0.1);
        border-radius: 10px;
        padding: 1rem;
    }

    .audio-player audio {
        width: 100%;
        outline: none;
    }

    .audio-player audio::-webkit-media-controls-panel {
        background-color: rgba(168, 85, 247, 0.2);
    }

    .play-button {
        background: linear-gradient(135deg, #a855f7 0%, #7B1FA2 100%);
        border: 2px solid #a855f7;
        color: #ffffff;
        padding: 15px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        text-shadow: 0 0 10px #a855f7;
        box-shadow: 0 0 20px rgba(168, 85, 247, 0.5);
        transition: all 0.3s ease;
        display: block;
        margin: 2rem auto;
    }

    .play-button:hover {
        background: linear-gradient(135deg, #7B1FA2 0%, #a855f7 100%);
        box-shadow: 0 0 30px rgba(168, 85, 247, 0.8);
        transform: translateY(-2px);
    }

    .homie-section {
        background: rgba(10, 0, 18, 0.8);
        backdrop-filter: blur(10px);
        border: 3px solid #a855f7;
        box-shadow: 0 0 30px rgba(168, 85, 247, 0.3), inset 0 0 20px rgba(168, 85, 247, 0.1);
        position: relative;
        z-index: 1;
        padding: 3rem 2rem;
        margin: 2rem auto;
        max-width: 1200px;
        border-radius: 15px;
    }

    .homie-text {
        color: #ffffff;
        text-shadow: 0 0 5px #a855f7, 0 0 10px rgba(168, 85, 247, 0.5);
        font-size: 1.1em;
        line-height: 1.8;
    }

    .homie-badge {
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

    .section-title {
        font-family: 'Permanent Marker', cursive;
        font-size: clamp(2rem, 6vw, 4rem);
        color: #a855f7;
        text-shadow: 0 0 15px #a855f7;
        text-align: center;
        margin-bottom: 2rem;
        text-transform: uppercase;
        letter-spacing: 3px;
    }

    /* Ensure content is above background */
    header, main, footer {
        position: relative;
        z-index: 1;
    }
</style>

<main class="homie-page">
    <!-- Hero Section -->
    <section class="homie-hero">
        <div class="container">
            <h1 class="homie-title">DABIGHOMIECHEESE</h1>
            <p class="homie-subtitle">Featured Artist</p>
        </div>
    </section>

    <!-- Music Section -->
    <section class="homie-music-section">
        <div class="container">
            <h2 class="section-title">MUSIC</h2>
            <div class="music-player-container">
                <div class="audio-player">
                    <audio id="homieAudio" controls>
                        <source src="<?php echo get_template_directory_uri(); ?>/music/Another_Night_clara_la_san.mp3" type="audio/mpeg">
                        <source src="https://southwestsecret.com/wp-content/themes/southwestsecret-theme/music/Another_Night_clara_la_san.mp3" type="audio/mpeg">
                        <source src="https://southwestsecret.com/wp-content/themes/southwestsecret/music/Another_Night_clara_la_san.mp3" type="audio/mpeg">
                        <source src="scrap/mix19.mp3" type="audio/mpeg">
                        <source src="scrap/mix_19.mp3" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                <button class="play-button" id="playBtn">Play Music</button>
                <p class="homie-text" style="text-align: center; margin-top: 1rem;">
                    Click play to listen to the track!
                </p>
                <p id="audioStatus" class="homie-text" style="text-align: center; margin-top: 1rem; color: #fcd34d; display: none;"></p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="homie-section">
        <div class="container">
            <h2 class="section-title">ABOUT</h2>
            <div class="homie-text" style="text-align: center; max-width: 800px; margin: 0 auto;">
                <p style="margin-bottom: 20px;">
                    Welcome to <strong style="color: #a855f7; text-shadow: 0 0 10px #a855f7;">DabigHomieCheese's</strong> space on SouthWest Secret!
                </p>
                <p style="margin-bottom: 20px;">
                    Check back soon for more music and updates!
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="homie-section">
        <div class="container">
            <h2 class="section-title">STAY CONNECTED</h2>
            <div style="text-align: center;">
                <p class="homie-text">Subscribe to SouthWest Secret for the latest mixes!</p>
                <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="play-button" style="text-decoration: none; display: inline-block;">
                    Subscribe Now
                </a>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('homieAudio');
    const playBtn = document.getElementById('playBtn');
    
    if (audio && playBtn) {
        // Try alternative audio sources if primary fails
        const audioSources = [
            '<?php echo get_template_directory_uri(); ?>/music/Another_Night_clara_la_san.mp3',
            'https://southwestsecret.com/wp-content/themes/southwestsecret-theme/music/Another_Night_clara_la_san.mp3',
            'https://southwestsecret.com/wp-content/themes/southwestsecret/music/Another_Night_clara_la_san.mp3',
            'scrap/mix19.mp3',
            'scrap/mix_19.mp3',
            'scrap/Mix19.mp3',
            'scrap/Mix 19.mp3'
        ];
        
        let currentSourceIndex = 0;
        const statusEl = document.getElementById('audioStatus');
        
        function tryNextSource() {
            if (currentSourceIndex < audioSources.length - 1) {
                currentSourceIndex++;
                audio.src = audioSources[currentSourceIndex];
                audio.load();
                if (statusEl) {
                    statusEl.textContent = `Trying source ${currentSourceIndex + 1}/${audioSources.length}...`;
                    statusEl.style.display = 'block';
                }
            } else {
                if (statusEl) {
                    statusEl.textContent = 'Audio file not found. Please upload the music file to the theme music directory.';
                    statusEl.style.display = 'block';
                }
            }
        }
        
        audio.addEventListener('error', function() {
            console.log('Audio load error, trying next source...');
            tryNextSource();
        });
        
        audio.addEventListener('loadeddata', function() {
            if (statusEl) {
                statusEl.style.display = 'none';
            }
        });
        
        playBtn.addEventListener('click', function() {
            if (audio.paused) {
                audio.play().then(() => {
                    playBtn.textContent = 'Pause Music';
                }).catch(err => {
                    console.error('Play error:', err);
                    tryNextSource();
                });
            } else {
                audio.pause();
                playBtn.textContent = 'Play Music';
            }
        });
        
        audio.addEventListener('play', function() {
            playBtn.textContent = 'Pause Music';
        });
        
        audio.addEventListener('pause', function() {
            playBtn.textContent = 'Play Music';
        });
    }
});
</script>

<?php get_footer(); ?>

