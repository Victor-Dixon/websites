#!/usr/bin/env python3
"""Verify remote configuration"""
import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
deployer.connect()
remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'

print("=== Remote File Check ===")
files = [
    'inc/assets.php',
    'inc/assets/enqueue.php',
    'functions.php',
    'css/styles/components/_navigation.css',
    'css/styles/layout/_header-footer.css',
    'style.css'
]

for f in files:
    result = deployer.execute_command(f'test -f {remote_path}/{f} && echo "EXISTS" || echo "NOT_FOUND"')
    status = "✅" if "EXISTS" in result else "❌"
    print(f"{status} {f}")

print("\n=== inc/assets.php content (if exists) ===")
result = deployer.execute_command(f'cat {remote_path}/inc/assets.php 2>&1 | head -35')
print(result)

deployer.disconnect()

