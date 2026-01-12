<?php
/**
 * Front Page Template for Crosby Ultimate Events Theme
 * Displays the hero section and featured event content
 */

get_header();
?>

<?php
// Include the hero section
get_template_part('template-parts/hero', 'events');
?>

<main id="primary" class="site-main">
    <div class="container">

        <!-- Featured Events Section -->
        <section class="featured-events py-16">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    🥏 Upcoming Events
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Join our ultimate frisbee community for tournaments, clinics, and events that bring
                    players of all skill levels together in the spirit of the game.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Spring Championship -->
                <div class="event-card">
                    <div class="event-card-header">
                        <h3 class="text-xl font-bold text-white mb-2">Spring Championship</h3>
                        <div class="text-green-200 text-sm">🥏 Tournament</div>
                    </div>
                    <div class="event-card-body">
                        <p class="text-gray-600 mb-4">
                            Our annual flagship tournament featuring teams from across the region.
                            32 teams, 3-day event with prizes and community celebration.
                        </p>
                        <div class="event-details mb-4">
                            <div class="text-sm text-gray-500">
                                📅 April 15-17, 2026<br>
                                📍 Crosby Sports Complex<br>
                                👥 32 Teams Registered
                            </div>
                        </div>
                        <a href="#register" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300">
                            Register Team →
                        </a>
                    </div>
                </div>

                <!-- Beginner Clinic -->
                <div class="event-card">
                    <div class="event-card-header">
                        <h3 class="text-xl font-bold text-white mb-2">Beginner Clinic</h3>
                        <div class="text-blue-200 text-sm">🎓 Training</div>
                    </div>
                    <div class="event-card-body">
                        <p class="text-gray-600 mb-4">
                            Perfect for new players looking to learn the fundamentals of ultimate frisbee.
                            Professional coaching and equipment provided.
                        </p>
                        <div class="event-details mb-4">
                            <div class="text-sm text-gray-500">
                                📅 Every Saturday<br>
                                📍 Crosby Field<br>
                                👥 All Skill Levels Welcome
                            </div>
                        </div>
                        <a href="#learn-more" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300">
                            Learn More →
                        </a>
                    </div>
                </div>

                <!-- Community Pickup -->
                <div class="event-card">
                    <div class="event-card-header">
                        <h3 class="text-xl font-bold text-white mb-2">Community Pickup</h3>
                        <div class="text-teal-200 text-sm">🤝 Social</div>
                    </div>
                    <div class="event-card-body">
                        <p class="text-gray-600 mb-4">
                            Casual pickup games for players of all levels. Great way to meet fellow
                            frisbee enthusiasts and improve your game.
                        </p>
                        <div class="event-details mb-4">
                            <div class="text-sm text-gray-500">
                                📅 Wednesdays 6-8 PM<br>
                                📍 Crosby Park<br>
                                👥 Drop-in Anytime
                            </div>
                        </div>
                        <a href="#join" class="inline-block bg-teal-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-teal-700 transition-colors duration-300">
                            Join Us →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Posts Section -->
        <?php if (have_posts()) : ?>
        <section class="latest-posts py-16 bg-gray-50">
            <div class="container">
                <h2 class="text-4xl font-bold text-center text-gray-900 mb-12">Latest from Our Community</h2>

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
                                    <a href="<?php the_permalink(); ?>" class="hover:text-green-600 transition-colors duration-300">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <p class="text-gray-600 mb-4">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500"><?php echo get_the_date(); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="text-green-600 font-semibold hover:text-green-700 transition-colors duration-300">
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

        <!-- Community Stats Section -->
        <section class="community-stats py-16">
            <div class="bg-gradient-to-r from-green-900 via-blue-900 to-teal-900 rounded-2xl p-8 text-white">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold mb-4">Community Impact</h2>
                    <p class="text-xl opacity-90">Building stronger connections through ultimate frisbee</p>
                </div>

                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-green-400 mb-2 community-connect">200+</div>
                        <div class="text-lg opacity-90">Active Players</div>
                        <div class="text-sm opacity-75">Growing Community</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-400 mb-2 community-connect">15</div>
                        <div class="text-lg opacity-90">Tournaments/Year</div>
                        <div class="text-sm opacity-75">Major Events</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-teal-400 mb-2 community-connect">50+</div>
                        <div class="text-lg opacity-90">Teams Supported</div>
                        <div class="text-sm opacity-75">All Skill Levels</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-yellow-400 mb-2 community-connect">10</div>
                        <div class="text-lg opacity-90">Years Running</div>
                        <div class="text-sm opacity-75">Established Legacy</div>
                    </div>
                </div>
            </div>
        </section>

    </div><!-- .container -->
</main><!-- #primary -->

<?php get_footer(); ?>

<style>
/* Additional front page styles */
.event-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.event-card-header {
    background: linear-gradient(135deg, #52b788, #74c69d);
    color: white;
    padding: 1.5rem;
}

.event-card-body {
    padding: 1.5rem;
}

.community-connect {
    animation: communityPulse 3s ease-in-out infinite;
}

@keyframes communityPulse {
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
    .featured-events .grid {
        grid-template-columns: 1fr;
    }

    .latest-posts .grid {
        grid-template-columns: 1fr;
    }

    .community-stats .grid {
        grid-template-columns: 2fr 2fr;
    }
}

@media (max-width: 480px) {
    .community-stats .grid {
        grid-template-columns: 1fr;
    }
}
</style>