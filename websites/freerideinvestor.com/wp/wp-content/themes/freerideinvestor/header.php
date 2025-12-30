<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="site-wrapper">
	<header class="site-header">
		<div class="header-container container">
			<div class="site-logo">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link">
						<?php bloginfo( 'name' ); ?>
					</a>
					<?php
				}
				?>
			</div>

			<button class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
				<span class="sr-only"><?php esc_html_e( 'Menu', 'freerideinvestor' ); ?></span>
				<span class="hamburger">☰</span>
			</button>

			<nav class="main-navigation">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'nav-menu',
						'container'      => false,
						'fallback_cb'    => 'fri_default_menu',
					)
				);
				?>
			</nav>
		</div>
	</header>

	<main class="site-main">
