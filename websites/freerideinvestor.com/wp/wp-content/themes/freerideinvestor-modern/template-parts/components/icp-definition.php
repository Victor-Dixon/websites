<?php
/**
 * ICP Definition Component
 * Phase 1 Brand Core Fix - BRAND-03
 * Displays ICP definition from Custom Post Type
 *
 * @package SimplifiedTradingTheme
 */

$site = parse_url(get_site_url(), PHP_URL_HOST);
$icp = get_posts([
    'post_type' => 'icp_definition',
    'meta_key' => 'site_assignment',
    'meta_value' => $site,
    'posts_per_page' => 1,
    'post_status' => 'publish'
]);

if ($icp) {
    $post = $icp[0];
    $demographic = get_post_meta($post->ID, 'target_demographic', true);
    $pain = get_post_meta($post->ID, 'pain_points', true);
    $outcomes = get_post_meta($post->ID, 'desired_outcomes', true);
    ?>
    <div class="icp-definition icp-card">
        <h3 class="icp-heading">Ideal Customer Profile</h3>
        <div class="icp-content">
            <p class="icp-text">
                For <strong><?php echo esc_html($demographic); ?></strong> 
                who <?php echo esc_html($pain); ?>, 
                we eliminate workflow bottlenecks.
            </p>
            <p class="outcome-text">
                <strong>Your outcome:</strong> <?php echo esc_html($outcomes); ?>
            </p>
        </div>
    </div>
    <?php
}
?>

