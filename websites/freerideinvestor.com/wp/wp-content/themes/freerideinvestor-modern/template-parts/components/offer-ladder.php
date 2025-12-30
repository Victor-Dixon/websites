<?php
/**
 * Offer Ladder Component
 * Phase 1 Brand Core Fix - BRAND-02
 * Displays hierarchical offer ladder from Custom Post Type
 *
 * @package SimplifiedTradingTheme
 */

$site = parse_url(get_site_url(), PHP_URL_HOST);
$ladder = get_posts([
    'post_type' => 'offer_ladder',
    'meta_key' => 'site_assignment',
    'meta_value' => $site,
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'meta_value_num',
    'meta_key' => 'ladder_level',
    'order' => 'ASC'
]);

if ($ladder) {
    ?>
    <div class="offer-ladder">
        <?php foreach ($ladder as $offer): 
            $level = get_post_meta($offer->ID, 'ladder_level', true);
            $offer_name = get_post_meta($offer->ID, 'offer_name', true);
            $price = get_post_meta($offer->ID, 'price_point', true);
            $description = get_post_meta($offer->ID, 'offer_description', true);
            $cta_text = get_post_meta($offer->ID, 'cta_text', true);
            $cta_url = get_post_meta($offer->ID, 'cta_url', true);
        ?>
            <div class="offer-level service-item" data-level="<?php echo esc_attr($level); ?>">
                <div class="offer-level-badge">Level <?php echo esc_html($level); ?></div>
                <h3><?php echo esc_html($offer_name); ?></h3>
                <p class="price"><?php echo esc_html($price); ?></p>
                <p class="description"><?php echo esc_html($description); ?></p>
                <?php if ($cta_url && $cta_text): ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="cta-button">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}
?>

