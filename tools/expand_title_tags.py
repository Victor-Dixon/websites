#!/usr/bin/env python3
"""
Expand Title Tags to 30-60 Characters
=====================================

Expands SEO title tags to optimal length (30-60 characters) for 7 WordPress sites.

Sites: ariajet.site (14 chars), crosbyultimateevents.com (24 chars),
houstonsipqueen.com (19 chars), digitaldreamscape.site (22 chars),
prismblossom.online (26 chars), tradingrobotplug.com (20 chars),
weareswarm.online (24 chars)

Author: Agent-7
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


# Expanded title tags (30-60 characters optimal for SEO)
EXPANDED_TITLES = {
    "ariajet.site": "AriaJet - Premium Private Jet Charter Services | Luxury Travel",
    "crosbyultimateevents.com": "Crosby Ultimate Events - Premier Event Planning & Coordination",
    "houstonsipqueen.com": "Houston's Sip Queen - Luxury Bartending & Craft Cocktail Services",
    "digitaldreamscape.site": "Digital Dreamscape - Living Narrative-Driven AI World & Technology",
    "prismblossom.online": "Prism Blossom - Unique Digital Experiences & Creative Content",
    "tradingrobotplug.com": "Trading Robot Plug - Automated Trading Robots That Actually Work",
    "weareswarm.online": "We Are Swarm - Autonomous Agent Civilization & AI Technology",
}


def expand_title_tag(site_name: str, new_title: str):
    """Expand title tag for a WordPress site."""
    print(f"\n{'='*70}")
    print(f"📝 EXPANDING TITLE TAG: {site_name}")
    print(f"{'='*70}")
    print(f"   New title ({len(new_title)} chars): {new_title}")
    
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
        
        # Check if title filter already exists
        if 'wp_title' in functions_content or 'document_title_parts' in functions_content:
            print("⚠️  Title filter may already exist, checking...")
            # Check if our specific title is there
            if new_title[:50] in functions_content:
                print("   ✅ Title tag already updated")
                return True
        
        # Create title filter function
        title_function = f'''
/**
 * Set SEO-optimized title tag (30-60 characters)
 */
function set_seo_title($title) {{
    $site_title = "{new_title}";
    if (is_front_page() || is_home()) {{
        return $site_title;
    }}
    return $site_title . " | " . get_bloginfo('name');
}}
add_filter('wp_title', 'set_seo_title', 10, 1);
add_filter('document_title_parts', function($title_parts) {{
    $title_parts['title'] = "{new_title}";
    return $title_parts;
}}, 10, 1);
'''
        
        # Add to functions.php
        if '?>' in functions_content:
            new_content = functions_content.replace('?>', title_function + '\n?>')
        else:
            new_content = functions_content + '\n' + title_function
        
        # Save locally first
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_with_title.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy updated file
        print(f"🚀 Deploying updated functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print(f"   ✅ Title tag updated successfully!")
            
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
    print("📝 EXPANDING TITLE TAGS TO 30-60 CHARACTERS")
    print("=" * 70)
    print()
    print(f"Sites to update: {len(EXPANDED_TITLES)}")
    print()
    
    results = {}
    
    for site_name, new_title in EXPANDED_TITLES.items():
        success = expand_title_tag(site_name, new_title)
        results[site_name] = "✅ SUCCESS" if success else "❌ FAILED"
    
    # Summary
    print("\n" + "=" * 70)
    print("📊 SUMMARY")
    print("=" * 70)
    print()
    
    success_count = sum(1 for r in results.values() if "SUCCESS" in r)
    
    for site_name, result in results.items():
        title_length = len(EXPANDED_TITLES[site_name])
        print(f"  {site_name}: {result} ({title_length} chars)")
    
    print()
    print(f"✅ Successfully updated: {success_count}/{len(EXPANDED_TITLES)} sites")
    
    if success_count == len(EXPANDED_TITLES):
        print("🎉 All title tags expanded successfully!")
        return 0
    else:
        print(f"⚠️  {len(EXPANDED_TITLES) - success_count} sites failed")
        return 1


if __name__ == "__main__":
    sys.exit(main())

