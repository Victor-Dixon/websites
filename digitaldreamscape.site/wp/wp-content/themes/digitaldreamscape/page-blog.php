<?php
/**
 * World Archive - Digital Dreamscape Repository
 *
 * Episodes, canon, artifacts, and unfinished quests
 *
 * @package DigitalDreamscape
 * @since 4.0.0 - World Archive Edition
 */

get_header();

// Get world statistics
$total_posts = wp_count_posts()->publish;
$categories = get_categories();
$total_categories = count($categories);
$last_post = wp_get_recent_posts(array('numberposts' => 1))[0] ?? null;
$last_update = $last_post ? date('M j, Y', strtotime($last_post['post_date'])) : 'Unknown';

// Get current filters
$current_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
$current_questline = isset($_GET['questline']) ? sanitize_text_field($_GET['questline']) : '';
$current_state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
$current_search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Build dynamic SEO content based on filters
$page_title = 'world archive';
$page_description = 'episodes, canon, artifacts, and unfinished quests.';
$seo_intro = '';
$canonical_url = home_url('/blog/');

if ($current_type) {
    $canonical_url = add_query_arg('type', $current_type, $canonical_url);
    $page_title = "world archive: {$current_type}";
    $seo_intro = get_filter_description($current_type);
} elseif ($current_questline) {
    $canonical_url = add_query_arg('questline', $current_questline, $canonical_url);
    $questline_name = get_term_by('slug', $current_questline, 'category')->name ?? $current_questline;
    $page_title = "world archive: {$questline_name}";
    $seo_intro = get_questline_description($current_questline);
} elseif ($current_state) {
    $canonical_url = add_query_arg('state', $current_state, $canonical_url);
    $page_title = "world archive: {$current_state} artifacts";
    $seo_intro = get_state_description($current_state);
} elseif ($current_search) {
    $canonical_url = add_query_arg('s', urlencode($current_search), $canonical_url);
    $page_title = "world archive: \"{$current_search}\"";
    $seo_intro = "search results for \"{$current_search}\" in the digital dreamscape archive.";
}

function get_filter_description($type) {
    $descriptions = [
        'canon' => 'sacred entries. stable lore. no noise. these are the permanent artifacts that define the digital dreamscape.',
        'episode' => 'narrative fragments. world snapshots. these entries capture specific moments in the simulation\'s evolution.',
        'artifact' => 'discovered objects. system outputs. tools, fixes, and creations that emerged from the world.',
        'devlog' => 'builder\'s notes. raw telemetry. direct from the development trenches of digital dreamscape.'
    ];
    return $descriptions[$type] ?? '';
}

function get_questline_description($questline_slug) {
    $questline = get_term_by('slug', $questline_slug, 'category');
    if (!$questline) return '';

    $post_count = $questline->count;
    $description = "questline: {$questline->name}. {$post_count} artifacts. ";

    // Add questline-specific description based on name
    if (stripos($questline->name, 'debt') !== false) {
        $description .= "tracking the purge of technical debt. fixes, regressions, and what survived the optimization.";
    } elseif (stripos($questline->name, 'system') !== false) {
        $description .= "system architecture evolution. patterns, constraints, and emergent behaviors.";
    } else {
        $description .= "ongoing questline in the digital dreamscape. follow the artifacts for progress updates.";
    }

    return $description;
}

function get_state_description($state) {
    $descriptions = [
        'active' => 'living artifacts. unresolved loops. these entries represent current quests and open problems.',
        'resolved' => 'completed quests. closed loops. artifacts that reached their conclusion or stable state.',
        'abandoned' => 'forgotten paths. abandoned branches. historical artifacts that were set aside or superseded.'
    ];
    return $descriptions[$state] ?? '';
}

// Clear filters URL
$clear_filters_url = home_url('/blog/');

?>

<!-- SEO Meta -->
<link rel="canonical" href="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>" />
<meta property="og:description" content="<?php echo esc_attr($page_description); ?>" />
<meta property="og:url" content="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:type" content="website" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="description" content="<?php echo esc_attr($page_description); ?>" />

