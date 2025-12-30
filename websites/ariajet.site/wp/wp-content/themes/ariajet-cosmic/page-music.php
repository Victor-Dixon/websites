<?php
/**
 * Template Name: Music Page
 * Template Post Type: page
 *
 * Music Page Template (Cosmic)
 *
 * @package AriaJet_Cosmic
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

                    <?php
                    $tracks = function_exists('ariajet_get_music_tracks') ? ariajet_get_music_tracks() : array();
                    ?>

                    <div class="music-grid">
                        <?php if (empty($tracks)) : ?>
                            <div class="cosmic-card no-content">
                                <h2>Nothing here yet</h2>
                                <p>
                                    Upload an MP3 in WordPress (Media → Add New) or place it in
                                    <code>wp-content/uploads/music/</code>.
                                </p>
                            </div>
                        <?php else : ?>
                            <?php foreach ($tracks as $track) : ?>
                                <div class="cosmic-card music-track">
                                    <div class="track-header">
                                        <div class="track-icon float-animation"><?php echo esc_html($track['icon']); ?></div>
                                        <div class="track-info">
                                            <h2 class="track-title"><?php echo esc_html($track['title']); ?></h2>
                                            <p class="track-artist"><?php echo esc_html($track['artist']); ?></p>
                                        </div>
                                    </div>
                                    <div class="audio-player-wrapper">
                                        <audio controls class="audio-player" preload="none">
                                            <source src="<?php echo esc_url($track['url']); ?>" type="<?php echo esc_attr($track['mime']); ?>">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                    <div class="cosmic-elements">
                                        <?php foreach ($track['emojis'] as $emoji) : ?>
                                            <span class="cosmic-emoji pulse-animation"><?php echo esc_html((string) $emoji); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
    padding: var(--space-10) 0;
}

.music-content {
    max-width: 1200px;
    margin: 0 auto;
}

.music-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-8);
    margin-top: var(--space-10);
}

.music-track {
    text-align: center;
}

.track-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-6);
    margin-bottom: var(--space-6);
}

.track-icon {
    font-size: 3.5rem;
    filter: drop-shadow(0 0 18px rgba(0, 255, 247, 0.35));
}

.track-info {
    text-align: left;
}

.track-title {
    font-size: var(--text-2xl);
    margin: 0 0 var(--space-2) 0;
    background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--neon-purple) 60%, var(--neon-pink) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.track-artist {
    margin: 0;
    font-size: var(--text-base);
    color: var(--text-secondary);
}

.audio-player-wrapper {
    margin: var(--space-6) 0;
}

.audio-player {
    width: 100%;
    height: 48px;
    border-radius: var(--radius-md);
    background: rgba(0, 168, 255, 0.12);
    outline: none;
}

.cosmic-elements {
    display: flex;
    justify-content: center;
    gap: var(--space-4);
    margin-top: var(--space-6);
}

.cosmic-emoji {
    font-size: 1.6rem;
    opacity: 0.95;
}
</style>

<?php
get_footer();
?>

