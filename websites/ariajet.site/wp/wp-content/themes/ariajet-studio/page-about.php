<?php
/**
 * About Page Template (slug: about)
 *
 * This template intentionally ignores the page editor content and renders
 * a simple "Space" layout with a working WordPress comments form.
 *
 * @package AriaJet_Studio
 */

get_header();
?>

<main id="main" class="site-main page-about-template">
    <section class="section">
        <div class="container container--narrow">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                    <header class="page-header reveal">
                        <h1 class="page-title">
                            <?php echo esc_html(get_bloginfo('name')) . '&#039;s Space'; ?>
                        </h1>
                    </header>

                    <div class="entry-content reveal">
                        <hr class="about-divider" />

                        <h2><?php _e('Things about me:', 'ariajet-studio'); ?></h2>
                        <ul class="about-list">
                            <li>[Point 1 about yourself]</li>
                            <li>[Point 2 about yourself]</li>
                            <li>[Point 3 about yourself]</li>
                        </ul>

                        <p class="about-comments-intro">
                            <strong><?php _e('Leave a comment below!', 'ariajet-studio'); ?></strong>
                        </p>
                    </div>

                    <div class="about-comments reveal">
                        <?php comments_template(); ?>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </div>
    </section>
</main>

<style>
.page-about-template .about-divider {
    border: 0;
    height: 1px;
    background: var(--border);
    margin: var(--space-8) 0 var(--space-10);
}
.page-about-template .about-list {
    margin: var(--space-6) 0 var(--space-10);
    padding-left: var(--space-8);
}
.page-about-template .about-comments {
    margin-top: var(--space-12);
}
</style>

<?php
get_footer();

