<?php
/**
 * Home template for blog posts page.
 * Used when a page is set as the Posts page in WordPress settings.
 *
 * @package DaDudeKC
 */


get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Get blog posts
$blog_query = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 12,
    'paged' => $paged,
    'post_status' => 'publish',
]);

// Get all categories for filtering
$categories = get_categories([
    'hide_empty' => true,
]);

// Get current category filter
$current_cat = isset($_GET['category']) ? sanitize_text_field(wp_unslash($_GET['category'])) : '';

// Get current series filter
$current_series = isset($_GET['series']) ? sanitize_text_field(wp_unslash($_GET['series'])) : '';

// Define available series
$available_series = ['dreamscape', 'swarm', 'trading-systems'];


if ($current_cat) {
    $cat_obj = get_category_by_slug($current_cat);
    if ($cat_obj) {
        $blog_query = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => 12,
            'paged' => $paged,
            'cat' => $cat_obj->term_id,
            'post_status' => 'publish',
        ]);
    }
} elseif ($current_series && in_array($current_series, $available_series)) {
    $series_cat = get_category_by_slug($current_series);
    if ($series_cat) {
        $blog_query = new WP_Query([
            'post_type' => 'post',
            'posts_per_page' => 12,
            'paged' => $paged,
            'cat' => $series_cat->term_id,
            'post_status' => 'publish',
        ]);
    }
}
?>

