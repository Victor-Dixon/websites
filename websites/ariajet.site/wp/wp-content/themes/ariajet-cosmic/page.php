<?php
/**
 * Page Template
 * 
 * Displays standard WordPress pages with cosmic styling.
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main page-template">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page-content cosmic-card'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title page-title">
                        <?php the_title(); ?>
                    </h1>
                </header>
                
                <?php if (has_post_thumbnail()) : ?>
                    <div class="page-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
                
                <div class="entry-content">
                    <?php 
                    the_content();
                    
                    wp_link_pages(array(
                        'before' => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'ariajet-cosmic') . '</span>',
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
            </article>
            
            <?php
            // If comments are open or there are comments, load up the comment template.
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            
        endwhile;
        ?>
    </div>
</main>

<style>
.page-content {
    padding: var(--space-12);
    max-width: 900px;
    margin: 0 auto;
}

.page-content .entry-title {
    text-align: center;
    margin-bottom: var(--space-10);
    font-size: var(--text-5xl);
    background: linear-gradient(135deg, var(--text-primary) 0%, var(--neon-cyan) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-thumbnail {
    margin: 0 calc(-1 * var(--space-12)) var(--space-8);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.page-thumbnail img {
    width: 100%;
    height: auto;
}

@media (max-width: 768px) {
    .page-content {
        padding: var(--space-6);
    }
    
    .page-content .entry-title {
        font-size: var(--text-3xl);
    }
    
    .page-thumbnail {
        margin: 0 calc(-1 * var(--space-6)) var(--space-6);
    }
}
</style>

<?php
get_footer();
