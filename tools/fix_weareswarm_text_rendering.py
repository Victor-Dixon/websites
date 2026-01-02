#!/usr/bin/env python3
"""
Fix text rendering issues on weareswarm.online
Apply similar fixes as used for crosbyultimateevents.com
"""

import os
import re
from pathlib import Path

class WeAreSwarmTextRenderingFix:
    def __init__(self, site_dir="websites/weareswarm.online"):
        self.site_dir = Path(site_dir)
        self.overlays_dir = self.site_dir / "overlays" / "theme"

    def create_css_fixes(self):
        """Create CSS fixes for text rendering issues"""
        css_content = """/* We Are Swarm Text Rendering Fixes */
/* Applied: 2026-01-01 */
/* Based on crosbyultimateevents.com fixes */

/* ===== CRITICAL TEXT RENDERING FIXES ===== */

/* Global fix - ensure proper word spacing */
* {
    word-spacing: normal !important;
    letter-spacing: normal !important;
}

/* Body-level fixes */
body {
    word-spacing: normal;
    letter-spacing: normal;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Typography fixes - comprehensive coverage */
h1, h2, h3, h4, h5, h6,
p, span, div, a, li, td, th, label,
input, textarea, select, button,
.site-title, .main-navigation, .hero-content,
.value-item, .service-card, .lead-capture-content,
.content-area, .entry-content, .post-content,
.widget, .sidebar, .footer-widget {
    word-spacing: normal !important;
    letter-spacing: normal !important;
    text-rendering: optimizeLegibility !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* Specific fixes for known issues */
.capabilities-text,
.services-content,
.about-content,
.hero-subtitle,
.feature-description {
    word-spacing: normal !important;
    letter-spacing: normal !important;
    white-space: normal !important;
}

/* Fix for multi-word elements that might be broken */
.multi-agent-text,
.system-architecture-text,
.swarm-intelligence-text {
    word-spacing: normal !important;
    letter-spacing: 0 !important;
}

/* Navigation fixes */
.main-navigation a,
.site-navigation a,
.nav-menu a {
    word-spacing: normal !important;
    letter-spacing: normal !important;
}

/* Content area fixes */
.site-main,
.content-area,
.main-content {
    word-spacing: normal !important;
    letter-spacing: normal !important;
}

/* Footer fixes */
.site-footer,
.footer-content,
.footer-text {
    word-spacing: normal !important;
    letter-spacing: normal !important;
}

/* ===== MOBILE RESPONSIVE FIXES ===== */
@media (max-width: 768px) {
    body, p, span, div, a, li, h1, h2, h3, h4, h5, h6 {
        word-spacing: normal !important;
        letter-spacing: normal !important;
    }
}
"""

        css_file = self.overlays_dir / "text_rendering_fixes.css"
        with open(css_file, 'w') as f:
            f.write(css_content)

        print(f"✅ Created CSS fixes: {css_file}")
        return css_file

    def create_php_content_filter(self):
        """Create PHP content filter to fix broken text patterns"""
        php_content = """<?php
/**
 * We Are Swarm Text Rendering Content Filter
 * Applied: 2026-01-01
 * Based on crosbyultimateevents.com fixes
 */

if (!defined('ABSPATH')) {
    exit;
}

function weareswarm_fix_text_rendering($content) {
    // Fix common text rendering issues found on weareswarm.online
    $fixes = array(
        // Fix "Capabilitie" -> "Capabilities"
        '/\\bCapabilitie\\b/i' => 'Capabilities',

        // Fix "WordPre" -> "WordPress"
        '/\\bWordPre\\b/i' => 'WordPress',

        // Fix spaced text patterns
        '/A multi-agent AI\\s+y\\s+tem\\s+howca\\s+ing/i' => 'A multi-agent AI system showcasing',
        '/Specialize\\s+d\\s+in\\s+y\\s+tem\\s+integration/i' => 'Specialized in system integration',
        '/©\\s+2025\\s+weare\\s+warm\\.online/i' => '© 2025 weareswarm.online',

        // Fix general spacing issues
        '/(\\w)\\s{2,}(\\w)/' => '$1 $2',  // Multiple spaces between words
        '/(\\w)\\s+(\\w)\\s+(\\w)/' => '$1 $2 $3',  // Spaced words that should be together

        // Fix domain spacing
        '/weare\\s+warm/i' => 'weareswarm',
    );

    // Apply fixes if broken patterns found
    foreach ($fixes as $pattern => $replacement) {
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
        }
    }

    return $content;
}

// Apply to content, titles, excerpts, and bloginfo
add_filter('the_content', 'weareswarm_fix_text_rendering', 999);
add_filter('the_title', 'weareswarm_fix_text_rendering', 999);
add_filter('the_excerpt', 'weareswarm_fix_text_rendering', 999);
add_filter('bloginfo', 'weareswarm_fix_text_rendering', 999);
add_filter('get_bloginfo', 'weareswarm_fix_text_rendering', 999);

// Additional filters for navigation and widgets
add_filter('wp_nav_menu_items', 'weareswarm_fix_text_rendering', 999);
add_filter('widget_text', 'weareswarm_fix_text_rendering', 999);
add_filter('widget_title', 'weareswarm_fix_text_rendering', 999);

function weareswarm_add_inline_css() {
    // High-priority inline CSS that loads after all plugins
    wp_add_inline_style('weareswarm-style', '
        /* CRITICAL FIX: Text rendering issues for weareswarm.online */
        * {
            word-spacing: normal !important;
        }
        body, p, span, div, a, li, h1, h2, h3, h4, h5, h6,
        td, th, label, input, textarea, select, button,
        .site-title, .main-navigation, .hero-content,
        .capabilities-text, .services-content, .about-content,
        .hero-subtitle, .feature-description, .content-area,
        .entry-content, .post-content, .widget, .sidebar,
        .footer-widget, .site-footer, .footer-content {
            word-spacing: normal !important;
            letter-spacing: normal !important;
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            white-space: normal !important;
        }
        /* Specific fixes for known spacing issues */
        .multi-agent-text, .system-architecture-text, .swarm-intelligence-text {
            word-spacing: normal !important;
            letter-spacing: 0 !important;
        }
    ');
}

// Hook into wp_enqueue_scripts with high priority
add_action('wp_enqueue_scripts', 'weareswarm_add_inline_css', 999);
"""

        php_file = self.overlays_dir / "text_rendering_content_filter.php"
        with open(php_file, 'w') as f:
            f.write(php_content)

        print(f"✅ Created PHP content filter: {php_file}")
        return php_file

    def update_existing_seo_file(self):
        """Update existing SEO file to include text rendering fixes"""
        seo_file = self.overlays_dir / "temp_weareswarm_site_seo.php"

        if seo_file.exists():
            with open(seo_file, 'r') as f:
                content = f.read()

            # Add text rendering fix function call
            if 'weareswarm_site_seo_head' in content:
                # Insert the content filter include before the add_action
                updated_content = content.replace(
                    "add_action('wp_head', 'weareswarm_site_seo_head', 1);",
                    "// Include text rendering fixes\nrequire_once __DIR__ . '/text_rendering_content_filter.php';\n\nadd_action('wp_head', 'weareswarm_site_seo_head', 1);"
                )

                with open(seo_file, 'w') as f:
                    f.write(updated_content)

                print(f"✅ Updated SEO file: {seo_file}")
                return seo_file

        return None

    def create_deployment_package(self):
        """Create deployment package info"""
        print("\n📦 DEPLOYMENT PACKAGE CREATED")
        print("=" * 40)
        print("Files to deploy to weareswarm.online:")
        print("1. overlays/theme/text_rendering_fixes.css")
        print("2. overlays/theme/text_rendering_content_filter.php")
        print("3. overlays/theme/temp_weareswarm_site_seo.php (updated)")

        print("\n🚀 DEPLOYMENT INSTRUCTIONS:")
        print("1. Use SFTP to upload files to WordPress theme directory")
        print("2. Clear WordPress cache (WP Super Cache, W3 Total Cache, etc.)")
        print("3. Clear browser cache (Ctrl+F5)")
        print("4. Test site: https://weareswarm.online")
        print("5. Verify text renders correctly")

        print("\n✅ EXPECTED RESULTS:")
        print("- 'Capabilitie' should become 'Capabilities'")
        print("- 'WordPre' should become 'WordPress'")
        print("- Spaced text should be properly formatted")
        print("- All text should have normal word spacing")

    def run_fixes(self):
        """Run all fixes"""
        print("🔧 Applying We Are Swarm Text Rendering Fixes")
        print("=" * 50)

        # Create overlay directory if it doesn't exist
        self.overlays_dir.mkdir(parents=True, exist_ok=True)

        # Create fixes
        css_file = self.create_css_fixes()
        php_file = self.create_php_content_filter()
        seo_file = self.update_existing_seo_file()

        # Create deployment package
        self.create_deployment_package()

        return {
            'css_file': str(css_file),
            'php_file': str(php_file),
            'seo_file': str(seo_file) if seo_file else None
        }

def main():
    fixer = WeAreSwarmTextRenderingFix()
    results = fixer.run_fixes()

    print("\n✅ FIXES CREATED SUCCESSFULLY")
    print(f"CSS Fixes: {results['css_file']}")
    print(f"PHP Filter: {results['php_file']}")
    if results['seo_file']:
        print(f"SEO File Updated: {results['seo_file']}")

if __name__ == '__main__':
    main()