#!/usr/bin/env python3
"""
Add Strict-Transport-Security Header to All Sites
=================================================

Adds Strict-Transport-Security (HSTS) header to all 10 WordPress sites.

Sites: All 10 sites need HSTS header added.

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


# All 10 sites
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


def add_hsts_header(site_name: str):
    """Add Strict-Transport-Security header to a WordPress site."""
    print(f"\n{'='*70}")
    print(f"🔒 ADDING HSTS HEADER: {site_name}")
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
        functions_file = None
        
        for theme_name in possible_themes:
            functions_path = f"{theme_path}/{theme_name}/functions.php"
            print(f"   Checking: {theme_name}")
            
            check_cmd = f"test -f {functions_path} && echo 'exists' || echo 'not found'"
            check_result = deployer.execute_command(check_cmd)
            
            if 'exists' in check_result:
                functions_file = functions_path
                theme_found = True
                print(f"   ✅ Found theme: {theme_name}")
                break
        
        if not theme_found:
            print("❌ Could not find theme functions.php")
            return False
        
        # Read current functions.php
        print(f"📖 Reading {functions_file}...")
        read_cmd = f"cat {functions_file}"
        functions_content = deployer.execute_command(read_cmd)
        
        if not functions_content:
            print("❌ Could not read functions.php")
            return False
        
        # Check if HSTS header already exists
        if 'Strict-Transport-Security' in functions_content or 'strict-transport-security' in functions_content.lower():
            print("⚠️  HSTS header may already exist, checking...")
            # Check if our specific header is there
            if 'max-age=31536000' in functions_content and 'includeSubDomains' in functions_content:
                print("   ✅ HSTS header already added")
                return True
        
        # Create HSTS header function
        hsts_function = '''
/**
 * Add Strict-Transport-Security (HSTS) header
 * Forces browsers to use HTTPS for all future requests
 */
function add_hsts_header() {
    if (is_ssl() || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}
add_action('send_headers', 'add_hsts_header', 1);
'''
        
        # Add to functions.php
        if '?>' in functions_content:
            new_content = functions_content.replace('?>', hsts_function + '\n?>')
        else:
            new_content = functions_content + '\n' + hsts_function
        
        # Save locally first
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_with_hsts.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy updated file
        print(f"🚀 Deploying updated functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print(f"   ✅ HSTS header added successfully!")
            
            # Verify syntax
            print("🔍 Verifying PHP syntax...")
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ PHP syntax is valid!")
                return True
            else:
                print(f"   ⚠️  Syntax check: {syntax_result[:200]}")
                return False
        else:
            print("   ❌ Failed to deploy updated file")
            return False
            
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
    print("🔒 ADDING STRICT-TRANSPORT-SECURITY HEADER TO ALL SITES")
    print("=" * 70)
    print()
    print(f"Sites to update: {len(ALL_SITES)}")
    print()
    
    results = {}
    
    for site_name in ALL_SITES:
        success = add_hsts_header(site_name)
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
    print(f"✅ Successfully updated: {success_count}/{len(ALL_SITES)} sites")
    
    if success_count == len(ALL_SITES):
        print("🎉 All HSTS headers added successfully!")
        print("   🔒 All sites now enforce HTTPS with HSTS")
        return 0
    else:
        print(f"⚠️  {len(ALL_SITES) - success_count} sites failed")
        return 1


if __name__ == "__main__":
    sys.exit(main())

