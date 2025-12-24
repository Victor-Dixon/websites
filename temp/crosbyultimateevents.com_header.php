<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header class="site-header">
        <div class="header-container">
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <div class="site-title" class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php bloginfo('name'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <nav class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary Menu', 'crosbyultimateevents'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'nav-menu',
                    'container' => false,
                    'fallback_cb' => 'crosbyultimateevents_default_menu',
                ));
                ?>
            </nav>
            <div class="header-cta">
                <a href="<?php echo esc_url(home_url('/consultation')); ?>" class="btn-primary btn-small">Book Consultation</a>
            </div>
        </div>
    </header>

    <?php
    // Default menu fallback
    function crosbyultimateevents_default_menu()
    {
        echo '<ul class="nav-menu">';
        echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
        echo '<li><a href="' . esc_url(home_url('/services')) . '">Services</a></li>';
        echo '<li><a href="' . esc_url(home_url('/portfolio')) . '">Portfolio</a></li>';
        echo '<li><a href="' . esc_url(home_url('/blog')) . '">Blog</a></li>';
        echo '<li><a href="' . esc_url(home_url('/contact')) . '">Contact</a></li>';
        echo '</ul>';
    }
    ?>