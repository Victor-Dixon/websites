<?php
/**
 * Positioning Statement Component
 * Phase 1 Brand Core Fix - BRAND-01
 * Displays positioning statement from Custom Post Type
 *
 * @package SimplifiedTradingTheme
 */

$site = parse_url(get_site_url(), PHP_URL_HOST);
$positioning = get_posts([
    'post_type' => 'positioning_statement',
    'meta_key' => 'site_assignment',
    'meta_value' => $site,
    'posts_per_page' => 1,
    'post_status' => 'publish'
]);

if ($positioning) {
    $post = $positioning[0];
    $target = get_post_meta($post->ID, 'target_audience', true);
    $pain = get_post_meta($post->ID, 'pain_points', true);
    $value = get_post_meta($post->ID, 'unique_value', true);
    $diff = get_post_meta($post->ID, 'differentiation', true);
    ?>
    <div class="positioning-statement hero-positioning">
        <div class="positioning-card">
            <p class="positioning-text">
                For <strong><?php echo esc_html($target); ?></strong> 
                who <?php echo esc_html($pain); ?>, 
                we provide <?php echo esc_html($value); ?> 
                <span class="differentiation">(unlike competitors because <?php echo esc_html($diff); ?>)</span>
            </p>
        </div>
    </div>
    <?php
}
?>

