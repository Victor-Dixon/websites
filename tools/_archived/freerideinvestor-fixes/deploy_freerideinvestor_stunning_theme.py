#!/usr/bin/env python3
"""
Deploy Stunning FreeRideInvestor Theme
======================================

Deploys the stunning new front page template and enhanced styling.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
except ImportError:
    print("❌ Could not import SimpleWordPressDeployer")
    print("   Searching for deployer module...")
    import os
    for root, dirs, files in os.walk(str(Path(__file__).parent.parent)):
        if 'simple_wordpress_deployer.py' in files:
            sys.path.insert(0, root)
            from simple_wordpress_deployer import SimpleWordPressDeployer
            break
    else:
        raise ImportError("Could not find simple_wordpress_deployer.py")


def deploy_stunning_theme():
    """Deploy stunning theme files."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🎨 DEPLOYING STUNNING THEME: {site_name}")
    print("=" * 70)
    print()
    
    # Use the deployer's built-in config loader which checks sites.json and blogging_api.json
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(site_name, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        print("   💡 Check SFTP credentials in .env file")
        return False
    
    try:
        remote_path = getattr(deployer, 'remote_path', '') or f"domains/{site_name}/public_html"
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        
        # Local theme file path
        local_template = Path(__file__).parent.parent / "websites" / site_name / "wp" / "wp-content" / "themes" / "freerideinvestor-modern" / "page-templates" / "page-front-page-stunning.php"
        
        # 1. Deploy stunning front page template
        print("1️⃣ Deploying stunning front page template...")
        print(f"   Local file: {local_template}")
        print(f"   Exists: {local_template.exists()}")
        
        if not local_template.exists():
            print(f"   ❌ Template file not found at: {local_template}")
            print(f"   💡 Creating file from current directory structure...")
            # File should exist based on our glob search - let's verify
            return False
        
        remote_template = f"{theme_path}/page-templates/page-front-page-stunning.php"
        print(f"   Remote path: {remote_template}")
        
        # Ensure remote directory exists
        remote_dir = f"{theme_path}/page-templates"
        print(f"   Ensuring directory exists: {remote_dir}")
        mkdir_cmd = f"mkdir -p {remote_dir} 2>&1"
        dir_result = deployer.execute_command(mkdir_cmd)
        if dir_result:
            print(f"   Directory check: {dir_result[:100]}")
        
        success = deployer.deploy_file(local_template, remote_template)
        if success:
            print("   ✅ Stunning front page template deployed successfully!")
        else:
            print("   ❌ Failed to deploy template")
            return False
        
        print()
        
        # 2. Instructions for activation
        print("2️⃣ Activation Instructions:")
        print("   To use the stunning front page:")
        print("   1. Go to WordPress Admin → Pages → Add New")
        print("   2. Title: 'Home' or your preferred title")
        print("   3. Page Attributes → Template: Select 'Stunning Front Page'")
        print("   4. Settings → Reading → Front page displays: Select 'A static page'")
        print("   5. Choose your new page as the Front page")
        print("   6. Save changes")
        print()
        
        print("=" * 70)
        print("✅ STUNNING THEME DEPLOYMENT COMPLETE")
        print("=" * 70)
        print()
        print("💡 Next steps:")
        print("   1. Follow activation instructions above")
        print("   2. Clear browser cache")
        print("   3. Visit the site to see the new stunning design!")
        
        return True
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False
    finally:
        deployer.disconnect()


def main():
    """Main execution."""
    success = deploy_stunning_theme()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

