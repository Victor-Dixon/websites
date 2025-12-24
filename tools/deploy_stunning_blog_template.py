#!/usr/bin/env python3
"""
Deploy Stunning Blog Template
==============================

Deploys the stunning blog archive template.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def deploy_blog_template():
    """Deploy stunning blog template."""
    site_name = "freerideinvestor.com"
    
    print("=" * 70)
    print(f"🎨 DEPLOYING STUNNING BLOG TEMPLATE: {site_name}")
    print("=" * 70)
    print()
    
    site_configs = load_site_configs()
    
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
        theme_path = f"{remote_path}/wp-content/themes/freerideinvestor-modern"
        
        # Local template file
        local_template = Path(__file__).parent.parent / "websites" / site_name / "wp" / "wp-content" / "themes" / "freerideinvestor-modern" / "page-templates" / "page-blog-stunning.php"
        
        print("1️⃣ Deploying stunning blog template...")
        print(f"   Local file: {local_template}")
        print(f"   Exists: {local_template.exists()}")
        
        if not local_template.exists():
            print(f"   ❌ Template file not found")
            return False
        
        remote_template = f"{theme_path}/page-templates/page-blog-stunning.php"
        print(f"   Remote path: {remote_template}")
        
        success = deployer.deploy_file(local_template, remote_template)
        if success:
            print("   ✅ Stunning blog template deployed successfully!")
        else:
            print("   ❌ Failed to deploy template")
            return False
        
        print()
        print("=" * 70)
        print("✅ STUNNING BLOG TEMPLATE DEPLOYED")
        print("=" * 70)
        print()
        print("💡 To activate:")
        print("   1. Edit the Blog page in WordPress Admin")
        print("   2. Page Attributes → Template: Select 'Stunning Blog Archive'")
        print("   3. Update the page")
        
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
    success = deploy_blog_template()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

