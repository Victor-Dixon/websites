<?php
/**
 * Template Name: Blog
 * Template Post Type: page
 *
 * Blog page template for FreeRide Investor
 *
 * @package FreeRideInvestor_V2
 */

get_header();
?>

<main class="blog-page">
    <!-- Hero Section -->
    <section class="blog-hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Trading Insights & Education</h1>
                <p class="hero-subtitle">Stay informed with expert analysis, market insights, and educational content from our trading team</p>

                <!-- Search and Filter -->
                <div class="blog-controls">
                    <div class="search-box">
                        <input type="text" placeholder="Search articles..." id="blogSearch">
                        <button type="button" class="search-btn">🔍</button>
                    </div>
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All Posts</button>
                        <button class="filter-btn" data-filter="analysis">Market Analysis</button>
                        <button class="filter-btn" data-filter="education">Education</button>
                        <button class="filter-btn" data-filter="strategy">Strategies</button>
                        <button class="filter-btn" data-filter="news">Trading News</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Post -->
    <section class="featured-post">
        <div class="container">
            <?php
            // Get the latest featured post
            $featured_args = array(
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => '_freerideinvestor_featured',
                        'value' => '1',
                        'compare' => '='
                    )
                )
            );

            $featured_query = new WP_Query($featured_args);

            if ($featured_query->have_posts()) :
                while ($featured_query->have_posts()) : $featured_query->the_post();
            ?>
            <article class="featured-article">
                <div class="featured-image">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else: ?>
                        <div class="placeholder-image">
                            <span>📈</span>
                        </div>
                    <?php endif; ?>
                    <div class="featured-badge">Featured</div>
                </div>

                <div class="featured-content">
                    <div class="post-meta">
                        <span class="post-category"><?php echo get_the_category()[0]->name; ?></span>
                        <span class="post-date"><?php echo get_the_date(); ?></span>
                        <span class="reading-time"><?php echo freerideinvestor_reading_time(); ?> min read</span>
                    </div>

                    <h2><?php the_title(); ?></h2>
                    <p class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>

                    <div class="post-actions">
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">Read Full Article</a>
                        <div class="post-stats">
                            <span>👁️ <?php echo get_post_meta(get_the_ID(), '_freerideinvestor_views', true) ?: '0'; ?> views</span>
                        </div>
                    </div>
                </div>
            </article>
            <?php
                endwhile;
                wp_reset_postdata();
            else:
            ?>
            <article class="featured-article placeholder">
                <div class="featured-image">
                    <div class="placeholder-image">
                        <span>📈</span>
                    </div>
                </div>

                <div class="featured-content">
                    <div class="post-meta">
                        <span class="post-category">Market Analysis</span>
                        <span class="post-date">Coming Soon</span>
                    </div>

                    <h2>Weekly Market Outlook</h2>
                    <p class="post-excerpt">Get our comprehensive weekly market analysis covering key trends, support/resistance levels, and trading opportunities across major asset classes.</p>

                    <div class="post-actions">
                        <span class="coming-soon">Coming Soon</span>
                    </div>
                </div>
            </article>
            <?php endif; ?>
        </div>
    </section>

    <!-- Blog Posts Grid -->
    <section class="blog-posts">
        <div class="container">
            <div class="posts-header">
                <h2>Latest Articles</h2>
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid">⊞ Grid</button>
                    <button class="view-btn" data-view="list">☰ List</button>
                </div>
            </div>

            <div class="posts-container" id="postsContainer">
                <?php
                // Get recent posts (excluding featured)
                $args = array(
                    'posts_per_page' => 12,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => '_freerideinvestor_featured',
                            'value' => '1',
                            'compare' => '!='
                        )
                    )
                );

                $blog_query = new WP_Query($args);

                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post();
                        $categories = get_the_category();
                        $category_class = strtolower($categories[0]->slug);
                ?>
                <article class="blog-post <?php echo esc_attr($category_class); ?>" data-category="<?php echo esc_attr($category_class); ?>">
                    <div class="post-image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('medium'); ?>
                        <?php else: ?>
                            <div class="placeholder-image">
                                <span><?php echo freerideinvestor_get_category_icon($categories[0]->slug); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-category"><?php echo $categories[0]->name; ?></span>
                            <span class="post-date"><?php echo get_the_date('M j, Y'); ?></span>
                        </div>

                        <h3><?php the_title(); ?></h3>
                        <p class="post-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>

                        <div class="post-footer">
                            <a href="<?php the_permalink(); ?>" class="read-more">Read More →</a>
                            <span class="reading-time"><?php echo freerideinvestor_reading_time(); ?> min</span>
                        </div>
                    </div>
                </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                <div class="no-posts">
                    <h3>No articles found</h3>
                    <p>Check back soon for new trading insights and educational content.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Load More Button -->
            <div class="load-more-container">
                <button class="btn btn-secondary" id="loadMoreBtn" style="display: none;">Load More Articles</button>
            </div>
        </div>
    </section>

    <!-- Newsletter CTA -->
    <section class="blog-newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2>Never Miss an Update</h2>
                <p>Get our latest market analysis and trading insights delivered directly to your inbox.</p>
                <form class="newsletter-form" id="blogNewsletterForm">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
                <p class="newsletter-note">Join 10,000+ traders who stay ahead of the market</p>
            </div>
        </div>
    </section>
