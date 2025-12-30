<?php
/**
 * Template Name: Beautiful About
 * 
 * Beautiful About Template
 * Modern, elegant about page design matching blog/streaming/community pages
 * 
 * @package DigitalDreamscape
 * @since 3.0.0
 */

get_header(); ?>

<main class="site-main beautiful-about-main">
    <div class="beautiful-about-container">
        <!-- Hero Header -->
        <header class="beautiful-about-header">
            <div class="beautiful-about-header-content">
                <div class="beautiful-about-badge">[ABOUT]</div>
                <h1 class="beautiful-about-title">About Digital Dreamscape</h1>
                <p class="beautiful-about-description">
                    <strong>Digital Dreamscape</strong> is a living, narrative-driven AI world where real actions become story, and story feeds back into execution. This is a build-in-public experiment in creating a persistent simulation of self + system.
                </p>
            </div>
        </header>

        <!-- Content Sections -->
        <div class="beautiful-about-content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('beautiful-about-article'); ?>>
                    <div class="beautiful-about-entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; endif; ?>
        </div>

        <!-- Additional Sections with Cards -->
        <div class="beautiful-about-sections">
            <!-- What We Are -->
            <section class="beautiful-about-section">
                <div class="beautiful-about-section-header">
                    <h2 class="beautiful-about-section-title">What We Are</h2>
                    <div class="beautiful-about-section-divider"></div>
                </div>
                <div class="beautiful-about-section-content">
                    <p>We're not just a blog or a portfolio. We're a narrative-driven system where every conversation, decision, build, failure, and breakthrough is treated as canon and transformed into structured state.</p>
                    <p>Each episode below is part of the persistent simulation. Every project update, every technical decision, every moment of clarity or confusion becomes part of the ongoing story.</p>
                </div>
            </section>

            <!-- Build in Public -->
            <section class="beautiful-about-section">
                <div class="beautiful-about-section-header">
                    <h2 class="beautiful-about-section-title">Build in Public</h2>
                    <div class="beautiful-about-section-divider"></div>
                </div>
                <div class="beautiful-about-section-content">
                    <p>This entire project is built in public. You can watch us:</p>
                    <ul class="beautiful-about-list">
                        <li>Stream live development on <a href="/streaming/">Twitch and YouTube</a></li>
                        <li>Read <a href="/blog/">episode archive</a> in our blog</li>
                        <li>Join the <a href="/community/">community</a> and see the system evolve in real-time</li>
                    </ul>
                </div>
            </section>

            <!-- Philosophy -->
            <section class="beautiful-about-section">
                <div class="beautiful-about-section-header">
                    <h2 class="beautiful-about-section-title">Our Philosophy</h2>
                    <div class="beautiful-about-section-divider"></div>
                </div>
                <div class="beautiful-about-section-content">
                    <p>We believe in:</p>
                    <div class="beautiful-about-cards">
                        <div class="beautiful-about-card">
                            <div class="beautiful-about-card-icon">🔍</div>
                            <h3 class="beautiful-about-card-title">Transparency</h3>
                            <p class="beautiful-about-card-text">Raw, authentic, direct communication about what we're building and why</p>
                        </div>
                        <div class="beautiful-about-card">
                            <div class="beautiful-about-card-icon">📖</div>
                            <h3 class="beautiful-about-card-title">Narrative as Structure</h3>
                            <p class="beautiful-about-card-text">Treating our work as an ongoing story, not just a series of tasks</p>
                        </div>
                        <div class="beautiful-about-card">
                            <div class="beautiful-about-card-icon">🚀</div>
                            <h3 class="beautiful-about-card-title">Build in Public</h3>
                            <p class="beautiful-about-card-text">Sharing the process, not just the polished result</p>
                        </div>
                        <div class="beautiful-about-card">
                            <div class="beautiful-about-card-icon">🔗</div>
                            <h3 class="beautiful-about-card-title">System Thinking</h3>
                            <p class="beautiful-about-card-text">Understanding how individual pieces connect into a larger whole</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Join the Journey -->
            <section class="beautiful-about-section beautiful-about-cta">
                <div class="beautiful-about-section-header">
                    <h2 class="beautiful-about-section-title">Join the Journey</h2>
                    <div class="beautiful-about-section-divider"></div>
                </div>
                <div class="beautiful-about-section-content">
                    <p>This is an experiment. We're figuring it out as we go. If you're interested in watching a system evolve in real-time, building in public, or just curious about how narrative and execution can merge, join us:</p>
                    <div class="beautiful-about-cta-buttons">
                        <a href="/streaming/" class="beautiful-about-cta-button beautiful-about-cta-primary">Watch Live Stream</a>
                        <a href="/blog/" class="beautiful-about-cta-button beautiful-about-cta-secondary">Read Episodes</a>
                        <a href="/community/" class="beautiful-about-cta-button beautiful-about-cta-secondary">Join Community</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php get_footer(); ?>

