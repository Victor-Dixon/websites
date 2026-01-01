<?php
/**
 * We Are Swarm SEO Optimization
 * Applied: 2025-12-19
 */

if (!defined('ABSPATH')) {
    exit;
}

function weareswarm_site_seo_head() {
    ?>
<!-- We Are Swarm SEO Optimization -->
<!-- Generated: 2025-12-19 by Agent-7 -->

<!-- Primary Meta Tags -->
<meta name="title" content="We Are Swarm - Multi-agent system architecture and operations">
<meta name="description" content="Multi-agent system architecture and operations. multi-agent systems">
<meta name="keywords" content="multi-agent systems, AI agents, swarm intelligence, system architecture, automation">
<meta name="author" content="We Are Swarm">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://weareswarm.site/">
<meta property="og:title" content="We Are Swarm - Multi-agent system architecture and operations">
<meta property="og:description" content="Multi-agent system architecture and operations. multi-agent systems">
<meta property="og:image" content="https://weareswarm.site/wp-content/uploads/og-image.jpg">
<meta property="og:site_name" content="We Are Swarm">
<meta property="og:locale" content="en_US">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://weareswarm.site/">
<meta property="twitter:title" content="We Are Swarm">
<meta property="twitter:description" content="Multi-agent system architecture and operations. multi-agent systems">
<meta property="twitter:image" content="https://weareswarm.site/wp-content/uploads/twitter-image.jpg">

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "We Are Swarm",
  "url": "https://weareswarm.site",
  "description": "Multi-agent system architecture and operations"

}
</script>

<!-- Canonical URL -->
<link rel="canonical" href="https://weareswarm.site/">
    <?php
}
add_action('wp_head', 'weareswarm_site_seo_head', 1);
