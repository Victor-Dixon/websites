#!/usr/bin/env python3
"""Verify CSS files exist locally and deploy them to remote"""
import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

base = Path('D:/websites/websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern')
remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'

css_files = [
    'css/styles/components/_navigation.css',
    'css/styles/layout/_header-footer.css',
    'css/styles/utilities/_responsive-enhancements.css',
    'css/custom.css',
]

print("=" * 70)
print("VERIFYING & DEPLOYING CSS FILES")
print("=" * 70)

# Check local files
print("\n📁 Checking local CSS files...")
for css_file in css_files:
    local_file = base / css_file
    if local_file.exists():
        size = local_file.stat().st_size
        print(f"  ✅ EXISTS: {css_file} ({size} bytes)")
    else:
        print(f"  ❌ NOT FOUND: {css_file}")

# Deploy files
deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
deployer.connect()

print("\n🚀 Deploying CSS files...")
for css_file in css_files:
    local_file = base / css_file
    if local_file.exists():
        remote_file = f'{remote_path}/{css_file}'
        try:
            deployer.deploy_file(str(local_file), remote_file)
            print(f"  ✅ Deployed: {css_file}")
        except Exception as e:
            print(f"  ❌ Failed: {css_file} - {e}")
    else:
        print(f"  ⚠️  Skipped: {css_file} (not found locally)")

print("\n🔍 Verifying deployment...")
for css_file in css_files:
    full_path = f'{remote_path}/{css_file}'
    result = deployer.execute_command(f'test -f {full_path} && echo "EXISTS" || echo "NOT_FOUND"')
    status = "✅" if "EXISTS" in result else "❌"
    print(f"  {status} {css_file}")

# Clear cache
print("\n🔄 Clearing cache...")
deployer.execute_command(f'cd {remote_path} && wp cache flush --allow-root 2>&1')
print("  ✅ Cache cleared")

deployer.disconnect()

print("\n" + "=" * 70)
print("DEPLOYMENT COMPLETE")
print("=" * 70)

