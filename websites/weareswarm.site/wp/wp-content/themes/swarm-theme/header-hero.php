<?php
/**
 * Header template with hero-specific styles and scripts
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="We Are Swarm - Revolutionary Multi-Agent Coordination System">
    <meta name="theme-color" content="#00ffff">

    <!-- Preload critical resources -->
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/style.css" as="style">
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/js/hero-animations.js" as="script">

    <!-- Futuristic Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">

    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" type="text/css" media="all">

    <!-- WordPress head -->
    <?php wp_head(); ?>

    <!-- Critical CSS for hero section -->
    <style>
        /* Critical rendering path CSS */
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            font-family: 'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #0a0a0a;
            color: #ffffff;
            overflow-x: hidden;
        }

        /* Loading state */
        .hero-loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-content {
            text-align: center;
            color: white;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top: 4px solid #00ffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 2rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 1rem;
        }

        .loading-subtitle {
            font-size: 1rem;
            opacity: 0.8;
        }
    </style>
</head>
<body <?php body_class(); ?>>
    <!-- Loading Screen -->
    <div id="hero-loading" class="hero-loading">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Initializing Swarm Intelligence</div>
            <div class="loading-subtitle">Loading advanced coordination protocols...</div>
        </div>
    </div>

    <!-- Skip links for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    <a href="#hero" class="skip-link">Skip to hero section</a>