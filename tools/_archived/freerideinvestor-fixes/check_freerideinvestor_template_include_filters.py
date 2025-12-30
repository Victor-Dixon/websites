#!/usr/bin/env python3
"""
Check freerideinvestor.com template_include Filters
===================================================

Examines template_include filters in functions.php that might be
preventing templates from loading correctly.

Author: Agent-1
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def analyze_template_include_filters(deployer):
    """Analyze template_include filters in functions.php."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("=" * 70)
    print("ANALYZING TEMPLATE_INCLUDE FILTERS")
    print("=" * 70)
    
    content = deployer.execute_command(f"cat {functions_php}")
    
    # Find all template_include filters
    pattern = r"add_filter\s*\(\s*['\"]template_include['\"].*?\);"
    matches = re.finditer(pattern, content, re.DOTALL | re.IGNORECASE)
    
    filters_found = []
    for match in matches:
        filter_code = match.group(0)
        filters_found.append(filter_code)
        
        # Extract the callback
        if "function" in filter_code:
            print("\n📋 Found template_include filter:")
            print(filter_code[:500])
            
            # Check for problematic patterns
            if "return" in filter_code:
                print("   ⚠️  This filter returns a value - might be changing template")
            if "null" in filter_code or "false" in filter_code:
                print("   ⚠️  This filter might return null/false - blocking template")
            if "empty" in filter_code:
                print("   ⚠️  This filter checks for empty - might block template")
    
    if not filters_found:
        print("⚠️  No template_include filters found (but grep found them)")
        print("   Searching for template_include in full content...")
        
        # Search for template_include in context
        lines = content.split('\n')
        for i, line in enumerate(lines):
            if 'template_include' in line.lower():
                print(f"\nLine {i+1}: {line}")
                # Show context
                start = max(0, i-5)
                end = min(len(lines), i+10)
                print("Context:")
                for j in range(start, end):
                    marker = ">>> " if j == i else "    "
                    print(f"{marker}{j+1:4d}: {lines[j]}")
    
    return filters_found


def check_template_redirect_hooks(deployer):
    """Check for template_redirect hooks."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("\n" + "=" * 70)
    print("CHECKING TEMPLATE_REDIRECT HOOKS")
    print("=" * 70)
    
    content = deployer.execute_command(f"cat {functions_php}")
    
    # Find template_redirect hooks
    pattern = r"add_action\s*\(\s*['\"]template_redirect['\"].*?\);"
    matches = re.finditer(pattern, content, re.DOTALL | re.IGNORECASE)
    
    redirects_found = []
    for match in matches:
        redirect_code = match.group(0)
        redirects_found.append(redirect_code)
        print("\n📋 Found template_redirect hook:")
        print(redirect_code[:500])
        print("   ⚠️  This hook fires before template loads - might redirect or exit")
    
    if not redirects_found:
        print("✅ No template_redirect hooks found")
    
    return redirects_found


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        filters = analyze_template_include_filters(deployer)
        redirects = check_template_redirect_hooks(deployer)
        
        print("\n" + "=" * 70)
        print("RECOMMENDATIONS")
        print("=" * 70)
        
        if filters:
            print("⚠️  template_include filters found - these might be modifying template loading")
            print("   Consider temporarily disabling them to test")
        
        if redirects:
            print("⚠️  template_redirect hooks found - these might be preventing template execution")
            print("   Consider temporarily disabling them to test")
        
        if not filters and not redirects:
            print("✅ No obvious template interference found")
            print("   Issue might be elsewhere - check template files directly")
        
        return 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(main())






