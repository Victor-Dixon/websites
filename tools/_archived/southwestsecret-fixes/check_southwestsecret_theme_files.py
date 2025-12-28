#!/usr/bin/env python3
"""
Check All Theme Files for Errors
==================================

Checks all PHP files in southwestsecret theme for syntax errors.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_all_files():
    """Check all PHP files for errors."""
    print("=" * 70)
    print("🔍 CHECKING ALL THEME FILES: southwestsecret")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        theme_path = f"{remote_path}/wp-content/themes/southwestsecret"
        
        # Find all PHP files
        print("📋 Finding PHP files...")
        find_cmd = f"find {theme_path} -name '*.php' -type f"
        files_result = deployer.execute_command(find_cmd)
        
        if not files_result:
            print("   ⚠️  No PHP files found")
            return False
        
        php_files = [f.strip() for f in files_result.strip().split('\n') if f.strip()]
        print(f"   Found {len(php_files)} PHP files")
        print()
        
        errors_found = []
        
        for php_file in php_files:
            file_name = Path(php_file).name
            print(f"🔍 Checking: {file_name}...")
            
            syntax_cmd = f"php -l {php_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print(f"   ✅ Syntax OK")
            else:
                print(f"   ❌ Syntax error:")
                print(f"   {syntax_result[:200]}")
                errors_found.append((file_name, syntax_result))
        
        print()
        print("=" * 70)
        print("📊 SUMMARY")
        print("=" * 70)
        
        if errors_found:
            print(f"   ❌ Found {len(errors_found)} file(s) with syntax errors:")
            for file_name, error in errors_found:
                print(f"      - {file_name}")
                print(f"        {error[:150]}")
            return False
        else:
            print(f"   ✅ All {len(php_files)} PHP files have valid syntax")
            print()
            print("💡 Since all files have valid syntax but site still returns 500,")
            print("   the issue may be:")
            print("   - Plugin conflict")
            print("   - Database connection issue")
            print("   - File permissions")
            print("   - Memory limit")
            return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if check_all_files() else 1)

