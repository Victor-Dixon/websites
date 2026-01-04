<?php
/**
 * Template Name: Episodes Directory
 *
 * Episodes overview and sample access
 *
 * @package DigitalDreamscape
 * @since 4.0.0 - Episodes Edition
 */

get_header();

$canonical_url = home_url('/episodes/');
$page_title = "episodes directory";
$page_description = "Devlog episodes converted to Digital Dreamscape narrative format. Sample episodes from the swarm brain archive.";

?>

<!-- SEO Meta -->
<link rel="canonical" href="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:title" content="<?php echo esc_attr($page_title); ?>" />
<meta property="og:description" content="<?php echo esc_attr($page_description); ?>" />
<meta property="og:url" content="<?php echo esc_url($canonical_url); ?>" />
<meta property="og:type" content="website" />
<meta name="description" content="<?php echo esc_attr($page_description); ?>" />

<!-- Episodes Directory Header -->
<section class="ds-episodes-header">
    <div class="ds-episodes__inner">
        <h1>episodes directory</h1>
        <p>Devlog episodes converted to Digital Dreamscape narrative format.</p>

        <div class="ds-episodes-stats">
            <span class="ds-stat-chip">total episodes: 3,000+</span>
            <span class="ds-stat-chip">converted devlogs: 3,351+</span>
            <span class="ds-stat-chip">questlines: 5</span>
        </div>

        <div class="ds-episodes-nav">
            <a href="<?php echo home_url('/questlines/'); ?>" class="ds-btn">← questlines</a>
            <a href="<?php echo home_url('/blog/'); ?>" class="ds-btn">← archive</a>
        </div>
    </div>
</section>

<!-- Episodes Introduction -->
<section class="ds-episodes-intro">
    <div class="ds-episodes__container">
        <div class="ds-intro-content">
            <h2>from devlogs to episodes</h2>
            <p>Every development session, every technical achievement, every coordination moment from the Agent_Cellphone_V2_Repository swarm brain has been transformed into narrative episodes.</p>
            <p>The complete development history is now accessible as Digital Dreamscape narrative.</p>

            <div class="ds-conversion-stats">
                <div class="ds-conversion-stat">
                    <span class="ds-stat-number">3,351+</span>
                    <span class="ds-stat-label">devlog files processed</span>
                </div>
                <div class="ds-conversion-stat">
                    <span class="ds-stat-number">3,000+</span>
                    <span class="ds-stat-label">episodes created</span>
                </div>
                <div class="ds-conversion-stat">
                    <span class="ds-stat-number">5</span>
                    <span class="ds-stat-label">questlines active</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Episodes Archive -->
