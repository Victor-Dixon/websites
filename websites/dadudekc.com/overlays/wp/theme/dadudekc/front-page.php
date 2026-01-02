<?php
/**
 * Front page template.
 *
 * @package DaDudeKC
 */

get_header();

$latest_posts = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post_status' => 'publish',
]);

$latest_notes = new WP_Query([
    'post_type' => 'note',
    'posts_per_page' => 4,
    'post_status' => 'publish',
]);

$idea_tags = get_terms([
    'taxonomy' => 'post_tag',
    'hide_empty' => true,
    'number' => 12,
]);
?>
<main>
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <h1><?php esc_html_e('Victor builds ambitious systems, ships experiments, and documents the path.', 'dadudekc'); ?></h1>
                <p><?php esc_html_e('Welcome to the portfolio + Idea Lab + blog hub. Explore projects, browse live experiments, and dive into long-form deep dives.', 'dadudekc'); ?></p>
                <div class="cta-row">
                    <form action="#" method="post" class="cta-row">
                        <label class="screen-reader-text" for="hero-email"><?php esc_html_e('Email', 'dadudekc'); ?></label>
                        <input type="email" id="hero-email" name="email" placeholder="<?php esc_attr_e('Email address', 'dadudekc'); ?>">
                        <button type="submit"><?php esc_html_e('Subscribe', 'dadudekc'); ?></button>
                    </form>
                    <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>"><?php esc_html_e('Contact Victor →', 'dadudekc'); ?></a>
                </div>
            </div>
            <div class="primary-links">
                <a href="<?php echo esc_url(dadudekc_get_portfolio_url()); ?>"><?php esc_html_e('Portfolio: shipped systems', 'dadudekc'); ?> <span>→</span></a>
                <a href="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>"><?php esc_html_e('Idea Lab: notes + articles', 'dadudekc'); ?> <span>→</span></a>
                <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>"><?php esc_html_e('Latest writing + series', 'dadudekc'); ?> <span>→</span></a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e('Latest Posts', 'dadudekc'); ?></h2>
                    <p class="section-subtitle"><?php esc_html_e('Recent deep dives, system builds, and lessons learned.', 'dadudekc'); ?></p>
                </div>
                <a href="<?php echo esc_url(dadudekc_get_blog_page_url()); ?>"><?php esc_html_e('View all →', 'dadudekc'); ?></a>
            </div>
            <div class="grid">
                <?php if ($latest_posts->have_posts()) : ?>
                    <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                        <article class="card">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="post-meta"><?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(dadudekc_get_reading_time()); ?></p>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 22)); ?></p>
                        </article>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="card"><?php esc_html_e('New posts coming soon.', 'dadudekc'); ?></div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/components/project-demos'); ?>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e('Idea Lab', 'dadudekc'); ?></h2>
                    <p class="section-subtitle"><?php esc_html_e('Notes, experiments, and brainstorms organized by tag.', 'dadudekc'); ?></p>
                </div>
                <a href="<?php echo esc_url(dadudekc_get_idea_lab_url()); ?>"><?php esc_html_e('Browse Idea Lab →', 'dadudekc'); ?></a>
            </div>
            <div class="tag-list">
                <?php if (!empty($idea_tags) && !is_wp_error($idea_tags)) : ?>
                    <?php foreach ($idea_tags as $tag) : ?>
                        <a class="tag-pill" href="<?php echo esc_url(add_query_arg('tag', $tag->slug, dadudekc_get_idea_lab_url())); ?>">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <span class="tag-pill"><?php esc_html_e('Add tags to notes and posts to populate filters.', 'dadudekc'); ?></span>
                <?php endif; ?>
            </div>
            <div class="grid" style="margin-top: 2rem;">
                <?php if ($latest_notes->have_posts()) : ?>
                    <?php while ($latest_notes->have_posts()) : $latest_notes->the_post(); ?>
                        <article class="card">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p class="post-meta"><?php esc_html_e('Note', 'dadudekc'); ?> · <?php echo esc_html(get_the_date()); ?></p>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: get_the_content(), 20)); ?></p>
                        </article>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="card"><?php esc_html_e('Idea Lab notes will appear here.', 'dadudekc'); ?></div>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </section>

    <section class="section swarm-capabilities-section" style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%); padding: 4rem 0;">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e('Swarm Capabilities', 'dadudekc'); ?></h2>
                    <p class="section-subtitle"><?php esc_html_e('Built by The Swarm: A multi-agent AI system that delivers web development, automation, and WordPress solutions at scale.', 'dadudekc'); ?></p>
                </div>
                <a href="https://weareswarm.site" target="_blank" rel="noopener"><?php esc_html_e('Learn More About Swarm →', 'dadudekc'); ?></a>
            </div>
            <div class="capabilities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-top: 3rem;">
                <div class="capability-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🌐</div>
                    <h3 style="margin-top: 0; color: var(--accent); font-size: 1.3em;"><?php esc_html_e('WordPress Development', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><?php esc_html_e('Custom themes, plugins, REST API integration, and performance optimization. This site itself is built by The Swarm.', 'dadudekc'); ?></p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">PHP</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">WordPress</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Custom Themes</span>
                    </div>
                </div>
                
                <div class="capability-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">⚡</div>
                    <h3 style="margin-top: 0; color: var(--accent); font-size: 1.3em;"><?php esc_html_e('Workflow Automation', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><?php esc_html_e('Done-for-you automation sprints that eliminate manual workflows. Delivered in 2 weeks, zero technical knowledge required.', 'dadudekc'); ?></p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Zapier</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">API Integration</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Automation</span>
                    </div>
                </div>
                
                <div class="capability-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🚀</div>
                    <h3 style="margin-top: 0; color: var(--accent); font-size: 1.3em;"><?php esc_html_e('Rapid Deployment', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><?php esc_html_e('Automated deployments, CI/CD pipelines, and infrastructure management. Sites deploy automatically on updates.', 'dadudekc'); ?></p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">SFTP</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">CI/CD</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Git</span>
                    </div>
                </div>
                
                <div class="capability-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                    <h3 style="margin-top: 0; color: var(--accent); font-size: 1.3em;"><?php esc_html_e('Business Intelligence', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><?php esc_html_e('Data visualization, analytics dashboards, and real-time insights. Turn data into actionable business intelligence.', 'dadudekc'); ?></p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Analytics</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Dashboards</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Data</span>
                    </div>
                </div>
                
                <div class="capability-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔧</div>
                    <h3 style="margin-top: 0; color: var(--accent); font-size: 1.3em;"><?php esc_html_e('System Integration', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><?php esc_html_e('Multi-system coordination, API integrations, and seamless connections across platforms. Everything works together.', 'dadudekc'); ?></p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">APIs</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Integration</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">DevOps</span>
                    </div>
                </div>
                
                <div class="capability-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎨</div>
                    <h3 style="margin-top: 0; color: var(--accent); font-size: 1.3em;"><?php esc_html_e('Modern UI/UX', 'dadudekc'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><?php esc_html_e('Responsive designs, smooth animations, and accessibility-first development. Every pixel crafted for optimal experience.', 'dadudekc'); ?></p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">CSS3</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">Responsive</span>
                        <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; color: var(--text-secondary);">UX</span>
                    </div>
                </div>
            </div>
            
            <div style="background: rgba(0, 212, 255, 0.1); border-left: 4px solid var(--accent); padding: 2rem; border-radius: 8px; margin-top: 3rem;">
                <h3 style="color: var(--accent); margin-top: 0;">🐝 <?php esc_html_e('Built by The Swarm', 'dadudekc'); ?></h3>
                <p style="margin-bottom: 1rem; color: var(--text-primary);">
                    <?php esc_html_e('This website and many others are built and maintained by', 'dadudekc'); ?> <strong><?php esc_html_e('The Swarm', 'dadudekc'); ?></strong>—<?php esc_html_e('a multi-agent AI system that coordinates specialized agents for web development, automation, and WordPress solutions. Each agent brings unique capabilities, working together to deliver results faster and more reliably.', 'dadudekc'); ?>
                </p>
                <p style="margin: 0; color: var(--text-primary);">
                    <strong><?php esc_html_e('Result:', 'dadudekc'); ?></strong> <?php esc_html_e('Professional websites, automated workflows, and scalable systems delivered in weeks, not months.', 'dadudekc'); ?> 
                    <a href="<?php echo esc_url('https://weareswarm.site'); ?>" target="_blank" rel="noopener" style="color: var(--accent); font-weight: 600;"><?php esc_html_e('See The Swarm in action →', 'dadudekc'); ?></a>
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php esc_html_e('Quick Links', 'dadudekc'); ?></h2>
                    <p class="section-subtitle"><?php esc_html_e('Jump into Victor's current focus, latest series, and active builds.', 'dadudekc'); ?></p>
                </div>
            </div>
            <div class="quick-links">
                <a class="quick-link" href="<?php echo esc_url(dadudekc_get_now_url()); ?>"><?php esc_html_e('Now: current focus + status →', 'dadudekc'); ?></a>
                <a class="quick-link" href="<?php echo esc_url(add_query_arg('series', 'dreamscape', dadudekc_get_blog_page_url())); ?>"><?php esc_html_e('Series: Dreamscape →', 'dadudekc'); ?></a>
                <a class="quick-link" href="<?php echo esc_url(add_query_arg('series', 'swarm', dadudekc_get_blog_page_url())); ?>"><?php esc_html_e('Series: Swarm →', 'dadudekc'); ?></a>
                <a class="quick-link" href="<?php echo esc_url(add_query_arg('series', 'trading-systems', dadudekc_get_blog_page_url())); ?>"><?php esc_html_e('Series: Trading Systems →', 'dadudekc'); ?></a>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
