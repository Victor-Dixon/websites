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

// Check for design variant (allows custom designs per post)
$design_variant = get_post_meta(get_the_ID(), 'design_variant', true);
if (!$design_variant) {
    $design_variant = 'default';
}
?>
<main class="content-area blog-style-<?php echo esc_attr($blog_style); ?> design-variant-<?php echo esc_attr($design_variant); ?>" data-post-id="<?php echo get_the_ID(); ?>">
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
                <?php if (dadudekc_is_swarm_intro_post()) : ?>
                    <?php get_template_part('template-parts/components/swarm-post-extras'); ?>
                <?php endif; ?>

                <?php if ($blog_style === 'technical') : ?>
                    <!-- Technical Style Showcase Section -->
                    <div class="technical-showcase" style="margin: 3rem 0;">
                        <h3 style="text-align: center; margin: 2rem 0 1rem 0; color: var(--accent); font-size: 1.5rem;">🚀 Technical Showcase</h3>
                        <p style="text-align: center; color: var(--text-secondary); margin-bottom: 2rem;">Advanced tools, metrics, and capabilities demonstrated</p>

                        <!-- Key Metrics Grid -->
                        <div class="metrics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
                            <div class="metric-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: var(--shadow);">
                                <div class="metric-number" style="font-size: 3rem; font-weight: bold; color: var(--accent); margin-bottom: 0.5rem;">4</div>
                                <div class="metric-label" style="color: var(--text-secondary); font-size: 0.9rem;">Core BI Tools Developed</div>
                            </div>
                            <div class="metric-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: var(--shadow);">
                                <div class="metric-number" style="font-size: 3rem; font-weight: bold; color: var(--accent); margin-bottom: 0.5rem;">100%</div>
                                <div class="metric-label" style="color: var(--text-secondary); font-size: 0.9rem;">V2 Compliance Achieved</div>
                            </div>
                            <div class="metric-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: var(--shadow);">
                                <div class="metric-number" style="font-size: 3rem; font-weight: bold; color: var(--accent); margin-bottom: 0.5rem;">∞</div>
                                <div class="metric-label" style="color: var(--text-secondary); font-size: 0.9rem;">Optimization Potential</div>
                            </div>
                            <div class="metric-card" style="background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: var(--shadow);">
                                <div class="metric-number" style="font-size: 3rem; font-weight: bold; color: var(--accent); margin-bottom: 0.5rem;">24/7</div>
                                <div class="metric-label" style="color: var(--text-secondary); font-size: 0.9rem;">Automated Analytics</div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Author Face Card - Home Page Style -->
                <div class="author-face-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; margin-top: 3rem; border: 1px solid var(--border);">
                    <div style="text-align: center;">
                        <div style="font-size: 2rem; font-weight: bold; color: var(--accent); margin-bottom: 1rem;">V</div>
                        <h4 style="margin: 0 0 1rem 0; color: var(--text-primary);">Victor Dixon</h4>
                        <p style="margin: 0 0 1.5rem 0; color: var(--text-secondary); line-height: 1.6;">
                            Building ambitious systems, shipping experiments, and documenting the path.
                        </p>
                        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                            <a href="<?php echo esc_url(dadudekc_get_portfolio_url()); ?>" style="background: var(--accent); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                View Portfolio →
                            </a>
                            <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>" style="background: var(--surface); color: var(--text-primary); padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid var(--border); font-size: 0.9rem;">
                                More Writing
                            </a>
                            <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>" style="background: transparent; color: var(--text-secondary); padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid var(--border); font-size: 0.9rem;">
                                Contact
                            </a>
                        </div>
                    </div>
                </div>

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