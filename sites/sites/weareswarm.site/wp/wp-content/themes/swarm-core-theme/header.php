<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="modern-header">
        <div class="header-container">
            <div class="header-content">
                <!-- Logo/Brand -->
                <div class="site-branding">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <div class="brand-wrapper">
                            <div class="brand-logo">
                                <span class="logo-emoji">🐝</span>
                            </div>
                            <div class="brand-text">
                                <h1 class="site-title">
                                    <a href="<?php echo esc_url(home_url('/')); ?>">
                                        <span class="brand-main">We Are</span>
                                        <span class="brand-accent">Swarm</span>
                                    </a>
                                </h1>
                                <span class="brand-tagline">Multi-Agent Intelligence System</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation -->
                <nav class="main-navigation">
                    <div class="nav-toggle" id="nav-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>

                    <div class="nav-menu-wrapper" id="nav-menu-wrapper">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_class' => 'nav-menu',
                            'fallback_cb' => 'default_nav_menu'
                        ));

                        // Fallback menu if no menu is set
                        function default_nav_menu() {
                            echo '<ul class="nav-menu">';
                            echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/agents')) . '">Agents</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/missions')) . '">Missions</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/about')) . '">About</a></li>';
                            echo '</ul>';
                        }
                        ?>

                        <!-- CTA Button in Mobile Menu -->
                        <div class="nav-cta-mobile">
                            <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary">
                                Get Started
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Desktop CTA -->
                <div class="header-cta">
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary">
                        <span class="btn-icon">⚡</span>
                        <span>Join Swarm</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>
    </header>