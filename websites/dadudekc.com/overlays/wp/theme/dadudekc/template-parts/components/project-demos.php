<?php
/**
 * Project Demos Component
 * SSOT: Project demos (what shipped + proof)
 * Dynamically pulls from 'project' Custom Post Type
 *
 * @package DaDudeKC
 */

$projects = get_posts([
    'post_type' => 'project',
    'posts_per_page' => 4,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
        [
            'key' => 'project_status',
            'value' => 'shipped',
            'compare' => '=',
        ],
    ],
]);

if ($projects) : ?>
    <section class="project-demos-section">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e('Project Demos', 'dadudekc'); ?></h2>
            <p class="section-subtitle"><?php esc_html_e('What shipped and proof it works.', 'dadudekc'); ?></p>
            
            <div class="project-demos-grid">
                <?php foreach ($projects as $project) :
                    $project_url = get_post_meta($project->ID, 'project_url', true);
                    $project_github = get_post_meta($project->ID, 'project_github', true);
                    $project_skills = get_post_meta($project->ID, 'project_skills', true);
                    $project_proof = get_post_meta($project->ID, 'project_proof', true);
                    $thumbnail = get_the_post_thumbnail($project->ID, 'medium');
                ?>
                    <div class="project-demo-card">
                        <?php if ($thumbnail) : ?>
                            <div class="project-demo-thumbnail">
                                <?php echo $thumbnail; ?>
                            </div>
                        <?php endif; ?>
                        <h3 class="project-demo-title"><?php echo esc_html($project->post_title); ?></h3>
                        <div class="project-demo-description">
                            <?php echo wp_kses_post($project->post_excerpt ?: wp_trim_words($project->post_content, 20)); ?>
                        </div>
                        <?php if ($project_skills) : ?>
                            <div class="project-demo-skills">
                                <strong><?php esc_html_e('Skills:', 'dadudekc'); ?></strong>
                                <?php echo esc_html($project_skills); ?>
                            </div>
                        <?php endif; ?>
                        <div class="project-demo-links">
                            <?php if ($project_url) : ?>
                                <a href="<?php echo esc_url($project_url); ?>" class="project-link" target="_blank" rel="noopener">
                                    <?php esc_html_e('View Demo →', 'dadudekc'); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($project_github) : ?>
                                <a href="<?php echo esc_url($project_github); ?>" class="project-link" target="_blank" rel="noopener">
                                    <?php esc_html_e('GitHub →', 'dadudekc'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if ($project_proof) : ?>
                            <div class="project-demo-proof">
                                <strong><?php esc_html_e('Proof:', 'dadudekc'); ?></strong>
                                <?php echo esc_html($project_proof); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