<!-- Schema.org CollectionPage -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "<?php echo esc_js($page_title); ?>",
  "description": "<?php echo esc_js($page_description); ?>",
  "url": "<?php echo esc_url($canonical_url); ?>",
  "mainEntity": {
    "@type": "ItemList",
    "numberOfItems": "<?php echo $total_posts; ?>",
    "itemListElement": [
      <?php
      $schema_items = array();
      $recent_posts = get_posts(array('numberposts' => 10, 'orderby' => 'date', 'order' => 'DESC'));
      foreach ($recent_posts as $index => $post) {
          $schema_items[] = '{
            "@type": "ListItem",
            "position": "' . ($index + 1) . '",
            "item": {
              "@type": "BlogPosting",
              "headline": "' . esc_js(get_the_title($post)) . '",
              "url": "' . esc_url(get_permalink($post)) . '",
              "datePublished": "' . get_the_date('c', $post) . '",
              "author": {
                "@type": "Person",
                "name": "' . esc_js(get_the_author_meta('display_name', $post->post_author)) . '"
              }
            }
          }';
      }
      echo implode(',', $schema_items);
      ?>
    ]
  },
  "publisher": {
    "@type": "Organization",
    "name": "Digital Dreamscape",
    "description": "A living world where systems evolve, agents act, and nothing you build is lost."
  }
}
</script>

<!-- World Archive Portal Header -->
<section class="ds-portal">
    <div class="ds-portal__inner">
        <div class="ds-portal__head">
            <h1><?php echo esc_html($page_title); ?></h1>
            <p><?php echo esc_html($page_description); ?></p>
            <?php if ($seo_intro): ?>
                <div class="ds-seo-intro"><?php echo esc_html($seo_intro); ?></div>
            <?php endif; ?>
        </div>

        <div class="ds-status">
            <span class="ds-status__chip">world state: <?php echo $total_posts > 10 ? 'stable' : 'emerging'; ?></span>
            <span class="ds-status__chip">last update: <time><?php echo $last_update; ?></time></span>
            <span class="ds-status__chip">active questlines: <?php echo min($total_categories, 7); ?></span>
        </div>

        <div class="ds-portal__cta">
            <a class="ds-btn ds-btn--primary" href="#latest">enter latest</a>
            <a class="ds-btn" href="?type=canon">canon only</a>
            <?php if ($current_type || $current_questline || $current_state || $current_search): ?>
                <a class="ds-btn ds-btn--secondary" href="<?php echo esc_url($clear_filters_url); ?>">clear filters</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- World Archive Interface -->