<section class="ds-sample-episodes">
    <div class="ds-episodes__container">
        <h2>episode archive</h2>
        <p class="ds-section-intro">All converted devlog episodes organized by questline and category.</p>

        <?php
        // Function to get episodes from filesystem
        function get_all_episodes() {
            $episodes_dir = get_template_directory() . '/../episodes';
            $episodes = [];

            if (is_dir($episodes_dir)) {
                $files = glob($episodes_dir . '/ep_*.html');
                foreach ($files as $file) {
                    $filename = basename($file);
                    if (preg_match('/ep_(\d+)_(.*?)(_sample|_episode)\.html/', $filename, $matches)) {
                        $episode_num = (int)$matches[1];
                        $category = str_replace('-', ' ', $matches[2]);

                        // Read episode content to get metadata
                        $content = file_get_contents($file);
                        if ($content) {
                            // Extract title
                            preg_match('/<h1 class="episode-title">(.*?)<\/h1>/s', $content, $title_match);
                            $title = $title_match[1] ?? "Episode {$episode_num}";

                            // Extract excerpt
                            preg_match('/<div class="episode-excerpt">.*?<p>(.*?)<\/p>.*?<\/div>/s', $content, $excerpt_match);
                            $excerpt = $excerpt_match[1] ?? "";
                            if (strlen($excerpt) > 120) {
                                $excerpt = substr($excerpt, 0, 120) . '...';
                            }

                            // Extract agent
                            preg_match('/<span>🤖<\/span>\s*<span>Agent: (.*?)<\/span>/', $content, $agent_match);
                            $agent = $agent_match[1] ?? "swarm";

                            // Extract status
                            preg_match('/<span>⚡<\/span>\s*<span>State: (.*?)<\/span>/', $content, $status_match);
                            $status = $status_match[1] ?? "active";

                            $episodes[] = [
                                'num' => $episode_num,
                                'title' => $title,
                                'excerpt' => $excerpt,
                                'agent' => $agent,
                                'status' => $status,
                                'category' => $category,
                                'file' => $filename
                            ];
                        }
                    }
                }
            }

            // Sort by episode number descending (newest first)
            usort($episodes, function($a, $b) {
                return $b['num'] - $a['num'];
            });

            return $episodes;
        }

        $all_episodes = get_all_episodes();
        $episodes_per_page = 50;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total_episodes = count($all_episodes);
        $total_pages = ceil($total_episodes / $episodes_per_page);
        $start_index = ($current_page - 1) * $episodes_per_page;
        $display_episodes = array_slice($all_episodes, $start_index, $episodes_per_page);
        ?>

        <div class="ds-episodes-meta">
            <div class="ds-episodes-count">
                <span>Showing <?php echo ($start_index + 1) . '-' . min($start_index + $episodes_per_page, $total_episodes); ?> of <?php echo $total_episodes; ?> episodes</span>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="ds-pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo ($current_page - 1); ?>" class="ds-btn">← Previous</a>
                <?php endif; ?>

                <span class="ds-page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo ($current_page + 1); ?>" class="ds-btn">Next →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="ds-episodes-grid">
            <?php foreach ($display_episodes as $episode): ?>
            <a href="<?php echo get_template_directory_uri(); ?>/../episodes/<?php echo $episode['file']; ?>" class="ds-episode-card" target="_blank">
                <div class="ds-episode-header">
                    <span class="ds-episode-id">EP-<?php echo str_pad($episode['num'], 3, '0', STR_PAD_LEFT); ?></span>
                    <span class="ds-episode-status <?php echo $episode['status'] === 'resolved' ? 'canon' : 'active'; ?>">
                        <?php echo $episode['status'] === 'resolved' ? 'canon' : 'active'; ?>
                    </span>
                </div>
                <div class="ds-episode-content">
                    <h3 class="ds-episode-title"><?php echo esc_html($episode['title']); ?></h3>
                    <p class="ds-episode-description"><?php echo esc_html($episode['excerpt']); ?></p>
                    <div class="ds-episode-meta">
                        <span>questline: <?php echo esc_html($episode['category']); ?></span>
                        <span>agent: <?php echo esc_html($episode['agent']); ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="ds-pagination ds-pagination-bottom">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?php echo ($current_page - 1); ?>" class="ds-btn">← Previous</a>
            <?php endif; ?>

            <span class="ds-page-info">Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?php echo ($current_page + 1); ?>" class="ds-btn">Next →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Conversion Process -->
<section class="ds-conversion-process">
    <div class="ds-episodes__container">
        <h2>conversion process</h2>
        <p>How devlogs become Digital Dreamscape episodes.</p>

        <div class="ds-process-steps">
            <div class="ds-process-step">
                <div class="ds-step-icon">📚</div>
                <h3>extract content</h3>
                <p>Parse devlog files for technical achievements, quantitative metrics, strategies, and impact assessments.</p>
            </div>
            <div class="ds-process-step">
                <div class="ds-step-icon">🎭</div>
                <h3>build narrative</h3>
                <p>Transform technical data into compelling stories with system state, execution logs, and strategic impact.</p>
            </div>
            <div class="ds-process-step">
                <div class="ds-process-icon">🌌</div>
                <h3>apply dreamscape</h3>
                <p>Format narratives with Digital Dreamscape visual grammar, canon authority, and questline integration.</p>
            </div>
        </div>
    </div>
</section>

