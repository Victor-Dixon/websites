<?php
/**
 * Single post template with multiple style variations.
 *
 * @package DaDudeKC
 */

get_header();

// Determine blog style based on categories or custom field
$post_categories = get_the_category();
$blog_style = 'magazine'; // default

// Style mapping based on categories
$style_mapping = [
    'newsletter' => ['newsletter', 'personal', 'conversation'],
    'technical' => ['technical', 'tutorial', 'code', 'development', 'ai-assisted-development'],
    'essay' => ['essay', 'reflection', 'thoughts', 'philosophy'],
    'magazine' => ['business-intelligence', 'showcase', 'review', 'analysis']
];

foreach ($post_categories as $category) {
    $category_slug = $category->slug;
    foreach ($style_mapping as $style => $keywords) {
        if (in_array($category_slug, $keywords)) {
            $blog_style = $style;
            break 2;
        }
    }
}

// Check for custom field override
$custom_style = get_post_meta(get_the_ID(), 'blog_style', true);
if ($custom_style && in_array($custom_style, ['magazine', 'newsletter', 'technical', 'essay'])) {
    $blog_style = $custom_style;
}
?>
<main class="content-area blog-style-<?php echo esc_attr($blog_style); ?>">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

            <article>

                <?php if ($blog_style === 'magazine') : ?>
                    <!-- Magazine Style Header -->
                    <header class="post-header">
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        <?php
                        $excerpt = get_the_excerpt();
                        if ($excerpt) : ?>
                            <p class="post-excerpt"><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>
                        <div class="post-meta">
                            <span><?php echo esc_html(get_the_date('F j, Y')); ?></span>
                            <span><?php echo esc_html(dadudekc_get_reading_time()); ?> min read</span>
                            <span>By Victor</span>
                        </div>
                        <div class="post-categories">
                            <?php the_category(', '); ?>
                        </div>
                    </header>

                <?php elseif ($blog_style === 'newsletter') : ?>
                    <!-- Newsletter Style Header -->
                    <header class="post-header">
                        <p class="post-date"><?php echo esc_html(get_the_date('l, F j, Y')); ?></p>
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        <?php
                        $excerpt = get_the_excerpt();
                        if ($excerpt) : ?>
                            <p class="post-subtitle"><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>
                        <div class="post-meta">
                            <?php echo esc_html(dadudekc_get_reading_time()); ?> minute read · <?php the_category(', '); ?>
                        </div>
                    </header>

                <?php elseif ($blog_style === 'technical') : ?>
                    <!-- Technical Style Header -->
                    <header class="post-header">
                        <div class="tech-tags">
                            <?php
                            $categories = get_the_category();
                            foreach ($categories as $category) :
                            ?>
                                <span class="tech-tag"><?php echo esc_html($category->name); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        <div class="post-meta">
                            <span><?php echo esc_html(get_the_date('M j, Y')); ?></span>
                            <span><?php echo esc_html(dadudekc_get_reading_time()); ?> min</span>
                            <span>Victor Dixon</span>
                        </div>
                        <?php
                        $excerpt = get_the_excerpt();
                        if ($excerpt) : ?>
                            <p class="post-excerpt"><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>
                    </header>

                    <!-- Table of Contents for Technical Posts -->
                    <div class="table-of-contents">
                        <h3 class="toc-title">Table of Contents</h3>
                        <ul class="toc-list">
                            <?php
                            $content = get_the_content();
                            $headings = [];
                            if (preg_match_all('/<h([2-3])([^>]*)>(.*?)<\/h[2-3]>/i', $content, $matches)) {
                                foreach ($matches[3] as $index => $heading) {
                                    $clean_heading = strip_tags($heading);
                                    $slug = sanitize_title($clean_heading);
                                    echo '<li><a href="#' . esc_attr($slug) . '">' . esc_html($clean_heading) . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>

                <?php elseif ($blog_style === 'essay') : ?>
                    <!-- Essay Style Header -->
                    <header class="post-header">
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        <?php
                        $excerpt = get_the_excerpt();
                        if ($excerpt) : ?>
                            <p class="post-subtitle"><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>
                        <div class="post-meta">
                            <?php echo esc_html(get_the_date('F j, Y')); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?> minutes
                        </div>
                    </header>

                <?php endif; ?>

                <!-- Post Content -->
                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <?php if ($blog_style === 'magazine') : ?>
                    <!-- Magazine Style Author Bio -->
                    <div class="author-bio">
                        <div class="author-avatar">V</div>
                        <div class="author-info">
                            <h4>Victor Dixon</h4>
                            <p>Building systems that matter. Writing about AI, automation, and the future of software development.</p>
                            <div class="social-share">
                                <a href="https://twitter.com/dadudekc" target="_blank" rel="noopener">Twitter</a>
                                <a href="https://github.com/dadudekc" target="_blank" rel="noopener">GitHub</a>
                                <a href="/contact">Contact</a>
                            </div>
                        </div>
                    </div>

                <?php elseif ($blog_style === 'newsletter') : ?>
                    <!-- Newsletter Style Signoff -->
                    <div class="newsletter-signoff">
                        <p>Thanks for reading,</p>
                        <p><strong>Victor</strong></p>
                        <p>Building and breaking systems daily. If you found this useful, share it with someone who might too.</p>
                    </div>

                <?php endif; ?>

                <?php
                // Series Navigation (works with all styles)
                $series_slugs = ['dreamscape', 'swarm', 'trading-systems'];
                $post_categories = wp_get_post_categories(get_the_ID(), ['fields' => 'slugs']);
                $matched_series = array_intersect($series_slugs, $post_categories);
                if (!empty($matched_series)) :
                    $series_slug = array_values($matched_series)[0];
                    ?>
                    <div class="series-nav">
                        <strong><?php esc_html_e('Series Navigation', 'dadudekc'); ?></strong>
                        <p><?php echo esc_html(sprintf(__('This post is part of the %s series.', 'dadudekc'), ucfirst(str_replace('-', ' ', $series_slug)))); ?></p>
                        <a href="<?php echo esc_url(add_query_arg('series', $series_slug, dadudekc_get_blog_page_url())); ?>">
                            <?php esc_html_e('View all in series →', 'dadudekc'); ?>
                        </a>
                    </div>
                <?php endif; ?>

            </article>

        <?php endwhile; ?>
    <?php endif; ?>
</main>
<?php
get_footer();
?>