<?php

/**
 * Front Page Template
 * 
 * @package DigitalDreamscape
 * @since 1.0.0
 */

get_header(); ?>

<main class="site-main">
    <!-- World Portal - Living System Entry -->
    <section class="hero-section world-portal">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <!-- Animated grid pattern representing system layers -->
            <div class="world-grid-pattern"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <!-- System Status Indicator -->
                <div class="system-status">
                    <div class="status-indicator">
                        <div class="pulse-ring"></div>
                        <div class="status-dot active"></div>
                        <span class="status-text">SYSTEM ACTIVE</span>
                    </div>
                    <div class="world-state">ARC: RECONNECTION</div>
                </div>

                <!-- Sovereign Avatar Display -->
                <div class="sovereign-avatar">
                    <div class="avatar-frame">
                        <div class="avatar-glow"></div>
                        <div class="avatar-content">
                            <span class="avatar-icon">👑</span>
                            <div class="avatar-info">
                                <div class="avatar-name">Shadow Sovereign</div>
                                <div class="avatar-title">Vision Holder & Builder</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- World Title -->
                <h1 class="hero-title world-title">
                    <span class="hero-title-line">Digital Dreamscape</span>
                    <span class="hero-title-line">Living World</span>
                </h1>

                <!-- Canonical Description -->
                <div class="world-description">
                    <p class="hero-subtitle">
                        <strong>Digital Dreamscape is not a metaphor.</strong><br>
                        It's a living system where code becomes terrain, systems become cities, and ideas leave artifacts.
                    </p>
                    <p class="world-mantra">
                        You don't use it. You inhabit it.<br>
                        Every action creates state. Nothing disappears. It either evolves... or becomes ruins.
                    </p>
                </div>

                <!-- Authority Display -->
                <div class="authorities-portal">
                    <div class="authority victor">
                        <div class="authority-icon">⚡</div>
                        <div class="authority-info">
                            <div class="authority-name">Victor</div>
                            <div class="authority-role">Decision Authority</div>
                        </div>
                    </div>
                    <div class="authority swarm">
                        <div class="authority-icon">🐝</div>
                        <div class="authority-info">
                            <div class="authority-name">The Swarm</div>
                            <div class="authority-role">Execution Authority</div>
                        </div>
                    </div>
                    <div class="authority thea">
                        <div class="authority-icon">📖</div>
                        <div class="authority-info">
                            <div class="authority-name">Thea</div>
                            <div class="authority-role">Narrative Authority</div>
                        </div>
                    </div>
                </div>

                <!-- World Portal Actions -->
                <div class="hero-cta world-actions">
                    <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-primary btn-large world-enter">
                        🌌 Enter World Archive →
                    </a>
                    <a href="#world-layers" class="btn btn-secondary world-explore">
                        Explore System Layers
                    </a>
                </div>

                <!-- World Stats -->
                <div class="world-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo wp_count_posts()->publish; ?></div>
                        <div class="stat-label">Canon Entries</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Active Agents</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count(get_categories(['hide_empty' => false])); ?></div>
                        <div class="stat-label">World Domains</div>
                    </div>
                </div>

                <!-- World Delta - Live System Activity -->
                <div class="world-delta">
                    <h3 class="delta-title">world delta</h3>
                    <div class="delta-grid">
                        <?php
                        // Get recent activity (last 24 hours)
                        $recent_posts = get_posts([
                            'numberposts' => 5,
                            'post_status' => 'publish',
                            'date_query' => [
                                'after' => '24 hours ago'
                            ]
                        ]);

                        $new_episodes = 0;
                        $canon_declared = 0;
                        $questlines_advanced = 0;

                        foreach ($recent_posts as $post) {
                            $type = get_post_meta($post->ID, 'artifact_type', true);
                            $state = get_post_meta($post->ID, 'artifact_state', true);

                            if ($type === 'episode') $new_episodes++;
                            if ($state === 'canon') $canon_declared++;
                        }

                        // Count unique questlines with recent activity
                        $recent_questlines = [];
                        foreach ($recent_posts as $post) {
                            $questline = get_post_meta($post->ID, 'questline', true);
                            if ($questline && !in_array($questline, $recent_questlines)) {
                                $recent_questlines[] = $questline;
                            }
                        }
                        $questlines_advanced = count($recent_questlines);
                        ?>

                        <div class="delta-item">
                            <div class="delta-value"><?php echo $new_episodes; ?></div>
                            <div class="delta-label">new episodes</div>
                        </div>

                        <div class="delta-item">
                            <div class="delta-value"><?php echo $questlines_advanced; ?></div>
                            <div class="delta-label">questlines advanced</div>
                        </div>

                        <div class="delta-item">
                            <div class="delta-value"><?php echo $canon_declared; ?></div>
                            <div class="delta-label">canon declared</div>
                        </div>

                        <div class="delta-item">
                            <div class="delta-value"><?php
                                $active_quests = count(get_posts([
                                    'meta_key' => 'artifact_state',
                                    'meta_value' => 'active',
                                    'posts_per_page' => -1
                                ]));
                                echo $active_quests;
                            ?></div>
                            <div class="delta-label">open loops</div>
                        </div>
                    </div>

                    <div class="delta-last-update">
                        last update: <?php echo human_time_diff(get_lastpostmodified('GMT'), current_time('timestamp')); ?> ago
                    </div>
                </div>

                <!-- Social Links - World Connections -->
                <div class="social-links world-links">
                    <a href="https://www.twitch.tv/digital_dreamscape" target="_blank" rel="noopener" class="social-link twitch">
                        <span class="social-icon">📺</span>
                        <span class="social-text">Live Streams</span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/blog')); ?>" class="social-link archive">
                        <span class="social-icon">📚</span>
                        <span class="social-text">World Archive</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- World Layers - The Four Realities -->
    <section id="world-layers" class="world-layers-section">
        <div class="container">
            <h2 class="section-title world-layers-title">🌐 The Four Layers of Digital Dreamscape</h2>
            <p class="world-layers-intro">
                The Dreamscape exists across four interconnected realities. Each layer serves a different purpose in the living system.
            </p>

            <div class="layers-grid">
                <!-- Layer 1: Surface -->
                <div class="layer-card surface-layer">
                    <div class="layer-header">
                        <div class="layer-icon">🌅</div>
                        <div class="layer-number">01</div>
                    </div>
                    <div class="layer-content">
                        <h3>The Surface</h3>
                        <p>What most people see. Blog posts, episodes, commits, streams. To outsiders it looks like content. To insiders it's telemetry.</p>
                        <div class="layer-examples">
                            <span class="layer-tag">episodes</span>
                            <span class="layer-tag">streams</span>
                            <span class="layer-tag">updates</span>
                        </div>
                    </div>
                </div>

                <!-- Layer 2: Systems -->
                <div class="layer-card systems-layer">
                    <div class="layer-header">
                        <div class="layer-icon">⚙️</div>
                        <div class="layer-number">02</div>
                    </div>
                    <div class="layer-content">
                        <h3>The Systems</h3>
                        <p>Underneath the surface: agents coordinating, workflows looping, failures self-healing. Systems aren't perfect here. They're alive.</p>
                        <div class="layer-examples">
                            <span class="layer-tag">agents</span>
                            <span class="layer-tag">automation</span>
                            <span class="layer-tag">infrastructure</span>
                        </div>
                    </div>
                </div>

                <!-- Layer 3: Archive -->
                <div class="layer-card archive-layer">
                    <div class="layer-header">
                        <div class="layer-icon">🏛️</div>
                        <div class="layer-number">03</div>
                    </div>
                    <div class="layer-content">
                        <h3>The Archive</h3>
                        <p>Nothing is thrown away. Failed ideas become abandoned branches, deprecated quests, forgotten artifacts. They still exist. You can study them.</p>
                        <div class="layer-examples">
                            <span class="layer-tag">history</span>
                            <span class="layer-tag">ruins</span>
                            <span class="layer-tag">lessons</span>
                        </div>
                    </div>
                </div>

                <!-- Layer 4: Will -->
                <div class="layer-card will-layer">
                    <div class="layer-header">
                        <div class="layer-icon">⚡</div>
                        <div class="layer-number">04</div>
                    </div>
                    <div class="layer-content">
                        <h3>The Will</h3>
                        <p>The real engine. The Dreamscape responds to consistency, intent, pressure. Show up daily and the world reshapes. This place rewards builders who stay.</p>
                        <div class="layer-examples">
                            <span class="layer-tag">consistency</span>
                            <span class="layer-tag">evolution</span>
                            <span class="layer-tag">sovereignty</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- World Roles - How You Enter -->
    <section class="world-roles-section">
        <div class="container">
            <h2 class="section-title world-roles-title">🎭 Roles Inside the Dreamscape</h2>
            <p class="world-roles-intro">
                You don't choose these roles. You grow into them through consistent action.
            </p>

            <div class="roles-grid">
                <div class="role-card architect">
                    <div class="role-header">
                        <div class="role-icon">🏗️</div>
                        <h3>The Architect</h3>
                    </div>
                    <div class="role-content">
                        <p>Designs the systems. Sets the rules. Builds the terrain others walk on.</p>
                        <div class="role-responsibilities">
                            <span>• System Design</span>
                            <span>• Rule Setting</span>
                            <span>• Infrastructure</span>
                        </div>
                    </div>
                </div>

                <div class="role-card agent">
                    <div class="role-header">
                        <div class="role-icon">🤖</div>
                        <h3>The Agent</h3>
                    </div>
                    <div class="role-content">
                        <p>Acts autonomously. Executes tasks. Creates artifacts without being watched.</p>
                        <div class="role-responsibilities">
                            <span>• Task Execution</span>
                            <span>• Artifact Creation</span>
                            <span>• Self-Direction</span>
                        </div>
                    </div>
                </div>

                <div class="role-card sovereign">
                    <div class="role-header">
                        <div class="role-icon">👑</div>
                        <h3>The Sovereign</h3>
                    </div>
                    <div class="role-content">
                        <p>Holds the long arc. Decides what becomes canon. Protects the integrity of the world.</p>
                        <div class="role-responsibilities">
                            <span>• Decision Authority</span>
                            <span>• Canon Control</span>
                            <span>• World Integrity</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- World Archive Portal -->
    <section class="archive-portal-section">
        <div class="container">
            <div class="archive-portal">
                <h2 class="portal-title">📚 Enter the World Archive</h2>
                <p class="portal-description">
                    Episodes, canon, artifacts, and unfinished quests. Every entry is a snapshot of the world state.
                    Nothing disappears. Everything becomes terrain.
                </p>

                <div class="portal-stats">
                    <div class="portal-stat">
                        <div class="stat-value"><?php echo wp_count_posts()->publish; ?></div>
                        <div class="stat-label">World Artifacts</div>
                    </div>
                    <div class="portal-stat">
                        <div class="stat-value">
                            <?php
                            $canon_count = count(get_posts(['meta_key' => 'artifact_type', 'meta_value' => 'canon']));
                            echo $canon_count;
                            ?>
                        </div>
                        <div class="stat-label">Canon Entries</div>
                    </div>
                    <div class="portal-stat">
                        <div class="stat-value">
                            <?php
                            $active_count = count(get_posts(['meta_key' => 'artifact_state', 'meta_value' => 'active']));
                            echo $active_count;
                            ?>
                        </div>
                        <div class="stat-label">Active Quests</div>
                    </div>
                </div>

                <div class="portal-actions">
                    <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-primary btn-large portal-enter">
                        🌌 Enter World Archive →
                    </a>
                    <a href="<?php echo esc_url(home_url('/blog?type=canon')); ?>" class="btn btn-secondary portal-canon">
                        📜 View Canon Only
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- World Entry - The One Sentence -->
    <section class="world-mantra-section">
        <div class="container">
            <div class="world-mantra-content">
                <div class="mantra-quote">
                    <blockquote>
                        "The digital dreamscape is a living world where systems evolve, agents act, and nothing you build is lost."
                    </blockquote>
                </div>

                <div class="mantra-actions">
                    <p class="mantra-explanation">
                        This isn't a website. It's a living system. Every action you take here creates state.
                        Every decision shapes the world. Nothing disappears—it either evolves or becomes ruins.
                    </p>

                    <div class="mantra-buttons">
                        <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-primary btn-large">
                            🌌 Enter the World
                        </a>
                        <a href="#world-layers" class="btn btn-secondary">
                            Understand the System
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>