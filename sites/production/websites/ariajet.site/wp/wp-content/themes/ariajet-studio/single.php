<?php
/**
 * Single Post Template
 * 
 * Clean, readable blog post layout.
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
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-content'); ?>>
                    
                    <header class="post-header reveal">
                        <div class="post-meta-top">
                            <time datetime="<?php echo get_the_date('c'); ?>" class="tag">
                                <?php echo get_the_date('F j, Y'); ?>
                            </time>
                            
                            <?php if (has_category()) : ?>
                                <span class="post-categories">
                                    <?php the_category(', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        
                        <?php if (has_excerpt()) : ?>
                            <p class="post-excerpt lead"><?php echo get_the_excerpt(); ?></p>
                        <?php endif; ?>
                    </header>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-featured-image reveal">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="entry-content reveal">
                        <?php the_content(); ?>
                    </div>
                    
                    <?php if (has_tag()) : ?>
                        <footer class="post-footer reveal">
                            <div class="post-tags">
                                <span class="tags-label">Tagged:</span>
                                <?php the_tags('', '', ''); ?>
                            </div>
                        </footer>
                    <?php endif; ?>
                    
                </article>
                
                <!-- Post Navigation -->
                <nav class="post-navigation reveal">
                    <?php
                    $prev = get_previous_post();
                    $next = get_next_post();
                    ?>
                    
                    <?php if ($prev) : ?>
                        <a href="<?php echo get_permalink($prev); ?>" class="post-nav-link prev">
                            <span class="post-nav-arrow">←</span>
                            <span class="post-nav-label">Previous</span>
                            <span class="post-nav-title"><?php echo esc_html($prev->post_title); ?></span>
                        </a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                    
                    <?php if ($next) : ?>
                        <a href="<?php echo get_permalink($next); ?>" class="post-nav-link next">
                            <span class="post-nav-arrow">→</span>
                            <span class="post-nav-label">Next</span>
                            <span class="post-nav-title"><?php echo esc_html($next->post_title); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>
                
                <?php
            endwhile;
            ?>
        </div>
    </section>
</main>

<style>
.single-post-content {
    padding: var(--space-16) 0 0;
}

.post-header {
    text-align: center;
    margin-bottom: var(--space-10);
}

.post-meta-top {
    display: flex;
    justify-content: center;
    gap: var(--space-3);
    margin-bottom: var(--space-5);
}

.post-categories a {
    color: var(--coral);
    font-size: var(--text-sm);
    font-weight: 500;
}

.post-title {
    font-size: var(--text-5xl);
    margin-bottom: var(--space-5);
}

.post-excerpt {
    max-width: 540px;
    margin: 0 auto;
}

.post-featured-image {
    margin: 0 calc(-1 * var(--space-12)) var(--space-10);
    border-radius: var(--radius-xl);
    overflow: hidden;
}

.post-featured-image img {
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

.post-footer {
    margin-top: var(--space-12);
    padding-top: var(--space-8);
    border-top: 1px solid var(--border);
}

.post-tags {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-2);
    align-items: center;
}

.tags-label {
    font-size: var(--text-sm);
    color: var(--ink-muted);
    margin-right: var(--space-2);
}

.post-tags a {
    display: inline-block;
    padding: var(--space-1) var(--space-3);
    font-size: var(--text-xs);
    color: var(--ink-light);
    background: var(--cream-dark);
    border-radius: var(--radius-sm);
}

.post-tags a:hover {
    color: var(--coral);
    background: var(--blush);
}

/* Post Navigation */
.post-navigation {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-6);
    margin-top: var(--space-16);
    padding-top: var(--space-10);
    border-top: 1px solid var(--border);
}

.post-nav-link {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
    padding: var(--space-6);
    background: var(--soft-white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    text-decoration: none;
    transition: all var(--duration-normal) var(--ease-smooth);
}

.post-nav-link:hover {
    border-color: var(--border-hover);
    box-shadow: 0 4px 20px var(--shadow-soft);
    transform: translateY(-2px);
}

.post-nav-link.next {
    text-align: right;
}

.post-nav-arrow {
    font-size: var(--text-xl);
    color: var(--coral);
}

.post-nav-label {
    font-size: var(--text-xs);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: var(--tracking-wider);
    color: var(--ink-subtle);
}

.post-nav-title {
    font-family: var(--font-display);
    font-size: var(--text-lg);
    font-weight: 500;
    color: var(--ink);
}

@media (max-width: 768px) {
    .post-title {
        font-size: var(--text-3xl);
    }
    
    .post-featured-image {
        margin-left: 0;
        margin-right: 0;
    }
    
    .post-navigation {
        grid-template-columns: 1fr;
    }
    
    .post-nav-link.next {
        text-align: left;
    }
}
</style>

<?php
get_footer();
