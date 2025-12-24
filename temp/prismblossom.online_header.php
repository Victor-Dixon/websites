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

    <header>
        <div class="header-content">
            <div class="logo-container">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <div class="site-title" class="site-title"><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a></div>
                <?php endif; ?>
            </div>
            <nav>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'nav-menu',
                    'container' => false,
                    'fallback_cb' => function () {
                        echo '<ul class="nav-menu">
                        <li><a href="' . home_url() . '">Home</a></li>
                        <li><a href="' . home_url('/carmyn') . '">Carmyn</a></li>
                        <li><a href="' . home_url('/guestbook') . '">Guestbook</a></li>
                        <li><a href="' . home_url('/invitation') . '">Invitation</a></li>
                        <li><a href="' . home_url('/birthday-fun') . '">Birthday Fun</a></li>
                    </ul>';
                    }
                ));
                ?>
            </nav>
        </div>
    </header>

    <main>