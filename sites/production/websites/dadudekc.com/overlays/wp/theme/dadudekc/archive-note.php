<?php
/**
 * Notes archive template.
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <header>
        <h1><?php esc_html_e('Idea Lab Notes', 'dadudekc'); ?></h1>
        <p class="post-meta"><?php esc_html_e('Quick hits, raw ideas, and brainstorms.', 'dadudekc'); ?></p>
    </header>
    <?php if (have_posts()) : ?>
        <div class="posts-list">
            <?php while (have_posts()) : the_post(); ?>
                <article class="card">
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p class="post-meta"><?php echo esc_html(get_the_date()); ?></p>
                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 35)); ?></p>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No notes yet.', 'dadudekc'); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
