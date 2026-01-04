#!/usr/bin/env python3
"""
Deploy DigitalDreamscape Theme Files
====================================

Deploys updated theme files for digitaldreamscape.site to the live server.
Fixes critical error by moving function definition to functions.php.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-23
"""

import sys
from pathlib import Path

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent))

# Add deployment tools to path (try multiple locations)
MAIN_REPO_TOOLS = Path("D:/Agent_Cellphone_V2_Repository/tools")
CURRENT_DIR = Path(__file__).parent
WORDPRESS_MANAGER_AVAILABLE = False
SIMPLE_DEPLOYER_AVAILABLE = False

# Try multiple paths for deployment tools
tool_paths = [str(MAIN_REPO_TOOLS), str(CURRENT_DIR)]
for path in tool_paths:
    if path not in sys.path:
        sys.path.insert(0, path)

# Try WordPressManager first
try:
    from wordpress_manager import WordPressManager
    WORDPRESS_MANAGER_AVAILABLE = True
    print("✅ Using WordPressManager for deployment")
except ImportError:
    print("⚠️ WordPressManager not available, trying SimpleWordPressDeployer")

# Try SimpleWordPressDeployer as fallback
if not WORDPRESS_MANAGER_AVAILABLE:
    try:
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
        WordPressManager = SimpleWordPressDeployer  # Alias for compatibility
        SIMPLE_DEPLOYER_AVAILABLE = True
        print("✅ Using SimpleWordPressDeployer for deployment")
    except ImportError as e:
        print("❌ ERROR: No deployment method available!")
        print(f"   WordPressManager not found: {e}")
        print("   SimpleWordPressDeployer not found")
        sys.exit(1)

# Ensure we have the load_site_configs function
if not SIMPLE_DEPLOYER_AVAILABLE:
    try:
        from simple_wordpress_deployer import load_site_configs
    except ImportError:
        # Fallback: try to load from any available module
        pass

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


def deploy_digitaldreamscape_theme():
    """Deploy updated theme files for digitaldreamscape.site"""
    print("\n" + "="*60)
    print("🌐 DEPLOYING: digitaldreamscape.site Theme Fixes")
    print("="*60 + "\n")

    site_name = "digitaldreamscape.site"
    site_key = "digitaldreamscape.site"

    # Files to deploy - Dreamscape Codex implementation
    files_to_deploy = [
        "wp/wp-content/themes/digitaldreamscape/style.css",
        "wp/wp-content/themes/digitaldreamscape/functions.php",
        "wp/wp-content/themes/digitaldreamscape/header.php",
        "wp/wp-content/themes/digitaldreamscape/page-blog.php"  # Dreamscape Codex
    ]

    try:
        # Load site configs (needed for both deployment methods)
        site_configs = None
        try:
            site_configs = load_site_configs()
            print(f"📋 Loaded {len(site_configs) if site_configs else 0} site configurations")
        except Exception as e:
            print(f"⚠️ Error loading site configs: {e}")

        if not site_configs:
            print("❌ No site configurations found!")
            print("   Expected one of:")
            print(
                "   1. Hostinger env vars (HOSTINGER_HOST, HOSTINGER_USER, HOSTINGER_PASS)")
            print(
                "   2. D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
            print("   3. D:/websites/config/site_configs.json")
            return False

        # Debug: Check if our site is in the configs
        if site_key in site_configs:
            print(f"✅ Site '{site_key}' found in configurations")
        else:
            print(f"⚠️ Site '{site_key}' not found in configurations")
            available_sites = list(site_configs.keys())[:5]
            print(f"   Available sites: {available_sites}")
            # Try alternative site keys
            alt_keys = ['digitaldreamscape', 'digitaldreamscape.site']
            for alt_key in alt_keys:
                if alt_key in site_configs and alt_key != site_key:
                    print(f"   Found alternative: {alt_key}")
                    site_key = alt_key
                    break

        # Initialize deployment manager
        if WORDPRESS_MANAGER_AVAILABLE and site_configs:
            # WordPressManager needs site configs passed to it
            manager = WordPressManager(site_key, site_configs)
        elif SIMPLE_DEPLOYER_AVAILABLE:
            manager = WordPressManager(site_key, site_configs)
        else:
            print("❌ No deployment method available!")
            return False

        # Get site configuration
        site_config = site_configs.get(site_key)
        if not site_config:
            print(f"❌ Site configuration for '{site_key}' not found!")
            return False

        print(f"🔧 Site config: {site_config.get('site_url', 'unknown')}")

        # Connect to server
        print(f"📡 Connecting to {site_key}...")
        try:
            if not manager.connect():
                print(f"❌ Failed to connect to {site_key}")
                print("   Check SFTP credentials in configuration")
                return False
            print("✅ Connected!\n")
        except Exception as e:
            print(f"❌ Connection error: {e}")
            return False

        # Deploy each file
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
                if SIMPLE_DEPLOYER_AVAILABLE:
                    # For SimpleWordPressDeployer, construct the full remote path
                    filename = local_path.name
                    remote_path = f"wp-content/themes/digitaldreamscape/{filename}"
                    success = manager.deploy_file(local_path, remote_path)
                else:
                    # For WordPressManager, just pass the local path
                    success = manager.deploy_file(local_path)

                if success:
                    print(f"✅ Deployed: {file_path}")
                    success_count += 1
                else:
                    print(f"❌ Failed: {file_path}")
                    fail_count += 1
            except Exception as e:
                print(f"❌ Error deploying {file_path}: {e}")
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
            print("   3. Visit https://digitaldreamscape.site to verify fixes")
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
    success = deploy_digitaldreamscape_theme()
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())
