#!/usr/bin/env python3
"""Deploy contact page template to freerideinvestor.com"""

import sys
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    site_domain = "freerideinvestor.com"
    local_template = Path(f"websites/{site_domain}/wp/wp-content/themes/freerideinvestor-modern/page-templates/page-contact.php")
    remote_template = "wp-content/themes/freerideinvestor-modern/page-templates/page-contact.php"
    
    print(f"📤 Deploying Contact Page Template to {site_domain}")
    print("=" * 60)
    
    if not local_template.exists():
        print(f"❌ Local template not found: {local_template}")
        sys.exit(1)
    
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    # Create page-templates directory if it doesn't exist
    print(f"\n📂 Ensuring page-templates directory exists...")
    deployer.execute_command(f"mkdir -p {deployer.remote_path}/{remote_template.rsplit('/', 1)[0]} 2>&1")
    
    full_remote_path = f"{deployer.remote_path}/{remote_template}"
    
    print(f"\n📄 Local file: {local_template}")
    print(f"📤 Remote file: {remote_template}")
    
    # Backup existing file if it exists (skip if method doesn't exist)
    print(f"\n💾 Checking for existing file...")
    check_cmd = f"cd {deployer.remote_path} && test -f {remote_template} && echo EXISTS || echo MISSING"
    exists = deployer.execute_command(check_cmd)
    if "EXISTS" in exists:
        print("   ℹ️  File exists, will be overwritten")
    
    # Deploy template
    print(f"\n🚀 Deploying template...")
    if deployer.deploy_file(local_template, full_remote_path):
        print("   ✅ Template deployed successfully")
    else:
        print("   ❌ Failed to deploy template")
        sys.exit(1)
    
    # Verify deployment
    print(f"\n🔍 Verifying deployment...")
    verify_cmd = f"cd {deployer.remote_path} && php -l {remote_template} 2>&1"
    verify_result = deployer.execute_command(verify_cmd)
    if "No syntax errors" in verify_result:
        print("   ✅ PHP syntax check passed")
    else:
        print(f"   ⚠️  Syntax check: {verify_result[:200]}")
    
    # Clear cache
    print(f"\n🧹 Clearing cache...")
    wp_path = f"/home/{deployer.site_config['sftp']['username']}/{deployer.remote_path}"
    deployer.execute_command(f"cd {wp_path} && wp cache flush 2>&1")
    deployer.execute_command(f"cd {wp_path} && wp rewrite flush 2>&1")
    
    deployer.disconnect()
    print(f"\n✨ Deployment complete!")
    print(f"   Visit: https://{site_domain}/contact/")

if __name__ == "__main__":
    main()

