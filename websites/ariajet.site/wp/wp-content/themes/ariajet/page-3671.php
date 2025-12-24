<?php
/**
 * Template for Page ID 3671 (Music Page)
 * 
 * This template is specifically for the music page
 * 
 * @package AriaJet
 */

get_header();
?>

<main id="main" class="site-main music-page">
    <div class="container">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <header class="page-header">
                    <h1 class="page-title">
                        <span class="emoji">🎵</span>
                        <?php the_title(); ?>
                    </h1>
                    <p class="page-description">Discover Aria's music collection from the cosmic universe</p>
                </header>
                
                <div class="entry-content music-content">
                    <?php the_content(); ?>
                    
                    <div class="music-grid">
                        <div class="music-track">
                            <div class="track-header">
                                <div class="track-icon">🎵</div>
                                <div class="track-info">
                                    <h2 class="track-title">Oxygen (Smino Mix)</h2>
                                    <p class="track-artist">Aria</p>
                                </div>
                            </div>
                            <div class="audio-player-wrapper">
                                <audio controls class="audio-player">
                                    <source src="<?php echo esc_url('https://ariajet.site/wp-content/uploads/music/oxygen%20smino.mp3'); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                            <div class="cosmic-elements">
                                <span class="cosmic-emoji">🪐</span>
                                <span class="cosmic-emoji">🌙</span>
                                <span class="cosmic-emoji">⭐</span>
                                <span class="cosmic-emoji">🌟</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            endwhile;
        endif;
        ?>
    </div>
</main>

<style>
.music-page {
    padding: 100px 2rem 50px;
    min-height: 80vh;
}

.music-page .page-header {
    text-align: center;
    margin-bottom: 3rem;
}

.music-page .page-title {
    font-size: 3rem;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.music-page .page-title .emoji {
    display: inline-block;
    margin-right: 0.5rem;
    font-size: 3rem;
}

.music-page .page-description {
    font-size: 1.2rem;
    color: #7f8c8d;
    max-width: 600px;
    margin: 0 auto;
}

.music-content {
    max-width: 1200px;
    margin: 0 auto;
}

.music-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.music-track {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 20px;
    padding: 3rem 2rem;
    text-align: center;
    border: 2px solid rgba(102, 126, 234, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.music-track:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.track-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.track-icon {
    font-size: 4rem;
    animation: float 3s ease-in-out infinite;
}

.track-info {
    text-align: left;
}

.track-title {
    font-size: 2rem;
    margin: 0 0 0.5rem 0;
    color: #2c3e50;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.track-artist {
    font-size: 1.2rem;
    color: #7f8c8d;
    margin: 0;
}

.audio-player-wrapper {
    margin: 2rem 0;
    width: 100%;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.audio-player {
    width: 100%;
    height: 50px;
    border-radius: 10px;
    background: rgba(102, 126, 234, 0.1);
    outline: none;
}

.audio-player::-webkit-media-controls-panel {
    background-color: rgba(102, 126, 234, 0.1);
    border-radius: 10px;
}

.cosmic-elements {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}

.cosmic-emoji {
    font-size: 2rem;
    animation: twinkle 2s ease-in-out infinite;
    animation-delay: calc(var(--i) * 0.2s);
}

.cosmic-emoji:nth-child(1) { --i: 0; }
.cosmic-emoji:nth-child(2) { --i: 1; }
.cosmic-emoji:nth-child(3) { --i: 2; }
.cosmic-emoji:nth-child(4) { --i: 3; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes twinkle {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.2); }
}

@media (max-width: 768px) {
    .music-page .page-title {
        font-size: 2rem;
    }
    
    .music-page .page-title .emoji {
        font-size: 2rem;
    }
    
    .music-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
get_footer();

