<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
  <div class="header-container container">
    <!-- Logo -->
    <?php if (has_custom_logo()) : ?>
      <div class="site-logo">
        <?php the_custom_logo(); ?>
      </div>
    <?php else : ?>
      <div class="site-logo">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
          <span class="logo-text"><?php bloginfo('name'); ?></span>
        </a>
      </div>
    <?php endif; ?>
    
    <nav class="main-navigation" aria-label="<?php esc_attr_e('Primary Menu', 'freerideinvestor-modern'); ?>">
      <!-- Mobile Menu Toggle Button -->
      <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="<?php esc_attr_e('Toggle navigation menu', 'freerideinvestor-modern'); ?>" aria-expanded="false">
        <span>☰ Menu</span>
      </button>
      
      <?php 
        $has_menu = has_nav_menu('primary');
        if ($has_menu) {
          wp_nav_menu([
            'theme_location' => 'primary',
            'container' => '',
            'menu_class' => 'nav-menu',
            'menu_id' => 'primary-menu'
          ]); 
        } else {
          echo '<ul class="nav-menu"><li><a href="' . esc_url(home_url('/')) . '">Home</a></li></ul>';
        }
      ?>
    </nav>
  </div>
</header>
