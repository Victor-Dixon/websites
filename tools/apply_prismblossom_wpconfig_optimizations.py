#!/usr/bin/env python3
"""
Apply wp-config.php Optimizations to prismblossom.online
========================================================

Applies WordPress cache configuration to wp-config.php.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def apply_wp_config_optimizations():
    """Apply wp-config.php optimizations."""
    site_name = "prismblossom.online"
    
    print("=" * 70)
    print(f"⚙️  APPLYING WP-CONFIG.PHP OPTIMIZATIONS: {site_name}")
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
        wp_config_path = f"{remote_path}/wp-config.php"
        
        # Read optimization content
        optimizations_dir = Path(__file__).parent.parent / "websites" / site_name / "optimizations"
        wp_config_cache_file = optimizations_dir / "wp-config-cache.php"
        
        if not wp_config_cache_file.exists():
            print(f"❌ Optimization file not found: {wp_config_cache_file}")
            return False
        
        cache_config = wp_config_cache_file.read_text(encoding='utf-8')
        
        # Read current wp-config.php
        print(f"📖 Reading {wp_config_path}...")
        read_cmd = f"cat {wp_config_path}"
        wp_config_content = deployer.execute_command(read_cmd)
        
        if not wp_config_content:
            print("❌ Could not read wp-config.php")
            return False
        
        # Check if optimizations already exist
        if 'WP_CACHE' in wp_config_content and 'define(\'WP_CACHE\', true)' in wp_config_content:
            print("⚠️  WP_CACHE already configured")
            if 'WP_MEMORY_LIMIT' in wp_config_content and '256M' in wp_config_content:
                print("   ✅ All optimizations already applied")
                return True
        
        # Find insertion point (before "That's all, stop editing!")
        if "That's all, stop editing!" in wp_config_content:
            # Insert before the comment
            new_content = wp_config_content.replace(
                "/* That's all, stop editing!",
                f"{cache_config}\n/* That's all, stop editing!"
            )
            print("   ✅ Found insertion point: Before 'That's all, stop editing!'")
        elif "That's all" in wp_config_content:
            # Insert before any "That's all" comment
            new_content = wp_config_content.replace(
                "/* That's all",
                f"{cache_config}\n/* That's all"
            )
            print("   ✅ Found insertion point: Before 'That's all' comment")
        else:
            # Add before closing PHP tag or at end
            if '?>' in wp_config_content:
                new_content = wp_config_content.replace('?>', f'\n{cache_config}\n?>')
            else:
                new_content = wp_config_content.rstrip() + '\n' + cache_config
            print("   ✅ Adding at end of file")
        
        # Save locally first
        local_file = Path(__file__).parent.parent / "temp" / f"{site_name}_wp_config_optimized.php"
        local_file.parent.mkdir(parents=True, exist_ok=True)
        local_file.write_text(new_content, encoding='utf-8')
        
        # Deploy updated file
        print(f"🚀 Deploying optimized wp-config.php...")
        success = deployer.deploy_file(local_file, wp_config_path)
        
        if success:
            print(f"   ✅ wp-config.php optimizations deployed!")
            
            # Verify syntax
            print("🔍 Verifying PHP syntax...")
            syntax_cmd = f"php -l {wp_config_path} 2>&1"
            syntax_result = deployer.execute_command(syntax_cmd)
            
            if "No syntax errors" in syntax_result or "syntax is OK" in syntax_result:
                print("   ✅ PHP syntax is valid!")
                print()
                print("✅ wp-config.php optimizations applied successfully!")
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
    success = apply_wp_config_optimizations()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())


