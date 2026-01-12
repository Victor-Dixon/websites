<?php
/**
 * Front Page Template for AriaJet Gaming Theme
 * Displays the animated hero section and featured gaming content
 */

get_header();
?>

<?php
// Include the hero section
get_template_part('template-parts/hero', 'gaming');
?>

<main id="primary" class="site-main">
    <div class="container">

        <!-- Featured Games Section -->
        <section class="featured-games py-16 bg-gradient-to-b from-gray-900 to-black">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4 font-mono">
                    🎮 <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-cyan-400">Featured Games</span>
                </h2>
                <p class="text-xl text-gray-400 max-w-3xl mx-auto font-mono">
                    Discover our latest pixel-perfect adventures and brain-teasing challenges
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Space Explorer -->
                <div class="game-card">
                    <div class="game-card-header">
                        <h3 class="text-xl font-bold text-white mb-2">🚀 Space Explorer</h3>
                        <div class="text-cyan-200 text-sm">2D Adventure</div>
                    </div>
                    <div class="game-card-body">
                        <p class="text-gray-300 mb-4">
                            Embark on an epic space journey through pixelated galaxies.
                            Collect power-ups, battle alien foes, and uncover hidden secrets.
                        </p>
                        <div class="game-stats mb-4">
                            <div class="text-sm text-gray-400">
                                ⭐ 4.8/5 Rating<br>
                                🎯 10K+ Downloads<br>
                                🏆 50+ Achievements
                            </div>
                        </div>
                        <a href="#play-space-explorer" class="inline-block bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-3 rounded font-semibold hover:from-pink-700 hover:to-purple-700 transition-all duration-300">
                            Play Now →
                        </a>
                    </div>
                </div>

                <!-- Puzzle Master -->
                <div class="game-card">
                    <div class="game-card-header">
                        <h3 class="text-xl font-bold text-white mb-2">🧩 Puzzle Master</h3>
                        <div class="text-green-200 text-sm">Brain Teaser</div>
                    </div>
                    <div class="game-card-body">
                        <p class="text-gray-300 mb-4">
                            Challenge your mind with intricate puzzles and mind-bending logic.
                            Each level gets more complex, testing your problem-solving skills.
                        </p>
                        <div class="game-stats mb-4">
                            <div class="text-sm text-gray-400">
                                🧠 100+ Levels<br>
                                🏅 4.6/5 Rating<br>
                                🕐 2-5 min/level
                            </div>
                        </div>
                        <a href="#play-puzzle-master" class="inline-block bg-gradient-to-r from-green-600 to-teal-600 text-white px-6 py-3 rounded font-semibold hover:from-green-700 hover:to-teal-700 transition-all duration-300">
                            Play Now →
                        </a>
                    </div>
                </div>

                <!-- Retro Quest -->
                <div class="game-card">
                    <div class="game-card-header">
                        <h3 class="text-xl font-bold text-white mb-2">⚔️ Retro Quest</h3>
                        <div class="text-yellow-200 text-sm">RPG Adventure</div>
                    </div>
                    <div class="game-card-body">
                        <p class="text-gray-300 mb-4">
                            Embark on a classic RPG adventure with pixel art graphics,
                            epic quests, and memorable characters in a retro gaming world.
                        </p>
                        <div class="game-stats mb-4">
                            <div class="text-sm text-gray-400">
                                🗺️ Open World<br>
                                ⚔️ 20+ Characters<br>
                                ⭐ 4.9/5 Rating
                            </div>
                        </div>
                        <a href="#play-retro-quest" class="inline-block bg-gradient-to-r from-yellow-600 to-orange-600 text-white px-6 py-3 rounded font-semibold hover:from-yellow-700 hover:to-orange-700 transition-all duration-300">
                            Play Now →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Latest Posts Section -->
        <?php if (have_posts()) : ?>
        <section class="latest-posts py-16 bg-gradient-to-r from-gray-800 to-gray-900">
            <div class="container">
                <h2 class="text-4xl font-bold text-center text-white mb-12 font-mono">
                    📝 <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-pink-400">Latest Updates</span>
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
                        <article class="bg-gradient-to-br from-gray-700 to-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-pink-500/20 hover:border-cyan-400/50">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="aspect-w-16 aspect-h-9">
                                    <?php the_post_thumbnail('large', array('class' => 'w-full h-48 object-cover')); ?>
                                </div>
                            <?php endif; ?>

                            <div class="p-6">
                                <h3 class="text-xl font-bold text-white mb-3 font-mono">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-cyan-400 transition-colors duration-300">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <p class="text-gray-300 mb-4">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                </p>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400 font-mono"><?php echo get_the_date(); ?></span>
                                    <a href="<?php the_permalink(); ?>" class="text-cyan-400 font-semibold hover:text-pink-400 transition-colors duration-300 font-mono">
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
        <section class="community-stats py-16 bg-gradient-to-r from-black via-gray-900 to-black">
            <div class="bg-gradient-to-r from-pink-900/20 via-purple-900/20 to-cyan-900/20 rounded-2xl p-8 backdrop-blur-lg border border-pink-500/30">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-bold mb-4 text-white font-mono">🌟 Community Impact</h2>
                    <p class="text-xl opacity-90 text-gray-300">Building connections through gaming excellence</p>
                </div>

                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-pink-400 mb-2 animate-pulse">50K+</div>
                        <div class="text-lg opacity-90 text-white">Active Players</div>
                        <div class="text-sm opacity-75 text-cyan-400">Growing Community</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-cyan-400 mb-2 animate-pulse">15</div>
                        <div class="text-lg opacity-90 text-white">Games Released</div>
                        <div class="text-sm opacity-75 text-pink-400">Pixel Perfect</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-purple-400 mb-2 animate-pulse">4.7★</div>
                        <div class="text-lg opacity-90 text-white">Average Rating</div>
                        <div class="text-sm opacity-75 text-yellow-400">Player Loved</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-yellow-400 mb-2 animate-pulse">24/7</div>
                        <div class="text-lg opacity-90 text-white">Servers Online</div>
                        <div class="text-sm opacity-75 text-green-400">Always Available</div>
                    </div>
                </div>
            </div>
        </section>

    </div><!-- .container -->
</main><!-- #primary -->

<?php get_footer(); ?>

<style>
/* Additional front page styles */
.game-card {
    background: linear-gradient(135deg, rgba(255, 0, 255, 0.1), rgba(0, 255, 255, 0.1));
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(255, 0, 255, 0.3);
}

.game-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(255, 0, 255, 0.3);
}

.game-card-header {
    background: linear-gradient(135deg, rgba(255, 0, 255, 0.2), rgba(0, 255, 255, 0.2));
    color: white;
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255, 0, 255, 0.3);
}

.game-card-body {
    padding: 1.5rem;
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
    .featured-games .grid {
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