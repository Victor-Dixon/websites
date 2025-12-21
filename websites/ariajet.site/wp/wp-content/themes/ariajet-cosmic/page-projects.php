<?php
/**
 * Template Name: Projects Page
 * 
 * Custom page template for displaying Aria's projects
 * 
 * @package AriaJet_Cosmic
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="container">
        
        <!-- Page Header -->
        <section class="cosmic-hero page-hero">
            <div class="hero-content">
                <h1 class="hero-title">
                    <span class="hero-emoji">💻</span>
                    <?php _e('Projects', 'ariajet-cosmic'); ?>
                </h1>
                <p class="hero-subtitle">
                    <?php _e('Explore Aria\'s creative projects, games, and web development work.', 'ariajet-cosmic'); ?>
                </p>
            </div>
        </section>

        <!-- Projects Grid -->
        <section class="projects-section">
            <div class="projects-grid">
                
                <article class="project-card cosmic-card">
                    <div class="project-icon">🎮</div>
                    <h3 class="project-title"><?php _e('Game Development', 'ariajet-cosmic'); ?></h3>
                    <p class="project-description">
                        <?php _e('Creating fun and interactive games like Aria\'s Wild World and Wildlife Adventure.', 'ariajet-cosmic'); ?>
                    </p>
                    <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="cosmic-button accent">
                        <?php _e('View Games', 'ariajet-cosmic'); ?>
                    </a>
                </article>

                <article class="project-card cosmic-card">
                    <div class="project-icon">🎨</div>
                    <h3 class="project-title"><?php _e('Creative Design', 'ariajet-cosmic'); ?></h3>
                    <p class="project-description">
                        <?php _e('Designing beautiful websites, graphics, and digital artwork.', 'ariajet-cosmic'); ?>
                    </p>
                </article>

                <article class="project-card cosmic-card">
                    <div class="project-icon">🎵</div>
                    <h3 class="project-title"><?php _e('Music Projects', 'ariajet-cosmic'); ?></h3>
                    <p class="project-description">
                        <?php _e('Curating playlists, creating mixes, and exploring new sounds.', 'ariajet-cosmic'); ?>
                    </p>
                    <a href="<?php echo esc_url(home_url('/playlists/')); ?>" class="cosmic-button accent">
                        <?php _e('View Playlists', 'ariajet-cosmic'); ?>
                    </a>
                </article>

                <article class="project-card cosmic-card">
                    <div class="project-icon">💻</div>
                    <h3 class="project-title"><?php _e('Web Development', 'ariajet-cosmic'); ?></h3>
                    <p class="project-description">
                        <?php _e('Building websites and web applications with modern technologies.', 'ariajet-cosmic'); ?>
                    </p>
                </article>

            </div>

            <!-- Coming Soon Section -->
            <div class="coming-soon cosmic-card">
                <div class="coming-soon-icon">🌌</div>
                <h2><?php _e('More Projects Coming Soon', 'ariajet-cosmic'); ?></h2>
                <p>
                    <?php _e('Aria is always working on new and exciting projects. Check back soon for updates!', 'ariajet-cosmic'); ?>
                </p>
            </div>

        </section>

        <?php
        // Display page content if any
        while (have_posts()) :
            the_post();
            if (get_the_content()) :
                ?>
                <section class="page-content">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </section>
                <?php
            endif;
        endwhile;
        ?>

    </div>
</main>

<?php
get_footer();

