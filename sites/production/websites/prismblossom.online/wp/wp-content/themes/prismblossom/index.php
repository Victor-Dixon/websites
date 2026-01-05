<?php

/**
 * Main Template File
 * 
 * @package PrismBlossom
 */

get_header();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="hero-title">Welcome to PrismBlossom</h1>
        <p class="hero-subtitle">A celebration of life's special moments and milestones</p>
        <div class="hero-buttons">
            <a href="<?php echo home_url('/carmyn'); ?>" class="btn-primary">Visit Carmyn</a>
            <a href="<?php echo home_url('/guestbook'); ?>" class="btn-outline">Sign Guestbook</a>
        </div>
    </div>
</section>

<!-- Moods & Music Section -->
<section id="moods" class="moods-library">
    <div class="container">
        <h2 class="section-title">
            <span>MOODS</span>
            <span class="subtitle">Playlists</span>
        </h2>
        <p class="library-subtitle">Explore music playlists curated for different moods and vibes. Click a mood to play!</p>

        <!-- Moods Grid -->
        <div class="moods-grid">
            <div class="mood-card" data-mood="happy" data-youtube="sF80I-TQiW0">
                <div class="mood-icon">😊</div>
                <h3>Happy</h3>
                <p>Uplifting tunes to brighten your day</p>
            </div>

            <div class="mood-card" data-mood="chill" data-youtube="5qap5aO4i9A">
                <div class="mood-icon">🧘</div>
                <h3>Chill</h3>
                <p>Relaxed vibes for unwinding</p>
            </div>

            <div class="mood-card" data-mood="party" data-youtube="jfKfPfyJRdk">
                <div class="mood-icon">🎉</div>
                <h3>Party</h3>
                <p>High energy beats for celebration</p>
            </div>

            <div class="mood-card" data-mood="romantic" data-youtube="rUxyKA_-grg">
                <div class="mood-icon">💕</div>
                <h3>Romantic</h3>
                <p>Sweet melodies for special moments</p>
            </div>

            <div class="mood-card" data-mood="nostalgic" data-youtube="jfKfPfyJRdk">
                <div class="mood-icon">✨</div>
                <h3>Nostalgic</h3>
                <p>Throwback hits and memories</p>
            </div>

            <div class="mood-card" data-mood="focused" data-youtube="5qap5aO4i9A">
                <div class="mood-icon">🎯</div>
                <h3>Focused</h3>
                <p>Concentration beats for productivity</p>
            </div>
        </div>

        <!-- Music Player Section -->
        <div class="mood-player" id="mood-player">
            <div class="player-header">
                <h3>Now Playing</h3>
                <div class="current-mood-info">
                    <span class="current-mood">Select a mood above</span>
                </div>
            </div>

            <div class="player-content">
                <div class="youtube-container">
                    <div class="placeholder-player">
                        <div class="play-icon">▶</div>
                        <p>Click a mood card above to load playlist</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about">
    <div class="container">
        <h2>About PrismBlossom</h2>
        <p>PrismBlossom is a personal site dedicated to celebrating important moments, sharing memories, and bringing people together. This space serves as:</p>
        <ul class="feature-list">
            <li>✨ A digital guestbook for special occasions</li>
            <li>📸 A photo gallery of cherished memories</li>
            <li>🎵 Music playlists for every mood</li>
            <li>🎉 A platform for event coordination and updates</li>
            <li>💝 A place to mark and celebrate personal milestones</li>
        </ul>
    </div>
</section>

<!-- Featured Pages -->
<section class="featured-pages">
    <div class="container">
        <h2>Explore</h2>
        <div class="pages-grid">
            <article class="page-card">
                <h3><a href="<?php echo home_url('/carmyn'); ?>">Carmyn</a></h3>
                <p>Learn more about Carmyn and her journey</p>
            </article>
            <article class="page-card">
                <h3><a href="<?php echo home_url('/guestbook'); ?>">Guestbook</a></h3>
                <p>Leave a message and share your thoughts</p>
            </article>
            <article class="page-card">
                <h3><a href="<?php echo home_url('/invitation'); ?>">Invitation</a></h3>
                <p>Special event invitations and details</p>
            </article>
            <article class="page-card">
                <h3><a href="<?php echo home_url('/birthday-fun'); ?>">Birthday Fun</a></h3>
                <p>Celebrate and have fun!</p>
            </article>
        </div>
    </div>
</section>

<?php
// Display blog posts if any
if (have_posts()) :
?>
    <section class="blog-posts">
        <div class="container">
            <h2>Latest Updates</h2>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="post-card">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="post-meta">
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                        </div>
                        <div class="post-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
