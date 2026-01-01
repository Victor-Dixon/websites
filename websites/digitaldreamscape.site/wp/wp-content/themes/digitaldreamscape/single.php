<?php
/**
 * Single Post Template
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="container">
        <div class="content-area single-post-area">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                    
                    <!-- Post Header - Digital Dreamscape Narrative Style -->
                    <header class="post-header dreamscape-header">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-featured-image dreamscape-hero">
                                <?php the_post_thumbnail('large', array('class' => 'featured-img')); ?>
                                <div class="dreamscape-overlay">
                                    <div class="narrative-marker">[EPISODE]</div>
                                </div>
                            </div>
                        <?php else : ?>
                            <!-- Default Dreamscape Hero Background -->
                            <div class="post-featured-image dreamscape-hero dreamscape-default-hero">
                                <div class="dreamscape-pattern"></div>
                                <div class="dreamscape-overlay">
                                    <div class="narrative-marker">[EPISODE]</div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-meta-header dreamscape-meta">
                            <!-- Questline / Category Tags -->
                            <div class="post-categories dreamscape-questlines">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    foreach ($categories as $category) {
                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="questline-tag">[QUESTLINE] ' . esc_html($category->name) . '</a>';
                                    }
                                } else {
                                    echo '<span class="questline-tag">[QUESTLINE] Uncategorized</span>';
                                }
                                ?>
                            </div>
                            
                            <!-- Title as Narrative Event -->
                            <h1 class="post-title dreamscape-title"><?php the_title(); ?></h1>
                            
                            <!-- Identity & State Meta -->
                            <div class="post-meta dreamscape-identity">
                                <div class="identity-avatar">
                                    <span class="avatar-frame">
                                        <?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
                                    </span>
                                    <div class="identity-info">
                                        <span class="identity-name"><?php the_author(); ?></span>
                                        <span class="identity-title">[Shadow Sovereign]</span>
                                        <time class="post-date dreamscape-timestamp" datetime="<?php echo get_the_date('c'); ?>">
                                            [TIMELINE] <?php echo get_the_date('F j, Y'); ?>
                                        </time>
                                    </div>
                                </div>
                                
                                <div class="post-stats dreamscape-stats">
                                    <?php
                                    $content = get_post_field('post_content', get_the_ID());
                                    $word_count = str_word_count(strip_tags($content));
                                    $reading_time = ceil($word_count / 200);
                                    ?>
                                    <div class="stat-item">
                                        <span class="stat-icon">⏱</span>
                                        <span class="stat-value"><?php echo $reading_time; ?> min</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-icon">📖</span>
                                        <span class="stat-value"><?php echo $word_count; ?> words</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-icon">🎯</span>
                                        <span class="stat-value">[CANON]</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Narrative Context Banner -->
                            <div class="dreamscape-context">
                                <div class="context-badge">
                                    <span class="context-label">[WORLD-STATE]</span>
                                    <span class="context-text">This episode becomes part of the persistent narrative</span>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Post Content - Narrative Format -->
                    <div class="post-content dreamscape-narrative">
                        <!-- Narrative Introduction -->
                        <div class="narrative-intro">
                            <div class="intro-badge">[NARRATIVE MODE: ACTIVE]</div>
                            <p class="intro-text">
                                <strong>Digital Dreamscape</strong> is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. This post is part of the persistent simulation of self + system.
                            </p>
                        </div>
                        
                        <?php
                        the_content();
                        
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'digitaldreamscape'),
                            'after'  => '</div>',
                        ));
                        ?>
                        
                        <!-- Narrative Conclusion -->
                        <div class="narrative-outro">
                            <div class="outro-badge">[EPISODE COMPLETE]</div>
                            <p class="outro-text">
                                This episode has been logged to memory. Identity state updated. Questline progression recorded.
                            </p>
                        </div>
                    </div>

                    <!-- Post Tags -->
                    <?php
                    $tags = get_the_tags();
                    if ($tags) :
                        ?>
                        <div class="post-tags">
                            <span class="tags-label">Tags:</span>
                            <?php
                            foreach ($tags as $tag) {
                                echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="tag-link">' . esc_html($tag->name) . '</a>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Footer -->
                    <footer class="post-footer">
                        <div class="post-share">
                            <span class="share-label">Share:</span>
                            <div class="share-buttons">
                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="share-btn share-twitter">
                                    Twitter
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="share-btn share-facebook">
                                    Facebook
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="share-btn share-linkedin">
                                    LinkedIn
                                </a>
                            </div>
                        </div>
                    </footer>

                    <!-- Author Bio -->
                    <div class="author-bio">
                        <div class="author-bio-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                        </div>
                        <div class="author-bio-content">
                            <h3 class="author-bio-name"><?php the_author(); ?></h3>
                            <?php if (get_the_author_meta('description')) : ?>
                                <p class="author-bio-description"><?php echo get_the_author_meta('description'); ?></p>
                            <?php endif; ?>
                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-bio-link">
                                View all posts by <?php the_author(); ?> →
                            </a>
                        </div>
                    </div>

                    <!-- Post Navigation -->
                    <nav class="post-navigation">
                        <div class="nav-previous">
                            <?php
                            $prev_post = get_previous_post();
                            if ($prev_post) :
                                ?>
                                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="nav-link">
                                    <span class="nav-label">← Previous Post</span>
                                    <span class="nav-title"><?php echo get_the_title($prev_post->ID); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="nav-next">
                            <?php
                            $next_post = get_next_post();
                            if ($next_post) :
                                ?>
                                <a href="<?php echo get_permalink($next_post->ID); ?>" class="nav-link">
                                    <span class="nav-label">Next Post →</span>
                                    <span class="nav-title"><?php echo get_the_title($next_post->ID); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </nav>

                    <!-- Comments Section -->
                    <?php
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>

                </article>
                <?php
            endwhile;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>


