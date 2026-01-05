<?php
/**
 * Dreamscape Codex - The Central Lore Repository
 *
 * Comprehensive archive of all Dreamscape narratives, episodes, lore, and canonical content
 *
 * @package DigitalDreamscape
 * @since 3.0.0 - Codex Edition
 */

get_header(); ?>

<main class="site-main codex-main">
    <div class="container">
        <!-- Codex Header - Scholarly Archive Interface -->
        <header class="codex-header">
            <div class="codex-badge">[DREAMSCAPE CODEX]</div>
            <h1 class="codex-title">
                <span class="codex-icon">📚</span>
                The Dreamscape Codex
                <span class="codex-subtitle">Central Lore Repository</span>
            </h1>
            <div class="codex-description">
                <p><strong>The Dreamscape Codex</strong> serves as the comprehensive archive for all canonical narratives, episodes, lore, and historical records of the Digital Dreamscape universe.</p>
                <p>Every interaction, decision, and development within the simulation becomes part of the persistent <strong>canonical timeline</strong>, documented and organized for reference and continuity.</p>
            </div>

            <!-- Codex Navigation -->
            <nav class="codex-navigation">
                <div class="codex-nav-tabs">
                    <button class="codex-tab active" data-filter="all">
                        <span class="tab-icon">📖</span>
                        All Entries
                    </button>
                    <button class="codex-tab" data-filter="episodes">
                        <span class="tab-icon">🎭</span>
                        Episodes
                    </button>
                    <button class="codex-tab" data-filter="lore">
                        <span class="tab-icon">🏛️</span>
                        World Lore
                    </button>
                    <button class="codex-tab" data-filter="characters">
                        <span class="tab-icon">👥</span>
                        Characters
                    </button>
                    <button class="codex-tab" data-filter="technology">
                        <span class="tab-icon">⚙️</span>
                        Technology
                    </button>
                    <button class="codex-tab" data-filter="events">
                        <span class="tab-icon">📅</span>
                        Key Events
                    </button>
                </div>

                <!-- Search and Filter Controls -->
                <div class="codex-controls">
                    <div class="search-container">
                        <input type="text" id="codex-search" placeholder="Search the codex..." class="codex-search-input">
                        <button class="search-clear" id="search-clear">×</button>
                    </div>
                    <div class="sort-controls">
                        <select id="codex-sort" class="codex-sort-select">
                            <option value="date-desc">Newest First</option>
                            <option value="date-asc">Oldest First</option>
                            <option value="title-asc">Title A-Z</option>
                            <option value="title-desc">Title Z-A</option>
                        </select>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Codex Statistics -->
        <div class="codex-stats">
            <?php
            $total_posts = wp_count_posts()->publish;
            $total_authors = count(get_users(['role__in' => ['administrator', 'editor', 'author']]));
            $categories = get_categories();
            $total_categories = count($categories);
            ?>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_posts; ?></div>
                <div class="stat-label">Canonical Entries</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_categories; ?></div>
                <div class="stat-label">Lore Categories</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_authors; ?></div>
                <div class="stat-label">Contributors</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="reading-time">—</div>
                <div class="stat-label">Total Read Time</div>
            </div>
        </div>

        <!-- Codex Entries Grid -->
        <div class="codex-entries-grid" id="codex-entries">
            <?php
            // Query all published posts for the codex
            $codex_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => -1, // Get all posts
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            $total_reading_time = 0;

            if ($codex_query->have_posts()) :
                while ($codex_query->have_posts()) : $codex_query->the_post();

                    // Calculate reading time
                    $content = get_the_content();
                    $word_count = str_word_count(strip_tags($content));
                    $reading_time_minutes = ceil($word_count / 200); // Average 200 words per minute
                    $total_reading_time += $reading_time_minutes;

                    // Get categories for filtering
                    $post_categories = get_the_category();
                    $category_classes = '';
                    $primary_category = '';

                    if (!empty($post_categories)) {
                        $primary_category = $post_categories[0]->name;
                        $category_classes = ' ' . implode(' ', array_map(function($cat) {
                            return 'category-' . sanitize_title($cat->name);
                        }, $post_categories));
                    }

                    // Determine entry type based on content or category
                    $entry_type = 'episode'; // default
                    if (stripos($primary_category, 'lore') !== false || stripos(get_the_title(), 'lore') !== false) {
                        $entry_type = 'lore';
                    } elseif (stripos($primary_category, 'character') !== false || stripos(get_the_title(), 'character') !== false) {
                        $entry_type = 'character';
                    } elseif (stripos($primary_category, 'tech') !== false || stripos($primary_category, 'system') !== false) {
                        $entry_type = 'technology';
                    } elseif (stripos($primary_category, 'event') !== false) {
                        $entry_type = 'event';
                    }
                    ?>
                    <article id="post-<?php the_ID(); ?>"
                             class="codex-entry codex-<?php echo $entry_type; ?><?php echo $category_classes; ?>"
                             data-type="<?php echo $entry_type; ?>"
                             data-title="<?php echo esc_attr(get_the_title()); ?>"
                             data-date="<?php echo get_the_date('Y-m-d'); ?>"
                             data-reading-time="<?php echo $reading_time_minutes; ?>">

                        <!-- Entry Header with Type Indicator -->
                        <div class="codex-entry-header">
                            <div class="entry-type-badge">
                                <?php
                                $type_icons = [
                                    'episode' => '🎭',
                                    'lore' => '🏛️',
                                    'character' => '👤',
                                    'technology' => '⚙️',
                                    'event' => '📅'
                                ];
                                $type_labels = [
                                    'episode' => 'EPISODE',
                                    'lore' => 'WORLD LORE',
                                    'character' => 'CHARACTER',
                                    'technology' => 'TECHNOLOGY',
                                    'event' => 'KEY EVENT'
                                ];
                                ?>
                                <span class="type-icon"><?php echo $type_icons[$entry_type]; ?></span>
                                <span class="type-label"><?php echo $type_labels[$entry_type]; ?></span>
                            </div>

                            <div class="entry-meta">
                                <time datetime="<?php echo get_the_date('c'); ?>" class="entry-date">
                                    <?php echo get_the_date('M j, Y'); ?>
                                </time>
                                <span class="reading-time">
                                    <span class="time-icon">⏱️</span>
                                    <?php echo $reading_time_minutes; ?> min read
                                </span>
                            </div>
                        </div>

                        <!-- Entry Visual -->
                        <div class="codex-entry-visual">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="entry-image">
                                    <?php the_post_thumbnail('medium_large', array('class' => 'entry-img')); ?>
                                    <div class="entry-overlay"></div>
                                </div>
                            <?php else : ?>
                                <div class="entry-placeholder">
                                    <div class="placeholder-pattern"></div>
                                    <div class="placeholder-icon">
                                        <?php echo $type_icons[$entry_type]; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Entry Content -->
                        <div class="codex-entry-content">
                            <div class="entry-categories">
                                <?php
                                if (!empty($post_categories)) {
                                    foreach ($post_categories as $category) {
                                        echo '<span class="entry-category">' . esc_html($category->name) . '</span>';
                                    }
                                }
                                ?>
                            </div>

                            <h3 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <div class="entry-excerpt">
                                <?php
                                $excerpt = get_the_excerpt();
                                if (empty($excerpt)) {
                                    $excerpt = wp_trim_words(get_the_content(), 30, '...');
                                }
                                echo $excerpt;
                                ?>
                            </div>

                            <div class="entry-author">
                                <div class="author-avatar">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 32); ?>
                                </div>
                                <span class="author-name"><?php the_author(); ?></span>
                            </div>
                        </div>

                        <!-- Entry Actions -->
                        <div class="codex-entry-actions">
                            <a href="<?php the_permalink(); ?>" class="entry-link">
                                <span class="link-text">Read Entry</span>
                                <span class="link-arrow">→</span>
                            </a>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="codex-empty">
                    <div class="empty-icon">📚</div>
                    <h3>The Codex Awaits</h3>
                    <p>The Dreamscape Codex is currently empty. As the simulation evolves, all canonical events, decisions, and developments will be recorded here.</p>
                    <p><em>The narrative begins with the first entry...</em></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Codex Footer with Timeline -->
        <div class="codex-footer">
            <div class="timeline-section">
                <h3 class="timeline-title">📅 Canonical Timeline</h3>
                <p class="timeline-description">
                    All entries in the Dreamscape Codex follow a canonical timeline. Events are recorded chronologically as they occur within the simulation.
                </p>

                <div class="timeline-stats">
                    <div class="timeline-stat">
                        <div class="stat-icon">📊</div>
                        <div class="stat-content">
                            <div class="stat-number" id="total-entries"><?php echo $total_posts; ?></div>
                            <div class="stat-label">Total Entries</div>
                        </div>
                    </div>
                    <div class="timeline-stat">
                        <div class="stat-icon">⏰</div>
                        <div class="stat-content">
                            <div class="stat-number" id="total-reading-time"><?php echo round($total_reading_time / 60, 1); ?>h</div>
                            <div class="stat-label">Total Read Time</div>
                        </div>
                    </div>
                    <div class="timeline-stat">
                        <div class="stat-icon">🎯</div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $total_categories; ?></div>
                            <div class="stat-label">Categories</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Codex JavaScript for Filtering and Search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codex = {
        entries: document.querySelectorAll('.codex-entry'),
        tabs: document.querySelectorAll('.codex-tab'),
        searchInput: document.getElementById('codex-search'),
        searchClear: document.getElementById('search-clear'),
        sortSelect: document.getElementById('codex-sort'),

        init() {
            this.bindEvents();
            this.updateStats();
        },

        bindEvents() {
            // Tab filtering
            this.tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    this.setActiveTab(tab);
                    this.filterEntries(tab.dataset.filter);
                });
            });

            // Search functionality
            this.searchInput.addEventListener('input', (e) => {
                this.searchEntries(e.target.value);
            });

            this.searchClear.addEventListener('click', () => {
                this.searchInput.value = '';
                this.searchEntries('');
            });

            // Sort functionality
            this.sortSelect.addEventListener('change', (e) => {
                this.sortEntries(e.target.value);
            });
        },

        setActiveTab(activeTab) {
            this.tabs.forEach(tab => tab.classList.remove('active'));
            activeTab.classList.add('active');
        },

        filterEntries(filterType) {
            this.entries.forEach(entry => {
                const entryType = entry.dataset.type;
                const shouldShow = filterType === 'all' || entryType === filterType;
                entry.style.display = shouldShow ? 'block' : 'none';
            });
            this.updateVisibleStats();
        },

        searchEntries(query) {
            const searchTerm = query.toLowerCase();

            this.entries.forEach(entry => {
                const title = entry.dataset.title.toLowerCase();
                const content = entry.querySelector('.entry-excerpt').textContent.toLowerCase();
                const shouldShow = !query || title.includes(searchTerm) || content.includes(searchTerm);
                entry.style.display = shouldShow ? 'block' : 'none';
            });

            this.updateVisibleStats();
        },

        sortEntries(sortType) {
            const entriesArray = Array.from(this.entries);
            const container = document.getElementById('codex-entries');

            entriesArray.sort((a, b) => {
                switch(sortType) {
                    case 'date-asc':
                        return new Date(a.dataset.date) - new Date(b.dataset.date);
                    case 'date-desc':
                        return new Date(b.dataset.date) - new Date(a.dataset.date);
                    case 'title-asc':
                        return a.dataset.title.localeCompare(b.dataset.title);
                    case 'title-desc':
                        return b.dataset.title.localeCompare(a.dataset.title);
                    default:
                        return 0;
                }
            });

            entriesArray.forEach(entry => container.appendChild(entry));
        },

        updateStats() {
            const totalTime = Array.from(this.entries).reduce((sum, entry) => {
                return sum + parseInt(entry.dataset.readingTime || 0);
            }, 0);

            document.getElementById('reading-time').textContent = `${Math.round(totalTime / 60 * 10) / 10}h`;
        },

        updateVisibleStats() {
            const visibleEntries = Array.from(this.entries).filter(entry =>
                entry.style.display !== 'none'
            );

            const visibleTime = visibleEntries.reduce((sum, entry) => {
                return sum + parseInt(entry.dataset.readingTime || 0);
            }, 0);

            // Update displayed stats for visible entries
            const totalEntriesEl = document.getElementById('total-entries');
            const totalTimeEl = document.getElementById('total-reading-time');

            if (totalEntriesEl) totalEntriesEl.textContent = visibleEntries.length;
            if (totalTimeEl) totalTimeEl.textContent = `${Math.round(visibleTime / 60 * 10) / 10}h`;
        }
    };

    codex.init();
});
</script>

<?php get_footer(); ?>

