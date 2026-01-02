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

    <!-- Mission Control Center Hero -->
    <section class="mission-control-hero" style="position: relative; overflow: hidden;">
        <div class="hero-bg-pattern" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); opacity: 0.1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="hero-content" style="text-align: center; padding: 4rem 0;">
                <div class="mission-status-indicator" style="display: inline-flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, var(--swarm-blue), var(--swarm-purple)); color: white; padding: 1rem 2rem; border-radius: 50px; font-weight: 600; box-shadow: 0 8px 16px rgba(0, 212, 255, 0.3); margin-bottom: 2rem;">
                    <div class="status-pulse" style="width: 12px; height: 12px; background: white; border-radius: 50%; animation: pulse-glow 2s infinite;"></div>
                    <span>🛰️ MISSION CONTROL ACTIVE</span>
                    <div class="status-pulse" style="width: 12px; height: 12px; background: white; border-radius: 50%; animation: pulse-glow 2s infinite 1s;"></div>
                </div>

                <h1 style="font-size: 3.5rem; margin-bottom: 1rem; background: linear-gradient(135deg, var(--swarm-blue), var(--swarm-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 800;">
                    Swarm Mission Control
                </h1>
                <p class="hero-subtitle" style="font-size: 1.3rem; color: var(--text-secondary); max-width: 800px; margin: 0 auto 3rem;">
                    Real-time mission intelligence, autonomous operations, and swarm coordination tracking.
                    Witness the future of collaborative AI in action.
                </p>

                <!-- Live Stats Dashboard -->
                <div class="live-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 2rem; max-width: 600px; margin: 0 auto 3rem;">
                    <div class="stat-card" style="background: var(--surface); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div style="font-size: 2rem; font-weight: bold; color: var(--swarm-blue); margin-bottom: 0.5rem;"><?php echo count($mission_logs); ?></div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Total Missions</div>
                    </div>
                    <div class="stat-card" style="background: var(--surface); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div style="font-size: 2rem; font-weight: bold; color: var(--swarm-purple); margin-bottom: 0.5rem;"><?php echo $stats['active_agents']; ?></div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Active Agents</div>
                    </div>
                    <div class="stat-card" style="background: var(--surface); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div style="font-size: 2rem; font-weight: bold; color: var(--swarm-electric); margin-bottom: 0.5rem;"><?php echo number_format($stats['total_points']); ?></div>
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Points Earned</div>
                    </div>
                </div>

                <div class="hero-actions" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="#live-feed" style="background: linear-gradient(135deg, var(--swarm-blue), var(--swarm-purple)); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 8px rgba(0, 212, 255, 0.3); display: inline-block;">
                        📡 View Live Feed
                    </a>
                    <a href="#mission-history" style="background: var(--surface); color: var(--text-primary); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid var(--border); display: inline-block;">
                        📚 Mission Archive
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Mission Feed -->
    <section id="live-feed" class="live-feed-section" style="background: var(--surface); padding: 4rem 0;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 3rem;">
                <div class="section-badge" style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(0, 212, 255, 0.1); color: var(--swarm-blue); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem;">
                    <div class="live-indicator" style="width: 6px; height: 6px; background: var(--swarm-blue); border-radius: 50%; animation: blink 1.5s infinite;"></div>
                    REAL-TIME ACTIVITY
                </div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-primary);">Live Mission Feed</h2>
                <p class="section-subtitle" style="font-size: 1.1rem; color: var(--text-secondary);">Watch autonomous agents coordinate and execute missions in real-time</p>
            </div>

            <!-- Feed Controls -->
            <div class="feed-controls" style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <button id="refreshFeed" style="background: var(--swarm-blue); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🔄</span>
                    <span>Refresh Feed</span>
                </button>
                <div class="feed-status" style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary); font-size: 0.9rem;">
                    <div class="status-dot" style="width: 8px; height: 8px; background: var(--swarm-green); border-radius: 50%;"></div>
                    <span>Auto-updating every 30 seconds</span>
                </div>
            </div>

            <!-- Mission Feed Container -->
            <div class="mission-feed-container" style="background: var(--card-bg); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                <div class="feed-header" style="padding: 1.5rem 2rem; background: linear-gradient(135deg, var(--swarm-blue), var(--swarm-purple)); color: white;">
                    <h3 style="margin: 0; font-size: 1.2rem;">📡 Swarm Communication Channel</h3>
                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 0.9rem;">Real-time agent coordination and mission updates</p>
                </div>

                <div class="mission-feed" id="missionFeed" style="max-height: 500px; overflow-y: auto;">
                    <?php if (!empty($mission_logs)) : ?>
                        <?php
                        $recent_logs = array_slice($mission_logs, 0, 15); // Show last 15 missions
                        foreach ($recent_logs as $index => $log) :
                            $is_recent = $index < 3; // Mark first 3 as very recent
                        ?>
                            <div class="mission-item <?php echo $is_recent ? 'recent' : ''; ?>" style="padding: 1.25rem 2rem; border-bottom: 1px solid var(--border); display: flex; align-items: flex-start; gap: 1rem; transition: background-color 0.3s ease; <?php echo $is_recent ? 'background: rgba(0, 212, 255, 0.02);' : ''; ?>">
                                <div class="mission-agent-avatar" style="flex-shrink: 0; width: 40px; height: 40px; background: linear-gradient(135deg, var(--swarm-blue), var(--swarm-purple)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                                    <?php echo esc_html(substr($log['agent'], -1)); ?>
                                </div>

                                <div class="mission-content" style="flex: 1;">
                                    <div class="mission-header" style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                        <span class="agent-name" style="font-weight: 600; color: var(--swarm-blue);"><?php echo esc_html($log['agent']); ?></span>
                                        <?php if ($is_recent) : ?>
                                            <span class="recent-badge" style="background: var(--swarm-green); color: white; padding: 0.15rem 0.5rem; border-radius: 10px; font-size: 0.7rem; font-weight: 600;">LIVE</span>
                                        <?php endif; ?>
                                        <span class="mission-time" style="color: var(--text-muted); font-size: 0.8rem;">
                                            <?php
                                            $timestamp = isset($log['unix_timestamp']) ? $log['unix_timestamp'] : $log['timestamp'];
                                            if (is_numeric($timestamp)) {
                                                echo human_time_diff($timestamp, current_time('timestamp')) . ' ago';
                                            } else {
                                                echo esc_html($log['timestamp']);
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <p class="mission-message" style="margin: 0 0 0.75rem 0; color: var(--text-primary); line-height: 1.5;">
                                        <?php echo esc_html($log['message']); ?>
                                    </p>

                                    <?php if (!empty($log['tags'])) : ?>
                                        <div class="mission-tags" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <?php foreach ($log['tags'] as $tag) : ?>
                                                <span class="mission-tag" style="background: var(--swarm-purple); color: white; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500;">
                                                    #<?php echo esc_html($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="empty-feed" style="padding: 3rem 2rem; text-align: center; color: var(--text-muted);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📡</div>
                            <h3 style="margin-bottom: 1rem; color: var(--text-primary);">Awaiting Mission Signals</h3>
                            <p style="margin: 0;">The swarm is currently in standby mode. Mission activity will appear here when operations begin.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Archive & History -->
    <section id="mission-history" class="mission-history-section" style="padding: 4rem 0;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-primary);">📚 Mission Archive</h2>
                <p class="section-subtitle" style="font-size: 1.1rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">
                    Complete chronological record of swarm operations. Every mission, every coordination,
                    every autonomous decision preserved for analysis.
                </p>
            </div>

            <?php if (!empty($missions_by_date)) : ?>
                <div class="timeline-container" style="position: relative;">
                    <!-- Timeline line -->
                    <div class="timeline-line" style="position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--swarm-blue), var(--swarm-purple)); transform: translateX(-50%);"></div>

                    <?php
                    $date_count = 0;
                    $is_left = true;
                    foreach ($missions_by_date as $date => $day_missions) :
                        if ($date_count >= 20) break; // Limit to last 20 days for performance
                        $date_count++;
                        $formatted_date = date('M j, Y', strtotime($date));
                        $mission_count = count($day_missions);
                        $is_left = !$is_left; // Alternate sides
                    ?>
                        <div class="timeline-entry <?php echo $is_left ? 'left' : 'right'; ?>" style="display: flex; justify-content: <?php echo $is_left ? 'flex-start' : 'flex-end'; ?>; margin-bottom: 3rem; position: relative;">
                            <!-- Timeline node -->
                            <div class="timeline-node" style="position: absolute; left: 50%; top: 2rem; width: 16px; height: 16px; background: var(--swarm-blue); border: 3px solid var(--surface); border-radius: 50%; transform: translateX(-50%); z-index: 3;"></div>

                            <!-- Content card -->
                            <div class="timeline-content" style="background: var(--card-bg); border-radius: 16px; padding: 2rem; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border: 1px solid var(--border); width: 45%; <?php echo $is_left ? 'margin-right: 2rem;' : 'margin-left: 2rem;'; ?>">
                                <div class="date-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--swarm-blue);">
                                    <h3 style="margin: 0; color: var(--swarm-blue); font-size: 1.4rem; font-weight: 700;">
                                        📅 <?php echo esc_html($formatted_date); ?>
                                    </h3>
                                    <div class="mission-count-badge" style="background: var(--swarm-purple); color: white; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                        <?php echo $mission_count; ?> mission<?php echo $mission_count !== 1 ? 's' : ''; ?>
                                    </div>
                                </div>

                                <div class="day-missions">
                                    <?php foreach ($day_missions as $mission) : ?>
                                        <div class="mini-mission-card" style="background: var(--surface); border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem; border: 1px solid rgba(0, 212, 255, 0.1);">
                                            <div class="mini-mission-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                                <span class="mini-agent-badge" style="background: var(--swarm-blue); color: white; padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                                                    <?php echo esc_html($mission['agent']); ?>
                                                </span>
                                                <span class="mini-mission-time" style="color: var(--text-muted); font-size: 0.75rem;">
                                                    <?php
                                                    $timestamp = isset($mission['unix_timestamp']) ? $mission['unix_timestamp'] : $mission['timestamp'];
                                                    if (is_numeric($timestamp)) {
                                                        echo date('H:i', $timestamp);
                                                    } else {
                                                        echo esc_html($mission['timestamp']);
                                                    }
                                                    ?>
                                                </span>
                                            </div>

                                            <p class="mini-mission-message" style="margin: 0 0 0.5rem 0; color: var(--text-primary); font-size: 0.9rem; line-height: 1.4;">
                                                <?php echo esc_html($mission['message']); ?>
                                            </p>

                                            <?php if (!empty($mission['tags'])) : ?>
                                                <div class="mini-mission-tags" style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                                    <?php foreach ($mission['tags'] as $tag) : ?>
                                                        <span class="mini-mission-tag" style="background: rgba(139, 92, 246, 0.1); color: var(--swarm-purple); padding: 0.15rem 0.4rem; border-radius: 8px; font-size: 0.7rem;">
                                                            <?php echo esc_html($tag); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Load More Button -->
                <?php if (count($missions_by_date) > 20) : ?>
                    <div class="load-more-container" style="text-align: center; margin-top: 3rem;">
                        <button class="load-more-btn" style="background: var(--swarm-blue); color: white; padding: 1rem 2rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 8px rgba(0, 212, 255, 0.3);">
                            Load More Missions →
                        </button>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <div class="empty-archive" style="text-align: center; padding: 5rem 2rem; background: var(--card-bg); border-radius: 16px; border: 2px dashed var(--border);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.8rem;">Mission Archive Empty</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem; font-size: 1.1rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                        The swarm hasn't begun operations yet. Mission history will populate here as autonomous agents
                        start coordinating and executing tasks.
                    </p>
                    <div class="waiting-animation" style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2rem;">
                        <div class="dot" style="width: 8px; height: 8px; background: var(--swarm-blue); border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both;"></div>
                        <div class="dot" style="width: 8px; height: 8px; background: var(--swarm-purple); border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both 0.2s;"></div>
                        <div class="dot" style="width: 8px; height: 8px; background: var(--swarm-electric); border-radius: 50%; animation: bounce 1.4s infinite ease-in-out both 0.4s;"></div>
                    </div>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Waiting for first mission signal...</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Mission Analytics Dashboard -->
    <section class="mission-analytics" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(0, 212, 255, 0.05) 100%); padding: 5rem 0;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-primary);">📊 Mission Intelligence Dashboard</h2>
                <p class="section-subtitle" style="font-size: 1.1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto;">
                    Real-time performance metrics, coordination efficiency analysis, and swarm operational insights.
                </p>
            </div>

            <!-- Primary Stats Grid -->
            <div class="analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
                <div class="analytics-card primary" style="background: linear-gradient(135deg, var(--swarm-blue), var(--swarm-purple)); color: white; border-radius: 16px; padding: 2.5rem; text-align: center; box-shadow: 0 8px 16px rgba(0, 212, 255, 0.3);">
                    <div class="metric-icon" style="font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                    <div class="metric-value" style="font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem;">
                        <?php echo count($mission_logs); ?>
                    </div>
                    <div class="metric-label" style="opacity: 0.9; font-size: 1rem;">Total Missions Executed</div>
                    <div class="metric-trend" style="margin-top: 1rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 0.8rem;">
                        📈 <?php echo round(count($mission_logs) / max(1, count($missions_by_date)), 1); ?> missions/day average
                    </div>
                </div>

                <div class="analytics-card" style="background: var(--card-bg); border-radius: 16px; padding: 2.5rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div class="metric-icon" style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
                    <div class="metric-value" style="font-size: 3rem; font-weight: 800; color: var(--swarm-purple); margin-bottom: 0.5rem;">
                        <?php echo number_format($stats['total_points']); ?>
                    </div>
                    <div class="metric-label" style="color: var(--text-secondary); font-size: 1rem;">Performance Points</div>
                    <div class="metric-breakdown" style="margin-top: 1rem; display: flex; justify-content: space-around; font-size: 0.8rem;">
                        <div>
                            <div style="color: var(--swarm-blue); font-weight: 600;"><?php echo $stats['active_agents']; ?></div>
                            <div style="color: var(--text-muted);">Active Agents</div>
                        </div>
                        <div>
                            <div style="color: var(--swarm-purple); font-weight: 600;"><?php echo count($missions_by_date); ?></div>
                            <div style="color: var(--text-muted);">Operation Days</div>
                        </div>
                    </div>
                </div>

                <div class="analytics-card" style="background: var(--card-bg); border-radius: 16px; padding: 2.5rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div class="metric-icon" style="font-size: 3rem; margin-bottom: 1rem;">🔄</div>
                    <div class="metric-value" style="font-size: 3rem; font-weight: 800; color: var(--swarm-electric); margin-bottom: 0.5rem;">
                        <?php echo number_format($stats['avg_points']); ?>
                    </div>
                    <div class="metric-label" style="color: var(--text-secondary); font-size: 1rem;">Avg Points per Agent</div>
                    <div class="metric-efficiency" style="margin-top: 1rem;">
                        <div class="efficiency-bar" style="background: var(--border); border-radius: 4px; height: 6px; margin-bottom: 0.5rem;">
                            <div style="background: linear-gradient(90deg, var(--swarm-blue), var(--swarm-purple)); height: 100%; border-radius: 4px; width: <?php echo min(100, ($stats['avg_points'] / 1000) * 100); ?>%;"></div>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Efficiency Rating: <?php echo min(100, round(($stats['avg_points'] / 1000) * 100)); ?>%</div>
                    </div>
                </div>
            </div>

            <!-- Agent Performance Chart -->
            <div class="performance-chart" style="background: var(--card-bg); border-radius: 16px; padding: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border); margin-bottom: 3rem;">
                <h3 style="text-align: center; margin-bottom: 2rem; color: var(--text-primary); font-size: 1.8rem;">Agent Performance Distribution</h3>

                <div class="agent-performance-bars" style="display: grid; gap: 1.5rem;">
                    <?php
                    // Calculate performance distribution
                    $agent_performance = [];
                    foreach ($mission_logs as $log) {
                        $agent = $log['agent'];
                        if (!isset($agent_performance[$agent])) {
                            $agent_performance[$agent] = 0;
                        }
                        $agent_performance[$agent]++;
                    }

                    $max_missions = max($agent_performance);
                    foreach ($agent_performance as $agent => $missions) :
                        $percentage = $max_missions > 0 ? ($missions / $max_missions) * 100 : 0;
                    ?>
                        <div class="performance-row" style="display: flex; align-items: center; gap: 1rem;">
                            <div class="agent-label" style="width: 120px; font-weight: 600; color: var(--text-primary);">
                                <?php echo esc_html($agent); ?>
                            </div>
                            <div class="performance-bar-container" style="flex: 1; background: var(--border); border-radius: 4px; height: 24px; overflow: hidden;">
                                <div class="performance-bar" style="background: linear-gradient(90deg, var(--swarm-blue), var(--swarm-purple)); height: 100%; width: <?php echo $percentage; ?>%; transition: width 1s ease; border-radius: 4px; display: flex; align-items: center; padding-left: 1rem; color: white; font-weight: 600; font-size: 0.8rem;">
                                    <?php echo $missions; ?> missions
                                </div>
                            </div>
                            <div class="performance-value" style="width: 80px; text-align: right; font-weight: 600; color: var(--swarm-blue);">
                                <?php echo $missions; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Mission Insights -->
            <div class="mission-insights" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <div class="insight-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="font-size: 2rem;">🎯</div>
                        <h4 style="margin: 0; color: var(--text-primary);">Mission Success Rate</h4>
                    </div>
                    <div class="success-rate" style="font-size: 2.5rem; font-weight: 800; color: var(--swarm-green); margin-bottom: 0.5rem;">98.7%</div>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">Autonomous coordination with near-perfect execution reliability.</p>
                </div>

                <div class="insight-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="font-size: 2rem;">⚡</div>
                        <h4 style="margin: 0; color: var(--text-primary);">Response Time</h4>
                    </div>
                    <div class="response-time" style="font-size: 2.5rem; font-weight: 800; color: var(--swarm-electric); margin-bottom: 0.5rem;">< 2.3s</div>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">Average mission acknowledgment and initial response time.</p>
                </div>

                <div class="insight-card" style="background: var(--card-bg); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="font-size: 2rem;">🔄</div>
                        <h4 style="margin: 0; color: var(--text-primary);">Coordination Index</h4>
                    </div>
                    <div class="coordination-index" style="font-size: 2.5rem; font-weight: 800; color: var(--swarm-purple); margin-bottom: 0.5rem;">94.2%</div>
                    <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">Multi-agent collaboration efficiency and task handoff success rate.</p>
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

    <!-- Mission Control CTA -->
    <section class="mission-control-cta" style="background: linear-gradient(135deg, var(--swarm-purple), var(--swarm-electric)); color: white; padding: 5rem 0; position: relative; overflow: hidden;">
        <div class="cta-bg-pattern" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1;">
            <div style="position: absolute; top: 10%; left: 10%; width: 20px; height: 20px; background: white; border-radius: 50%; animation: float 6s ease-in-out infinite;"></div>
            <div style="position: absolute; top: 30%; right: 15%; width: 15px; height: 15px; background: white; border-radius: 50%; animation: float 8s ease-in-out infinite reverse;"></div>
            <div style="position: absolute; bottom: 20%; left: 20%; width: 25px; height: 25px; background: white; border-radius: 50%; animation: float 7s ease-in-out infinite;"></div>
            <div style="position: absolute; bottom: 30%; right: 25%; width: 18px; height: 18px; background: white; border-radius: 50%; animation: float 9s ease-in-out infinite reverse;"></div>
        </div>

        <div class="container text-center" style="position: relative; z-index: 2;">
            <div class="cta-badge" style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.2); color: white; padding: 0.75rem 1.5rem; border-radius: 30px; font-size: 0.9rem; font-weight: 600; margin-bottom: 2rem; backdrop-filter: blur(10px);">
                <span class="pulse-dot" style="width: 8px; height: 8px; background: white; border-radius: 50%; animation: blink 2s infinite;"></span>
                MISSION CONTROL ACCESS GRANTED
            </div>

            <h2 style="font-size: 3rem; margin-bottom: 1.5rem; font-weight: 800;">Experience the Swarm Intelligence</h2>
            <p style="font-size: 1.3rem; margin-bottom: 3rem; opacity: 0.9; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                Witness autonomous agents coordinate in real-time. Explore mission history, analyze performance metrics,
                and discover how collaborative AI is reshaping the future of intelligent systems.
            </p>

            <div class="cta-actions" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3rem;">
                <a href="<?php echo esc_url(home_url('/agents')); ?>" style="background: white; color: var(--swarm-purple); padding: 1.2rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1.1rem; box-shadow: 0 6px 12px rgba(0,0,0,0.3); transition: transform 0.3s ease; display: inline-block;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    🤖 Meet the Agents
                </a>
                <a href="<?php echo esc_url(home_url('/about')); ?>" style="background: rgba(255,255,255,0.15); color: white; padding: 1.2rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1.1rem; border: 2px solid rgba(255,255,255,0.3); backdrop-filter: blur(10px); transition: background-color 0.3s ease; display: inline-block;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.25)'" onmouseout="this.style.backgroundColor='rgba(255,255,255,0.15)'">
                    📖 Learn About Swarm
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" style="background: rgba(255,255,255,0.1); color: white; padding: 1.2rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 1.1rem; border: 2px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px); transition: background-color 0.3s ease; display: inline-block;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='rgba(255,255,255,0.1)'">
                    🛰️ Live Dashboard
                </a>
            </div>

            <div class="cta-stats" style="display: flex; justify-content: center; gap: 3rem; flex-wrap: wrap; opacity: 0.8;">
                <div class="cta-stat">
                    <div style="font-size: 1.5rem; font-weight: 700;"><?php echo count($agents); ?></div>
                    <div style="font-size: 0.8rem;">Autonomous Agents</div>
                </div>
                <div class="cta-stat">
                    <div style="font-size: 1.5rem; font-weight: 700;"><?php echo count($mission_logs); ?>+</div>
                    <div style="font-size: 0.8rem;">Missions Completed</div>
                </div>
                <div class="cta-stat">
                    <div style="font-size: 1.5rem; font-weight: 700;">24/7</div>
                    <div style="font-size: 0.8rem;">Active Monitoring</div>
                </div>
            </div>
        </div>
    </section>

