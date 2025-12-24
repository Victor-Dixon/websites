#!/usr/bin/env python3
"""
Install WP Super Cache Plugin for prismblossom.online
=====================================================

Installs and activates WP Super Cache plugin via WP-CLI.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def install_wp_super_cache():
    """Install and activate WP Super Cache plugin."""
    site_name = "prismblossom.online"
    
    print("=" * 70)
    print(f"📦 INSTALLING WP SUPER CACHE: {site_name}")
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
        
        # Check if WP-CLI is available
        print("🔍 Checking WP-CLI availability...")
        wp_cli_check = f"cd {remote_path} && which wp && echo 'WP-CLI found' || echo 'WP-CLI not found'"
        wp_cli_result = deployer.execute_command(wp_cli_check)
        
        if 'WP-CLI not found' in wp_cli_result:
            print("   ⚠️  WP-CLI not available")
            print("   💡 Manual installation required:")
            print("      1. Log into WordPress admin")
            print("      2. Go to Plugins > Add New")
            print("      3. Search for 'WP Super Cache'")
            print("      4. Install and Activate")
            print("      5. Go to Settings > WP Super Cache")
            print("      6. Enable caching and set to 'Expert' mode")
            return False
        
        # Check if plugin is already installed
        print("🔍 Checking if WP Super Cache is already installed...")
        check_plugin_cmd = f"cd {remote_path} && wp plugin list --allow-root 2>&1 | grep -i 'wp-super-cache' || echo 'not found'"
        plugin_check = deployer.execute_command(check_plugin_cmd)
        
        if 'wp-super-cache' in plugin_check.lower() and 'not found' not in plugin_check:
            print("   ✅ WP Super Cache already installed")
            
            # Check if activated
            check_active_cmd = f"cd {remote_path} && wp plugin is-active wp-super-cache --allow-root 2>&1"
            active_check = deployer.execute_command(check_active_cmd)
            
            if 'Active' in active_check or 'active' in active_check.lower():
                print("   ✅ WP Super Cache already activated")
                print()
                print("💡 Configure caching:")
                print("   1. Log into WordPress admin")
                print("   2. Go to Settings > WP Super Cache")
                print("   3. Enable caching")
                print("   4. Set to 'Expert' mode")
                return True
            else:
                print("   ⚠️  Plugin installed but not activated")
                print("   🔄 Activating plugin...")
                activate_cmd = f"cd {remote_path} && wp plugin activate wp-super-cache --allow-root 2>&1"
                activate_result = deployer.execute_command(activate_cmd)
                
                if 'Success' in activate_result or 'activated' in activate_result.lower():
                    print("   ✅ Plugin activated successfully!")
                    return True
                else:
                    print(f"   ⚠️  Activation result: {activate_result[:200]}")
                    return False
        else:
            # Install plugin
            print("📦 Installing WP Super Cache...")
            install_cmd = f"cd {remote_path} && wp plugin install wp-super-cache --activate --allow-root 2>&1"
            install_result = deployer.execute_command(install_cmd)
            
            if 'Success' in install_result or 'installed' in install_result.lower():
                print("   ✅ WP Super Cache installed and activated!")
                print()
                print("💡 Next steps:")
                print("   1. Log into WordPress admin")
                print("   2. Go to Settings > WP Super Cache")
                print("   3. Enable caching")
                print("   4. Set to 'Expert' mode")
                print("   5. Save settings")
                return True
            else:
                print(f"   ⚠️  Installation result: {install_result[:200]}")
                print()
                print("💡 Manual installation required:")
                print("   1. Log into WordPress admin")
                print("   2. Go to Plugins > Add New")
                print("   3. Search for 'WP Super Cache'")
                print("   4. Install and Activate")
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
    success = install_wp_super_cache()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())


