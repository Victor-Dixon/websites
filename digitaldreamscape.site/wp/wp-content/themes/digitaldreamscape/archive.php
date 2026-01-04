<?php
/**
 * Archive Template (Blog Listing)
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

get_header(); ?>

<main class="site-main">
    <div class="container">
        <!-- Archive Header - Digital Dreamscape Narrative -->
        <header class="archive-header dreamscape-archive-header">
            <div class="archive-badge">[EPISODE ARCHIVE]</div>
            <h1 class="archive-title dreamscape-archive-title">
                <?php
                if (is_category()) {
                    echo '[QUESTLINE] ' . single_cat_title('', false);
                } elseif (is_tag()) {
                    echo '[TAG] ' . single_tag_title('', false);
                } elseif (is_author()) {
                    echo '[IDENTITY] ' . get_the_author();
                } elseif (is_date()) {
                    if (is_year()) {
                        echo '[TIMELINE] ' . get_the_date('Y');
                    } elseif (is_month()) {
                        echo '[TIMELINE] ' . get_the_date('F Y');
                    } elseif (is_day()) {
                        echo '[TIMELINE] ' . get_the_date('F j, Y');
                    }
                } else {
                    echo 'Narrative Episodes';
                }
                ?>
            </h1>
            <?php
            $description = get_the_archive_description();
            if ($description) :
                ?>
                <div class="archive-description dreamscape-archive-desc">
                    <?php echo wp_kses_post($description); ?>
                </div>
            <?php else : ?>
                <div class="archive-description dreamscape-archive-desc">
                    <p><strong>Digital Dreamscape</strong> is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. Each episode below is part of the persistent simulation of self + system.</p>
                    <p>Every conversation, decision, build, failure, and breakthrough is treated as <strong>canon</strong> and transformed into structured state.</p>
                </div>
            <?php endif; ?>
        </header>

        <!-- Blog Posts Grid -->
        <div class="blog-posts-grid">
            <?php
            // Ensure we have posts to display
            if (!have_posts()) {
                // Try to query posts directly if main query is empty
                query_posts(array('post_type' => 'post', 'posts_per_page' => 10, 'post_status' => 'publish'));
            }
            
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post-card dreamscape-episode-card face-card'); ?>>
                        <!-- Face Card Header - Author Avatar Prominent -->
                        <div class="face-card-header">
                            <div class="face-card-avatar-wrapper">
                                <div class="face-card-avatar-frame">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 120); ?>
                                    <div class="avatar-glow"></div>
                                </div>
                                <div class="face-card-identity">
                                    <div class="face-card-name"><?php the_author(); ?></div>
                                    <div class="face-card-title">[Shadow Sovereign]</div>
                                </div>
                            </div>
                            <div class="face-card-badges">
                                <span class="episode-marker">[EPISODE]</span>
                                <span class="canon-badge">[CANON]</span>
                            </div>
                        </div>
                        
                        <!-- Featured Image or Gradient Background -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="face-card-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large', array('class' => 'face-card-img')); ?>
                                    <div class="face-card-image-overlay"></div>
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="face-card-image face-card-gradient">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="face-card-pattern"></div>
                                    <div class="face-card-image-overlay"></div>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Face Card Content -->
                        <div class="face-card-content">
                            <div class="face-card-meta">
                                <time datetime="<?php echo get_the_date('c'); ?>" class="face-card-date">
                                    [TIMELINE] <?php echo get_the_date('M j, Y'); ?>
                                </time>
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    $category = $categories[0];
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="face-card-questline">[QUESTLINE] ' . esc_html($category->name) . '</a>';
                                } else {
                                    echo '<span class="face-card-questline">[QUESTLINE] Uncategorized</span>';
                                }
                                ?>
                            </div>
                            
                            <h2 class="face-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="face-card-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="face-card-cta">
                                <span>[ENTER EPISODE]</span>
                                <span class="cta-arrow">→</span>
                            </a>
                        </div>
                    </article>
                    <?php
                endwhile;
            else :
                ?>
                <div class="no-posts">
                    <p>No posts found. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php
        if (have_posts()) :
            ?>
            <nav class="pagination">
                <?php
                echo paginate_links(array(
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                    'type' => 'list',
                ));
                ?>
            </nav>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>


