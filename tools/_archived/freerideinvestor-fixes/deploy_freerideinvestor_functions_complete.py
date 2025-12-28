#!/usr/bin/env python3
"""
Deploy Complete functions.php to freerideinvestor.com
======================================================

Deploys the complete functions.php file with menu fix included.
"""

import sys
from pathlib import Path
from datetime import datetime

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "freerideinvestor.com"

def main():
    """Main execution."""
    print("=" * 70)
    print("DEPLOY COMPLETE functions.php TO FREERIDEINVESTOR.COM")
    print("=" * 70)
    print()
    
    # Read complete functions.php
    functions_file = Path(__file__).parent.parent / "docs" / "freerideinvestor" / "functions.php"
    if not functions_file.exists():
        print(f"❌ functions.php not found: {functions_file}")
        print("   Run: python tools/create_freerideinvestor_functions.php first")
        return
    
    with open(functions_file, 'r', encoding='utf-8') as f:
        functions_content = f.read()
    
    print(f"✅ Read functions.php ({len(functions_content)} characters)")
    print()
    
    # Load site configs
    site_configs = load_site_configs()
    if SITE_NAME not in site_configs:
        print(f"❌ Site {SITE_NAME} not found in configs")
        return
    
    deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    remote_base = site_configs[SITE_NAME].get('sftp', {}).get('remote_path', 
        'domains/freerideinvestor.com/public_html')
    
    print(f"📂 Remote path: {remote_base}")
    print()
    
    # Connect
    print("🔌 Connecting to server...")
    if not deployer.connect():
        print("❌ Failed to connect")
        return
    print("✅ Connected")
    print()
    
    # Create backup if file exists
    functions_path = f"{remote_base}/wp-content/themes/freerideinvestor-modern/functions.php"
    try:
        deployer.sftp.stat(functions_path)
        print("💾 Creating backup of existing functions.php...")
        backup_path = f"{functions_path}.backup_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        with deployer.sftp.open(functions_path, 'r') as src:
            with deployer.sftp.open(backup_path, 'w') as dst:
                dst.write(src.read())
        print(f"✅ Backup created: {backup_path}")
    except FileNotFoundError:
        print("ℹ️  No existing functions.php found (creating new file)")
    except Exception as e:
        print(f"⚠️  Backup warning: {e}")
    
    print()
    
    # Deploy
    print("🚀 Deploying functions.php...")
    try:
        with deployer.sftp.open(functions_path, 'w') as f:
            f.write(functions_content.encode('utf-8'))
        print("✅ functions.php deployed")
    except Exception as e:
        print(f"❌ Failed to deploy: {e}")
        return
    
    print()
    print("=" * 70)
    print("✅ DEPLOYMENT COMPLETE")
    print("=" * 70)
    print()
    print("Next steps:")
    print("1. Test site: https://freerideinvestor.com")
    print("2. Test menu toggle on mobile")
    print("3. Test navigation links")
    print("4. Verify menu matches theme style")
    print()
    print("⚠️  If site goes down, restore from backup or run:")
    print("   python tools/fix_freerideinvestor_500_emergency.py")

if __name__ == "__main__":
    main()

