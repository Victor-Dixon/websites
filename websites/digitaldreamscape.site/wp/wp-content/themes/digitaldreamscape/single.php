<?php
/**
 * Single Post Template
 * 
 * @package DigitalDreamscape
 * @since 2.0.0
 */

get_header(); 

/**
 * Generate face card HTML for breaking up content
 */
function get_face_card_html() {
    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author_meta('display_name', $author_id);
    if (empty($author_name) || strpos($author_name, '@') !== false) {
        $author_name = get_the_author_meta('nickname', $author_id);
    }
    if (empty($author_name) || strpos($author_name, '@') !== false) {
        $author_name = 'Shadow Sovereign';
    }
    $author_avatar = get_avatar($author_id, 120);
    $author_bio = get_the_author_meta('description', $author_id);
    if (empty($author_bio)) {
        $author_bio = 'Building Digital Dreamscape in public. One episode at a time.';
    }
    
    return '
    <div class="face-card-break">
        <div class="face-card">
            <div class="face-card-avatar">
                ' . $author_avatar . '
            </div>
            <div class="face-card-content">
                <div class="face-card-name">' . esc_html($author_name) . '</div>
                <div class="face-card-role">[Shadow Sovereign]</div>
                <div class="face-card-bio">' . esc_html($author_bio) . '</div>
            </div>
        </div>
    </div>';
}
?>

