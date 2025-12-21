<?php

/**
 * Template Name: About Page (Auto)
 * Description: About page template - automatically used for /about URL
 * Author: Agent-6 (Coordination & Communication Specialist)
 * Version: 1.0.0
 * Updated: 2025-12-17
 */
// Use the existing page-about template if it exists
$template_path = locate_template('page-templates/page-about.php');
if ($template_path) {
    include($template_path);
} else {
    // Fallback: Use the standard page template
    get_header();
?>
    <main id="main-content" class="site-main">
        <section class="hero blog-hero">
            <div class="hero-content">
                <h1 class="hero-title">About FreeRide Investor</h1>
                <p class="hero-description">
                    Trading is a journey we take together. Let's explore strategies, share insights, and grow as traders and investors.
                </p>
            </div>
        </section>
        <div class="container">
            <section class="section">
                <h2>Our Mission</h2>
                <p>We're here to help you become a better trader through education, community, and data-driven strategies.</p>
            </section>
        </div>
    </main>
<?php
    get_footer();
}
?>