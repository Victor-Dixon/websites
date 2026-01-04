<?php
/**
 * Episode Template - Digital Dreamscape
 *
 * Individual episode pages in the persistent narrative
 *
 * @package DigitalDreamscape
 * @since 4.0.0 - Episode Edition
 */

get_header();

// Get episode metadata
$episode_id = 'EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT);
$artifact_type = get_post_meta(get_the_ID(), 'artifact_type', true) ?: 'episode';
$questline = get_the_category()[0]->name ?? 'General';
$artifact_state = get_post_meta(get_the_ID(), 'artifact_state', true) ?: 'active';
$canonical = get_post_meta(get_the_ID(), 'canonical', true) === 'true';

// Calculate reading metrics
$content = get_post_field('post_content', get_the_ID());
$word_count = str_word_count(strip_tags($content));
$reading_time = ceil($word_count / 200);

?>

<main class="site-main">
    <div class="container">
        <div class="content-area single-post-area">

            <!-- Episode Header -->
            <header class="episode-header">
                <!-- Episode Identity Strip -->
                <div class="episode-identity">
                    <div class="episode-badge">[<?php echo strtoupper($artifact_type); ?>]</div>
                    <div class="episode-id"><?php echo $episode_id; ?></div>
                    <?php if ($canonical): ?>
                        <div class="canon-seal">[CANON AUTHORITY]</div>
                    <?php endif; ?>
                </div>

                <!-- Questline Context -->
                <div class="episode-questline">
                    <span class="questline-label">QUESTLINE:</span>
                    <a href="<?php echo esc_url(get_category_link(get_the_category()[0]->term_id ?? 0)); ?>" class="questline-name">
                        <?php echo esc_html($questline); ?>
                    </a>
                    <span class="questline-state">[<?php echo strtoupper($artifact_state); ?>]</span>
                </div>

                <!-- Episode Title -->
                <h1 class="episode-title"><?php the_title(); ?></h1>

                <!-- Episode Metadata -->
                <div class="episode-meta">
                    <div class="meta-author">
                        <div class="author-avatar">
                            <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
                        </div>
                        <div class="author-info">
                            <span class="author-name"><?php the_author(); ?></span>
                            <span class="author-title">[Shadow Sovereign]</span>
                        </div>
                    </div>

                    <div class="meta-timeline">
                        <time datetime="<?php echo get_the_date('c'); ?>">
                            [TIMELINE] <?php echo get_the_date('M j, Y'); ?>
                        </time>
                    </div>

                    <div class="meta-stats">
                        <span class="stat-reading">⏱ <?php echo $reading_time; ?> min</span>
                        <span class="stat-words">📖 <?php echo $word_count; ?> words</span>
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if (has_post_thumbnail()) : ?>
                    <div class="episode-visual">
                        <?php the_post_thumbnail('large', array('class' => 'episode-image')); ?>
                        <div class="episode-overlay">
                            <div class="episode-marker">[EPISODE LOG]</div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Canon Declaration -->
                <div class="canon-declaration">
                    <div class="canon-status">
                        <?php if ($canonical): ?>
                            <strong>CANON AUTHORITY GRANTED</strong>
                            <p>This episode establishes binding precedent. Future work assumes these decisions as truth.</p>
                        <?php else: ?>
                            <strong>EPISODE LOG</strong>
                            <p>This narrative fragment contributes to the evolving simulation state.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- Episode Content -->
            <div class="episode-content">
                <!-- Episode Introduction -->
                <div class="episode-intro">
                    <div class="intro-marker">[NARRATIVE MODE: ACTIVE]</div>
                    <div class="intro-context">
                        Digital Dreamscape is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. This episode contributes to the persistent simulation of self + system.
                    </div>
                </div>

                <!-- Episode Body -->
                <div class="episode-body">
                    <?php
                    the_content();

                    wp_link_pages(array(
                        'before' => '<div class="page-links">Pages: ',
                        'after'  => '</div>',
                    ));
                    ?>
                </div>

                <!-- Episode Resolution -->
                <div class="episode-resolution">
                    <div class="resolution-marker">[EPISODE COMPLETE]</div>
                    <div class="resolution-summary">
                        <p>This episode has been logged to memory. Identity state updated. Questline progression recorded.</p>
                        <?php if ($canonical): ?>
                            <p><strong>Canon Impact:</strong> This episode establishes binding precedent for future development.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Episode Footer -->
            <footer class="episode-footer">
                <!-- Episode Tags -->
                <?php
                $tags = get_the_tags();
                if ($tags) :
                    ?>
                    <div class="episode-tags">
                        <span class="tags-label">system tags:</span>
                        <?php
                        foreach ($tags as $tag) {
                            echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="tag-link">' . esc_html($tag->name) . '</a>';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Episode Impact -->
                <div class="episode-impact">
                    <h3>Episode Impact</h3>
                    <div class="impact-grid">
                        <div class="impact-item">
                            <span class="impact-label">Questline Progress:</span>
                            <span class="impact-value"><?php echo esc_html($questline); ?> updated</span>
                        </div>
                        <div class="impact-item">
                            <span class="impact-label">World State:</span>
                            <span class="impact-value"><?php echo $canonical ? 'Canon established' : 'Narrative advanced'; ?></span>
                        </div>
                        <div class="impact-item">
                            <span class="impact-label">Future Dependencies:</span>
                            <span class="impact-value"><?php echo $artifact_state === 'active' ? 'Open loops remain' : 'Resolution achieved'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Episode Navigation -->
                <nav class="episode-navigation">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>

                    <?php if ($prev_post) : ?>
                        <a href="<?php echo get_permalink($prev_post->ID); ?>" class="nav-link nav-prev">
                            <span class="nav-direction">← Previous Episode</span>
                            <span class="nav-title"><?php echo get_the_title($prev_post->ID); ?></span>
                            <span class="nav-meta">EP-<?php echo str_pad($prev_post->ID, 4, '0', STR_PAD_LEFT); ?></span>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo home_url('/blog/'); ?>" class="nav-link nav-archive">
                        <span class="nav-direction">Return to</span>
                        <span class="nav-title">World Archive</span>
                        <span class="nav-meta">All Episodes</span>
                    </a>

                    <?php if ($next_post) : ?>
                        <a href="<?php echo get_permalink($next_post->ID); ?>" class="nav-link nav-next">
                            <span class="nav-direction">Next Episode →</span>
                            <span class="nav-title"><?php echo get_the_title($next_post->ID); ?></span>
                            <span class="nav-meta">EP-<?php echo str_pad($next_post->ID, 4, '0', STR_PAD_LEFT); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- Episode Sharing -->
                <div class="episode-sharing">
                    <h3>Share Episode</h3>
                    <div class="share-links">
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode('EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT) . ': ' . get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>&via=digitaldreamscape" target="_blank" class="share-link twitter">
                            𝕏 Share
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode('EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT) . ': ' . get_the_title()); ?>" target="_blank" class="share-link linkedin">
                            💼 Share
                        </a>
                        <a href="mailto:?subject=<?php echo urlencode('EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT) . ': ' . get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" class="share-link email">
                            ✉️ Forward
                        </a>
                    </div>
                </div>
            </footer>

            <?php
            // Episode Comments (if enabled)
            if (comments_open() || get_comments_number()) :
                ?>
                <div class="episode-comments">
                    <h3>Episode Discussion</h3>
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>

            <?php
            endwhile;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>


