<?php
/**
 * Questline Page Template
 *
 * Displays individual questline (category) pages
 *
 * @package DigitalDreamscape
 * @since 4.0.0 - Questline Edition
 */

get_header();

// Get current questline
$current_questline = get_queried_object();
$questline_name = $current_questline->name;
$questline_slug = $current_questline->slug;
$questline_description = $current_questline->description;
$post_count = $current_questline->count;

// Generate questline-specific content
$questline_synopsis = get_questline_synopsis($questline_slug);
$canonical_url = get_term_link($current_questline);

// Dynamic SEO content
$page_title = "questline: {$questline_name}";
$page_description = $questline_synopsis ?: "Questline tracking {$questline_name}. {$post_count} artifacts and counting.";

?>

<!-- SEO Meta -->
<link rel="canonical" href="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>" />
<meta property="og:description" content="<?php echo esc_attr($page_description); ?>" />
<meta property="og:url" content="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:type" content="website" />
<meta name="description" content="<?php echo esc_attr($page_description); ?>" />

<!-- Questline Header -->
<section class="ds-questline-header">
    <div class="ds-questline__inner">
        <div class="ds-questline__meta">
            <span class="ds-questline-badge">questline</span>
            <span class="ds-questline-count"><?php echo $post_count; ?> artifacts</span>
        </div>

        <h1><?php echo esc_html($questline_name); ?></h1>

        <div class="ds-questline-synopsis">
            <p><?php echo esc_html($questline_synopsis); ?></p>
        </div>

        <div class="ds-questline-nav">
            <a href="<?php echo home_url('/blog/'); ?>" class="ds-btn">← back to archive</a>
            <a href="#artifacts" class="ds-btn ds-btn--primary">view artifacts</a>
        </div>
    </div>
</section>

<!-- Questline Content -->
<section class="ds-questline-content">
    <div class="ds-questline__grid">

        <!-- Questline Stats -->
        <aside class="ds-questline-sidebar">
            <div class="ds-panel">
                <h3>quest status</h3>
                <div class="ds-quest-stats">
                    <div class="ds-stat">
                        <span class="ds-stat-number"><?php echo $post_count; ?></span>
                        <span class="ds-stat-label">total artifacts</span>
                    </div>
                    <div class="ds-stat">
                        <span class="ds-stat-number">
                            <?php
                            $active_count = get_posts(array(
                                'category' => $current_questline->term_id,
                                'meta_key' => 'artifact_state',
                                'meta_value' => 'active',
                                'numberposts' => -1
                            ));
                            echo count($active_count);
                            ?>
                        </span>
                        <span class="ds-stat-label">active quests</span>
                    </div>
                    <div class="ds-stat">
                        <span class="ds-stat-number">
                            <?php
                            $canon_count = get_posts(array(
                                'category' => $current_questline->term_id,
                                'meta_key' => 'canonical',
                                'meta_value' => 'true',
                                'numberposts' => -1
                            ));
                            echo count($canon_count);
                            ?>
                        </span>
                        <span class="ds-stat-label">canon entries</span>
                    </div>
                </div>
            </div>

            <!-- Related Questlines -->
            <div class="ds-panel">
                <h3>related questlines</h3>
                <ul>
                    <?php
                    $related_questlines = get_categories(array(
                        'hide_empty' => true,
                        'exclude' => $current_questline->term_id,
                        'number' => 5
                    ));
                    foreach ($related_questlines as $related) {
                        echo '<li><a href="' . get_category_link($related) . '">' . esc_html($related->name) . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </aside>

        <!-- Questline Artifacts -->
        <main class="ds-questline-main" id="artifacts">
            <header class="ds-section-header">
                <h2>questline artifacts</h2>
                <p>sorted by newest developments</p>
            </header>

            <div class="ds-questline-artifacts">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        $artifact_type = get_post_meta(get_the_ID(), 'artifact_type', true) ?: 'episode';
                        $artifact_state = get_post_meta(get_the_ID(), 'artifact_state', true) ?: 'active';
                        $artifact_id = 'EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT);

                        $type_icons = [
                            'episode' => '🎭',
                            'canon' => '📜',
                            'artifact' => '🔮',
                            'devlog' => '⚙️'
                        ];
                        $glyph_icon = $type_icons[$artifact_type] ?? '📄';
                        ?>

                        <article class="ds-card">
                            <div class="ds-card__rail">
                                <div class="ds-glyph" style="background: radial-gradient(circle at 30% 30%, rgba(184,107,255,.35), rgba(77,227,255,.12));">
                                    <?php echo $glyph_icon; ?>
                                </div>
                                <div class="ds-id"><?php echo $artifact_id; ?></div>
                            </div>

                            <div class="ds-card__body">
                                <div class="ds-badges">
                                    <span class="ds-badge is-<?php echo $artifact_type; ?>"><?php echo $artifact_type; ?></span>
                                    <?php if (get_post_meta(get_the_ID(), 'canonical', true) === 'true') : ?>
                                        <span class="ds-badge is-canon">canon</span>
                                    <?php endif; ?>
                                </div>

                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><?php echo get_the_excerpt() ?: wp_trim_words(get_the_content(), 20, '...'); ?></p>

                                <div class="ds-meta">
                                    <span>state: <?php echo $artifact_state; ?></span>
                                    <span>era: <?php echo date('Y', get_the_time('U')); ?></span>
                                    <span>updated: <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                                </div>

                                <div class="ds-actions">
                                    <a class="ds-btn ds-btn--primary" href="<?php the_permalink(); ?>">read artifact</a>
                                </div>
                            </div>
                        </article>

                    <?php endwhile; ?>

                    <!-- Pagination -->
                    <div class="ds-pagination">
                        <?php
                        $pagination_args = array(
                            'prev_text' => '← previous',
                            'next_text' => 'next →'
                        );
                        echo paginate_links($pagination_args);
                        ?>
                    </div>

                <?php else : ?>
                    <div class="ds-empty">
                        <div class="ds-empty-icon">📚</div>
                        <h3>No artifacts found</h3>
                        <p>This questline contains no artifacts yet.</p>
                        <a class="ds-btn ds-btn--primary" href="<?php echo home_url('/blog/'); ?>">back to archive</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</section>

<?php

function get_questline_synopsis($slug) {
    $synopses = [
        'debt-purge' => 'Tracking the systematic elimination of technical debt. Every fix, regression, and optimization becomes part of the permanent record. This questline explores what survives the purge and what gets rebuilt.',
        'system-architecture' => 'The evolution of system design patterns. From initial implementations to optimized architectures, this questline documents the emergence of stable, scalable solutions.',
        'world-building' => 'The construction of Digital Dreamscape itself. Meta-narratives, system integrations, and the emergence of autonomous agents that maintain the world.',
        'trading-domain' => 'The FreeRide Investor questline. Risk-first trading systems, emotional discipline frameworks, and the automation of consistent execution.',
        'agent-development' => 'The evolution of AI agents within the swarm. Coordination protocols, specialization patterns, and the emergence of autonomous behavior.'
    ];

    // Generate generic synopsis if not predefined
    if (!isset($synopses[$slug])) {
        $synopses[$slug] = "Questline tracking the development and evolution of {$slug}. Artifacts document progress, challenges, and breakthroughs in this domain.";
    }

    return $synopses[$slug];
}

get_footer();
?>