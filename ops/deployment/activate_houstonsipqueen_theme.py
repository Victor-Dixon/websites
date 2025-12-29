#!/usr/bin/env python3
"""
Activate Houston Sip Queen Theme
=================================

Activates the custom houstonsipqueen theme via WordPress admin or WP-CLI.

Usage:
    python activate_houstonsipqueen_theme.py
"""

import sys
from pathlib import Path

# Add deployment directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_KEY = "houstonsipqueen.com"
THEME_NAME = "houstonsipqueen"


def activate_theme_via_wp_cli(deployer):
    """Activate theme using WP-CLI command."""
    print(f"\n🎨 Activating theme: {THEME_NAME}")
    
    # Get remote path
    site_configs = load_site_configs()
    site_config = site_configs.get(SITE_KEY, {})
    
    if 'sftp' in site_config:
        remote_path = site_config['sftp'].get('remote_path', '')
    else:
        remote_path = site_config.get('remote_path', '')
    
    if not remote_path:
        remote_path = "domains/houstonsipqueen.com/public_html"
    
    # WP-CLI command to activate theme
    command = f"cd /home/*/domains/houstonsipqueen.com/public_html && wp theme activate {THEME_NAME} --allow-root"
    
    print(f"📝 Executing: {command}")
    result = deployer.execute_command(command)
    
    if result:
        print(f"✅ Command output:\n{result}")
        if "Success" in result or "activated" in result.lower():
            print(f"✅ Theme '{THEME_NAME}' activated successfully!")
            return True
        else:
            print(f"⚠️  Theme activation may have failed. Check output above.")
            return False
    else:
        print(f"❌ Failed to execute WP-CLI command")
        return False


def main():
    """Main function."""
    print(f"\n{'='*70}")
    print(f"🎨 ACTIVATING THEME: {THEME_NAME}")
    print(f"{'='*70}\n")
    
    # Load site configurations
    site_configs = load_site_configs()
    
    if SITE_KEY not in site_configs:
        print(f"❌ Site '{SITE_KEY}' not found in configuration")
        return False
    
    # Initialize deployer
    try:
        deployer = SimpleWordPressDeployer(SITE_KEY, site_configs)
    except ValueError as e:
        print(f"❌ {e}")
        return False
    
    # Connect to server
    print(f"🔌 Connecting to server...")
    if not deployer.connect():
        print(f"❌ Failed to connect to {SITE_KEY}")
        return False
    
    # Activate theme
    success = activate_theme_via_wp_cli(deployer)
    
    # Disconnect
    deployer.disconnect()
    
    if success:
        print(f"\n✅ Theme activation complete!")
        print(f"🌐 Visit https://houstonsipqueen.com to verify")
    else:
        print(f"\n⚠️  Theme activation may have failed. Check output above.")
        print(f"💡 Alternative: Activate theme via WordPress Admin → Appearance → Themes")
    
    return success


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)