<?php
endif;
?>

<?php get_footer(); ?>

<script>
    // Moods & Music Player Functionality
    document.addEventListener('DOMContentLoaded', function() {
        let currentPlayer = null;
        const moodCards = document.querySelectorAll('.mood-card');
        const playerContainer = document.getElementById('mood-player');
        const currentMoodSpan = document.querySelector('.current-mood');
        const youtubeContainer = document.querySelector('.youtube-container');

        // Mood data
        const moodData = {
            'happy': {
                youtubeId: 'sF80I-TQiW0',
                title: 'Happy Mood Playlist',
                description: 'Uplifting tunes to brighten your day'
            },
            'chill': {
                youtubeId: '5qap5aO4i9A',
                title: 'Chill Mood Playlist',
                description: 'Relaxed vibes for unwinding'
            },
            'party': {
                youtubeId: 'jfKfPfyJRdk',
                title: 'Party Mood Playlist',
                description: 'High energy beats for celebration'
            },
            'romantic': {
                youtubeId: 'rUxyKA_-grg',
                title: 'Romantic Mood Playlist',
                description: 'Sweet melodies for special moments'
            },
            'nostalgic': {
                youtubeId: 'jfKfPfyJRdk',
                title: 'Nostalgic Mood Playlist',
                description: 'Throwback hits and memories'
            },
            'focused': {
                youtubeId: '5qap5aO4i9A',
                title: 'Focused Mood Playlist',
                description: 'Concentration beats for productivity'
            }
        };

        // Handle mood card clicks
        moodCards.forEach(card => {
            card.addEventListener('click', function() {
                const mood = this.getAttribute('data-mood');
                const data = moodData[mood];

                if (data) {
                    // Update current mood display
                    if (currentMoodSpan) {
                        currentMoodSpan.textContent = data.title;
                    }

                    // Remove active class from all cards
                    moodCards.forEach(c => c.classList.remove('active'));
                    // Add active class to clicked card
                    this.classList.add('active');

                    // Load YouTube player
                    loadYouTubePlayer(data.youtubeId);
                }
            });

            // Add hover effect
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'translateY(0)';
                }
            });
        });

        // Load YouTube player
        function loadYouTubePlayer(youtubeId) {
            if (!youtubeContainer) return;

            // Remove placeholder
            const placeholder = youtubeContainer.querySelector('.placeholder-player');
            if (placeholder) {
                placeholder.style.display = 'none';
            }

            // Create iframe for YouTube embed
            youtubeContainer.innerHTML = `
            <iframe 
                width="100%" 
                height="400" 
                src="https://www.youtube.com/embed/${youtubeId}?autoplay=1&enablejsapi=1" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                style="border-radius: 10px; max-width: 100%;">
            </iframe>
        `;
        }
    });
</script>

