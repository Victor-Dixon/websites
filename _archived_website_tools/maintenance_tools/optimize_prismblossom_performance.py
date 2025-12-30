#!/usr/bin/env python3
"""
Optimize prismblossom.online Performance
=========================================

Deploys performance optimizations to prismblossom.online.
Target: Reduce load time from 16.61s to <3s.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def deploy_optimizations():
    """Deploy performance optimizations to prismblossom.online."""
    site_name = "prismblossom.online"
    
    print("=" * 70)
    print(f"🚀 DEPLOYING PERFORMANCE OPTIMIZATIONS: {site_name}")
    print("=" * 70)
    print()
    
    # Load site configs
    site_configs = load_site_configs()
    
    if site_name not in site_configs:
        print(f"❌ {site_name} not found in site_configs.json")
        return False
    
    # Ensure site_config is in site_configs dict for deployer
    if site_name not in site_configs:
        site_configs[site_name] = site_configs.get(site_name, {})
    
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
        optimizations_dir = Path(__file__).parent.parent / "websites" / site_name / "optimizations"
        
        if not optimizations_dir.exists():
            print(f"❌ Optimizations directory not found: {optimizations_dir}")
            print("   Run tools/generate_prismblossom_optimizations.py first")
            return False
        
        print(f"📁 Optimizations directory: {optimizations_dir}")
        print()
        
        # Find active theme
        theme_path = f"{remote_path}/wp-content/themes"
        print("🔍 Finding active theme...")
        
        list_themes_cmd = f"ls -1 {theme_path}/ 2>/dev/null | head -5"
        themes_list = deployer.execute_command(list_themes_cmd)
        
        site_theme_name = site_name.replace('.', '').replace('-', '')
        possible_themes = [site_theme_name, site_name.split('.')[0], 'prismblossom', 'default', 'twentytwentyfour']
        
        if themes_list:
            for line in themes_list.strip().split('\n'):
                if line.strip() and line.strip() not in possible_themes:
                    possible_themes.append(line.strip())
        
        theme_found = False
        functions_file = None
        theme_name = None
        
        for tname in possible_themes:
            functions_path = f"{theme_path}/{tname}/functions.php"
            check_cmd = f"test -f {functions_path} && echo 'exists' || echo 'not found'"
            check_result = deployer.execute_command(check_cmd)
            
            if 'exists' in check_result:
                functions_file = functions_path
                theme_name = tname
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
        
        # Read optimization files
        functions_opt_file = optimizations_dir / "functions-php-optimizations.php"
        if not functions_opt_file.exists():
            print(f"❌ Optimization file not found: {functions_opt_file}")
            return False
        
        functions_opt_content = functions_opt_file.read_text(encoding='utf-8')
        
        # Check if optimizations already exist
        if 'WordPress Performance Optimizations - Added by Agent-7' in functions_content:
            print("⚠️  Performance optimizations may already exist")
            # Check if our specific optimizations are there
            if 'disable_embeds' in functions_content and 'defer_parsing_of_js' in functions_content:
                print("   ✅ Optimizations already applied")
                return True
        
        # Add optimizations to functions.php
        if '?>' in functions_content:
            new_content = functions_content.replace('?>', '\n' + functions_opt_content + '\n?>')
        else:
            new_content = functions_content + '\n' + functions_opt_content
        
        # Save locally first
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_functions_optimized.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy updated file
        print(f"🚀 Deploying optimized functions.php...")
        success = deployer.deploy_file(local_file, functions_file)
        
        if success:
            print(f"   ✅ Functions.php optimizations deployed!")
            
            # Verify syntax
            print("🔍 Verifying PHP syntax...")
            syntax_cmd = f"php -l {functions_file} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ PHP syntax is valid!")
                
                print()
                print("=" * 70)
                print("📊 DEPLOYMENT SUMMARY")
                print("=" * 70)
                print(f"✅ Functions.php optimizations deployed")
                print(f"📁 Theme: {theme_name}")
                print()
                print("💡 Next steps:")
                print("   1. Apply wp-config.php optimizations manually")
                print("   2. Apply .htaccess optimizations manually")
                print("   3. Install WP Super Cache plugin")
                print("   4. Test site performance")
                print("   5. Verify load time <3s")
                
                return True
            else:
                print(f"   ⚠️  Syntax check: {syntax_result[:200]}")
                return False
        else:
            print("   ❌ Failed to deploy optimized file")
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
    success = deploy_optimizations()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())


