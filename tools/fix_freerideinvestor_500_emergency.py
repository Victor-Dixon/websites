#!/usr/bin/env python3
"""
Emergency Fix for freerideinvestor.com HTTP 500 Error
======================================================

Quick fix to restore site functionality. Likely caused by recent menu navigation fix.
Removes or fixes the problematic code in functions.php.

Agent-7: Web Development Specialist
Task: Emergency fix for site being down
"""

import sys
from pathlib import Path
from datetime import datetime

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "freerideinvestor.com"

def get_functions_php_content(deployer, remote_base: str) -> str:
    """Get current functions.php content."""
    functions_path = f"{remote_base}/wp-content/themes/freerideinvestor-modern/functions.php"
    
    try:
        if deployer.connect():
            with deployer.sftp.open(functions_path, 'r') as f:
                content = f.read().decode('utf-8')
            return content
    except Exception as e:
        print(f"   ❌ Error reading functions.php: {e}")
        return None

def remove_menu_fix_code(content: str) -> str:
    """Remove the menu navigation fix code that may be causing the error."""
    # Remove the menu fix functions
    patterns_to_remove = [
        r"/\*\*[\s\S]*?freerideinvestor\.com Menu Navigation Fixes[\s\S]*?\*/",
        r"function freerideinvestor_menu_css_styled\(\)[\s\S]*?add_action\('wp_head', 'freerideinvestor_menu_css_styled', 99\);",
        r"function freerideinvestor_menu_js_styled\(\)[\s\S]*?add_action\('wp_footer', 'freerideinvestor_menu_js_styled', 99\);",
        r"function freerideinvestor_menu_css\(\)[\s\S]*?add_action\('wp_head', 'freerideinvestor_menu_css', 99\);",
        r"function freerideinvestor_menu_js\(\)[\s\S]*?add_action\('wp_footer', 'freerideinvestor_menu_js', 99\);",
    ]
    
    import re
    for pattern in patterns_to_remove:
        content = re.sub(pattern, '', content, flags=re.MULTILINE)
    
    # Clean up extra blank lines
    content = re.sub(r'\n{3,}', '\n\n', content)
    
    return content

def check_php_syntax(content: str) -> tuple:
    """Check PHP syntax using PHP CLI."""
    import tempfile
    import subprocess
    
    with tempfile.NamedTemporaryFile(mode='w', suffix='.php', delete=False) as f:
        f.write(content)
        temp_file = f.name
    
    try:
        result = subprocess.run(
            ['php', '-l', temp_file],
            capture_output=True,
            text=True,
            timeout=5
        )
        return result.returncode == 0, result.stdout + result.stderr
    except Exception as e:
        return None, str(e)
    finally:
        Path(temp_file).unlink()

def main():
    """Main execution."""
    print("=" * 70)
    print("🚨 EMERGENCY FIX: freerideinvestor.com HTTP 500 Error")
    print("=" * 70)
    print()
    print("This tool will:")
    print("1. Read current functions.php")
    print("2. Remove problematic menu fix code")
    print("3. Verify PHP syntax")
    print("4. Deploy fixed version")
    print()
    
    # Load site configs
    site_configs = load_site_configs()
    if SITE_NAME not in site_configs:
        print(f"❌ Site {SITE_NAME} not found in configs")
        return
    
    deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    
    # Get remote base path
    remote_base = site_configs[SITE_NAME].get('sftp', {}).get('remote_path', 
        'domains/freerideinvestor.com/public_html')
    
    print(f"📂 Remote path: {remote_base}")
    print()
    
    # Connect
    print("🔌 Connecting to server...")
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return
    print("✅ Connected")
    print()
    
    # Read functions.php
    print("📖 Reading functions.php...")
    content = get_functions_php_content(deployer, remote_base)
    if not content:
        print("❌ Could not read functions.php")
        return
    
    print(f"✅ Read functions.php ({len(content)} characters)")
    print()
    
    # Check current syntax
    print("🔍 Checking current PHP syntax...")
    is_valid, syntax_output = check_php_syntax(content)
    if is_valid is False:
        print(f"❌ PHP syntax error detected:")
        print(syntax_output)
        print()
        print("🔧 Removing problematic menu fix code...")
        content = remove_menu_fix_code(content)
        
        # Check syntax again
        print("🔍 Checking syntax after removal...")
        is_valid, syntax_output = check_php_syntax(content)
        if is_valid:
            print("✅ Syntax is now valid")
        else:
            print(f"❌ Still has syntax errors:")
            print(syntax_output)
            print()
            print("⚠️  Manual intervention required")
            return
    elif is_valid:
        print("✅ Current syntax is valid")
        print()
        print("⚠️  Syntax is valid, but site is still down.")
        print("   The issue may be runtime-related, not syntax.")
        print("   Removing menu fix code anyway as precaution...")
        content = remove_menu_fix_code(content)
    else:
        print(f"⚠️  Could not check syntax: {syntax_output}")
        print("   Proceeding with code removal...")
        content = remove_menu_fix_code(content)
    
    print()
    
    # Deploy fixed version
    print("🚀 Deploying fixed functions.php...")
    functions_path = f"{remote_base}/wp-content/themes/freerideinvestor-modern/functions.php"
    
    try:
        with deployer.sftp.open(functions_path, 'w') as f:
            f.write(content.encode('utf-8'))
        print("✅ functions.php deployed")
    except Exception as e:
        print(f"❌ Failed to deploy: {e}")
        return
    
    print()
    print("=" * 70)
    print("✅ EMERGENCY FIX COMPLETE")
    print("=" * 70)
    print()
    print("Next steps:")
    print("1. Test site: https://freerideinvestor.com")
    print("2. If still down, check error logs")
    print("3. Review functions.php for other issues")
    print()
    print("⚠️  Note: Menu navigation fix has been removed.")
    print("   We'll need to fix the menu code and redeploy properly.")

if __name__ == "__main__":
    main()

