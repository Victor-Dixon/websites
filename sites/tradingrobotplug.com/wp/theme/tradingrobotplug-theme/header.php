<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
	<div class="container header-inner">
		<div class="site-branding">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">TradingRobotPlug</a>
		</div>

		<nav id="site-navigation" class="main-navigation">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
                'container'      => false,
			) );
			?>
		</nav>
        
        <div class="header-actions">
            <?php if ( is_user_logged_in() ) : ?>
                <a href="/dashboard" class="btn btn-primary">Dashboard</a>
            <?php else : ?>
                <a href="<?php echo wp_login_url(); ?>" class="btn btn-outline">Log In</a>
                <a href="/pricing" class="btn btn-primary">Start Free</a>
            <?php endif; ?>
        </div>
	</div>
</header>
<div id="content" class="site-content">
