<?php
/**
 * Template Name: Playlists Page
 * 
 * Custom page template for displaying Aria's playlists
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        
        <!-- Page Header -->
        <section class="cosmic-hero page-hero">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="hero-emoji">🎵</span>
                    <?php _e('Playlists', 'ariajet-cosmic'); ?>
                </h1>
                <p class="hero-subtitle">
                    <?php _e('A collection of Aria\'s favorite music mixes, playlists, and curated tracks.', 'ariajet-cosmic'); ?>
                </p>
            </div>
        </section>

        <!-- Playlists Grid -->
        <section class="playlists-section">
            <div class="playlists-grid">
                
                <article class="playlist-card cosmic-card">
                    <div class="playlist-icon">🎵</div>
                    <h3 class="playlist-title"><?php _e('Favorite Mixes', 'ariajet-cosmic'); ?></h3>
                    <p class="playlist-description">
                        <?php _e('A collection of Aria\'s favorite music mixes and remixes.', 'ariajet-cosmic'); ?>
                    </p>
                </article>

                <article class="playlist-card cosmic-card">
                    <div class="playlist-icon">🎶</div>
                    <h3 class="playlist-title"><?php _e('Chill Vibes', 'ariajet-cosmic'); ?></h3>
                    <p class="playlist-description">
                        <?php _e('Relaxing tunes for when you need to unwind and chill out.', 'ariajet-cosmic'); ?>
                    </p>
                </article>

                <article class="playlist-card cosmic-card">
                    <div class="playlist-icon">🔥</div>
                    <h3 class="playlist-title"><?php _e('Energy Boost', 'ariajet-cosmic'); ?></h3>
                    <p class="playlist-description">
                        <?php _e('High-energy tracks to get you pumped and motivated.', 'ariajet-cosmic'); ?>
                    </p>
                </article>

            </div>

            <!-- Coming Soon Section -->
            <div class="coming-soon cosmic-card">
                <div class="coming-soon-icon">🚀</div>
                <h2><?php _e('Coming Soon', 'ariajet-cosmic'); ?></h2>
                <p>
                    <?php _e('Playlists will be available soon! Aria is curating the perfect collection of tracks for you.', 'ariajet-cosmic'); ?>
                </p>
            </div>

        </section>

        <?php
        // Display page content if any
        while (have_posts()) :
            the_post();
            if (get_the_content()) :
                ?>
                <section class="page-content">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </section>
                <?php
            endif;
        endwhile;
        ?>

    </div>
</main>

<?php
get_footer();

