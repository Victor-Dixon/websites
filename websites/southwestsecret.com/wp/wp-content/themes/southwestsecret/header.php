<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="stars"></div>
<div class="twinkling"></div>

<header>
    <div class="header-content">
        <div class="logo-container">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="<?php bloginfo('name'); ?> Logo" class="header-logo">
            <?php endif; ?>
        </div>
        <nav>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => 'nav-menu',
                'container' => false,
                'fallback_cb' => function() {
                    echo '<ul class="nav-menu">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#music">Music</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="aria.html">Aria</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>';
                }
            ));
            ?>
        </nav>
    </div>
</header>

<main>