<main class="content-area">
    <header class="page-header" style="text-align: center; margin-bottom: 4rem;">
        <?php if ($current_series && in_array($current_series, $available_series)) : ?>
            <h1 style="font-size: 3rem; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent), var(--text-primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php echo esc_html(ucfirst(str_replace('-', ' ', $current_series))); ?> Series</h1>
            <p class="post-meta" style="font-size: 1.2rem; color: var(--text-secondary);">
                <?php
                $series_subtitles = [
                    'dreamscape' => 'Creative coding explorations and technological imagination.',
                    'swarm' => 'Multi-agent AI systems and autonomous coordination.',
                    'trading-systems' => 'Algorithmic trading, quantitative strategies, and market automation.'
                ];
                echo esc_html($series_subtitles[$current_series] ?? 'Series articles and deep dives.');
                ?>
            </p>
        <?php else : ?>
            <h1 style="font-size: 3rem; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent), var(--text-primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php esc_html_e('Blog', 'dadudekc'); ?></h1>
            <p class="post-meta" style="font-size: 1.2rem; color: var(--text-secondary);"><?php esc_html_e('Deep dives, tutorials, and insights from my journey in technology and entrepreneurship.', 'dadudekc'); ?></p>
        <?php endif; ?>

        <!-- Blog Stats -->
        <div class="blog-stats" style="display: flex; justify-content: center; gap: 3rem; margin-top: 2rem; flex-wrap: wrap;">
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: bold; color: var(--accent);"><?php echo wp_count_posts('post')->publish; ?></div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php esc_html_e('Articles', 'dadudekc'); ?></div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: bold; color: var(--accent);"><?php echo count($categories); ?></div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php esc_html_e('Topics', 'dadudekc'); ?></div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: bold; color: var(--accent);"><?php echo wp_count_posts('post')->publish * 8; ?> min</div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php esc_html_e('Reading Time', 'dadudekc'); ?></div>
            </div>
        </div>
    </header>

    <!-- Series Filter -->
    <?php if ($current_series && in_array($current_series, $available_series)) : ?>
    <div class="series-filter" style="background: var(--surface); border-radius: 12px; padding: 2rem; margin-bottom: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
        <div class="filter-header" style="text-align: center; margin-bottom: 2rem;">
            <h3 style="margin: 0; color: var(--text-primary);"><?php esc_html_e('Series', 'dadudekc'); ?></h3>
        </div>

        <!-- Active Series Display -->
        <div class="active-series" style="text-align: center;">
            <strong><?php esc_html_e('Reading:', 'dadudekc'); ?></strong>
            <span style="background: var(--accent); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 1rem; font-weight: 500; margin-left: 0.5rem;">
                <?php echo esc_html(ucfirst(str_replace('-', ' ', $current_series))); ?> Series
                <a href="<?php echo esc_url(get_permalink()); ?>" style="color: white; text-decoration: none; margin-left: 0.5rem; font-weight: bold;">×</a>
            </span>
        </div>

        <!-- Series Description -->
        <div class="series-description" style="text-align: center; margin-top: 1.5rem; padding: 1.5rem; background: rgba(0, 212, 255, 0.05); border-radius: 8px;">
            <?php
            $series_descriptions = [
                'dreamscape' => 'Explorations into creative coding, generative art, and the intersection of technology and imagination.',
                'swarm' => 'Deep dives into multi-agent AI systems, swarm intelligence, and autonomous coordination platforms.',
                'trading-systems' => 'Technical analysis of algorithmic trading systems, quantitative strategies, and market automation.'
            ];
            if (isset($series_descriptions[$current_series])) {
                echo '<p style="margin: 0; color: var(--text-primary); font-style: italic;">' . esc_html($series_descriptions[$current_series]) . '</p>';
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Category Filter -->
    <?php if (!empty($categories)) : ?>
    <div class="category-filter" style="background: var(--surface); border-radius: 12px; padding: 2rem; margin-bottom: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="filter-header" style="text-align: center; margin-bottom: 2rem;">
            <h3 style="margin: 0; color: var(--text-primary);"><?php esc_html_e('Filter by Topic', 'dadudekc'); ?></h3>
        </div>

        <div class="category-grid" style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo esc_url(get_permalink()); ?>" class="category-pill <?php echo !$current_cat ? 'active' : ''; ?>" style="background: <?php echo !$current_cat ? 'var(--accent)' : 'var(--border)'; ?>; color: <?php echo !$current_cat ? 'white' : 'var(--text-primary)'; ?>; padding: 0.75rem 1.5rem; border-radius: 25px; text-decoration: none; font-weight: 500; transition: all 0.3s ease;">
                <?php esc_html_e('All Topics', 'dadudekc'); ?>
            </a>

            <?php foreach ($categories as $category) :
                $is_active = ($current_cat === $category->slug);
            ?>
                <a href="<?php echo esc_url(add_query_arg('category', $category->slug, get_permalink())); ?>" class="category-pill <?php echo $is_active ? 'active' : ''; ?>" style="background: <?php echo $is_active ? 'var(--accent)' : 'var(--border)'; ?>; color: <?php echo $is_active ? 'white' : 'var(--text-primary)'; ?>; padding: 0.75rem 1.5rem; border-radius: 25px; text-decoration: none; font-weight: 500; transition: all 0.3s ease;">
                    <?php echo esc_html($category->name); ?>
                    <span style="margin-left: 0.5rem; opacity: 0.8;">(<?php echo $category->count; ?>)</span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Active Filter Display -->
        <?php if ($current_cat) : ?>
            <div class="active-filter" style="text-align: center; margin-top: 1.5rem; padding: 1rem; background: rgba(0, 212, 255, 0.1); border-radius: 8px;">
                <strong><?php esc_html_e('Showing:', 'dadudekc'); ?></strong>
                <span style="background: var(--accent); color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem; margin-left: 0.5rem;">
                    <?php echo esc_html($cat_obj->name); ?>
                    <a href="<?php echo esc_url(get_permalink()); ?>" style="color: white; text-decoration: none; margin-left: 0.5rem;">×</a>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Blog Posts Grid -->
    <section class="blog-posts">
        <?php if ($blog_query->have_posts()) : ?>
            <div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 2.5rem;">
                <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                    <article class="blog-card" style="background: var(--surface); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 16px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; border: 1px solid var(--border);">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail" style="height: 200px; overflow: hidden;">
                                <?php the_post_thumbnail('large', ['style' => 'width: 100%; height: 100%; object-fit: cover;']); ?>
                            </div>
                        <?php endif; ?>

                        <div style="padding: 2rem;">
                            <!-- Categories -->
                            <div class="post-categories" style="margin-bottom: 1rem;">
                                <?php
                                $post_categories = get_the_category();
                                if (!empty($post_categories)) :
                                    foreach ($post_categories as $category) :
                                ?>
                                            <span style="background: var(--accent); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                                <?php echo esc_html($category->name); ?>
                                            </span>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>

                            <!-- Title -->
                            <h2 style="margin-top: 0; margin-bottom: 0.75rem; font-size: 1.5rem;">
                                <a href="<?php the_permalink(); ?>" style="color: var(--text-primary); text-decoration: none; transition: color 0.3s ease;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-primary)'">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <!-- Meta -->
                            <div class="post-meta" style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                                <?php echo esc_html(get_the_date('M j, Y')); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?> min read
                                <?php if (get_comments_number() > 0) : ?>
                                    · <?php echo get_comments_number(); ?> comments
                                <?php endif; ?>
                            </div>

                            <!-- Excerpt -->
                            <p style="margin: 0; color: var(--text-primary); line-height: 1.6; font-size: 1rem;">
                                <?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 25)); ?>
                            </p>

                            <!-- Read More -->
                            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                                <a href="<?php the_permalink(); ?>" style="color: var(--accent); text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                    <?php esc_html_e('Read Full Article →', 'dadudekc'); ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination" style="text-align: center; margin-top: 4rem;">
                <?php
                $pagination_args = [
                    'total' => $blog_query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => __('← Previous', 'dadudekc'),
                    'next_text' => __('Next →', 'dadudekc'),
                ];

                if ($current_cat) {
                    $pagination_args['base'] = add_query_arg('category', $current_cat, get_permalink() . '%_%');
                    $pagination_args['format'] = '?paged=%#%';
                } elseif ($current_series) {
                    $pagination_args['base'] = add_query_arg('series', $current_series, get_permalink() . '%_%');
                    $pagination_args['format'] = '?paged=%#%';
                }

                echo paginate_links($pagination_args);
                ?>
            </div>

        <?php else : ?>
            <!-- Empty State -->
            <div class="empty-state" style="text-align: center; padding: 6rem 2rem; background: var(--surface); border-radius: 16px; border: 2px dashed var(--border);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📝</div>
                <h2 style="margin-bottom: 1rem; color: var(--text-primary);"><?php esc_html_e('No blog posts yet', 'dadudekc'); ?></h2>
                <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.1rem;"><?php esc_html_e('Articles and insights are being crafted. Check back soon for fresh content and deep dives.', 'dadudekc'); ?></p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>" style="background: var(--surface); color: var(--text-primary); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid var(--border);">
                        <?php esc_html_e('Get Updates', 'dadudekc'); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </section>

    <!-- Newsletter Signup -->
    <section class="newsletter-cta" style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-radius: 16px; padding: 4rem 2rem; text-align: center; margin-top: 4rem;">
        <h2 style="margin-top: 0; color: var(--accent); font-size: 2.5rem; margin-bottom: 1rem;"><?php esc_html_e('Stay Updated', 'dadudekc'); ?></h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem; color: var(--text-primary);"><?php esc_html_e('Get notified when new articles and insights are published.', 'dadudekc'); ?></p>

        <form class="newsletter-form" style="max-width: 500px; margin: 0 auto;">
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <input type="email" placeholder="<?php esc_attr_e('Enter your email', 'dadudekc'); ?>" style="flex: 1; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem;" required>
                <button type="submit" style="background: var(--accent); color: white; padding: 1rem 2rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                    <?php esc_html_e('Subscribe', 'dadudekc'); ?>
                </button>
            </div>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin: 0;"><?php esc_html_e('No spam, unsubscribe anytime. Your email stays private.', 'dadudekc'); ?></p>
        </form>
    </section>
</main>

<?php
get_footer();
?>