</main>

<script>
// Blog functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('blogSearch');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const viewButtons = document.querySelectorAll('.view-btn');
    const postsContainer = document.getElementById('postsContainer');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const newsletterForm = document.getElementById('blogNewsletterForm');

    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterPosts(searchTerm, getActiveFilter());
    });

    // Filter functionality
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            filterPosts(searchInput.value.toLowerCase(), filter);
        });
    });

    // View toggle functionality
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const view = this.getAttribute('data-view');
            toggleView(view);
        });
    });

    // Newsletter form
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = newsletterForm.querySelector('.btn-primary');
        const originalText = submitBtn.textContent;

        submitBtn.textContent = 'Subscribing...';
        submitBtn.disabled = true;

        setTimeout(() => {
            submitBtn.textContent = 'Subscribed!';
            submitBtn.style.background = '#48bb78';

            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.style.background = '';
                submitBtn.disabled = false;
                newsletterForm.reset();
            }, 2000);
        }, 1000);
    });

    function filterPosts(searchTerm, categoryFilter) {
        const posts = document.querySelectorAll('.blog-post');

        posts.forEach(post => {
            const title = post.querySelector('h3').textContent.toLowerCase();
            const excerpt = post.querySelector('.post-excerpt').textContent.toLowerCase();
            const category = post.getAttribute('data-category');

            const matchesSearch = !searchTerm ||
                title.includes(searchTerm) ||
                excerpt.includes(searchTerm);

            const matchesCategory = categoryFilter === 'all' || category === categoryFilter;

            if (matchesSearch && matchesCategory) {
                post.style.display = 'block';
            } else {
                post.style.display = 'none';
            }
        });
    }

    function getActiveFilter() {
        const activeBtn = document.querySelector('.filter-btn.active');
        return activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
    }

    function toggleView(view) {
        const posts = document.querySelectorAll('.blog-post');

        if (view === 'list') {
            postsContainer.classList.add('list-view');
        } else {
            postsContainer.classList.remove('list-view');
        }
    }
});
</script>

<style>
/* Blog Page Styles */
.blog-page {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Hero Section */
.blog-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: white;
    padding: 6rem 0;
    text-align: center;
}

.hero-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 2rem;
}

.blog-hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1.4rem;
    opacity: 0.9;
    margin-bottom: 3rem;
    line-height: 1.6;
}

.blog-controls {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 2rem;
    align-items: center;
    max-width: 800px;
    margin: 0 auto;
}

.search-box {
    position: relative;
    max-width: 400px;
}

.search-box input {
    width: 100%;
    padding: 1rem 3rem 1rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 25px;
    font-size: 1rem;
    background: white;
    color: #1a202c;
}

.search-btn {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.5rem;
}

.filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    background: transparent;
    color: white;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn:hover,
.filter-btn.active {
    background: white;
    color: #1a202c;
    border-color: white;
}

