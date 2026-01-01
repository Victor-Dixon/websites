<?php
/**
 * Experiments Feed Component
 * SSOT: Builder logs (experiments → learnings → next build)
 * Dynamically pulls from 'experiment' Custom Post Type
 *
 * @package DaDudeKC
 */

$experiments = get_posts([
    'post_type' => 'experiment',
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_query' => [
        [
            'key' => 'experiment_status',
            'value' => 'archived',
            'compare' => '!=',
        ],
    ],
]);

if ($experiments) : ?>
    <section class="experiments-feed-section">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e('🧪 Live Experiments', 'dadudekc'); ?></h2>
            <p class="section-subtitle"><?php esc_html_e('Real projects. Real progress. Built transparently.', 'dadudekc'); ?></p>
            
            <div class="experiments-feed">
                <?php foreach ($experiments as $experiment) :
                    $status = get_post_meta($experiment->ID, 'experiment_status', true) ?: 'in-progress';
                    $url = get_post_meta($experiment->ID, 'experiment_url', true);
                    $stats = get_post_meta($experiment->ID, 'experiment_stats', true);
                    $learnings = get_post_meta($experiment->ID, 'experiment_learnings', true);
                    $is_featured = $status === 'live';
                    
                    // Parse stats if JSON
                    $stats_array = [];
                    if ($stats) {
                        $parsed = json_decode($stats, true);
                        if (is_array($parsed)) {
                            $stats_array = $parsed;
                        }
                    }
                ?>
                    <div class="experiment-feed-item <?php echo $is_featured ? 'featured' : ''; ?>">
                        <div class="experiment-feed-header">
                            <h3 class="experiment-feed-title"><?php echo esc_html($experiment->post_title); ?></h3>
                            <span class="status-badge status-<?php echo esc_attr($status); ?>">
                                <?php echo esc_html(strtoupper(str_replace('-', ' ', $status))); ?>
                            </span>
                        </div>
                        <div class="experiment-feed-description">
                            <?php echo wp_kses_post($experiment->post_excerpt ?: wp_trim_words($experiment->post_content, 30)); ?>
                        </div>
                        <?php if ($learnings) : ?>
                            <div class="experiment-learnings">
                                <strong><?php esc_html_e('Key Learnings:', 'dadudekc'); ?></strong>
                                <?php echo esc_html($learnings); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($stats_array)) : ?>
                            <div class="experiment-stats">
                                <?php foreach ($stats_array as $stat) : ?>
                                    <span class="stat"><?php echo esc_html($stat); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($url) : ?>
                            <a href="<?php echo esc_url($url); ?>" class="experiment-link" target="_blank" rel="noopener">
                                <?php esc_html_e('View Experiment →', 'dadudekc'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