<!-- Critical Dark Theme Styles + Card System -->
<style>
    /* Dark Theme Base */
    body.single, body.single-post,
    .single .site-main, .single-post .site-main,
    .single-post-area, .single .content-area,
    .single article {
        background: #0a0a0a !important;
        background-image: none !important;
    }

    /* Glass Card System */
    .episode-card {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .episode-card:hover {
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    .episode-card-header {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.1)) !important;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .episode-card-content {
        background: rgba(255, 255, 255, 0.02) !important;
    }
    .episode-card-footer {
        background: rgba(99, 102, 241, 0.05) !important;
        border: 1px solid rgba(99, 102, 241, 0.15);
    }
    
    /* Content Styling */
    .dreamscape-narrative, .post-content.dreamscape-narrative,
    .narrative-intro, .narrative-outro {
        background: transparent !important;
        background-image: none !important;
    }
    .dreamscape-narrative::before, .dreamscape-narrative::after,
    .narrative-intro::before, .narrative-intro::after,
    .narrative-outro::before, .narrative-outro::after {
        display: none !important;
    }
    .dreamscape-narrative p, .post-content p,
    .dreamscape-narrative li, .post-content li {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    .dreamscape-narrative h2, .dreamscape-narrative h3,
    .post-content h2, .post-content h3 {
        color: #ffffff !important;
    }
    .intro-text, .outro-text {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    .intro-badge, .outro-badge {
        color: #a78bfa !important;
    }
    .outro-badge { color: #4ade80 !important; }
    .dreamscape-narrative strong, .post-content strong {
        color: #ffffff !important;
    }
    .dreamscape-narrative a, .post-content a {
        color: #a78bfa !important;
    }
    .dreamscape-narrative ul li::before, .post-content ul li::before {
        color: #a78bfa !important;
    }
    
    /* Narrative Cards */
    .narrative-intro-card {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.1)) !important;
        border-left: 4px solid #6366f1;
        border-radius: 0 12px 12px 0;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
    }
    .narrative-outro-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(16, 185, 129, 0.1)) !important;
        border-left: 4px solid #22c55e;
        border-radius: 0 12px 12px 0;
        padding: 1.5rem 2rem;
        margin-top: 2rem;
    }
    
    /* Card Labels */
    .card-label {
        display: inline-block;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 0.35rem 0.75rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    .card-label-purple {
        background: rgba(99, 102, 241, 0.2);
        color: #a78bfa;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }
    .card-label-green {
        background: rgba(34, 197, 94, 0.2);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .card-label-blue {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    
    /* Author Card */
    .author-card {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }
    .author-card-avatar img {
        border-radius: 50%;
        border: 3px solid rgba(99, 102, 241, 0.4);
    }
    .author-card-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #ffffff;
        margin: 0 0 0.5rem;
    }
    .author-card-desc {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0 0 1rem;
    }
    .author-card-link {
        color: #a78bfa !important;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
    }
    .author-card-link:hover {
        color: #c4b5fd !important;
    }
    
    /* Share Card */
    .share-card-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .share-card-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .share-card-twitter {
        background: rgba(29, 161, 242, 0.15);
        border: 1px solid rgba(29, 161, 242, 0.3);
        color: #1da1f2 !important;
    }
    .share-card-facebook {
        background: rgba(66, 103, 178, 0.15);
        border: 1px solid rgba(66, 103, 178, 0.3);
        color: #4267b2 !important;
    }
    .share-card-linkedin {
        background: rgba(0, 119, 181, 0.15);
        border: 1px solid rgba(0, 119, 181, 0.3);
        color: #0077b5 !important;
    }
    
    /* Navigation Cards */
    .nav-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 768px) {
        .nav-cards { grid-template-columns: 1fr; }
    }
    .nav-card {
        padding: 1.5rem !important;
    }
    .nav-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #a78bfa;
        margin-bottom: 0.5rem;
    }
    .nav-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: #ffffff;
        line-height: 1.4;
        text-decoration: none;
    }
    .nav-card a { text-decoration: none; }
    .nav-card:hover {
        border-color: rgba(99, 102, 241, 0.5) !important;
        transform: translateY(-2px);
    }
    
    /* Tags Card */
    .tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .tag-badge {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .tag-badge:hover {
        background: rgba(99, 102, 241, 0.2);
        border-color: rgba(99, 102, 241, 0.4);
        color: #a78bfa;
    }
    
    /* Face Cards - Break up text walls */
    .face-card-break {
        margin: 3rem 0;
        padding: 0;
    }
    .face-card {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        padding: 1.5rem;
        background: rgba(99, 102, 241, 0.08);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .face-card-avatar img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid rgba(99, 102, 241, 0.4);
        object-fit: cover;
    }
    .face-card-content {
        flex: 1;
    }
    .face-card-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.25rem;
    }
    .face-card-role {
        font-size: 0.85rem;
        color: #a78bfa;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .face-card-bio {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.5;
    }
    
    /* Improve content readability - less book-like */
    .dreamscape-narrative {
        max-width: 100%;
        line-height: 1.8;
    }
    .dreamscape-narrative p {
        margin-bottom: 1.5rem;
        font-size: 1.05rem;
        max-width: 65ch;
    }
    .dreamscape-narrative h2 {
        margin-top: 3rem;
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
        font-weight: 700;
        color: #ffffff;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid rgba(99, 102, 241, 0.3);
    }
    .dreamscape-narrative h3 {
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        font-size: 1.4rem;
        font-weight: 600;
        color: #a78bfa;
    }
    .dreamscape-narrative ul,
    .dreamscape-narrative ol {
        margin: 1.5rem 0;
        padding-left: 2rem;
    }
    .dreamscape-narrative li {
        margin-bottom: 0.75rem;
        line-height: 1.7;
    }
    
    /* Comments Card */
    .comments-card textarea,
    .comments-card input[type="text"],
    .comments-card input[type="email"],
    .comments-card input[type="url"] {
        width: 100%;
        padding: 0.875rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #ffffff;
        font-size: 1rem;
    }
    .comments-card textarea:focus,
    .comments-card input:focus {
        outline: none;
        border-color: rgba(99, 102, 241, 0.5);
    }
    .comments-card label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        font-weight: 600;
    }
    .comments-card .submit {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        color: #ffffff;
        font-weight: 600;
        cursor: pointer;
    }
    
    /* Responsive face cards */
    @media (max-width: 768px) {
        .face-card {
            flex-direction: column;
            text-align: center;
        }
        .face-card-avatar {
            margin-bottom: 1rem;
        }
    }
