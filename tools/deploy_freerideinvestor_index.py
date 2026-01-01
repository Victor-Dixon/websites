#!/usr/bin/env python3
"""
Deploy freerideinvestor.com index.php
====================================

Deploys the local index.php with custom queries to the server.

Agent-8: SSOT & System Integration Specialist
"""

import sys
from pathlib import Path
from datetime import datetime

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def main():
    print("🚀 Deploying freerideinvestor.com index.php...\n")
    
    configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        sys.exit(1)
    
    try:
        # Read local index.php from canonical location
        # FreeRideInvestor archived - use archive or websites location
        repo_root = Path(__file__).parent.parent.parent
        local_index = repo_root / "archive" / "FreeRideInvestor" / "index.php"
        if not local_index.exists():
            # Fallback to websites location if theme has index.php
            local_index = repo_root / "websites" / "freerideinvestor.com" / "wp" / "wp-content" / "themes" / "freerideinvestor-modern" / "index.php"
        if not local_index.exists():
            print(f"❌ Local index.php not found. Checked:")
            print(f"   - archive/FreeRideInvestor/index.php")
            print(f"   - websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern/index.php")
            sys.exit(1)
        
        with open(local_index, 'r', encoding='utf-8') as f:
            index_content = f.read()
        
        print(f"✅ Read local index.php ({len(index_content)} bytes)")
        
        # Deploy to server
        wp_path = "/home/u996867598/domains/freerideinvestor.com/public_html"
        theme = "freerideinvestor-modern"
        remote_index = f"{wp_path}/wp-content/themes/{theme}/index.php"
        
        # Backup existing file
        backup = f"{remote_index}.backup_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
        deployer.execute_command(f"cp {remote_index} {backup}")
        print(f"✅ Backed up existing index.php to {backup.split('/')[-1]}")
        
        # Write new index.php using deploy_file method
        # Create temp file locally
        repo_root = Path(__file__).parent.parent.parent
        temp_file = repo_root / "temp" / "temp_index.php"
        temp_file.parent.mkdir(parents=True, exist_ok=True)
        with open(temp_file, 'w', encoding='utf-8') as f:
            f.write(index_content)
        
        # Deploy
        success = deployer.deploy_file(temp_file, remote_index)
        
        if success:
            print(f"✅ Deployed index.php to server")
            print(f"   Remote: {remote_index}")
            
            # Verify deployment
            verify = deployer.execute_command(f"head -20 {remote_index}")
            if "FreeRideInvestor" in verify or "hero-section" in verify:
                print(f"✅ Deployment verified - custom content found")
            else:
                print(f"⚠️  Deployment may have failed - content doesn't match")
        else:
            print(f"❌ Deployment failed")
            sys.exit(1)
        
        # Cleanup
        if temp_file.exists():
            temp_file.unlink()
        
        print(f"\n✅ Index.php deployment complete!")
        print(f"   The homepage should now display custom content sections.")
        
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    main()


