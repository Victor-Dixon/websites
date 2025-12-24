<?php
/**
 * Header Template
 * 
 * @package HoustonSipQueen
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="site-header" class="site-header">
    <div class="header-container">
        <div class="header-content">
            <!-- Logo -->
            <div class="site-logo">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
                        <span class="logo-text">Houston Sip Queen</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Navigation -->
            <nav id="site-navigation" class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id' => 'primary-menu',
                    'container' => false,
                    'fallback_cb' => 'houstonsipqueen_default_menu',
                ));
                ?>
            </nav>

            <!-- CTA Button -->
            <div class="header-cta">
                <a href="<?php echo esc_url(home_url('/quote')); ?>" class="btn-request-quote">
                    Request a Quote
                </a>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<?php
/**
 * Default menu fallback if no menu is set
 */
function houstonsipqueen_default_menu() {
    ?>
    <ul id="primary-menu" class="menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
        <li><a href="<?php echo esc_url(home_url('/services')); ?>">Services</a></li>
        <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
        <li><a href="<?php echo esc_url(home_url('/blog')); ?>">Blog</a></li>
        <li><a href="<?php echo esc_url(home_url('/faq')); ?>">FAQ</a></li>
        <li><a href="<?php echo esc_url(home_url('/portfolio')); ?>">Portfolio</a></li>
        <li><a href="<?php echo esc_url(home_url('/testimonials')); ?>">Testimonials</a></li>
        <li><a href="<?php echo esc_url(home_url('/quote')); ?>">Request a Quote</a></li>
    </ul>
    <?php
}
?>

