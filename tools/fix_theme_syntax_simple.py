#!/usr/bin/env python3
"""Simple fix for theme syntax error - read current state and fix properly."""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

deployer = SimpleWordPressDeployer("freerideinvestor.com", load_site_configs())
deployer.connect()

remote_path = "domains/freerideinvestor.com/public_html"
functions_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/functions.php"

# Read current file
content = deployer.execute_command(f"cat {functions_file}")
lines = content.split('\n')

# Show lines 205-220
print("Current lines 205-220:")
for i in range(204, min(220, len(lines))):
    print(f"{i+1:4d}: {lines[i]}")

# Fix the specific issues
fixed_lines = []
for i, line in enumerate(lines):
    line_num = i + 1
    
    if line_num == 209:
        # Function name (already fixed, but ensure it's correct)
        line = line.replace('freerideinvestor_add_mcp-test-slug_menu', 'freerideinvestor_add_mcp_test_slug_menu')
    elif line_num == 210:
        # Fix broken object property access: $args_> should be $args->
        line = line.replace('$args_>', '$args->')
    elif line_num == 211:
        # Variable (already fixed)
        line = line.replace('$mcp-test-slug_page', '$mcp_test_slug_page')
    elif line_num == 212:
        # Variables and fix broken object property access
        line = line.replace('$mcp-test-slug_url', '$mcp_test_slug_url')
        line = line.replace('$mcp-test-slug_page', '$mcp_test_slug_page')
        line = line.replace('$mcp_test_slug_page_>', '$mcp_test_slug_page->')
    elif line_num == 213:
        # Variable (already fixed)
        line = line.replace('$mcp-test-slug_url', '$mcp_test_slug_url')
    elif line_num == 217:
        # Function reference (already fixed)
        line = line.replace("'freerideinvestor_add_mcp-test-slug_menu'", "'freerideinvestor_add_mcp_test_slug_menu'")
    elif line_num == 223:
        # Variable with hyphens: $mcp-test-page_page → $mcp_test_page_page
        line = line.replace('$mcp-test-page_page', '$mcp_test_page_page')
        print(f"   ✅ Line {line_num}: Fixed variable name")
    elif line_num == 230:
        # Variable with hyphens: $mcp-test-page_page → $mcp_test_page_page
        line = line.replace('$mcp-test-page_page', '$mcp_test_page_page')
        print(f"   ✅ Line {line_num}: Fixed variable name")
    
    fixed_lines.append(line)

# Save and deploy
fixed_content = '\n'.join(fixed_lines)
local_file = Path(__file__).parent.parent / "docs" / "theme_functions_fixed.php"
local_file.write_text(fixed_content, encoding='utf-8')

print("\nDeploying...")
deployer.deploy_file(local_file, functions_file)

print("\nVerifying syntax...")
syntax = deployer.execute_command(f"php -l {functions_file} 2>&1")
print(f"Syntax check:\n{syntax}")

if "No syntax errors" in syntax or "syntax is OK" in syntax:
    print("\n✅ Syntax is valid! Testing site...")
    import requests
    try:
        r = requests.get("https://freerideinvestor.com", timeout=10)
        print(f"   Status: {r.status_code}")
        if r.status_code == 200:
            print("   ✅ Site is now accessible (HTTP 200)")
            print("   🎉 Fix successful!")
        else:
            print(f"   ⚠️  Site returned HTTP {r.status_code}")
            if "critical error" in r.text.lower():
                print("   📝 WordPress still showing error - check debug.log")
    except Exception as e:
        print(f"   ⚠️  Could not test: {e}")
else:
    print("\n❌ Syntax error still exists")
    # Try to get more details
    error_details = deployer.execute_command(f"php -l {functions_file} 2>&1 | grep -E 'Parse error|syntax error|on line' || echo 'No detailed error'")
    if error_details and "No detailed error" not in error_details:
        print(f"   Error details: {error_details}")

deployer.disconnect()

