#!/usr/bin/env python3
"""
Fix Multiple H1 Headings
=========================

Fixes multiple H1 headings by reducing them to 1 per page for 4 WordPress sites.

Sites: crosbyultimateevents.com (2 H1s), houstonsipqueen.com (2 H1s),
prismblossom.online (2 H1s), tradingrobotplug.com (2 H1s)

Author: Agent-7
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


# Sites with multiple H1 headings
SITES_TO_FIX = {
    "crosbyultimateevents.com": "Crosby Ultimate Events - Premier Event Planning & Coordination",
    "houstonsipqueen.com": "Houston's Sip Queen - Luxury Bartending & Craft Cocktail Services",
    "prismblossom.online": "Prism Blossom - Unique Digital Experiences & Creative Content",
    "tradingrobotplug.com": "Trading Robot Plug - Automated Trading Robots That Actually Work",
}


def fix_h1_headings(site_name: str, primary_h1: str):
    """Fix multiple H1 headings by ensuring only one primary H1 exists."""
    print(f"\n{'='*70}")
    print(f"🔧 FIXING MULTIPLE H1 HEADINGS: {site_name}")
    print(f"{'='*70}")
    print(f"   Primary H1: {primary_h1}")
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        theme_path = f"{remote_path}/wp-content/themes"
        
        # Find active theme
        print("🔍 Finding active theme...")
        list_themes_cmd = f"ls -1 {theme_path}/ 2>/dev/null | head -5"
        themes_list = deployer.execute_command(list_themes_cmd)
        
        site_theme_name = site_name.replace('.', '').replace('-', '')
        possible_themes = [site_theme_name, site_name.split('.')[0], 'default', 'twentytwentyfour']
        
        if themes_list:
            for line in themes_list.strip().split('\n'):
                if line.strip() and line.strip() not in possible_themes:
                    possible_themes.append(line.strip())
        
        theme_found = False
        theme_dir = None
        
        for theme_name in possible_themes:
            theme_path_full = f"{theme_path}/{theme_name}"
            check_cmd = f"test -d {theme_path_full} && echo 'exists' || echo 'not found'"
            check_result = deployer.execute_command(check_cmd)
            
            if 'exists' in check_result:
                theme_dir = theme_path_full
                theme_found = True
                print(f"   ✅ Found theme: {theme_name}")
                break
        
        if not theme_found:
            print("❌ Could not find theme directory")
            return False
        
        # Strategy: Fix header.php first (usually contains site name H1), then main templates
        # Priority: Convert header H1 to <div> or <span>, keep main content H1
        
        # Step 1: Fix header.php (convert site name H1 to non-heading element)
        header_file = f"{theme_dir}/header.php"
        files_updated = 0
        
        # Check header.php
        check_cmd = f"test -f {header_file} && echo 'exists' || echo 'not found'"
        if 'exists' in deployer.execute_command(check_cmd):
            print(f"📖 Checking header.php...")
            
            read_cmd = f"cat {header_file}"
            header_content = deployer.execute_command(read_cmd)
            
            if header_content:
                # Find H1 in header (usually site name/logo)
                h1_pattern = r'<h1([^>]*)>.*?</h1>'
                h1_matches = list(re.finditer(h1_pattern, header_content, re.IGNORECASE | re.DOTALL))
                
                if h1_matches:
                    print(f"   ⚠️  Found {len(h1_matches)} H1 in header")
                    
                    # Convert header H1s to div with same styling
                    new_header = header_content
                    for match in reversed(h1_matches):  # Reverse to maintain positions
                        h1_tag = match.group(0)
                        # Extract attributes and content
                        attrs_match = re.search(r'<h1([^>]*)>', h1_tag, re.IGNORECASE)
                        content_match = re.search(r'<h1[^>]*>(.*?)</h1>', h1_tag, re.IGNORECASE | re.DOTALL)
                        
                        if attrs_match and content_match:
                            attrs = attrs_match.group(1)
                            content = content_match.group(1)
                            # Convert to div with class for styling
                            div_tag = f'<div{attrs} class="site-title">{content}</div>'
                            new_header = new_header[:match.start()] + div_tag + new_header[match.end():]
                    
                    if new_header != header_content:
                        # Save and deploy
                        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_header.php"
                        local_file.parent.mkdir(parents=True, exist_ok=True)
                        local_file.write_text(new_header, encoding='utf-8')
                        
                        print(f"   🚀 Deploying updated header.php...")
                        success = deployer.deploy_file(local_file, header_file)
                        
                        if success:
                            print(f"   ✅ Converted {len(h1_matches)} header H1(s) to div")
                            files_updated += 1
        
        # Step 2: Check main template files for multiple H1s
        template_files = [
            f"{theme_dir}/front-page.php",
            f"{theme_dir}/index.php",
            f"{theme_dir}/home.php",
            f"{theme_dir}/page.php",
            f"{theme_dir}/single.php",
        ]
        
        for template_file in template_files:
            check_cmd = f"test -f {template_file} && echo 'exists' || echo 'not found'"
            if 'exists' not in deployer.execute_command(check_cmd):
                continue
            
            print(f"📖 Checking: {Path(template_file).name}...")
            
            read_cmd = f"cat {template_file}"
            file_content = deployer.execute_command(read_cmd)
            
            if not file_content:
                continue
            
            # Count H1 headings
            h1_pattern = r'<h1[^>]*>.*?</h1>'
            h1_matches = list(re.finditer(h1_pattern, file_content, re.IGNORECASE | re.DOTALL))
            
            if len(h1_matches) <= 1:
                print(f"   ✅ Only {len(h1_matches)} H1 found (OK)")
                continue
            
            print(f"   ⚠️  Found {len(h1_matches)} H1 headings")
            
            # Strategy: Keep first H1 (main content), convert others to H2
            new_content = file_content
            for i, match in enumerate(reversed(h1_matches), 1):  # Reverse to maintain positions
                if i == 1:
                    # Keep last H1 (usually main content)
                    continue
                else:
                    # Convert to H2
                    h1_tag = match.group(0)
                    h2_tag = re.sub(r'<h1', '<h2', h1_tag, flags=re.IGNORECASE)
                    h2_tag = re.sub(r'</h1>', '</h2>', h2_tag, flags=re.IGNORECASE)
                    new_content = new_content[:match.start()] + h2_tag + new_content[match.end():]
            
            if new_content != file_content:
                # Save locally
                local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_{Path(template_file).name}"
                local_file.parent.mkdir(parents=True, exist_ok=True)
                local_file.write_text(new_content, encoding='utf-8')
                
                # Deploy
                print(f"   🚀 Deploying updated {Path(template_file).name}...")
                success = deployer.deploy_file(local_file, template_file)
                
                if success:
                    print(f"   ✅ Updated {Path(template_file).name} ({len(h1_matches)} H1s → 1 H1, {len(h1_matches)-1} converted to H2)")
                    files_updated += 1
                else:
                    print(f"   ❌ Failed to deploy {Path(template_file).name}")
        
        if files_updated > 0:
            print(f"\n   ✅ Updated {files_updated} template file(s)")
            return True
        else:
            print("\n   ℹ️  No files needed updating (H1 count already correct)")
            return True
            
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    print("=" * 70)
    print("🔧 FIXING MULTIPLE H1 HEADINGS")
    print("=" * 70)
    print()
    print(f"Sites to fix: {len(SITES_TO_FIX)}")
    print()
    
    results = {}
    
    for site_name, primary_h1 in SITES_TO_FIX.items():
        success = fix_h1_headings(site_name, primary_h1)
        results[site_name] = "✅ SUCCESS" if success else "❌ FAILED"
    
    # Summary
    print("\n" + "=" * 70)
    print("📊 SUMMARY")
    print("=" * 70)
    print()
    
    success_count = sum(1 for r in results.values() if "SUCCESS" in r)
    
    for site_name, result in results.items():
        print(f"  {site_name}: {result}")
    
    print()
    print(f"✅ Successfully updated: {success_count}/{len(SITES_TO_FIX)} sites")
    
    if success_count == len(SITES_TO_FIX):
        print("🎉 All H1 heading issues fixed!")
        return 0
    else:
        print(f"⚠️  {len(SITES_TO_FIX) - success_count} sites failed")
        return 1


if __name__ == "__main__":
    sys.exit(main())

