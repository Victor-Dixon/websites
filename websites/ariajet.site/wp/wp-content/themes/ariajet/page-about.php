<?php
/**
 * About Page Template (slug: about)
 *
 * This template intentionally ignores the page editor content and renders
 * a simple "Space" layout with a working WordPress comments form.
 *
 * @package AriaJet
 */

get_header();
?>

<main id="main" class="site-main page-about-template">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('game-showcase'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title">
                        <?php echo esc_html(get_bloginfo('name')) . '&#039;s Space'; ?>
                    </h1>
                </header>

                <div class="entry-content">
                    <hr class="about-divider" />

                    <h2 class="about-heading"><?php _e('Things about me:', 'ariajet'); ?></h2>
                    <ul class="about-list">
                        <li>[Point 1 about yourself]</li>
                        <li>[Point 2 about yourself]</li>
                        <li>[Point 3 about yourself]</li>
                    </ul>

                    <p class="about-comments-intro">
                        <strong><?php _e('Leave a comment below!', 'ariajet'); ?></strong>
                    </p>
                </div>

                <div class="about-comments">
                    <?php comments_template(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<style>
.page-about-template .about-divider {
    border: 0;
    height: 1px;
    background: rgba(255, 255, 255, 0.2);
    margin: 1.5rem 0 2rem;
}
.page-about-template .about-list {
    margin: 1rem 0 2rem;
    padding-left: 1.5rem;
}
.page-about-template .about-comments {
    margin-top: 2.5rem;
}
</style>

<?php
get_footer();

