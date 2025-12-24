#!/usr/bin/env python3
"""Fix ALL syntax errors in functions.php - comprehensive fix."""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

deployer = SimpleWordPressDeployer("freerideinvestor.com", load_site_configs())
deployer.connect()

remote_path = "domains/freerideinvestor.com/public_html"
functions_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/functions.php"

# Read full file
print("Reading functions.php...")
content = deployer.execute_command(f"cat {functions_file}")
lines = content.split('\n')

# Fix ALL variables and function names with hyphens
print("Fixing all syntax errors...")
fixed_lines = []
fixes_applied = []

for i, line in enumerate(lines):
    line_num = i + 1
    original_line = line
    
    # Fix function names with hyphens
    import re
    if 'function' in line and '-' in line and '(' in line:
        # Find and replace function names with hyphens
        pattern = r'function\s+([a-zA-Z_][a-zA-Z0-9_-]*)\s*\('
        match = re.search(pattern, line)
        if match:
            func_name = match.group(1)
            if '-' in func_name:
                fixed_func_name = func_name.replace('-', '_')
                line = line.replace(f'function {func_name}(', f'function {fixed_func_name}(')
                fixes_applied.append(f"Line {line_num}: function {func_name} → {fixed_func_name}")
    
    # Fix variable names with hyphens (but not in strings)
    # Pattern: $variable-name (not inside quotes)
    var_pattern = r'\$([a-zA-Z_][a-zA-Z0-9_-]+)'
    matches = list(re.finditer(var_pattern, line))
    for match in reversed(matches):
        var_name = match.group(1)
        if '-' in var_name:
            # Check if it's inside a string (simple check)
            before_match = line[:match.start()]
            quote_count = before_match.count("'") + before_match.count('"')
            # If even number of quotes before, we're outside strings
            if quote_count % 2 == 0:
                fixed_var_name = var_name.replace('-', '_')
                line = line[:match.start()] + f'${fixed_var_name}' + line[match.end():]
                fixes_applied.append(f"Line {line_num}: ${var_name} → ${fixed_var_name}")
    
    # Fix function references in strings (add_action, add_filter)
    if 'add_action' in line or 'add_filter' in line:
        if "'freerideinvestor_add_mcp-test" in line:
            line = line.replace("'freerideinvestor_add_mcp-test-slug_menu'", "'freerideinvestor_add_mcp_test_slug_menu'")
            fixes_applied.append(f"Line {line_num}: Fixed function reference in hook")
    
    fixed_lines.append(line)

if fixes_applied:
    print(f"\n✅ Applied {len(fixes_applied)} fixes:")
    for fix in fixes_applied:
        print(f"   {fix}")
else:
    print("\n⚠️  No fixes applied - checking if already fixed...")

# Save and deploy
fixed_content = '\n'.join(fixed_lines)
local_file = Path(__file__).parent.parent / "docs" / "theme_functions_final_fixed.php"
local_file.parent.mkdir(parents=True, exist_ok=True)
local_file.write_text(fixed_content, encoding='utf-8')

print(f"\n💾 Saved fixed file to: {local_file}")
print("🚀 Deploying...")
deployer.deploy_file(local_file, functions_file)

print("\n🔍 Verifying syntax...")
syntax = deployer.execute_command(f"php -l {functions_file} 2>&1")
print(f"Syntax check result:\n{syntax}")

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
    except Exception as e:
        print(f"   ⚠️  Could not test: {e}")
else:
    print("\n❌ Syntax error still exists")
    # Try to get line number
    if "on line" in syntax:
        error_line = syntax.split("on line")[1].split()[0]
        print(f"   Error on line: {error_line}")
        # Show that line
        line_content = deployer.execute_command(f"sed -n '{error_line}p' {functions_file}")
        print(f"   Line content: {line_content[:100]}")

deployer.disconnect()