</style>

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
                            
                            <!-- Identity & State Meta Card -->
                            <div class="episode-card episode-card-header">
                                <div class="post-meta dreamscape-identity">
                                    <div class="identity-avatar">
                                        <span class="avatar-frame">
                                            <?php echo get_avatar(get_the_author_meta('ID'), 48); ?>
                                        </span>
                                        <div class="identity-info">
                                            <span class="identity-name"><?php echo get_the_author_meta('display_name') ?: get_the_author_meta('nickname') ?: 'Shadow Sovereign'; ?></span>
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

                    <!-- Main Content Card -->
                    <div class="episode-card episode-card-content">
                        <div class="post-content dreamscape-narrative">
                            <!-- Narrative Introduction -->
                            <div class="narrative-intro-card">
                                <span class="card-label card-label-purple">[NARRATIVE MODE: ACTIVE]</span>
                                <p class="intro-text">
                                    <strong>Digital Dreamscape</strong> is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. This post is part of the persistent simulation of self + system.
                                </p>
                            </div>
                            
                            <?php
                            // Output content with face cards inserted to break up text walls
                            $content = get_the_content();
                            $content = apply_filters('the_content', $content);
                            
                            // Split content by closing </p> tags to find paragraph breaks
                            $parts = preg_split('/(<\/p>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
                            $para_count = 0;
                            $output = '';
                            
                            foreach ($parts as $part) {
                                $output .= $part;
                                
                                // Count paragraphs (look for opening <p> tags)
                                if (preg_match('/<p[^>]*>/', $part)) {
                                    $para_count++;
                                    
                                    // Insert face card after every 4-5 paragraphs to break up text walls
                                    if ($para_count > 0 && $para_count % 5 == 0) {
                                        $output .= get_face_card_html();
                                    }
                                }
                            }
                            
                            // For very long posts, add an extra face card in the middle
                            $word_count = str_word_count(strip_tags($content));
                            if ($word_count > 800 && $para_count > 10) {
                                // Find middle paragraph and insert face card there if we haven't already
                                $mid_para = floor($para_count / 2);
                                $current = 0;
                                $new_output = '';
                                $inserted = false;
                                
                                foreach ($parts as $part) {
                                    $new_output .= $part;
                                    if (preg_match('/<p[^>]*>/', $part)) {
                                        $current++;
                                        if (!$inserted && $current >= $mid_para && $current % 5 != 0) {
                                            $new_output .= get_face_card_html();
                                            $inserted = true;
                                        }
                                    }
                                }
                                $output = $new_output;
                            }
                            
                            echo $output;
                            
                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'digitaldreamscape'),
                                'after'  => '</div>',
                            ));
                            ?>
                            
                            <!-- Narrative Conclusion -->
                            <div class="narrative-outro-card">
                                <span class="card-label card-label-green">[EPISODE COMPLETE]</span>
                                <p class="outro-text">
                                    This episode has been logged to memory. Identity state updated. Questline progression recorded.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tags Card -->
                    <?php
                    $tags = get_the_tags();
                    if ($tags) :
                        ?>
                        <div class="episode-card">
                            <span class="card-label card-label-blue">[TAGS]</span>
                            <div class="tags-list">
                                <?php
                                foreach ($tags as $tag) {
                                    echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="tag-badge">#' . esc_html($tag->name) . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Share Card -->
                    <div class="episode-card">
                        <span class="card-label card-label-purple">[SHARE EPISODE]</span>
                        <div class="share-card-buttons">
                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="share-card-btn share-card-twitter">
                                𝕏 Twitter
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="share-card-btn share-card-facebook">
                                📘 Facebook
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="share-card-btn share-card-linkedin">
                                💼 LinkedIn
                            </a>
                        </div>
                    </div>

                    <!-- Author Card -->
                    <div class="episode-card episode-card-footer">
                        <span class="card-label card-label-purple">[AUTHOR]</span>
                        <div class="author-card">
                            <div class="author-card-avatar">
                                <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                            </div>
                            <div class="author-card-content">
                                <h3 class="author-card-name"><?php echo get_the_author_meta('display_name') ?: get_the_author_meta('nickname') ?: 'Shadow Sovereign'; ?></h3>
                                <?php if (get_the_author_meta('description')) : ?>
                                    <p class="author-card-desc"><?php echo get_the_author_meta('description'); ?></p>
                                <?php else : ?>
                                    <p class="author-card-desc">Building Digital Dreamscape in public. One episode at a time.</p>
                                <?php endif; ?>
                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-card-link">
                                    View all posts by <?php echo get_the_author_meta('display_name') ?: get_the_author_meta('nickname') ?: 'Shadow Sovereign'; ?> →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Cards -->
                    <div class="nav-cards">
                        <?php
                        $prev_post = get_previous_post();
                        if ($prev_post) :
                            ?>
                            <div class="episode-card nav-card">
                                <a href="<?php echo get_permalink($prev_post->ID); ?>">
                                    <div class="nav-card-label">← Previous Episode</div>
                                    <div class="nav-card-title"><?php echo get_the_title($prev_post->ID); ?></div>
                                </a>
                            </div>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>
                        
                        <?php
                        $next_post = get_next_post();
                        if ($next_post) :
                            ?>
                            <div class="episode-card nav-card" style="text-align: right;">
                                <a href="<?php echo get_permalink($next_post->ID); ?>">
                                    <div class="nav-card-label">Next Episode →</div>
                                    <div class="nav-card-title"><?php echo get_the_title($next_post->ID); ?></div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Comments Card -->
                    <?php
                    if (comments_open() || get_comments_number()) :
                        ?>
                        <div class="episode-card comments-card">
                            <span class="card-label card-label-blue">[COMMENTS]</span>
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>

                </article>
                <?php
            endwhile;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
