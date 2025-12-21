<?php
/**
 * AriaJet SEO Optimization
 * Applied: 2025-12-19
 */

if (!defined('ABSPATH')) {
    exit;
}

function ariajet_site_seo_head() {
    ?>
<!-- AriaJet SEO Optimization -->
<!-- Generated: 2025-12-19 by Agent-7 -->

<!-- Primary Meta Tags -->
<meta name="title" content="AriaJet - Personal gaming and development blog">
<meta name="description" content="Personal gaming and development blog. gaming">
<meta name="keywords" content="gaming, development, personal blog, indie games">
<meta name="author" content="AriaJet">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="https://ariajet.site/">
<meta property="og:title" content="AriaJet - Personal gaming and development blog">
<meta property="og:description" content="Personal gaming and development blog. gaming">
<meta property="og:image" content="https://ariajet.site/wp-content/uploads/og-image.jpg">
<meta property="og:site_name" content="AriaJet">
<meta property="og:locale" content="en_US">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="https://ariajet.site/">
<meta property="twitter:title" content="AriaJet">
<meta property="twitter:description" content="Personal gaming and development blog. gaming">
<meta property="twitter:image" content="https://ariajet.site/wp-content/uploads/twitter-image.jpg">

<!-- Schema.org Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "AriaJet",
  "url": "https://ariajet.site",
  "description": "Personal gaming and development blog"

}
</script>

<!-- Canonical URL -->
<link rel="canonical" href="https://ariajet.site/">
    <?php
}
add_action('wp_head', 'ariajet_site_seo_head', 1);
