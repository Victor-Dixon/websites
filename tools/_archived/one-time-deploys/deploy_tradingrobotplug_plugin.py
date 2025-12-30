#!/usr/bin/env python3
"""
Deploy Trading Robot Plug WordPress Plugin Files
================================================

Deploys modified plugin files to tradingrobotplug.com:
- wp/plugins/tradingrobotplug-wordpress-plugin/includes/class-trading-robot-plug.php
- wp/plugins/tradingrobotplug-wordpress-plugin/public/class-public.php
- wp/plugins/tradingrobotplug-wordpress-plugin/public/js/public.js
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

SITE_NAME = "tradingrobotplug.com"

# Files to deploy (relative to project root)
FILES_TO_DEPLOY = [
    ("sites/tradingrobotplug.com/wp/plugins/tradingrobotplug-wordpress-plugin/includes/class-trading-robot-plug.php", "wp-content/plugins/tradingrobotplug-wordpress-plugin/includes/class-trading-robot-plug.php"),
    ("sites/tradingrobotplug.com/wp/plugins/tradingrobotplug-wordpress-plugin/public/class-public.php", "wp-content/plugins/tradingrobotplug-wordpress-plugin/public/class-public.php"),
    ("sites/tradingrobotplug.com/wp/plugins/tradingrobotplug-wordpress-plugin/public/js/public.js", "wp-content/plugins/tradingrobotplug-wordpress-plugin/public/js/public.js"),
]


def deploy_plugin_files():
    """Deploy plugin files to WordPress site."""
    print(f"🚀 Deploying Trading Robot Plug Plugin Files to {SITE_NAME}...")
    print(f"📁 Files to deploy: {len(FILES_TO_DEPLOY)}\n")
    
    project_root = Path(__file__).parent.parent
    site_configs = load_site_configs()
    
    try:
        deployer = SimpleWordPressDeployer(SITE_NAME, site_configs)
    except Exception as e:
        print(f"❌ Failed to initialize deployer: {e}")
        return False
    
    if not deployer.connect():
        print("❌ Failed to connect to server")
        return False
    
    deployed_count = 0
    failed_count = 0
    
    for file_path, remote_relative in FILES_TO_DEPLOY:
        local_file = project_root / file_path
        
        if not local_file.exists():
            print(f"⚠️  File not found: {file_path}")
            failed_count += 1
            continue
        
        print(f"📤 Deploying: {local_file.name}")
        print(f"   From: {file_path}")
        print(f"   To: {remote_relative}")
        
        try:
            # Ensure Path object exists
            local_path = Path(local_file).resolve()
            
            if not local_path.exists():
                print(f"   ❌ Local file does not exist: {local_path}")
                failed_count += 1
                continue
            
            print(f"   📄 Local file size: {local_path.stat().st_size} bytes")
            
            # Build absolute remote path
            # deploy_file handles path construction, but we need to provide relative path from public_html
            # or use absolute path format: /home/username/domains/domain.com/public_html/...
            remote_base = deployer.remote_path or f"/home/u996867598/domains/{SITE_NAME}/public_html"
            full_remote_path = f"{remote_base}/{remote_relative}".replace('\\', '/')
            
            # Ensure remote directory exists first (use Unix path format)
            remote_dir = '/'.join(full_remote_path.split('/')[:-1])
            print(f"   📁 Creating remote directory: {remote_dir}")
            
            # Create directory if needed using execute_command
            mkdir_cmd = f"mkdir -p {remote_dir}"
            deployer.execute_command(mkdir_cmd)
            
            # Deploy file
            print(f"   📤 Uploading to: {full_remote_path}")
            success = deployer.deploy_file(local_path, full_remote_path)
            if success:
                print(f"   ✅ Deployed successfully")
                deployed_count += 1
            else:
                print(f"   ❌ Deployment failed")
                failed_count += 1
        except Exception as e:
            print(f"   ❌ Error: {e}")
            import traceback
            traceback.print_exc()
            failed_count += 1
    
    print(f"\n✅ Deployment complete!")
    print(f"📊 Summary:")
    print(f"   ✅ Successful: {deployed_count}")
    print(f"   ❌ Failed: {failed_count}")
    
    if deployed_count > 0:
        print(f"\n📋 Next Steps:")
        print(f"   1. Flush WordPress rewrite rules: WordPress Admin → Settings → Permalinks → Save Changes")
        print(f"   2. Clear browser cache: Hard refresh (Ctrl+F5)")
        print(f"   3. Test the REST API endpoint: https://tradingrobotplug.com/wp-json/tradingrobotplug/v1/chart-data")
        print(f"   4. Navigate to performance dashboard page and check browser console (F12)")
    
    return deployed_count > 0


if __name__ == "__main__":
    success = deploy_plugin_files()
    sys.exit(0 if success else 1)

