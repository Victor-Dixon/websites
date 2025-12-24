#!/usr/bin/env python3
"""
Add Meta Descriptions to WordPress Sites
========================================

Adds SEO meta descriptions to 8 WordPress sites that are missing them.

Sites: ariajet.site, crosbyultimateevents.com, houstonsipqueen.com,
digitaldreamscape.site, prismblossom.online, tradingrobotplug.com,
southwestsecret.com

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


# Meta descriptions for each site
META_DESCRIPTIONS = {
    "ariajet.site": "AriaJet - Premium private jet charter services. Experience luxury travel with our fleet of private aircraft. Book your next flight with confidence.",
    "crosbyultimateevents.com": "Crosby Ultimate Events - Premier event planning and coordination services. Creating unforgettable experiences for weddings, corporate events, and special occasions.",
    "houstonsipqueen.com": "Houston's Sip Queen - Luxury bartending and beverage services for your special events. Professional mixologists bringing craft cocktails to your celebration.",
    "digitaldreamscape.site": "Digital Dreamscape - A living, narrative-driven AI world. Explore the intersection of technology, creativity, and autonomous agent civilization.",
    "prismblossom.online": "Prism Blossom - Discover unique digital experiences and creative content. Your destination for innovative online experiences.",
    "tradingrobotplug.com": "Trading Robot Plug - Automated trading robots that actually work. Join the waitlist for performance-tracked trading automation with proven results.",
    "southwestsecret.com": "Southwest Secret - Uncover hidden gems and exclusive experiences in the Southwest. Your guide to the best kept secrets.",
}


def add_meta_description(site_name: str, description: str):
    """Add meta description to a WordPress site."""
    print(f"\n{'='*70}")
    print(f"📝 ADDING META DESCRIPTION: {site_name}")
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
        
        # Find active theme by checking theme directories
        print("🔍 Finding active theme...")
        
        # List theme directories
        list_themes_cmd = f"ls -1 {theme_path}/ 2>/dev/null | head -5"
        themes_list = deployer.execute_command(list_themes_cmd)
        
        # Try common theme names based on site
        site_theme_name = site_name.replace('.', '').replace('-', '')
        possible_themes = [site_theme_name, site_name.split('.')[0], 'default', 'twentytwentyfour']
        
        # Add themes from directory listing
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
        
        # Check if meta description already exists
        if 'meta_description' in functions_content.lower() or 'meta name="description"' in functions_content.lower():
            print("⚠️  Meta description may already exist, checking...")
            # Check if our specific description is there
            if description[:50] in functions_content:
                print("   ✅ Meta description already added")
                return True
        
        # Create meta description function
        meta_function = f'''
/**
 * Add SEO meta description
 */
function add_meta_description() {{
    $description = "{description}";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\\n";
}}
add_action('wp_head', 'add_meta_description', 1);
'''
        
        # Add to functions.php
        # Find insertion point (before closing PHP tag or at end)
        if '?>' in functions_content:
            # Insert before closing tag
            new_content = functions_content.replace('?>', meta_function + '\n?>')
        else:
            # Append to end
            new_content = functions_content + '\n' + meta_function
        
        # Save locally first
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_with_meta.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy updated file
        print(f"🚀 Deploying updated functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print(f"   ✅ Meta description added successfully!")
            
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
    print("📝 ADDING META DESCRIPTIONS TO WORDPRESS SITES")
    print("=" * 70)
    print()
    print(f"Sites to update: {len(META_DESCRIPTIONS)}")
    print()
    
    results = {}
    
    for site_name, description in META_DESCRIPTIONS.items():
        success = add_meta_description(site_name, description)
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
    print(f"✅ Successfully updated: {success_count}/{len(META_DESCRIPTIONS)} sites")
    
    if success_count == len(META_DESCRIPTIONS):
        print("🎉 All meta descriptions added successfully!")
        return 0
    else:
        print(f"⚠️  {len(META_DESCRIPTIONS) - success_count} sites failed")
        return 1


if __name__ == "__main__":
    sys.exit(main())

