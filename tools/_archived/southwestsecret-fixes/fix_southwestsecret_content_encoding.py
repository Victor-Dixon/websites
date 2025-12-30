#!/usr/bin/env python3
"""
Fix Content Encoding Issues
============================

Fixes character encoding issues in WordPress content that may be causing
text rendering problems.

Author: Agent-7
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_content_encoding():
    """Fix content encoding issues."""
    print("=" * 70)
    print("🔧 FIXING CONTENT ENCODING: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        
        # Get homepage content
        print("📖 Checking homepage content...")
        get_home_cmd = f"cd {remote_path} && wp post list --post_type=page --name=home --format=json 2>&1"
        home_result = deployer.execute_command(get_home_cmd)
        
        # Try to get page by slug
        get_page_cmd = f"cd {remote_path} && wp post get $(wp post list --post_type=page --name=home --format=ids 2>/dev/null | head -1) --field=content 2>&1"
        page_content = deployer.execute_command(get_page_cmd)
        
        if page_content:
            print(f"   Found homepage content ({len(page_content)} chars)")
            
            # Check for encoding issues
            issues = []
            if 'Hou ton' in page_content:
                issues.append("'Hou ton' should be 'Houston'")
            if 'In ide' in page_content:
                issues.append("'In ide' should be 'Inside'")
            if ' crewed' in page_content:
                issues.append("' crewed' should be 'screwed'")
            
            if issues:
                print(f"   ⚠️  Found {len(issues)} encoding issues:")
                for issue in issues:
                    print(f"      - {issue}")
                
                # Fix common encoding issues
                print("\n🔧 Fixing encoding issues...")
                fixed_content = page_content
                
                # Common fixes
                fixes = [
                    ('Hou ton', 'Houston'),
                    ('In ide', 'Inside'),
                    (' crewed', 'screwed'),
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
                    ('  lowed', 'slowed'),
                    ('  low', 'slow'),
                    ('  low it', 'slow it'),
                    ('  low down', 'slow down'),
                    ('  low thing', 'slow things'),
                    ('  oundtrack', 'soundtrack'),
                    ('  torie ', 'stories '),
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
                    ('  e ion', 'session'),
                    ('  e ion:', 'session:'),
                    ('  et:', 'set:'),
                    ('  et ', 'set '),
                    ('  et.', 'set.'),
                    ('  et,', 'set,'),
                    ('  et;', 'set;'),
                    ('  et?', 'set?'),
                    ('  et!', 'set!'),
                    ('  et)', 'set)'),
                    ('  et"', 'set"'),
                    ('  et\'', 'set\''),
                ]
                
                for old, new in fixes:
                    fixed_content = fixed_content.replace(old, new)
                
                if fixed_content != page_content:
                    # Get page ID
                    page_id_cmd = f"cd {remote_path} && wp post list --post_type=page --name=home --format=ids 2>&1 | head -1"
                    page_id = deployer.execute_command(page_id_cmd).strip()
                    
                    if page_id and page_id.isdigit():
                        print(f"   📝 Updating page ID {page_id}...")
                        
                        # Update via WP-CLI
                        update_cmd = f"cd {remote_path} && wp post update {page_id} --post_content='{fixed_content.replace(chr(39), chr(39)+chr(39))}' 2>&1"
                        update_result = deployer.execute_command(update_cmd)
                        
                        if 'Updated post' in update_result or 'Success' in update_result:
                            print("   ✅ Content updated")
                            return True
                        else:
                            print(f"   ⚠️  Update result: {update_result[:200]}")
            else:
                print("   ✅ No obvious encoding issues found")
                print("   💡 Issue may be in theme template files")
        
        return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if fix_content_encoding() else 1)





