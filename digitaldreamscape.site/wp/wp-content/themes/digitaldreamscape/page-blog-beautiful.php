<?php
/**
 * Template Name: Codex
 * Dreamscape Codex - The Central Lore Repository
 *
 * Custom template for the Blog page that displays the Dreamscape Codex
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
            </h1>
            <p class="codex-subtitle">Central Repository of All Dreamscape Lore & Episodes</p>

            <!-- Codex Statistics -->
            <div class="codex-stats">
                <div class="stat-item">
                    <span class="stat-number" id="total-entries">0</span>
                    <span class="stat-label">Entries</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="reading-time">0</span>
                    <span class="stat-label">Hours Read</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="content-types">0</span>
                    <span class="stat-label">Categories</span>
                </div>
            </div>
        </header>

        <!-- Codex Controls -->
        <div class="codex-controls">
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">All Entries</button>
                <button class="filter-tab" data-filter="episodes">Episodes</button>
                <button class="filter-tab" data-filter="lore">World Lore</button>
                <button class="filter-tab" data-filter="characters">Characters</button>
                <button class="filter-tab" data-filter="technology">Technology</button>
                <button class="filter-tab" data-filter="events">Key Events</button>
            </div>

            <!-- Search and Sort -->
            <div class="search-sort">
                <div class="search-box">
                    <input type="text" id="codex-search" placeholder="Search the codex..." autocomplete="off">
                    <button id="clear-search" style="display: none;">✕</button>
                </div>
                <div class="sort-dropdown">
                    <select id="codex-sort">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="title-asc">Title A-Z</option>
                        <option value="title-desc">Title Z-A</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Codex Content Grid -->
        <div class="codex-grid" id="codex-entries">
            <?php
            // Get all posts for the codex
            $codex_query = new WP_Query(array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1, // Get all posts
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            $entry_count = 0;
            $total_reading_time = 0;
            $categories_used = array();

            if ($codex_query->have_posts()) :
                while ($codex_query->have_posts()) :
                    $codex_query->the_post();
                    $entry_count++;

                    // Calculate reading time (average 200 words per minute)
                    $content = get_the_content();
                    $word_count = str_word_count(strip_tags($content));
                    $reading_time_minutes = ceil($word_count / 200);
                    $total_reading_time += $reading_time_minutes;

                    // Get categories for this post
                    $post_categories = get_the_category();
                    $primary_category = !empty($post_categories) ? $post_categories[0]->name : 'Uncategorized';

                    // Determine content type based on categories
                    $content_type = 'misc';
                    $type_label = 'Misc';
                    $type_color = '#6c757d';

                    if (stripos($primary_category, 'episode') !== false || stripos(get_the_title(), 'episode') !== false) {
                        $content_type = 'episodes';
                        $type_label = 'Episode';
                        $type_color = '#007bff';
                    } elseif (stripos($primary_category, 'lore') !== false || stripos($primary_category, 'world') !== false) {
                        $content_type = 'lore';
                        $type_label = 'World Lore';
                        $type_color = '#28a745';
                    } elseif (stripos($primary_category, 'character') !== false) {
                        $content_type = 'characters';
                        $type_label = 'Character';
                        $type_color = '#dc3545';
                    } elseif (stripos($primary_category, 'tech') !== false || stripos($primary_category, 'system') !== false) {
                        $content_type = 'technology';
                        $type_label = 'Technology';
                        $type_color = '#17a2b8';
                    } elseif (stripos($primary_category, 'event') !== false) {
                        $content_type = 'events';
                        $type_label = 'Key Event';
                        $type_color = '#ffc107';
                    }

                    // Track categories
                    if (!in_array($primary_category, $categories_used)) {
                        $categories_used[] = $primary_category;
                    }

                    // Get featured image or fallback
                    $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                    if (!$thumbnail_url) {
                        $thumbnail_url = 'https://digitaldreamscape.site/wp-content/uploads/2024/01/digital-dreamscape-logo.png';
                    }
                    ?>
                    <article class="codex-entry <?php echo esc_attr($content_type); ?>" data-type="<?php echo esc_attr($content_type); ?>" data-date="<?php echo get_the_date('Y-m-d'); ?>" data-title="<?php echo esc_attr(get_the_title()); ?>">
                        <div class="entry-thumbnail">
                            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                            <div class="entry-type-badge" style="background-color: <?php echo esc_attr($type_color); ?>">
                                <?php echo esc_html($type_label); ?>
                            </div>
                        </div>

                        <div class="entry-content">
                            <div class="entry-meta">
                                <time class="entry-date" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date('M j, Y'); ?>
                                </time>
                                <span class="reading-time">
                                    <?php echo $reading_time_minutes; ?> min read
                                </span>
                            </div>

                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <div class="entry-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 25, '...'); ?>
                            </div>

                            <div class="entry-categories">
                                <?php
                                foreach ($post_categories as $category) {
                                    echo '<span class="category-tag">' . esc_html($category->name) . '</span>';
                                }
                                ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="entry-link">Read Entry →</a>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <div class="no-codex-entries">
                    <div class="empty-codex">
                        <span class="empty-icon">📚</span>
                        <h3>The Codex Awaits</h3>
                        <p>New entries will appear here as the Dreamscape narrative unfolds.</p>
                    </div>
                </div>
                <?php
            endif;
            ?>
        </div>

        <!-- Codex Footer Stats -->
        <footer class="codex-footer">
            <div class="codex-summary">
                <p>
                    📚 <strong><?php echo $entry_count; ?> entries</strong> catalogued •
                    📖 <strong><?php echo round($total_reading_time / 60, 1); ?> hours</strong> of content •
                    🏷️ <strong><?php echo count($categories_used); ?> categories</strong> explored
                </p>
            </div>
        </footer>
    </div>
</main>

<!-- Codex JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const codex = {
        entries: document.querySelectorAll('.codex-entry'),
        filterTabs: document.querySelectorAll('.filter-tab'),
        searchInput: document.getElementById('codex-search'),
        clearSearchBtn: document.getElementById('clear-search'),
        sortSelect: document.getElementById('codex-sort'),
        stats: {
            total: document.getElementById('total-entries'),
            reading: document.getElementById('reading-time'),
            types: document.getElementById('content-types')
        }
    };

    // Update statistics
    function updateStats() {
        const visibleEntries = document.querySelectorAll('.codex-entry:not(.hidden)');
        const totalHours = Array.from(visibleEntries).reduce((total, entry) => {
            const timeText = entry.querySelector('.reading-time').textContent;
            const minutes = parseInt(timeText.replace(' min read', '')) || 0;
            return total + (minutes / 60);
        }, 0);

        const categories = new Set();
        visibleEntries.forEach(entry => {
            const cats = entry.querySelectorAll('.category-tag');
            cats.forEach(cat => categories.add(cat.textContent));
        });

        codex.stats.total.textContent = visibleEntries.length;
        codex.stats.reading.textContent = Math.round(totalHours * 10) / 10;
        codex.stats.types.textContent = categories.size;
    }

    // Filter entries
    function filterEntries(filterType) {
        codex.entries.forEach(entry => {
            const entryType = entry.dataset.type;
            if (filterType === 'all' || entryType === filterType) {
                entry.classList.remove('hidden');
            } else {
                entry.classList.add('hidden');
            }
        });
        updateStats();
    }

    // Search entries
    function searchEntries(query) {
        const lowerQuery = query.toLowerCase();
        codex.entries.forEach(entry => {
            const title = entry.querySelector('.entry-title').textContent.toLowerCase();
            const excerpt = entry.querySelector('.entry-excerpt').textContent.toLowerCase();
            const isVisible = title.includes(lowerQuery) || excerpt.includes(lowerQuery);
            entry.classList.toggle('hidden', !isVisible);
        });
        updateStats();
    }

    // Sort entries
    function sortEntries(sortType) {
        const entriesArray = Array.from(codex.entries);
        const container = document.getElementById('codex-entries');

        entriesArray.sort((a, b) => {
            switch(sortType) {
                case 'oldest':
                    return new Date(a.dataset.date) - new Date(b.dataset.date);
                case 'title-asc':
                    return a.dataset.title.localeCompare(b.dataset.title);
                case 'title-desc':
                    return b.dataset.title.localeCompare(a.dataset.title);
                case 'newest':
                default:
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
            }
        });

        // Reorder DOM elements
        entriesArray.forEach(entry => container.appendChild(entry));
    }

    // Event listeners
    codex.filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            codex.filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            filterEntries(this.dataset.filter);
        });
    });

    codex.searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        codex.clearSearchBtn.style.display = query ? 'block' : 'none';
        searchEntries(query);
    });

    codex.clearSearchBtn.addEventListener('click', function() {
        codex.searchInput.value = '';
        this.style.display = 'none';
        searchEntries('');
    });

    codex.sortSelect.addEventListener('change', function() {
        sortEntries(this.value);
    });

    // Initialize stats
    updateStats();
});
</script>

<?php get_footer(); ?>