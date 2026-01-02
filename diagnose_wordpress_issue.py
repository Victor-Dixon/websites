#!/usr/bin/env python3
"""
Diagnose WordPress Issue
=======================

Tries different diagnostic approaches to identify the cause of HTTP 500 errors.
"""

from pathlib import Path
import sys

sys.path.insert(0, str(Path(__file__).parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def test_wordpress_core(site_domain):
    """Test if WordPress core files are accessible."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_domain}/public_html"

        # Check if wp-load.php exists and is readable
        result = deployer.execute_command(f"ls -la {remote_path}/wp-load.php")
        print(f"wp-load.php status: {result.strip()}")

        # Check if wp-settings.php exists
        result = deployer.execute_command(f"ls -la {remote_path}/wp-settings.php")
        print(f"wp-settings.php status: {result.strip()}")

        # Try to check the first few lines of wp-load.php for syntax errors
        result = deployer.execute_command(f"head -10 {remote_path}/wp-load.php")
        print("wp-load.php content preview:")
        print(result[:200])

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def disable_plugins_and_theme(site_domain):
    """Temporarily disable plugins and theme to isolate the issue."""
    site_configs = load_site_configs()

    try:
        deployer = SimpleWordPressDeployer(site_domain, site_configs)

        if not deployer.connect():
            print(f"❌ Failed to connect to {site_domain}")
            return False

        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_domain}/public_html"

        print(f"🔧 Disabling plugins and theme for {site_domain}...")

        # Rename plugins directory
        result = deployer.execute_command(f"mv {remote_path}/wp-content/plugins {remote_path}/wp-content/plugins_disabled 2>/dev/null || echo 'Plugins already disabled or not found'")
        print(f"Plugins: {result.strip()}")

        # Rename theme directory (keep the current theme name)
        # First find the active theme
        theme_result = deployer.execute_command(f"grep 'TEMPLATEPATH\|STYLESHEETPATH' {remote_path}/wp-config.php || echo 'No theme constants found'")
        print(f"Theme config: {theme_result.strip()}")

        # Try common theme locations
        theme_paths = [f"{remote_path}/wp-content/themes/freerideinvestor", f"{remote_path}/wp-content/themes/prismblossom"]
        for theme_path in theme_paths:
            result = deployer.execute_command(f"ls -la {theme_path} 2>/dev/null || echo 'Theme not found'")
            if 'Theme not found' not in result:
                print(f"Found theme: {theme_path}")
                # Rename theme directory
                disabled_path = theme_path + "_disabled"
                deployer.execute_command(f"mv {theme_path} {disabled_path}")
                print(f"Disabled theme: {theme_path} -> {disabled_path}")

        deployer.disconnect()
        return True

    except Exception as e:
        print(f"❌ Error: {e}")
        return False

def main():
    """Run diagnostics."""
    print("🔍 WORDPRESS DIAGNOSTIC TOOLS")
    print("=" * 50)

    sites = ['freerideinvestor.com']

    for site in sites:
        print(f"\n🔍 Diagnosing {site}...")

        # Test core files
        print("\n📋 Checking WordPress core files...")
        test_wordpress_core(site)

        # Automatically disable plugins and theme to isolate the issue
        print(f"\n🔧 Automatically disabling plugins and theme for {site}...")
        if disable_plugins_and_theme(site):
            print(f"✅ Plugins and theme disabled for {site}")
            print("🔍 Test the website now to see if it loads with defaults")
            print("   If it works, the issue is in a plugin or theme")
            print("   Re-enable by renaming directories back: plugins_disabled -> plugins")

if __name__ == "__main__":
    main()