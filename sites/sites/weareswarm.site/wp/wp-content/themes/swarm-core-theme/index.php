<?php get_header(); ?>

<main class="swarm-content">
    <section class="swarm-hero">
        <h1><?php _e('Welcome to Swarm Intelligence', 'swarm-core-theme'); ?></h1>
        <p><?php _e('Collaborative AI agents working together to achieve extraordinary results', 'swarm-core-theme'); ?></p>
    </section>

    <?php if (have_posts()) : ?>
        <section class="swarm-posts">
            <h2><?php _e('Latest Updates', 'swarm-core-theme'); ?></h2>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="post-card">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="post-meta">
                            <span class="date"><?php echo get_the_date(); ?></span>
                            <span class="author"><?php _e('by', 'swarm-core-theme'); ?> <?php the_author(); ?></span>
                        </div>
                        <div class="post-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="swarm-agents">
        <h2><?php _e('Our Swarm Agents', 'swarm-core-theme'); ?></h2>
        <?php echo do_shortcode('[swarm_agents limit="6"]'); ?>
    </section>
</main>

<?php get_footer(); ?>