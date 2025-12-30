#!/usr/bin/env python3
"""
Fix freerideinvestor.com Empty Content Area
==========================================

Diagnoses and fixes the empty content area issue on freerideinvestor.com.
Checks WordPress homepage settings, theme template hierarchy, and content visibility.

Agent-8: SSOT & System Integration Specialist
Task: Fix freerideinvestor.com empty content area (CRITICAL)
"""

import json
import sys
from pathlib import Path
from datetime import datetime

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
# Add main repo tools to path
MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
if MAIN_REPO_TOOLS.exists():
    sys.path.insert(0, str(MAIN_REPO_TOOLS))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False
    print("❌ SimpleWordPressDeployer not available")
    print("   Tried paths:")
    print(f"   - {Path(__file__).parent.parent / 'ops' / 'deployment'}")
    print(f"   - {MAIN_REPO_TOOLS}")
    sys.exit(1)

def check_homepage_settings(deployer, wp_path):
    """Check WordPress homepage settings via WP-CLI."""
    print("🔍 Checking WordPress homepage settings...")
    
    # Check if homepage is set to static page or blog posts
    result = deployer.execute_command(f"wp option get show_on_front --path={wp_path}")
    show_on_front = result.strip() if result else None
    
    if show_on_front == "page":
        # Get the page ID set as homepage
        page_id_result = deployer.execute_command(f"wp option get page_on_front --path={wp_path}")
        page_id = page_id_result.strip() if page_id_result else None
        
        if page_id and page_id.isdigit():
            # Get page details
            page_result = deployer.execute_command(f"wp post get {page_id} --field=title,status,content --path={wp_path}")
            return {
                "show_on_front": "page",
                "page_id": page_id,
                "page_info": page_result.strip() if page_result else None
            }
        else:
            return {
                "show_on_front": "page",
                "page_id": None,
                "issue": "Homepage set to static page but no page ID configured"
            }
    else:
        return {
            "show_on_front": "posts" if show_on_front == "posts" else show_on_front,
            "note": "Homepage set to blog posts (should use index.php)"
        }

def check_theme_template_files(deployer, theme_name, wp_path):
    """Check if required theme template files exist."""
    print(f"🔍 Checking theme template files for {theme_name}...")
    
    templates = {
        "front-page.php": False,
        "home.php": False,
        "index.php": False,
        "page.php": False
    }
    
    for template in templates.keys():
        # Use test command to check if file exists
        template_path = f"{wp_path}/wp-content/themes/{theme_name}/{template}"
        result = deployer.execute_command(f"test -f {template_path} && echo 'EXISTS' || echo 'MISSING'")
        if "EXISTS" in result:
            templates[template] = True
            print(f"  ✅ {template} exists")
        else:
            print(f"  ❌ {template} missing")
    
    return templates

def check_active_theme(deployer, wp_path):
    """Check which theme is active."""
    print("🔍 Checking active theme...")
    
    result = deployer.execute_command(f"wp theme list --status=active --field=name --path={wp_path}")
    active_theme = result.strip() if result else None
    
    if active_theme:
        print(f"  ✅ Active theme: {active_theme}")
    else:
        print("  ⚠️  Could not determine active theme")
    
    return active_theme

def check_css_hiding_content(deployer, theme_name, wp_path):
    """Check if CSS might be hiding content."""
    print("🔍 Checking for CSS that might hide content...")
    
    css_files = ["style.css", "custom.css"]
    issues = []
    
    for css_file in css_files:
        css_path = f"{wp_path}/wp-content/themes/{theme_name}/{css_file}"
        # Check if file exists
        check_result = deployer.execute_command(f"test -f {css_path} && echo 'EXISTS' || echo 'MISSING'")
        if "EXISTS" in check_result:
            # Read file content
            css_content = deployer.execute_command(f"cat {css_path}")
            if css_content:
                hiding_patterns = [
                    "display: none",
                    "visibility: hidden",
                    "opacity: 0",
                    "#main-content { display: none",
                    ".site-main { display: none",
                    "main { display: none"
                ]
                
                for pattern in hiding_patterns:
                    if pattern.lower() in css_content.lower():
                        issues.append({
                            "file": css_file,
                            "pattern": pattern,
                            "note": "Found CSS that might hide content"
                        })
    
    return issues


