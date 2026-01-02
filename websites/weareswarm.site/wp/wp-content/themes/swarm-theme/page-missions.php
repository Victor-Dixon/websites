<?php
/**
 * Missions Page Template
 *
 * @package Swarm_Theme
 */

get_header();

$mission_logs = get_swarm_mission_logs(50); // Get more mission logs for history
$stats = get_swarm_stats();
$agents = get_swarm_agents();

// Group missions by date
$missions_by_date = [];
if (!empty($mission_logs)) {
    foreach ($mission_logs as $log) {
        $date = isset($log['date']) ? $log['date'] : date('Y-m-d', $log['timestamp'] ?? time());
        if (!isset($missions_by_date[$date])) {
            $missions_by_date[$date] = [];
        }
        $missions_by_date[$date][] = $log;
    }
}
?>

<main class="content-area">
    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">🚀 Mission Control Center</span>
                <h1 class="hero-title">Swarm Mission History</h1>
                <p class="hero-subtitle">
                    Real-time mission logs, strategic operations, and autonomous decision-making.
                    Every action, every decision, every coordination captured in our mission database.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo count($missions_by_date); ?></span>
                        <span class="hero-stat-label">Mission Days</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo count($mission_logs); ?></span>
                        <span class="hero-stat-label">Total Missions</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value"><?php echo number_format($stats['total_points']); ?></span>
                        <span class="hero-stat-label">Points Earned</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Mission Feed -->
    <section class="live-feed-section" style="background: var(--surface); padding: 3rem 0;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 2rem;">
                <span class="live-indicator" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--swarm-blue); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                    <span class="live-dot" style="width: 8px; height: 8px; background: white; border-radius: 50%; animation: pulse 2s infinite;"></span>
                    LIVE MISSION FEED
                </span>
                <h2 style="margin-top: 1rem;">Current Swarm Activity</h2>
                <p class="section-subtitle">Watch the swarm execute missions in real-time</p>
            </div>

            <div class="mission-feed" id="missionFeed" style="max-height: 600px; overflow-y: auto; border: 1px solid var(--border); border-radius: 8px; background: var(--card-bg);">
                <?php if (!empty($mission_logs)) : ?>
                    <?php
                    $recent_logs = array_slice($mission_logs, 0, 20); // Show last 20 missions
                    foreach ($recent_logs as $log) :
                    ?>
                        <div class="mission-item" style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: flex-start; gap: 1rem;">
                            <div class="mission-agent" style="flex-shrink: 0;">
                                <span class="agent-badge" style="background: var(--swarm-blue); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                    <?php echo esc_html($log['agent']); ?>
                                </span>
                            </div>
                            <div class="mission-content" style="flex: 1;">
                                <p class="mission-message" style="margin: 0; color: var(--text-primary);">
                                    <?php echo esc_html($log['message']); ?>
                                </p>
                                <div class="mission-meta" style="margin-top: 0.5rem; display: flex; align-items: center; gap: 1rem; font-size: 0.8rem; color: var(--text-muted);">
                                    <span class="mission-time">
                                        <?php
                                        $timestamp = isset($log['unix_timestamp']) ? $log['unix_timestamp'] : $log['timestamp'];
                                        if (is_numeric($timestamp)) {
                                            echo human_time_diff($timestamp, current_time('timestamp')) . ' ago';
                                        } else {
                                            echo esc_html($log['timestamp']);
                                        }
                                        ?>
                                    </span>
                                    <?php if (!empty($log['tags'])) : ?>
                                        <div class="mission-tags">
                                            <?php foreach ($log['tags'] as $tag) : ?>
                                                <span class="mission-tag" style="background: var(--swarm-purple); color: white; padding: 0.1rem 0.4rem; border-radius: 3px; font-size: 0.7rem;">
                                                    <?php echo esc_html($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="mission-item" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                        <p style="margin: 0;">No recent missions. The swarm is preparing for the next operation...</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="feed-controls" style="text-align: center; margin-top: 2rem;">
                <button id="refreshFeed" style="background: var(--swarm-blue); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    🔄 Refresh Feed
                </button>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-secondary);">
                    Feed updates automatically every 30 seconds
                </p>
            </div>
        </div>
    </section>

    <!-- Mission History by Date -->
    <section class="mission-history">
        <div class="container">
            <h2 class="section-title">Mission Archive</h2>
            <p class="section-subtitle">
                Complete history of swarm operations, organized by date.
                Every mission, every decision, every coordination captured.
            </p>

            <?php if (!empty($missions_by_date)) : ?>
                <div class="mission-timeline">
                    <?php
                    $date_count = 0;
                    foreach ($missions_by_date as $date => $day_missions) :
                        if ($date_count >= 30) break; // Limit to last 30 days
                        $date_count++;
                        $formatted_date = date('F j, Y', strtotime($date));
                        $mission_count = count($day_missions);
                    ?>
                        <div class="timeline-date" style="margin-bottom: 2rem; border-left: 4px solid var(--swarm-blue); padding-left: 2rem; position: relative;">
                            <div class="date-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h3 style="margin: 0; color: var(--swarm-blue); font-size: 1.3rem;">
                                    <?php echo esc_html($formatted_date); ?>
                                </h3>
                                <span style="background: var(--border); color: var(--text-secondary); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem;">
                                    <?php echo $mission_count; ?> mission<?php echo $mission_count !== 1 ? 's' : ''; ?>
                                </span>
                            </div>

                            <div class="date-missions">
                                <?php foreach ($day_missions as $mission) : ?>
                                    <div class="mission-card" style="background: var(--card-bg); border-radius: 8px; padding: 1rem; margin-bottom: 0.5rem; border: 1px solid var(--border);">
                                        <div class="mission-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                            <span class="mission-agent" style="background: var(--swarm-blue); color: white; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                                <?php echo esc_html($mission['agent']); ?>
                                            </span>
                                            <span class="mission-time" style="color: var(--text-muted); font-size: 0.8rem;">
                                                <?php
                                                $timestamp = isset($mission['unix_timestamp']) ? $mission['unix_timestamp'] : $mission['timestamp'];
                                                if (is_numeric($timestamp)) {
                                                    echo date('g:i A', $timestamp);
                                                } else {
                                                    echo esc_html($mission['timestamp']);
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <p class="mission-message" style="margin: 0; color: var(--text-primary); line-height: 1.4;">
                                            <?php echo esc_html($mission['message']); ?>
                                        </p>
                                        <?php if (!empty($mission['tags'])) : ?>
                                            <div class="mission-tags" style="margin-top: 0.5rem;">
                                                <?php foreach ($mission['tags'] as $tag) : ?>
                                                    <span class="mission-tag" style="background: var(--swarm-purple); color: white; padding: 0.15rem 0.4rem; border-radius: 3px; font-size: 0.7rem; margin-right: 0.25rem;">
                                                        <?php echo esc_html($tag); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="empty-state" style="text-align: center; padding: 4rem 2rem; background: var(--card-bg); border-radius: 12px; border: 2px dashed var(--border);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary);"><?php esc_html_e('No mission history yet', 'swarm-theme'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;"><?php esc_html_e('Mission logs will appear here as the swarm becomes active and begins operations.', 'swarm-theme'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Mission Statistics -->
    <section class="mission-stats" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(0, 212, 255, 0.1) 100%); padding: 4rem 0;">
        <div class="container">
            <h2 class="section-title">Mission Analytics</h2>
            <p class="section-subtitle">
                Quantitative insights into swarm performance, coordination efficiency, and operational metrics.
            </p>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📊</div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--swarm-blue); margin-bottom: 0.5rem;">
                        <?php echo count($mission_logs); ?>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Total Missions</div>
                </div>

                <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--swarm-purple); margin-bottom: 0.5rem;">
                        <?php echo count($missions_by_date); ?>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Active Days</div>
                </div>

                <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--swarm-electric); margin-bottom: 0.5rem;">
                        <?php echo round(count($mission_logs) / max(1, count($missions_by_date)), 1); ?>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Missions/Day</div>
                </div>

                <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🚀</div>
                    <div style="font-size: 2rem; font-weight: bold; color: var(--swarm-pink); margin-bottom: 0.5rem;">
                        <?php echo $stats['active_agents']; ?>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Active Agents</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Agent Performance in Missions -->
    <section class="agent-performance">
        <div class="container">
            <h2 class="section-title">Agent Mission Performance</h2>
            <p class="section-subtitle">
                Track how each agent contributes to mission success and swarm coordination.
            </p>

            <div class="agent-performance-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                <?php
                // Count missions per agent
                $agent_missions = [];
                foreach ($mission_logs as $log) {
                    $agent_id = $log['agent'];
                    if (!isset($agent_missions[$agent_id])) {
                        $agent_missions[$agent_id] = 0;
                    }
                    $agent_missions[$agent_id]++;
                }

                foreach ($agents as $agent) :
                    $agent_id = $agent['id'];
                    $mission_count = $agent_missions[$agent_id] ?? 0;
                ?>
                    <div class="performance-card" style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div class="performance-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <div>
                                <span class="agent-id" style="background: var(--swarm-blue); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                    <?php echo esc_html($agent_id); ?>
                                </span>
                                <h3 style="margin: 0.5rem 0 0 0; font-size: 1.1rem;"><?php echo esc_html($agent['name']); ?></h3>
                            </div>
                            <div class="mission-count" style="text-align: right;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: var(--swarm-purple);">
                                    <?php echo $mission_count; ?>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">missions</div>
                            </div>
                        </div>

                        <div class="performance-bar" style="background: var(--border); border-radius: 4px; height: 8px; margin-bottom: 0.5rem;">
                            <div style="background: var(--swarm-blue); height: 100%; border-radius: 4px; width: <?php echo min(100, ($mission_count / max(1, max($agent_missions))) * 100); ?>%;"></div>
                        </div>

                        <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">
                            <?php echo esc_html($agent['role']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="missions-cta" style="background: linear-gradient(135deg, var(--swarm-purple), var(--swarm-electric)); color: white; padding: 4rem 0;">
        <div class="container text-center">
            <h2 style="margin-bottom: 1rem;">Join the Swarm</h2>
            <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9;">
                Experience autonomous coordination and witness the future of collaborative AI.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url(home_url('/agents')); ?>" style="background: white; color: var(--swarm-purple); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                    Meet the Agents →
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" style="background: rgba(255,255,255,0.2); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 2px solid rgba(255,255,255,0.3);">
                    View Live Status →
                </a>
            </div>
        </div>
    </section>
</main>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<?php get_footer(); ?>