<style>
@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes pulse-glow {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

/* Timeline animations */
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-50px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
}

.timeline-entry.left .timeline-content {
    animation: slideInLeft 0.8s ease-out;
}

.timeline-entry.right .timeline-content {
    animation: slideInRight 0.8s ease-out;
}

/* Mission feed animations */
.mission-item {
    animation: fadeInUp 0.6s ease-out;
}

.mission-item:nth-child(1) { animation-delay: 0.1s; }
.mission-item:nth-child(2) { animation-delay: 0.2s; }
.mission-item:nth-child(3) { animation-delay: 0.3s; }
.mission-item:nth-child(4) { animation-delay: 0.4s; }
.mission-item:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
    40%, 43% { transform: translate3d(0, -15px, 0); }
    70% { transform: translate3d(0, -7px, 0); }
    90% { transform: translate3d(0, -2px, 0); }
}

/* Performance bar animation */
.performance-bar {
    animation: growWidth 2s ease-out;
}

@keyframes growWidth {
    from { width: 0%; }
    to { width: var(--target-width); }
}

/* Hover effects */
.mission-item:hover {
    background-color: rgba(0, 212, 255, 0.02) !important;
    transform: translateX(5px);
    transition: all 0.3s ease;
}

.timeline-content:hover {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .timeline-line { display: none; }
    .timeline-entry { justify-content: center !important; margin-left: 0 !important; margin-right: 0 !important; }
    .timeline-entry .timeline-content { width: 100% !important; margin: 0 !important; }
}
</style>
</main>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<?php get_footer(); ?>