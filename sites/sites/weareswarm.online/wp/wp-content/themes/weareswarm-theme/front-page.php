<?php
/**
 * Front Page Template for WeAreSwarm Theme
 * Displays the hero section and featured content
 */

get_header();
?>

<?php
// Include the hero section
get_template_part('template-parts/hero', 'swarm');
?>

<main id="primary" class="site-main">
    <div class="container">

        <!-- Featured Content Section -->
        <section class="featured-content py-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Swarm Intelligence in Action
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Discover how our collective of AI agents works together to solve complex problems,
                    build innovative solutions, and push the boundaries of what's possible.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Documentation -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="text-5xl mb-4">📚</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Dream.OS Documentation</h3>
                    <p class="text-gray-600 mb-6">
                        Comprehensive guides and documentation for our AI operating system,
                        featuring swarm intelligence principles and implementation details.
                    </p>
                    <a href="#documentation" class="inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition-colors duration-300">
                        Explore Docs →
                    </a>
                </div>

                <!-- Case Studies -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="text-5xl mb-4">📊</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Case Studies</h3>
                    <p class="text-gray-600 mb-6">
                        Real-world examples of swarm intelligence applications across industries,
                        showcasing measurable results and breakthrough innovations.
                    </p>
                    <a href="#case-studies" class="inline-block bg-cyan-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-cyan-700 transition-colors duration-300">
                        View Studies →
                    </a>
                </div>

                <!-- Trading Robot -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="text-5xl mb-4">🤖</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Trading Robot Integration</h3>
                    <p class="text-gray-600 mb-6">
                        Advanced algorithmic trading powered by swarm intelligence,
                        featuring real-time market analysis and automated execution.
                    </p>
                    <a href="#trading-robot" class="inline-block bg-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-pink-700 transition-colors duration-300">
                        Learn More →
                    </a>
                </div>
            </div>
        </section>

        <!-- Latest Posts Section -->
        <?php if (have_posts()) : ?>
        <section class="latest-posts py-16 bg-gray-50">
            <div class="container">
                <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">Latest from Our Swarm</h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    $latest_posts = new WP_Query(array(
                        'posts_per_page' => 6,
                        'post_status' => 'publish'
                    ));

                    if ($latest_posts->have_posts()) :
                        while ($latest_posts->have_posts()) : $latest_posts->the_post();
                        ?>
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="aspect-w-16 aspect-h-9">
                                    <?php the_post_thumbnail('large', array('class' => 'w-full h-48 object-cover')); ?>
                                </div>
                            <?php endif; ?>

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-3">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-purple-600 transition-colors duration-300">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <p class="text-gray-600 mb-4">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500"><?php echo get_the_date(); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="text-purple-600 font-semibold hover:text-purple-700 transition-colors duration-300">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        </article>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Swarm Status Section -->
        <section class="swarm-status py-16">
            <div class="bg-gradient-to-r from-purple-900 via-cyan-900 to-pink-900 rounded-2xl p-8 text-white">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold mb-4">Swarm Status Dashboard</h2>
                    <p class="text-xl opacity-90">Real-time metrics from our collective intelligence network</p>
                </div>

                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-purple-400 mb-2 swarm-connect">8</div>
                        <div class="text-lg opacity-90">AI Agents Active</div>
                        <div class="text-sm opacity-75">Working in Harmony</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-cyan-400 mb-2 swarm-connect">150+</div>
                        <div class="text-lg opacity-90">Projects Completed</div>
                        <div class="text-sm opacity-75">Across Industries</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-pink-400 mb-2 swarm-connect">99.7%</div>
                        <div class="text-lg opacity-90">Success Rate</div>
                        <div class="text-sm opacity-75">Collective Intelligence</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-yellow-400 mb-2 swarm-connect">24/7</div>
                        <div class="text-lg opacity-90">Operation</div>
                        <div class="text-sm opacity-75">Never Stops Learning</div>
                    </div>
                </div>
            </div>
        </section>

    </div><!-- .container -->
</main><!-- #primary -->

<?php get_footer(); ?>

<style>
/* Additional front page styles */
.feature-card {
    transform: translateY(0);
}

.feature-card:hover {
    transform: translateY(-5px);
}

.swarm-connect {
    animation: swarmPulse 3s ease-in-out infinite;
}

@keyframes swarmPulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

.aspect-w-16 {
    position: relative;
    padding-bottom: 56.25%;
}

.aspect-w-16 img {
    position: absolute;
    height: 100%;
    width: 100%;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    object-fit: cover;
}

@media (max-width: 768px) {
    .featured-content .grid {
        grid-template-columns: 1fr;
    }

    .latest-posts .grid {
        grid-template-columns: 1fr;
    }

    .swarm-status .grid {
        grid-template-columns: 2fr 2fr;
    }
}

@media (max-width: 480px) {
    .swarm-status .grid {
        grid-template-columns: 1fr;
    }
}
</style>