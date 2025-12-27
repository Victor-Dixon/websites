<?php
/**
 * Streaming Page Template
 * 
 * Displays streaming content in Digital Dreamscape style
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="container">
        <!-- Streaming Header -->
        <header class="page-header dreamscape-page-header">
            <div class="page-badge">[LIVE BROADCAST]</div>
            <h1 class="page-title dreamscape-page-title">
                Streaming Hub
            </h1>
            <div class="page-description dreamscape-page-desc">
                <p>Watch live streams, catch up on past broadcasts, and join the <strong>Digital Dreamscape</strong> community in real-time.</p>
            </div>
        </header>

        <!-- Streaming Content -->
        <section class="streaming-section">
            
            <!-- Live Player Container -->
            <div class="stream-embed-container" style="background: #000; aspect-ratio: 16/9; margin-bottom: 2rem; border-radius: 8px; overflow: hidden; box-shadow: 0 0 30px rgba(138, 43, 226, 0.2);">
                <!-- Twitch Embed Placeholder -->
                <iframe src="https://player.twitch.tv/?channel=digitaldreamscape&parent=digitaldreamscape.site" frameborder="0" allowfullscreen="true" scrolling="no" height="100%" width="100%"></iframe>
            </div>

            <div class="streaming-grid">
                
                <!-- Building This Week -->
                <div class="streaming-card current-project-card" style="grid-column: span 2;">
                    <div class="card-header">
                        <span class="card-badge">[BUILDING THIS WEEK]</span>
                    </div>
                    <div class="card-content">
                        <h3>System Integration & Narrative AI</h3>
                        <p>This week we are connecting the 'Voice of the System' to the WordPress REST API. Watch us debug the authentication flow and generate the first autonomous blog post.</p>
                        <div class="progress-bar" style="background: rgba(255,255,255,0.1); height: 6px; border-radius: 3px; margin-top: 1rem;">
                            <div class="progress" style="width: 65%; height: 100%; background: var(--primary-color, #8a2be2); border-radius: 3px;"></div>
                        </div>
                        <p style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.7;">65% Complete</p>
                    </div>
                </div>

                <!-- Schedule Card -->
                <div class="streaming-card schedule-card">
                    <div class="card-header">
                        <span class="card-badge">[SCHEDULE]</span>
                    </div>
                    <div class="card-content">
                        <h3>Stream Schedule</h3>
                        <ul class="schedule-list" style="list-style: none; padding: 0; margin: 1rem 0;">
                            <li style="margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
                                <span>Tue</span>
                                <span style="opacity: 0.8;">7:00 PM EST - Dev Log</span>
                            </li>
                            <li style="margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
                                <span>Thu</span>
                                <span style="opacity: 0.8;">7:00 PM EST - Build Session</span>
                            </li>
                            <li style="margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
                                <span>Sun</span>
                                <span style="opacity: 0.8;">2:00 PM EST - Weekly Review</span>
                            </li>
                        </ul>
                        <a href="https://twitch.tv/digitaldreamscape" class="streaming-cta" target="_blank" rel="noopener">Follow on Twitch →</a>
                    </div>
                </div>

                <!-- VODs Card -->
                <div class="streaming-card vods-card" style="grid-column: span 3;">
                    <div class="card-header">
                        <span class="card-badge">[RECENT VODS]</span>
                    </div>
                    <div class="card-content">
                        <div class="vod-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div class="vod-item">
                                <a href="#" style="text-decoration: none; color: inherit;">
                                    <div class="vod-thumb" style="background: #333; aspect-ratio: 16/9; margin-bottom: 0.5rem; border-radius: 4px; display: flex; align-items: center; justify-content: center;">▶</div>
                                    <h4 style="font-size: 0.9rem; margin: 0;">Ep. 12: The API Awakening</h4>
                                    <span style="font-size: 0.8rem; opacity: 0.6;">2 days ago</span>
                                </a>
                            </div>
                            <div class="vod-item">
                                <a href="#" style="text-decoration: none; color: inherit;">
                                    <div class="vod-thumb" style="background: #333; aspect-ratio: 16/9; margin-bottom: 0.5rem; border-radius: 4px; display: flex; align-items: center; justify-content: center;">▶</div>
                                    <h4 style="font-size: 0.9rem; margin: 0;">Ep. 11: Debugging the Dream</h4>
                                    <span style="font-size: 0.8rem; opacity: 0.6;">5 days ago</span>
                                </a>
                            </div>
                            <div class="vod-item">
                                <a href="#" style="text-decoration: none; color: inherit;">
                                    <div class="vod-thumb" style="background: #333; aspect-ratio: 16/9; margin-bottom: 0.5rem; border-radius: 4px; display: flex; align-items: center; justify-content: center;">▶</div>
                                    <h4 style="font-size: 0.9rem; margin: 0;">Ep. 10: System Architecture</h4>
                                    <span style="font-size: 0.8rem; opacity: 0.6;">1 week ago</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Page Content (if any from WordPress editor) -->
        <?php while (have_posts()) : the_post(); ?>
            <?php if (get_the_content()) : ?>
                <section class="page-content">
                    <?php the_content(); ?>
                </section>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
</main>

<style>
.streaming-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}
.streaming-card {
    background: rgba(20, 20, 25, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 1.5rem;
}
.card-header {
    margin-bottom: 1rem;
}
.card-badge {
    background: rgba(138, 43, 226, 0.2);
    color: #ae81ff;
    padding: 0.2rem 0.5rem;
    font-size: 0.7rem;
    border-radius: 4px;
    letter-spacing: 1px;
}
@media (max-width: 768px) {
    .streaming-grid {
        grid-template-columns: 1fr;
    }
    .streaming-card, .current-project-card, .vods-card {
        grid-column: span 1 !important;
    }
}
</style>

<?php get_footer(); ?>
