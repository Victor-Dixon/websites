<?php
/**
 * About Page Template (slug: about)
 *
 * This template intentionally ignores the page editor content and renders
 * a simple "Space" layout with a working WordPress comments form.
 *
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main page-template page-about-template">
    <div class="container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('page-content cosmic-card'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title page-title">
                        <?php echo esc_html(get_bloginfo('name')) . '&#039;s Space'; ?>
                    </h1>
                </header>

                <div class="entry-content">
                    <hr class="about-divider" />

                    <h2 class="about-heading"><?php _e('Things about me:', 'ariajet-cosmic'); ?></h2>
                    <ul class="about-list">
                        <li>[Point 1 about yourself]</li>
                        <li>[Point 2 about yourself]</li>
                        <li>[Point 3 about yourself]</li>
                    </ul>

                    <p class="about-comments-intro">
                        <strong><?php _e('Leave a comment below!', 'ariajet-cosmic'); ?></strong>
                    </p>
                </div>

                <div class="about-comments">
                    <?php
                    // We show comments + form (the theme also forces comments open for slug "about").
                    comments_template();
                    ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<style>
.page-about-template .page-content {
    padding: var(--space-12);
    max-width: 900px;
    margin: 0 auto;
}
.page-about-template .about-divider {
    border: 0;
    height: 1px;
    background: rgba(255, 255, 255, 0.12);
    margin: var(--space-6) 0 var(--space-8);
}
.page-about-template .about-list {
    margin: var(--space-4) 0 var(--space-8);
    padding-left: var(--space-8);
}
.page-about-template .about-comments {
    margin-top: var(--space-10);
}
</style>

<?php
get_footer();

