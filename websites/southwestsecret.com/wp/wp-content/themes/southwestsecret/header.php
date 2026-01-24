<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset='<?php bloginfo('charset'); ?>'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <div class='container'>
            <div class='site-header'>
                <h1 class='site-title'><a href='<?php echo esc_url(home_url('/')); ?>'><?php bloginfo('name'); ?></a></h1>
                <nav class='main-navigation'>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id' => 'primary-menu',
                        'fallback_cb' => function() {
                            echo '<ul id="primary-menu"><li><a href="' . esc_url(home_url('/')) . '">Home</a></li><li><a href="' . esc_url(home_url('/about')) . '">About</a></li><li><a href="' . esc_url(home_url('/contact')) . '">Contact</a></li></ul>';
                        }
                    ));
                    ?>
                </nav>
            </div>
        </div>
    </header>