<style id="homepage-neon-purple-styles">
    /* Homepage Neon Purple Background - Force apply - Version 2 */
    html,
    body,
    body.home,
    body.page-template-default,
    body.blog,
    body.archive {
        background: #bf00ff !important;
        /* Neon purple */
        background: linear-gradient(135deg, #bf00ff 0%, #9d00ff 50%, #7d00ff 100%) !important;
        background-attachment: fixed !important;
        min-height: 100vh !important;
    }

    /* Ensure it covers the entire viewport */
    html {
        background: #bf00ff !important;
        height: 100% !important;
    }

    body {
        background: #bf00ff !important;
        background: linear-gradient(135deg, #bf00ff 0%, #9d00ff 50%, #7d00ff 100%) !important;
        background-attachment: fixed !important;
        min-height: 100vh !important;
    }

    /* Hero Section */
    .hero {
        background: transparent;
        color: white;
        padding: 80px 20px;
        text-align: center;
    }

    .hero-title {
        color: white !important;
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.8);
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .hero-subtitle {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 1.5rem;
        margin-bottom: 30px;
    }

    /* About Section */
    .about {
        background: rgba(255, 255, 255, 0.1);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        padding: 60px 20px;
        border-radius: 15px;
        margin: 40px 0;
        color: white;
        border: 2px solid rgba(255, 0, 255, 0.3);
        box-shadow: 0 4px 20px rgba(191, 0, 255, 0.3);
    }

    .about h2 {
        color: white !important;
        text-shadow: 0 0 20px rgba(255, 0, 255, 0.9), 0 0 40px rgba(191, 0, 255, 0.7);
    }

    .about p,
    .about li {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Featured Pages Section */
    .featured-pages {
        background: rgba(255, 255, 255, 0.1);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        padding: 60px 20px;
        border-radius: 15px;
        margin: 40px 0;
    }

    .featured-pages h2 {
        color: white !important;
        text-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
        text-align: center;
        margin-bottom: 30px;
    }

    .pages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-card {
        background: rgba(255, 255, 255, 0.15);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        padding: 30px;
        border-radius: 15px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
    }

    .page-card:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .page-card h3 a {
        color: white !important;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
        font-size: 1.5rem;
    }

    .page-card p {
        color: rgba(255, 255, 255, 0.9) !important;
        margin-top: 10px;
    }

    /* Blog Posts Section */
    .blog-posts {
        background: rgba(255, 255, 255, 0.1);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        padding: 60px 20px;
        border-radius: 15px;
        margin: 40px 0;
    }

    .blog-posts h2 {
        color: white !important;
        text-shadow: 0 0 15px rgba(255, 255, 255, 0.6);
        text-align: center;
        margin-bottom: 30px;
    }

    .posts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .post-card {
        background: rgba(255, 255, 255, 0.15);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        padding: 25px;
        border-radius: 15px;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .post-card h3 a {
        color: white !important;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
    }

    .post-card .post-meta,
    .post-card .post-excerpt {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Moods Library Styles */
    .moods-library {
        padding: 60px 20px;
        background: rgba(255, 255, 255, 0.1);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        margin: 40px 0;
    }

    .moods-library .section-title {
        color: white !important;
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.8);
    }

    .moods-library .section-title .subtitle {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .library-subtitle {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .moods-library .section-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: white !important;
        text-shadow: 0 0 20px rgba(255, 0, 255, 0.9), 0 0 40px rgba(191, 0, 255, 0.7);
    }

    .moods-library .section-title .subtitle {
        display: block;
        font-size: 1.5rem;
        color: rgba(255, 255, 255, 0.95) !important;
        margin-top: 5px;
        text-shadow: 0 0 15px rgba(255, 0, 255, 0.8);
    }

    .library-subtitle {
        text-align: center;
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.9) !important;
        margin-bottom: 40px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        text-shadow: 0 0 10px rgba(255, 0, 255, 0.6);
    }

    .moods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .mood-card {
        background: rgba(255, 255, 255, 0.15);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(191, 0, 255, 0.3);
        border: 3px solid rgba(255, 0, 255, 0.5);
    }

    .mood-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(255, 0, 255, 0.6), 0 0 40px rgba(191, 0, 255, 0.4);
        border-color: #ff00ff;
        background: rgba(255, 0, 255, 0.2);
    }

    .mood-card.active {
        border-color: #ff00ff;
        background: rgba(255, 0, 255, 0.25);
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(255, 0, 255, 0.8), 0 0 60px rgba(191, 0, 255, 0.6);
    }

    .mood-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .mood-card h3 {
        font-size: 1.5rem;
        color: white !important;
        margin-bottom: 10px;
        text-shadow: 0 0 15px rgba(255, 0, 255, 0.8), 0 0 30px rgba(191, 0, 255, 0.6);
    }

    .mood-card p {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 0.9rem;
        text-shadow: 0 0 10px rgba(255, 0, 255, 0.6);
    }

    .mood-player {
        background: rgba(255, 255, 255, 0.15);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(191, 0, 255, 0.4), 0 0 40px rgba(255, 0, 255, 0.3);
        border: 2px solid rgba(255, 0, 255, 0.5);
        max-width: 800px;
        margin: 0 auto;
    }

    .player-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .player-header h3 {
        font-size: 1.8rem;
        color: white !important;
        margin-bottom: 10px;
        text-shadow: 0 0 15px rgba(255, 0, 255, 0.8), 0 0 30px rgba(191, 0, 255, 0.6);
    }

    .current-mood-info {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 1.1rem;
        text-shadow: 0 0 10px rgba(255, 0, 255, 0.6);
    }

    .youtube-container {
        width: 100%;
        position: relative;
        padding-bottom: 56.25%;
        /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        border-radius: 10px;
    }

    .youtube-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .placeholder-player {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(191, 0, 255, 0.1);
        -webkit-backdrop-filter: blur(5px);
        backdrop-filter: blur(5px);
        color: rgba(255, 255, 255, 0.9);
        text-shadow: 0 0 10px rgba(255, 0, 255, 0.6);
    }

    .play-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .moods-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .mood-card {
            padding: 20px 15px;
        }

        .mood-icon {
            font-size: 2rem;
        }
    }
</style>