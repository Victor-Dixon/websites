<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-container">
        <div class="site-logo">
            <div class="logo-icon">
                <span>🐝</span>
            </div>
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <h1 class="site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <?php bloginfo('name'); ?>
                    </a>
                </h1>
            <?php endif; ?>
        </div>
        
        <nav class="main-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container' => false,
                'fallback_cb' => function() {
                    echo '<ul>';
                    echo '<li><a href="' . home_url('/') . '">Home</a></li>';
                    echo '<li><a href="' . home_url('/agents') . '">Agents</a></li>';
                    echo '<li><a href="' . home_url('/missions') . '">Missions</a></li>';
                    echo '<li><a href="' . home_url('/about') . '">About</a></li>';
                    echo '</ul>';
                }
            ));
            ?>
        </nav>
    </div>
</header>

