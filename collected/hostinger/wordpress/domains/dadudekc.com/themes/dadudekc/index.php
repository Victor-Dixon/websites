<?php get_header(); ?>

<!-- Animated Automation Hero Section -->
<?php include(get_template_directory() . '/hero-automation.php'); ?>

<main>
    <div class='container'>
        <!-- Additional content sections can go here -->
        <section class="py-16">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-white mb-6">About <?php echo dadudekc_get_positioning_line(); ?></h2>
                <p class="text-xl text-gray-300 mb-8">
                    I specialize in creating automation systems that transform how teams work.
                    By eliminating repetitive tasks and streamlining workflows, my solutions help
                    organizations focus on what matters most - innovation and growth.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                    <div class="bg-white/5 p-6 rounded-lg border border-white/10">
                        <div class="text-4xl mb-4">⚡</div>
                        <h3 class="text-xl font-semibold text-white mb-2">Fast Implementation</h3>
                        <p class="text-gray-400">Quick deployment with minimal disruption to existing workflows.</p>
                    </div>
                    <div class="bg-white/5 p-6 rounded-lg border border-white/10">
                        <div class="text-4xl mb-4">🎯</div>
                        <h3 class="text-xl font-semibold text-white mb-2">Custom Solutions</h3>
                        <p class="text-gray-400">Tailored automation systems designed for your specific needs.</p>
                    </div>
                    <div class="bg-white/5 p-6 rounded-lg border border-white/10">
                        <div class="text-4xl mb-4">📈</div>
                        <h3 class="text-xl font-semibold text-white mb-2">Measurable Results</h3>
                        <p class="text-gray-400">Track time savings and efficiency improvements with detailed metrics.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio Showcase Section -->
        <section class="py-16 bg-gradient-to-br from-slate-800 to-slate-900">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Recent Automation Projects</h2>
                    <p class="text-xl text-gray-300">Real results from real automation systems</p>
                </div>

                <?php echo do_shortcode('[dadudekc_portfolio limit="3"]'); ?>

                <div class="text-center mt-12">
                    <a href="<?php echo esc_url(home_url('/projects')); ?>" class="inline-block bg-gradient-to-r from-blue-500 to-green-500 text-white px-8 py-4 rounded-full font-semibold text-lg hover:from-blue-600 hover:to-green-600 transition-all duration-300 transform hover:scale-105">
                        View All Projects →
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
