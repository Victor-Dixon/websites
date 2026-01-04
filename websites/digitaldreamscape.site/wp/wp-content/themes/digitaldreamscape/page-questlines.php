<?php
/**
 * Template Name: Questlines Directory
 *
 * Questlines overview page
 *
 * @package DigitalDreamscape
 * @since 4.0.0 - Questlines Edition
 */

get_header();

$canonical_url = home_url('/questlines/');
$page_title = "questlines directory";
$page_description = "All active questlines in Digital Dreamscape. Domains of exploration, development, and evolution.";

?>

<!-- SEO Meta -->
<link rel="canonical" href="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>" />
<meta property="og:description" content="<?php echo esc_attr($page_description); ?>" />
<meta property="og:url" content="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:type" content="website" />
<meta name="description" content="<?php echo esc_attr($page_description); ?>" />

<!-- Questlines Directory Header -->
<section class="ds-questlines-header">
    <div class="ds-questlines__inner">
        <h1>questlines directory</h1>
        <p>All active domains of exploration and development in Digital Dreamscape.</p>

        <div class="ds-questlines-stats">
            <span class="ds-stat-chip">total questlines: <?php echo wp_count_terms('category'); ?></span>
            <span class="ds-stat-chip">active domains: <?php echo wp_count_terms('category', array('hide_empty' => true)); ?></span>
        </div>

        <div class="ds-questlines-nav">
            <a href="<?php echo home_url('/blog/'); ?>" class="ds-btn">← back to archive</a>
        </div>
    </div>
</section>

<!-- Questlines Grid -->
<section class="ds-questlines-grid">
    <div class="ds-questlines__container">

        <?php
        $questlines = get_categories(array(
            'hide_empty' => false,
            'orderby' => 'count',
            'order' => 'DESC'
        ));

        foreach ($questlines as $questline) :
            $questline_link = get_category_link($questline);
            $artifact_count = $questline->count;
            $last_post = get_posts(array(
                'category' => $questline->term_id,
                'numberposts' => 1,
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            $last_updated = $last_post ? human_time_diff(strtotime($last_post[0]->post_date), current_time('timestamp')) . ' ago' : 'never';

            // Get questline theme colors
            $questline_colors = get_questline_theme($questline->slug);
            ?>

            <article class="ds-questline-card" style="--questline-color: <?php echo $questline_colors['primary']; ?>; --questline-accent: <?php echo $questline_colors['accent']; ?>">
                <div class="ds-questline-header">
                    <div class="ds-questline-icon">
                        <?php echo $questline_colors['icon']; ?>
                    </div>
                    <div class="ds-questline-meta">
                        <span class="ds-questline-count"><?php echo $artifact_count; ?> artifacts</span>
                        <span class="ds-questline-updated">updated <?php echo $last_updated; ?></span>
                    </div>
                </div>

                <div class="ds-questline-content">
                    <h3><a href="<?php echo esc_url($questline_link); ?>"><?php echo esc_html($questline->name); ?></a></h3>
                    <p><?php echo esc_html(get_questline_synopsis($questline->slug)); ?></p>
                </div>

                <div class="ds-questline-footer">
                    <div class="ds-questline-stats">
                        <span>active: <?php
                        $active_count = get_posts(array(
                            'category' => $questline->term_id,
                            'meta_key' => 'artifact_state',
                            'meta_value' => 'active',
                            'numberposts' => -1
                        ));
                        echo count($active_count);
                        ?></span>
                        <span>canon: <?php
                        $canon_count = get_posts(array(
                            'category' => $questline->term_id,
                            'meta_key' => 'canonical',
                            'meta_value' => 'true',
                            'numberposts' => -1
                        ));
                        echo count($canon_count);
                        ?></span>
                    </div>
                    <a href="<?php echo esc_url($questline_link); ?>" class="ds-btn ds-btn--primary">enter questline</a>
                </div>
            </article>

        <?php endforeach; ?>
    </div>
</section>

<?php

function get_questline_theme($slug) {
    $themes = [
        'debt-purge' => ['primary' => '#ff6b6b', 'accent' => '#ff3838', 'icon' => '🧹'],
        'system-architecture' => ['primary' => '#4fd1ff', 'accent' => '#00a8e8', 'icon' => '🏗️'],
        'world-building' => ['primary' => '#b864ff', 'accent' => '#a020f0', 'icon' => '🌍'],
        'trading-domain' => ['primary' => '#10b981', 'accent' => '#059669', 'icon' => '📈'],
        'agent-development' => ['primary' => '#f59e0b', 'accent' => '#d97706', 'icon' => '🤖']
    ];

    return $themes[$slug] ?? ['primary' => '#6366f1', 'accent' => '#4f46e5', 'icon' => '🎯'];
}

function get_questline_synopsis($slug) {
    $synopses = [
        'debt-purge' => 'Tracking the systematic elimination of technical debt. Every fix, regression, and optimization becomes part of the permanent record.',
        'system-architecture' => 'The evolution of system design patterns. From initial implementations to optimized architectures.',
        'world-building' => 'The construction of Digital Dreamscape itself. Meta-narratives, system integrations, and autonomous agents.',
        'trading-domain' => 'The FreeRide Investor questline. Risk-first trading systems, emotional discipline frameworks.',
        'agent-development' => 'The evolution of AI agents within the swarm. Coordination protocols, specialization patterns.'
    ];

    return $synopses[$slug] ?? "Questline tracking the development and evolution of {$slug}.";
}

get_footer();
?>