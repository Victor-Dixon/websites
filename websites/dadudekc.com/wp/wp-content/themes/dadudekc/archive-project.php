<?php
/**
 * Template Name: Projects Archive
 * Template for displaying all projects at /projects/
 */

get_header(); ?>

<main class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <div class="container mx-auto px-4 py-16">
        <div class="max-w-6xl mx-auto">
            <!-- Page Header -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Projects</h1>
                <p class="text-xl text-gray-300">Portfolio of automation systems and development projects</p>
            </div>

            <!-- Projects Grid -->
            <?php if (have_posts()) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php while (have_posts()) : the_post(); 
                        $one_line_summary = get_post_meta(get_the_ID(), '_project_one_line_summary', true);
                        $tech_stack = get_post_meta(get_the_ID(), '_project_tech_stack', true);
                        $project_status = get_post_meta(get_the_ID(), '_project_status', true);
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
                        <article class="bg-white/5 backdrop-blur-sm rounded-lg border border-white/10 p-6 hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="mb-4 rounded-lg overflow-hidden">
                                    <?php the_post_thumbnail('medium', array('class' => 'w-full h-48 object-cover')); ?>
                                </div>
                            <?php endif; ?>
                            
                            <h2 class="text-2xl font-bold text-white mb-2">
                                <a href="<?php the_permalink(); ?>" class="hover:text-blue-400 transition-colors">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <?php if ($one_line_summary) : ?>
                                <p class="text-gray-300 mb-4"><?php echo esc_html($one_line_summary); ?></p>
                            <?php elseif (has_excerpt()) : ?>
                                <p class="text-gray-300 mb-4"><?php the_excerpt(); ?></p>
                            <?php endif; ?>

                            <?php if ($tech_stack) : ?>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-400 mb-2">Tech Stack:</p>
                                    <p class="text-sm text-gray-300"><?php echo esc_html($tech_stack); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if ($project_status) : ?>
                                <div class="mb-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border <?php echo esc_attr($status_colors[$project_status] ?? $status_colors['active']); ?>">
                                        <?php echo esc_html($status_labels[$project_status] ?? 'Active'); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <a href="<?php the_permalink(); ?>" class="inline-block bg-gradient-to-r from-blue-500 to-green-500 text-white px-4 py-2 rounded font-semibold hover:from-blue-600 hover:to-green-600 transition-all">
                                View Project →
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('← Previous', 'dadudekc'),
                        'next_text' => __('Next →', 'dadudekc'),
                    ));
                    ?>
                </div>
            <?php else : ?>
                <div class="text-center py-16">
                    <p class="text-xl text-gray-300 mb-8">No projects found.</p>
                    <a href="<?php echo esc_url(home_url()); ?>" class="inline-block bg-blue-500 text-white px-6 py-3 rounded font-semibold hover:bg-blue-600 transition-colors">
                        Return Home
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
