<?php
/**
 * Single Game Template
 * 
 * @package AriaJet
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
            $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
            $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
            ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('game-showcase'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    
                    <div class="game-meta">
                        <?php if ($game_status) : ?>
                            <span class="game-badge <?php echo esc_attr($game_status); ?>">
                                <?php echo esc_html(ucfirst($game_status)); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($game_type) : ?>
                            <span class="game-badge"><?php echo esc_html(ucfirst($game_type)); ?></span>
                        <?php endif; ?>
                    </div>
                </header>
                
                <?php if ($game_url) : ?>
                    <div class="game-embed" data-game-url="<?php echo esc_url($game_url); ?>">
                        <!-- Game will be embedded here via JavaScript -->
                    </div>
                    
                    <div class="game-actions">
                        <a href="<?php echo esc_url($game_url); ?>" class="game-play-button" target="_blank">
                            <?php _e('Play Full Screen', 'ariajet'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
            
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php
get_footer();





