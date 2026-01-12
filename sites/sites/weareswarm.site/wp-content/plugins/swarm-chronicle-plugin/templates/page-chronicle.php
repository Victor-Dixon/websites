<?php
/**
 * Template Name: Swarm Chronicle
 * Description: Displays the complete Swarm operating chronicle
 */

get_header();

// Get query parameters
$current_view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'chronicle';
$current_agent = isset($_GET['agent']) ? sanitize_text_field($_GET['agent']) : 'all';
$current_period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'all';
?>

<div class="swarm-chronicle-page">
    <div class="chronicle-header">
        <h1><?php _e('Swarm Operating Chronicle', 'swarm-chronicle'); ?></h1>
        <p class="chronicle-subtitle">
            <?php _e('Real-time insights into Swarm intelligence operations, accomplishments, and mission progress', 'swarm-chronicle'); ?>
        </p>
    </div>

    <!-- Navigation Tabs -->
    <div class="chronicle-nav">
        <nav class="nav-tabs">
            <a href="?view=chronicle" class="nav-tab <?php echo $current_view === 'chronicle' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Chronicle', 'swarm-chronicle'); ?>
            </a>
            <a href="?view=missions" class="nav-tab <?php echo $current_view === 'missions' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Active Missions', 'swarm-chronicle'); ?>
            </a>
            <a href="?view=accomplishments" class="nav-tab <?php echo $current_view === 'accomplishments' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Accomplishments', 'swarm-chronicle'); ?>
            </a>
            <a href="?view=state" class="nav-tab <?php echo $current_view === 'state' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Project State', 'swarm-chronicle'); ?>
            </a>
        </nav>

        <!-- Filters -->
        <div class="chronicle-filters">
            <form method="get" class="filter-form">
                <input type="hidden" name="view" value="<?php echo esc_attr($current_view); ?>">

                <?php if (in_array($current_view, ['chronicle', 'missions', 'accomplishments'])): ?>
                <div class="filter-group">
                    <label for="agent-filter"><?php _e('Agent:', 'swarm-chronicle'); ?></label>
                    <select name="agent" id="agent-filter">
                        <option value="all" <?php selected($current_agent, 'all'); ?>><?php _e('All Agents', 'swarm-chronicle'); ?></option>
                        <option value="Agent-1" <?php selected($current_agent, 'Agent-1'); ?>>Agent-1</option>
                        <option value="Agent-2" <?php selected($current_agent, 'Agent-2'); ?>>Agent-2</option>
                        <option value="Agent-3" <?php selected($current_agent, 'Agent-3'); ?>>Agent-3</option>
                        <option value="Agent-4" <?php selected($current_agent, 'Agent-4'); ?>>Agent-4</option>
                        <option value="Agent-5" <?php selected($current_agent, 'Agent-5'); ?>>Agent-5</option>
                        <option value="Agent-6" <?php selected($current_agent, 'Agent-6'); ?>>Agent-6</option>
                        <option value="Agent-7" <?php selected($current_agent, 'Agent-7'); ?>>Agent-7</option>
                        <option value="Agent-8" <?php selected($current_agent, 'Agent-8'); ?>>Agent-8</option>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ($current_view === 'accomplishments'): ?>
                <div class="filter-group">
                    <label for="period-filter"><?php _e('Period:', 'swarm-chronicle'); ?></label>
                    <select name="period" id="period-filter">
                        <option value="all" <?php selected($current_period, 'all'); ?>><?php _e('All Time', 'swarm-chronicle'); ?></option>
                        <option value="week" <?php selected($current_period, 'week'); ?>><?php _e('This Week', 'swarm-chronicle'); ?></option>
                        <option value="month" <?php selected($current_period, 'month'); ?>><?php _e('This Month', 'swarm-chronicle'); ?></option>
                        <option value="quarter" <?php selected($current_period, 'quarter'); ?>><?php _e('This Quarter', 'swarm-chronicle'); ?></option>
                    </select>
                </div>
                <?php endif; ?>

                <button type="submit" class="filter-submit"><?php _e('Filter', 'swarm-chronicle'); ?></button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="chronicle-content-wrapper">
        <?php
        switch ($current_view) {
            case 'missions':
                echo do_shortcode('[swarm_missions status="active" limit="50" agent="' . esc_attr($current_agent) . '"]');
                break;

            case 'accomplishments':
                echo do_shortcode('[swarm_accomplishments period="' . esc_attr($current_period) . '" limit="50" agent="' . esc_attr($current_agent) . '"]');
                break;

            case 'state':
                echo do_shortcode('[swarm_project_state]');
                break;

            default:
                echo do_shortcode('[swarm_chronicle type="overview" limit="50" agent="' . esc_attr($current_agent) . '"]');
                break;
        }
        ?>
    </div>

    <!-- Footer Stats -->
    <div class="chronicle-footer">
        <div class="footer-stats">
            <?php
            $api = new Chronicle_API();
            $stats = $api->get_chronicle_data(array('limit' => 1));
            if ($stats) {
                echo '<span class="stat-item">📊 ' . esc_html($stats['total_tasks']) . ' Total Missions</span>';
                echo '<span class="stat-item">✅ ' . esc_html($stats['completed_tasks']) . ' Completed</span>';
                echo '<span class="stat-item">🚀 ' . esc_html($stats['active_agents']) . ' Active Agents</span>';
            }
            ?>
        </div>

        <div class="footer-meta">
            <p><?php _e('Last updated: ', 'swarm-chronicle'); ?><span id="last-updated"><?php echo esc_html(date('Y-m-d H:i:s')); ?></span></p>
            <p><?php _e('Data synchronized from Swarm systems', 'swarm-chronicle'); ?></p>
        </div>
    </div>
