<?php
/**
 * Main Template File
 * 
 * The main template file for AriaJet Cosmic theme.
 * Displays a cosmic-styled homepage with games and posts.
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        
        <?php if (is_home() && !is_paged()) : ?>
        <!-- Hero Section -->
        <section class="cosmic-hero">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="hero-emoji">🚀</span>
                    <?php _e("Welcome to Aria's Cosmic World", 'ariajet-cosmic'); ?>
                </h1>
                <p class="hero-subtitle">
                    <?php _e('Explore amazing 2D games, creative projects, and wild adventures in the cosmic universe!', 'ariajet-cosmic'); ?>
                </p>
                <div class="hero-buttons">
                    <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="cosmic-button">
                        <span class="button-icon">🎮</span>
                        <?php _e('Play Games', 'ariajet-cosmic'); ?>
                    </a>
                    <a href="#latest-posts" class="cosmic-button secondary">
                        <span class="button-icon">📖</span>
                        <?php _e('Latest Updates', 'ariajet-cosmic'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Floating Space Elements -->
            <div class="hero-decorations">
                <div class="floating-planet planet-1">🪐</div>
                <div class="floating-planet planet-2">🌙</div>
                <div class="floating-planet planet-3">⭐</div>
                <div class="floating-planet planet-4">🌟</div>
            </div>
        </section>
        
        <!-- Featured Games Section -->
        <section class="featured-games">
            <h2 class="section-title">
                <span class="title-icon">🎮</span>
                <?php _e("Aria's Games", 'ariajet-cosmic'); ?>
            </h2>
            
            <div class="game-grid">
                <?php
                $games = new WP_Query(array(
                    'post_type' => 'game',
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                
                if ($games->have_posts()) :
                    while ($games->have_posts()) :
                        $games->the_post();
                        $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
                        $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
                        $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
                        ?>
                        <article class="game-card cosmic-card" data-game-type="<?php echo esc_attr($game_type); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="game-thumbnail">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                    <div class="game-overlay">
                                        <span class="play-icon">▶</span>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="game-thumbnail placeholder">
                                    <div class="placeholder-icon">🎮</div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="game-info">
                                <h3 class="game-title"><?php the_title(); ?></h3>
                                
                                <div class="game-meta">
                                    <?php if ($game_status) : ?>
                                        <span class="game-badge status-<?php echo esc_attr($game_status); ?>">
                                            <?php echo esc_html(ucfirst($game_status)); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($game_type) : ?>
                                        <span class="game-badge type">
                                            <?php echo esc_html(ucfirst($game_type)); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="game-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <div class="game-actions">
                                    <?php if ($game_url) : ?>
                                        <a href="<?php echo esc_url($game_url); ?>" class="cosmic-button accent" target="_blank">
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
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-games cosmic-card">
                        <div class="no-games-icon">🚀</div>
                        <h3><?php _e('Games Coming Soon!', 'ariajet-cosmic'); ?></h3>
                        <p><?php _e("Aria is working on some amazing games. Check back soon!", 'ariajet-cosmic'); ?></p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            
            <div class="section-cta">
                <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="cosmic-button">
                    <?php _e('View All Games', 'ariajet-cosmic'); ?>
                    <span class="arrow">→</span>
                </a>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Latest Posts Section -->
        <section id="latest-posts" class="latest-posts">
            <?php if (is_home() && !is_paged()) : ?>
                <h2 class="section-title">
                    <span class="title-icon">📝</span>
                    <?php _e('Latest Updates', 'ariajet-cosmic'); ?>
                </h2>
            <?php endif; ?>
            
            <div class="posts-grid">
                <?php
                if (have_posts()) :
                    while (have_posts()) :
                        the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card cosmic-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <header class="entry-header">
                                    <h2 class="entry-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="entry-meta">
                                        <span class="post-date">
                                            <?php echo get_the_date(); ?>
                                        </span>
                                    </div>
                                </header>
                                
                                <div class="entry-summary">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <footer class="entry-footer">
                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                        <?php _e('Read More', 'ariajet-cosmic'); ?>
                                        <span class="arrow">→</span>
                                    </a>
                                </footer>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    
                    // Pagination
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '<span class="nav-arrow">←</span> ' . __('Previous', 'ariajet-cosmic'),
                        'next_text' => __('Next', 'ariajet-cosmic') . ' <span class="nav-arrow">→</span>',
                    ));
                    
                else :
                    ?>
                    <div class="no-content cosmic-card">
                        <div class="no-content-icon">🌌</div>
                        <h2><?php _e('Nothing here yet!', 'ariajet-cosmic'); ?></h2>
                        <p><?php _e("The cosmic universe is still being created. Check back soon for Aria's amazing adventures!", 'ariajet-cosmic'); ?></p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
        </section>
        
    </div>
</main>

<?php
get_footer();