<!-- Next Steps -->
<section class="ds-next-steps">
    <div class="ds-episodes__container">
        <h2>ready for full conversion</h2>
        <p>These samples demonstrate the quality and variety of episode formats available.</p>

        <div class="ds-decision-options">
            <div class="ds-decision-option">
                <h3>🎯 proceed with system events</h3>
                <p>Major milestones and quantitative achievements - most measurable and compelling narratives.</p>
                <span class="ds-option-meta">~679 episodes available</span>
            </div>
            <div class="ds-decision-option">
                <h3>🐝 convert mission reports</h3>
                <p>Swarm coordination stories - demonstrates distributed intelligence in action.</p>
                <span class="ds-option-meta">~204 episodes available</span>
            </div>
            <div class="ds-decision-option">
                <h3>🤖 process agent sessions</h3>
                <p>Individual agent journeys - personal growth and technical skill development.</p>
                <span class="ds-option-meta">~555 episodes available</span>
            </div>
            <div class="ds-decision-option">
                <h3>🚀 full archive conversion</h3>
                <p>All categories combined - complete transformation of development history.</p>
                <span class="ds-option-meta">3,351+ episodes available</span>
            </div>
        </div>
    </div>
</section>

<style>
/* Episodes Directory Styles */
.ds-episodes-header {
    background: linear-gradient(135deg, #1a1a2e, #0a0a0f);
    border-bottom: 1px solid rgba(99, 102, 241, 0.2);
    padding: 60px 0;
}

.ds-episodes__inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    text-align: center;
}

.ds-episodes__inner h1 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.ds-episodes__inner p {
    font-size: 1.25rem;
    color: #cbd5e1;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.ds-episodes-stats {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.ds-stat-chip {
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.3);
    border-radius: 20px;
    padding: 8px 16px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    color: #cbd5e1;
}

.ds-episodes-nav {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.ds-btn {
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.3);
    color: #cbd5e1;
    text-decoration: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.ds-btn:hover {
    background: rgba(99, 102, 241, 0.2);
    border-color: #6366f1;
    color: #ffffff;
}

/* Episodes Intro */
.ds-episodes-intro {
    padding: 60px 0;
    background: #0a0a0f;
}

.ds-episodes__container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.ds-intro-content h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    color: #ffffff;
    text-align: center;
}

.ds-intro-content p {
    font-size: 1.1rem;
    color: #cbd5e1;
    line-height: 1.6;
    margin-bottom: 40px;
    text-align: center;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.ds-conversion-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    max-width: 800px;
    margin: 0 auto;
}

.ds-conversion-stat {
    text-align: center;
}

.ds-stat-number {
    font-size: 3rem;
    font-weight: bold;
    color: #6366f1;
    display: block;
    margin-bottom: 10px;
}

.ds-stat-label {
    font-size: 1rem;
    color: #94a3b8;
}

/* Sample Episodes */
.ds-sample-episodes {
    padding: 60px 0;
    background: #1a1a2e;
}

.ds-sample-episodes h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    text-align: center;
    margin-bottom: 20px;
}

.ds-section-intro {
    text-align: center;
    color: #cbd5e1;
    font-size: 1.1rem;
    margin-bottom: 50px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.ds-sample-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
}

.ds-sample-card {
    background: #2a2a4e;
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 12px;
    padding: 30px;
    transition: all 0.3s ease;
}

.ds-sample-card:hover {
    border-color: #6366f1;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
    transform: translateY(-5px);
}

.ds-sample-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.ds-sample-icon {
    font-size: 2rem;
}

.ds-sample-meta {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.ds-sample-category {
    font-weight: 600;
    color: #cbd5e1;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.ds-sample-id {
    font-family: 'JetBrains Mono', monospace;
    color: #6366f1;
    font-size: 1.1rem;
    font-weight: bold;
}

.ds-sample-content h3 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 15px;
    line-height: 1.3;
}

.ds-sample-content p {
    color: #cbd5e1;
    line-height: 1.6;
    margin-bottom: 20px;
}

.ds-sample-details {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.ds-sample-details span {
    font-size: 0.85rem;
    color: #94a3b8;
    font-family: 'JetBrains Mono', monospace;
}

.ds-btn--primary {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-color: #6366f1;
    color: #ffffff;
}

.ds-btn--primary:hover {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-color: #4f46e5;
}

/* Conversion Process */
.ds-conversion-process {
    padding: 60px 0;
    background: #0a0a0f;
}

.ds-conversion-process h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    text-align: center;
    margin-bottom: 20px;
}

