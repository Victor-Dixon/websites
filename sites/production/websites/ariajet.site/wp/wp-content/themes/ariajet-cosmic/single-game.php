<?php
/**
 * Single Game Template
 * 
 * Displays a single game with embed player and cosmic styling.
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main single-game">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            
            // Get game meta
            $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
            $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
            $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
            $game_difficulty = get_post_meta(get_the_ID(), '_ariajet_game_difficulty', true);
            ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('game-showcase'); ?>>
                <!-- Game Header -->
                <header class="entry-header">
                    <h1 class="entry-title">
                        <span class="game-icon">🎮</span>
                        <?php the_title(); ?>
                    </h1>
                    
                    <div class="game-meta">
                        <?php if ($game_status) : ?>
                            <span class="game-badge status-<?php echo esc_attr($game_status); ?>">
                                <?php 
                                $status_labels = array(
                                    'published'   => __('Published', 'ariajet-cosmic'),
                                    'beta'        => __('Beta', 'ariajet-cosmic'),
                                    'development' => __('In Development', 'ariajet-cosmic'),
                                    'coming-soon' => __('Coming Soon', 'ariajet-cosmic'),
                                );
                                echo esc_html($status_labels[$game_status] ?? ucfirst($game_status));
                                ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($game_type) : ?>
                            <span class="game-badge type">
                                <?php 
                                $type_labels = array(
                                    '2d'         => __('2D Game', 'ariajet-cosmic'),
                                    'puzzle'     => __('Puzzle', 'ariajet-cosmic'),
                                    'adventure'  => __('Adventure', 'ariajet-cosmic'),
                                    'survival'   => __('Survival', 'ariajet-cosmic'),
                                    'platformer' => __('Platformer', 'ariajet-cosmic'),
                                    'arcade'     => __('Arcade', 'ariajet-cosmic'),
                                );
                                echo esc_html($type_labels[$game_type] ?? ucfirst($game_type));
                                ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($game_difficulty) : ?>
                            <span class="game-badge difficulty-<?php echo esc_attr($game_difficulty); ?>">
                                <?php 
                                $difficulty_labels = array(
                                    'easy'   => __('Easy', 'ariajet-cosmic'),
                                    'medium' => __('Medium', 'ariajet-cosmic'),
                                    'hard'   => __('Hard', 'ariajet-cosmic'),
                                    'expert' => __('Expert', 'ariajet-cosmic'),
                                );
                                echo esc_html($difficulty_labels[$game_difficulty] ?? ucfirst($game_difficulty));
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </header>
                
                <!-- Game Embed -->
                <?php if ($game_url) : ?>
                    <div class="game-embed-container">
                        <div class="game-embed" data-game-url="<?php echo esc_url($game_url); ?>">
                            <!-- Game will be embedded here via JavaScript -->
                            <div class="loading-spinner"></div>
                        </div>
                        
                        <div class="game-play-fullscreen">
                            <a href="<?php echo esc_url($game_url); ?>" 
                               class="cosmic-button accent" 
                               target="_blank"
                               rel="noopener">
                                <span class="button-icon">🖥️</span>
                                <?php _e('Play Full Screen', 'ariajet-cosmic'); ?>
                            </a>
                            
                            <button type="button" 
                                    class="cosmic-button secondary"
                                    onclick="toggleGameFullscreen('<?php echo esc_js($game_url); ?>')">
                                <span class="button-icon">⛶</span>
                                <?php _e('Expand', 'ariajet-cosmic'); ?>
                            </button>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="game-coming-soon cosmic-card">
                        <div class="coming-soon-icon">🚀</div>
                        <h3><?php _e('Game Coming Soon!', 'ariajet-cosmic'); ?></h3>
                        <p><?php _e('This game is still being developed. Check back soon!', 'ariajet-cosmic'); ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Game Content/Description -->
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
                
                <!-- Game Navigation -->
                <nav class="game-navigation">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>
                    
                    <?php if ($prev_post) : ?>
                        <a href="<?php echo get_permalink($prev_post); ?>" class="game-nav-link prev">
                            <span class="nav-arrow">←</span>
                            <span class="nav-label"><?php _e('Previous Game', 'ariajet-cosmic'); ?></span>
                            <span class="nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($next_post) : ?>
                        <a href="<?php echo get_permalink($next_post); ?>" class="game-nav-link next">
                            <span class="nav-arrow">→</span>
                            <span class="nav-label"><?php _e('Next Game', 'ariajet-cosmic'); ?></span>
                            <span class="nav-title"><?php echo esc_html($next_post->post_title); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>
            </article>
            
            <?php
        endwhile;
        ?>
        
        <!-- Back to Games -->
        <div class="back-to-games">
            <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="cosmic-button">
                <span class="nav-arrow">←</span>
                <?php _e('Back to All Games', 'ariajet-cosmic'); ?>
            </a>
        </div>
    </div>
</main>

<style>
/* Single Game Page Additional Styles */
.game-icon {
    display: inline-block;
    margin-right: var(--space-3);
    animation: game-bounce 1s ease-in-out infinite;
}

@keyframes game-bounce {
    0%, 100% { transform: translateY(0) rotate(-5deg); }
    50% { transform: translateY(-5px) rotate(5deg); }
}

.game-coming-soon {
    text-align: center;
    padding: var(--space-16);
    margin: var(--space-8) 0;
}

.coming-soon-icon {
    font-size: 5rem;
    margin-bottom: var(--space-6);
    animation: rocket-float 3s ease-in-out infinite;
}

.game-coming-soon h3 {
    font-size: var(--text-2xl);
    color: var(--neon-cyan);
    margin-bottom: var(--space-4);
}

.game-navigation {
    display: flex;
    justify-content: space-between;
    gap: var(--space-6);
    margin-top: var(--space-12);
    padding-top: var(--space-8);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.game-nav-link {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
    padding: var(--space-4);
    background: rgba(20, 20, 50, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-md);
    text-decoration: none;
    transition: all var(--transition-normal);
    max-width: 45%;
}

.game-nav-link:hover {
    border-color: var(--neon-cyan);
    background: rgba(0, 255, 247, 0.1);
    transform: translateY(-3px);
}

.game-nav-link.next {
    text-align: right;
    margin-left: auto;
}

.game-nav-link .nav-arrow {
    font-size: var(--text-2xl);
    color: var(--neon-cyan);
}

.game-nav-link .nav-label {
    font-size: var(--text-xs);
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.game-nav-link .nav-title {
    font-family: var(--font-display);
    font-size: var(--text-base);
    font-weight: 600;
    color: var(--text-primary);
}

.back-to-games {
    text-align: center;
    margin-top: var(--space-12);
}

@media (max-width: 768px) {
    .game-navigation {
        flex-direction: column;
    }
    
    .game-nav-link {
        max-width: 100%;
    }
    
    .game-nav-link.next {
        text-align: left;
    }
}
</style>

<?php
get_footer();
