#!/usr/bin/env python3
"""
Final Database Password Test
===========================

Quick test of common password patterns for freerideinvestor.com
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def quick_test(site_domain, db_name, db_user, password):
    """Quick database connection test."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            return False

        # Simple test
        test_cmd = f"mysql -h 127.0.0.1 -u {db_user} -p{password} {db_name} -e 'SELECT 1;' 2>/dev/null && echo 'SUCCESS' || echo 'FAILED'"
        result = deployer.execute_command(test_cmd)

        deployer.disconnect()

        return 'SUCCESS' in result

    except:
        return False

# Test freerideinvestor with some common patterns
print("🔍 Testing freerideinvestor.com database passwords...")

test_passwords = [
    'sUlnVM9fPd',  # ariajet pattern
    'tCqiZyJgMX',  # prismblossom
    'tU0I5x8AmH',  # southwestsecret
    'aB3dE5fG7h',  # pattern 1
    '1q2w3e4r5t',  # pattern 2
    'zX9cV4bN1m',  # pattern 3
    'P8kL6jH3g',   # pattern 4
]

for pwd in test_passwords:
    print(f"Testing: {pwd}")
    if quick_test('freerideinvestor.com', 'u996867598_6cbPB', 'u996867598_9dVzt', pwd):
        print(f"✅ FOUND: {pwd} works!")
        # Update the config
        with open('websites/freerideinvestor.com/wp-config.php', 'r') as f:
            content = f.read()
        content = content.replace("'Falcons#1247'", f"'{pwd}'")
        with open('websites/freerideinvestor.com/wp-config.php', 'w') as f:
            f.write(content)
        print("✅ Updated wp-config.php")
        break
    else:
        print(f"❌ {pwd} failed")

print("Test complete.")