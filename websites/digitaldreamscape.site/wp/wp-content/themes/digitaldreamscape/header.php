<?php

/**
 * Header Template - Unified Brand Header
 * 
 * Features:
 * - [BUILD IN PUBLIC] identity tagline
 * - Watch Live + Read Episodes CTAs
 * - Consistent across ALL pages
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 * @cache-bust 2025-12-24-v2
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header id="site-header" class="site-header">
        <div class="header-container">
            <div class="header-content">
                <!-- Logo with BUILD IN PUBLIC identity -->
                <div class="site-logo">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
                            <span class="logo-text">Digital Dreamscape</span>
                            <span class="logo-tagline">[BUILD IN PUBLIC]</span>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Navigation -->
                <nav id="site-navigation" class="main-navigation" aria-label="Primary navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id' => 'primary-menu',
                        'container' => false,
                        'fallback_cb' => 'digitaldreamscape_default_menu',
                    ));
                    ?>
                </nav>

                <!-- CTA Buttons - Consistent across all pages -->
                <div class="nav-cta-group">
                    <a href="https://twitch.tv/digitaldreamscape" class="nav-cta nav-cta-primary" target="_blank" rel="noopener">
                        Watch Live
                    </a>
                    <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="nav-cta nav-cta-secondary">
                        Read Episodes
                    </a>
                </div>
            </div>
        </div>
    </header>