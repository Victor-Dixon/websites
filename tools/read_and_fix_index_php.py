#!/usr/bin/env python3
"""
Read and Fix index.php
=======================

Reads index.php and fixes any text encoding issues.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def read_and_fix_index():
    """Read and fix index.php."""
    print("=" * 70)
    print("🔧 READING AND FIXING index.php")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        index_file = f"{remote_path}/wp-content/themes/southwestsecret/index.php"
        
        print("📖 Reading index.php...")
        read_cmd = f"cat {index_file}"
        index_content = deployer.execute_command(read_cmd)
        
        if not index_content:
            print("   ⚠️  Could not read index.php")
            return False
        
        print(f"   File size: {len(index_content)} bytes")
        
        # Text fixes
        text_fixes = [
            ('Hou ton', 'Houston'),
            ('In ide', 'Inside'),
            (' crewed', 'screwed'),
            ('Sub cribe', 'Subscribe'),
            (' ca ette', 'cassette'),
            ('SouthWe t', 'SouthWest'),
            (' mu ic', 'music'),
            (' live ', 'lives '),
            ('In pired', 'Inspired'),
            ('  ound', 'sound'),
            ('  lowed', 'slowed'),
            ('  ignature', 'signature'),
            ('Cla ic', 'Classic'),
            ('Fre h', 'Fresh'),
            ('  election', 'selection'),
            (' late t', 'latest'),
            (' mixe', 'mixes'),
            ('Capabilitie', 'Capabilities'),
            ('  etup', 'setup'),
            ('  etli t', 'setlist'),
            (' tran form', 'transform'),
            (' tarted', 'started'),
            (' pirit', 'spirit'),
            ('  ame', 'same'),
            (' ride ', 'rides '),
            (' u ', 'us '),
            ('  peaker ', 'speakers '),
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
            ('  alway ', 'always '),
            ('  et recap', 'set recap'),
            (' li t', 'list'),
        ]
        
        # Check for issues
        issues_found = []
        for old, new in text_fixes:
            if old in index_content:
                issues_found.append((old, new))
        
        if issues_found:
            print(f"   ⚠️  Found {len(issues_found)} text issues:")
            for old, new in issues_found[:10]:
                print(f"      - '{old}' should be '{new}'")
            
            # Fix issues
            fixed_content = index_content
            for old, new in issues_found:
                fixed_content = fixed_content.replace(old, new)
            
            # Backup
            print("\n💾 Creating backup...")
            backup_cmd = f"cp {index_file} {index_file}.backup.$(date +%Y%m%d_%H%M%S)"
            deployer.execute_command(backup_cmd)
            print("   ✅ Backup created")
            
            # Save locally
            local_file = Path(__file__).parent.parent / "temp" / "southwestsecret_index_fixed.php"
            local_file.parent.mkdir(parents=True, exist_ok=True)
            local_file.write_text(fixed_content, encoding='utf-8')
            
            # Deploy
            print("🚀 Deploying fixed index.php...")
            success = deployer.deploy_file(local_file, index_file)
            
            if success:
                # Verify syntax
                syntax_cmd = f"php -l {index_file} 2>&1"
                syntax_result = deployer.execute_command(syntax_cmd)
                
                if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                    print("   ✅ index.php fixed and syntax verified")
                    
                    # Clear cache
                    print("🧹 Clearing cache...")
                    cache_cmd = f"cd {remote_path} && wp cache flush && wp litespeed-purge all 2>&1"
                    deployer.execute_command(cache_cmd)
                    print("   ✅ Cache cleared")
                    
                    print("\n✅ Text fixes applied to index.php!")
                    return True
                else:
                    print(f"   ⚠️  Syntax error: {syntax_result[:200]}")
                    return False
            else:
                print("   ❌ Failed to deploy")
                return False
        else:
            print("   ✅ No text issues found in index.php")
            print("   💡 Issue may be in other template files or database content")
            return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if read_and_fix_index() else 1)