</div>

<style>
/* Page-specific styles */
.swarm-chronicle-page {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.chronicle-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
}

.chronicle-header h1 {
    margin: 0 0 15px 0;
    font-size: 3em;
    font-weight: 300;
}

.chronicle-subtitle {
    font-size: 1.2em;
    opacity: 0.9;
    margin: 0;
}

.chronicle-nav {
    margin-bottom: 30px;
}

.nav-tabs {
    display: flex;
    border-bottom: 1px solid #e1e5e9;
    margin-bottom: 20px;
}

.nav-tab {
    padding: 12px 24px;
    text-decoration: none;
    color: #666;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    font-weight: 500;
}

.nav-tab:hover {
    color: #667eea;
}

.nav-tab-active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.chronicle-filters {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #e1e5e9;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-group label {
    font-weight: 500;
    color: #333;
}

.filter-group select {
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.filter-submit {
    background: #667eea;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.2s;
}

.filter-submit:hover {
    background: #5a6fd8;
}

.chronicle-content-wrapper {
    min-height: 400px;
}

.chronicle-footer {
    margin-top: 40px;
    padding: 30px;
    background: #f8f9fa;
    border-radius: 8px;
    text-align: center;
}

.footer-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.stat-item {
    font-size: 1.1em;
    font-weight: 500;
    color: #333;
}

.footer-meta {
    color: #666;
    font-size: 0.9em;
}

.footer-meta p {
    margin: 5px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .chronicle-header h1 {
        font-size: 2em;
    }

    .nav-tabs {
        flex-direction: column;
    }

    .nav-tab {
        text-align: center;
    }

    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .footer-stats {
        flex-direction: column;
        gap: 15px;
    }
}
</style>

<script>
// Auto-refresh functionality
document.addEventListener('DOMContentLoaded', function() {
    let autoRefreshInterval;

    function updateLastRefresh() {
        const now = new Date();
        document.getElementById('last-updated').textContent = now.toISOString().slice(0, 19).replace('T', ' ');
    }

    // Update timestamp every minute
    setInterval(updateLastRefresh, 60000);

    // Optional: Auto-refresh content every 5 minutes
    if (typeof swarmChronicleAjax !== 'undefined') {
        autoRefreshInterval = setInterval(function() {
            // Trigger refresh if auto-refresh is enabled
            if (document.querySelector('.auto-refresh-toggle')?.checked) {
                location.reload();
            }
        }, 300000); // 5 minutes
    }
});
</script>

<?php get_footer(); ?>