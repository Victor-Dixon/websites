<?php
/**
 * Game Archive Template
 * 
 * A clean, spacious game gallery with subtle interactions.
 * 
 * @package AriaJet_Studio
 */

get_header();
?>

<main id="main" class="site-main">
    <section class="section">
        <div class="container">
            
            <!-- Page Header -->
            <header class="game-archive-header">
                <p class="section-eyebrow">
                    <span class="icon-box icon-box--blush">🎮</span>
                    Game Collection
                </p>
                <h1 class="game-archive-title">
                    Games I've made
                </h1>
                <p class="game-archive-intro">
                    I love creating games — each one is a little world to explore. 
                    Pick one and have fun!
                </p>
            </header>
            
            <!-- Filters -->
            <div class="game-filters">
                <button class="game-filter active" data-filter="all">All games</button>
                <button class="game-filter" data-filter="2d">2D</button>
                <button class="game-filter" data-filter="adventure">Adventure</button>
                <button class="game-filter" data-filter="puzzle">Puzzle</button>
                <button class="game-filter" data-filter="platformer">Platformer</button>
                <button class="game-filter" data-filter="arcade">Arcade</button>
            </div>
            
            <!-- Games Grid -->
            <?php if (have_posts()) : ?>
            <div class="games-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    
                    $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
                    $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
                    $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
                    ?>
                    <article class="card game-card reveal" data-game-type="<?php echo esc_attr($game_type); ?>">
                        <div class="card-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            <?php else : ?>
                                <div class="card-placeholder">
                                    <span class="placeholder-icon">🎮</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($game_status) : ?>
                                <span class="game-status tag tag--<?php echo $game_status === 'published' ? 'sage' : ($game_status === 'beta' ? 'honey' : 'lavender'); ?>">
                                    <?php 
                                    $labels = array(
                                        'published' => '✓ Ready',
                                        'beta' => '🧪 Beta',
                                        'development' => '🔨 WIP',
                                    );
                                    echo esc_html($labels[$game_status] ?? ucfirst($game_status));
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-body">
                            <h3 class="card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php if ($game_type) : ?>
                                <span class="tag tag--coral"><?php echo esc_html(ucfirst($game_type)); ?></span>
                            <?php endif; ?>
                            
                            <p class="card-text">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </p>
                            
                            <div class="card-footer">
                                <?php if ($game_url) : ?>
                                    <a href="<?php echo esc_url($game_url); ?>" class="btn btn--accent" target="_blank">
                                        Play now →
                                    </a>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="btn btn--ghost">
                                    Details
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>
            
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '← Previous',
                'next_text' => 'Next →',
            ));
            ?>
            
            <?php else : ?>
            <div class="games-empty reveal">
                <span class="games-empty-icon">🚀</span>
                <h3>Games coming soon!</h3>
                <p>I'm working on some really cool stuff. Check back soon!</p>
            </div>
            <?php endif; ?>
            
        </div>
    </section>
</main>

<?php
get_footer();
