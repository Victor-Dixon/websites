<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <style id="force-purple-background">
        html {
            background: #bf00ff !important;
            height: 100% !important;
        }

        body {
            background: #bf00ff !important;
            background: linear-gradient(135deg, #bf00ff 0%, #9d00ff 50%, #7d00ff 100%) !important;
            background-attachment: fixed !important;
            min-height: 100vh !important;
        }

        /* Only exclude specific pages */
        body.page-template-page-carmyn,
        body.page-template-page-guestbook,
        body.page-template-page-invitation,
        body.page-template-page-birthday-fun {
            background: inherit !important;
        }
    </style>

    <script>
        // Force purple background via JavaScript as backup
        (function() {
            var bodyClasses = document.body.className.split(' ');
            var excludePages = ['page-template-page-carmyn', 'page-template-page-guestbook', 'page-template-page-invitation', 'page-template-page-birthday-fun'];
            var shouldExclude = excludePages.some(function(cls) {
                return bodyClasses.indexOf(cls) !== -1;
            });

            if (!shouldExclude) {
                document.documentElement.style.setProperty('background', '#bf00ff', 'important');
                document.body.style.setProperty('background', 'linear-gradient(135deg, #bf00ff 0%, #9d00ff 50%, #7d00ff 100%)', 'important');
                document.body.style.setProperty('background-attachment', 'fixed', 'important');
                document.body.style.setProperty('min-height', '100vh', 'important');
            }
        })();
    </script>

    <header>
        <div class="header-content">
            <div class="logo-container">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <h1 class="site-title"><a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a></h1>
                <?php endif; ?>
            </div>
            <nav>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'nav-menu',
                    'container' => false,
                    'fallback_cb' => function () {
                        echo '<ul class="nav-menu">
                        <li><a href="' . home_url() . '">Home</a></li>
                        <li><a href="' . home_url('/carmyn') . '">Carmyn</a></li>
                        <li><a href="' . home_url('/guestbook') . '">Guestbook</a></li>
                        <li><a href="' . home_url('/invitation') . '">Invitation</a></li>
                        <li><a href="' . home_url('/birthday-fun') . '">Birthday Fun</a></li>
                    </ul>';
                    }
                ));
                ?>
            </nav>
        </div>
    </header>

    <main>