/* Featured Post */
.featured-post {
    padding: 6rem 0;
    background: #f8fafc;
}

.featured-post .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.featured-article {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.featured-image {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.featured-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.placeholder-image {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
}

.featured-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: #48bb78;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.featured-content {
    padding: 3rem;
}

.post-meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.post-category {
    background: #00d4ff;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.post-date,
.reading-time {
    color: #718096;
    font-size: 0.9rem;
    font-weight: 500;
}

.featured-content h2 {
    font-size: 2.2rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
    line-height: 1.3;
}

.post-excerpt {
    color: #4a5568;
    line-height: 1.7;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

.post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.post-stats {
    color: #718096;
    font-size: 0.9rem;
}

/* Blog Posts */
.blog-posts {
    padding: 6rem 0;
    background: white;
}

.posts-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.posts-header h2 {
    font-size: 2.5rem;
    color: #1a202c;
    font-weight: 600;
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.view-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    color: #4a5568;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.view-btn.active {
    background: #00d4ff;
    color: white;
    border-color: #00d4ff;
}

.posts-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.blog-post {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.blog-post:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.post-image {
    height: 200px;
    overflow: hidden;
}

.post-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.post-content {
    padding: 2rem;
}

.post-content .post-meta {
    margin-bottom: 1rem;
}

.post-content h3 {
    font-size: 1.4rem;
    color: #1a202c;
    margin-bottom: 1rem;
    font-weight: 600;
    line-height: 1.3;
}

.post-content .post-excerpt {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.read-more {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: #0099cc;
}

/* List View */
.posts-container.list-view .blog-post {
    display: flex;
    height: auto;
}

.posts-container.list-view .post-image {
    width: 200px;
    height: 150px;
    flex-shrink: 0;
}

.posts-container.list-view .post-content {
    flex: 1;
    padding: 1.5rem;
}

/* Load More */
.load-more-container {
    text-align: center;
    margin-top: 3rem;
}

/* Newsletter CTA */
.blog-newsletter {
    padding: 6rem 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
}

.newsletter-content {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}

.newsletter-content h2 {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.newsletter-content p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.newsletter-form {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.newsletter-form input {
    flex: 1;
    min-width: 250px;
    padding: 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
}

.newsletter-note {
    font-size: 0.9rem;
    opacity: 0.7;
}

/* No Posts */
.no-posts {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.no-posts h3 {
    color: #1a202c;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.no-posts p {
    color: #4a5568;
    font-size: 1.1rem;
}

/* Button Styles */
.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    display: inline-block;
    text-align: center;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .blog-hero h1 {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1.2rem;
    }

    .blog-controls {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .filter-buttons {
        justify-content: center;
    }

    .featured-article {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .posts-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .posts-container {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .posts-container.list-view .blog-post {
        flex-direction: column;
    }

    .posts-container.list-view .post-image {
        width: 100%;
        height: 200px;
    }

    .newsletter-form {
        flex-direction: column;
    }

    .newsletter-form input {
        min-width: auto;
    }

    .container {
        padding: 0 1rem;
    }

    .blog-hero,
    .featured-post,
    .blog-posts,
    .blog-newsletter {
        padding: 4rem 0;
    }
}

@media (max-width: 480px) {
    .blog-hero h1 {
        font-size: 2rem;
    }

    .featured-content h2 {
        font-size: 1.8rem;
    }

    .posts-header h2,
    .newsletter-content h2 {
        font-size: 2rem;
    }

    .filter-buttons {
        flex-direction: column;
        align-items: center;
    }

    .filter-btn {
        width: 100%;
        max-width: 200px;
    }

    .post-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
}
</style>

<?php
// Helper functions
function freerideinvestor_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Assuming 200 words per minute
    return $reading_time;
}

function freerideinvestor_get_category_icon($category_slug) {
    $icons = array(
        'analysis' => '📊',
        'education' => '📚',
        'strategy' => '🎯',
        'news' => '📰',
        'default' => '📝'
    );

    return isset($icons[$category_slug]) ? $icons[$category_slug] : $icons['default'];
}

get_footer();
?>