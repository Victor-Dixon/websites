<?php
/**
 * The sidebar containing the main widget area
 *
 * @package FreeRideInvestor_V2
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="sidebar widget-area">
    <?php dynamic_sidebar('sidebar-1'); ?>

    <!-- Default widgets if sidebar is empty -->
    <section class="widget widget_search">
        <h3 class="widget-title"><?php _e('Search', 'freerideinvestor'); ?></h3>
        <?php get_search_form(); ?>
    </section>

    <section class="widget widget_recent_entries">
        <h3 class="widget-title"><?php _e('Recent Posts', 'freerideinvestor'); ?></h3>
        <ul>
            <?php
            $recent_posts = wp_get_recent_posts(array(
                'numberposts' => 5,
                'post_status' => 'publish'
            ));

            foreach ($recent_posts as $post_item) :
                ?>
                <li>
                    <a href="<?php echo esc_url(get_permalink($post_item['ID'])); ?>">
                        <?php echo esc_html($post_item['post_title']); ?>
                    </a>
                </li>
                <?php
            endforeach;
            wp_reset_postdata();
            ?>
        </ul>
    </section>

    <section class="widget widget_categories">
        <h3 class="widget-title"><?php _e('Categories', 'freerideinvestor'); ?></h3>
        <ul>
            <?php
            $categories = get_categories();
            foreach ($categories as $category) :
                ?>
                <li>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                        <?php echo esc_html($category->name); ?>
                        <span class="post-count">(<?php echo $category->count; ?>)</span>
                    </a>
                </li>
                <?php
            endforeach;
            ?>
        </ul>
    </section>

    <section class="widget widget_text">
        <h3 class="widget-title"><?php _e('Connect With Us', 'freerideinvestor'); ?></h3>
        <div class="textwidget">
            <p><?php _e('Stay updated with the latest trading insights and market analysis.', 'freerideinvestor'); ?></p>
            <div class="social-links">
                <a href="#" class="social-link" aria-label="Discord">
                    <span class="dashicons dashicons-groups"></span>
                </a>
                <a href="#" class="social-link" aria-label="Twitter">
                    <span class="dashicons dashicons-twitter"></span>
                </a>
                <a href="#" class="social-link" aria-label="Twitch">
                    <span class="dashicons dashicons-video-alt3"></span>
                </a>
                <a href="mailto:info@freerideinvestor.com" class="social-link" aria-label="Email">
                    <span class="dashicons dashicons-email"></span>
                </a>
            </div>
        </div>
    </section>
</aside>

<style>
.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.social-link {
    display: inline-block;
    width: 40px;
    height: 40px;
    background: #007cba;
    color: white;
    border-radius: 50%;
    text-align: center;
    line-height: 40px;
    transition: background-color 0.3s ease;
}

.social-link:hover {
    background: #005a87;
}

.post-count {
    color: #666;
    font-size: 0.9rem;
}
</style>

