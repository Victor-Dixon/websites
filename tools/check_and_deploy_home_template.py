#!/usr/bin/env python3
"""
Check and Deploy home.php Template
===================================

When a page is set as Posts page, WordPress uses home.php instead of archive.php.
We need to deploy home.php based on archive.php.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def deploy_home_template():
    """Deploy home.php template based on archive.php."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔧 DEPLOYING HOME.PHP TEMPLATE: {site_name}")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        
        # Read local archive.php
        local_archive = Path(__file__).parent.parent / "websites" / site_name / "wp" / "wp-content" / "themes" / "freerideinvestor-modern" / "archive.php"
        
        if not local_archive.exists():
            print(f"❌ Local archive.php not found at {local_archive}")
            return False
        
        print("1️⃣ Reading local archive.php...")
        with open(local_archive, 'r', encoding='utf-8') as f:
            archive_content = f.read()
        
        # Modify header comment for home.php
        home_content = archive_content.replace(
            'Archive Template - Stunning Blog Archive',
            'Home Template - Stunning Blog Archive (Posts Page)'
        ).replace(
            'Archive Template',
            'Home Template'
        )
        
        print("2️⃣ Creating home.php from archive.php...")
        
        # Deploy home.php
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        remote_home = f"{theme_path}/home.php"
        
        print(f"3️⃣ Deploying home.php to {remote_home}...")
        with deployer.sftp.open(remote_home, 'w') as f:
            f.write(home_content.encode('utf-8'))
        
        print("✅ home.php deployed successfully")
        
        # Verify deployment
        print()
        print("4️⃣ Verifying deployment...")
        verify_cmd = f"test -f {remote_home} && echo 'EXISTS' || echo 'NOT_FOUND'"
        verify_result = deployer.execute_command(verify_cmd)
        
        if 'EXISTS' in verify_result:
            print("✅ home.php exists on server")
            
            # Check file size
            size_cmd = f"stat -c '%s' {remote_home}"
            file_size = deployer.execute_command(size_cmd).strip()
            print(f"   File size: {file_size} bytes")
        else:
            print("❌ home.php deployment failed")
            return False
        
        # Clear cache
        print()
        print("5️⃣ Clearing cache...")
        cache_cmd = f"cd {remote_path} && wp cache flush --allow-root 2>&1"
        deployer.execute_command(cache_cmd)
        print("✅ Cache cleared")
        
        print()
        print("=" * 70)
        print("✅ HOME.PHP DEPLOYMENT COMPLETE")
        print("=" * 70)
        print()
        print("💡 WordPress will now use home.php for the Posts page")
        print("   Visit https://freerideinvestor.com/blog/ to verify")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = deploy_home_template()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

