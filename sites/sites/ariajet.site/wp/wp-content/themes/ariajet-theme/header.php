<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'ariajet-theme'); ?></a>

    <header id="masthead" class="site-header" style="background: linear-gradient(135deg, #0f0f23, #1a1a2e); border-bottom: 2px solid #ff00ff;">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="site-branding">
                    <?php
                    the_custom_logo();
                    if (is_front_page() && is_home()) :
                        ?>
                        <h1 class="site-title text-2xl font-bold text-white font-mono">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-cyan-400">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                    else :
                        ?>
                        <p class="site-title text-2xl font-bold text-white font-mono">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-cyan-400">
                                <?php bloginfo('name'); ?>
                            </a>
                        </p>
                        <?php
                    endif;
                    $ariajet_theme_description = get_bloginfo('description', 'display');
                    if ($ariajet_theme_description || is_customize_preview()) :
                        ?>
                        <p class="site-description text-cyan-400 text-sm font-mono"><?php echo $ariajet_theme_description; ?></p>
                    <?php endif; ?>
                </div><!-- .site-branding -->

                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'menu-1',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'flex space-x-6 text-white font-mono',
                        'container'      => false,
                        'link_before'    => '<span class="text-cyan-400">> </span>',
                        'link_after'     => '',
                    ));
                    ?>
                </nav><!-- #site-navigation -->
            </div>
        </div>
    </header><!-- #masthead -->