def fix_homepage_settings(deployer, wp_path):
    """Fix homepage settings to use blog posts if static page is empty."""
    print("🔍 Checking if homepage settings need fixing...")
    
    settings = check_homepage_settings(deployer, wp_path)
    
    if settings.get("show_on_front") == "page":
        page_id = settings.get("page_id")
        
        if not page_id:
            # No page set - switch to blog posts
            print("  ⚠️  Homepage set to static page but no page configured")
            print("  🔧 Switching homepage to blog posts...")
            result = deployer.execute_command(f"wp option update show_on_front posts --path={wp_path}")
            if result and "error" not in result.lower():
                print("  ✅ Homepage switched to blog posts")
                return True
        else:
            # Check if page has content
            page_content = deployer.execute_command(f"wp post get {page_id} --field=content --path={wp_path}")
            if page_content and len(page_content.strip()) < 50:
                print(f"  ⚠️  Page {page_id} has minimal content")
                print("  🔧 Switching homepage to blog posts...")
                result = deployer.execute_command(f"wp option update show_on_front posts --path={wp_path}")
                if result and "error" not in result.lower():
                    print("  ✅ Homepage switched to blog posts")
                    return True
    
    return False

def main():
    """Main execution."""
    print("🔧 Fixing freerideinvestor.com empty content area...\n")
    
    # Load site config
    site_configs = load_site_configs()
    if "freerideinvestor.com" not in site_configs:
        print("❌ freerideinvestor.com not found in site configs")
        sys.exit(1)
    
    site_config = site_configs["freerideinvestor.com"]
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect to freerideinvestor.com")
        sys.exit(1)
    
    try:
        # Get WordPress path
        wp_path = deployer.remote_path
        if not wp_path:
            wp_path = f"domains/freerideinvestor.com/public_html"
        
        # Ensure absolute path
        if not wp_path.startswith('/'):
            username = site_config.get('username') or site_config.get('sftp', {}).get('username', '')
            if username:
                wp_path = f"/home/{username}/{wp_path}"
        
        print(f"📁 WordPress path: {wp_path}\n")
        
        # Check active theme
        active_theme = check_active_theme(deployer, wp_path)
        if not active_theme or "error" in active_theme.lower():
            print("❌ Could not determine active theme")
            print(f"   Result: {active_theme}")
            sys.exit(1)
        
        # Check homepage settings
        homepage_settings = check_homepage_settings(deployer, wp_path)
        print(f"\n📊 Homepage Settings:")
        print(f"   Show on front: {homepage_settings.get('show_on_front')}")
        if homepage_settings.get('page_id'):
            print(f"   Page ID: {homepage_settings.get('page_id')}")
        
        # Check template files
        templates = check_theme_template_files(deployer, active_theme, wp_path)
        print(f"\n📊 Template Files:")
        for template, exists in templates.items():
            status = "✅" if exists else "❌"
            print(f"   {status} {template}")
        
        # Check CSS issues
        css_issues = check_css_hiding_content(deployer, active_theme, wp_path)
        if css_issues:
            print(f"\n⚠️  CSS Issues Found:")
            for issue in css_issues:
                print(f"   - {issue['file']}: {issue['pattern']}")
        else:
            print(f"\n✅ No CSS hiding issues found")
        
        # Fix homepage settings if needed
        print(f"\n🔧 Applying fixes...")
        homepage_fixed = fix_homepage_settings(deployer, wp_path)
        
        # Note: front-page.php creation removed - will use index.php if homepage is set to posts
        
        # Summary
        print(f"\n{'='*60}")
        print("Fix Summary")
        print("="*60)
        if homepage_fixed:
            print("✅ Homepage settings fixed (switched to blog posts)")
        if templates.get("front-page.php") or (homepage_settings.get("show_on_front") == "posts" and templates.get("index.php")):
            print("✅ Template files available")
        if not css_issues:
            print("✅ No CSS hiding issues")
        
        print("\n✅ Fix complete. Please verify the site displays content correctly.")
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    main()





