#!/usr/bin/env python3
"""
Fix Missing Theme File Error
============================

Fixes the missing freerideinvestor_blog_template.php file error by either
creating a stub file or removing the require statement.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_missing_file():
    """Fix the missing theme file error."""
    print("=" * 70)
    print("🔧 FIXING MISSING THEME FILE ERROR")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return 1
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return 1
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        functions_file = f"{theme_path}/functions.php"
        missing_file = f"{theme_path}/freerideinvestor_blog_template.php"
        
        print("1️⃣  Checking functions.php for require statement...")
        functions_content = deployer.execute_command(f"cat {functions_file}")
        
        if "freerideinvestor_blog_template.php" in functions_content:
            print("   ✅ Found require statement for missing file")
            print()
            print("2️⃣  Checking if file exists...")
            file_check = deployer.execute_command(f"test -f {missing_file} && echo 'EXISTS' || echo 'MISSING'")
            
            if "MISSING" in file_check or "EXISTS" not in file_check:
                print("   ⚠️  File does not exist")
                print()
                print("3️⃣  Options:")
                print("   A) Create a stub file (recommended)")
                print("   B) Comment out the require statement")
                print()
                print("   Choosing option A: Creating stub file...")
                
                # Create a minimal stub file
                stub_content = """<?php
/**
 * Freeride Investor Blog Template
 * 
 * Stub file created to fix missing file error.
 * This file can be populated with actual template code later.
 */

// Stub file - no functionality yet
"""
                
                # Write stub file locally first
                local_stub = Path(__file__).parent.parent / "docs" / "freerideinvestor_blog_template_stub.php"
                local_stub.parent.mkdir(parents=True, exist_ok=True)
                local_stub.write_text(stub_content, encoding='utf-8')
                print(f"   ✅ Stub file created locally: {local_stub}")
                
                # Deploy stub file
                print("   🚀 Deploying stub file...")
                success = deployer.deploy_file(local_stub, missing_file)
                
                if success:
                    print("   ✅ Stub file deployed successfully")
                    print()
                    print("4️⃣  Verifying file exists...")
                    verify = deployer.execute_command(f"test -f {missing_file} && echo 'EXISTS' || echo 'MISSING'")
                    if "EXISTS" in verify:
                        print("   ✅ File now exists on server")
                    else:
                        print("   ⚠️  File deployment may have failed")
                else:
                    print("   ❌ Failed to deploy stub file")
                    print()
                    print("   💡 Alternative: Comment out the require statement in functions.php")
                    return 1
            else:
                print("   ✅ File exists (unexpected - error may be elsewhere)")
        else:
            print("   ⚠️  No require statement found for freerideinvestor_blog_template.php")
            print("   📝 Error may be from a different source")
        
        print()
        print("=" * 70)
        print("🌐 TESTING SITE")
        print("=" * 70)
        print()
        
        import requests
        try:
            response = requests.get("https://freerideinvestor.com", timeout=10)
            if response.status_code == 200:
                print("   ✅ Site is now accessible (HTTP 200)")
                print("   🎉 Fix successful!")
            else:
                print(f"   ⚠️  Site returned HTTP {response.status_code}")
                print("   📝 Check site manually - file error should be fixed")
        except Exception as e:
            print(f"   ⚠️  Could not test site: {e}")
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(fix_missing_file())


