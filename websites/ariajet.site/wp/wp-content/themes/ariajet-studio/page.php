<?php
/**
 * Page Template
 * 
 * Clean, spacious page layout.
 * 
 * @package AriaJet_Studio
 */

get_header();
?>

<main id="main" class="site-main">
    <section class="section">
        <div class="container container--narrow">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                    <header class="page-header reveal">
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </header>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="page-featured-image reveal">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="entry-content reveal">
                        <?php 
                        the_content();
                        
                        wp_link_pages(array(
                            'before' => '<div class="page-links">',
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </div>
    </section>
</main>

<style>
.page-content {
    padding: var(--space-16) 0;
}

.page-header {
    text-align: center;
    margin-bottom: var(--space-12);
}

.page-title {
    font-size: var(--text-5xl);
}

.page-featured-image {
    margin: 0 calc(-1 * var(--space-12)) var(--space-10);
    border-radius: var(--radius-xl);
    overflow: hidden;
}

.page-featured-image img {
    width: 100%;
    height: auto;
}

.entry-content {
    font-size: var(--text-lg);
    line-height: var(--leading-relaxed);
}

.entry-content h2,
.entry-content h3,
.entry-content h4 {
    margin-top: var(--space-12);
    margin-bottom: var(--space-5);
}

.entry-content p {
    margin-bottom: var(--space-6);
}

.entry-content ul,
.entry-content ol {
    margin: var(--space-6) 0;
    padding-left: var(--space-8);
}

.entry-content li {
    margin-bottom: var(--space-3);
}

.entry-content blockquote {
    margin: var(--space-8) 0;
    padding: var(--space-6) var(--space-8);
    background: var(--blush);
    border-radius: var(--radius-lg);
    border-left: 4px solid var(--coral);
    font-style: italic;
}

.entry-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
    margin: var(--space-6) 0;
}

@media (max-width: 768px) {
    .page-title {
        font-size: var(--text-3xl);
    }
    
    .page-featured-image {
        margin-left: 0;
        margin-right: 0;
    }
}
</style>

<?php
get_footer();
