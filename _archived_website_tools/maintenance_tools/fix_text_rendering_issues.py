#!/usr/bin/env python3
"""
Fix Text Rendering Issues
==========================

Fixes text spacing/rendering problems by:
1. Ensuring proper font-display strategy
2. Adding CSS to prevent text spacing issues
3. Checking for and fixing HTML source spacing issues

Author: Agent-1
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path
from typing import Dict, List, Optional

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def create_text_fix_css():
    """Create CSS to fix common text rendering issues."""
    css = """
/* Text Rendering Fixes - Agent-1 2025-12-22 */
/* Prevent text spacing issues and ensure proper font rendering */

/* Ensure proper text rendering for all text elements */
body, 
h1, h2, h3, h4, h5, h6,
p, a, span, div,
.site-title, .logo, .brand,
nav a, .menu a {
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    word-spacing: normal !important;
    letter-spacing: normal !important;
}

/* Prevent unwanted word breaks in domain names and brand names */
.site-title,
.logo,
.brand,
h1.site-title,
a.site-title,
.site-header a {
    word-break: keep-all !important;
    white-space: nowrap !important;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Ensure proper font loading fallback */
body {
    font-display: swap;
}

/* Fix for domain names that should not break */
a[href*="freerideinvestor.com"],
a[href*="crosbyultimateevents.com"],
a[href*="houstonsipqueen.com"],
a[href*="tradingrobotplug.com"],
a[href*="ariajet.site"] {
    word-break: keep-all !important;
    white-space: nowrap !important;
}
"""
    return css


def add_css_to_theme(site_key, theme_name, deployer):
    """Add CSS fix to theme's style.css."""
    remote_path = "domains/{}/public_html".format(site_key)
    css_path = f"{remote_path}/wp-content/themes/{theme_name}/style.css"
    
    print(f"\n📝 Adding text rendering fixes to style.css...")
    
    # Check if file exists
    exists = deployer.execute_command(f"test -f {css_path} && echo 'EXISTS' || echo 'MISSING'")
    if "MISSING" in exists:
        print(f"   ⚠️  style.css not found, skipping")
        return False
    
    # Read current content
    content = deployer.execute_command(f"cat {css_path}")
    
    # Check if fix already exists
    if "Text Rendering Fixes - Agent-1" in content:
        print(f"   ✅ Text rendering fixes already present")
        return True
    
    # Add CSS fix at the end
    fix_css = create_text_fix_css()
    new_content = content + "\n\n" + fix_css
    
    # Save locally
    local_file = Path(__file__).parent.parent / "docs" / f"{site_key.replace('.', '_')}_text_fix.css"
    local_file.write_text(new_content, encoding='utf-8')
    
    # Deploy
    success = deployer.deploy_file(local_file, css_path)
    
    if success:
        print(f"   ✅ Text rendering fixes added to style.css")
        return True
    else:
        print(f"   ❌ Failed to deploy fixes")
        return False


def check_html_source_spacing(site_key, deployer):
    """Check if HTML source has actual spaces in domain names."""
    import requests
    
    url = f"https://{site_key}"
    try:
        r = requests.get(url, timeout=10)
        html = r.text
        
        # Check for common broken patterns
        patterns = {
            "freerideinvestor": r"freerideinve\s+tor",
            "crosbyultimateevents": r"cro\s+byultimateevent\s*",
            "houstonsipqueen": r"hou\s+ton\s+ipqueen",
            "tradingrobotplug": r"tradingrobotplug",  # Check for any spacing
        }
        
        issues = []
        for domain, pattern in patterns.items():
            if domain in site_key:
                matches = re.findall(pattern, html, re.IGNORECASE)
                if matches:
                    issues.append(f"Found spacing in HTML source for {domain}")
        
        return issues
        
    except Exception as e:
        print(f"   ⚠️  Could not check HTML source: {e}")
        return []


def ensure_font_display_swap(deployer, site_key, theme_name):
    """Ensure Google Fonts use font-display: swap."""
    remote_path = "domains/{}/public_html".format(site_key)
    header_path = f"{remote_path}/wp-content/themes/{theme_name}/header.php"
    
    print(f"\n📝 Ensuring font-display: swap for Google Fonts...")
    
    exists = deployer.execute_command(f"test -f {header_path} && echo 'EXISTS' || echo 'MISSING'")
    if "MISSING" in exists:
        print(f"   ⚠️  header.php not found, skipping")
        return False
    
    content = deployer.execute_command(f"cat {header_path}")
    
    # Check if Google Fonts link exists
    if 'fonts.googleapis.com' not in content:
        print(f"   ℹ️  No Google Fonts found in header.php")
        return False
    
    # Check if font-display=swap is already present
    if 'display=swap' in content:
        print(f"   ✅ font-display=swap already present")
        return True
    
    # Add display=swap to Google Fonts URLs
    modified = re.sub(
        r'(fonts\.googleapis\.com/css2\?[^"]+)',
        r'\1&display=swap',
        content
    )
    
    if modified != content:
        # Save locally
        local_file = Path(__file__).parent.parent / "docs" / f"{site_key.replace('.', '_')}_header_fixed.php"
        local_file.write_text(modified, encoding='utf-8')
        
        # Deploy
        success = deployer.deploy_file(local_file, header_path)
        
        if success:
            print(f"   ✅ Added font-display=swap to Google Fonts")
            return True
        else:
            print(f"   ❌ Failed to deploy header fix")
            return False
    
    return False


def fix_site(site_key, theme_name=None):
    """Fix text rendering issues for a site."""
    print("\n" + "=" * 70)
    print(f"FIXING TEXT RENDERING: {site_key}")
    print("=" * 70)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_key, site_configs)
    
    if not deployer.connect():
        print(f"❌ Failed to connect to {site_key}")
        return False
    
    try:
        # Detect theme if not provided
        if not theme_name:
            remote_path = "domains/{}/public_html".format(site_key)
            active_theme = deployer.execute_command(
                f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>/dev/null || "
                f"ls wp-content/themes/ | head -1"
            ).strip()
            theme_name = active_theme.split('\n')[0] if active_theme else "default"
            print(f"📌 Using theme: {theme_name}")
        
        # Check HTML source first
        html_issues = check_html_source_spacing(site_key, deployer)
        if html_issues:
            print(f"\n⚠️  HTML source issues found:")
            for issue in html_issues:
                print(f"   - {issue}")
            print(f"   ⚠️  These need manual fixes in WordPress admin or template files")
        
        # Add CSS fixes
        css_success = add_css_to_theme(site_key, theme_name, deployer)
        
        # Ensure font-display: swap
        font_success = ensure_font_display_swap(deployer, site_key, theme_name)
        
        if css_success or font_success:
            print(f"\n✅ Fixes applied for {site_key}")
            print(f"   ⏳ Please clear cache and test the site")
            return True
        else:
            print(f"\n⚠️  No fixes could be applied automatically")
            return False
            
    except Exception as e:
        print(f"❌ Error fixing {site_key}: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    import sys
    
    if len(sys.argv) > 1:
        site_key = sys.argv[1]
        theme_name = sys.argv[2] if len(sys.argv) > 2 else None
        fix_site(site_key, theme_name)
    else:
        # Fix all affected sites
        sites_to_fix = [
            ("freerideinvestor.com", "freerideinvestor-modern"),
            ("crosbyultimateevents.com", None),
            ("houstonsipqueen.com", None),
            ("tradingrobotplug.com", None),
            ("ariajet.site", None),
        ]
        
        print("=" * 70)
        print("FIXING TEXT RENDERING ISSUES")
        print("=" * 70)
        
        results = {}
        for site_key, theme_name in sites_to_fix:
            results[site_key] = fix_site(site_key, theme_name)
        
        # Summary
        print("\n" + "=" * 70)
        print("SUMMARY")
        print("=" * 70)
        
        fixed = sum(1 for v in results.values() if v)
        total = len(results)
        
        print(f"\nSites fixed: {fixed}/{total}")
        for site_key, success in results.items():
            status = "✅ Fixed" if success else "⚠️  Needs attention"
            print(f"   {site_key}: {status}")
        
        if fixed == total:
            print("\n✅ All fixes applied! Please test sites and clear cache.")
        else:
            print("\n⚠️  Some sites need manual intervention.")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())






