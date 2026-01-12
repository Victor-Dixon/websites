<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>

    <!-- Preconnect to Google Fonts for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">

    <!-- Meta tags for SEO and social sharing -->
    <meta name="description" content="<?php echo get_bloginfo('description'); ?>">
    <meta name="keywords" content="AI agents, swarm intelligence, collective intelligence, trading robots, automation, machine learning">

    <!-- Open Graph meta tags -->
    <meta property="og:title" content="<?php echo get_the_title(); ?>">
    <meta property="og:description" content="<?php echo get_bloginfo('description'); ?>">
    <meta property="og:image" content="<?php echo get_template_directory_uri(); ?>/images/og-image.jpg">
    <meta property="og:url" content="<?php echo get_permalink(); ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card meta tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo get_the_title(); ?>">
    <meta name="twitter:description" content="<?php echo get_bloginfo('description'); ?>">
    <meta name="twitter:image" content="<?php echo get_template_directory_uri(); ?>/images/twitter-card.jpg">
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="container">
            <div class="main-navigation">
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                🧠 <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                    }
                    ?>
                </div>

                <nav id="site-navigation" class="main-navigation-nav">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'fallback_cb'    => 'weareswarm_default_menu',
                    ));
                    ?>
                </nav>
            </div>
        </div>

        <!-- Mobile menu toggle -->
        <button class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
            <span class="mobile-menu-icon"></span>
        </button>
    </header>

    <div id="content" class="site-content">