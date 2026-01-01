#!/usr/bin/env python3
"""
Cleanup Broken Alt Text Functions
==================================

Removes broken alt text functions from functions.php files.

Author: Agent-7
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


ALL_SITES = [
    "ariajet.site",
    "crosbyultimateevents.com",
    "dadudekc.com",
    "digitaldreamscape.site",
    "freerideinvestor.com",
    "houstonsipqueen.com",
    "prismblossom.online",
    "southwestsecret.com",
    "tradingrobotplug.com",
    "weareswarm.online",
    "weareswarm.site",
]


def cleanup_broken_functions(site_name: str):
    """Remove broken alt text functions."""
    print(f"\n{'='*70}")
    print(f"🧹 CLEANUP: {site_name}")
    print(f"{'='*70}")
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed: {e}")
        return False
    
    if not deployer.connect():
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        theme_path = f"{remote_path}/wp-content/themes"
        
        # Find theme
        site_theme_name = site_name.replace('.', '').replace('-', '')
        possible_themes = [site_theme_name, site_name.split('.')[0], 'default', 'twentytwentyfour']
        
        list_themes_cmd = f"ls -1 {theme_path}/ 2>/dev/null | head -5"
        themes_list = deployer.execute_command(list_themes_cmd)
        if themes_list:
            for line in themes_list.strip().split('\n'):
                if line.strip() and line.strip() not in possible_themes:
                    possible_themes.append(line.strip())
        
        functions_file = None
        for theme_name in possible_themes:
            functions_path = f"{theme_path}/{theme_name}/functions.php"
            check_cmd = f"test -f {functions_path} && echo 'exists' || echo 'not found'"
            if 'exists' in deployer.execute_command(check_cmd):
                functions_file = functions_path
                break
        
        if not functions_file:
            print("   ⚠️  Theme not found")
            return False
        
        # Read file
        read_cmd = f"cat {functions_file}"
        content = deployer.execute_command(read_cmd)
        
        if not content:
            return False
        
        # Remove broken alt text functions
        patterns_to_remove = [
            r'/\*\*[\s\S]*?Add missing alt text[\s\S]*?\*/',
            r'function add_missing_alt_text[\s\S]*?add_filter.*?wp_get_attachment_image_attributes.*?\);',
            r'function add_alt_to_content_images[\s\S]*?add_filter.*?the_content.*?\);',
            r'function add_missing_image_alt[\s\S]*?add_filter.*?wp_get_attachment_image_attributes.*?\);',
        ]
        
        cleaned = content
        removed = False
        
        for pattern in patterns_to_remove:
            if re.search(pattern, cleaned, re.IGNORECASE | re.DOTALL):
                cleaned = re.sub(pattern, '', cleaned, flags=re.IGNORECASE | re.DOTALL)
                removed = True
        
        if not removed:
            print("   ✅ No broken functions found")
            return True
        
        # Clean up extra blank lines
        cleaned = re.sub(r'\n{3,}', '\n\n', cleaned)
        
        # Save and deploy
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_cleaned.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(cleaned, encoding='utf-8')
        
        print(f"   🚀 Deploying cleaned functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            # Verify syntax
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print(f"   ✅ Cleaned and syntax is valid!")
                return True
            else:
                print(f"   ⚠️  Still has syntax errors")
                return False
        else:
            return False
            
    except Exception as e:
        print(f"❌ Error: {e}")
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    print("=" * 70)
    print("🧹 CLEANUP BROKEN ALT TEXT FUNCTIONS")
    print("=" * 70)
    print()
    
    for site_name in ALL_SITES:
        cleanup_broken_functions(site_name)
    
    print("\n✅ Cleanup complete!")
    print("💡 Next: Run add_alt_text_simple.py again")


if __name__ == "__main__":
    sys.exit(main())

