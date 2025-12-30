<?php
/**
 * Template Name: Beautiful Streaming
 * 
 * Beautiful Streaming Hub Template
 * Modern, elegant streaming page with card-based design
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

// Enqueue CSS BEFORE get_header() so it's in the <head>
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('digitaldreamscape-beautiful-streaming', get_template_directory_uri() . '/assets/css/beautiful-streaming.css', array('digitaldreamscape-style'), '1.0.2');
}, 20);

// Alternative: Force CSS inline if wp_enqueue_scripts already fired
if (did_action('wp_enqueue_scripts')) {
    add_action('wp_head', function() {
        echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/assets/css/beautiful-streaming.css?v=1.0.2" />';
    }, 999);
}

get_header(); 
?>

<main class="site-main beautiful-streaming-main">
    <div class="beautiful-streaming-container">
        <!-- Hero Header -->
        <header class="beautiful-streaming-header">
            <div class="beautiful-streaming-header-content">
                <div class="beautiful-streaming-badge">[LIVE BROADCAST]</div>
                <h1 class="beautiful-streaming-title">Streaming Hub</h1>
                <p class="beautiful-streaming-description">
                    Watch live streams, catch up on past broadcasts, and join the <strong>Digital Dreamscape</strong> community in real-time.
                </p>
            </div>
        </header>

        <!-- Status Card -->
        <div class="beautiful-streaming-status-card">
            <div class="beautiful-streaming-status-header">
                <span class="beautiful-streaming-status-label">[STATUS]</span>
                <span class="beautiful-streaming-status-value offline">OFFLINE</span>
            </div>
            <div class="beautiful-streaming-status-content">
                <h3 class="beautiful-streaming-status-title">Current Status</h3>
                <p class="beautiful-streaming-status-text">
                    No live stream at the moment. Check back soon or follow for notifications when we go live.
                </p>
            </div>
        </div>

        <!-- Platform Cards Grid -->
        <div class="beautiful-streaming-grid">
            <!-- Twitch Card -->
            <div class="beautiful-streaming-card">
                <div class="beautiful-streaming-card-icon">
                    <div class="beautiful-streaming-card-icon-bg twitch">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
                            <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                        </svg>
                    </div>
                </div>
                <div class="beautiful-streaming-card-content">
                    <div class="beautiful-streaming-card-badge">[TWITCH]</div>
                    <h3 class="beautiful-streaming-card-title">Watch on Twitch</h3>
                    <p class="beautiful-streaming-card-description">
                        Join us on Twitch for live development streams, Q&A sessions, and community events.
                    </p>
                    <a href="https://twitch.tv/digitaldreamscape" target="_blank" rel="noopener" class="beautiful-streaming-card-button">
                        Visit Twitch Channel →
                    </a>
                </div>
            </div>

            <!-- YouTube Card -->
            <div class="beautiful-streaming-card">
                <div class="beautiful-streaming-card-icon">
                    <div class="beautiful-streaming-card-icon-bg youtube">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                </div>
                <div class="beautiful-streaming-card-content">
                    <div class="beautiful-streaming-card-badge">[YOUTUBE]</div>
                    <h3 class="beautiful-streaming-card-title">Watch on YouTube</h3>
                    <p class="beautiful-streaming-card-description">
                        Catch up on past streams, tutorials, and exclusive content on our YouTube channel.
                    </p>
                    <a href="https://youtube.com/@digitaldreamscape" target="_blank" rel="noopener" class="beautiful-streaming-card-button">
                        Visit YouTube Channel →
                    </a>
                </div>
            </div>

            <!-- Schedule Card -->
            <div class="beautiful-streaming-card">
                <div class="beautiful-streaming-card-icon">
                    <div class="beautiful-streaming-card-icon-bg schedule">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="48" height="48">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                        </svg>
                    </div>
                </div>
                <div class="beautiful-streaming-card-content">
                    <div class="beautiful-streaming-card-badge">[SCHEDULE]</div>
                    <h3 class="beautiful-streaming-card-title">Stream Schedule</h3>
                    <p class="beautiful-streaming-card-description">
                        We stream regularly throughout the week. Follow our socials to get notified when we go live.
                    </p>
                    <div class="beautiful-streaming-card-schedule">
                        <em>Schedule coming soon</em>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>

