<?php
/**
 * The main template file
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container py-12">

        <?php if (have_posts()) : ?>

            <header class="page-header mb-12 text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">Latest Insights</span>
                </h1>
            </header>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="aspect-w-16 aspect-h-9">
                                <?php the_post_thumbnail('large', array('class' => 'w-full h-48 object-cover')); ?>
                            </div>
                        <?php endif; ?>

                        <div class="p-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-3">
                                <a href="<?php the_permalink(); ?>" class="hover:text-purple-600 transition-colors">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <p class="text-gray-600 mb-4">
                                <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                            </p>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500"><?php echo get_the_date(); ?></span>
                                <a href="<?php the_permalink(); ?>" class="text-purple-600 hover:text-blue-600 transition-colors font-semibold">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <div class="pagination mt-12 text-center">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                    'class' => 'text-purple-600 hover:text-blue-600'
                ));
                ?>
            </div>

        <?php else : ?>

            <div class="text-center py-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">No Posts Found</h2>
                <p class="text-gray-600 mb-8">We're working on bringing you valuable business insights. Check back soon!</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Return Home</a>
            </div>

        <?php endif; ?>

    </div><!-- .container -->
</main><!-- #primary -->

<?php get_footer(); ?>