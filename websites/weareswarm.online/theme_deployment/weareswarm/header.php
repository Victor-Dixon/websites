<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset='<?php bloginfo('charset'); ?>'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <div class='container' style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem;padding:0.75rem 0;">
            <h1 style="margin:0;"><a href="<?php echo esc_url(home_url('/')); ?>" style="text-decoration:none;color:inherit;"><?php bloginfo('name'); ?></a></h1>
            <nav aria-label="Swarm command links" style="display:flex;flex-wrap:wrap;gap:1rem;font-size:0.95rem;">
                <a href="<?php echo esc_url(home_url('/focus/')); ?>">Focus</a>
                <a href="<?php echo esc_url(home_url('/projects/')); ?>">Projects</a>
                <a href="<?php echo esc_url(home_url('/tasks/')); ?>">Tasks</a>
            </nav>
        </div>
    </header>
