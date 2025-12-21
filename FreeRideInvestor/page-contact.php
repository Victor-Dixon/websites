<?php

/**
 * Template Name: Contact Page (Auto)
 * Description: Contact page template - automatically used for /contact URL
 * Author: Agent-6 (Coordination & Communication Specialist)
 * Version: 1.0.0
 * Updated: 2025-12-17
 */
// Use the existing page-contact template if it exists
$template_path = locate_template('page-templates/page-contact.php');
if ($template_path) {
    include($template_path);
} else {
    // Fallback: Use the standard page template
    get_header();
?>
    <main id="main-content" class="site-main">
        <section class="hero blog-hero">
            <div class="hero-content">
                <h1 class="hero-title">Contact FreeRide Investor</h1>
                <p class="hero-description">
                    Get in touch with us. We'd love to hear from you!
                </p>
            </div>
        </section>
        <div class="container">
            <section class="section">
                <h2>Contact Us</h2>
                <p>Email us at <a href="mailto:support@freerideinvestor.com">support@freerideinvestor.com</a> or join our Discord community.</p>
            </section>
        </div>
    </main>
<?php
    get_footer();
}
?>