<?php
/**
 * Template Name: Portfolio
 *
 * @package DaDudeKC
 */

get_header();

$projects_query = new WP_Query([
    'post_type' => 'project',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'project_status',
            'value' => 'shipped',
            'compare' => '='
        ]
    ]
]);

$projects = $projects_query->posts;
$categories = [];

// Group projects by categories based on skills/tags
if ($projects) {
    foreach ($projects as $project) {
        $skills = get_post_meta($project->ID, 'project_skills', true);
        if ($skills) {
            $skill_array = array_map('trim', explode(',', $skills));
            foreach ($skill_array as $skill) {
                if (!isset($categories[$skill])) {
                    $categories[$skill] = [];
                }
                $categories[$skill][] = $project;
            }
        } else {
            // Default category for projects without skills
            if (!isset($categories['Other'])) {
                $categories['Other'] = [];
            }
            $categories['Other'][] = $project;
        }
    }
}
?>

<main class="content-area">
    <header class="portfolio-header" style="text-align: center; margin-bottom: 4rem; padding: 4rem 0; background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); border-radius: 0 0 24px 24px;">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent), var(--text-primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php esc_html_e('Portfolio', 'dadudekc'); ?></h1>
            <p style="font-size: 1.3rem; color: var(--text-secondary); margin-bottom: 2rem;"><?php esc_html_e('Shipped systems, solved problems, delivered results.', 'dadudekc'); ?></p>
            <div class="portfolio-stats" style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--accent);"><?php echo count($projects); ?></div>
                    <div style="color: var(--text-secondary);"><?php esc_html_e('Projects Shipped', 'dadudekc'); ?></div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--accent);"><?php echo count($categories); ?></div>
                    <div style="color: var(--text-secondary);"><?php esc_html_e('Technologies', 'dadudekc'); ?></div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: var(--accent);">100%</div>
                    <div style="color: var(--text-secondary);"><?php esc_html_e('Client Satisfaction', 'dadudekc'); ?></div>
                </div>
            </div>
        </div>
    </header>

    <?php if ($projects) : ?>
        <!-- Category Filter -->
        <section class="portfolio-filter" style="padding: 2rem 0; border-bottom: 1px solid var(--border); margin-bottom: 3rem;">
            <div class="container">
                <div class="filter-buttons" style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                    <button class="filter-btn active" data-filter="all" style="background: var(--accent); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;"><?php esc_html_e('All Projects', 'dadudekc'); ?></button>
                    <?php foreach (array_keys($categories) as $category) : ?>
                        <button class="filter-btn" data-filter="<?php echo esc_attr($category); ?>" style="background: var(--surface); color: var(--text-primary); border: 2px solid var(--border); padding: 0.75rem 1.5rem; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;"><?php echo esc_html($category); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Projects Grid -->
        <section class="portfolio-projects">
            <div class="container">
                <div class="projects-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
                    <?php foreach ($categories as $category_name => $category_projects) : ?>
                        <?php foreach ($category_projects as $project) :
                            $project_url = get_post_meta($project->ID, 'project_url', true);
                            $project_github = get_post_meta($project->ID, 'project_github', true);
                            $project_skills = get_post_meta($project->ID, 'project_skills', true);
                            $project_problem = get_post_meta($project->ID, 'project_problem', true);
                            $project_approach = get_post_meta($project->ID, 'project_approach', true);
                            $project_outcome = get_post_meta($project->ID, 'project_outcome', true);
                            $project_proof = get_post_meta($project->ID, 'project_proof', true);
                            $project_stack = get_post_meta($project->ID, 'project_stack', true);
                            $thumbnail = get_the_post_thumbnail($project->ID, 'large');
                        ?>
                            <article class="project-card <?php echo esc_attr($category_name); ?>" style="background: var(--surface); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 16px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; border: 1px solid var(--border);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'">
                                <?php if ($thumbnail) : ?>
                                    <div class="project-thumbnail" style="width: 100%; height: 200px; overflow: hidden;">
                                        <?php echo $thumbnail; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="project-content" style="padding: 2rem;">
                                    <div class="project-category" style="background: var(--accent); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-bottom: 1rem;">
                                        <?php echo esc_html($category_name); ?>
                                    </div>

                                    <h3 class="project-title" style="margin-top: 0; margin-bottom: 1rem; font-size: 1.5rem; color: var(--text-primary);">
                                        <a href="<?php the_permalink($project->ID); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html($project->post_title); ?></a>
                                    </h3>

                                    <div class="project-meta" style="margin-bottom: 1.5rem;">
                                        <?php if ($project_stack) : ?>
                                            <div style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                                <strong><?php esc_html_e('Stack:', 'dadudekc'); ?></strong> <?php echo esc_html($project_stack); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($project_skills) : ?>
                                            <div class="project-skills" style="margin-bottom: 1rem;">
                                                <?php
                                                $skills_array = array_map('trim', explode(',', $project_skills));
                                                foreach ($skills_array as $skill) :
                                                ?>
                                                    <span style="background: var(--border); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; color: var(--text-secondary); margin-right: 0.5rem; margin-bottom: 0.5rem; display: inline-block;"><?php echo esc_html($skill); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="project-summary">
                                        <?php if ($project_problem) : ?>
                                            <div class="project-problem" style="margin-bottom: 1rem;">
                                                <strong style="color: var(--accent);"><?php esc_html_e('Problem:', 'dadudekc'); ?></strong>
                                                <p style="margin: 0.5rem 0; color: var(--text-secondary);"><?php echo esc_html($project_problem); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($project_approach) : ?>
                                            <div class="project-approach" style="margin-bottom: 1rem;">
                                                <strong style="color: var(--accent);"><?php esc_html_e('Approach:', 'dadudekc'); ?></strong>
                                                <p style="margin: 0.5rem 0; color: var(--text-secondary);"><?php echo esc_html($project_approach); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($project_outcome) : ?>
                                            <div class="project-outcome" style="margin-bottom: 1rem;">
                                                <strong style="color: var(--accent);"><?php esc_html_e('Outcome:', 'dadudekc'); ?></strong>
                                                <p style="margin: 0.5rem 0; color: var(--text-secondary);"><?php echo esc_html($project_outcome); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($project_proof) : ?>
                                        <div class="project-proof" style="background: rgba(0, 212, 255, 0.1); border-left: 4px solid var(--accent); padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px;">
                                            <strong style="color: var(--accent); font-size: 0.9rem;"><?php esc_html_e('PROOF:', 'dadudekc'); ?></strong>
                                            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: var(--text-secondary);"><?php echo esc_html($project_proof); ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <div class="project-links" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                        <?php if ($project_url) : ?>
                                            <a href="<?php echo esc_url($project_url); ?>" target="_blank" rel="noopener" style="background: var(--accent); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                <span>🚀</span>
                                                <?php esc_html_e('View Live', 'dadudekc'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($project_github) : ?>
                                            <a href="<?php echo esc_url($project_github); ?>" target="_blank" rel="noopener" style="background: var(--surface); color: var(--text-primary); border: 2px solid var(--border); padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                <span>💻</span>
                                                <?php esc_html_e('GitHub', 'dadudekc'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php the_permalink($project->ID); ?>" style="background: var(--surface); color: var(--text-primary); border: 2px solid var(--border); padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
                                            <?php esc_html_e('Details →', 'dadudekc'); ?>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="portfolio-cta" style="padding: 4rem 0; background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); margin-top: 4rem;">
            <div class="container" style="text-align: center;">
                <h2 style="color: var(--accent); margin-bottom: 1rem;"><?php esc_html_e('Ready to Build Something Together?', 'dadudekc'); ?></h2>
                <p style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <?php esc_html_e('Let\'s discuss your project and explore how we can turn your vision into reality with proven systems and reliable delivery.', 'dadudekc'); ?>
                </p>
                <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                    <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>" style="background: var(--accent); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                        <?php esc_html_e('Start a Conversation →', 'dadudekc'); ?>
                    </a>
                    <a href="https://calendly.com/dadudekc/consultation" target="_blank" rel="noopener" style="background: var(--surface); color: var(--text-primary); border: 2px solid var(--border); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                        <?php esc_html_e('Schedule a Call →', 'dadudekc'); ?>
                    </a>
                </div>
            </div>
        </section>

    <?php else : ?>
        <!-- Empty State -->
        <section class="portfolio-empty" style="text-align: center; padding: 6rem 0;">
            <div class="container">
                <div style="font-size: 4rem; margin-bottom: 2rem;">🚧</div>
                <h2 style="color: var(--accent); margin-bottom: 1rem;"><?php esc_html_e('Portfolio Under Construction', 'dadudekc'); ?></h2>
                <p style="font-size: 1.2rem; color: var(--text-secondary); margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <?php esc_html_e('I\'m currently working on several exciting projects. Portfolio entries will be added as systems are completed and shipped.', 'dadudekc'); ?>
                </p>
                <div style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border); display: inline-block;">
                    <h3 style="margin-top: 0; color: var(--accent);"><?php esc_html_e('Current Focus Areas', 'dadudekc'); ?></h3>
                    <ul style="text-align: left; margin: 1rem 0; padding-left: 1.5rem;">
                        <li><?php esc_html_e('Multi-agent AI systems and swarm coordination', 'dadudekc'); ?></li>
                        <li><?php esc_html_e('Automated deployment pipelines and DevOps', 'dadudekc'); ?></li>
                        <li><?php esc_html_e('Business intelligence dashboards and analytics', 'dadudekc'); ?></li>
                        <li><?php esc_html_e('WordPress development and performance optimization', 'dadudekc'); ?></li>
                        <li><?php esc_html_e('Workflow automation and system integration', 'dadudekc'); ?></li>
                    </ul>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            filterButtons.forEach(btn => {
                btn.style.background = 'var(--surface)';
                btn.style.color = 'var(--text-primary)';
                btn.style.border = '2px solid var(--border)';
            });

            // Add active class to clicked button
            this.classList.add('active');
            this.style.background = 'var(--accent)';
            this.style.color = 'white';
            this.style.border = 'none';

            const filterValue = this.getAttribute('data-filter');

            projectCards.forEach(card => {
                if (filterValue === 'all' || card.classList.contains(filterValue)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php
get_footer();