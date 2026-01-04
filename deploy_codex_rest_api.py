#!/usr/bin/env python3
"""Deploy Dreamscape Codex using WordPress REST API"""

import json
import requests
import base64
from pathlib import Path

def load_api_credentials():
    """Load WordPress REST API credentials"""
    # Load from blogging_api.json
    api_config_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/blogging_api.json")
    with open(api_config_path, 'r') as f:
        api_configs = json.load(f)

    return api_configs.get('digitaldreamscape.site')

def deploy_theme_file_via_rest_api(site_config, file_path, file_content):
    """Deploy a theme file using WordPress REST API"""

    # Prepare authentication
    credentials = f"{site_config['username']}:{site_config['app_password']}"
    auth_header = base64.b64encode(credentials.encode()).decode()

    headers = {
        'Authorization': f'Basic {auth_header}',
        'Content-Type': 'application/text',  # Sending raw file content
        'Content-Disposition': f'attachment; filename="{file_path}"'
    }

    # WordPress REST API endpoint for theme file updates
    # Note: WordPress REST API doesn't have direct theme file update endpoint
    # This would require a custom plugin or alternative approach
    api_url = f"{site_config['site_url']}/wp-json/wp/v2/themes/digitaldreamscape/{file_path}"

    try:
        response = requests.put(api_url, data=file_content, headers=headers, timeout=30)
        return response.status_code == 200
    except Exception as e:
        print(f"REST API deployment failed: {e}")
        return False

def deploy_via_sftp():
    """Deploy using SFTP as fallback"""
    print("🔄 Falling back to SFTP deployment...")

    # Import and use SimpleWordPressDeployer
    import sys
    sys.path.insert(0, 'D:/websites/ops/deployment')

    try:
        from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

        # Load configs and create deployer
        configs = load_site_configs()
        deployer = SimpleWordPressDeployer("digitaldreamscape.site", configs)

        if not deployer.connect():
            print("❌ SFTP connection failed")
            return False

        print("✅ SFTP connected successfully")

        # Files to deploy
        local_base = Path('websites/digitaldreamscape.site')
        files_to_deploy = [
            'wp/wp-content/themes/digitaldreamscape/style.css',
            'wp/wp-content/themes/digitaldreamscape/page-blog.php',
            'wp/wp-content/themes/digitaldreamscape/functions.php',
            'wp/wp-content/themes/digitaldreamscape/header.php'
        ]

        success_count = 0
        for file_path in files_to_deploy:
            local_path = local_base / file_path
            if not local_path.exists():
                print(f"❌ Local file missing: {local_path}")
                continue

            # Use relative remote path for deploy_file
            remote_path = f"wp-content/themes/digitaldreamscape/{Path(file_path).name}"
            if deployer.deploy_file(local_path, remote_path):
                print(f"✅ Deployed: {file_path}")
                success_count += 1
            else:
                print(f"❌ Failed: {file_path}")

        deployer.disconnect()

        if success_count == len(files_to_deploy):
            print("🎉 SFTP deployment successful!")
            return True
        else:
            print(f"⚠️ Partial deployment: {success_count}/{len(files_to_deploy)} files")
            return success_count > 0

    except Exception as e:
        print(f"❌ SFTP deployment error: {e}")
        return False

def main():
    print("🚀 DEPLOYING DREAMSCAPE CODEX")
    print("=" * 50)

    # Try REST API first (configured method)
    print("🔗 Attempting WordPress REST API deployment...")
    site_config = load_api_credentials()

    if site_config:
        print(f"📡 API Config loaded for: {site_config['site_url']}")

        # For now, skip REST API since WordPress doesn't support direct theme file updates
        # Fall back to SFTP
        print("⚠️ WordPress REST API doesn't support direct theme file updates")
        print("🔄 Using SFTP deployment instead...")
    else:
        print("❌ Could not load REST API credentials")

    # Use SFTP deployment
    return deploy_via_sftp()

if __name__ == '__main__':
    success = main()
    exit(0 if success else 1)