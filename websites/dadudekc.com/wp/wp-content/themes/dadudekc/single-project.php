<?php
/**
 * Template Name: Single Project
 * Template for displaying individual project pages
 */

get_header(); ?>

<main class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <?php while (have_posts()) : the_post(); 
                $one_line_summary = get_post_meta(get_the_ID(), '_project_one_line_summary', true);
                $core_purpose = get_post_meta(get_the_ID(), '_project_core_purpose', true);
                $value_impact = get_post_meta(get_the_ID(), '_project_value_impact', true);
                $tech_stack = get_post_meta(get_the_ID(), '_project_tech_stack', true);
                $project_status = get_post_meta(get_the_ID(), '_project_status', true);
                $unique_angle = get_post_meta(get_the_ID(), '_project_unique_angle', true);
                $next_steps = get_post_meta(get_the_ID(), '_project_next_steps', true);
                $repo_link = get_post_meta(get_the_ID(), '_project_repo_link', true);
                
                $status_labels = array(
                    'mvp' => 'MVP',
                    'active' => 'Active',
                    'archived' => 'Archived'
                );
                $status_colors = array(
                    'mvp' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                    'active' => 'bg-green-500/20 text-green-300 border-green-500/30',
                    'archived' => 'bg-gray-500/20 text-gray-300 border-gray-500/30'
                );
            ?>
                <!-- Back Link -->
                <div class="mb-8">
                    <a href="<?php echo esc_url(get_post_type_archive_link('project')); ?>" class="text-blue-400 hover:text-blue-300 transition-colors">
                        ← Back to Projects
                    </a>
                </div>

                <!-- Project Header -->
                <header class="mb-12">
                    <div class="flex items-start justify-between mb-6">
                        <h1 class="text-4xl md:text-5xl font-bold text-white"><?php the_title(); ?></h1>
                        <?php if ($project_status) : ?>
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold border <?php echo esc_attr($status_colors[$project_status] ?? $status_colors['active']); ?>">
                                <?php echo esc_html($status_labels[$project_status] ?? 'Active'); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($one_line_summary) : ?>
                        <p class="text-xl text-gray-300 mb-6"><?php echo esc_html($one_line_summary); ?></p>
                    <?php endif; ?>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="rounded-lg overflow-hidden mb-6">
                            <?php the_post_thumbnail('large', array('class' => 'w-full h-auto')); ?>
                        </div>
                    <?php endif; ?>
                </header>

                <!-- Project Content -->
                <div class="bg-white/5 backdrop-blur-sm rounded-lg border border-white/10 p-8 mb-8">
                    <?php if ($core_purpose) : ?>
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-white mb-4">Overview</h2>
                            <div class="prose prose-invert max-w-none">
                                <h3 class="text-xl font-semibold text-white mb-2">Core Purpose</h3>
                                <p class="text-gray-300 mb-4"><?php echo nl2br(esc_html($core_purpose)); ?></p>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php if ($value_impact) : ?>
                        <section class="mb-8">
                            <h3 class="text-xl font-semibold text-white mb-2">Value / Impact</h3>
                            <p class="text-gray-300"><?php echo nl2br(esc_html($value_impact)); ?></p>
                        </section>
                    <?php endif; ?>

                    <?php if ($tech_stack) : ?>
                        <section class="mb-8">
                            <h3 class="text-xl font-semibold text-white mb-2">Tech Stack</h3>
                            <p class="text-gray-300"><?php echo esc_html($tech_stack); ?></p>
                        </section>
                    <?php endif; ?>

                    <?php if ($unique_angle) : ?>
                        <section class="mb-8">
                            <h3 class="text-xl font-semibold text-white mb-2">What Makes It Interesting</h3>
                            <p class="text-gray-300"><?php echo nl2br(esc_html($unique_angle)); ?></p>
                        </section>
                    <?php endif; ?>

                    <?php if ($next_steps) : ?>
                        <section class="mb-8">
                            <h3 class="text-xl font-semibold text-white mb-2">Next Steps</h3>
                            <p class="text-gray-300"><?php echo nl2br(esc_html($next_steps)); ?></p>
                        </section>
                    <?php endif; ?>

                    <?php if ($repo_link) : ?>
                        <section class="mb-8">
                            <h3 class="text-xl font-semibold text-white mb-2">Links</h3>
                            <a href="<?php echo esc_url($repo_link); ?>" target="_blank" rel="noopener noreferrer" class="inline-block bg-blue-500 text-white px-6 py-3 rounded font-semibold hover:bg-blue-600 transition-colors">
                                View Repository →
                            </a>
                        </section>
                    <?php endif; ?>

                    <!-- Main Content -->
                    <?php if (get_the_content()) : ?>
                        <section class="mt-8 pt-8 border-t border-white/10">
                            <div class="prose prose-invert max-w-none text-gray-300">
                                <?php the_content(); ?>
                            </div>
                        </section>
                    <?php endif; ?>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between items-center">
                    <a href="<?php echo esc_url(get_post_type_archive_link('project')); ?>" class="text-blue-400 hover:text-blue-300 transition-colors">
                        ← All Projects
                    </a>
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    if ($prev_post || $next_post) :
                    ?>
                        <div class="flex gap-4">
                            <?php if ($prev_post) : ?>
                                <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="text-blue-400 hover:text-blue-300 transition-colors">
                                    ← Previous
                                </a>
                            <?php endif; ?>
                            <?php if ($next_post) : ?>
                                <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="text-blue-400 hover:text-blue-300 transition-colors">
                                    Next →
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
