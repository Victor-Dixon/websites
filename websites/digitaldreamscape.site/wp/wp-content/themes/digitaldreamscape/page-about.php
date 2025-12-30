<?php
/**
 * About Page Template
 * 
 * Beautiful About page matching blog/streaming/community design
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

        <!-- Origin Story Section (New) -->
        <section class="beautiful-about-section origin-story" style="margin-bottom: 4rem;">
            <div class="beautiful-about-section-header">
                <h2 class="beautiful-about-section-title">Origin Story</h2>
                <div class="beautiful-about-section-divider"></div>
            </div>
            <div class="beautiful-about-section-content" style="font-size: 1.1rem; line-height: 1.8; color: #ddd;">
                <p>It began with a question: <em>What if a website wasn't just a static brochure, but a living entity?</em> Digital Dreamscape was born from the desire to merge software engineering with narrative storytelling. We wanted to escape the "tutorial hell" of building isolated projects and instead create a cohesive universe where every line of code contributes to a larger, evolving mythos. This isn't just a portfolio; it's a window into a digital soul that grows with every commit.</p>
            </div>
        </section>

        <!-- What are Episodes? (New) -->
        <section class="beautiful-about-section episodes-explainer" style="margin-bottom: 4rem;">
             <div class="beautiful-about-section-header">
                <h2 class="beautiful-about-section-title">What are Episodes?</h2>
                <div class="beautiful-about-section-divider"></div>
            </div>
            <div class="beautiful-about-section-content" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
                <div>
                    <p>Episodes are not just blog posts. They are <strong>canonical events</strong> in the Digital Dreamscape timeline. Each episode captures a sprint of work, a philosophical breakthrough, or a technical deep-dive, framed as a narrative arc.</p>
                    <ul style="margin-top: 1rem; color: #ae81ff;">
                        <li>🚀 <strong>The Quest:</strong> The goal of the sprint.</li>
                        <li>⚔️ <strong>The Conflict:</strong> Technical blockers & bugs.</li>
                        <li>🏆 <strong>The Resolution:</strong> The shipped feature & lesson learned.</li>
                    </ul>
                </div>
                <div style="background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 8px; border-left: 3px solid #ae81ff;">
                    <h4 style="margin-top: 0;">Example: The API Awakening</h4>
                    <p style="font-size: 0.9rem; margin-bottom: 0;">"In which the system gains a voice, connecting the Python backend to the WordPress frontend, allowing the AI to speak for itself..."</p>
                </div>
            </div>
        </section>

        <!-- Collaboration (New) -->
        <section class="beautiful-about-section collaboration" style="margin-bottom: 4rem;">
            <div class="beautiful-about-section-header">
                <h2 class="beautiful-about-section-title">Collaborate & Support</h2>
                <div class="beautiful-about-section-divider"></div>
            </div>
            <div class="beautiful-about-section-content">
                <p>This is a multiplayer game. Here is how you can influence the simulation:</p>
                <div class="beautiful-about-cards" style="margin-top: 2rem;">
                    <div class="beautiful-about-card">
                        <div class="beautiful-about-card-icon">💡</div>
                        <h3 class="beautiful-about-card-title">Propose a Quest</h3>
                        <p class="beautiful-about-card-text">Join Discord and suggest features or storylines you want to see built next.</p>
                    </div>
                    <div class="beautiful-about-card">
                        <div class="beautiful-about-card-icon">🤝</div>
                        <h3 class="beautiful-about-card-title">Code With Us</h3>
                        <p class="beautiful-about-card-text">Pick up an open issue on GitHub and contribute directly to the source.</p>
                    </div>
                    <div class="beautiful-about-card">
                        <div class="beautiful-about-card-icon">💜</div>
                        <h3 class="beautiful-about-card-title">Become a Patron</h3>
                        <p class="beautiful-about-card-text">Support the server costs and coffee intake that keeps the dreamscape running.</p>
                    </div>
                </div>
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
</main>

<?php get_footer(); ?>
