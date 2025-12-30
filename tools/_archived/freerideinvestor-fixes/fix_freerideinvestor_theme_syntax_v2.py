#!/usr/bin/env python3
"""
Fix freerideinvestor-modern Theme Syntax Error (V2)
====================================================

Fixes syntax error in functions.php line 209 - properly handles variable names
with hyphens without breaking object property access.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_theme_syntax_error():
    """Fix syntax error in theme functions.php."""
    print("=" * 70)
    print("🔧 FIXING FREERIDEINVESTOR-MODERN THEME SYNTAX ERROR (V2)")
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
        
        # Manual fix for the specific lines
        lines = functions_content.split('\n')
        fixed_lines = []
        
        for i, line in enumerate(lines):
            line_num = i + 1
            
            # Lines 209-217 need fixing
            if line_num == 209:
                # Fix function name: freerideinvestor_add_mcp-test-slug_menu → freerideinvestor_add_mcp_test_slug_menu
                line = line.replace('freerideinvestor_add_mcp-test-slug_menu', 'freerideinvestor_add_mcp_test_slug_menu')
                fixed_lines.append(line)
                print(f"   ✅ Line {line_num}: Fixed function name")
            elif line_num == 211:
                # Fix variable: $mcp-test-slug_page → $mcp_test_slug_page
                line = line.replace('$mcp-test-slug_page', '$mcp_test_slug_page')
                fixed_lines.append(line)
                print(f"   ✅ Line {line_num}: Fixed variable name")
            elif line_num == 212:
                # Fix variables: $mcp-test-slug_url and $mcp-test-slug_page
                line = line.replace('$mcp-test-slug_url', '$mcp_test_slug_url')
                line = line.replace('$mcp-test-slug_page', '$mcp_test_slug_page')
                fixed_lines.append(line)
                print(f"   ✅ Line {line_num}: Fixed variable names")
            elif line_num == 213:
                # Fix variable: $mcp-test-slug_url
                line = line.replace('$mcp-test-slug_url', '$mcp_test_slug_url')
                fixed_lines.append(line)
                print(f"   ✅ Line {line_num}: Fixed variable name")
            elif line_num == 217:
                # Fix function reference in add_filter: freerideinvestor_add_mcp-test-slug_menu → freerideinvestor_add_mcp_test_slug_menu
                line = line.replace("'freerideinvestor_add_mcp-test-slug_menu'", "'freerideinvestor_add_mcp_test_slug_menu'")
                fixed_lines.append(line)
                print(f"   ✅ Line {line_num}: Fixed function reference in add_filter")
            else:
                fixed_lines.append(line)
        
        fixed_content = '\n'.join(fixed_lines)
        
        # Save fixed file locally
        local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_theme_functions_fixed_v2.php"
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
                print("   ✅ functions.php syntax is now valid!")
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
                        print("   📝 Check site manually")
                except Exception as e:
                    print(f"   ⚠️  Could not test site: {e}")
            else:
                print(f"   ⚠️  Syntax check result: {syntax_check[:300]}")
                print("   📝 Review fixed file and deploy manually if needed")
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
    sys.exit(fix_theme_syntax_error())






