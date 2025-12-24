#!/usr/bin/env python3
"""
Fix SouthwestSecret Theme Deployment
====================================

Deploys the correct Chopped & Screwed DJ theme index.php file.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def deploy_correct_theme():
    """Deploy correct theme file."""
    site_name = "southwestsecret.com"
    
    print("=" * 70)
    print(f"🎨 DEPLOYING CORRECT SOUTHWESTSECRET THEME")
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
        theme_path = f"{remote_path}/wp-content/themes/southwestsecret"
        
        # Local theme file
        local_theme_file = Path(__file__).parent.parent / "websites" / site_name / "wordpress-theme" / "southwestsecret" / "index.php"
        
        if not local_theme_file.exists():
            print(f"❌ Local theme file not found: {local_theme_file}")
            return False
        
        print(f"📖 Reading local theme file...")
        theme_content = local_theme_file.read_text(encoding='utf-8')
        
        # Verify it has correct content
        if 'SOUTHWEST' not in theme_content or 'Chopped & Screwed DJ' not in theme_content:
            print("❌ Local theme file doesn't have expected content")
            return False
        
        print(f"   ✅ Found correct theme content")
        print()
        
        # Deploy to server
        remote_file = f"{theme_path}/index.php"
        print(f"🚀 Deploying to {remote_file}...")
        
        success = deployer.deploy_file(local_theme_file, remote_file)
        
        if success:
            print(f"   ✅ Theme file deployed successfully!")
            
            # Verify deployment
            print("🔍 Verifying deployment...")
            verify_cmd = f"head -30 {remote_file} 2>&1 | grep -i 'southwest\\|chopped'"
            verify_result = deployer.execute_command(verify_cmd)
            
            if 'SOUTHWEST' in verify_result or 'Chopped' in verify_result:
                print("   ✅ Deployment verified - correct content found!")
                print()
                print("✅ Theme deployment complete!")
                print()
                print("💡 Next steps:")
                print("   1. Clear WordPress cache (if caching plugin active)")
                print("   2. Clear browser cache")
                print("   3. Refresh the site to see changes")
                return True
            else:
                print("   ⚠️  Verification unclear - please check manually")
                return False
        else:
            print("   ❌ Failed to deploy theme file")
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
    success = deploy_correct_theme()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())