<section class="ds-archive">
    <!-- Filter Rail -->
    <aside class="ds-filters">
        <h2>filters</h2>

        <div class="ds-filter">
            <label>type</label>
            <div class="ds-chips">
                <a class="ds-chip <?php echo $current_type === 'episode' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('type', 'episode', remove_query_arg(array('questline', 'state', 's')))); ?>">episode</a>
                <a class="ds-chip <?php echo $current_type === 'canon' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('type', 'canon', remove_query_arg(array('questline', 'state', 's')))); ?>">canon</a>
                <a class="ds-chip <?php echo $current_type === 'artifact' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('type', 'artifact', remove_query_arg(array('questline', 'state', 's')))); ?>">artifact</a>
                <a class="ds-chip <?php echo $current_type === 'devlog' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('type', 'devlog', remove_query_arg(array('questline', 'state', 's')))); ?>">devlog</a>
            </div>
        </div>

        <div class="ds-filter">
            <label>questline</label>
            <div class="ds-chips">
                <?php
                $questlines = get_categories(array('hide_empty' => false, 'number' => 8));
                foreach ($questlines as $questline) {
                    $active_class = $current_questline === $questline->slug ? 'active' : '';
                    $filter_url = add_query_arg('questline', $questline->slug, remove_query_arg(array('type', 'state', 's')));
                    echo '<a class="ds-chip ' . $active_class . '" href="' . esc_url($filter_url) . '">' . esc_html($questline->name) . '</a>';
                }
                ?>
            </div>
        </div>

        <div class="ds-filter">
            <label>status</label>
            <div class="ds-chips">
                <a class="ds-chip <?php echo $current_state === 'active' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('state', 'active', remove_query_arg(array('type', 'questline', 's')))); ?>">active</a>
                <a class="ds-chip <?php echo $current_state === 'resolved' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('state', 'resolved', remove_query_arg(array('type', 'questline', 's')))); ?>">resolved</a>
                <a class="ds-chip <?php echo $current_state === 'abandoned' ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg('state', 'abandoned', remove_query_arg(array('type', 'questline', 's')))); ?>">abandoned</a>
            </div>
        </div>

        <div class="ds-filter">
            <label>search</label>
            <input class="ds-input" type="search" placeholder="find artifact..." value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" />
        </div>
    </aside>

    <!-- Start Here Rails -->
    <aside class="ds-start-here">
        <h2>start here</h2>

        <div class="ds-rail">
            <h3>essential artifacts</h3>
            <div class="ds-rail-items">
                <?php
                $essential_posts = get_posts(array(
                    'meta_key' => 'canonical',
                    'meta_value' => 'true',
                    'numberposts' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                if (empty($essential_posts)) {
                    $essential_posts = get_posts(array('numberposts' => 3, 'orderby' => 'date', 'order' => 'DESC'));
                }

                foreach ($essential_posts as $post) {
                    setup_postdata($post);
                    $artifact_id = 'EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT);
                    echo '<div class="ds-rail-item">';
                    echo '<div class="ds-rail-glyph">📜</div>';
                    echo '<div class="ds-rail-content">';
                    echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                    echo '<div class="ds-rail-meta">' . $artifact_id . ' • ' . human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago</div>';
                    echo '</div></div>';
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>

        <div class="ds-rail">
            <h3>latest canon</h3>
            <div class="ds-rail-items">
                <?php
                $canon_posts = get_posts(array(
                    'meta_key' => 'canonical',
                    'meta_value' => 'true',
                    'numberposts' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                foreach ($canon_posts as $post) {
                    setup_postdata($post);
                    $artifact_id = 'EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT);
                    echo '<div class="ds-rail-item">';
                    echo '<div class="ds-rail-glyph">⚔️</div>';
                    echo '<div class="ds-rail-content">';
                    echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                    echo '<div class="ds-rail-meta">' . $artifact_id . ' • canon</div>';
                    echo '</div></div>';
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>

        <div class="ds-rail">
            <h3>active quests</h3>
            <div class="ds-rail-items">
                <?php
                $active_posts = get_posts(array(
                    'meta_key' => 'artifact_state',
                    'meta_value' => 'active',
                    'numberposts' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                if (empty($active_posts)) {
                    $active_posts = get_posts(array('numberposts' => 3, 'orderby' => 'date', 'order' => 'ASC'));
                }

                foreach ($active_posts as $post) {
                    setup_postdata($post);
                    $artifact_id = 'EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT);
                    echo '<div class="ds-rail-item">';
                    echo '<div class="ds-rail-glyph">🎯</div>';
                    echo '<div class="ds-rail-content">';
                    echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                    echo '<div class="ds-rail-meta">' . $artifact_id . ' • active</div>';
                    echo '</div></div>';
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </aside>

    <!-- Archive Feed -->
    <main class="ds-feed">
        <header class="ds-feed__head" id="latest">
            <h2>archive feed</h2>
            <p>sorted by newest world state.</p>
        </header>

        <?php
        // Build WP_Query based on filters
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => 12,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );

        // Apply filters
        if ($current_type) {
            $query_args['meta_query'] = array(
                array(
                    'key' => 'artifact_type',
                    'value' => $current_type,
                    'compare' => '='
                )
            );
        }

        if ($current_questline) {
            $query_args['category_name'] = $current_questline;
        }

        if ($current_state) {
            $query_args['meta_query'][] = array(
                'key' => 'artifact_state',
                'value' => $current_state,
                'compare' => '='
            );
        }

        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $query_args['s'] = sanitize_text_field($_GET['s']);
        }

        $archive_query = new WP_Query($query_args);
        $total_reading_time = 0;

        if ($archive_query->have_posts()) :
            while ($archive_query->have_posts()) : $archive_query->the_post();

                // Calculate reading time
                $content = get_the_content();
                $word_count = str_word_count(strip_tags($content));
                $reading_time_minutes = ceil($word_count / 200);
                $total_reading_time += $reading_time_minutes;

                // Get artifact metadata
                $artifact_type = get_post_meta(get_the_ID(), 'artifact_type', true) ?: 'episode';
                $artifact_state = get_post_meta(get_the_ID(), 'artifact_state', true) ?: 'active';
                $questline = get_the_category()[0]->name ?? 'General';
                $artifact_id = 'EP-' . str_pad(get_the_ID(), 4, '0', STR_PAD_LEFT);

                // Type icons
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
                            <span>questline: <?php echo esc_html($questline); ?></span>
                            <span>era: <?php echo date('Y', get_the_time('U')); ?></span>
                            <span>state: <?php echo $artifact_state; ?></span>
                            <span>updated: <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</span>
                        </div>

                        <div class="ds-actions">
                            <a class="ds-btn ds-btn--primary" href="<?php the_permalink(); ?>">read artifact</a>
                            <a class="ds-btn" href="?questline=<?php echo esc_attr(get_the_category()[0]->slug ?? ''); ?>">view questline</a>
                        </div>
                    </div>
                </article>

            <?php
            endwhile;
            wp_reset_postdata();
        else :
            ?>
            <div class="ds-empty">
                <div class="ds-empty-icon">📚</div>
                <h3>No artifacts found</h3>
                <p>The archive contains no entries matching your current filters.</p>
                <a class="ds-btn ds-btn--primary" href="<?php echo esc_url(remove_query_arg(array('type', 'questline', 'state', 's'))); ?>">clear filters</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- World Intel Sidebar -->
    <aside class="ds-intel">
        <h2>world intel</h2>

        <!-- Email Capture -->
        <div class="ds-panel ds-capture">
            <h3>join the archive</h3>
            <p>new canon + new tools when they drop.</p>
            <form class="ds-capture-form" action="#" method="post">
                <input type="email" name="email" placeholder="your.email@example.com" required class="ds-capture-input">
                <button type="submit" class="ds-btn ds-btn--primary ds-capture-submit">join archive</button>
            </form>
        </div>

        <div class="ds-panel">
            <h3>active questlines</h3>
            <ul>
                <?php
                $active_questlines = get_categories(array(
                    'hide_empty' => true,
                    'number' => 5,
                    'orderby' => 'count',
                    'order' => 'DESC'
                ));
                foreach ($active_questlines as $questline) {
                    echo '<li><a href="?questline=' . $questline->slug . '">' . esc_html($questline->name) . '</a> <span>(' . $questline->count . ')</span></li>';
                }
                ?>
            </ul>
        </div>

        <div class="ds-panel">
            <h3>recent canon</h3>
            <ul>
                <?php
                $recent_canon = get_posts(array(
                    'meta_key' => 'canonical',
                    'meta_value' => 'true',
                    'numberposts' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                foreach ($recent_canon as $canon_post) {
                    echo '<li><a href="' . get_permalink($canon_post) . '">' . esc_html($canon_post->post_title) . '</a></li>';
                }
                ?>
            </ul>
        </div>

        <div class="ds-panel">
            <h3>unresolved loops</h3>
            <ul>
                <?php
                $unresolved = get_posts(array(
                    'meta_key' => 'artifact_state',
                    'meta_value' => 'active',
                    'numberposts' => 5,
                    'orderby' => 'date',
                    'order' => 'ASC'
                ));
                foreach ($unresolved as $unresolved_post) {
                    echo '<li><a href="' . get_permalink($unresolved_post) . '">' . esc_html($unresolved_post->post_title) . '</a></li>';
                }
                ?>
            </ul>
        </div>

        <div class="ds-panel">
            <h3>top artifacts</h3>
            <ul>
                <?php
                $top_artifacts = get_posts(array(
                    'numberposts' => 5,
                    'orderby' => 'comment_count',
                    'order' => 'DESC'
                ));
                foreach ($top_artifacts as $artifact) {
                    echo '<li><a href="' . get_permalink($artifact) . '">' . esc_html($artifact->post_title) . '</a></li>';
                }
                ?>
            </ul>
        </div>

        <!-- Builder Proof -->
        <div class="ds-panel ds-builder-proof">
            <h3>builder proof</h3>
            <div class="ds-proof-stats">
                <div class="ds-proof-stat">
                    <span class="ds-proof-number">6k+</span>
                    <span class="ds-proof-label">commits last year</span>
                </div>
                <div class="ds-proof-links">
                    <a href="https://github.com/Victor-Dixon" class="ds-proof-link" target="_blank">github</a>
                    <a href="<?php echo home_url('/systems/'); ?>" class="ds-proof-link">systems</a>
                </div>
            </div>
        </div>
    </aside>
</section>

<!-- World Archive JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.ds-input');
    const searchForm = document.querySelector('.ds-filter:last-child');

    // Search functionality - redirect to search URL
    if (searchInput && searchForm) {
        let searchTimeout;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();

            if (searchTerm.length >= 3) {
                searchTimeout = setTimeout(() => {
                    const searchUrl = new URL(window.location);
                    searchUrl.searchParams.set('s', searchTerm);
                    // Clear other filters when searching
                    searchUrl.searchParams.delete('type');
                    searchUrl.searchParams.delete('questline');
                    searchUrl.searchParams.delete('state');
                    window.location.href = searchUrl.toString();
                }, 1000); // 1 second delay
            }
        });

        // Handle Enter key for immediate search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = e.target.value.trim();
                if (searchTerm) {
                    const searchUrl = new URL(window.location);
                    searchUrl.searchParams.set('s', searchTerm);
                    searchUrl.searchParams.delete('type');
                    searchUrl.searchParams.delete('questline');
                    searchUrl.searchParams.delete('state');
                    window.location.href = searchUrl.toString();
                }
            }
        });
    }

    // Email capture form handling
    const captureForm = document.querySelector('.ds-capture-form');
    if (captureForm) {
        captureForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;

            // For now, just show a success message
            // In production, this would submit to your email service
            const submitBtn = this.querySelector('.ds-capture-submit');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'joined!';
            submitBtn.disabled = true;

            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                this.reset();
            }, 2000);
        });
    }
});
</script>

<?php get_footer(); ?>