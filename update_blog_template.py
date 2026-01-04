#!/usr/bin/env python3
"""Update Blog page template to use default template"""

import json
import requests
import base64
from pathlib import Path

def main():
    # Load API credentials from site_configs.json
    config_path = Path('config/site_configs.json')
    with open(config_path, 'r') as f:
        site_configs = json.load(f)

    site_config = site_configs.get('digitaldreamscape.site', {}).get('rest_api')

    if site_config:
        print(f'Updating page template for: {site_config["site_url"]}')

        # Prepare authentication
        credentials = f"{site_config['username']}:{site_config['app_password']}"
        auth_header = base64.b64encode(credentials.encode()).decode()

        headers = {
            'Authorization': f'Basic {auth_header}',
            'Content-Type': 'application/json'
        }

        # Update page template (page ID 5 is the Blog page)
        page_id = 5
        api_url = f"{site_config['site_url']}/wp-json/wp/v2/pages/{page_id}"

        # Data to update the template - set to empty string for default template
        data = {
            'template': ''  # Empty string means default template (page.php)
        }

        try:
            response = requests.post(api_url, json=data, headers=headers, timeout=30)
            print(f'API Response Status: {response.status_code}')

            if response.status_code == 200:
                result = response.json()
                print('✅ Successfully updated page template')
                print(f'Template set to: {result.get("template", "default")}')
                return True
            else:
                print(f'❌ Failed to update template: {response.text}')
                return False

        except Exception as e:
            print(f'❌ Error updating page template: {e}')
            return False
    else:
        print('❌ Could not load API credentials')
        return False

if __name__ == '__main__':
    success = main()
    exit(0 if success else 1)