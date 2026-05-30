<?php
/**
 * Template Name: Idea Lab
 *
 * @package DaDudeKC
 */

get_header();

$tag = isset($_GET['tag']) ? sanitize_text_field(wp_unslash($_GET['tag'])) : '';
$search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

$common_args = [
    'posts_per_page' => 10,
    'post_status' => 'publish',
    's' => $search,
];

if ($tag) {
    $common_args['tag'] = $tag;
}

$notes_query = new WP_Query(array_merge($common_args, [
    'post_type' => 'note',
]));

$articles_query = new WP_Query(array_merge($common_args, [
    'post_type' => 'post',
    'category_name' => 'idea-lab',
]));

$idea_tags = get_terms([
    'taxonomy' => 'post_tag',
    'hide_empty' => true,
]);
?>
<main class="content-area">
    <header class="page-header" style="text-align: center; margin-bottom: 3rem;">
        <h1 style="font-size: 3rem; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent), var(--text-primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php esc_html_e('Idea Lab', 'dadudekc'); ?></h1>
        <p class="post-meta" style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 2rem;"><?php esc_html_e('Where ideas spark, evolve, and find their way into reality.', 'dadudekc'); ?></p>

        <!-- Hero Stats -->
        <div class="idea-stats" style="display: flex; justify-content: center; gap: 2rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: var(--accent);"><?php echo wp_count_posts('note')->publish; ?></div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php esc_html_e('Notes', 'dadudekc'); ?></div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: var(--accent);"><?php echo wp_count_posts('post')->publish; ?></div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php esc_html_e('Articles', 'dadudekc'); ?></div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: var(--accent);"><?php echo count($idea_tags); ?></div>
                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php esc_html_e('Topics', 'dadudekc'); ?></div>
            </div>
        </div>
    </header>

    <!-- Enhanced Search & Filter Section -->
    <div class="search-filter-section" style="background: var(--surface); border-radius: 12px; padding: 2rem; margin-bottom: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <form class="search-form" method="get" action="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>" style="margin-bottom: 2rem;">
            <div style="position: relative; max-width: 600px; margin: 0 auto;">
                <input type="search" name="s" placeholder="<?php esc_attr_e('Search through ideas, notes, and insights...', 'dadudekc'); ?>" value="<?php echo esc_attr($search); ?>" style="width: 100%; padding: 1rem 3rem 1rem 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1.1rem;">
                <button type="submit" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: var(--accent); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">🔍</button>
            </div>
            <?php if ($tag) : ?>
                <input type="hidden" name="tag" value="<?php echo esc_attr($tag); ?>">
            <?php endif; ?>
        </form>

        <!-- Active Filters Display -->
        <?php if ($tag || $search) : ?>
            <div class="active-filters" style="margin-bottom: 2rem; padding: 1rem; background: rgba(0, 212, 255, 0.1); border-radius: 8px;">
                <strong><?php esc_html_e('Active filters:', 'dadudekc'); ?></strong>
                <?php if ($tag) : ?>
                    <span style="background: var(--accent); color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem; margin-left: 0.5rem;">
                        <?php echo esc_html($tag); ?>
                        <a href="<?php echo esc_url(remove_query_arg('tag')); ?>" style="color: white; text-decoration: none; margin-left: 0.5rem;">×</a>
                    </span>
                <?php endif; ?>
                <?php if ($search) : ?>
                    <span style="background: var(--accent); color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.9rem; margin-left: 0.5rem;">
                        "<?php echo esc_html($search); ?>"
                        <a href="<?php echo esc_url(remove_query_arg('s')); ?>" style="color: white; text-decoration: none; margin-left: 0.5rem;">×</a>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Tag Cloud -->
        <div class="tag-section">
            <h3 style="margin-bottom: 1rem; color: var(--text-primary);"><?php esc_html_e('Explore by Topic', 'dadudekc'); ?></h3>
            <div class="tag-list" style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                <?php if (!empty($idea_tags) && !is_wp_error($idea_tags)) : ?>
                    <?php foreach ($idea_tags as $term) :
                        $is_active = ($tag === $term->slug);
                    ?>
                        <a class="tag-pill <?php echo $is_active ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('tag', $term->slug, dadudekc_get_idea_lab_url())); ?>" style="background: <?php echo $is_active ? 'var(--accent)' : 'var(--border)'; ?>; color: <?php echo $is_active ? 'white' : 'var(--text-primary)'; ?>; padding: 0.5rem 1rem; border-radius: 20px; text-decoration: none; font-size: 0.9rem; transition: all 0.3s ease;">
                            <?php echo esc_html($term->name); ?>
                            <span style="margin-left: 0.5rem; opacity: 0.7;">(<?php echo $term->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="color: var(--text-secondary); margin: 0;"><?php esc_html_e('Tags will appear as you add content to the Idea Lab.', 'dadudekc'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    <section class="content-section" style="margin-bottom: 4rem;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 class="section-title" style="font-size: 2rem; margin-bottom: 0.5rem;">📝 <?php esc_html_e('Notes', 'dadudekc'); ?></h2>
                <p class="section-subtitle" style="color: var(--text-secondary); margin: 0;"><?php esc_html_e('Quick captures of ideas, insights, and observations.', 'dadudekc'); ?></p>
            </div>
            <?php if ($notes_query->found_posts > 0) : ?>
                <div style="text-align: right;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--accent);"><?php echo $notes_query->found_posts; ?></div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">notes</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
            <?php if ($notes_query->have_posts()) : ?>
                <?php while ($notes_query->have_posts()) : $notes_query->the_post(); ?>
                    <article class="content-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; border: 1px solid var(--border);">
                        <div style="display: flex; align-items: flex-start; gap: 1rem;">
                            <div style="flex-shrink: 0; width: 4px; height: 4rem; background: linear-gradient(to bottom, var(--accent), transparent); border-radius: 2px;"></div>
                            <div style="flex: 1;">
                                <h3 style="margin-top: 0; margin-bottom: 0.5rem; font-size: 1.2rem;">
                                    <a href="<?php the_permalink(); ?>" style="color: var(--text-primary); text-decoration: none; transition: color 0.3s ease;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-primary)'">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                <p class="post-meta" style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                                    <?php echo esc_html(get_the_date('M j, Y')); ?> · Note
                                </p>
                                <p style="margin: 0; color: var(--text-primary); line-height: 1.6;">
                                    <?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 25)); ?>
                                </p>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: var(--surface); border-radius: 12px; border: 2px dashed var(--border);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">💭</div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary);"><?php esc_html_e('No notes yet', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;"><?php esc_html_e('Ideas are brewing! Check back soon for fresh insights and observations.', 'dadudekc'); ?></p>
                    <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>" style="background: var(--accent); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600;"><?php esc_html_e('Browse Articles Instead →', 'dadudekc'); ?></a>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>

    <!-- Articles Section -->
    <section class="content-section" style="margin-bottom: 4rem;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 class="section-title" style="font-size: 2rem; margin-bottom: 0.5rem;">📖 <?php esc_html_e('Articles', 'dadudekc'); ?></h2>
                <p class="section-subtitle" style="color: var(--text-secondary); margin: 0;"><?php esc_html_e('Deep dives, tutorials, and comprehensive explorations.', 'dadudekc'); ?></p>
            </div>
            <?php if ($articles_query->found_posts > 0) : ?>
                <div style="text-align: right;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: var(--accent);"><?php echo $articles_query->found_posts; ?></div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">articles</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 2rem;">
            <?php if ($articles_query->have_posts()) : ?>
                <?php while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                    <article class="content-card featured" style="background: var(--surface); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; border: 1px solid var(--border);">
                        <div style="padding: 2rem;">
                            <h3 style="margin-top: 0; margin-bottom: 0.5rem; font-size: 1.3rem;">
                                <a href="<?php the_permalink(); ?>" style="color: var(--text-primary); text-decoration: none; transition: color 0.3s ease;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--text-primary)'">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <p class="post-meta" style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                                <?php echo esc_html(get_the_date('M j, Y')); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?> min read
                            </p>
                            <p style="margin: 0; color: var(--text-primary); line-height: 1.6; font-size: 1rem;">
                                <?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 35)); ?>
                            </p>
                        </div>
                        <div style="padding: 1rem 2rem; background: rgba(0, 212, 255, 0.05); border-top: 1px solid var(--border);">
                            <a href="<?php the_permalink(); ?>" style="color: var(--accent); text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                <?php esc_html_e('Read Full Article →', 'dadudekc'); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: var(--surface); border-radius: 12px; border: 2px dashed var(--border);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary);"><?php esc_html_e('Articles in progress', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;"><?php esc_html_e('Deep dives and comprehensive guides are being crafted. Stay tuned for detailed explorations of complex topics.', 'dadudekc'); ?></p>
                    <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>" style="background: var(--accent); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600;"><?php esc_html_e('View Recent Posts →', 'dadudekc'); ?></a>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="idea-lab-cta" style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-radius: 12px; padding: 3rem; text-align: center; margin-bottom: 2rem;">
        <h2 style="margin-top: 0; color: var(--accent); font-size: 2rem;"><?php esc_html_e('Have an Idea Worth Exploring?', 'dadudekc'); ?></h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; color: var(--text-primary);"><?php esc_html_e('Share your thoughts, questions, or topics you\'d like me to dive into. The Idea Lab is always growing!', 'dadudekc'); ?></p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>" style="background: var(--accent); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600;"><?php esc_html_e('Suggest a Topic →', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>" style="background: var(--surface); color: var(--text-primary); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid var(--border);"><?php esc_html_e('Read the Blog', 'dadudekc'); ?></a>
        </div>
    </section>
</main>
<?php
get_footer();
