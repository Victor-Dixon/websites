#!/usr/bin/env python3
"""
Remove Debug Hooks from freerideinvestor-modern functions.php
=============================================================

Removes the debug hooks we added that might be interfering with template execution.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def remove_debug_hooks(deployer):
    """Remove debug hooks from functions.php."""
    remote_path = "domains/freerideinvestor.com/public_html"
    theme_name = "freerideinvestor-modern"
    functions_php = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print("=" * 70)
    print("REMOVING DEBUG HOOKS FROM FUNCTIONS.PHP")
    print("=" * 70)
    
    # Read current content
    content = deployer.execute_command(f"cat {functions_php}")
    
    # Create backup
    backup_path = f"{functions_php}.backup.remove_debug"
    deployer.execute_command(f"cp {functions_php} {backup_path}")
    print(f"✅ Backup created")
    
    # Remove debug template_include filter
    # Find the debug template_include filter block
    lines = content.split('\n')
    new_lines = []
    skip_until_close = False
    brace_count = 0
    
    i = 0
    while i < len(lines):
        line = lines[i]
        
        # Check if this is the start of our debug template_include filter
        if 'add_filter(\'template_include\', function($template)' in line and 'DEBUG:' in content:
            # Skip until we find the closing );
            skip_until_close = True
            brace_count = 0
            i += 1
            continue
        
        # Check if this is the start of our debug wp action
        if 'add_action(\'wp\', function()' in line and 'DEBUG:' in content:
            # Skip until we find the closing );
            skip_until_close = True
            brace_count = 0
            i += 1
            continue
        
        if skip_until_close:
            # Count braces to know when we're done
            brace_count += line.count('{') - line.count('}')
            if '}, 999);' in line or '}, 1);' in line:
                if brace_count <= 0:
                    skip_until_close = False
                    i += 1
                    continue
            i += 1
            continue
        
        new_lines.append(line)
        i += 1
    
    # Join back
    new_content = '\n'.join(new_lines)
    
    # Alternative: Just remove the debug code blocks manually
    # Remove template_include debug filter (lines around 268-279)
    if '// DEBUG: Template usage tracking' in content:
        # Find and remove the entire debug block
        start_marker = '// DEBUG: Template usage tracking'
        end_marker = '}, 999);'
        
        start_idx = content.find(start_marker)
        if start_idx > 0:
            end_idx = content.find(end_marker, start_idx)
            if end_idx > 0:
                end_idx = content.find('\n', end_idx) + 1
                new_content = content[:start_idx] + content[end_idx:]
                print("✅ Removed template_include debug filter")
    
    # Remove wp action debug hook
    if '// DEBUG: Check which template is being used' in new_content:
        start_marker = '// DEBUG: Check which template is being used'
        end_marker = '}, 1);'
        
        start_idx = new_content.find(start_marker)
        if start_idx > 0:
            end_idx = new_content.find(end_marker, start_idx)
            if end_idx > 0:
                end_idx = new_content.find('\n', end_idx) + 1
                new_content = new_content[:start_idx] + new_content[end_idx:]
                print("✅ Removed wp action debug hook")
    
    if new_content != content:
        # Save locally
        local_file = Path(__file__).parent.parent / "docs" / "freerideinvestor_functions_no_debug.php"
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy
        success = deployer.deploy_file(local_file, functions_php)
        
        if success:
            print("✅ Debug hooks removed")
            # Verify syntax
            syntax_result = deployer.check_php_syntax(functions_php)
            if syntax_result.get('valid'):
                print("✅ Syntax is valid")
                return True
            else:
                print(f"❌ Syntax error: {syntax_result.get('error_message', 'Unknown')}")
                # Restore backup
                deployer.execute_command(f"cp {backup_path} {functions_php}")
                print("⚠️  Restored backup")
                return False
        else:
            print("❌ Failed to deploy")
            return False
    else:
        print("⚠️  No debug hooks found to remove")
        return False


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        if remove_debug_hooks(deployer):
            print("\n✅ Debug hooks removed")
            print("\n📋 Testing site now...")
            import time
            import requests
            from bs4 import BeautifulSoup
            
            time.sleep(3)
            r = requests.get("https://freerideinvestor.com", timeout=10)
            soup = BeautifulSoup(r.text, 'html.parser')
            main = soup.find('main')
            
            if main:
                print("🎉 SUCCESS! Main tag found after removing debug hooks!")
            else:
                print("❌ Main tag still missing")
        
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






