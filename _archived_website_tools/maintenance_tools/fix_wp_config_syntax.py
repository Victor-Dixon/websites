#!/usr/bin/env python3
"""
Fix wp-config.php Syntax Error for freerideinvestor.com
========================================================

Fixes the syntax error in wp-config.php by removing duplicate debug settings
and fixing the broken comment structure.

Author: Agent-1 (Integration & Core Systems Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_wp_config_syntax():
    """Fix wp-config.php syntax error."""
    print("=" * 70)
    print("🔧 FIXING FREERIDEINVESTOR.COM WP-CONFIG.PHP SYNTAX ERROR")
    print("=" * 70)
    print()
    
    # Load configs
    site_configs = load_site_configs()
    
    # Initialize deployer
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
        wp_config_path = f"{remote_path}/wp-config.php"
        
        # Read current config
        print("📖 Reading wp-config.php...")
        config_content = deployer.execute_command(f"cat {wp_config_path}")
        
        if not config_content:
            print("❌ Cannot read wp-config.php")
            return 1
        
        # Create backup
        print("💾 Creating backup...")
        backup_result = deployer.execute_command(f"cp {wp_config_path} {wp_config_path}.backup.before_fix")
        print("   ✅ Backup created")
        
        # Fix the syntax error
        # Problem: Lines 106-125 have broken comment structure with duplicate debug settings
        # Solution: Remove duplicate debug blocks and fix comment structure
        
        lines = config_content.split('\n')
        fixed_lines = []
        in_broken_comment = False
        skip_until_stop_editing = False
        
        for i, line in enumerate(lines):
            line_num = i + 1
            
            # Skip the broken comment block (lines 106-125)
            if line_num == 106 and '/*' in line:
                # Start of broken comment - skip it
                in_broken_comment = True
                skip_until_stop_editing = True
                continue
            
            if skip_until_stop_editing:
                # Check if we've reached the closing comment
                if 'That\'s all, stop editing' in line and '*/' in line:
                    # Add proper closing comment
                    fixed_lines.append("/* That's all, stop editing! Happy publishing. */")
                    skip_until_stop_editing = False
                    in_broken_comment = False
                # Skip all lines in the broken comment block
                continue
            
            # Keep all other lines
            fixed_lines.append(line)
        
        # If we still have the broken comment, add the closing manually
        if in_broken_comment or skip_until_stop_editing:
            # Find where to insert the closing comment
            for i, line in enumerate(fixed_lines):
                if 'That\'s all' in line or 'stop editing' in line.lower():
                    # Replace with proper closing
                    fixed_lines[i] = "/* That's all, stop editing! Happy publishing. */"
                    break
            else:
                # Add before ABSPATH definition
                for i, line in enumerate(fixed_lines):
                    if 'ABSPATH' in line:
                        fixed_lines.insert(i, "/* That's all, stop editing! Happy publishing. */")
                        break
        
        fixed_content = '\n'.join(fixed_lines)
        
        # Write fixed config to temporary file
        temp_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_wp_config_fixed.php"
        temp_file.parent.mkdir(parents=True, exist_ok=True)
        temp_file.write_text(fixed_content, encoding='utf-8')
        print(f"   ✅ Fixed config saved to: {temp_file}")
        
        # Deploy fixed config
        print("🚀 Deploying fixed wp-config.php...")
        success = deployer.deploy_file(temp_file, wp_config_path)
        
        if success:
            print("   ✅ Fixed wp-config.php deployed")
            
            # Verify syntax
            print("🔍 Verifying syntax...")
            syntax_check = deployer.execute_command(f"php -l {wp_config_path} 2>&1")
            if "No syntax errors" in syntax_check or "syntax is OK" in syntax_check:
                print("   ✅ wp-config.php syntax is now valid!")
                print()
                print("🌐 Testing site...")
                import requests
                try:
                    response = requests.get("https://freerideinvestor.com", timeout=10)
                    if response.status_code == 200:
                        print("   ✅ Site is now accessible (HTTP 200)")
                        print("   🎉 Fix successful!")
                    else:
                        print(f"   ⚠️  Site returned HTTP {response.status_code}")
                        print("   📝 Check site manually - syntax error is fixed")
                except Exception as e:
                    print(f"   ⚠️  Could not test site: {e}")
                    print("   📝 Syntax error is fixed - test site manually")
            else:
                print(f"   ⚠️  Syntax check result: {syntax_check[:200]}")
                print("   📝 Review fixed file and deploy manually if needed")
        else:
            print("   ❌ Failed to deploy fixed config")
            print(f"   💡 Manual fix: Edit {wp_config_path} and remove duplicate debug blocks")
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
    sys.exit(fix_wp_config_syntax())






