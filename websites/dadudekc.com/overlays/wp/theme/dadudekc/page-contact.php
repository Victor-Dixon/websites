<?php
/**
 * Template Name: Contact
 *
 * @package DaDudeKC
 */

get_header();
?>
<main class="content-area">
    <?php while (have_posts()) : the_post(); ?>
        <article>
            <header class="page-header" style="text-align: center; margin-bottom: 4rem;">
                <h1 style="font-size: 3rem; margin-bottom: 1rem; background: linear-gradient(135deg, var(--accent), var(--text-primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><?php esc_html_e('Let\'s Build Something Together', 'dadudekc'); ?></h1>
                <p class="post-meta" style="font-size: 1.2rem; color: var(--text-secondary);"><?php esc_html_e('Ready to turn your vision into reality? Let\'s discuss your project and explore possibilities.', 'dadudekc'); ?></p>
            </header>

            <!-- Profile Introduction -->
            <section class="profile-intro" style="text-align: center; margin-bottom: 4rem;">
                <div style="max-width: 600px; margin: 0 auto;">
                    <img src="<?php echo esc_url(get_site_url()); ?>/wp-content/uploads/2026/01/dadudekc_profile.png"
                         alt="DaDudeKC Profile"
                         style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; margin-bottom: 2rem; box-shadow: 0 8px 16px rgba(0,0,0,0.2); border: 4px solid var(--accent);">
                    <h2 style="color: var(--accent); font-size: 1.8rem; margin-bottom: 1rem;"><?php esc_html_e('Hi, I\'m DaDudeKC', 'dadudekc'); ?></h2>
                    <p style="font-size: 1.1rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 0;">
                        <?php esc_html_e('A passionate developer and problem-solver who loves turning complex challenges into elegant solutions. With expertise in web development, automation, and AI integration, I help businesses and entrepreneurs bring their ideas to life.', 'dadudekc'); ?>
                    </p>
                </div>
            </section>

            <div class="contact-content" style="max-width: 1200px; margin: 0 auto;">
                <!-- Contact Options Grid -->
                <section class="contact-options" style="margin-bottom: 4rem;">
                    <div class="contact-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                        <!-- Direct Contact -->
                        <div class="contact-card primary" style="background: linear-gradient(135deg, var(--accent), var(--accent-dark)); color: white; border-radius: 16px; padding: 3rem 2rem; text-align: center; box-shadow: 0 8px 16px rgba(0, 212, 255, 0.3);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">💬</div>
                            <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.8rem;"><?php esc_html_e('Direct Conversation', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 2rem; opacity: 0.9; font-size: 1.1rem;"><?php esc_html_e('The fastest way to get started. Schedule a call to discuss your project in detail.', 'dadudekc'); ?></p>
                            <a href="https://calendly.com/dadudekc/30min?back=1&month=2026-01" target="_blank" rel="noopener" style="background: white; color: var(--accent); padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                <?php esc_html_e('Schedule a Call →', 'dadudekc'); ?>
                            </a>
                        </div>

                        <!-- Email Contact -->
                        <div class="contact-card" style="background: var(--surface); border-radius: 16px; padding: 3rem 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">✉️</div>
                            <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--accent); font-size: 1.8rem;"><?php esc_html_e('Email Discussion', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 2rem; color: var(--text-secondary); font-size: 1.1rem;"><?php esc_html_e('Send details about your project. I typically respond within 24 hours.', 'dadudekc'); ?></p>
                            <a href="mailto:dadudekc@gmail.com?subject=Project%20Inquiry" style="background: var(--accent); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                                <?php esc_html_e('Send Email →', 'dadudekc'); ?>
                            </a>
                        </div>

                        <!-- Portfolio Review -->
                        <div class="contact-card" style="background: var(--surface); border-radius: 16px; padding: 3rem 2rem; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🚀</div>
                            <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--accent); font-size: 1.8rem;"><?php esc_html_e('Explore My Work', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 2rem; color: var(--text-secondary); font-size: 1.1rem;"><?php esc_html_e('See examples of systems I\'ve built and solutions I\'ve delivered.', 'dadudekc'); ?></p>
                            <a href="<?php echo esc_url(dadudekc_get_portfolio_url()); ?>" style="background: var(--accent); color: white; padding: 1rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">
                                <?php esc_html_e('View Portfolio →', 'dadudekc'); ?>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Project Inquiry Form -->
                <section class="inquiry-form" style="margin-bottom: 4rem;">
                    <div style="background: var(--surface); border-radius: 16px; padding: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                        <div style="text-align: center; margin-bottom: 3rem;">
                            <h2 style="margin-top: 0; color: var(--accent); font-size: 2rem;"><?php esc_html_e('Tell Me About Your Project', 'dadudekc'); ?></h2>
                            <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 0;"><?php esc_html_e('Share details and let\'s explore how we can work together.', 'dadudekc'); ?></p>
                        </div>

                        <form class="project-form" style="max-width: 600px; margin: 0 auto;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label for="first_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);"><?php esc_html_e('First Name', 'dadudekc'); ?></label>
                                    <input type="text" id="first_name" name="first_name" required style="width: 100%; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem;">
                                </div>
                                <div>
                                    <label for="last_name" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);"><?php esc_html_e('Last Name', 'dadudekc'); ?></label>
                                    <input type="text" id="last_name" name="last_name" required style="width: 100%; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem;">
                                </div>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);"><?php esc_html_e('Email Address', 'dadudekc'); ?></label>
                                <input type="email" id="email" name="email" required style="width: 100%; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem;">
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label for="company" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);"><?php esc_html_e('Company (Optional)', 'dadudekc'); ?></label>
                                <input type="text" id="company" name="company" style="width: 100%; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem;">
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label for="project_type" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);"><?php esc_html_e('Project Type', 'dadudekc'); ?></label>
                                <select id="project_type" name="project_type" required style="width: 100%; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem; background: white;">
                                    <option value=""><?php esc_html_e('Select project type...', 'dadudekc'); ?></option>
                                    <option value="web-development"><?php esc_html_e('Web Development', 'dadudekc'); ?></option>
                                    <option value="automation"><?php esc_html_e('Workflow Automation', 'dadudekc'); ?></option>
                                    <option value="ai-integration"><?php esc_html_e('AI Integration', 'dadudekc'); ?></option>
                                    <option value="business-intelligence"><?php esc_html_e('Business Intelligence', 'dadudekc'); ?></option>
                                    <option value="system-architecture"><?php esc_html_e('System Architecture', 'dadudekc'); ?></option>
                                    <option value="consulting"><?php esc_html_e('Technical Consulting', 'dadudekc'); ?></option>
                                    <option value="other"><?php esc_html_e('Other', 'dadudekc'); ?></option>
                                </select>
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label for="timeline" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);"><?php esc_html_e('Timeline', 'dadudekc'); ?></label>
                                <select id="timeline" name="timeline" style="width: 100%; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem; background: white;">
                                    <option value=""><?php esc_html_e('Project timeline...', 'dadudekc'); ?></option>
                                    <option value="asap"><?php esc_html_e('ASAP', 'dadudekc'); ?></option>
                                    <option value="1-month"><?php esc_html_e('Within 1 month', 'dadudekc'); ?></option>
                                    <option value="3-months"><?php esc_html_e('Within 3 months', 'dadudekc'); ?></option>
                                    <option value="6-months"><?php esc_html_e('Within 6 months', 'dadudekc'); ?></option>
                                    <option value="planning"><?php esc_html_e('Still planning', 'dadudekc'); ?></option>
                                </select>
                            </div>

                            <div style="margin-bottom: 2rem;">
                                <label for="message" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary);"><?php esc_html_e('Project Details', 'dadudekc'); ?></label>
                                <textarea id="message" name="message" rows="6" placeholder="<?php esc_attr_e('Tell me about your project, goals, challenges, and what success looks like...', 'dadudekc'); ?>" style="width: 100%; padding: 1rem; border: 2px solid var(--border); border-radius: 8px; font-size: 1rem; resize: vertical;"></textarea>
                            </div>

                            <div style="text-align: center;">
                                <button type="submit" style="background: var(--accent); color: white; padding: 1.2rem 3rem; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 8px rgba(0, 212, 255, 0.3);">
                                    <?php esc_html_e('Send Project Inquiry →', 'dadudekc'); ?>
                                </button>
                                <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-secondary);"><?php esc_html_e('I typically respond within 24 hours.', 'dadudekc'); ?></p>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- FAQ Section -->
                <section class="faq-section" style="margin-bottom: 4rem;">
                    <h2 style="text-align: center; margin-bottom: 3rem; color: var(--accent); font-size: 2rem;"><?php esc_html_e('Frequently Asked Questions', 'dadudekc'); ?></h2>

                    <div class="faq-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
                        <div class="faq-item" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                            <h3 style="margin-top: 0; color: var(--accent); font-size: 1.2rem;">💰 <?php esc_html_e('What are your rates?', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 0; color: var(--text-secondary);"><?php esc_html_e('Rates vary by project complexity and scope. I offer flexible pricing models including hourly, project-based, and retainer options. Let\'s discuss your specific needs.', 'dadudekc'); ?></p>
                        </div>

                        <div class="faq-item" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                            <h3 style="margin-top: 0; color: var(--accent); font-size: 1.2rem;">⏱️ <?php esc_html_e('How long do projects take?', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 0; color: var(--text-secondary);"><?php esc_html_e('Timeline depends on scope and complexity. Simple automations: 1-2 weeks. Full web applications: 4-8 weeks. Complex systems: 2-6 months. I provide detailed estimates after understanding your requirements.', 'dadudekc'); ?></p>
                        </div>

                        <div class="faq-item" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                            <h3 style="margin-top: 0; color: var(--accent); font-size: 1.2rem;">🔧 <?php esc_html_e('What technologies do you use?', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 0; color: var(--text-secondary);"><?php esc_html_e('WordPress, React, Python, AI/ML, API integrations, cloud platforms (AWS, Vercel), databases (MySQL, PostgreSQL), and modern development tools. I choose the best technology for each project.', 'dadudekc'); ?></p>
                        </div>

                        <div class="faq-item" style="background: var(--surface); border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                            <h3 style="margin-top: 0; color: var(--accent); font-size: 1.2rem;">🤝 <?php esc_html_e('Do you work with startups?', 'dadudekc'); ?></h3>
                            <p style="margin-bottom: 0; color: var(--text-secondary);"><?php esc_html_e('Absolutely! I love working with startups and early-stage companies. I understand limited budgets and the need for rapid iteration. Let\'s build something amazing together.', 'dadudekc'); ?></p>
                        </div>
                    </div>
                </section>

                <!-- Social Links -->
                <section class="social-links" style="text-align: center; padding: 3rem; background: var(--surface); border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid var(--border);">
                    <h2 style="margin-top: 0; color: var(--accent);"><?php esc_html_e('Connect With Me', 'dadudekc'); ?></h2>
                    <p style="color: var(--text-secondary); margin-bottom: 2rem;"><?php esc_html_e('Follow my journey and see the latest projects and insights.', 'dadudekc'); ?></p>

                    <div class="social-grid" style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                        <a href="https://twitter.com/dadudekc" target="_blank" rel="noopener" style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary); text-decoration: none; padding: 1rem; border-radius: 8px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(0, 212, 255, 0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                            <span style="font-size: 1.5rem;">🐦</span>
                            <span><?php esc_html_e('Twitter', 'dadudekc'); ?></span>
                        </a>

                        <a href="https://linkedin.com/in/dadudekc" target="_blank" rel="noopener" style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary); text-decoration: none; padding: 1rem; border-radius: 8px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(0, 212, 255, 0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                            <span style="font-size: 1.5rem;">💼</span>
                            <span><?php esc_html_e('LinkedIn', 'dadudekc'); ?></span>
                        </a>

                        <a href="https://github.com/victor-dixon" target="_blank" rel="noopener" style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary); text-decoration: none; padding: 1rem; border-radius: 8px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(0, 212, 255, 0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                            <span style="font-size: 1.5rem;">💻</span>
                            <span><?php esc_html_e('GitHub', 'dadudekc'); ?></span>
                        </a>

                        <a href="<?php echo esc_url(get_feed_link('rss2')); ?>" style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-primary); text-decoration: none; padding: 1rem; border-radius: 8px; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(0, 212, 255, 0.1)'" onmouseout="this.style.backgroundColor='transparent'">
                            <span style="font-size: 1.5rem;">📡</span>
                            <span><?php esc_html_e('RSS Feed', 'dadudekc'); ?></span>
                        </a>
                    </div>
                </section>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php
get_footer();