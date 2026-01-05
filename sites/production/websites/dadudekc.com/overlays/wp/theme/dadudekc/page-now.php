<?php
/**
 * Template Name: Now
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <?php while (have_posts()) : the_post(); ?>
        <article>
            <header class="page-header">
                <h1><?php the_title(); ?></h1>
                <p class="post-meta"><?php esc_html_e('Real-time status of current projects, focus areas, and availability.', 'dadudekc'); ?></p>
            </header>

            <div class="now-content">
                <!-- Status Overview -->
                <section class="status-overview" style="margin-bottom: 3rem;">
                    <div class="status-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                        <div class="status-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">🚀</div>
                            <h3 style="margin-top: 0; color: var(--accent);"><?php esc_html_e('Current Focus', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 0;"><?php esc_html_e('Building ambitious systems and scaling operations for clients.', 'dadudekc'); ?></p>
                        </div>

                        <div class="status-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">✅</div>
                            <h3 style="margin-top: 0; color: var(--accent);"><?php esc_html_e('Availability', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 0;"><?php esc_html_e('Open for new projects and consulting opportunities.', 'dadudekc'); ?></p>
                        </div>

                        <div class="status-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <div style="font-size: 2rem; margin-bottom: 1rem;">🔧</div>
                            <h3 style="margin-top: 0; color: var(--accent);"><?php esc_html_e('Tech Stack', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 0;"><?php esc_html_e('WordPress, React, Python, AI automation, system architecture.', 'dadudekc'); ?></p>
                        </div>
                    </div>
                </section>

                <!-- Current Projects -->
                <section class="current-projects" style="margin-bottom: 3rem;">
                    <h2 style="border-bottom: 2px solid var(--accent); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?php esc_html_e('Active Projects', 'dadudekc'); ?></h2>
                    <div class="projects-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                        <div class="project-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <h3 style="margin-top: 0; color: var(--accent);">🐝 <?php esc_html_e('The Swarm', 'dadudekc'); ?></h3>
                            <p><?php esc_html_e('Multi-agent AI system for web development and automation. Currently optimizing deployment pipelines and expanding capabilities.', 'dadudekc'); ?></p>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem;">
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">AI</span>
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">Automation</span>
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">In Progress</span>
                            </div>
                        </div>

                        <div class="project-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <h3 style="margin-top: 0; color: var(--accent);">📊 <?php esc_html_e('Business Intelligence Platform', 'dadudekc'); ?></h3>
                            <p><?php esc_html_e('Real-time analytics and dashboard systems. Building data visualization tools for client businesses.', 'dadudekc'); ?></p>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem;">
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">Analytics</span>
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">Dashboards</span>
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">Active</span>
                            </div>
                        </div>

                        <div class="project-card" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <h3 style="margin-top: 0; color: var(--accent);">🎨 <?php esc_html_e('Portfolio & Idea Lab', 'dadudekc'); ?></h3>
                            <p><?php esc_html_e('This website! Continuously improving design, content, and user experience. Adding new features and case studies.', 'dadudekc'); ?></p>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem;">
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">WordPress</span>
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">UX</span>
                                <span style="background: var(--border); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem;">Ongoing</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Skills & Expertise -->
                <section class="expertise" style="margin-bottom: 3rem;">
                    <h2 style="border-bottom: 2px solid var(--accent); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?php esc_html_e('Core Expertise', 'dadudekc'); ?></h2>
                    <div class="skills-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                        <div class="skill-category">
                            <h3 style="color: var(--accent); margin-bottom: 1rem;">⚡ <?php esc_html_e('Full-Stack Development', 'dadudekc'); ?></h3>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li style="margin-bottom: 0.5rem;">• WordPress (themes, plugins, custom development)</li>
                                <li style="margin-bottom: 0.5rem;">• React & modern JavaScript frameworks</li>
                                <li style="margin-bottom: 0.5rem;">• PHP, Python, Node.js backend systems</li>
                                <li style="margin-bottom: 0.5rem;">• Database design & optimization</li>
                            </ul>
                        </div>

                        <div class="skill-category">
                            <h3 style="color: var(--accent); margin-bottom: 1rem;">🤖 <?php esc_html_e('AI & Automation', 'dadudekc'); ?></h3>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li style="margin-bottom: 0.5rem;">• Multi-agent AI system design</li>
                                <li style="margin-bottom: 0.5rem;">• Workflow automation & integration</li>
                                <li style="margin-bottom: 0.5rem;">• API development & orchestration</li>
                                <li style="margin-bottom: 0.5rem;">• Machine learning implementation</li>
                            </ul>
                        </div>

                        <div class="skill-category">
                            <h3 style="color: var(--accent); margin-bottom: 1rem;">📈 <?php esc_html_e('Business Systems', 'dadudekc'); ?></h3>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li style="margin-bottom: 0.5rem;">• System architecture & scaling</li>
                                <li style="margin-bottom: 0.5rem;">• Business intelligence & analytics</li>
                                <li style="margin-bottom: 0.5rem;">• Process optimization & automation</li>
                                <li style="margin-bottom: 0.5rem;">• Technical strategy & consulting</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Contact CTA -->
                <section class="contact-cta" style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-radius: 12px; padding: 3rem; text-align: center; margin-bottom: 2rem;">
                    <h2 style="margin-top: 0; color: var(--accent);"><?php esc_html_e('Ready to Build Something Amazing?', 'dadudekc'); ?></h2>
                    <p style="font-size: 1.2rem; margin-bottom: 2rem;"><?php esc_html_e('Let\'s discuss your project and explore how we can work together to bring your vision to life.', 'dadudekc'); ?></p>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="<?php echo esc_url(dadudekc_get_contact_url()); ?>" style="background: var(--accent); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;"><?php esc_html_e('Start a Conversation →', 'dadudekc'); ?></a>
                        <a href="<?php echo esc_url(dadudekc_get_portfolio_url()); ?>" style="background: var(--surface); color: var(--text-primary); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; border: 1px solid var(--border);"><?php esc_html_e('View My Work', 'dadudekc'); ?></a>
                    </div>
                </section>

                <!-- Page Content (if any) -->
                <?php if (get_the_content()) : ?>
                    <div class="card" style="margin: 2rem 0;">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php
get_footer();
