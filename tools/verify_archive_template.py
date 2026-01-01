#!/usr/bin/env python3
"""
Verify Archive Template Deployment
===================================

Verifies archive.php is correctly deployed and has proper content.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def verify_archive():
    """Verify archive.php template."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🔍 VERIFYING ARCHIVE.PHP TEMPLATE: {site_name}")
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
        archive_file = f"{remote_path}/wp-content/themes/freerideinvestor-modern/archive.php"
        
        print("1️⃣ Checking archive.php file...")
        if not deployer.sftp:
            print("❌ SFTP not connected")
            return False
        
        try:
            with deployer.sftp.open(archive_file, 'r') as f:
                content = f.read().decode('utf-8')
            
            # Check for key WordPress functions
            checks = {
                'get_header()': 'get_header()' in content,
                'have_posts()': 'have_posts()' in content,
                'the_post()': 'the_post()' in content,
                'the_posts_pagination()': 'the_posts_pagination' in content,
                'get_footer()': 'get_footer()' in content,
            }
            
            print(f"   File size: {len(content)} bytes")
            print(f"   File exists: ✅")
            print()
            print("   Key functions check:")
            for check, result in checks.items():
                status = "✅" if result else "❌"
                print(f"   {status} {check}")
            
            if all(checks.values()):
                print()
                print("   ✅ archive.php looks correct!")
            else:
                print()
                print("   ⚠️  Some functions are missing")
                
        except FileNotFoundError:
            print("   ❌ archive.php not found on server!")
            return False
        
        # Check file permissions
        print()
        print("2️⃣ Checking file permissions...")
        perm_cmd = f"stat -c '%a' {archive_file} 2>&1"
        perms = deployer.execute_command(perm_cmd).strip()
        print(f"   Permissions: {perms}")
        
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
    success = verify_archive()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

