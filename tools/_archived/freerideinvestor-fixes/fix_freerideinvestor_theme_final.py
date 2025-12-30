#!/usr/bin/env python3
"""
Fix freerideinvestor-modern Theme Final Syntax Error
=====================================================

Fixes the remaining function reference with hyphen on line 3403.

Author: Agent-3
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_final_error():
    """Fix the remaining function reference error."""
    print("=" * 70)
    print("🔧 FIXING FINAL THEME SYNTAX ERROR")
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
        
        print("📖 Reading functions.php...")
        command = f"cat {functions_file}"
        functions_content = deployer.execute_command(command)
        
        if not functions_content:
            print("❌ Cannot read functions.php")
            return 1
        
        # Create backup
        print("💾 Creating backup...")
        deployer.execute_command(f"cp {functions_file} {functions_file}.backup.$(date +%Y%m%d_%H%M%S)")
        print("   ✅ Backup created")
        
        # Fix line 3403: replace hyphen with underscore in function name
        lines = functions_content.split('\n')
        fixed_lines = []
        
        for i, line in enumerate(lines):
            line_num = i + 1
            
            # Line 3403: Fix function reference in add_filter
            if line_num == 3403:
                # Replace 'freerideinvestor_add_mcp-test-page_menu' with 'freerideinvestor_add_mcp_test_page_menu'
                fixed_line = line.replace("'freerideinvestor_add_mcp-test-page_menu'", "'freerideinvestor_add_mcp_test_page_menu'")
                fixed_lines.append(fixed_line)
                print(f"   ✅ Line {line_num}: Fixed function reference")
                print(f"      Before: {line.strip()}")
                print(f"      After:  {fixed_line.strip()}")
            else:
                fixed_lines.append(line)
        
        fixed_content = '\n'.join(fixed_lines)
        
        # Save fixed file locally
        local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_theme_functions_final_fixed.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(fixed_content, encoding='utf-8')
        print(f"   ✅ Fixed file saved to: {local_file}")
        
        # Deploy fixed file
        print("🚀 Deploying fixed functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print("   ✅ Fixed functions.php deployed")
            
            # Verify syntax
            print("🔍 Verifying syntax...")
            syntax_check = deployer.execute_command(f"php -l {functions_file} 2>&1")
            if "No syntax errors" in syntax_check or "syntax is OK" in syntax_check:
                print("   ✅ functions.php syntax is valid!")
                
                # Wait a moment for WordPress to reload
                import time
                print("\n⏳ Waiting 3 seconds for WordPress to reload...")
                time.sleep(3)
                
                # Test site
                print("🌐 Testing site...")
                import requests
                try:
                    response = requests.get("https://freerideinvestor.com", timeout=10)
                    if response.status_code == 200:
                        print("   ✅ Site is now accessible (HTTP 200)")
                        print("   🎉 Fix successful!")
                    else:
                        print(f"   ⚠️  Site returned HTTP {response.status_code}")
                        print(f"   📝 Response size: {len(response.content)} bytes")
                        if response.status_code == 500:
                            print("   🔍 Checking debug.log for errors...")
                            debug_log = f"{remote_path}/wp-content/debug.log"
                            log_check = deployer.execute_command(f"tail -n 10 {debug_log} 2>&1")
                            if log_check and "No such file" not in log_check:
                                print("   Last 10 lines of debug.log:")
                                print("   " + "\n   ".join(log_check.split('\n')[-10:]))
                except Exception as e:
                    print(f"   ⚠️  Could not test site: {e}")
            else:
                print(f"   ⚠️  Syntax check result: {syntax_check[:300]}")
        else:
            print("   ❌ Failed to deploy fixed file")
            return 1
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(fix_final_error())

