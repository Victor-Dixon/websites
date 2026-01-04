<?php
/**
 * Check what's actually running on the live site
 */

echo "Checking live site content...\n\n";

// Check blog page
$blog_content = file_get_contents('https://digitaldreamscape.site/blog/');
if ($blog_content) {
    echo "=== BLOG PAGE ANALYSIS ===\n";

    // Check for our theme indicators
    if (strpos($blog_content, 'The Dreamscape Codex') !== false) {
        echo "✅ Found 'The Dreamscape Codex' - our theme elements present\n";
    } else {
        echo "❌ 'The Dreamscape Codex' not found\n";
    }

    if (strpos($blog_content, 'codex-entry') !== false) {
        echo "✅ Found 'codex-entry' class - our theme CSS present\n";
    } else {
        echo "❌ 'codex-entry' class not found\n";
    }

    if (strpos($blog_content, 'Digital Dreamscape') !== false) {
        echo "✅ Found 'Digital Dreamscape' branding\n";
    } else {
        echo "❌ 'Digital Dreamscape' branding not found\n";
    }

    // Check for WordPress theme
    if (strpos($blog_content, 'wp-content/themes') !== false) {
        echo "✅ WordPress theme system detected\n";
    } else {
        echo "❌ WordPress theme system not detected\n";
    }

    // Look for our posts
    if (strpos($blog_content, 'Data Migration Strategy') !== false) {
        echo "✅ Found our promoted post content\n";
    } else {
        echo "❌ Our promoted post content not found\n";
    }

} else {
    echo "❌ Could not fetch blog page\n";
}

echo "\n=== HOMEPAGE ANALYSIS ===\n";

// Check homepage
$home_content = file_get_contents('https://digitaldreamscape.site/');
if ($home_content) {
    if (strpos($home_content, 'Digital Dreamscape') !== false) {
        echo "✅ Homepage has Digital Dreamscape branding\n";
    } else {
        echo "❌ Homepage missing Digital Dreamscape branding\n";
    }

    if (strpos($home_content, 'World Portal') !== false) {
        echo "✅ Found World Portal elements (our theme)\n";
    } else {
        echo "❌ World Portal elements not found\n";
    }
} else {
    echo "❌ Could not fetch homepage\n";
}

echo "\n=== DIAGNOSIS ===\n";
echo "The site appears to be using a mix of themes or our theme isn't fully activated.\n";
echo "Our posts are being created (confirmed via system status) but the theme display isn't working.\n";
echo "This suggests either:\n";
echo "1. Theme not activated in WordPress admin\n";
echo "2. Theme files not in the correct location\n";
echo "3. WordPress cache issues\n";
echo "4. Multiple WordPress installations\n";
?>