<?php
/**
 * Game Archive Template
 * 
 * Displays all games in a cosmic-styled grid with filtering.
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main game-archive">
    <div class="container">
        <!-- Page Header -->
        <header class="page-header">
            <h1 class="page-title">
                <span class="title-emoji">🎮</span>
                <?php _e("Aria's Game Galaxy", 'ariajet-cosmic'); ?>
                <span class="title-emoji">🌟</span>
            </h1>
            <p class="page-description">
                <?php _e('Explore the cosmic collection of amazing 2D games! Click to play and have fun!', 'ariajet-cosmic'); ?>
            </p>
        </header>
        
        <!-- Game Filters -->
        <div class="game-filters">
            <button class="game-filter active" data-filter="all">
                <?php _e('All Games', 'ariajet-cosmic'); ?>
            </button>
            <button class="game-filter" data-filter="2d">
                <?php _e('2D', 'ariajet-cosmic'); ?>
            </button>
            <button class="game-filter" data-filter="adventure">
                <?php _e('Adventure', 'ariajet-cosmic'); ?>
            </button>
            <button class="game-filter" data-filter="puzzle">
                <?php _e('Puzzle', 'ariajet-cosmic'); ?>
            </button>
            <button class="game-filter" data-filter="survival">
                <?php _e('Survival', 'ariajet-cosmic'); ?>
            </button>
            <button class="game-filter" data-filter="platformer">
                <?php _e('Platformer', 'ariajet-cosmic'); ?>
            </button>
            <button class="game-filter" data-filter="arcade">
                <?php _e('Arcade', 'ariajet-cosmic'); ?>
            </button>
        </div>
        
        <!-- Game Grid -->
        <div class="game-grid">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    
                    // Get game meta
                    $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
                    $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
                    $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
                    $game_difficulty = get_post_meta(get_the_ID(), '_ariajet_game_difficulty', true);
                    ?>
                    
                    <article class="game-card" 
                             data-game-type="<?php echo esc_attr($game_type); ?>" 
                             data-game-url="<?php echo esc_url(get_permalink()); ?>">
                        
                        <!-- Game Thumbnail -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="game-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                                <div class="game-overlay">
                                    <span class="play-icon">▶</span>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="game-thumbnail placeholder">
                                <div class="placeholder-icon">🎮</div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Game Info -->
                        <div class="game-info">
                            <h3 class="game-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <!-- Game Badges -->
                            <div class="game-meta">
                                <?php if ($game_status) : ?>
                                    <span class="game-badge status-<?php echo esc_attr($game_status); ?>">
                                        <?php 
                                        $status_labels = array(
                                            'published'   => __('Published', 'ariajet-cosmic'),
                                            'beta'        => __('Beta', 'ariajet-cosmic'),
                                            'development' => __('In Dev', 'ariajet-cosmic'),
                                            'coming-soon' => __('Soon', 'ariajet-cosmic'),
                                        );
                                        echo esc_html($status_labels[$game_status] ?? ucfirst($game_status));
                                        ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($game_type) : ?>
                                    <span class="game-badge type">
                                        <?php echo esc_html(ucfirst($game_type)); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($game_difficulty) : ?>
                                    <span class="game-badge difficulty-<?php echo esc_attr($game_difficulty); ?>">
                                        <?php echo esc_html(ucfirst($game_difficulty)); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Game Excerpt -->
                            <div class="game-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <!-- Game Actions -->
                            <div class="game-actions">
                                <?php if ($game_url) : ?>
                                    <a href="<?php echo esc_url($game_url); ?>" 
                                       class="cosmic-button accent" 
                                       target="_blank"
                                       rel="noopener">
                                        <?php _e('Play Now', 'ariajet-cosmic'); ?>
                                    </a>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="cosmic-button secondary">
                                    <?php _e('Details', 'ariajet-cosmic'); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                    
                    <?php
                endwhile;
            else :
                ?>
                <div class="no-games cosmic-card">
                    <div class="no-games-icon">🚀</div>
                    <h3><?php _e('No Games Yet!', 'ariajet-cosmic'); ?></h3>
                    <p><?php _e("Aria is working on some amazing games. The cosmic universe is being created... Check back soon!", 'ariajet-cosmic'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
        
        <!-- Pagination -->
        <?php
        the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => '<span class="nav-arrow">←</span> ' . __('Previous', 'ariajet-cosmic'),
            'next_text' => __('Next', 'ariajet-cosmic') . ' <span class="nav-arrow">→</span>',
        ));
        ?>
        
        <!-- Back to Home -->
        <div class="section-cta">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="cosmic-button secondary">
                <span class="nav-arrow">←</span>
                <?php _e('Back to Home', 'ariajet-cosmic'); ?>
            </a>
        </div>
    </div>
</main>

<style>
/* Archive Page Additional Styles */
.title-emoji {
    display: inline-block;
    animation: star-spin 3s linear infinite;
}

@keyframes star-spin {
    0% { transform: rotate(0deg) scale(1); }
    50% { transform: rotate(180deg) scale(1.2); }
    100% { transform: rotate(360deg) scale(1); }
}

.game-title a {
    color: var(--text-primary);
    text-decoration: none;
    transition: color var(--transition-normal);
}

.game-title a:hover {
    color: var(--neon-cyan);
}

.game-archive .page-header {
    margin-bottom: var(--space-8);
}

@media (max-width: 768px) {
    .title-emoji {
        display: none;
    }
}
</style>

<?php
get_footer();
