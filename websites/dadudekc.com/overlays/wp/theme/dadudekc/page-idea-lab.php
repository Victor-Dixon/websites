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
    <header>
        <h1><?php esc_html_e('Idea Lab', 'dadudekc'); ?></h1>
        <p class="post-meta"><?php esc_html_e('Browse notes, articles, and brainstorms. Filter by tag or search fast.', 'dadudekc'); ?></p>
    </header>

    <form class="search-form" method="get" action="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>">
        <input type="search" name="s" placeholder="<?php esc_attr_e('Search ideas...', 'dadudekc'); ?>" value="<?php echo esc_attr($search); ?>">
        <?php if ($tag) : ?>
            <input type="hidden" name="tag" value="<?php echo esc_attr($tag); ?>">
        <?php endif; ?>
        <button type="submit"><?php esc_html_e('Search', 'dadudekc'); ?></button>
    </form>

    <div class="tag-list">
        <?php if (!empty($idea_tags) && !is_wp_error($idea_tags)) : ?>
            <?php foreach ($idea_tags as $term) : ?>
                <a class="tag-pill" href="<?php echo esc_url(add_query_arg('tag', $term->slug, dadudekc_get_idea_lab_url())); ?>">
                    <?php echo esc_html($term->name); ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <section style="margin-top: 2.5rem;">
        <div class="section-header">
            <div>
                <h2 class="section-title"><?php esc_html_e('Notes', 'dadudekc'); ?></h2>
                <p class="section-subtitle"><?php esc_html_e('Short-form ideas, captured quickly.', 'dadudekc'); ?></p>
            </div>
        </div>
        <div class="posts-list">
            <?php if ($notes_query->have_posts()) : ?>
                <?php while ($notes_query->have_posts()) : $notes_query->the_post(); ?>
                    <article class="card">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="post-meta"><?php echo esc_html(get_the_date()); ?></p>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 28)); ?></p>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="card"><?php esc_html_e('No notes found yet.', 'dadudekc'); ?></div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>

    <section style="margin-top: 3rem;">
        <div class="section-header">
            <div>
                <h2 class="section-title"><?php esc_html_e('Articles', 'dadudekc'); ?></h2>
                <p class="section-subtitle"><?php esc_html_e('Long-form explorations and deep dives.', 'dadudekc'); ?></p>
            </div>
        </div>
        <div class="posts-list">
            <?php if ($articles_query->have_posts()) : ?>
                <?php while ($articles_query->have_posts()) : $articles_query->the_post(); ?>
                    <article class="card">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="post-meta"><?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?></p>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 30)); ?></p>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="card"><?php esc_html_e('No Idea Lab articles published yet.', 'dadudekc'); ?></div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </section>
</main>
<?php
get_footer();
