<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-gray-50'); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site min-h-screen flex flex-col">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'prism-blossom-theme'); ?></a>

    <header id="masthead" class="site-header bg-white shadow-lg border-b-4 border-gradient-to-r from-purple-500 to-blue-500">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div class="site-branding flex items-center space-x-4">
                    <?php
                    the_custom_logo();
                    if (is_front_page() && is_home()) :
                        ?>
                        <h1 class="site-title text-3xl font-bold">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                    else :
                        ?>
                        <p class="site-title text-3xl font-bold">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
                                <?php bloginfo('name'); ?>
                            </a>
                        </p>
                        <?php
                    endif;
                    $prism_blossom_theme_description = get_bloginfo('description', 'display');
                    if ($prism_blossom_theme_description || is_customize_preview()) :
                        ?>
                        <p class="site-description text-gray-600 text-lg"><?php echo $prism_blossom_theme_description; ?></p>
                    <?php endif; ?>
                </div><!-- .site-branding -->

                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'menu-1',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'flex space-x-8 text-gray-700 font-semibold',
                        'container'      => false,
                        'link_before'    => '',
                        'link_after'     => '',
                    ));
                    ?>
                </nav><!-- #site-navigation -->
            </div>
        </div>
    </header><!-- #masthead -->