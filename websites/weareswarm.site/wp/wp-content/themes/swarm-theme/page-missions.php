<?php
/**
 * Template Name: Mission Archives
 * Description: Comprehensive display of all SWARM mission history and accomplishments
 */

get_header();
include_once('mission-data.php');
?>

<section class="missions-hero">
    <div class="section-container">
        <div class="section-header">
            <h1 class="section-title">
                Mission <span class="title-accent">Archives</span>
            </h1>
            <p class="section-subtitle">
                Complete chronicle of SWARM operations, accomplishments, and ongoing missions
            </p>
        </div>

        <!-- Mission Statistics Overview -->
        <div class="mission-stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $mission_stats['total_missions']; ?></div>
                <div class="stat-label">Total Missions</div>
                <div class="stat-subtext">All time</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $mission_stats['completed_missions']; ?></div>
                <div class="stat-label">Completed</div>
                <div class="stat-subtext"><?php echo $mission_stats['completion_rate']; ?>% success rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $mission_stats['pending_missions']; ?></div>
                <div class="stat-label">Active Missions</div>
                <div class="stat-subtext">In progress</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $mission_stats['total_points']; ?></div>
                <div class="stat-label">Points Earned</div>
                <div class="stat-subtext">Total rewards</div>
            </div>
        </div>
    </div>
</section>

