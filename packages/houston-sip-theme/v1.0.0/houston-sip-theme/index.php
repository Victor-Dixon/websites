<?php get_header(); ?>

<main id="primary" class="site-main">
    <div class="container">

        <?php if (have_posts()) : ?>

            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Latest from Houston Sip Queen', 'houstonsipqueen'); ?></h1>
                <p class="page-subtitle">Cocktail recipes, event stories, and luxury bartending insights</p>
            </header>

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
                                    <?php esc_html_e('Read More', 'houstonsipqueen'); ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (is_singular()) : ?>
                            <footer class="entry-footer">
                                <?php
                                $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'houstonsipqueen'));
                                if ($tags_list) {
                                    printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'houstonsipqueen') . '</span>', $tags_list);
                                }
                                ?>
                            </footer>
                        <?php endif; ?>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <?php houstonsipqueen_pagination(); ?>

        <?php else : ?>

            <section class="no-results">
                <h1 class="page-title"><?php esc_html_e('Nothing Found', 'houstonsipqueen'); ?></h1>

                <?php if (is_home() && current_user_can('publish_posts')) : ?>
                    <p>
                        <?php
                        printf(
                            wp_kses(
                                __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'houstonsipqueen'),
                                array('a' => array('href' => array()))
                            ),
                            esc_url(admin_url('post-new.php'))
                        );
                        ?>
                    </p>
                <?php elseif (is_search()) : ?>
                    <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'houstonsipqueen'); ?></p>
                    <?php get_search_form(); ?>
                <?php else : ?>
                    <p><?php esc_html_e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'houstonsipqueen'); ?></p>
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
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(11, 11, 15, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(201, 162, 106, 0.1);
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(11, 11, 15, 0.15);
}

.entry-header {
    padding: 1.5rem;
}

.entry-title {
    margin-bottom: 0.5rem;
}

.entry-title a {
    color: var(--onyx);
    text-decoration: none;
    transition: color 0.3s ease;
}

.entry-title a:hover {
    color: var(--rosegold);
}

.entry-meta {
    font-size: 0.875rem;
    color: var(--onyx-light);
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
    color: var(--rosegold);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: var(--berry);
}

.no-results {
    text-align: center;
    padding: 3rem 0;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem 0;
}

.page-title {
    color: var(--onyx);
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: var(--onyx-light);
    font-size: 1.1rem;
    font-family: 'Montserrat', sans-serif;
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
    border: 1px solid rgba(201, 162, 106, 0.3);
    border-radius: 25px;
    text-decoration: none;
    color: var(--onyx-light);
    transition: all 0.3s ease;
}

.pagination-list a:hover,
.pagination-list .active span {
    background: var(--rosegold);
    color: var(--white);
    border-color: var(--rosegold);
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