<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset='<?php bloginfo('charset'); ?>'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <div class='header-container'>
            <div class='site-title'>
                <a href="<?php echo home_url(); ?>">
                    <span style="font-weight: 300;">we</span><span style="font-weight: 700;">areswarm</span>
                </a>
            </div>
            <nav class='nav-menu'>
                <a href="<?php echo home_url(); ?>">Home</a>
                <a href="<?php echo home_url('/agents'); ?>">Agents</a>
                <a href="<?php echo home_url('/missions'); ?>">Missions</a>
                <a href="<?php echo home_url('/about'); ?>">About</a>
            </nav>
        </div>
    </header>
