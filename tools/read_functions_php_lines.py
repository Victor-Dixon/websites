#!/usr/bin/env python3
"""Read specific lines from functions.php to debug syntax error."""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

deployer = SimpleWordPressDeployer("freerideinvestor.com", load_site_configs())
deployer.connect()

remote_path = "domains/freerideinvestor.com/public_html"
functions_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/functions.php"

# Read lines 200-230
print("Reading lines 200-230 from functions.php:")
command = f"sed -n '200,230p' {functions_file}"
result = deployer.execute_command(command)

print("\nRaw output:")
print(result)

print("\nParsed lines:")
lines = result.split('\n')
for i, line in enumerate(lines):
    print(f"{200+i:4d}: {repr(line)}")

# Check for incomplete lines
print("\nChecking for incomplete lines (missing semicolons, unclosed strings):")
for i, line in enumerate(lines):
    line_num = 200 + i
    stripped = line.strip()
    if stripped and not stripped.startswith('//'):
        # Check if line looks incomplete
        if not stripped.endswith(';') and not stripped.endswith('}') and not stripped.endswith('{') and 'function' not in stripped and 'if' not in stripped and 'return' not in stripped:
            if 'add_action' in stripped or 'add_filter' in stripped:
                # These should end with semicolon
                if not stripped.endswith(';'):
                    print(f"  ⚠️  Line {line_num}: May be missing semicolon: {stripped[:60]}")

deployer.disconnect()






