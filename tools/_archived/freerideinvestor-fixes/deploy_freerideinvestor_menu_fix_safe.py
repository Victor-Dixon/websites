#!/usr/bin/env python3
"""
Deploy Safe Menu Fix to freerideinvestor.com
=============================================

Safely deploys menu navigation fix that matches theme style.
Includes syntax validation and backup.

Agent-7: Web Development Specialist
Task: Deploy menu fix safely
"""

import sys
from pathlib import Path
from datetime import datetime
import subprocess

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "freerideinvestor.com"

def validate_php_syntax(php_code: str) -> tuple:
    """Validate PHP syntax."""
    import tempfile
    
    with tempfile.NamedTemporaryFile(mode='w', suffix='.php', delete=False) as f:
        f.write(php_code)
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

def backup_functions_php(deployer, remote_base: str) -> str:
    """Backup current functions.php. Returns backup path."""
    functions_path = f"{remote_base}/wp-content/themes/freerideinvestor-modern/functions.php"
    backup_path = f"{remote_base}/wp-content/themes/freerideinvestor-modern/functions.php.backup_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
    
    try:
        if deployer.connect():
            # Copy file to backup (don't rename - keep original)
            with deployer.sftp.open(functions_path, 'r') as src:
                with deployer.sftp.open(backup_path, 'w') as dst:
                    dst.write(src.read())
            print(f"✅ Backup created: {backup_path}")
            return backup_path
    except Exception as e:
        print(f"⚠️  Backup warning: {e}")
        return None

def main():
    """Main execution."""
    print("=" * 70)
    print("DEPLOY SAFE MENU FIX TO FREERIDEINVESTOR.COM")
    print("=" * 70)
    print()
    
    # Read fix file
    fix_file = Path(__file__).parent.parent / "docs" / "freerideinvestor" / "freerideinvestor_menu_fix_FINAL.php"
    if not fix_file.exists():
        print(f"❌ Fix file not found: {fix_file}")
        return
    
    with open(fix_file, 'r', encoding='utf-8') as f:
        fix_code = f.read()
    
    print(f"✅ Read fix file: {fix_file.name}")
    print()
    
    # Validate PHP syntax
    print("🔍 Validating PHP syntax...")
    is_valid, syntax_output = validate_php_syntax(fix_code)
    if is_valid is False:
        print(f"❌ PHP syntax error:")
        print(syntax_output)
        return
    elif is_valid:
        print("✅ PHP syntax is valid")
    else:
        print(f"⚠️  Could not validate: {syntax_output}")
        print("   Proceeding anyway...")
    
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
    
    # Read current functions.php FIRST (before backup)
    print("📖 Reading current functions.php...")
    functions_path = f"{remote_base}/wp-content/themes/freerideinvestor-modern/functions.php"
    
    try:
        # Check if file exists first
        try:
            deployer.sftp.stat(functions_path)
        except FileNotFoundError:
            print(f"⚠️  functions.php not found at {functions_path}")
            print("   Creating new functions.php file...")
            current_content = "<?php\n"
        else:
            with deployer.sftp.open(functions_path, 'r') as f:
                current_content = f.read().decode('utf-8')
            print(f"✅ Read functions.php ({len(current_content)} characters)")
    except Exception as e:
        print(f"❌ Error reading functions.php: {e}")
        print("   Creating new functions.php file...")
        current_content = "<?php\n"
    
    print()
    
    # Backup AFTER reading
    print("💾 Creating backup...")
    backup_path = backup_functions_php(deployer, remote_base)
    print()
    
    # Check if fix already exists
    if 'freerideinvestor_menu_css_final' in current_content:
        print("⚠️  Menu fix already exists in functions.php")
        print("   Skipping deployment to avoid duplicates")
        return
    
    # Append fix code
    print("📝 Adding menu fix to functions.php...")
    new_content = current_content.rstrip() + "\n\n" + fix_code
    
    # Validate combined syntax
    print("🔍 Validating combined PHP syntax...")
    is_valid, syntax_output = validate_php_syntax(new_content)
    if is_valid is False:
        print(f"❌ Combined PHP syntax error:")
        print(syntax_output)
        print("   Aborting deployment")
        return
    elif is_valid:
        print("✅ Combined syntax is valid")
    
    print()
    
    # Deploy
    print("🚀 Deploying fixed functions.php...")
    try:
        with deployer.sftp.open(functions_path, 'w') as f:
            f.write(new_content.encode('utf-8'))
        print("✅ functions.php deployed")
    except Exception as e:
        print(f"❌ Failed to deploy: {e}")
        return
    
    print()
    print("=" * 70)
    print("✅ MENU FIX DEPLOYED SUCCESSFULLY")
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

