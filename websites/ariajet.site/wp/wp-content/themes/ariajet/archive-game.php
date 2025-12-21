<?php
/**
 * Game Archive Template
 * 
 * @package AriaJet
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title"><?php _e('Aria\'s 2D Games', 'ariajet'); ?></h1>
            <p class="page-description"><?php _e('Explore Aria\'s collection of amazing 2D games!', 'ariajet'); ?></p>
        </header>
        
        <div class="game-filters">
            <button class="game-filter" data-filter="all"><?php _e('All Games', 'ariajet'); ?></button>
            <button class="game-filter" data-filter="2d"><?php _e('2D Games', 'ariajet'); ?></button>
            <button class="game-filter" data-filter="adventure"><?php _e('Adventure', 'ariajet'); ?></button>
            <button class="game-filter" data-filter="survival"><?php _e('Survival', 'ariajet'); ?></button>
        </div>
        
        <div class="game-grid">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    $game_url = get_post_meta(get_the_ID(), '_ariajet_game_url', true);
                    $game_type = get_post_meta(get_the_ID(), '_ariajet_game_type', true);
                    $game_status = get_post_meta(get_the_ID(), '_ariajet_game_status', true);
                    ?>
                    <div class="game-card" data-game-type="<?php echo esc_attr($game_type); ?>" data-game-url="<?php echo esc_url($game_url); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php endif; ?>
                        
                        <h3><?php the_title(); ?></h3>
                        
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
                        
                        <div class="game-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <?php if ($game_url) : ?>
                            <a href="<?php echo esc_url($game_url); ?>" class="game-play-button" target="_blank">
                                <?php _e('Play Game', 'ariajet'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php
                endwhile;
            else :
                ?>
                <div class="no-games">
                    <p><?php _e('No games found. Check back soon!', 'ariajet'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>
        
        <?php
        // Pagination
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => __('&laquo; Previous', 'ariajet'),
            'next_text' => __('Next &raquo;', 'ariajet'),
        ));
        ?>
    </div>
</main>

<?php
get_footer();





