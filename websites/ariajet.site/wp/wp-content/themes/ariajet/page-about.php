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
            <article id="post-<?php the_ID(); ?>" <?php post_class('about-card'); ?>>
                <header class="about-hero">
                    <div class="about-hero__inner">
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
                    </div>
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
                                <?php echo esc_html__('Contact', 'ariajet'); ?>
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

.page-about-template {
    padding: 110px 1.25rem 60px;
    min-height: 80vh;
}
.page-about-template .container {
    max-width: 1100px;
}
.page-about-template .about-card {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.14);
    background:
        radial-gradient(1200px 500px at 10% 0%, rgba(0, 255, 209, 0.14), transparent 55%),
        radial-gradient(900px 500px at 95% 15%, rgba(255, 77, 196, 0.14), transparent 60%),
        radial-gradient(800px 600px at 40% 120%, rgba(124, 92, 255, 0.22), transparent 60%),
        linear-gradient(135deg, rgba(10, 12, 25, 0.94) 0%, rgba(18, 9, 31, 0.92) 100%);
    backdrop-filter: blur(10px);
}
.page-about-template .about-card::before {
    content: "";
    position: absolute;
    inset: -2px;
    pointer-events: none;
    background:
        radial-gradient(1px 1px at 12% 20%, rgba(255,255,255,0.9), rgba(255,255,255,0)),
        radial-gradient(1px 1px at 22% 55%, rgba(255,255,255,0.8), rgba(255,255,255,0)),
        radial-gradient(1px 1px at 35% 35%, rgba(255,255,255,0.7), rgba(255,255,255,0)),
        radial-gradient(1px 1px at 55% 30%, rgba(255,255,255,0.75), rgba(255,255,255,0)),
        radial-gradient(1px 1px at 78% 22%, rgba(255,255,255,0.9), rgba(255,255,255,0)),
        radial-gradient(1px 1px at 88% 62%, rgba(255,255,255,0.8), rgba(255,255,255,0));
    opacity: 0.6;
    mix-blend-mode: screen;
}
.page-about-template .about-card::after {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: radial-gradient(900px 300px at 50% 0%, rgba(255,255,255,0.10), transparent 60%);
    opacity: 0.35;
}
.page-about-template .about-hero {
    position: relative;
    padding: 56px 32px 40px;
    text-align: center;
    background:
        radial-gradient(900px 260px at 50% 0%, rgba(0, 255, 209, 0.35), transparent 65%),
        linear-gradient(135deg, rgba(124, 92, 255, 0.75) 0%, rgba(255, 77, 196, 0.60) 45%, rgba(0, 255, 209, 0.50) 100%);
    color: #fff;
}
.page-about-template .about-hero__inner {
    position: relative;
    z-index: 1;
}
.page-about-template .about-hero::after {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(180deg, rgba(0,0,0,0.0) 0%, rgba(0,0,0,0.28) 100%);
}
.page-about-template .about-eyebrow {
    margin: 0 0 10px;
    opacity: 0.9;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 0.85rem;
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.page-about-template .about-title {
    margin: 0;
    font-size: 2.6rem;
    line-height: 1.12;
    color: #fff;
    font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
    letter-spacing: -0.02em;
}
.page-about-template .about-tagline {
    margin: 14px auto 0;
    max-width: 60ch;
    font-size: 1.1rem;
    opacity: 0.92;
    font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}
.page-about-template .about-summary {
    margin: 14px auto 0;
    max-width: 70ch;
    font-size: 1.05rem;
    line-height: 1.6;
    opacity: 0.95;
    color: rgba(255, 255, 255, 0.92);
    font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}
.page-about-template .about-body {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 28px;
    padding: 34px 32px 38px;
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
    border: 1px solid rgba(255, 255, 255, 0.20);
    box-shadow: 0 20px 55px rgba(0,0,0,0.35);
    transform: translateZ(0);
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
    justify-content: flex-start;
    gap: 12px;
    padding-top: 8px;
}
.page-about-template .about-button {
    display: inline-block;
    padding: 0.9rem 1.25rem;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 600;
    background: linear-gradient(135deg, rgba(0, 255, 209, 0.95) 0%, rgba(124, 92, 255, 0.92) 100%);
    color: rgba(10, 12, 25, 0.96);
    border: 1px solid rgba(255, 255, 255, 0.14);
    box-shadow: 0 14px 30px rgba(0, 255, 209, 0.18);
    font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.page-about-template .about-button:hover {
    transform: translateY(-1px);
}

@media (max-width: 900px) {
    .page-about-template .about-body {
        grid-template-columns: 1fr;
        padding: 26px 18px 30px;
    }
    .page-about-template .about-hero {
        padding: 44px 18px 32px;
    }
    .page-about-template .about-title {
        font-size: 2.1rem;
    }
    .page-about-template .about-cta {
        justify-content: center;
    }
}
</style>

<?php
get_footer();

