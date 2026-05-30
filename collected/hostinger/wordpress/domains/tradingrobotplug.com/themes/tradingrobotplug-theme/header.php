<?php
/*
C:\TheTradingRobotPlugWeb\my-custom-theme\header.php
Description: The header template for The Trading Robot Plug theme, including the site logo, navigation menu, and meta information.
Version: 1.0.0
Author: Victor Dixon
*/
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
    <div class="container">
        <div class="site-logo">
            <?php
            if (function_exists('the_custom_logo') && has_custom_logo()) {
                the_custom_logo();
            } else {
                echo '<a href="' . esc_url(home_url('/')) . '"><strong>TradingRobotPlug</strong></a>';
            }
            ?>
        </div>

        <nav class="main-navigation">
            <button class="mobile-menu-toggle" aria-label="Toggle navigation menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'nav-menu',
                'depth' => 2,
                'fallback_cb' => 'wp_page_menu',
            ));
            ?>

            <div class="nav-cta">
                <a href="#pricing" class="btn btn-secondary">Get Started</a>
            </div>
        </nav>
    </div>
</header>
