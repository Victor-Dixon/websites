#!/usr/bin/env python3
"""
Deploy DigitalDreamscape Theme with Permission Fallbacks
=======================================================

Enhanced deployment script that handles permission issues gracefully.
If SFTP directory creation fails, it falls back to SSH directory creation.

Author: Agent-7 (Web Development Specialist)
Date: 2025-01-03
"""

import sys
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import load_site_configs, SimpleWordPressDeployer

def create_directories_via_ssh(deployer, remote_base_path):
    """Create directories using SSH when SFTP permissions fail."""
    print("🔧 Attempting directory creation via SSH...")

    commands = [
        f"mkdir -p {remote_base_path}",
        f"mkdir -p {remote_base_path}/js",
        f"chmod 755 {remote_base_path}",
        f"chmod 755 {remote_base_path}/js",
        f"ls -la {remote_base_path}/"
    ]

    success = True
    for command in commands:
        try:
            result = deployer.execute_command(command)
            if result and "cannot" in result.lower():
                print(f"❌ SSH command failed: {result.strip()}")
                success = False
                break
            print(f"✅ SSH: {command}")
        except Exception as e:
            print(f"❌ SSH command error: {e}")
            success = False
            break

    return success

def deploy_digitaldreamscape_with_fallbacks():
    """Deploy DigitalDreamscape theme with permission fallbacks."""
    print("\n" + "="*60)
    print("🚀 DEPLOYING: digitaldreamscape.site Theme (with fallbacks)")
    print("="*60 + "\n")

    site_name = "digitaldreamscape.site"
    site_key = "digitaldreamscape.site"

    # Files to deploy
    files_to_deploy = [
        ("wp/wp-content/themes/digitaldreamscape/style.css", "wp-content/themes/digitaldreamscape/style.css"),
        ("wp/wp-content/themes/digitaldreamscape/functions.php", "wp-content/themes/digitaldreamscape/functions.php"),
        ("wp/wp-content/themes/digitaldreamscape/header.php", "wp-content/themes/digitaldreamscape/header.php"),
        ("wp/wp-content/themes/digitaldreamscape/page-blog.php", "wp-content/themes/digitaldreamscape/page-blog.php")
    ]

    try:
        # Load site configurations
        site_configs = load_site_configs()
        if not site_configs:
            print("❌ No site configurations found!")
            return False

        # Initialize deployer
        deployer = SimpleWordPressDeployer(site_key, site_configs)

        # Connect to server
        print(f"📡 Connecting to {site_key}...")
        if not deployer.connect():
            print(f"❌ Failed to connect to {site_key}")
            return False
        print("✅ Connected!\n")

        # Get base paths
        base_path = Path("D:/websites/websites") / site_name
        remote_base_path = "domains/digitaldreamscape.site/public_html/wp-content/themes/digitaldreamscape"

        success_count = 0
        fail_count = 0
        permission_fallback_used = False

        for local_relative_path, remote_relative_path in files_to_deploy:
            local_path = base_path / local_relative_path

            if not local_path.exists():
                print(f"❌ File not found: {local_path}")
                fail_count += 1
                continue

            print(f"📤 Deploying: {local_relative_path}...")

            # First attempt: Try SFTP deployment (will create dirs if possible)
            try:
                full_remote_path = f"{remote_base_path}/{Path(remote_relative_path).name}"
                success = deployer.deploy_file(local_path, full_remote_path)

                if success:
                    print(f"✅ Deployed via SFTP: {local_relative_path}")
                    success_count += 1
                    continue

            except Exception as e:
                print(f"⚠️  SFTP deployment failed: {e}")

                # If this is the first failure, try creating directories via SSH
                if not permission_fallback_used:
                    print("🔄 Attempting permission fallback...")
                    if create_directories_via_ssh(deployer, remote_base_path):
                        permission_fallback_used = True
                        print("✅ Directory creation successful, retrying deployment...")

                        # Retry the same file
                        try:
                            success = deployer.deploy_file(local_path, full_remote_path)
                            if success:
                                print(f"✅ Deployed via fallback: {local_relative_path}")
                                success_count += 1
                                continue
                        except Exception as e2:
                            print(f"❌ Fallback deployment also failed: {e2}")
                    else:
                        print("❌ Directory creation via SSH failed")

                fail_count += 1

        # Disconnect
        deployer.disconnect()

        # Summary
        print(f"\n{'='*60}")
        print("📊 DEPLOYMENT SUMMARY")
        print(f"{'='*60}")
        print(f"   ✅ Succeeded: {success_count}")
        print(f"   ❌ Failed: {fail_count}")
        if permission_fallback_used:
            print("   🔄 Permission fallback used: Yes")

        if fail_count == 0:
            print("✅ All files deployed successfully!")
            print("\n💡 Next Steps:")
            print("   1. Clear WordPress cache")
            print("   2. Clear browser cache")
            print("   3. Visit https://digitaldreamscape.site to verify Dreamscape Codex")
            return True
        else:
            print("⚠️  Some files failed to deploy. Check errors above.")
            return False

    except Exception as e:
        print(f"❌ Error deploying {site_name}: {e}")
        import traceback
        traceback.print_exc()
        return False

def main():
    """Main execution"""
    success = deploy_digitaldreamscape_with_fallbacks()
    return 0 if success else 1

if __name__ == '__main__':
    exit(main())