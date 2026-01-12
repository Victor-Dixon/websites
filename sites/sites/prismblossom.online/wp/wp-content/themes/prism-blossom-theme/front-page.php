<?php
/**
 * Front Page Template for PrismBlossom Business Theme
 * Displays the animated hero section and featured business content
 */

get_header();
?>

<?php
// Include the hero section
get_template_part('template-parts/hero', 'business');
?>

<main id="primary" class="site-main">
    <div class="container">

        <!-- Services Overview Section -->
        <section class="services-overview py-16 bg-white">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Our Services</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive business solutions designed to accelerate growth and drive innovation
                    across all aspects of your organization.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Strategic Consulting -->
                <div class="service-card">
                    <div class="service-card-header">
                        <div class="text-4xl mb-4">🎯</div>
                        <h3 class="text-xl font-bold text-white mb-2">Strategic Consulting</h3>
                    </div>
                    <div class="service-card-body">
                        <p class="text-gray-600 mb-6">
                            Develop comprehensive business strategies that align with your vision
                            and market opportunities. We help you navigate complex challenges.
                        </p>
                        <ul class="text-sm text-gray-500 mb-6 space-y-1">
                            <li>✓ Market Analysis</li>
                            <li>✓ Competitive Intelligence</li>
                            <li>✓ Growth Strategy</li>
                            <li>✓ Risk Assessment</li>
                        </ul>
                        <a href="#strategic-consulting" class="text-purple-600 font-semibold hover:text-blue-600 transition-colors">
                            Learn More →
                        </a>
                    </div>
                </div>

                <!-- Digital Transformation -->
                <div class="service-card">
                    <div class="service-card-header">
                        <div class="text-4xl mb-4">🚀</div>
                        <h3 class="text-xl font-bold text-white mb-2">Digital Transformation</h3>
                    </div>
                    <div class="service-card-body">
                        <p class="text-gray-600 mb-6">
                            Modernize your business operations with cutting-edge technology
                            solutions that improve efficiency and customer experience.
                        </p>
                        <ul class="text-sm text-gray-500 mb-6 space-y-1">
                            <li>✓ Process Automation</li>
                            <li>✓ Cloud Migration</li>
                            <li>✓ Data Analytics</li>
                            <li>✓ Customer Experience</li>
                        </ul>
                        <a href="#digital-transformation" class="text-purple-600 font-semibold hover:text-blue-600 transition-colors">
                            Learn More →
                        </a>
                    </div>
                </div>

                <!-- Business Development -->
                <div class="service-card">
                    <div class="service-card-header">
                        <div class="text-4xl mb-4">📈</div>
                        <h3 class="text-xl font-bold text-white mb-2">Business Development</h3>
                    </div>
                    <div class="service-card-body">
                        <p class="text-gray-600 mb-6">
                            Expand your market reach and revenue streams through strategic
                            partnerships, new market entries, and growth initiatives.
                        </p>
                        <ul class="text-sm text-gray-500 mb-6 space-y-1">
                            <li>✓ Partnership Strategy</li>
                            <li>✓ Market Expansion</li>
                            <li>✓ Revenue Optimization</li>
                            <li>✓ Channel Development</li>
                        </ul>
                        <a href="#business-development" class="text-purple-600 font-semibold hover:text-blue-600 transition-colors">
                            Learn More →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Stories Section -->
        <section class="success-stories py-16 bg-gradient-to-r from-purple-50 to-blue-50">
            <div class="container">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Success Stories</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Real results from businesses that partnered with PrismBlossom
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="success-story-card">
                        <div class="bg-white p-8 rounded-xl shadow-lg border border-purple-100">
                            <div class="text-4xl text-purple-600 mb-4">💼</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Tech Startup Scale-up</h3>
                            <p class="text-gray-600 mb-6">
                                "PrismBlossom helped us secure $5M in Series A funding and expand
                                to 3 new markets within 18 months. Their strategic guidance was invaluable."
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    <div class="font-semibold">TechFlow Solutions</div>
                                    <div>CEO - Sarah Chen</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-600">300%</div>
                                    <div class="text-sm text-gray-500">Revenue Growth</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="success-story-card">
                        <div class="bg-white p-8 rounded-xl shadow-lg border border-blue-100">
                            <div class="text-4xl text-blue-600 mb-4">🏭</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Manufacturing Optimization</h3>
                            <p class="text-gray-600 mb-6">
                                "Through digital transformation initiatives, we reduced operational
                                costs by 40% and improved delivery times by 60%. Exceptional results."
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    <div class="font-semibold">Industrio Corp</div>
                                    <div>CFO - Michael Rodriguez</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-600">40%</div>
                                    <div class="text-sm text-gray-500">Cost Reduction</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Posts Section -->
        <?php if (have_posts()) : ?>
        <section class="latest-posts py-16 bg-white">
            <div class="container">
                <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Latest Insights</span>
                </h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    $latest_posts = new WP_Query(array(
                        'posts_per_page' => 6,
                        'post_status' => 'publish'
                    ));

                    if ($latest_posts->have_posts()) :
                        while ($latest_posts->have_posts()) : $latest_posts->the_post();
                        ?>
                        <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100">
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
                                    <a href="<?php the_permalink(); ?>" class="text-purple-600 font-semibold hover:text-blue-600 transition-colors duration-300">
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

        <!-- CTA Section -->
        <section class="cta-section py-16 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
            <div class="container text-center">
                <h2 class="text-4xl font-bold mb-4">Ready to Transform Your Business?</h2>
                <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto">
                    Let's discuss how PrismBlossom can help you achieve your strategic objectives
                    and drive sustainable growth for your organization.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#contact" class="btn btn-primary bg-white text-purple-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-semibold">
                        Schedule Consultation
                    </a>
                    <a href="#services" class="btn btn-secondary border-2 border-white text-white hover:bg-white hover:text-purple-600 px-8 py-4 rounded-lg font-semibold">
                        View Our Services
                    </a>
                </div>
            </div>
        </section>

    </div><!-- .container -->
</main><!-- #primary -->

<?php get_footer(); ?>

<style>
/* Additional front page styles */
.service-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e5e7eb;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.service-card-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 2rem 1.5rem 1rem;
    text-align: center;
}

.service-card-body {
    padding: 1.5rem;
}

.success-story-card {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
    .services-overview .grid,
    .latest-posts .grid {
        grid-template-columns: 1fr;
    }

    .success-stories .grid {
        grid-template-columns: 1fr;
    }
}
</style>