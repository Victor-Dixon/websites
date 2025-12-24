#!/usr/bin/env python3
"""
Comprehensive H1 Fix - All Template Files
==========================================

Fixes multiple H1 headings by checking all template files and adding CSS fallback.

Author: Agent-7
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


SITES_TO_FIX = {
    "crosbyultimateevents.com": "Crosby Ultimate Events - Premier Event Planning & Coordination",
    "houstonsipqueen.com": "Houston's Sip Queen - Luxury Bartending & Craft Cocktail Services",
    "prismblossom.online": "Prism Blossom - Unique Digital Experiences & Creative Content",
    "tradingrobotplug.com": "Trading Robot Plug - Automated Trading Robots That Actually Work",
}


def fix_h1_comprehensive(site_name: str, primary_h1: str):
    """Comprehensive H1 fix - check all files and add CSS fallback."""
    print(f"\n{'='*70}")
    print(f"🔧 COMPREHENSIVE H1 FIX: {site_name}")
    print(f"{'='*70}")
    
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
        
        files_updated = 0
        
        # Find all PHP files in theme
        print("🔍 Finding all template files...")
        find_cmd = f"find {theme_dir} -name '*.php' -type f | head -20"
        php_files = deployer.execute_command(find_cmd)
        
        if not php_files:
            print("   ⚠️  No PHP files found")
            return False
        
        template_files = [f.strip() for f in php_files.strip().split('\n') if f.strip()]
        print(f"   Found {len(template_files)} PHP files")
        
        # Process each file
        for template_file in template_files:
            file_name = Path(template_file).name
            
            # Skip certain files
            if any(skip in file_name.lower() for skip in ['functions.php', 'style.css', '.min.', 'vendor']):
                continue
            
            print(f"📖 Checking: {file_name}...")
            
            # Read file
            read_cmd = f"cat {template_file}"
            file_content = deployer.execute_command(read_cmd)
            
            if not file_content:
                continue
            
            # Count H1 headings
            h1_pattern = r'<h1[^>]*>.*?</h1>'
            h1_matches = list(re.finditer(h1_pattern, file_content, re.IGNORECASE | re.DOTALL))
            
            if not h1_matches:
                continue
            
            print(f"   ⚠️  Found {len(h1_matches)} H1 heading(s)")
            
            # Strategy based on file type
            new_content = file_content
            
            if 'header' in file_name.lower():
                # Header files: Convert H1 to div/span
                for match in reversed(h1_matches):
                    h1_tag = match.group(0)
                    attrs_match = re.search(r'<h1([^>]*)>', h1_tag, re.IGNORECASE)
                    content_match = re.search(r'<h1[^>]*>(.*?)</h1>', h1_tag, re.IGNORECASE | re.DOTALL)
                    
                    if attrs_match and content_match:
                        attrs = attrs_match.group(1)
                        content = content_match.group(1)
                        div_tag = f'<div{attrs} class="site-title">{content}</div>'
                        new_content = new_content[:match.start()] + div_tag + new_content[match.end():]
                        print(f"      Converted header H1 to div")
            elif 'sidebar' in file_name.lower() or 'widget' in file_name.lower():
                # Sidebar/widget files: Convert all H1s to H2
                for match in reversed(h1_matches):
                    h1_tag = match.group(0)
                    h2_tag = re.sub(r'<h1', '<h2', h1_tag, flags=re.IGNORECASE)
                    h2_tag = re.sub(r'</h1>', '</h2>', h2_tag, flags=re.IGNORECASE)
                    new_content = new_content[:match.start()] + h2_tag + new_content[match.end():]
                    print(f"      Converted sidebar H1 to H2")
            else:
                # Main content files: Keep first H1, convert others to H2
                if len(h1_matches) > 1:
                    for i, match in enumerate(reversed(h1_matches), 1):
                        if i == 1:
                            # Keep last H1 (main content)
                            continue
                        else:
                            # Convert to H2
                            h1_tag = match.group(0)
                            h2_tag = re.sub(r'<h1', '<h2', h1_tag, flags=re.IGNORECASE)
                            h2_tag = re.sub(r'</h1>', '</h2>', h2_tag, flags=re.IGNORECASE)
                            new_content = new_content[:match.start()] + h2_tag + new_content[match.end():]
                            print(f"      Converted extra H1 #{i} to H2")
            
            if new_content != file_content:
                # Save locally
                local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_{file_name}"
                local_file.parent.mkdir(parents=True, exist_ok=True)
                local_file.write_text(new_content, encoding='utf-8')
                
                # Deploy
                print(f"   🚀 Deploying updated {file_name}...")
                success = deployer.deploy_file(local_file, template_file)
                
                if success:
                    print(f"   ✅ Updated {file_name}")
                    files_updated += 1
                else:
                    print(f"   ❌ Failed to deploy {file_name}")
        
        # Add CSS fallback to hide extra H1s (if any remain)
        print(f"\n📝 Adding CSS fallback to hide extra H1s...")
        functions_file = f"{theme_dir}/functions.php"
        
        check_cmd = f"test -f {functions_file} && echo 'exists' || echo 'not found'"
        if 'exists' in deployer.execute_command(check_cmd):
            read_cmd = f"cat {functions_file}"
            functions_content = deployer.execute_command(read_cmd)
            
            if functions_content and 'hide-extra-h1' not in functions_content.lower():
                css_fix = '''
/**
 * CSS fix to hide extra H1 headings (SEO best practice: only 1 H1 per page)
 */
function hide_extra_h1_headings() {
    echo '<style>
        /* Hide all H1s except the first one */
        h1:not(:first-of-type) {
            display: none !important;
        }
        /* Alternative: Convert visually but keep semantic structure */
        body h1:nth-of-type(n+2) {
            font-size: 0;
            height: 0;
            overflow: hidden;
            visibility: hidden;
        }
    </style>';
}
add_action('wp_head', 'hide_extra_h1_headings', 999);
'''
                
                if '?>' in functions_content:
                    new_functions = functions_content.replace('?>', css_fix + '\n?>')
                else:
                    new_functions = functions_content + '\n' + css_fix
                
                local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_h1_css.php"
                local_file.parent.mkdir(parents=True, exist_ok=True)
                local_file.write_text(new_functions, encoding='utf-8')
                
                print(f"   🚀 Deploying CSS fix to functions.php...")
                success = deployer.deploy_file(local_file, functions_file)
                
                if success:
                    print(f"   ✅ CSS fallback added")
                    files_updated += 1
        
        if files_updated > 0:
            print(f"\n   ✅ Updated {files_updated} file(s)")
            return True
        else:
            print("\n   ℹ️  No files needed updating")
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
    print("🔧 COMPREHENSIVE H1 HEADING FIX")
    print("=" * 70)
    print()
    print(f"Sites to fix: {len(SITES_TO_FIX)}")
    print()
    
    results = {}
    
    for site_name, primary_h1 in SITES_TO_FIX.items():
        success = fix_h1_comprehensive(site_name, primary_h1)
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
    print("\n💡 Next: Verify rendered HTML after cache clears")
    
    if success_count == len(SITES_TO_FIX):
        return 0
    else:
        return 1


if __name__ == "__main__":
    sys.exit(main())

