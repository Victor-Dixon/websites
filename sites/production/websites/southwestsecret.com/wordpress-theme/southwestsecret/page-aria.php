<?php
/**
 * Template Name: Aria Page
 * Template for Aria's dedicated showcase page
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<style>
/* Aria Page - Purple Dream Theme */
/* Color Palette: #8A2BE2 (electric purple), #7B1FA2 (deep neon purple), #000000 (pure black), #0A0012 (deep night purple-black) */

.aria-page {
    background: #0A0012;
    min-height: 100vh;
    padding-top: 80px;
}

/* Hero Banner Section */
.aria-hero {
    background: linear-gradient(135deg, #0A0012 0%, #000000 50%, #0A0012 100%);
    padding: 6rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    border-bottom: 3px solid #8A2BE2;
    box-shadow: 0 0 30px rgba(138, 43, 226, 0.5);
}

.aria-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(ellipse at center, rgba(138, 43, 226, 0.2) 0%, transparent 70%);
    pointer-events: none;
}

.aria-title {
    font-family: 'Permanent Marker', cursive;
    font-size: clamp(4rem, 20vw, 12rem);
    color: #8A2BE2;
    text-shadow: 0 0 20px #8A2BE2, 0 0 40px #8A2BE2, 0 0 60px rgba(138, 43, 226, 0.8);
    margin: 0;
    animation: aria-glow 3s ease-in-out infinite;
    position: relative;
    z-index: 1;
    letter-spacing: 5px;
}

@keyframes aria-glow {
    0%, 100% {
        text-shadow: 0 0 20px #8A2BE2, 0 0 40px #8A2BE2, 0 0 60px rgba(138, 43, 226, 0.8);
    }
    50% {
        text-shadow: 0 0 30px #8A2BE2, 0 0 60px #8A2BE2, 0 0 90px rgba(138, 43, 226, 1);
    }
}

.aria-hero-subtitle {
    font-size: clamp(1.2rem, 3vw, 2rem);
    color: #ffffff;
    margin-top: 1rem;
    opacity: 0.9;
    font-family: 'Rubik Bubbles', cursive;
    position: relative;
    z-index: 1;
}

/* Soundwave Silhouette */
.soundwave-container {
    margin: 2rem auto;
    max-width: 600px;
    position: relative;
    z-index: 1;
}

.soundwave {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 8px;
    height: 120px;
    margin: 2rem 0;
}

