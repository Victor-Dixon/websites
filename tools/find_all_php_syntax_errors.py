#!/usr/bin/env python3
"""Find all PHP syntax errors in functions.php by checking sections."""

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
print("Reading full functions.php...")
content = deployer.execute_command(f"cat {functions_file}")
lines = content.split('\n')

# Check syntax of file in sections to find error location
print(f"\nTotal lines: {len(lines)}")
print("Checking syntax in sections...")

# Check first 100 lines
section1 = '\n'.join(lines[:100])
temp1 = Path("/tmp/functions_section1.php")
temp1.write_text(section1)
result1 = deployer.execute_command(f"php -l /tmp/functions_section1.php 2>&1" if deployer.execute_command("test -f /tmp/functions_section1.php && echo 'EXISTS' || echo 'NOT_EXISTS'") == "EXISTS" else "echo 'Cannot test section 1'")
print(f"Lines 1-100: {result1[:100]}")

# Check lines 100-250
section2 = '\n'.join(lines[100:250])
print(f"\nLines 200-220 (around known issue):")
for i in range(199, min(221, len(lines))):
    print(f"{i+1:4d}: {lines[i][:80]}")

# Try to find syntax errors by checking for common issues
print("\nScanning for common PHP syntax issues...")
issues = []
for i, line in enumerate(lines):
    line_num = i + 1
    # Check for unclosed strings, brackets, etc.
    if line_num >= 200 and line_num <= 230:
        if line.count('(') != line.count(')'):
            issues.append(f"Line {line_num}: Unmatched parentheses")
        if line.count('[') != line.count(']'):
            issues.append(f"Line {line_num}: Unmatched brackets")
        if line.count('{') != line.count('}'):
            issues.append(f"Line {line_num}: Unmatched braces")

if issues:
    print("Potential issues found:")
    for issue in issues:
        print(f"  - {issue}")
else:
    print("No obvious syntax issues in lines 200-230")

# Final syntax check
print("\nFinal syntax check:")
syntax = deployer.execute_command(f"php -l {functions_file} 2>&1")
print(syntax)

deployer.disconnect()






