#!/usr/bin/env python3
"""
Fix freerideinvestor.com Text Rendering Issues
==============================================

Fixes text rendering issues similar to crosbyultimateevents.com:
- Adds CSS fixes for font rendering
- Adds PHP content filter to fix broken text patterns
- Deploys fixes to live WordPress site

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "freerideinvestor.com"
THEME_NAME = "freerideinvestor-modern"

def add_text_rendering_css(css_file: Path) -> bool:
    """Add text rendering CSS fixes to style.css."""
    if not css_file.exists():
        print(f"⚠️  CSS file not found: {css_file}")
        return False
    
    content = css_file.read_text(encoding="utf-8", errors="ignore")
    
    # Check if fixes already exist
    if "text-rendering-fix" in content or "font-variant-ligatures" in content:
        print(f"✅ Text rendering CSS fixes already present")
        return True
    
    # Add CSS fixes at the end of the file
    css_fixes = """

/* Text Rendering Fixes - Agent-5 */
.text-rendering-fix {
    text-rendering: optimizeLegibility !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    font-variant-ligatures: none !important;
    font-feature-settings: normal !important;
}

body, p, h1, h2, h3, h4, h5, h6, a, span, div, li, td, th {
    text-rendering: optimizeLegibility !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    font-variant-ligatures: none !important;
}
"""
    
    if not content.endswith("\n"):
        content += "\n"
    content += css_fixes
    
    css_file.write_text(content, encoding="utf-8")
    print(f"✅ Added text rendering CSS fixes")
    return True

def add_text_rendering_php_filter(functions_file: Path) -> bool:
    """Add PHP content filter to fix broken text patterns."""
    if not functions_file.exists():
        print(f"⚠️  Functions file not found: {functions_file}")
        return False
    
    content = functions_file.read_text(encoding="utf-8", errors="ignore")
    
    # Check if filter already exists
    if "fix_text_rendering_issues" in content:
        print(f"✅ Text rendering PHP filter already present")
        return True
    
    # Add PHP filter
    php_filter = """

/**
 * Fix Text Rendering Issues
 * Fixes broken text patterns (missing spaces, corrupted characters)
 */
function fix_text_rendering_issues($content) {
    if (empty($content)) {
        return $content;
    }
    
    // Common broken patterns: "ha  been" -> "has been", "thi  web" -> "this web", etc.
    $patterns = [
        // Fix double spaces in common words
        '/\bha\s{2,}been\b/i' => 'has been',
        '/\bthi\s{2,}web\b/i' => 'this web',
        '/\btrouble\s{2,}hooting\b/i' => 'troubleshooting',
        '/\bWordPre\s{2,}\b/i' => 'WordPress',
        '/\bweb\s{2,}ite\b/i' => 'website',
        
        // Fix corrupted spacing in common phrases
        '/\s{3,}/' => ' ', // Replace 3+ spaces with single space
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    return $content;
}
add_filter('the_content', 'fix_text_rendering_issues');
add_filter('widget_text', 'fix_text_rendering_issues');
add_filter('get_the_excerpt', 'fix_text_rendering_issues');

/**
 * Enqueue inline CSS for text rendering fixes
 */
function freerideinvestor_text_rendering_styles() {
    $css = '
        body, p, h1, h2, h3, h4, h5, h6, a, span, div, li, td, th {
            text-rendering: optimizeLegibility !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            font-variant-ligatures: none !important;
            font-feature-settings: normal !important;
        }
    ';
    wp_add_inline_style('freerideinvestor-modern-style', $css);
}
add_action('wp_enqueue_scripts', 'freerideinvestor_text_rendering_styles', 999);
"""
    
    if not content.endswith("\n"):
        content += "\n"
    content += php_filter
    
    functions_file.write_text(content, encoding="utf-8")
    print(f"✅ Added text rendering PHP filter")
    return True

def main():
    print(f"🔧 Fixing Text Rendering Issues on {SITE_NAME}...\n")
    
    project_root = Path(__file__).parent.parent
    theme_path = project_root / "websites" / SITE_NAME / "wp" / "wp-content" / "themes" / THEME_NAME
    
    if not theme_path.exists():
        print(f"❌ Theme directory not found: {theme_path}")
        return 1
    
    fixes_applied = []
    
    # Fix CSS
    css_file = theme_path / "style.css"
    if add_text_rendering_css(css_file):
        fixes_applied.append("CSS fixes")
    
    # Fix PHP
    functions_file = theme_path / "functions.php"
    if add_text_rendering_php_filter(functions_file):
        fixes_applied.append("PHP content filter")
    
    print(f"\n✅ Local fixes applied: {len(fixes_applied)}")
    for fix in fixes_applied:
        print(f"   - {fix}")
    
    print(f"\n📋 Next Step: Deploy fixes to WordPress")
    print(f"   Run: python tools/deploy_freerideinvestor_text_fixes.py")
    
    return 0

if __name__ == "__main__":
    sys.exit(main())