.wave-bar {
    width: 12px;
    background: linear-gradient(to top, #8A2BE2, #7B1FA2);
    border-radius: 6px 6px 0 0;
    box-shadow: 0 0 10px #8A2BE2;
    animation: wave-animate 1.5s ease-in-out infinite;
}

.wave-bar:nth-child(1) { height: 40%; animation-delay: 0s; }
.wave-bar:nth-child(2) { height: 70%; animation-delay: 0.1s; }
.wave-bar:nth-child(3) { height: 90%; animation-delay: 0.2s; }
.wave-bar:nth-child(4) { height: 100%; animation-delay: 0.3s; }
.wave-bar:nth-child(5) { height: 85%; animation-delay: 0.4s; }
.wave-bar:nth-child(6) { height: 60%; animation-delay: 0.5s; }
.wave-bar:nth-child(7) { height: 45%; animation-delay: 0.6s; }
.wave-bar:nth-child(8) { height: 65%; animation-delay: 0.7s; }
.wave-bar:nth-child(9) { height: 95%; animation-delay: 0.8s; }
.wave-bar:nth-child(10) { height: 75%; animation-delay: 0.9s; }
.wave-bar:nth-child(11) { height: 50%; animation-delay: 1s; }

@keyframes wave-animate {
    0%, 100% { transform: scaleY(1); }
    50% { transform: scaleY(0.7); }
}

/* Featured Mixes Section */
.aria-featured-mixes {
    background: #000000;
    padding: 4rem 2rem;
    border-top: 2px solid #8A2BE2;
    border-bottom: 2px solid #8A2BE2;
    box-shadow: 0 0 40px rgba(138, 43, 226, 0.3);
}

.mixes-title {
    font-family: 'Permanent Marker', cursive;
    font-size: clamp(2rem, 6vw, 4rem);
    color: #8A2BE2;
    text-shadow: 0 0 15px #8A2BE2;
    text-align: center;
    margin-bottom: 3rem;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.mixes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.mix-card {
    background: linear-gradient(135deg, #0A0012 0%, #000000 100%);
    border: 2px solid #8A2BE2;
    border-radius: 15px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(138, 43, 226, 0.3), inset 0 0 20px rgba(138, 43, 226, 0.1);
    position: relative;
    overflow: hidden;
}

.mix-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(138, 43, 226, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.mix-card:hover::before {
    opacity: 1;
}

.mix-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 0 40px rgba(138, 43, 226, 0.6), inset 0 0 30px rgba(138, 43, 226, 0.2);
    border-color: #7B1FA2;
}

.mix-image {
    width: 100%;
    aspect-ratio: 1;
    background: linear-gradient(135deg, #8A2BE2, #7B1FA2);
    border-radius: 10px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    box-shadow: 0 0 30px rgba(138, 43, 226, 0.5);
    position: relative;
}

.mix-title {
    color: #ffffff;
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.mix-play-btn {
    background: linear-gradient(135deg, #8A2BE2, #7B1FA2);
    color: #000000;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(138, 43, 226, 0.5);
    width: 100%;
    margin-top: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.mix-play-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(138, 43, 226, 0.8);
}

/* Playlist Section */
.aria-playlist {
    background: #0A0012;
    padding: 4rem 2rem;
    border-top: 2px solid #8A2BE2;
}

.playlist-title {
    font-family: 'Permanent Marker', cursive;
    font-size: clamp(2rem, 6vw, 4rem);
    color: #8A2BE2;
    text-shadow: 0 0 15px #8A2BE2;
    text-align: center;
    margin-bottom: 3rem;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.playlist-container {
    max-width: 900px;
    margin: 0 auto;
    background: #000000;
    border: 2px solid #8A2BE2;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 0 40px rgba(138, 43, 226, 0.3), inset 0 0 30px rgba(138, 43, 226, 0.1);
}

.playlist-embed {
    width: 100%;
    min-height: 400px;
    border-radius: 10px;
    background: rgba(138, 43, 226, 0.1);
    border: 1px solid #8A2BE2;
}

/* Bio Section */
.aria-bio {
    background: linear-gradient(135deg, #000000 0%, #0A0012 100%);
    padding: 4rem 2rem;
    border-top: 2px solid #8A2BE2;
    border-bottom: 2px solid #8A2BE2;
}

.bio-title {
    font-family: 'Permanent Marker', cursive;
    font-size: clamp(2rem, 6vw, 4rem);
    color: #8A2BE2;
    text-shadow: 0 0 15px #8A2BE2;
    text-align: center;
    margin-bottom: 3rem;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.bio-content {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.bio-text {
    font-size: clamp(1.1rem, 2.5vw, 1.5rem);
    line-height: 1.8;
    color: #ffffff;
    font-style: italic;
    padding: 2rem;
    background: rgba(138, 43, 226, 0.05);
    border-left: 4px solid #8A2BE2;
    border-radius: 10px;
    box-shadow: 0 0 30px rgba(138, 43, 226, 0.2);
}

.bio-text strong {
    color: #8A2BE2;
    text-shadow: 0 0 10px #8A2BE2;
}

/* Contact Section */
.aria-contact {
    background: #000000;
    padding: 4rem 2rem;
    text-align: center;
}

.contact-title {
    font-family: 'Permanent Marker', cursive;
    font-size: clamp(2rem, 6vw, 4rem);
    color: #8A2BE2;
    text-shadow: 0 0 15px #8A2BE2;
    margin-bottom: 3rem;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.social-icons {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    margin-top: 2rem;
}

.social-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid #8A2BE2;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: #8A2BE2;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(138, 43, 226, 0.4);
    background: rgba(138, 43, 226, 0.1);
}

.social-icon:hover {
    transform: scale(1.2) translateY(-5px);
    box-shadow: 0 0 40px rgba(138, 43, 226, 0.8);
    background: rgba(138, 43, 226, 0.2);
    border-color: #7B1FA2;
}

@media (max-width: 768px) {
    .mixes-grid {
        grid-template-columns: 1fr;
    }
    
    .soundwave {
        height: 80px;
    }
    
    .wave-bar {
        width: 8px;
    }
}
</style>

<div class="aria-page">
    <!-- Hero Banner Section -->
    <section class="aria-hero">
        <div class="container">
            <h1 class="aria-title">ARIA</h1>
            <p class="aria-hero-subtitle">Featured Artist</p>
            <div class="soundwave-container">
                <div class="soundwave">
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                    <div class="wave-bar"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Mixes Section -->
    <section class="aria-featured-mixes">
        <div class="container">
            <h2 class="mixes-title">Featured Mixes</h2>
            <div class="mixes-grid">
                <div class="mix-card">
                    <div class="mix-image">🎵</div>
                    <h3 class="mix-title">Mix Vol. 1</h3>
                    <p style="color: #cccccc; font-size: 0.9rem; margin-bottom: 1rem;">Coming Soon</p>
                    <button class="mix-play-btn">▶ Play</button>
                </div>
                <div class="mix-card">
                    <div class="mix-image">🎧</div>
                    <h3 class="mix-title">Mix Vol. 2</h3>
                    <p style="color: #cccccc; font-size: 0.9rem; margin-bottom: 1rem;">Coming Soon</p>
                    <button class="mix-play-btn">▶ Play</button>
                </div>
                <div class="mix-card">
                    <div class="mix-image">✨</div>
                    <h3 class="mix-title">Mix Vol. 3</h3>
                    <p style="color: #cccccc; font-size: 0.9rem; margin-bottom: 1rem;">Coming Soon</p>
                    <button class="mix-play-btn">▶ Play</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Playlist Section -->
    <section class="aria-playlist">
        <div class="container">
            <h2 class="playlist-title">Playlist</h2>
            <div class="playlist-container">
                <div class="playlist-embed">
                    <!-- YouTube or SoundCloud embed will go here -->
                    <p style="color: #8A2BE2; text-align: center; padding: 8rem 2rem; font-size: 1.2rem;">
                        Playlist embed coming soon
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bio Section -->
    <section class="aria-bio">
        <div class="container">
            <h2 class="bio-title">About Aria</h2>
            <div class="bio-content">
                <p class="bio-text">
                    Soft voice. <strong>Sharp taste</strong>. Aria curates sounds that drip like <strong>syrup</strong> — 
                    slowed, deepened, and glowing in <strong>purple light</strong>.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="aria-contact">
        <div class="container">
            <h2 class="contact-title">Follow</h2>
            <div class="social-icons">
                <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="social-icon" title="YouTube">
                    ▶
                </a>
                <a href="#" class="social-icon" title="Instagram" style="font-size: 1.5rem;">
                    📷
                </a>
                <a href="mailto:aria@southwestsecret.com" class="social-icon" title="Email">
                    ✉
                </a>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>

