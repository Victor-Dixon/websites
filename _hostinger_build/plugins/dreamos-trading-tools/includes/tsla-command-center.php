<?php
/**
 * TSLA Command Center snapshot shortcode.
 *
 * Displays a private/cache-fed TSLA daytrading command center snapshot.
 * This module intentionally does not fetch provider APIs or store provider keys.
 *
 * Shortcode:
 *   [freeride_tsla_command_center]
 *
 * Snapshot source:
 *   wp-content/uploads/freerideinvestor/tsla-command-center.json
 *
 * Expected JSON shape:
 * {
 *   "symbol": "TSLA",
 *   "generated_at": "2026-06-01T13:30:00Z",
 *   "status": "pass",
 *   "freshness": "fresh",
 *   "headline": "TSLA command center ready",
 *   "bias": "neutral",
 *   "opening_range": "watching",
 *   "vwap_state": "above",
 *   "churn_risk": "medium",
 *   "no_trade_windows": ["09:30-09:40 ET"],
 *   "checklist": ["Opening range marked", "VWAP checked"],
 *   "insight": "Avoid re-entry churn until confirmation."
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

function dtt_tsla_command_center_snapshot_path(): string {
    $upload = wp_upload_dir(null, false);
    $base = isset($upload['basedir']) ? $upload['basedir'] : WP_CONTENT_DIR . '/uploads';
    return trailingslashit($base) . 'freerideinvestor/tsla-command-center.json';
}

function dtt_tsla_command_center_default_snapshot(): array {
    return [
        'symbol' => 'TSLA',
        'generated_at' => '',
        'status' => 'warn',
        'freshness' => 'missing',
        'headline' => 'TSLA command center snapshot missing',
        'bias' => 'not available',
        'opening_range' => 'not available',
        'vwap_state' => 'not available',
        'churn_risk' => 'unknown',
        'no_trade_windows' => [],
        'checklist' => [
            'Export a fresh snapshot from DreamVault or DreamTradeData.',
            'Upload it to wp-content/uploads/freerideinvestor/tsla-command-center.json.',
        ],
        'insight' => 'FreeRideInvestor displays derived command-center data. Raw provider collection stays private.',
    ];
}

function dtt_tsla_command_center_load_snapshot(): array {
    $path = dtt_tsla_command_center_snapshot_path();

    if (!is_readable($path)) {
        return dtt_tsla_command_center_default_snapshot();
    }

    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return dtt_tsla_command_center_default_snapshot();
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $fallback = dtt_tsla_command_center_default_snapshot();
        $fallback['headline'] = 'TSLA command center snapshot is invalid JSON';
        $fallback['freshness'] = 'invalid';
        return $fallback;
    }

    return array_merge(dtt_tsla_command_center_default_snapshot(), $data);
}

function dtt_tsla_command_center_badge_class(string $status): string {
    $status = strtolower($status);
    if ($status === 'pass' || $status === 'fresh') {
        return 'dtt-badge-pass';
    }
    if ($status === 'warn' || $status === 'stale') {
        return 'dtt-badge-warn';
    }
    return 'dtt-badge-fail';
}

function dtt_tsla_command_center_render_list(array $items): string {
    if (empty($items)) {
        return '<p class="dtt-muted">None listed.</p>';
    }

    $html = '<ul class="dtt-list">';
    foreach ($items as $item) {
        $html .= '<li>' . esc_html((string) $item) . '</li>';
    }
    $html .= '</ul>';

    return $html;
}

function dtt_tsla_command_center_shortcode($atts = []): string {
    $snapshot = dtt_tsla_command_center_load_snapshot();

    $symbol = esc_html((string) $snapshot['symbol']);
    $status = esc_html((string) $snapshot['status']);
    $freshness = esc_html((string) $snapshot['freshness']);
    $badge_class = esc_attr(dtt_tsla_command_center_badge_class((string) $snapshot['status']));

    $generated = !empty($snapshot['generated_at'])
        ? esc_html((string) $snapshot['generated_at'])
        : 'not generated yet';

    ob_start();
    ?>
    <section class="dtt-command-center">
        <style>
            .dtt-command-center {
                border: 1px solid #d7dee8;
                border-radius: 18px;
                padding: 24px;
                margin: 24px 0;
                background: #ffffff;
                box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            }
            .dtt-command-center h2 {
                margin: 0 0 8px;
                font-size: 1.7rem;
            }
            .dtt-command-center .dtt-subtitle {
                margin: 0 0 18px;
                color: #475569;
            }
            .dtt-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 14px;
                margin: 18px 0;
            }
            .dtt-card {
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 14px;
                background: #f8fafc;
            }
            .dtt-label {
                display: block;
                font-size: 0.78rem;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                margin-bottom: 6px;
            }
            .dtt-value {
                font-size: 1.1rem;
                font-weight: 700;
                color: #0f172a;
            }
            .dtt-badge {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 999px;
                font-size: 0.8rem;
                font-weight: 700;
            }
            .dtt-badge-pass {
                background: #dcfce7;
                color: #166534;
            }
            .dtt-badge-warn {
                background: #fef3c7;
                color: #92400e;
            }
            .dtt-badge-fail {
                background: #fee2e2;
                color: #991b1b;
            }
            .dtt-list {
                margin: 8px 0 0 18px;
            }
            .dtt-muted {
                color: #64748b;
            }
            .dtt-insight {
                border-left: 4px solid #0f172a;
                padding: 10px 14px;
                background: #f1f5f9;
                border-radius: 10px;
                margin-top: 16px;
            }
        </style>

        <h2><?php echo $symbol; ?> Daytrading Command Center</h2>
        <p class="dtt-subtitle">
            Snapshot-fed trading discipline dashboard. Generated: <?php echo $generated; ?>
        </p>

        <p>
            <span class="dtt-badge <?php echo $badge_class; ?>">
                Status: <?php echo $status; ?> / <?php echo $freshness; ?>
            </span>
        </p>

        <div class="dtt-grid">
            <div class="dtt-card">
                <span class="dtt-label">Headline</span>
                <span class="dtt-value"><?php echo esc_html((string) $snapshot['headline']); ?></span>
            </div>
            <div class="dtt-card">
                <span class="dtt-label">Bias</span>
                <span class="dtt-value"><?php echo esc_html((string) $snapshot['bias']); ?></span>
            </div>
            <div class="dtt-card">
                <span class="dtt-label">Opening Range</span>
                <span class="dtt-value"><?php echo esc_html((string) $snapshot['opening_range']); ?></span>
            </div>
            <div class="dtt-card">
                <span class="dtt-label">VWAP State</span>
                <span class="dtt-value"><?php echo esc_html((string) $snapshot['vwap_state']); ?></span>
            </div>
            <div class="dtt-card">
                <span class="dtt-label">Churn Risk</span>
                <span class="dtt-value"><?php echo esc_html((string) $snapshot['churn_risk']); ?></span>
            </div>
        </div>

        <div class="dtt-grid">
            <div class="dtt-card">
                <span class="dtt-label">No-Trade Windows</span>
                <?php echo dtt_tsla_command_center_render_list((array) $snapshot['no_trade_windows']); ?>
            </div>
            <div class="dtt-card">
                <span class="dtt-label">Checklist</span>
                <?php echo dtt_tsla_command_center_render_list((array) $snapshot['checklist']); ?>
            </div>
        </div>

        <div class="dtt-insight">
            <strong>DreamOS Insight:</strong>
            <?php echo esc_html((string) $snapshot['insight']); ?>
        </div>
    </section>
    <?php

    return (string) ob_get_clean();
}

add_shortcode('freeride_tsla_command_center', 'dtt_tsla_command_center_shortcode');
