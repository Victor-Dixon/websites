<?php get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">

        <?php if (have_posts()) : ?>

            <div class="posts-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <header class="entry-header">
                            <?php
                            if (is_singular()) :
                                the_title('<h1 class="entry-title">', '</h1>');
                            else :
                                the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
                            endif;

                            if ('post' === get_post_type()) :
                                ?>
                                <div class="entry-meta">
                                    <span class="posted-on">
                                        <?php echo get_the_date(); ?>
                                    </span>
                                    <span class="byline">
                                        <?php echo get_the_author(); ?>
                                    </span>
                                    <?php if (get_the_category_list(', ')) : ?>
                                        <span class="cat-links">
                                            <?php echo get_the_category_list(', '); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </header>

                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="entry-content">
                            <?php
                            if (is_singular()) :
                                the_content();
                            else :
                                the_excerpt();
                                ?>
                                <a href="<?php the_permalink(); ?>" class="read-more">
                                    <?php esc_html_e('Read More', 'weareswarm'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (is_singular()) : ?>
                            <footer class="entry-footer">
                                <?php
                                $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'weareswarm'));
                                if ($tags_list) {
                                    printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'weareswarm') . '</span>', $tags_list);
                                }
                                ?>
                            </footer>
                        <?php endif; ?>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <?php weareswarm_pagination(); ?>

        <?php else : ?>

            <section class="no-results">
                <h1 class="page-title"><?php esc_html_e('Nothing Found', 'weareswarm'); ?></h1>

                <?php if (is_home() && current_user_can('publish_posts')) : ?>
                    <p>
                        <?php
                        printf(
                            wp_kses(
                                __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'weareswarm'),
                                array('a' => array('href' => array()))
                            ),
                            esc_url(admin_url('post-new.php'))
                        );
                        ?>
                    </p>
                <?php elseif (is_search()) : ?>
                    <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'weareswarm'); ?></p>
                    <?php get_search_form(); ?>
                <?php else : ?>
                    <p><?php esc_html_e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'weareswarm'); ?></p>
                    <?php get_search_form(); ?>
                <?php endif; ?>
            </section>

        <?php endif; ?>

    </div><!-- .container -->
</main><!-- #primary -->

<?php get_footer(); ?>

<style>
.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.post-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.entry-header {
    padding: 1.5rem;
}

.entry-title {
    margin-bottom: 0.5rem;
}

.entry-title a {
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.3s ease;
}

.entry-title a:hover {
    color: #a855f7;
}

.entry-meta {
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 1rem;
}

.entry-meta span {
    display: inline-block;
    margin-right: 1rem;
}

.post-thumbnail img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.entry-content {
    padding: 1.5rem;
}

.read-more {
    display: inline-block;
    margin-top: 1rem;
    color: #a855f7;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: #06b6d4;
}

.no-results {
    text-align: center;
    padding: 3rem 0;
}

.pagination {
    margin-top: 3rem;
}

.pagination-list {
    display: flex;
    justify-content: center;
    list-style: none;
    gap: 0.5rem;
}

.pagination-list a,
.pagination-list span {
    display: block;
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #666;
    transition: all 0.3s ease;
}

.pagination-list a:hover,
.pagination-list .active span {
    background: #a855f7;
    color: white;
    border-color: #a855f7;
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .post-card {
        margin: 0 1rem;
    }
}
</style>