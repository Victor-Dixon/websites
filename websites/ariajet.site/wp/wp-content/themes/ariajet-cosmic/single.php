<?php
/**
 * Single Post Template
 * 
 * Displays single blog posts with cosmic styling.
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main single-post">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-content cosmic-card'); ?>>
                <header class="entry-header">
                    <div class="entry-meta-top">
                        <span class="post-date">
                            <span class="date-icon">📅</span>
                            <?php echo get_the_date(); ?>
                        </span>
                        
                        <?php if (has_category()) : ?>
                            <span class="post-categories">
                                <?php the_category(', '); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    
                    <?php if (has_excerpt()) : ?>
                        <div class="entry-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    <?php endif; ?>
                </header>
                
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-featured-image">
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
                
                <footer class="entry-footer">
                    <?php if (has_tag()) : ?>
                        <div class="post-tags">
                            <span class="tags-label"><?php _e('Tags:', 'ariajet-cosmic'); ?></span>
                            <?php the_tags('', ', ', ''); ?>
                        </div>
                    <?php endif; ?>
                </footer>
            </article>
            
            <!-- Post Navigation -->
            <nav class="post-navigation">
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                ?>
                
                <?php if ($prev_post) : ?>
                    <a href="<?php echo get_permalink($prev_post); ?>" class="post-nav-link prev">
                        <span class="nav-arrow">←</span>
                        <span class="nav-label"><?php _e('Previous Post', 'ariajet-cosmic'); ?></span>
                        <span class="nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
                    </a>
                <?php endif; ?>
                
                <?php if ($next_post) : ?>
                    <a href="<?php echo get_permalink($next_post); ?>" class="post-nav-link next">
                        <span class="nav-arrow">→</span>
                        <span class="nav-label"><?php _e('Next Post', 'ariajet-cosmic'); ?></span>
                        <span class="nav-title"><?php echo esc_html($next_post->post_title); ?></span>
                    </a>
                <?php endif; ?>
            </nav>
            
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
.single-post .post-content {
    padding: var(--space-10);
    max-width: 800px;
    margin: 0 auto var(--space-8);
}

.entry-meta-top {
    display: flex;
    gap: var(--space-4);
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: var(--space-4);
}

.post-date {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    font-size: var(--text-sm);
    color: var(--text-muted);
}

.post-categories a {
    display: inline-block;
    padding: var(--space-1) var(--space-3);
    font-size: var(--text-xs);
    font-weight: 600;
    color: var(--neon-cyan);
    background: rgba(0, 255, 247, 0.1);
    border: 1px solid rgba(0, 255, 247, 0.2);
    border-radius: var(--radius-full);
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all var(--transition-normal);
}

.post-categories a:hover {
    background: rgba(0, 255, 247, 0.2);
    border-color: var(--neon-cyan);
}

.single-post .entry-title {
    font-size: var(--text-4xl);
    margin-bottom: var(--space-6);
    line-height: 1.2;
}

.entry-excerpt {
    font-size: var(--text-lg);
    color: var(--text-secondary);
    font-style: italic;
    padding-bottom: var(--space-6);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: var(--space-6);
}

.post-featured-image {
    margin: 0 calc(-1 * var(--space-10)) var(--space-8);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.post-featured-image img {
    width: 100%;
    height: auto;
}

.entry-footer {
    margin-top: var(--space-8);
    padding-top: var(--space-6);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.post-tags {
    display: flex;
    gap: var(--space-2);
    flex-wrap: wrap;
    align-items: center;
}

.tags-label {
    font-size: var(--text-sm);
    color: var(--text-muted);
    margin-right: var(--space-2);
}

.post-tags a {
    display: inline-block;
    padding: var(--space-1) var(--space-3);
    font-size: var(--text-xs);
    color: var(--text-secondary);
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-sm);
    text-decoration: none;
    transition: all var(--transition-normal);
}

.post-tags a:hover {
    color: var(--neon-pink);
    border-color: var(--neon-pink);
    background: rgba(255, 45, 149, 0.1);
}

/* Post Navigation */
.post-navigation {
    display: flex;
    justify-content: space-between;
    gap: var(--space-6);
    max-width: 800px;
    margin: 0 auto var(--space-10);
}

.post-nav-link {
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

.post-nav-link:hover {
    border-color: var(--neon-cyan);
    background: rgba(0, 255, 247, 0.1);
    transform: translateY(-3px);
}

.post-nav-link.next {
    text-align: right;
    margin-left: auto;
}

.post-nav-link .nav-arrow {
    font-size: var(--text-2xl);
    color: var(--neon-cyan);
}

.post-nav-link .nav-label {
    font-size: var(--text-xs);
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.post-nav-link .nav-title {
    font-family: var(--font-display);
    font-size: var(--text-base);
    font-weight: 600;
    color: var(--text-primary);
}

@media (max-width: 768px) {
    .single-post .post-content {
        padding: var(--space-6);
    }
    
    .single-post .entry-title {
        font-size: var(--text-2xl);
    }
    
    .post-featured-image {
        margin: 0 calc(-1 * var(--space-6)) var(--space-6);
    }
    
    .post-navigation {
        flex-direction: column;
    }
    
    .post-nav-link {
        max-width: 100%;
    }
    
    .post-nav-link.next {
        text-align: left;
    }
}
</style>

<?php
get_footer();
