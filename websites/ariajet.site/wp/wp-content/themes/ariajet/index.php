<?php
/**
 * Main Template File
 * 
 * @package AriaJet
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        <?php if (is_home() && !is_paged()) : ?>
            <header class="page-header">
                <h1 class="page-title"><?php echo esc_html(get_bloginfo('name')); ?></h1>
                <?php $tagline = get_bloginfo('description', 'display'); ?>
                <?php if (!empty($tagline)) : ?>
                    <p class="page-description"><?php echo esc_html($tagline); ?></p>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('game-showcase'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>
                    
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            endwhile;
        else :
            ?>
            <div class="no-content">
                <h2><?php _e('Nothing here yet!', 'ariajet'); ?></h2>
                <p><?php _e('Check back soon for Aria\'s amazing 2D games!', 'ariajet'); ?></p>
            </div>
            <?php
        endif;
        ?>
    </div>
</main>

<?php
get_footer();





