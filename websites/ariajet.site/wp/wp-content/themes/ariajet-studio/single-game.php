<?php
/**
 * Single Game Template
 * 
 * A clean, focused game display with embedded player.
 * 
 * @package AriaJet_Studio
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    while (have_posts()) :
        the_post();
        
        $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
        $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
        $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="container">
                <div class="single-game-content">
                    
                    <!-- Game Header -->
                    <header class="game-header reveal">
                        <div class="game-meta-top">
                            <?php if ($game_status) : ?>
                                <span class="tag tag--<?php echo $game_status === 'published' ? 'sage' : ($game_status === 'beta' ? 'honey' : 'lavender'); ?>">
                                    <?php 
                                    $labels = array(
                                        'published' => '✓ Ready to play',
                                        'beta' => '🧪 Beta version',
                                        'development' => '🔨 In development',
                                    );
                                    echo esc_html($labels[$game_status] ?? ucfirst($game_status));
                                    ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($game_type) : ?>
                                <span class="tag tag--coral">
                                    <?php echo esc_html(ucfirst($game_type)); ?> game
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="game-title"><?php the_title(); ?></h1>
                        
                        <?php if (has_excerpt()) : ?>
                            <p class="game-description"><?php echo get_the_excerpt(); ?></p>
                        <?php endif; ?>
                    </header>
                    
                    <!-- Game Embed -->
                    <?php if ($game_url) : ?>
                        <div class="game-embed-wrapper reveal">
                            <div class="game-embed" data-game-url="<?php echo esc_url($game_url); ?>">
                                <!-- Game loaded via JavaScript -->
                            </div>
                            
                            <div class="game-actions">
                                <a href="<?php echo esc_url($game_url); ?>" 
                                   class="btn btn--primary btn--large" 
                                   target="_blank">
                                    <span class="btn-icon">↗</span>
                                    Open in new tab
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="games-empty reveal">
                            <span class="games-empty-icon">🎮</span>
                            <h3>Coming soon!</h3>
                            <p>This game is still being worked on. Check back later!</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Game Content -->
                    <?php if (get_the_content()) : ?>
                        <div class="game-content reveal">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Game Navigation -->
                    <nav class="game-navigation reveal">
                        <?php
                        $prev = get_previous_post();
                        $next = get_next_post();
                        ?>
                        
                        <?php if ($prev) : ?>
                            <a href="<?php echo get_permalink($prev); ?>" class="game-nav-link prev">
                                <span class="game-nav-arrow">←</span>
                                <span class="game-nav-label">Previous game</span>
                                <span class="game-nav-title"><?php echo esc_html($prev->post_title); ?></span>
                            </a>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>
                        
                        <?php if ($next) : ?>
                            <a href="<?php echo get_permalink($next); ?>" class="game-nav-link next">
                                <span class="game-nav-arrow">→</span>
                                <span class="game-nav-label">Next game</span>
                                <span class="game-nav-title"><?php echo esc_html($next->post_title); ?></span>
                            </a>
                        <?php endif; ?>
                    </nav>
                    
                    <!-- Back Link -->
                    <div class="back-to-games">
                        <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="btn btn--secondary">
                            <span class="btn-icon">←</span>
                            Back to all games
                        </a>
                    </div>
                    
                </div>
            </div>
        </article>
        
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