<section class="missions-content">
    <div class="section-container">

        <!-- Filter Controls -->
        <div class="mission-filters">
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">All Missions (<?php echo count($mission_history); ?>)</button>
                <button class="filter-tab" data-filter="completed">Completed (<?php echo $mission_stats['completed_missions']; ?>)</button>
                <button class="filter-tab" data-filter="pending">Active (<?php echo $mission_stats['pending_missions']; ?>)</button>
                <button class="filter-tab" data-filter="high">High Priority</button>
                <button class="filter-tab" data-filter="medium">Medium Priority</button>
            </div>

            <div class="filter-search">
                <input type="text" id="mission-search" placeholder="Search missions..." class="search-input">
                <select id="agent-filter" class="agent-select">
                    <option value="">All Agents</option>
                    <?php
                    $agents = array_unique(array_column(array_filter($mission_history, function($m) { return $m['agent'] != 'Unknown'; }), 'agent'));
                    sort($agents);
                    foreach ($agents as $agent): ?>
                        <option value="<?php echo strtolower($agent); ?>"><?php echo $agent; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Missions Timeline -->
        <div class="missions-timeline">
            <?php foreach ($mission_history as $mission): ?>
                <div class="mission-item <?php echo $mission['status']; ?> <?php echo $mission['priority']; ?>"
                     data-agent="<?php echo strtolower($mission['agent']); ?>"
                     data-priority="<?php echo $mission['priority']; ?>"
                     data-status="<?php echo $mission['status']; ?>">

                    <div class="mission-header">
                        <div class="mission-meta">
                            <span class="mission-priority priority-<?php echo $mission['priority']; ?>">
                                <?php echo strtoupper($mission['priority']); ?>
                            </span>
                            <span class="mission-agent">Agent <?php echo $mission['agent']; ?></span>
                            <?php if ($mission['points'] > 0): ?>
                                <span class="mission-points"><?php echo $mission['points']; ?> pts</span>
                            <?php endif; ?>
                            <span class="mission-type"><?php echo $mission['execution_type']; ?></span>
                        </div>

                        <div class="mission-status">
                            <?php if ($mission['status'] === 'completed'): ?>
                                <span class="status-completed">✅ COMPLETE</span>
                                <?php if ($mission['completed_date']): ?>
                                    <span class="completion-date"><?php echo date('M j, Y', strtotime($mission['completed_date'])); ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="status-pending">⏳ ACTIVE</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mission-content">
                        <h3 class="mission-title"><?php echo htmlspecialchars($mission['title']); ?></h3>
                        <p class="mission-description"><?php echo htmlspecialchars($mission['description']); ?></p>

                        <?php if (!empty($mission['tags'])): ?>
                            <div class="mission-tags">
                                <?php foreach ($mission['tags'] as $tag): ?>
                                    <span class="mission-tag"><?php echo htmlspecialchars($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mission-actions">
                        <button class="mission-toggle" onclick="toggleMissionDetails(this)">
                            <span class="toggle-text">Show Details</span>
                            <span class="toggle-icon">▼</span>
                        </button>
                    </div>

                    <div class="mission-details" style="display: none;">
                        <div class="mission-raw-description">
                            <strong>Full Description:</strong><br>
                            <?php echo nl2br(htmlspecialchars($mission['raw_description'])); ?>
                        </div>

                        <div class="mission-technical-info">
                            <div class="info-row">
                                <strong>ID:</strong> <?php echo $mission['id']; ?>
                            </div>
                            <div class="info-row">
                                <strong>Progress:</strong> <?php echo $mission['progress']; ?>%
                            </div>
                            <?php if ($mission['date']): ?>
                                <div class="info-row">
                                    <strong>Date:</strong> <?php echo date('F j, Y', strtotime($mission['date'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Agent Performance Section -->
        <div class="agent-performance">
            <h2 class="section-title">Agent Performance</h2>
            <div class="performance-grid">
                <?php
                $agent_stats = [];
                foreach ($mission_history as $mission) {
                    $agent = $mission['agent'];
                    if ($agent !== 'Unknown') {
                        if (!isset($agent_stats[$agent])) {
                            $agent_stats[$agent] = ['total' => 0, 'completed' => 0, 'points' => 0];
                        }
                        $agent_stats[$agent]['total']++;
                        if ($mission['status'] === 'completed') {
                            $agent_stats[$agent]['completed']++;
                            $agent_stats[$agent]['points'] += $mission['points'];
                        }
                    }
                }

                arsort($agent_stats); // Sort by total missions
                foreach ($agent_stats as $agent => $stats):
                    $completion_rate = $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0;
                ?>
                    <div class="performance-card">
                        <div class="agent-avatar">
                            <div class="agent-icon"><?php echo substr($agent, -1); ?></div>
                        </div>
                        <div class="performance-info">
                            <h3><?php echo $agent; ?></h3>
                            <div class="performance-stats">
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo $stats['total']; ?></span>
                                    <span class="stat-label">Total</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo $stats['completed']; ?></span>
                                    <span class="stat-label">Completed</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo $completion_rate; ?>%</span>
                                    <span class="stat-label">Success</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo $stats['points']; ?></span>
                                    <span class="stat-label">Points</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<script>
function toggleMissionDetails(button) {
    const missionItem = button.closest('.mission-item');
    const details = missionItem.querySelector('.mission-details');
    const toggleText = button.querySelector('.toggle-text');
    const toggleIcon = button.querySelector('.toggle-icon');

    if (details.style.display === 'none') {
        details.style.display = 'block';
        toggleText.textContent = 'Hide Details';
        toggleIcon.textContent = '▲';
    } else {
        details.style.display = 'none';
        toggleText.textContent = 'Show Details';
        toggleIcon.textContent = '▼';
    }
}

// Mission filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const searchInput = document.getElementById('mission-search');
    const agentSelect = document.getElementById('agent-filter');
    const missionItems = document.querySelectorAll('.mission-item');

    function filterMissions() {
        const activeFilter = document.querySelector('.filter-tab.active').dataset.filter;
        const searchTerm = searchInput.value.toLowerCase();
        const selectedAgent = agentSelect.value.toLowerCase();

        missionItems.forEach(item => {
            const status = item.dataset.status;
            const priority = item.dataset.priority;
            const agent = item.dataset.agent;
            const title = item.querySelector('.mission-title').textContent.toLowerCase();
            const description = item.querySelector('.mission-description').textContent.toLowerCase();

            let showItem = true;

            // Filter by tab
            if (activeFilter !== 'all') {
                if (activeFilter === 'high' || activeFilter === 'medium') {
                    showItem = priority === activeFilter;
                } else {
                    showItem = status === activeFilter;
                }
            }

            // Filter by search
            if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm)) {
                showItem = false;
            }

            // Filter by agent
            if (selectedAgent && agent !== selectedAgent) {
                showItem = false;
            }

            item.style.display = showItem ? 'block' : 'none';
        });
    }

    // Tab filtering
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            filterMissions();
        });
    });

    // Search filtering
    searchInput.addEventListener('input', filterMissions);
    agentSelect.addEventListener('change', filterMissions);
});
</script>

