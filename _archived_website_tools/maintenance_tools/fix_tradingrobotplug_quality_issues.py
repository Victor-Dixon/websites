#!/usr/bin/env python3
"""
Fix Trading Robot Plug Website Quality Issues
==============================================

Fixes critical quality issues identified in quality assessment:
1. Navigation typo: "Capabilitie" → "Capabilities"
2. Footer typo: "All right re erved" → "All rights reserved"
3. Page title: "tradingrobotplug.com" → Descriptive title

Agent-5: Quality Assurance & Professional Standards
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "tradingrobotplug.com"
THEME_NAME = "tradingrobotplug-theme"

def fix_navigation_typo(header_file: Path) -> bool:
    """Fix 'Capabilitie' → 'Capabilities' in navigation."""
    if not header_file.exists():
        print(f"⚠️  Header file not found: {header_file}")
        return False
    
    content = header_file.read_text(encoding="utf-8")
    original_content = content
    
    # Fix navigation typo
    content = content.replace("Capabilitie", "Capabilities")
    
    if content != original_content:
        header_file.write_text(content, encoding="utf-8")
        print(f"✅ Fixed navigation typo: Capabilitie → Capabilities")
        return True
    else:
        print(f"⚠️  Navigation typo not found in header file")
        return False

def fix_footer_typo(footer_file: Path) -> bool:
    """Fix 'All right re erved' → 'All rights reserved' in footer."""
    if not footer_file.exists():
        print(f"⚠️  Footer file not found: {footer_file}")
        return False
    
    content = footer_file.read_text(encoding="utf-8")
    original_content = content
    
    # Fix footer typo variations
    content = content.replace("All right re erved", "All rights reserved")
    content = content.replace("All right  re erved", "All rights reserved")
    content = content.replace("All right reserved", "All rights reserved")
    
    if content != original_content:
        footer_file.write_text(content, encoding="utf-8")
        print(f"✅ Fixed footer typo: All right re erved → All rights reserved")
        return True
    else:
        # Check if already correct
        if "All rights reserved" in content:
            print(f"✅ Footer copyright already correct")
            return True
        else:
            print(f"⚠️  Footer typo pattern not found")
            return False

def fix_page_title(functions_file: Path) -> bool:
    """Update page title from generic 'tradingrobotplug.com' to descriptive title."""
    if not functions_file.exists():
        print(f"⚠️  Functions file not found: {functions_file}")
        return False
    
    content = functions_file.read_text(encoding="utf-8")
    original_content = content
    
    # Look for document title filter or wp_title
    # Add/update title tag function
    title_function = """
/**
 * Set proper page title
 */
function tradingrobotplug_page_title($title) {
    if (is_front_page() || is_home()) {
        return 'Trading Robot Plug - Automated Trading Robots That Actually Work';
    }
    return $title;
}
add_filter('document_title_parts', function($title_parts) {
    if (is_front_page() || is_home()) {
        $title_parts['title'] = 'Trading Robot Plug';
        $title_parts['site'] = 'Automated Trading Robots That Actually Work';
    }
    return $title_parts;
});
"""
    
    # Check if title function already exists
    if "tradingrobotplug_page_title" in content or "Trading Robot Plug" in content:
        print(f"✅ Page title function may already exist")
        return True
    
    # Append title function
    if not content.endswith("\n"):
        content += "\n"
    content += "\n" + title_function + "\n"
    
    functions_file.write_text(content, encoding="utf-8")
    print(f"✅ Added page title function")
    return True

def main():
    print(f"🔧 Fixing Trading Robot Plug Quality Issues...\n")
    
    project_root = Path(__file__).parent.parent
    theme_path = project_root / "websites" / SITE_NAME / "wp" / "wp-content" / "themes" / THEME_NAME
    
    if not theme_path.exists():
        print(f"❌ Theme directory not found: {theme_path}")
        sys.exit(1)
    
    fixes_applied = []
    
    # Fix navigation typo
    header_file = theme_path / "header.php"
    if fix_navigation_typo(header_file):
        fixes_applied.append("Navigation typo")
    
    # Fix footer typo
    footer_file = theme_path / "footer.php"
    if fix_footer_typo(footer_file):
        fixes_applied.append("Footer typo")
    
    # Fix page title
    functions_file = theme_path / "functions.php"
    if fix_page_title(functions_file):
        fixes_applied.append("Page title")
    
    print(f"\n✅ Quality fixes complete!")
    print(f"📊 Fixes applied: {len(fixes_applied)}")
    for fix in fixes_applied:
        print(f"   - {fix}")
    
    if fixes_applied:
        print(f"\n📋 Next Step: Deploy fixes to WordPress")
        print(f"   Run: python tools/deploy_tradingrobotplug_quality_fixes.py")
    
    return len(fixes_applied) > 0

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)


