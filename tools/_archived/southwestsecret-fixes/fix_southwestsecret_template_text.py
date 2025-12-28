#!/usr/bin/env python3
"""
Fix Template Text Encoding Issues
==================================

Fixes hardcoded text in theme templates with encoding issues.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_template_text():
    """Fix text encoding in template files."""
    print("=" * 70)
    print("🔧 FIXING TEMPLATE TEXT: southwestsecret")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        theme_path = f"{remote_path}/wp-content/themes/southwestsecret"
        
        # Common text fixes
        text_fixes = [
            ('Hou ton', 'Houston'),
            ('In ide', 'Inside'),
            (' crewed', 'screwed'),
            (' etup', 'setup'),
            (' etli t', 'setlist'),
            (' tran form', 'transform'),
            (' tarted', 'started'),
            (' pirit', 'spirit'),
            ('  ame', 'same'),
            (' ride ', 'rides '),
            (' u ', 'us '),
            ('  peaker ', 'speakers '),
            ('  lowed', 'slowed'),
            (' remixe ', 'remixes '),
            ('  outhern', 'southern'),
            (' ho pitality', 'hospitality'),
            ('  e ion', 'session'),
            ('  torie ', 'stories '),
            ('  heartbeat', 'heartbeat'),
            ('  low', 'slow'),
            ('  low it', 'slow it'),
            ('  low down', 'slow down'),
            ('  low thing', 'slow things'),
            ('  oundtrack', 'soundtrack'),
            (' per onalized', 'personalized'),
            ('  how ', 'show '),
            ('  oulful', 'soulful'),
            ('  pace', 'space'),
            ('  tory', 'story'),
            ('  et', 'set'),
            ('  et up', 'set up'),
            ('  election', 'selection'),
            ('  alway ', 'always '),
            ('  et recap', 'set recap'),
            (' li t', 'list'),
            ('Capabilitie', 'Capabilities'),
        ]
        
        # Find all PHP files
        print("📋 Finding template files...")
        find_cmd = f"find {theme_path} -name '*.php' -type f"
        files_result = deployer.execute_command(find_cmd)
        
        if not files_result:
            print("   ⚠️  No PHP files found")
            return False
        
        php_files = [f.strip() for f in files_result.strip().split('\n') if f.strip()]
        print(f"   Found {len(php_files)} PHP files")
        
        files_fixed = 0
        
        for php_file in php_files:
            file_name = Path(php_file).name
            print(f"\n📖 Checking: {file_name}...")
            
            # Read file
            read_cmd = f"cat {php_file}"
            file_content = deployer.execute_command(read_cmd)
            
            if not file_content:
                continue
            
            # Check for issues
            issues_found = []
            for old_text, new_text in text_fixes:
                if old_text in file_content:
                    issues_found.append((old_text, new_text))
            
            if issues_found:
                print(f"   ⚠️  Found {len(issues_found)} text issues")
                
                # Fix issues
                fixed_content = file_content
                for old_text, new_text in issues_found:
                    fixed_content = fixed_content.replace(old_text, new_text)
                    print(f"      Fixed: '{old_text}' → '{new_text}'")
                
                if fixed_content != file_content:
                    # Backup
                    backup_cmd = f"cp {php_file} {php_file}.backup.$(date +%Y%m%d_%H%M%S)"
                    deployer.execute_command(backup_cmd)
                    
                    # Save locally
                    local_file = Path(__file__).parent.parent / "temp" / f"southwestsecret_{file_name}_fixed.php"
                    local_file.parent.mkdir(parents=True, exist_ok=True)
                    local_file.write_text(fixed_content, encoding='utf-8')
                    
                    # Deploy
                    print(f"   🚀 Deploying fixed {file_name}...")
                    success = deployer.deploy_file(local_file, php_file)
                    
                    if success:
                        # Verify syntax
                        syntax_cmd = f"php -l {php_file} 2>&1"
                        syntax_result = deployer.execute_command(syntax_cmd)
                        
                        if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                            print(f"   ✅ Fixed and syntax verified")
                            files_fixed += 1
                        else:
                            print(f"   ⚠️  Syntax error: {syntax_result[:200]}")
            else:
                print(f"   ✅ No text issues found")
        
        print()
        print("=" * 70)
        print("📊 SUMMARY")
        print("=" * 70)
        print(f"   Files fixed: {files_fixed}/{len(php_files)}")
        
        if files_fixed > 0:
            # Clear cache
            print("\n🧹 Clearing cache...")
            cache_cmd = f"cd {remote_path} && wp cache flush 2>&1"
            deployer.execute_command(cache_cmd)
            print("   ✅ Cache cleared")
        
        return files_fixed > 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if fix_template_text() else 1)





