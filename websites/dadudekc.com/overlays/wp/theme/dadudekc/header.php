<?php
/**
 * Header template.
 *
 * @package DaDudeKC
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
            const theme = stored ? stored : (prefersLight ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
    <div class="container site-header-inner">
        <a class="site-brand" href="<?php echo esc_url(home_url('/')); ?>">DaDudeKC</a>
        <nav class="site-nav" aria-label="<?php esc_attr_e('Primary navigation', 'dadudekc'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_portfolio_url()); ?>"><?php esc_html_e('Portfolio', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>"><?php esc_html_e('Idea Lab', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>"><?php esc_html_e('Blog', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_now_url()); ?>"><?php esc_html_e('Now', 'dadudekc'); ?></a>
            <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>"><?php esc_html_e('Contact', 'dadudekc'); ?></a>
        </nav>
        <button class="toggle-button" type="button" id="theme-toggle" aria-label="<?php esc_attr_e('Toggle theme', 'dadudekc'); ?>">
            <?php esc_html_e('Toggle Light', 'dadudekc'); ?>
        </button>
    </div>
</header>
<script>
    (function () {
        const toggle = document.getElementById('theme-toggle');
        if (!toggle) return;
        const root = document.documentElement;
        const updateLabel = () => {
            const current = root.getAttribute('data-theme') || 'dark';
            toggle.textContent = current === 'dark' ? 'Toggle Light' : 'Toggle Dark';
        };
        updateLabel();
        toggle.addEventListener('click', () => {
            const current = root.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateLabel();
        });
    })();
</script>
