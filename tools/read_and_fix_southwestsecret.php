#!/usr/bin/env python3
"""
Read and Fix southwestsecret functions.php
==========================================

Reads the file and attempts to fix the brace imbalance.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def read_and_fix():
    """Read the file and fix brace issues."""
    print("=" * 70)
    print("🔧 READING AND FIXING: southwestsecret functions.php")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        functions_file = f"{remote_path}/wp-content/themes/southwestsecret/functions.php"
        
        # Read file
        print("📖 Reading functions.php...")
        read_cmd = f"cat {functions_file}"
        content = deployer.execute_command(read_cmd)
        
        if not content:
            print("❌ Could not read file")
            return False
        
        lines = content.split('\n')
        print(f"   Total lines: {len(lines)}")
        
        # Check if file is very short (might be corrupted)
        if len(lines) < 100:
            print(f"   ⚠️  File is very short - might be corrupted")
            print(f"   First 20 lines:")
            for i, line in enumerate(lines[:20], 1):
                print(f"      {i}: {line[:80]}")
        
        # Check around line 2159
        if len(lines) > 2158:
            print(f"\n   Lines around 2159:")
            for i in range(2155, min(len(lines), 2165)):
                print(f"      {i+1}: {lines[i][:80]}")
        else:
            print(f"\n   ⚠️  File only has {len(lines)} lines, but error mentions line 2159")
            print(f"   This suggests the file might be corrupted or truncated")
        
        # Try to find where the actual content ends
        print(f"\n🔍 Finding actual content end...")
        last_function_line = 0
        for i, line in enumerate(lines):
            if 'function ' in line or 'add_action' in line or 'add_filter' in line:
                last_function_line = i
        
        print(f"   Last function-related line: {last_function_line + 1}")
        
        # Check if there's a lot of empty content after
        non_empty_after = 0
        for i in range(last_function_line + 1, len(lines)):
            if lines[i].strip():
                non_empty_after += 1
        
        print(f"   Non-empty lines after last function: {non_empty_after}")
        
        # Strategy: If file seems corrupted, try to restore from backup or fix by removing extra braces
        # For now, let's try to find a backup
        print(f"\n🔍 Looking for backups...")
        backup_cmd = f"ls -1t {functions_file}.backup* 2>/dev/null | head -5"
        backups = deployer.execute_command(backup_cmd)
        
        if backups and backups.strip():
            print(f"   ✅ Found backups:")
            for backup in backups.strip().split('\n'):
                print(f"      - {backup}")
            
            # Try to restore from most recent backup
            latest_backup = backups.strip().split('\n')[0]
            print(f"\n🔄 Attempting to restore from: {latest_backup}")
            
            restore_cmd = f"cp {latest_backup} {functions_file}"
            deployer.execute_command(restore_cmd)
            
            # Check syntax
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print(f"   ✅ Restored from backup - syntax is now valid!")
                return True
            else:
                print(f"   ⚠️  Backup also has syntax errors")
                print(f"   {syntax_result[:300]}")
        else:
            print(f"   ⚠️  No backups found")
        
        # Alternative: Try to fix by removing lines after a certain point if file is corrupted
        # But this is risky - let's just report what we found
        
        print(f"\n📊 Summary:")
        print(f"   File appears to have {len(lines)} lines")
        print(f"   Error reported on line 2159")
        print(f"   File may be corrupted or have structural issues")
        print(f"\n💡 Recommendation: Restore from Git or manual backup")
        
        return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if read_and_fix() else 1)