.ds-conversion-process > .ds-episodes__container > p {
    text-align: center;
    color: #cbd5e1;
    font-size: 1.1rem;
    margin-bottom: 50px;
}

.ds-process-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.ds-process-step {
    text-align: center;
    padding: 30px;
    background: #1a1a2e;
    border-radius: 12px;
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.ds-step-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.ds-process-step h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 15px;
}

.ds-process-step p {
    color: #cbd5e1;
    line-height: 1.6;
}

/* Next Steps */
.ds-next-steps {
    padding: 60px 0;
    background: #2a2a4e;
}

.ds-next-steps h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    text-align: center;
    margin-bottom: 20px;
}

.ds-next-steps > .ds-episodes__container > p {
    text-align: center;
    color: #cbd5e1;
    font-size: 1.1rem;
    margin-bottom: 50px;
}

.ds-decision-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.ds-decision-option {
    background: #1a1a2e;
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 12px;
    padding: 30px;
    text-align: center;
}

.ds-decision-option h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 15px;
}

.ds-decision-option p {
    color: #cbd5e1;
    line-height: 1.6;
    margin-bottom: 20px;
}

.ds-option-meta {
    font-size: 0.9rem;
    color: #6366f1;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 500;
}

@media (max-width: 768px) {
    .ds-episodes__inner h1 {
        font-size: 2rem;
    }

    .ds-sample-grid {
        grid-template-columns: 1fr;
    }

    .ds-process-steps,
    .ds-decision-options {
        grid-template-columns: 1fr;
    }

    .ds-episodes-stats {
        flex-direction: column;
        align-items: center;
    }

    .ds-episodes-nav {
        flex-direction: column;
        align-items: center;
    }
}

/* Episodes Archive */
.ds-episodes-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.ds-episodes-count {
    font-size: 1.1rem;
    color: var(--text-secondary);
    font-family: 'JetBrains Mono', monospace;
}

.ds-pagination {
    display: flex;
    align-items: center;
    gap: 20px;
}

.ds-page-info {
    font-family: 'JetBrains Mono', monospace;
    color: var(--text-secondary);
}

.ds-episodes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.ds-episode-card {
    background: var(--void-dark);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 12px;
    padding: 25px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    display: block;
}

.ds-episode-card:hover {
    border-color: var(--energy-primary);
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
    transform: translateY(-5px);
}

.ds-episode-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.ds-episode-id {
    font-family: 'JetBrains Mono', monospace;
    font-weight: bold;
    color: var(--energy-primary);
    font-size: 1.1rem;
}

.ds-episode-status {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 12px;
}

.ds-episode-status.canon {
    background: rgba(139, 92, 246, 0.2);
    color: var(--energy-secondary);
}

.ds-episode-status.active {
    background: rgba(99, 102, 241, 0.2);
    color: var(--energy-primary);
}

.ds-episode-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
    line-height: 1.3;
}

.ds-episode-description {
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 15px;
}

.ds-episode-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.ds-episode-meta span {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-family: 'JetBrains Mono', monospace;
}

.ds-pagination-bottom {
    margin-top: 40px;
    justify-content: center;
}
</style>

<?php get_footer(); ?></content>
</xai:function_call">Now I need to create a WordPress page that uses this template. Let me check if there's a way to create a page through the WordPress admin or if I need to do it programmatically.

Actually, let me first copy the sample episodes to the root directory where they can be served, and then create the WordPress page. I'll use the existing WordPress structure. Let me copy the episodes to the site root so they can be accessed via URLs. 

Actually, let me check if the episodes directory is already accessible via the web server. Let me copy the 5 sample episodes to the root of the website directory. 

<xai:function_call name="Shell">
<parameter name="command">Copy-Item -Path "D:\websites\digitaldreamscape.site\episodes\ep_200_agent-sessions_sample.html" -Destination "D:\websites\digitaldreamscape.site\ep_200_agent-sessions_sample.html"