#!/usr/bin/env python3
"""
Deploy AriaJet Music Menu Update
=================================

Updates the navigation menu to change "Capabilities" to "MUSIC" and deploy to ariajet.site
"""

import sys
from pathlib import Path

# Add project root to path
project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

try:
    from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
except ImportError:
    # Try alternative import path
    try:
        sys.path.insert(0, str(project_root / "ops" / "deployment"))
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    except ImportError:
        print("❌ Could not import SimpleWordPressDeployer")
        print("   Make sure you're in the websites directory")
        sys.exit(1)

def main():
    site_domain = "ariajet.site"
    
    print(f"🚀 Deploying Music Menu Update to {site_domain}")
    print("=" * 60)
    
    # Load site configurations
    site_configs = load_site_configs()
    
    if site_domain not in site_configs:
        print(f"❌ Site '{site_domain}' not found in site configurations")
        sys.exit(1)
    
    # Initialize deployer
    deployer = SimpleWordPressDeployer(site_domain, site_configs)
    
    # Local functions.php path
    local_functions = project_root / "websites" / "ariajet.site" / "wp" / "wp-content" / "themes" / "ariajet" / "functions.php"
    
    if not local_functions.exists():
        print(f"❌ Local functions.php not found at: {local_functions}")
        sys.exit(1)
    
    # Remote functions.php path
    remote_functions = "wp-content/themes/ariajet/functions.php"
    
    print(f"\n📄 Local file: {local_functions}")
    print(f"📤 Remote file: {remote_functions}")
    
    # Read local file
    with open(local_functions, 'r', encoding='utf-8') as f:
        local_content = f.read()
    
    # Verify the change is in the local file
    if "MUSIC" not in local_content or "Capabilities" in local_content:
        print("⚠️  Warning: Local functions.php may not have the MUSIC update")
        print("   Checking for 'MUSIC' in file...")
        if "MUSIC" in local_content:
            print("   ✅ Found 'MUSIC' in local file")
        else:
            print("   ❌ 'MUSIC' not found in local file - please update functions.php first")
            sys.exit(1)
    
    # Connect to server
    print(f"\n🔌 Connecting to server...")
    if not deployer.connect():
        print("   ❌ Failed to connect to server")
        sys.exit(1)
    
    # Backup remote file first (if possible)
    print(f"\n💾 Attempting to backup remote functions.php...")
    try:
        # Try to read existing file first
        full_remote_path = f"{deployer.remote_path}/{remote_functions}" if deployer.remote_path else remote_functions
        if deployer.sftp:
            try:
                deployer.sftp.stat(full_remote_path)
                # File exists, create backup
                import datetime
                timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
                backup_path = f"{full_remote_path}.backup.{timestamp}"
                deployer.sftp.get(full_remote_path, str(local_functions) + ".backup")
                print("   ✅ Backup created locally")
            except FileNotFoundError:
                print("   ℹ️  Remote file doesn't exist yet (new deployment)")
    except Exception as e:
        print(f"   ⚠️  Backup skipped: {e}")
    
    # Deploy the file
    print(f"\n📤 Deploying updated functions.php...")
    try:
        success = deployer.deploy_file(
            local_path=local_functions,
            remote_path=remote_functions
        )
        if success:
            print("   ✅ File deployed successfully")
        else:
            print("   ❌ Deployment failed")
            sys.exit(1)
    except Exception as e:
        print(f"   ❌ Deployment failed: {e}")
        sys.exit(1)
    
    # Verify deployment
    print(f"\n🔍 Verifying deployment...")
    try:
        full_remote_path = f"{deployer.remote_path}/{remote_functions}" if deployer.remote_path else remote_functions
        if deployer.sftp:
            with deployer.sftp.open(full_remote_path, 'r') as f:
                remote_content = f.read().decode('utf-8')
    except Exception as e:
        remote_content = None
        print(f"   ⚠️  Could not read remote file for verification: {e}")
    
    if remote_content and "MUSIC" in remote_content:
        print("   ✅ Verification passed - MUSIC found in remote file")
    else:
        print("   ⚠️  Warning: Could not verify MUSIC in remote file")
        print("   Please check the site manually")
    
    print(f"\n✨ Deployment complete!")
    print(f"   Visit https://{site_domain} to see the updated menu")
    print(f"   The 'Capabilities' menu item should now show as 'MUSIC'")

if __name__ == "__main__":
    main()

