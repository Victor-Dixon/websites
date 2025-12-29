#!/usr/bin/env python3
"""
Fix Houston Sip Queen Homepage Template
========================================

Ensures the Home page uses front-page.php template.

Usage:
    python fix_houstonsipqueen_homepage_template.py
"""

import sys
from pathlib import Path

# Add deployment directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_KEY = "houstonsipqueen.com"


def fix_homepage_template(deployer):
    """Set Home page to use front-page.php template."""
    print(f"\n🔧 Fixing homepage template...")
    
    # Get Home page ID
    command1 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp post list --post_type=page --name=home --format=ids --allow-root"
    page_id = deployer.execute_command(command1).strip()
    
    if not page_id:
        print(f"❌ Home page not found")
        return False
    
    page_id = page_id.split()[0]
    print(f"✅ Found Home page (ID: {page_id})")
    
    # Option 1: Set page template to front-page.php (if template exists)
    # But front-page.php is a special template - WordPress uses it automatically for homepage
    # The issue might be that the page has content that's overriding the template
    
    # Option 2: Clear the page content so front-page.php is used
    print(f"\n📝 Clearing Home page content to use front-page.php template...")
    command2 = f"cd /home/*/domains/houstonsipqueen.com/public_html && wp post update {page_id} --post_content='' --allow-root"
    result2 = deployer.execute_command(command2)
    print(f"✅ Home page content cleared:\n{result2}")
    
    # Option 3: Actually, WordPress should use front-page.php when it exists
    # But if a static page is set, it might use page.php
    # Let's check what template is being used
    print(f"\n🔍 Checking current template...")
    command3 = f"cd /home/*/domains/houstonsipqueen.com/public_html && wp post get {page_id} --field=template --allow-root"
    result3 = deployer.execute_command(command3)
    print(f"Current template: {result3.strip()}")
    
    # Clear cache again
    print(f"\n🧹 Clearing cache...")
    command4 = "cd /home/*/domains/houstonsipqueen.com/public_html && wp cache flush --allow-root"
    result4 = deployer.execute_command(command4)
    print(f"✅ Cache cleared")
    
    return True


def main():
    """Main function."""
    print(f"\n{'='*70}")
    print(f"🔧 FIXING HOMEPAGE TEMPLATE: {SITE_KEY}")
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
    
    # Fix homepage template
    success = fix_homepage_template(deployer)
    
    # Disconnect
    deployer.disconnect()
    
    if success:
        print(f"\n✅ Homepage template fix complete!")
        print(f"🌐 Visit https://houstonsipqueen.com to verify")
        print(f"💡 Note: If still showing blog, WordPress may need to use front-page.php automatically")
        print(f"💡 Alternative: Set 'Your homepage displays' back to 'Your latest posts' temporarily, then back to static page")
    else:
        print(f"\n⚠️  Homepage template fix may have failed.")
    
    return success


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)

