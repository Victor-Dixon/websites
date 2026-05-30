<?php
/**
 * Archive Template for Cheat Sheets Custom Post Type
 *
 * @package FreeRideInvestor
 */

get_header();

$queried_object = get_queried_object();
$post_type = $queried_object->name ?? 'cheat_sheet';
?>

<main class="content-area">
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">📊 Quick Reference Guides</span>
                <h1 class="hero-title">Cheat Sheets</h1>
                <p class="hero-subtitle">
                    Essential trading knowledge condensed into actionable cheat sheets.
                    Master complex strategies with our comprehensive reference guides.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo wp_count_posts('cheat_sheet')->publish; ?></span>
                        <span class="hero-stat-label">Cheat Sheets</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">Free</span>
                        <span class="hero-stat-label">Access</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">Updated</span>
                        <span class="hero-stat-label">Regularly</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Archive Content -->
    <section class="archive-content">
        <div class="container">
            <?php if (have_posts()) : ?>
                <div class="cheat-sheets-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article class="cheat-sheet-card">
                            <div class="cheat-sheet-header">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="cheat-sheet-image">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="cheat-sheet-content">
                                    <h2 class="cheat-sheet-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>

                                    <div class="cheat-sheet-meta">
                                        <span class="post-date"><?php echo get_the_date(); ?></span>
                                        <?php if (get_the_terms(get_the_ID(), 'stock-category')) : ?>
                                            <span class="post-categories">
                                                <?php echo get_the_term_list(get_the_ID(), 'stock-category', '', ', '); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="cheat-sheet-excerpt">
                                        <?php if (has_excerpt()) : ?>
                                            <p><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
                                        <?php else : ?>
                                            <p><?php echo wp_trim_words(get_the_content(), 25); ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="cheat-sheet-actions">
                                        <a href="<?php the_permalink(); ?>" class="download-btn">📥 View Cheat Sheet</a>
                                        <?php if (get_field('download_link')) : ?>
                                            <a href="<?php the_field('download_link'); ?>" class="download-btn secondary" target="_blank">⬇️ Download PDF</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => '← Previous',
                        'next_text' => 'Next →',
                    ));
                    ?>
                </div>

            <?php else : ?>
                <div class="no-posts">
                    <div class="no-posts-icon">📋</div>
                    <h2>No Cheat Sheets Available</h2>
                    <p>We're working on creating comprehensive trading cheat sheets. Check back soon!</p>
                    <a href="<?php echo home_url('/'); ?>" class="btn-primary">Return Home</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Categories Section -->
    <?php
    $categories = get_terms(array(
        'taxonomy' => 'stock-category',
        'hide_empty' => true,
    ));

    if (!empty($categories)) :
    ?>
    <section class="categories-section">
        <div class="container">
            <h2>Browse by Category</h2>
            <div class="categories-grid">
                <?php foreach ($categories as $category) : ?>
                    <a href="<?php echo get_term_link($category); ?>" class="category-card">
                        <div class="category-name"><?php echo esc_html($category->name); ?></div>
                        <div class="category-count"><?php echo $category->count; ?> sheets</div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Need a Custom Cheat Sheet?</h2>
                <p>Contact us for personalized trading guides tailored to your strategy and goals.</p>
                <div class="cta-buttons">
                    <a href="<?php echo home_url('/contact/'); ?>" class="btn-primary">Request Custom Guide</a>
                    <a href="<?php echo home_url('/'); ?>" class="btn-secondary">Explore More Resources</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.cheat-sheets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.cheat-sheet-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid var(--border, #e1e5e9);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.cheat-sheet-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.cheat-sheet-header {
    display: flex;
    gap: 1.5rem;
}

.cheat-sheet-image {
    flex-shrink: 0;
    width: 120px;
    height: 120px;
    overflow: hidden;
    border-radius: 8px;
}

.cheat-sheet-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cheat-sheet-content {
    flex: 1;
    padding: 1rem 0;
}

.cheat-sheet-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.4rem;
}

.cheat-sheet-title a {
    color: var(--text-primary, #333);
    text-decoration: none;
    transition: color 0.3s ease;
}

.cheat-sheet-title a:hover {
    color: var(--primary-color, #007cba);
}

.cheat-sheet-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: var(--text-secondary, #666);
}

.cheat-sheet-excerpt p {
    color: var(--text-primary, #333);
    line-height: 1.6;
    margin: 0 0 1rem 0;
}

.cheat-sheet-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.download-btn {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.download-btn {
    background: var(--primary-color, #007cba);
    color: white;
}

.download-btn:hover {
    background: var(--primary-hover, #005a87);
    transform: translateY(-1px);
}

.download-btn.secondary {
    background: var(--secondary-color, #6c757d);
}

.download-btn.secondary:hover {
    background: var(--secondary-hover, #545b62);
}

.categories-section {
    padding: 4rem 0;
    background: var(--surface, #f8f9fa);
}

.categories-section h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--text-primary, #333);
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.category-card {
    display: block;
    background: var(--card-bg, #fff);
    border-radius: 8px;
    padding: 1.5rem;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid var(--border, #e1e5e9);
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.category-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary, #333);
    margin-bottom: 0.5rem;
}

.category-count {
    font-size: 0.9rem;
    color: var(--text-secondary, #666);
}

/* Reuse existing styles from free-investor archive */
.pagination, .no-posts, .btn-primary, .btn-secondary, .cta-section, .cta-content, .cta-buttons {
    /* Same styles as free-investor archive */
}
</style>

<?php get_footer(); ?>