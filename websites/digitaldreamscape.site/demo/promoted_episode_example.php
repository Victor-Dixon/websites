<?php
/**
 * DEMONSTRATION: How Devlog Promotion Works
 *
 * This shows what happens when we run:
 * php promote_artifacts.php devlog devlogs/2026-01-03_agent_cellphone_cleanup.md
 */

// Simulated promotion result
$promotion_demo = [
    'success' => true,
    'post_id' => 145, // EP-145
    'artifact_id' => 'EP-145',
    'metadata' => [
        'artifact_type' => 'episode',
        'questline' => 'technical-debt',
        'artifact_state' => 'active',
        'era' => '2026',
        'source_system' => 'devlog',
        'internal_source' => 'devlogs/2026-01-03_agent_cellphone_cleanup.md',
        'canon_weight' => 1,
        'canonical' => 'false'
    ],
    'content' => [
        'title' => 'The Day We Killed 1,000 Duplicate Files',
        'excerpt' => 'Our digital garden had become overgrown. Files were scattered like weeds across the filesystem, with duplicates hiding in every shadow. The Agent-8 cellphone cleanup operation had revealed the tip of a much larger iceberg.',
        'full_content' => 'The complete devlog content would be here...'
    ]
];

/**
 * What the promoted episode looks like in the World Archive
 */
$archive_display = [
    'card_html' => '
    <article class="ds-card">
        <div class="ds-card__rail">
            <div class="ds-glyph" style="background: radial-gradient(circle at 30% 30%, rgba(184,107,255,.35), rgba(77,227,255,.12));">
                🎭
            </div>
            <div class="ds-id">EP-145</div>
        </div>

        <div class="ds-card__body">
            <div class="ds-badges">
                <span class="ds-badge is-episode">episode</span>
            </div>

            <h3><a href="/blog/the-day-we-killed-1000-duplicate-files">The Day We Killed 1,000 Duplicate Files</a></h3>
            <p>Our digital garden had become overgrown. Files were scattered like weeds across the filesystem, with duplicates hiding in every shadow. The Agent-8 cellphone cleanup operation had revealed the tip of a much larger iceberg.</p>

            <div class="ds-meta">
                <span>questline: technical-debt</span>
                <span>era: 2026</span>
                <span>state: active</span>
                <span>updated: 2 hours ago</span>
            </div>

            <div class="ds-actions">
                <a class="ds-btn ds-btn--primary" href="/blog/the-day-we-killed-1000-duplicate-files">read artifact</a>
                <a class="ds-btn" href="?questline=technical-debt">view questline</a>
            </div>
        </div>
    </article>',

    'single_post_html' => '
    <article class="ds-artifact ds-episode">
        <header class="ds-artifact-header">
            <div class="ds-artifact-meta">
                <span class="artifact-type">🎭 Episode EP-145</span>
                <span class="artifact-questline">Questline: technical-debt</span>
                <span class="artifact-state artifact-active">Active</span>
                <span class="artifact-era">Era: 2026</span>
                <span class="artifact-source">Source: devlog</span>
            </div>
            <h1>The Day We Killed 1,000 Duplicate Files</h1>
        </header>

        <div class="ds-artifact-content">
            <!-- Full devlog content would appear here -->
            <p>Our digital garden had become overgrown...</p>
        </div>

        <footer class="ds-artifact-footer">
            <div class="artifact-navigation">
                <a href="?questline=technical-debt">← View Questline</a>
                <a href="/blog">↑ Back to Archive</a>
                <a href="?type=episode">More Episodes →</a>
            </div>
        </footer>
    </article>'
];

/**
 * What the promotion command output would show
 */
$command_output = [
    'command' => 'php promote_artifacts.php devlog devlogs/2026-01-03_agent_cellphone_cleanup.md',
    'output' => [
        '🔍 Processing devlog: devlogs/2026-01-03_agent_cellphone_cleanup.md',
        '✅ Promoted devlog to episode',
        '   Title: The Day We Killed 1,000 Duplicate Files',
        '   Questline: technical-debt',
        '   URL: https://digitaldreamscape.site/blog/the-day-we-killed-1000-duplicate-files',
        '',
        '📊 World Archive Status Updated:',
        '   Episodes: +1 (now 12)',
        '   Questlines: technical-debt (2/5 complete)',
        '   Active Artifacts: +1 (now 8)'
    ]
];

/**
 * How this affects questline tracking
 */
$questline_update = [
    'questline' => 'technical-debt',
    'before_promotion' => [
        'total_artifacts' => 4,
        'resolved_count' => 1,
        'progress' => '1/4'
    ],
    'after_promotion' => [
        'total_artifacts' => 5,
        'resolved_count' => 2,
        'progress' => '2/5',
        'latest_artifact' => 'EP-145: The Day We Killed 1,000 Duplicate Files'
    ]
];

echo "Devlog promotion demonstration created!\n";
echo "Run: php promote_artifacts.php devlog devlogs/2026-01-03_agent_cellphone_cleanup.md\n";
?>