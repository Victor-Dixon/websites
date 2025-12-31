#!/usr/bin/env python3
"""
Configure Houston Sip Queen Homepage
=====================================

Sets up the homepage to use front-page.php template and clears cache.

Usage:
    python configure_houstonsipqueen_homepage.py
"""

import sys
from pathlib import Path

# Add deployment directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_KEY = "houstonsipqueen.com"


def configure_homepage(deployer):
    """Configure homepage settings via WP-CLI."""
    print(f"\n🏠 Configuring homepage...")
    
    # Option 1: Set homepage to use front-page.php (WordPress should auto-detect)
    # But we can also create a Home page and set it as static homepage
    
    # First, check if front-page.php exists
    command1 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp theme list --allow-root"
    result1 = deployer.execute_command(command1)
    print(f"📋 Active theme check:\n{result1}")
    
    # Clear cache
    print(f"\n🧹 Clearing cache...")
    command2 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp cache flush --allow-root"
    result2 = deployer.execute_command(command2)
    print(f"✅ Cache cleared:\n{result2}")
    
    # Flush rewrite rules (helps with permalinks and template recognition)
    print(f"\n🔄 Flushing rewrite rules...")
    command3 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp rewrite flush --allow-root"
    result3 = deployer.execute_command(command3)
    print(f"✅ Rewrite rules flushed:\n{result3}")
    
    # Check if Home page exists, if not create it
    print(f"\n📄 Checking for Home page...")
    command4 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp post list --post_type=page --name=home --format=ids --allow-root"
    result4 = deployer.execute_command(command4)
    
    if not result4.strip():
        print(f"📝 Creating Home page...")
        command5 = """cd /home/*/domains/houstonsipqueen.com/public_html && wp post create --post_type=page --post_title='Home' --post_name='home' --post_status=publish --post_content='<!-- This page uses the front-page.php template -->' --allow-root"""
        result5 = deployer.execute_command(command5)
        print(f"✅ Home page created:\n{result5}")
        
        # Get the page ID
        command6 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp post list --post_type=page --name=home --format=ids --allow-root"
        page_id = deployer.execute_command(command6).strip()
        
        if page_id:
            # Set as homepage
            print(f"\n🏠 Setting Home page as homepage...")
            command7 = f"cd /home/*/domains/houstonsipqueen.com/public_html && wp option update show_on_front page --allow-root && wp option update page_on_front {page_id} --allow-root"
            result7 = deployer.execute_command(command7)
            print(f"✅ Homepage configured:\n{result7}")
    else:
        page_id = result4.strip().split()[0] if result4.strip() else None
        if page_id:
            print(f"✅ Home page exists (ID: {page_id})")
            # Set as homepage
            print(f"\n🏠 Setting Home page as homepage...")
            command7 = f"cd /home/*/domains/houstonsipqueen.com/public_html && wp option update show_on_front page --allow-root && wp option update page_on_front {page_id} --allow-root"
            result7 = deployer.execute_command(command7)
            print(f"✅ Homepage configured:\n{result7}")
    
    return True


def main():
    """Main function."""
    print(f"\n{'='*70}")
    print(f"🏠 CONFIGURING HOMEPAGE: {SITE_KEY}")
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
    
    # Configure homepage
    success = configure_homepage(deployer)
    
    # Disconnect
    deployer.disconnect()
    
    if success:
        print(f"\n✅ Homepage configuration complete!")
        print(f"🌐 Visit https://houstonsipqueen.com to verify")
        print(f"💡 Note: WordPress should now use front-page.php template")
    else:
        print(f"\n⚠️  Homepage configuration may have failed.")
    
    return success


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)


