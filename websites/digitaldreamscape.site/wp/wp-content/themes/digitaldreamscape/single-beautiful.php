<?php
/**
 * Beautiful Single Post Template
 * 
 * Modern, elegant single post design matching blog template
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

get_header(); ?>

<main class="site-main beautiful-single-main">
    <div class="beautiful-single-container">
        <?php
        while (have_posts()) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('beautiful-single-article'); ?>>
                <!-- Episode Header -->
                <header class="beautiful-single-header">
                    <div class="beautiful-single-badges">
                        <span class="beautiful-single-badge episode">[EPISODE]</span>
                        <?php
                        $categories = get_the_category();
                        if (!empty($categories)) {
                            $category = $categories[0];
                            echo '<span class="beautiful-single-badge questline">[QUESTLINE] ' . esc_html($category->name) . '</span>';
                        }
                        ?>
                    </div>
                    
                    <h1 class="beautiful-single-title"><?php the_title(); ?></h1>
                    
                    <div class="beautiful-single-meta">
                        <div class="beautiful-single-author">
                            <?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
                            <div class="beautiful-single-author-info">
                                <span class="beautiful-single-author-name"><?php the_author(); ?></span>
                                <span class="beautiful-single-author-role">[Shadow Sovereign]</span>
                            </div>
                        </div>
                        
                        <div class="beautiful-single-date">
                            <span class="beautiful-single-badge timeline">[TIMELINE]</span>
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('F j, Y'); ?></time>
                        </div>
                    </div>
                    
                    <div class="beautiful-single-stats">
                        <span class="beautiful-single-stat">
                            <span class="beautiful-single-stat-icon">⏱</span>
                            <?php
                            $content = get_the_content();
                            $word_count = str_word_count(strip_tags($content));
                            $reading_time = ceil($word_count / 200);
                            echo $reading_time . ' min';
                            ?>
                        </span>
                        <span class="beautiful-single-stat">
                            <span class="beautiful-single-stat-icon">📖</span>
                            <?php echo $word_count; ?> words
                        </span>
                        <span class="beautiful-single-stat">
                            <span class="beautiful-single-stat-icon">🎯</span>
                            <span class="beautiful-single-badge canon">[CANON]</span>
                        </span>
                    </div>
                </header>
                
                <!-- Narrative Context -->
                <div class="beautiful-single-context">
                    <div class="beautiful-single-context-card">
                        <div class="beautiful-single-context-badge">[WORLD-STATE]</div>
                        <p class="beautiful-single-context-text">This episode becomes part of the persistent narrative</p>
                    </div>
                    <div class="beautiful-single-context-card">
                        <div class="beautiful-single-context-badge">[NARRATIVE MODE: ACTIVE]</div>
                        <p class="beautiful-single-context-text"><strong>Digital Dreamscape</strong> is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. This post is part of the persistent simulation of self + system.</p>
                    </div>
                </div>
                
                <!-- Featured Image -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="beautiful-single-featured-image">
                        <?php the_post_thumbnail('large', array('class' => 'beautiful-single-image')); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Post Content -->
                <div class="beautiful-single-content">
                    <?php
                    // Get and process content
                    $content = get_the_content();
                    $content = apply_filters('the_content', $content);
                    
                    // Extract headings from processed content for TOC
                    preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h[2-3]>/i', $content, $headings);
                    
                    // Add IDs to headings for TOC navigation
                    if (!empty($headings[0])) {
                        $heading_count = 0;
                        foreach ($headings[0] as $index => $full_heading) {
                            $heading_count++;
                            $heading_id = 'heading-' . $heading_count;
                            // Add ID to heading if it doesn't already have one
                            if (strpos($full_heading, 'id=') === false) {
                                $content = preg_replace(
                                    '/' . preg_quote($full_heading, '/') . '/i',
                                    preg_replace('/<h([2-3])([^>]*)>/i', '<h$1$2 id="' . esc_attr($heading_id) . '">', $full_heading),
                                    $content,
                                    1
                                );
                            }
                        }
                    }
                    
                    echo $content;
                    ?>
                </div>
                
                <!-- Table of Contents (for long posts, placed after content generation) -->
                <?php
                $word_count = str_word_count(strip_tags($content));
                if ($word_count > 500 && !empty($headings[0])) :
                    ?>
                    <div class="beautiful-single-toc beautiful-single-toc-bottom">
                        <div class="beautiful-single-toc-label">[TABLE OF CONTENTS]</div>
                        <nav class="beautiful-single-toc-list">
                            <?php
                            $heading_count = 0;
                            foreach ($headings[2] as $index => $heading_text) {
                                $heading_count++;
                                $heading_id = 'heading-' . $heading_count;
                                $level = $headings[1][$index];
                                $heading_text_clean = strip_tags($heading_text);
                                echo '<a href="#' . esc_attr($heading_id) . '" class="beautiful-single-toc-link toc-level-' . esc_attr($level) . '">' . esc_html($heading_text_clean) . '</a>';
                            }
                            ?>
                        </nav>
                    </div>
                <?php endif; ?>
                
                <!-- Episode Footer -->
                <footer class="beautiful-single-footer">
                    <div class="beautiful-single-episode-complete">
                        <div class="beautiful-single-episode-badge">[EPISODE COMPLETE]</div>
                        <p class="beautiful-single-episode-text">This episode has been logged to memory. Identity state updated. Questline progression recorded.</p>
                    </div>
                    
                    <!-- Share Buttons -->
                    <div class="beautiful-single-share">
                        <div class="beautiful-single-share-label">[SHARE EPISODE]</div>
                        <div class="beautiful-single-share-buttons">
                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" 
                               rel="noopener" 
                               class="beautiful-single-share-button twitter">
                                <span>𝕏</span> Twitter
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" 
                               rel="noopener" 
                               class="beautiful-single-share-button facebook">
                                <span>📘</span> Facebook
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" 
                               rel="noopener" 
                               class="beautiful-single-share-button linkedin">
                                <span>💼</span> LinkedIn
                            </a>
                        </div>
                    </div>
                    
                    <!-- Author Bio -->
                    <div class="beautiful-single-author-bio">
                        <div class="beautiful-single-author-bio-label">[AUTHOR]</div>
                        <div class="beautiful-single-author-bio-content">
                            <?php echo get_avatar(get_the_author_meta('ID'), 64); ?>
                            <div class="beautiful-single-author-bio-text">
                                <h3 class="beautiful-single-author-bio-name"><?php the_author(); ?></h3>
                                <p class="beautiful-single-author-bio-description">Building Digital Dreamscape in public. One episode at a time.</p>
                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="beautiful-single-author-bio-link">
                                    View all posts by <?php the_author(); ?> →
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Newsletter CTA -->
                    <div class="beautiful-single-newsletter">
                        <div class="beautiful-single-newsletter-card">
                            <div class="beautiful-single-newsletter-icon">📧</div>
                            <h3 class="beautiful-single-newsletter-title">Subscribe for the Next Episode</h3>
                            <p class="beautiful-single-newsletter-text">Get notified when new episodes drop. Join the Digital Dreamscape narrative.</p>
                            <form class="beautiful-single-newsletter-form" action="#" method="post">
                                <input type="email" 
                                       name="email" 
                                       placeholder="Enter your email" 
                                       required 
                                       class="beautiful-single-newsletter-input">
                                <button type="submit" class="beautiful-single-newsletter-button">
                                    Subscribe →
                                </button>
                            </form>
                            <p class="beautiful-single-newsletter-disclaimer">No spam. Unsubscribe anytime.</p>
                        </div>
                    </div>
                    
                    <!-- Related Posts -->
                    <?php
                    $related_posts = get_posts(array(
                        'category__in' => wp_get_post_categories(get_the_ID()),
                        'numberposts' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if (!empty($related_posts)) :
                        ?>
                        <div class="beautiful-single-related">
                            <div class="beautiful-single-related-label">[RELATED EPISODES]</div>
                            <div class="beautiful-single-related-grid">
                                <?php foreach ($related_posts as $related_post) : ?>
                                    <article class="beautiful-single-related-card">
                                        <?php if (has_post_thumbnail($related_post->ID)) : ?>
                                            <a href="<?php echo get_permalink($related_post->ID); ?>" class="beautiful-single-related-image-link">
                                                <?php echo get_the_post_thumbnail($related_post->ID, 'medium', array('class' => 'beautiful-single-related-image')); ?>
                                            </a>
                                        <?php endif; ?>
                                        <div class="beautiful-single-related-content">
                                            <time class="beautiful-single-related-date" datetime="<?php echo get_the_date('c', $related_post->ID); ?>">
                                                <?php echo get_the_date('M j, Y', $related_post->ID); ?>
                                            </time>
                                            <h4 class="beautiful-single-related-title">
                                                <a href="<?php echo get_permalink($related_post->ID); ?>" class="beautiful-single-related-link">
                                                    <?php echo get_the_title($related_post->ID); ?>
                                                </a>
                                            </h4>
                                            <p class="beautiful-single-related-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt($related_post->ID), 20); ?>
                                            </p>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Navigation -->
                    <nav class="beautiful-single-navigation">
                        <?php
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        ?>
                        <?php if ($prev_post) : ?>
                            <div class="beautiful-single-nav-prev">
                                <span class="beautiful-single-nav-label">← Previous Episode</span>
                                <a href="<?php echo get_permalink($prev_post); ?>" class="beautiful-single-nav-link">
                                    <?php echo get_the_title($prev_post); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($next_post) : ?>
                            <div class="beautiful-single-nav-next">
                                <span class="beautiful-single-nav-label">Next Episode →</span>
                                <a href="<?php echo get_permalink($next_post); ?>" class="beautiful-single-nav-link">
                                    <?php echo get_the_title($next_post); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </nav>
                </footer>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>

