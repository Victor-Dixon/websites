<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFFBF7">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Google Fonts - Elegant typography pairing -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="container">
            <div class="header-inner">
                <!-- Logo / Branding -->
                <div class="site-branding">
                    <h1 class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <span class="logo-mark" aria-hidden="true">✦</span>
                            <?php bloginfo('name'); ?>
                        </a>
                    </h1>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="nav-toggle" aria-label="<?php esc_attr_e('Menu', 'ariajet-studio'); ?>" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <!-- Main Navigation -->
                <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Main Navigation', 'ariajet-studio'); ?>">
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'container' => false,
                        ));
                    } else {
                        ?>
                        <ul id="primary-menu">
                            <li>
                                <a href="<?php echo esc_url(home_url('/')); ?>">
                                    <span class="nav-icon">🏠</span>
                                    <?php _e('Home', 'ariajet-studio'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>">
                                    <span class="nav-icon">🎮</span>
                                    <?php _e('Games', 'ariajet-studio'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#about">
                                    <span class="nav-icon">✨</span>
                                    <?php _e('About', 'ariajet-studio'); ?>
                                </a>
                            </li>
                        </ul>
                        <?php
                    }
                    ?>
                </nav>
            </div>
        </div>
    </header>