<style>
/* Mission Archives Styles */
.missions-hero {
    padding: 5rem 0 3rem;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
}

.mission-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-top: 4rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(0, 255, 255, 0.3);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.stat-number {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(135deg, #00ffff, #ff00ff);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1.125rem;
    font-weight: 600;
    opacity: 0.9;
    margin-bottom: 0.25rem;
}

.stat-subtext {
    font-size: 0.875rem;
    opacity: 0.7;
}

/* Mission Filters */
.mission-filters {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 3rem;
}

.filter-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

.filter-tab {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}

.filter-tab:hover,
.filter-tab.active {
    background: linear-gradient(135deg, #00ffff, #ff00ff);
    border-color: transparent;
    color: #0a0a0a;
}

.filter-search {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.search-input,
.agent-select {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    color: #fff;
    font-size: 1rem;
}

.search-input:focus,
.agent-select:focus {
    outline: none;
    border-color: #00ffff;
    box-shadow: 0 0 0 2px rgba(0, 255, 255, 0.2);
}

/* Mission Timeline */
.missions-timeline {
    margin-bottom: 4rem;
}

.mission-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.mission-item:hover {
    border-color: rgba(0, 255, 255, 0.2);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.mission-item.completed {
    border-left: 4px solid #00ff00;
}

.mission-item.pending {
    border-left: 4px solid #ffff00;
}

.mission-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.mission-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
}

.mission-priority {
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.priority-high {
    background: linear-gradient(135deg, #ff4444, #cc0000);
    color: white;
}

.priority-medium {
    background: linear-gradient(135deg, #ffaa00, #cc8800);
    color: white;
}

.mission-agent,
.mission-points,
.mission-type {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
}

.mission-status {
    text-align: right;
}

.status-completed {
    color: #00ff00;
    font-weight: 700;
    font-size: 0.875rem;
}

.status-pending {
    color: #ffff00;
    font-weight: 700;
    font-size: 0.875rem;
}

.completion-date {
    display: block;
    font-size: 0.75rem;
    opacity: 0.8;
    margin-top: 0.25rem;
}

.mission-title {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
    color: #fff;
}

.mission-description {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.mission-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.mission-tag {
    background: rgba(0, 255, 255, 0.1);
    border: 1px solid rgba(0, 255, 255, 0.3);
    color: #00ffff;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.mission-actions {
    margin-top: 1rem;
}

.mission-toggle {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.mission-toggle:hover {
    background: rgba(0, 255, 255, 0.1);
    border-color: rgba(0, 255, 255, 0.3);
}

.mission-details {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.mission-raw-description {
    background: rgba(255, 255, 255, 0.02);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    line-height: 1.4;
}

.mission-technical-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-row {
    background: rgba(255, 255, 255, 0.02);
    padding: 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
}

/* Agent Performance Section */
.agent-performance {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 3rem;
}

.performance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.performance-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all 0.3s ease;
}

.performance-card:hover {
    transform: translateY(-5px);
    border-color: rgba(0, 255, 255, 0.3);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.agent-avatar {
    flex-shrink: 0;
}

.agent-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #00ffff, #ff00ff);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: #0a0a0a;
}

.performance-info h3 {
    margin-bottom: 1rem;
    color: #fff;
    font-size: 1.25rem;
}

.performance-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #00ffff;
}

.stat-label {
    font-size: 0.75rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .mission-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .filter-tabs {
        flex-direction: column;
    }

    .filter-tab {
        text-align: center;
    }

    .filter-search {
        flex-direction: column;
        gap: 0.5rem;
    }

    .mission-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .mission-status {
        text-align: left;
        margin-top: 0.5rem;
    }

    .performance-grid {
        grid-template-columns: 1fr;
    }

    .performance-card {
        flex-direction: column;
        text-align: center;
    }

    .mission-technical-info {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>