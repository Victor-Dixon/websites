<?php
/**
 * Template Part: Premium Single Post Content
 * 
 * Beautiful, readable single post layout for FreeRide Investor.
 * 
 * @package FreeRideInvestor
 */

// Get post data
$categories = get_the_category();
$category_name = !empty($categories) ? $categories[0]->name : 'Uncategorized';

// Estimate read time
$content = get_the_content();
$word_count = str_word_count(strip_tags($content));
$read_time = ceil($word_count / 200);

// Author info
$author_id = get_the_author_meta('ID');
$author_name = get_the_author();
$author_bio = get_the_author_meta('description');
$author_initial = strtoupper(substr($author_name, 0, 1));
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
    <div class="blog-container">
        
        <!-- Post Header -->
        <header class="single-post__header">
            <div class="single-post__meta">
                <span class="single-post__category"><?php echo esc_html($category_name); ?></span>
                <span class="single-post__date">
                    📅 <?php echo get_the_date('F j, Y'); ?>
                </span>
                <span class="single-post__read-time">
                    ⏱️ <?php echo $read_time; ?> min read
                </span>
            </div>
            
            <h1 class="single-post__title"><?php the_title(); ?></h1>
            
            <?php if (has_excerpt()) : ?>
                <p class="single-post__excerpt"><?php echo get_the_excerpt(); ?></p>
            <?php endif; ?>
        </header>
        
        <!-- Featured Image -->
        <?php if (has_post_thumbnail()) : ?>
            <div class="single-post__featured-image">
                <?php the_post_thumbnail('full'); ?>
            </div>
        <?php endif; ?>
        
        <!-- Post Content -->
        <div class="single-post__content">
            <?php the_content(); ?>
        </div>
        
        <!-- Post Tags -->
        <?php
        $tags = get_the_tags();
        if ($tags) :
        ?>
            <div class="post-tags">
                <span class="post-tags__label">Tags:</span>
                <?php foreach ($tags as $tag) : ?>
                    <a href="<?php echo get_tag_link($tag->term_id); ?>">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Author Box -->
        <div class="author-box">
            <div class="author-box__avatar">
                <?php
                $avatar_url = get_avatar_url($author_id, array('size' => 160));
                if ($avatar_url && strpos($avatar_url, 'gravatar') !== false) :
                ?>
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($author_name); ?>">
                <?php else : ?>
                    <?php echo esc_html($author_initial); ?>
                <?php endif; ?>
            </div>
            
            <div class="author-box__content">
                <h3 class="author-box__name"><?php echo esc_html($author_name); ?></h3>
                <p class="author-box__role">Contributor at FreeRide Investor</p>
                <?php if ($author_bio) : ?>
                    <p class="author-box__bio"><?php echo esc_html($author_bio); ?></p>
                <?php else : ?>
                    <p class="author-box__bio">
                        Sharing insights on trading strategies, market analysis, and the journey to financial freedom.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Post Navigation -->
        <?php
        $prev_post = get_previous_post();
        $next_post = get_next_post();
        
        if ($prev_post || $next_post) :
        ?>
            <nav class="post-navigation">
                <?php if ($prev_post) : ?>
                    <a href="<?php echo get_permalink($prev_post); ?>" class="post-nav-link prev">
                        <span class="post-nav-link__label">← Previous Article</span>
                        <span class="post-nav-link__title"><?php echo esc_html($prev_post->post_title); ?></span>
                    </a>
                <?php else : ?>
                    <div></div>
                <?php endif; ?>
                
                <?php if ($next_post) : ?>
                    <a href="<?php echo get_permalink($next_post); ?>" class="post-nav-link next">
                        <span class="post-nav-link__label">Next Article →</span>
                        <span class="post-nav-link__title"><?php echo esc_html($next_post->post_title); ?></span>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
        
    </div>
</article>
