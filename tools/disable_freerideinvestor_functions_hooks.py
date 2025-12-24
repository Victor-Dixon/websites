#!/usr/bin/env python3
"""
Temporarily Disable freerideinvestor-modern functions.php Hooks
================================================================

Temporarily disables hooks in functions.php to test if they're causing
the template execution issue.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def backup_and_disable_hooks(deployer):
    """Backup functions.php and comment out problematic hooks."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("=" * 70)
    print("TEMPORARILY DISABLING FUNCTIONS.PHP HOOKS")
    print("=" * 70)
    
    # Read current content
    content = deployer.execute_command(f"cat {functions_php}")
    
    # Create backup
    backup_path = f"{functions_php}.backup.{Path(__file__).stem}"
    deployer.execute_command(f"cp {functions_php} {backup_path}")
    print(f"✅ Backup created: {backup_path}")
    
    # Comment out template_include and template_redirect hooks
    modified_content = content
    
    # Comment out template_include filters
    import re
    # Find and comment out template_include filters
    pattern = r"(add_filter\s*\(\s*['\"]template_include['\"].*?\);)"
    def comment_match(match):
        return "// TEMPORARILY DISABLED: " + match.group(0)
    modified_content = re.sub(pattern, comment_match, modified_content, flags=re.DOTALL)
    
    # Comment out template_redirect hooks
    pattern = r"(add_action\s*\(\s*['\"]template_redirect['\"].*?\);)"
    modified_content = re.sub(pattern, comment_match, modified_content, flags=re.DOTALL)
    
    # Also comment out wp action hooks that might exit
    # But be careful - only comment debug ones
    if "DEBUG: wp action fired" in modified_content:
        pattern = r"(add_action\s*\(\s*['\"]wp['\"].*?DEBUG.*?\);)"
        modified_content = re.sub(pattern, comment_match, modified_content, flags=re.DOTALL)
    
    if modified_content != content:
        # Save modified version locally
        local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_functions_disabled.php"
        local_file.write_text(modified_content, encoding='utf-8')
        
        # Deploy
        success = deployer.deploy_file(local_file, functions_php)
        
        if success:
            print("✅ Hooks temporarily disabled")
            # Verify syntax
            syntax_result = deployer.check_php_syntax(functions_php)
            if syntax_result.get('valid'):
                print("✅ Syntax is valid")
                return True
            else:
                print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
                # Restore backup
                deployer.execute_command(f"cp {backup_path} {functions_php}")
                print("⚠️  Restored backup due to syntax error")
                return False
        else:
            print("❌ Failed to deploy modified functions.php")
            return False
    else:
        print("⚠️  No hooks to disable found")
        return False


def restore_functions_php(deployer):
    """Restore functions.php from backup."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    backup_path = f"{functions_php}.backup.{Path(__file__).stem}"
    exists = deployer.execute_command(f"test -f {backup_path} && echo 'EXISTS' || echo 'MISSING'")
    
    if "EXISTS" in exists:
        deployer.execute_command(f"cp {backup_path} {functions_php}")
        print(f"✅ Restored functions.php from backup")
        return True
    else:
        print("⚠️  Backup not found")
        return False


def main():
    import sys as sys_module
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        # Check if user wants to restore
        if len(sys_module.argv) > 1 and sys_module.argv[1] == "restore":
            restore_functions_php(deployer)
            return 0
        
        # Disable hooks
        if backup_and_disable_hooks(deployer):
            print("\n✅ Hooks disabled")
            print("\n📋 Next steps:")
            print("   1. Visit https://freerideinvestor.com to test")
            print("   2. Check if content now displays")
            print("   3. If it works, identify which hook was causing the issue")
            print("   4. To restore: python tools/disable_freerideinvestor_functions_hooks.py restore")
            
            # Test site
            import time
            import requests
            from bs4 import BeautifulSoup
            
            print("\n⏳ Waiting 3 seconds, then testing...")
            time.sleep(3)
            
            r = requests.get("https://freerideinvestor.com", timeout=10)
            soup = BeautifulSoup(r.text, 'html.parser')
            main = soup.find('main')
            
            if main:
                print("🎉 SUCCESS! Main tag found after disabling hooks!")
                print(f"   Body text: {len(soup.find('body').get_text()) if soup.find('body') else 0} chars")
            else:
                print("❌ Main tag still missing - issue is elsewhere")
        
        return 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(main())


