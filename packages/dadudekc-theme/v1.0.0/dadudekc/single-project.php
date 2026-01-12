<?php
/**
 * Single project template.
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $problem = get_post_meta(get_the_ID(), 'project_problem', true);
        $approach = get_post_meta(get_the_ID(), 'project_approach', true);
        $outcome = get_post_meta(get_the_ID(), 'project_outcome', true);
        $stack = get_post_meta(get_the_ID(), 'project_stack', true);
        $status = get_post_meta(get_the_ID(), 'project_status', true);
        $proof = get_post_meta(get_the_ID(), 'project_proof', true);
        $project_url = get_post_meta(get_the_ID(), 'project_url', true);
        $project_github = get_post_meta(get_the_ID(), 'project_github', true);
        ?>
        <article>
            <p class="post-meta"><?php echo esc_html($status ? ucfirst($status) : __('In progress', 'dadudekc')); ?></p>
            <h1><?php the_title(); ?></h1>
            <p class="post-meta"><?php echo esc_html($stack ?: __('Stack TBD', 'dadudekc')); ?></p>

            <div class="card" style="margin: 2rem 0;">
                <?php if ($problem) : ?>
                    <p><strong><?php esc_html_e('Problem', 'dadudekc'); ?>:</strong> <?php echo esc_html($problem); ?></p>
                <?php endif; ?>
                <?php if ($approach) : ?>
                    <p><strong><?php esc_html_e('Approach', 'dadudekc'); ?>:</strong> <?php echo esc_html($approach); ?></p>
                <?php endif; ?>
                <?php if ($outcome) : ?>
                    <p><strong><?php esc_html_e('Outcome', 'dadudekc'); ?>:</strong> <?php echo esc_html($outcome); ?></p>
                <?php endif; ?>
                <?php if ($proof) : ?>
                    <p><strong><?php esc_html_e('Proof', 'dadudekc'); ?>:</strong> <?php echo esc_html($proof); ?></p>
                <?php endif; ?>
            </div>

            <div class="cta-row">
                <?php if ($project_url) : ?>
                    <a class="toggle-button" href="<?php echo esc_url($project_url); ?>" target="_blank" rel="noopener">
                        <?php esc_html_e('View Live', 'dadudekc'); ?>
                    </a>
                <?php endif; ?>
                <?php if ($project_github) : ?>
                    <a class="toggle-button" href="<?php echo esc_url($project_github); ?>" target="_blank" rel="noopener">
                        <?php esc_html_e('GitHub', 'dadudekc'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="post-content" style="margin-top: 2rem;">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php
get_footer();
