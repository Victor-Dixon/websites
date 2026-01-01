<?php
/**
 * Digital Dreamscape SEO Optimization
 * Applied: 2025-12-19
 */

if (!defined('ABSPATH')) {
    exit;
}

function digitaldreamscape_site_seo_head() {
    ?>
<!-- Digital Dreamscape SEO Optimization -->
<!-- Generated: 2025-12-19 by Agent-7 -->

<!-- Primary Meta Tags -->
<meta name="title" content="Digital Dreamscape - Digital art and creative portfolio">
<meta name="description" content="Digital art and creative portfolio. digital art">
<meta name="keywords" content="digital art, portfolio, creative, design, artwork">
<meta name="author" content="Digital Dreamscape">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://digitaldreamscape.site/">
<meta property="og:title" content="Digital Dreamscape - Digital art and creative portfolio">
<meta property="og:description" content="Digital art and creative portfolio. digital art">
<meta property="og:image" content="https://digitaldreamscape.site/wp-content/uploads/og-image.jpg">
<meta property="og:site_name" content="Digital Dreamscape">
<meta property="og:locale" content="en_US">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://digitaldreamscape.site/">
<meta property="twitter:title" content="Digital Dreamscape">
<meta property="twitter:description" content="Digital art and creative portfolio. digital art">
<meta property="twitter:image" content="https://digitaldreamscape.site/wp-content/uploads/twitter-image.jpg">

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CreativeWork",
  "name": "Digital Dreamscape",
  "url": "https://digitaldreamscape.site",
  "description": "Digital art and creative portfolio"

}
</script>

<!-- Canonical URL -->
<link rel="canonical" href="https://digitaldreamscape.site/">
    <?php
}
add_action('wp_head', 'digitaldreamscape_site_seo_head', 1);
