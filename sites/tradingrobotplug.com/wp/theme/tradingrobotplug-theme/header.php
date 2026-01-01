<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
    <style>
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --dark: #343a40;
            --light: #f8f9fa;
            --white: #ffffff;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .site-header {
            background: var(--white);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .site-branding a {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }
        .main-navigation ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }
        .main-navigation a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: color 0.2s;
        }
        .main-navigation a:hover {
            color: var(--primary);
        }
        .header-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-outline {
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
        }
        .btn-primary {
            background: var(--primary);
            color: var(--white);
            border: 1px solid var(--primary);
        }
        .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
        }
    </style>
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
