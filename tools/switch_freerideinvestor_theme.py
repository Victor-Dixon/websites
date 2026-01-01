#!/usr/bin/env python3
"""
Switch freerideinvestor.com to Default Theme
============================================

Switches to default WordPress theme to test for theme conflicts.

Author: Agent-1
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def switch_to_default_theme():
    """Switch to default WordPress theme."""
    print("=" * 70)
    print("🎨 SWITCHING FREERIDEINVESTOR.COM TO DEFAULT THEME")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return 1
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return 1
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or "domains/freerideinvestor.com/public_html"
        
        # Get current theme
        print("1️⃣  Checking current theme...")
        command = f"cd {remote_path} && wp theme list --status=active --allow-root --format=json 2>&1"
        result = deployer.execute_command(command)
        
        if result:
            print(f"   Current theme info: {result[:200]}")
        
        # Switch to default theme (usually twentytwentyfour or twentytwentythree)
        print()
        print("2️⃣  Switching to default theme...")
        
        # Try twentytwentyfour first (WordPress 6.4+)
        command = f"cd {remote_path} && wp theme activate twentytwentyfour --allow-root 2>&1"
        result = deployer.execute_command(command)
        
        if "Success" in result or "activated" in result.lower():
            print("   ✅ Switched to twentytwentyfour theme")
        else:
            # Try twentytwentythree
            print("   ⚠️  twentytwentyfour not available, trying twentytwentythree...")
            command = f"cd {remote_path} && wp theme activate twentytwentythree --allow-root 2>&1"
            result = deployer.execute_command(command)
            
            if "Success" in result or "activated" in result.lower():
                print("   ✅ Switched to twentytwentythree theme")
            else:
                # Try twentytwentytwo
                print("   ⚠️  twentytwentythree not available, trying twentytwentytwo...")
                command = f"cd {remote_path} && wp theme activate twentytwentytwo --allow-root 2>&1"
                result = deployer.execute_command(command)
                
                if "Success" in result or "activated" in result.lower():
                    print("   ✅ Switched to twentytwentytwo theme")
                else:
                    print(f"   ⚠️  Could not switch theme: {result[:200]}")
                    print("   💡 Manual: Use wp theme activate <theme-name> via WP-CLI")
        
        print()
        print("🌐 Test site now - if it works, the issue was the theme")
        print("   If still broken, check core WordPress files or .htaccess")
        
        return 0
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(switch_to_default_theme())


