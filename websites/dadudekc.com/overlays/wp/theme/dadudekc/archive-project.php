<?php
/**
 * Project archive template.
 *
 * @package DaDudeKC
 */

get_header();

$projects = new WP_Query([
    'post_type' => 'project',
    'posts_per_page' => 12,
    'post_status' => 'publish',
]);
?>
<main class="content-area">
    <header>
        <h1><?php esc_html_e('Portfolio', 'dadudekc'); ?></h1>
        <p class="post-meta"><?php esc_html_e('Problem → Approach → Outcome. Proof backed, shipped systems.', 'dadudekc'); ?></p>
    </header>

    <div class="grid">
        <?php if ($projects->have_posts()) : ?>
            <?php while ($projects->have_posts()) : $projects->the_post(); ?>
                <?php
                $problem = get_post_meta(get_the_ID(), 'project_problem', true);
                $approach = get_post_meta(get_the_ID(), 'project_approach', true);
                $outcome = get_post_meta(get_the_ID(), 'project_outcome', true);
                $stack = get_post_meta(get_the_ID(), 'project_stack', true);
                $project_url = get_post_meta(get_the_ID(), 'project_url', true);
                $project_github = get_post_meta(get_the_ID(), 'project_github', true);
                ?>
                <article class="card">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="post-meta"><?php echo esc_html($stack ? $stack : __('Stack TBD', 'dadudekc')); ?></p>
                    <?php if ($problem) : ?>
                        <p><strong><?php esc_html_e('Problem:', 'dadudekc'); ?></strong> <?php echo esc_html($problem); ?></p>
                    <?php endif; ?>
                    <?php if ($approach) : ?>
                        <p><strong><?php esc_html_e('Approach:', 'dadudekc'); ?></strong> <?php echo esc_html($approach); ?></p>
                    <?php endif; ?>
                    <?php if ($outcome) : ?>
                        <p><strong><?php esc_html_e('Outcome:', 'dadudekc'); ?></strong> <?php echo esc_html($outcome); ?></p>
                    <?php endif; ?>
                    <?php if ($project_url || $project_github) : ?>
                        <p>
                            <?php if ($project_url) : ?>
                                <a href="<?php echo esc_url($project_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Live →', 'dadudekc'); ?></a>
                            <?php endif; ?>
                            <?php if ($project_github) : ?>
                                <a href="<?php echo esc_url($project_github); ?>" target="_blank" rel="noopener"><?php esc_html_e('GitHub →', 'dadudekc'); ?></a>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="card"><?php esc_html_e('Portfolio entries are being added.', 'dadudekc'); ?></div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</main>
<?php
get_footer();
