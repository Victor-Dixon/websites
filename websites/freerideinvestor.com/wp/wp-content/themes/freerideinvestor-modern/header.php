<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
<style>
/* Stunning Menu Styles - Matches Homepage Design */
:root {
    --fri-primary: #0066ff;
    --fri-primary-dark: #0052cc;
    --fri-accent-green: #00c853;
    --fri-accent-gold: #ffb300;
    --fri-bg-dark: #0d1117;
    --fri-bg-darker: #010409;
    --fri-text-light: #f0f6fc;
    --fri-text-muted: #8b949e;
    --fri-border: rgba(240, 246, 252, 0.1);
}

/* Site Header - Stunning Dark Design */
.site-header {
    background: linear-gradient(135deg, var(--fri-bg-darker) 0%, var(--fri-bg-dark) 100%);
    border-bottom: 1px solid var(--fri-border);
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px var(--spacing-md);
    max-width: 1200px;
    margin: 0 auto;
}

/* Logo Styling - Matches Homepage */
.site-logo {
    font-size: 1.5rem;
    font-weight: 700;
}

.logo-link,
.site-logo a {
    color: var(--fri-text-light);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.logo-link:hover,
.site-logo a:hover {
    color: var(--fri-primary);
    transform: translateY(-1px);
}

.logo-text {
    background: linear-gradient(135deg, var(--fri-text-light) 0%, var(--fri-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    white-space: normal !important;
    letter-spacing: normal !important;
    word-spacing: normal !important;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Navigation - Stunning Styling */
.main-nav {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Mobile Menu Toggle Button */
.menu-toggle {
    display: none;
    background: rgba(240, 246, 252, 0.1);
    border: 1px solid var(--fri-border);
    color: var(--fri-text-light);
    padding: 10px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.menu-toggle:hover {
    background: rgba(240, 246, 252, 0.15);
    border-color: var(--fri-primary);
    color: var(--fri-primary);
}

/* Navigation List */
.main-nav .nav-list {
    list-style: none;
    display: flex;
    gap: 10px;
    padding: 0;
    margin: 0;
    align-items: center;
}

/* Navigation Links - Stunning Style */
.main-nav .nav-list li a {
    display: inline-block;
    padding: 12px 24px;
    color: var(--fri-text-light);
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 8px;
    background: rgba(240, 246, 252, 0.03);
    border: 1px solid transparent;
    transition: all 0.3s ease;
    position: relative;
}

.main-nav .nav-list li a:hover {
    background: rgba(240, 246, 252, 0.08);
    border-color: var(--fri-border);
    color: var(--fri-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 102, 255, 0.2);
}

.main-nav .nav-list li a:focus {
    outline: 2px solid var(--fri-primary);
    outline-offset: 2px;
}

/* Active Navigation Link */
.main-nav .nav-list li.current-menu-item > a,
.main-nav .nav-list li.current_page_item > a {
    background: linear-gradient(135deg, var(--fri-primary) 0%, var(--fri-primary-dark) 100%);
    color: white;
    border-color: var(--fri-primary);
    box-shadow: 0 4px 12px rgba(0, 102, 255, 0.4);
}

/* Responsive Menu */
@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }
    
    .main-nav .nav-list {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, var(--fri-bg-darker) 0%, var(--fri-bg-dark) 100%);
        border-top: 1px solid var(--fri-border);
        border-bottom: 1px solid var(--fri-border);
        flex-direction: column;
        padding: 20px;
        gap: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }
    
    .main-nav .nav-list.active {
        display: flex;
    }
    
    .main-nav .nav-list li {
        width: 100%;
    }
    
    .main-nav .nav-list li a {
        width: 100%;
        text-align: center;
        padding: 16px 24px;
    }
    
    .header-content {
        padding: 15px var(--spacing-md);
    }
}

/* Ensure body background matches on all pages */
body {
    background: linear-gradient(135deg, var(--fri-bg-darker) 0%, var(--fri-bg-dark) 100%);
    color: var(--fri-text-light);
    min-height: 100vh;
}
</style>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
  <div class="header-content">
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
    
    <nav class="main-nav">
      <!-- Mobile Menu Toggle Button -->
      <button class="menu-toggle" id="mobile-menu-toggle" aria-label="Toggle navigation menu">
        <span>☰ Menu</span>
      </button>
      
      <?php 
        $has_menu = has_nav_menu('primary');
        if ($has_menu) {
          wp_nav_menu([
            'theme_location' => 'primary',
            'container' => '',
            'menu_class' => 'nav-list',
            'menu_id' => 'primary-menu'
          ]); 
        } else {
          echo '<ul class="nav-list" id="primary-menu"><li><a href="' . esc_url(home_url('/')) . '">Home</a></li><li><a href="' . esc_url(home_url('/blog/')) . '">Blog</a></li><li><a href="' . esc_url(home_url('/about/')) . '">About</a></li><li><a href="' . esc_url(home_url('/contact/')) . '">Contact</a></li></ul>';
        }
      ?>
    </nav>
  </div>
</header>

<script>
// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const navList = document.getElementById('primary-menu');
    
    if (menuToggle && navList) {
        menuToggle.addEventListener('click', function() {
            navList.classList.toggle('active');
        });
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (menuToggle && navList && 
            !menuToggle.contains(event.target) && 
            !navList.contains(event.target)) {
            navList.classList.remove('active');
        }
    });
    
    // Set active menu item based on current page
    const currentUrl = window.location.pathname;
    const menuLinks = document.querySelectorAll('.nav-list a');
    menuLinks.forEach(function(link) {
        if (link.getAttribute('href') === currentUrl || 
            (currentUrl === '/' && link.getAttribute('href') === homeUrl) ||
            (currentUrl.includes(link.getAttribute('href').replace(homeUrl, '')) && link.getAttribute('href') !== homeUrl)) {
            link.closest('li').classList.add('current-menu-item');
        }
    });
});
</script>
