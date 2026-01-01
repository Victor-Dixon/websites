#!/usr/bin/env python3
"""
Check freerideinvestor.com CSS and JavaScript Issues
===================================================

Checks for CSS rules or JavaScript that might be hiding the main content.

Author: Agent-1
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_css_for_hiding_rules(deployer):
    """Check CSS files for rules that hide main content."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    
    print("=" * 70)
    print("CHECKING CSS FOR CONTENT-HIDING RULES")
    print("=" * 70)
    
    css_files = ["style.css", "custom.css", "main.css"]
    issues = []
    
    for css_file in css_files:
        css_path = f"{remote_path}/wp-content/themes/{theme_name}/{css_file}"
        exists = deployer.execute_command(f"test -f {css_path} && echo 'EXISTS' || echo 'MISSING'")
        
        if "EXISTS" in exists:
            print(f"\n📄 Checking {css_file}...")
            content = deployer.execute_command(f"cat {css_path}")
            
            # Check for hiding patterns
            hiding_patterns = [
                (r"\.site-main\s*\{[^}]*display\s*:\s*none", "site-main display:none"),
                (r"main\s*\{[^}]*display\s*:\s*none", "main display:none"),
                (r"#main\s*\{[^}]*display\s*:\s*none", "#main display:none"),
                (r"\.site-main\s*\{[^}]*visibility\s*:\s*hidden", "site-main visibility:hidden"),
                (r"main\s*\{[^}]*visibility\s*:\s*hidden", "main visibility:hidden"),
                (r"\.site-main\s*\{[^}]*opacity\s*:\s*0", "site-main opacity:0"),
                (r"main\s*\{[^}]*opacity\s*:\s*0", "main opacity:0"),
                (r"\.front-page\s*\{[^}]*display\s*:\s*none", "front-page display:none"),
                (r"\.no-content\s*\{[^}]*display\s*:\s*none", "no-content display:none"),
            ]
            
            for pattern, description in hiding_patterns:
                matches = re.finditer(pattern, content, re.IGNORECASE | re.MULTILINE)
                for match in matches:
                    line_num = content[:match.start()].count('\n') + 1
                    issues.append({
                        "file": css_file,
                        "line": line_num,
                        "pattern": description,
                        "match": match.group(0)[:100]
                    })
                    print(f"   ⚠️  Found: {description} (line {line_num})")
                    print(f"      {match.group(0)[:80]}")
    
    return issues


def check_javascript_files(deployer):
    """Check JavaScript files for content-loading issues."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    
    print("\n" + "=" * 70)
    print("CHECKING JAVASCRIPT FILES")
    print("=" * 70)
    
    js_files = deployer.execute_command(
        f"find {remote_path}/wp-content/themes/{theme_name} -name '*.js' -type f 2>/dev/null | head -10"
    )
    
    if js_files.strip():
        print("JavaScript files found:")
        for js_file in js_files.strip().split('\n'):
            if js_file:
                print(f"   - {js_file}")
                # Check if it manipulates main content
                content = deployer.execute_command(f"cat {js_file} 2>/dev/null | head -50")
                if "getElementById('main')" in content or "querySelector('main')" in content or ".site-main" in content:
                    print(f"      ⚠️  This file manipulates main content")
    else:
        print("No JavaScript files found in theme")


def check_functions_php_for_issues(deployer):
    """Check functions.php for issues that might prevent content from rendering."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("\n" + "=" * 70)
    print("CHECKING FUNCTIONS.PHP FOR ISSUES")
    print("=" * 70)
    
    content = deployer.execute_command(f"cat {functions_php}")
    
    # Check for problematic patterns
    issues = []
    
    # Check for filters that might remove content
    if "remove_action('wp_head'" in content or "remove_filter" in content:
        print("⚠️  Found remove_action or remove_filter - might affect content")
    
    # Check for content filters
    if "the_content" in content:
        print("✅ Found the_content filter - checking for issues...")
        # Look for filters that might strip content
        if "strip_tags" in content or "wp_strip_all_tags" in content:
            print("   ⚠️  Found strip_tags - might be removing content")
    
    # Check for query modifications
    if "pre_get_posts" in content:
        print("⚠️  Found pre_get_posts hook - might be modifying queries")
        # Check if it's excluding posts
        if "set('post__in'" in content or "set('post__not_in'" in content:
            print("   ⚠️  Query might be excluding posts")
    
    return issues


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        css_issues = check_css_for_hiding_rules(deployer)
        check_javascript_files(deployer)
        func_issues = check_functions_php_for_issues(deployer)
        
        print("\n" + "=" * 70)
        print("SUMMARY")
        print("=" * 70)
        
        if css_issues:
            print(f"⚠️  Found {len(css_issues)} CSS issues that might hide content")
            print("\nRecommended fixes:")
            for issue in css_issues:
                print(f"   - {issue['file']} line {issue['line']}: {issue['pattern']}")
        else:
            print("✅ No CSS hiding rules found")
        
        if func_issues:
            print(f"⚠️  Found {len(func_issues)} functions.php issues")
        else:
            print("✅ No obvious functions.php issues")
        
        print("\n💡 Next steps:")
        print("   1. If CSS issues found, remove or comment out hiding rules")
        print("   2. Check browser console for JavaScript errors")
        print("   3. Verify WordPress posts exist and are published")
        print("   4. Check if template-parts/content-front-page.php exists and works")
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()
    
    return 0


if __name__ == "__main__":
    sys.exit(main())


