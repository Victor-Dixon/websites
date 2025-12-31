#!/usr/bin/env python3
"""
Set Houston Sip Queen to Use front-page.php
===========================================

Sets homepage to "Your latest posts" so WordPress uses front-page.php template.

Usage:
    python set_houstonsipqueen_front_page.py
"""

import sys
from pathlib import Path

# Add deployment directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_KEY = "houstonsipqueen.com"


def set_front_page(deployer):
    """Set homepage to use front-page.php by setting to 'Your latest posts'."""
    print(f"\n🏠 Setting homepage to use front-page.php template...")
    
    # Set homepage to "Your latest posts" (this makes WordPress use front-page.php)
    command1 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp option update show_on_front posts --allow-root"
    result1 = deployer.execute_command(command1)
    print(f"✅ Homepage set to 'Your latest posts':\n{result1}")
    
    # Clear cache
    print(f"\n🧹 Clearing cache...")
    command2 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp cache flush --allow-root"
    result2 = deployer.execute_command(command2)
    print(f"✅ Cache cleared")
    
    # Flush rewrite rules
    print(f"\n🔄 Flushing rewrite rules...")
    command3 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp rewrite flush --allow-root"
    result3 = deployer.execute_command(command3)
    print(f"✅ Rewrite rules flushed")
    
    return True


def main():
    """Main function."""
    print(f"\n{'='*70}")
    print(f"🏠 SETTING FRONT PAGE: {SITE_KEY}")
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
    
    # Set front page
    success = set_front_page(deployer)
    
    # Disconnect
    deployer.disconnect()
    
    if success:
        print(f"\n✅ Front page configuration complete!")
        print(f"🌐 Visit https://houstonsipqueen.com to verify")
        print(f"💡 WordPress will now use front-page.php template automatically")
    else:
        print(f"\n⚠️  Front page configuration may have failed.")
    
    return success


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)


