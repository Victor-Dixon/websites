#!/usr/bin/env python3
"""
Deploy PrismBlossom Theme Files
================================

Deploys updated theme files for prismblossom.online to the live server.
Specifically deploys functions.php and page-carmyn.php from the merged PR.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-23
"""

import sys
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

# Try WordPressManager first, then fallback to SimpleWordPressDeployer
MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
WORDPRESS_MANAGER_AVAILABLE = False
SIMPLE_DEPLOYER_AVAILABLE = False

if MAIN_REPO_TOOLS.exists():
    sys.path.insert(0, str(MAIN_REPO_TOOLS))
    try:
        from wordpress_manager import WordPressManager
        WORDPRESS_MANAGER_AVAILABLE = True
    except ImportError:
        pass

# Try SimpleWordPressDeployer as fallback
if not WORDPRESS_MANAGER_AVAILABLE:
    try:
        from simple_wordpress_deployer import SimpleWordPressDeployer
        WordPressManager = SimpleWordPressDeployer  # Alias for compatibility
        SIMPLE_DEPLOYER_AVAILABLE = True
    except ImportError:
        print("❌ ERROR: No deployment method available!")
        print("   WordPressManager not found and SimpleWordPressDeployer not available")
        sys.exit(1)

# Load .env file for credentials
try:
    from dotenv import load_dotenv, dotenv_values
    env_vars = dotenv_values("D:/Agent_Cellphone_V2_Repository/.env")
    import os
    for key, value in env_vars.items():
        if value and key not in os.environ:
            os.environ[key] = value
    load_dotenv("D:/Agent_Cellphone_V2_Repository/.env")
except ImportError:
    pass  # dotenv not installed
except Exception:
    pass  # .env file not found


def deploy_prismblossom_theme():
    """Deploy updated theme files for prismblossom.online"""
    print("\n" + "="*60)
    print("🌐 DEPLOYING: prismblossom.online Theme Updates")
    print("="*60 + "\n")

    site_name = "prismblossom.online"
    site_key = "prismblossom"

    # Files to deploy - Neon Purple Homepage Theme Updates
    files_to_deploy = [
        "wp/wp-content/themes/prismblossom/style.css",
        "wp/wp-content/themes/prismblossom/index.php",
        "wp/wp-content/themes/prismblossom/functions.php",
        "wp/wp-content/themes/prismblossom/page-carmyn.php"
    ]

    try:
        # Load site configs - SimpleWordPressDeployer.load_site_configs() checks:
        # 1. Hostinger env vars (.env)
        # 2. .deploy_credentials/sites.json (WordPressManager format)
        # 3. configs/site_configs.json
        site_configs = None
        if SIMPLE_DEPLOYER_AVAILABLE:
            from simple_wordpress_deployer import load_site_configs
            site_configs = load_site_configs()
            if not site_configs:
                print("❌ No site configurations found!")
                print("   Expected one of:")
                print(
                    "   1. Hostinger env vars (HOSTINGER_HOST, HOSTINGER_USER, HOSTINGER_PASS)")
                print(
                    "   2. D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
                print("   3. D:/websites/configs/site_configs.json")
                return False

        # Initialize deployment manager
        if WORDPRESS_MANAGER_AVAILABLE:
            manager = WordPressManager(site_key)
        elif SIMPLE_DEPLOYER_AVAILABLE:
            manager = WordPressManager(site_key, site_configs)
        else:
            print("❌ No deployment method available!")
            return False

        # For SimpleWordPressDeployer, get base remote path from config before connecting
        base_remote_path = None
        if SIMPLE_DEPLOYER_AVAILABLE:
            # Check if site_key or site_name exists in configs
            site_config = site_configs.get(
                site_key) or site_configs.get(site_name, {})
            # Handle both sites.json format (direct keys) and site_configs.json format (nested sftp)
            if 'sftp' in site_config:
                sftp_config = site_config.get('sftp', {})
            else:
                sftp_config = site_config  # sites.json format has keys directly
            base_remote_path = sftp_config.get(
                'remote_path', 'domains/prismblossom.online/public_html')

        # Connect to server
        print(f"📡 Connecting to {site_key}...")
        if SIMPLE_DEPLOYER_AVAILABLE and base_remote_path:
            # SimpleWordPressDeployer.connect() doesn't take parameters, remote_path is stored in config
            if not manager.connect():
                print(f"❌ Failed to connect to {site_key}")
                print("   Check SFTP credentials in configs/site_configs.json")
                return False
        else:
            if not manager.connect():
                print(f"❌ Failed to connect to {site_key}")
                if WORDPRESS_MANAGER_AVAILABLE:
                    print("   Check credentials in .deploy_credentials/sites.json")
                else:
                    print("   Check SFTP credentials in configs/site_configs.json")
                return False
        print("✅ Connected!\n")

        # Deploy each file
        # Files are located at: D:/websites/websites/prismblossom.online/wp/...
        base_path = Path("D:/websites/websites") / site_name
        success_count = 0
        fail_count = 0

        for file_path in files_to_deploy:
            local_path = base_path / file_path
            if not local_path.exists():
                print(f"❌ File not found: {local_path}")
                fail_count += 1
                continue

            print(f"📤 Deploying: {file_path}...")
            try:
                # For SimpleWordPressDeployer, remote path is relative to base_remote_path
                if SIMPLE_DEPLOYER_AVAILABLE:
                    # Remote path should be: wp-content/themes/prismblossom/{filename}
                    # SimpleWordPressDeployer will prepend the base_remote_path automatically
                    filename = local_path.name
                    remote_path = f"wp-content/themes/prismblossom/{filename}"
                    success = manager.deploy_file(local_path, remote_path)
                else:
                    # WordPressManager handles remote path automatically
                    success = manager.deploy_file(local_path)

                if success:
                    print(f"✅ Deployed: {file_path}")
                    success_count += 1
                else:
                    print(f"❌ Failed: {file_path}")
                    fail_count += 1
            except Exception as e:
                print(f"❌ Error deploying {file_path}: {e}")
                import traceback
                traceback.print_exc()
                fail_count += 1

        # Disconnect
        manager.disconnect()

        # Summary
        print(f"\n{'='*60}")
        print("📊 DEPLOYMENT SUMMARY")
        print(f"{'='*60}")
        print(f"   ✅ Succeeded: {success_count}")
        print(f"   ❌ Failed: {fail_count}\n")

        if fail_count == 0:
            print("✅ All files deployed successfully!")
            print("\n💡 Next Steps:")
            print("   1. Clear WordPress cache")
            print("   2. Clear browser cache")
            print("   3. Visit https://prismblossom.online to verify changes")
            print("   4. Test the Carmyn page specifically")
            return True
        else:
            print("⚠️  Some files failed to deploy. Check errors above.")
            return False

    except ValueError as e:
        print(f"❌ Site configuration error: {e}")
        print(f"   Site key '{site_key}' not found in WordPressManager")
        print("   Check .deploy_credentials/sites.json for configuration")
        return False
    except Exception as e:
        print(f"❌ Error deploying {site_name}: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution"""
    success = deploy_prismblossom_theme()
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())
