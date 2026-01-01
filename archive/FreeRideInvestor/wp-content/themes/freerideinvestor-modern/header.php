<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div class="site-wrapper">
        <header class="site-header">
            <div class="container">
                <div class="header-container">
                    <div class="site-logo">
                        <?php
                        if (has_custom_logo()) {
                            the_custom_logo();
                        } else {
                        ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php bloginfo('name'); ?>
                            </a>
                        <?php
                        }
                        ?>
                    </div>

                    <nav class="main-navigation" id="main-navigation">
                        <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Toggle navigation menu">
                            <span>☰</span>
                        </button>

                        <?php
                        if (has_nav_menu('primary')) {
                            wp_nav_menu(array(
                                'theme_location' => 'primary',
                                'container' => false,
                                'menu_class' => 'nav-menu',
                                'fallback_cb' => false,
                            ));
                        } else {
                        ?>
                            <ul class="nav-menu">
                                <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                                <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
                                <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
                                <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
                            </ul>
                        <?php
                        }
                        ?>
                    </nav>
                </div>
            </div>
        </header>

        <main class="site-main">