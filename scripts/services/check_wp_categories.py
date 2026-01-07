#!/usr/bin/env python3
"""
Check WordPress Categories for Digital Dreamscape
"""

import requests
import os

def check_wp_categories():
    """Check categories on digitaldreamscape.site"""

    # Environment variables are loaded from system/shell

    # Get WordPress API details
    base_url = os.environ.get('DREAM_WP_URL', '').replace('/wp-json/wp/v2', '')
    user = os.environ.get('DREAM_WP_USER')
    password = os.environ.get('DREAM_WP_APP_PASS')

    if not all([base_url, user, password]):
        print('❌ WordPress credentials not found in environment')
        return

    print('🌐 Checking WordPress categories...')
    print(f'📍 Site: {base_url}')

    # Get categories
    response = requests.get(f'{base_url}/wp-json/wp/v2/categories', auth=(user, password))

    if response.status_code == 200:
        categories = response.json()
        print(f'\n📂 Found {len(categories)} categories:')
        print('=' * 50)

        for cat in categories:
            cat_id = cat.get('id', 'N/A')
            name = cat.get('name', 'N/A')
            slug = cat.get('slug', 'N/A')
            count = cat.get('count', 0)

            print(f'  {cat_id}: {name} ({slug}) - {count} posts')

        # Check for Digital Dreamscape specific categories
        dd_categories = [cat for cat in categories if 'dream' in cat.get('slug', '').lower() or 'episode' in cat.get('slug', '').lower()]

        if dd_categories:
            print(f'\n🎭 Digital Dreamscape categories found:')
            for cat in dd_categories:
                print(f'  ✓ {cat.get("name")} ({cat.get("slug")})')
        else:
            print('\n⚠️ No Digital Dreamscape specific categories found')
            print('💡 Consider creating categories for questlines: infrastructure-architecture, agent-coordination, digitaldreamscape-chronicles, canon-automation, development-operations')

    else:
        print(f'❌ Failed to get categories: {response.status_code}')
        print(f'   Response: {response.text[:200]}...')

if __name__ == "__main__":
    check_wp_categories()