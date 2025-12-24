<?php
/**
 * Main Template File - Chopped & Screwed DJ Theme
 * Original theme restored from home.html
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="container">
        <div class="hero-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="<?php bloginfo('name'); ?>" class="main-logo">
        </div>
        <h1 class="graffiti-title">
            <span class="graffiti">SOUTHWEST</span>
            <span class="bubble">SECRET</span>
        </h1>
        <p class="tagline">Chopped & Screwed DJ</p>
        <div class="social-links">
            <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="btn-youtube">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
                Subscribe on YouTube
            </a>
        </div>
    </div>
</section>

<!-- Screw Tapes Library Section -->
<section id="music" class="screw-tapes-library">
    <div class="container">
        <h2 class="section-title">
            <span class="graffiti-sub">SCREW</span>
            <span class="bubble-sub">TAPES</span>
        </h2>
        <p class="library-subtitle">Click a tape to load it into the player below</p>
        
        <!-- Featured Tape -->
        <div class="featured-tape-container">
            <div class="cassette-tape featured" data-volume="1" data-youtube="oYqlfb2sghc">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 1</div>
                        <div class="tape-title">FEATURED</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tape Grid -->
        <div class="tape-grid">
            <div class="cassette-tape" data-volume="1" data-youtube="oYqlfb2sghc">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 1</div>
                        <div class="tape-title">SCREW TAPE</div>
                    </div>
                </div>
            </div>
            
            <div class="cassette-tape" data-volume="2" data-youtube="jBQ0gArMvzc">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 2</div>
                        <div class="tape-title">SCREW TAPE</div>
                    </div>
                </div>
            </div>
            
            <div class="cassette-tape" data-volume="3" data-audio="Another_Night_clara_la_san.mp3">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 3</div>
                        <div class="tape-title">ANOTHER NIGHT</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Player Section -->
        <div class="tape-player" id="tape-player">
            <div class="player-header">
                <h3>Now Playing</h3>
                <div class="current-tape-info">
                    <span class="current-volume">Select a tape</span>
                </div>
            </div>
            
            <div class="player-content">
                <div class="youtube-container">
                    <div class="placeholder-player">
                        <div class="play-icon">▶</div>
                        <p>Click a cassette tape above to load</p>
                    </div>
                </div>
                
                <div class="tracklist" id="tracklist">
                    <h4>Tracklist</h4>
                    <div class="tracklist-content">
                        <p>Select a tape to view tracklist</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about">
    <div class="container">
        <h2 class="section-title">
            <span class="graffiti-sub">THE</span>
            <span class="bubble-sub">STORY</span>
        </h2>
        <div class="about-content">
            <p>
                Welcome to <strong>SouthWest Secret</strong>, where the art of chopped and screwed music lives on. 
                Inspired by the legendary DJ Screw and the Houston sound, I bring you slowed-down, 
                remixed versions of your favorite tracks.
            </p>
            <p>
                Each mix is carefully crafted with that signature screwed tempo, 
                creating a hypnotic and laid-back vibe that's perfect for cruising or just vibing out.
            </p>
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🎵</div>
                    <h3>Chopped & Screwed</h3>
                    <p>Classic Houston technique</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎧</div>
                    <h3>Fresh Mixes</h3>
                    <p>New content regularly</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🔥</div>
                    <h3>Original Style</h3>
                    <p>Unique sound selection</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact/Subscribe Section -->
<section id="contact" class="contact">
    <div class="container">
        <h2 class="section-title">
            <span class="graffiti-sub">STAY</span>
            <span class="bubble-sub">CONNECTED</span>
        </h2>
        <div class="contact-content">
            <p>Subscribe to the channel for the latest chopped and screwed mixes!</p>
            <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="btn-subscribe">
                Subscribe Now
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
