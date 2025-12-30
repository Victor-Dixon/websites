<?php
/**
 * ICP Definition Component
 * Phase 1 Brand Core Fix - BRAND-03
 * Displays ICP definition from Custom Post Type
 *
 * @package DaDudeKC
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
    <section class="icp-definition-section">
        <div class="container">
            <div class="icp-definition icp-card">
                <h3 class="icp-heading"><?php esc_html_e('Ideal Customer Profile', 'dadudekc'); ?></h3>
                <div class="icp-content">
                    <p class="icp-text">
                        <?php 
                        printf(
                            esc_html__('For %s who %s, we eliminate workflow bottlenecks.', 'dadudekc'),
                            '<strong>' . esc_html($demographic) . '</strong>',
                            esc_html($pain)
                        );
                        ?>
                    </p>
                    <p class="outcome-text">
                        <strong><?php esc_html_e('Your outcome:', 'dadudekc'); ?></strong> <?php echo esc_html($outcomes); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>
    <?php
}
?>



