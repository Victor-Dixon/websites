#!/usr/bin/env python3
"""
Check Raw Database Content
===========================

Checks the actual database content to see if text is corrupted at source.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_database_content():
    """Check raw database content."""
    print("=" * 70)
    print("🔍 CHECKING RAW DATABASE CONTENT")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        
        # Get homepage post content directly from database
        print("📖 Checking homepage content from database...")
        get_content_cmd = f"cd {remote_path} && wp post get $(wp post list --post_type=page --name=home --format=ids 2>/dev/null | head -1) --field=content --raw 2>&1"
        raw_content = deployer.execute_command(get_content_cmd)
        
        if raw_content:
            print(f"   Content length: {len(raw_content)} chars")
            print(f"\n   First 500 characters:")
            print(f"   {raw_content[:500]}")
            
            # Check for encoding issues
            issues = []
            if 'Hou ton' in raw_content:
                issues.append("'Hou ton' found in database")
            if 'In ide' in raw_content:
                issues.append("'In ide' found in database")
            if ' crewed' in raw_content:
                issues.append("' crewed' found in database")
            
            if issues:
                print(f"\n   ⚠️  Found {len(issues)} encoding issues in database:")
                for issue in issues:
                    print(f"      - {issue}")
                print("\n   💡 The content in the database is corrupted!")
                return True
            else:
                print("\n   ✅ No encoding issues found in database content")
                print("   💡 Issue must be in theme template files or JavaScript")
        
        # Check theme index.php for hardcoded content
        print("\n📖 Checking theme index.php for hardcoded content...")
        index_file = f"{remote_path}/wp-content/themes/southwestsecret/index.php"
        read_cmd = f"cat {index_file}"
        index_content = deployer.execute_command(read_cmd)
        
        if index_content:
            # Check for problematic text
            if 'Hou ton' in index_content or 'In ide' in index_content or ' crewed' in index_content:
                print("   ⚠️  Found encoding issues in index.php")
                # Show problematic lines
                lines = index_content.split('\n')
                for i, line in enumerate(lines, 1):
                    if 'Hou ton' in line or 'In ide' in line or ' crewed' in line:
                        print(f"      Line {i}: {line.strip()[:100]}")
                return True
            else:
                print("   ✅ No encoding issues in index.php")
        
        return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if check_database_content() else 1)





