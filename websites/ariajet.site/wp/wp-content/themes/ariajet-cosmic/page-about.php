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
            <article id="post-<?php the_ID(); ?>" <?php post_class('page-content cosmic-card about-card'); ?>>
                <header class="about-hero">
                    <p class="about-eyebrow"><?php echo esc_html(get_bloginfo('name')); ?></p>
                    <h1 class="about-title"><?php the_title(); ?></h1>
                    <?php $tagline = get_bloginfo('description'); ?>
                    <?php if (!empty($tagline)) : ?>
                        <p class="about-tagline"><?php echo esc_html($tagline); ?></p>
                    <?php endif; ?>

                    <?php
                    // Summary: use page excerpt if set, otherwise derive from content.
                    $summary = trim((string) get_the_excerpt());
                    if (empty($summary)) {
                        $summary = wp_strip_all_tags((string) get_the_content());
                        $summary = trim(preg_replace('/\s+/', ' ', $summary));
                        $max_len = 190;
                        if (function_exists('mb_substr')) {
                            $summary = mb_substr($summary, 0, $max_len);
                        } else {
                            $summary = substr($summary, 0, $max_len);
                        }
                        $summary = rtrim($summary, " \t\n\r\0\x0B.,;:!-–—") . '…';
                    }
                    ?>
                    <?php if (!empty($summary)) : ?>
                        <p class="about-summary"><?php echo esc_html($summary); ?></p>
                    <?php endif; ?>
                </header>

                <div class="about-body">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="about-media">
                            <?php the_post_thumbnail('large', array('class' => 'about-avatar', 'loading' => 'lazy')); ?>
                        </div>
                    <?php endif; ?>

                    <div class="about-content entry-content">
                        <?php the_content(); ?>
                    </div>

                    <div class="about-cta">
                        <?php
                        $contact = get_page_by_path('contact');
                        if ($contact) :
                            ?>
                            <a class="about-button" href="<?php echo esc_url(get_permalink($contact)); ?>">
                                <?php echo esc_html__('Contact', 'ariajet-cosmic'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<style>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700&family=JetBrains+Mono:wght@500;600&display=swap');

.page-about-template .page-content {
    padding: 0;
}
.page-about-template .about-hero {
    padding: var(--space-12) var(--space-10) var(--space-10);
    text-align: center;
    background:
        radial-gradient(900px 260px at 50% 0%, rgba(0, 255, 209, 0.28), transparent 65%),
        linear-gradient(135deg, rgba(124, 92, 255, 0.72) 0%, rgba(255, 77, 196, 0.58) 45%, rgba(0, 255, 209, 0.48) 100%);
}
.page-about-template .about-eyebrow {
    margin: 0 0 var(--space-3);
    opacity: 0.92;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 0.85rem;
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.page-about-template .about-title {
    margin: 0;
    font-size: 2.4rem;
    line-height: 1.12;
    font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
    letter-spacing: -0.02em;
}
.page-about-template .about-tagline {
    margin: var(--space-4) auto 0;
    max-width: 60ch;
    font-size: 1.05rem;
    opacity: 0.92;
    font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}
.page-about-template .about-summary {
    margin: var(--space-4) auto 0;
    max-width: 70ch;
    font-size: 1.02rem;
    line-height: 1.6;
    opacity: 0.95;
    color: rgba(255, 255, 255, 0.92);
    font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}
.page-about-template .about-body {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: var(--space-8);
    padding: var(--space-10);
}
.page-about-template .about-media {
    display: flex;
    justify-content: center;
}
.page-about-template .about-avatar {
    width: 260px;
    height: 260px;
    object-fit: cover;
    border-radius: 22px;
    border: 1px solid rgba(255, 255, 255, 0.14);
    box-shadow: 0 20px 45px rgba(0,0,0,0.25);
}
.page-about-template .about-content {
    max-width: 75ch;
    color: rgba(255, 255, 255, 0.92);
    font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
    font-size: 1.05rem;
    line-height: 1.75;
}
.page-about-template .about-content > *:first-child {
    margin-top: 0;
}
.page-about-template .about-content a {
    color: rgba(0, 255, 209, 0.95);
    text-decoration: none;
    border-bottom: 1px solid rgba(0, 255, 209, 0.35);
}
.page-about-template .about-content a:hover {
    border-bottom-color: rgba(0, 255, 209, 0.7);
}
.page-about-template .about-cta {
    grid-column: 1 / -1;
    display: flex;
    gap: var(--space-3);
    padding-top: var(--space-2);
}
.page-about-template .about-button {
    display: inline-block;
    padding: 0.9rem 1.25rem;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.92);
    color: rgba(10, 12, 25, 0.96);
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 14px 30px rgba(0, 255, 209, 0.18);
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.page-about-template .about-button:hover {
    transform: translateY(-1px);
}

@media (max-width: 900px) {
    .page-about-template .about-body {
        grid-template-columns: 1fr;
        padding: var(--space-8) var(--space-6);
    }
    .page-about-template .about-hero {
        padding: var(--space-10) var(--space-6) var(--space-8);
    }
    .page-about-template .about-title {
        font-size: 2.0rem;
    }
    .page-about-template .about-cta {
        justify-content: center;
    }
}
</style>

<?php
get_footer();

