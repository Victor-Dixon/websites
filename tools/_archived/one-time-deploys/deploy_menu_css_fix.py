#!/usr/bin/env python3
"""
Deploy Menu CSS Fix to freerideinvestor.com
Deploys all necessary files for the menu styling fix
"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    print("=" * 60)
    print("DEPLOYING MENU CSS FIX")
    print("=" * 60)
    
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    base = Path('D:/websites/websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern')
    
    files_to_deploy = [
        (base / 'inc/assets.php', f'{remote_path}/inc/assets.php'),
        (base / 'style.css', f'{remote_path}/style.css'),
        (base / 'functions.php', f'{remote_path}/functions.php'),
        (base / 'css/styles/components/_navigation.css', f'{remote_path}/css/styles/components/_navigation.css'),
        (base / 'css/styles/layout/_header-footer.css', f'{remote_path}/css/styles/layout/_header-footer.css'),
        (base / 'css/styles/utilities/_responsive-enhancements.css', f'{remote_path}/css/styles/utilities/_responsive-enhancements.css'),
    ]
    
    print("\n📦 Deploying files...")
    for local_file, remote_file in files_to_deploy:
        if local_file.exists():
            try:
                deployer.deploy_file(str(local_file), remote_file)
                print(f"  ✅ Deployed: {local_file.name}")
            except Exception as e:
                print(f"  ❌ Failed: {local_file.name} - {e}")
        else:
            print(f"  ⚠️  Not found: {local_file}")
    
    print("\n🔄 Clearing cache...")
    result = deployer.execute_command(f'cd {remote_path} && wp cache flush --allow-root 2>&1')
    print(f"  {result}")
    
    deployer.disconnect()
    
    print("\n" + "=" * 60)
    print("DEPLOYMENT COMPLETE")
    print("=" * 60)

if __name__ == '__main__':
    main()

