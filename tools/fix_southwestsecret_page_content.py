#!/usr/bin/env python3
"""
Fix Page Content Directly
==========================

Fixes the actual page content in WordPress to correct text encoding issues.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def fix_page_content():
    """Fix page content directly."""
    print("=" * 70)
    print("🔧 FIXING PAGE CONTENT: southwestsecret.com")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
        
        # Get all pages
        print("📖 Getting all pages...")
        pages_cmd = f"cd {remote_path} && wp post list --post_type=page --format=json 2>&1"
        pages_result = deployer.execute_command(pages_cmd)
        
        import json
        try:
            pages = json.loads(pages_result) if pages_result and pages_result.strip() else []
        except:
            pages = []
        
        print(f"   Found {len(pages)} pages")
        
        # Find homepage
        homepage = None
        for page in pages:
            if 'home' in page.get('post_name', '').lower() or page.get('post_name', '') == '':
                homepage = page
                break
        
        if not homepage:
            print("   ⚠️  Homepage not found")
            return False
        
        page_id = homepage['ID']
        print(f"   Homepage ID: {page_id}")
        
        # Get current content
        get_content_cmd = f"cd {remote_path} && wp post get {page_id} --field=content 2>&1"
        current_content = deployer.execute_command(get_content_cmd)
        
        print(f"   Current content length: {len(current_content)} chars")
        
        # The content might be minimal - the actual content is likely in theme templates
        # Let's check if there's a custom homepage template
        print("\n📖 Checking for custom homepage template...")
        theme_path = f"{remote_path}/wp-content/themes/southwestsecret"
        
        # Check front-page.php or home.php
        for template in ['front-page.php', 'home.php', 'page-home.php']:
            template_file = f"{theme_path}/{template}"
            check_cmd = f"test -f {template_file} && echo 'exists' || echo 'not found'"
            if 'exists' in deployer.execute_command(check_cmd):
                print(f"   ✅ Found: {template}")
                
                # Read template
                read_cmd = f"cat {template_file}"
                template_content = deployer.execute_command(read_cmd)
                
                if template_content:
                    # Check for encoding issues
                    fixes_needed = []
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
                    ]
                    
                    fixed_content = template_content
                    for old, new in text_fixes:
                        if old in fixed_content:
                            fixes_needed.append((old, new))
                            fixed_content = fixed_content.replace(old, new)
                    
                    if fixes_needed:
                        print(f"   ⚠️  Found {len(fixes_needed)} text issues in {template}")
                        for old, new in fixes_needed:
                            print(f"      Fixing: '{old}' → '{new}'")
                        
                        # Backup
                        backup_cmd = f"cp {template_file} {template_file}.backup.$(date +%Y%m%d_%H%M%S)"
                        deployer.execute_command(backup_cmd)
                        
                        # Save locally
                        local_file = Path(__file__).parent.parent / "temp" / f"southwestsecret_{template}_fixed.php"
                        local_file.parent.mkdir(parents=True, exist_ok=True)
                        local_file.write_text(fixed_content, encoding='utf-8')
                        
                        # Deploy
                        print(f"   🚀 Deploying fixed {template}...")
                        success = deployer.deploy_file(local_file, template_file)
                        
                        if success:
                            # Verify syntax
                            syntax_cmd = f"php -l {template_file} 2>&1"
                            syntax_result = deployer.execute_command(syntax_cmd)
                            
                            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                                print(f"   ✅ {template} fixed and syntax verified")
                            else:
                                print(f"   ⚠️  Syntax error: {syntax_result[:200]}")
                                return False
                    else:
                        print(f"   ✅ No text issues in {template}")
        
        # Clear cache
        print("\n🧹 Clearing cache...")
        cache_cmd = f"cd {remote_path} && wp cache flush && wp litespeed-purge all 2>&1"
        deployer.execute_command(cache_cmd)
        print("   ✅ Cache cleared")
        
        print("\n✅ Content fixes applied!")
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(0 if fix_page_content() else 1)

