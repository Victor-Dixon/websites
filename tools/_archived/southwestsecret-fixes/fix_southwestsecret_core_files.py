#!/usr/bin/env python3
"""
Fix WordPress Core File Issues
===============================

Fixes core file issues that may be causing the 500 error.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_core_files():
    """Fix WordPress core file issues."""
    print("=" * 70)
    print("🔧 FIXING CORE FILES: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        
        # Step 1: Remove wp-config-cache.php (shouldn't exist)
        print("📋 Step 1: Removing wp-config-cache.php...")
        print("-" * 70)
        
        cache_file = f"{remote_path}/wp-config-cache.php"
        check_cmd = f"test -f {cache_file} && echo 'exists' || echo 'not found'"
        check_result = deployer.execute_command(check_cmd)
        
        if 'exists' in check_result:
            print("   ⚠️  wp-config-cache.php found (should not exist)")
            print("   🗑️  Removing...")
            
            # Backup first
            backup_cmd = f"cp {cache_file} {cache_file}.backup.$(date +%Y%m%d_%H%M%S)"
            deployer.execute_command(backup_cmd)
            
            # Remove
            remove_cmd = f"rm {cache_file}"
            deployer.execute_command(remove_cmd)
            
            # Verify
            verify_cmd = f"test -f {cache_file} && echo 'still exists' || echo 'removed'"
            verify_result = deployer.execute_command(verify_cmd)
            
            if 'removed' in verify_result:
                print("   ✅ wp-config-cache.php removed")
            else:
                print("   ⚠️  File still exists")
        else:
            print("   ✅ wp-config-cache.php not found (OK)")
        
        print()
        
        # Step 2: Fix index.php
        print("📋 Step 2: Fixing index.php...")
        print("-" * 70)
        
        index_file = f"{remote_path}/index.php"
        
        # Read current index.php
        read_cmd = f"cat {index_file}"
        index_content = deployer.execute_command(read_cmd)
        
        if index_content:
            # Standard WordPress index.php content
            standard_index = '''<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
require __DIR__ . '/wp-blog-header.php';
'''
            
            # Check if current index.php is different
            if index_content.strip() != standard_index.strip():
                print("   ⚠️  index.php doesn't match standard WordPress file")
                print("   🔧 Restoring standard index.php...")
                
                # Backup
                backup_cmd = f"cp {index_file} {index_file}.backup.$(date +%Y%m%d_%H%M%S)"
                deployer.execute_command(backup_cmd)
                
                # Save locally
                local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_index.php"
                local_file.parent.mkdir(parents=True, exist_ok=True)
                local_file.write_text(standard_index, encoding='utf-8')
                
                # Deploy
                success = deployer.deploy_file(local_file, index_file)
                
                if success:
                    print("   ✅ index.php restored")
                else:
                    print("   ❌ Failed to restore index.php")
            else:
                print("   ✅ index.php is correct")
        else:
            print("   ⚠️  Could not read index.php")
        
        print()
        
        # Step 3: Verify core files again
        print("📋 Step 3: Verifying core files...")
        print("-" * 70)
        
        verify_cmd = f"cd {remote_path} && wp core verify-checksums 2>&1"
        verify_result = deployer.execute_command(verify_cmd)
        
        if verify_result:
            if "Success" in verify_result or "No differences" in verify_result:
                print("   ✅ Core files now verify correctly")
            else:
                print("   ⚠️  Still have core file issues:")
                print(f"   {verify_result[:400]}")
        
        print()
        
        # Step 4: Test site
        print("📋 Step 4: Testing site...")
        print("-" * 70)
        
        import requests
        import time
        
        print("   ⏳ Waiting 2 seconds for changes to take effect...")
        time.sleep(2)
        
        try:
            response = requests.get("https://southwestsecret.com", timeout=10, headers={
                'User-Agent': 'Mozilla/5.0',
                'Cache-Control': 'no-cache'
            })
            
            print(f"   Status Code: {response.status_code}")
            print(f"   Response Size: {len(response.content)} bytes")
            
            if response.status_code == 200:
                print("   ✅ Site is now accessible!")
                return True
            elif response.status_code == 500:
                print("   ⚠️  Still returning HTTP 500")
                print("   💡 May need to check debug.log after a page request")
                return False
            else:
                print(f"   ⚠️  Unexpected status: {response.status_code}")
                return False
                
        except Exception as e:
            print(f"   ⚠️  Error testing site: {e}")
            return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if fix_core_files() else 1)





