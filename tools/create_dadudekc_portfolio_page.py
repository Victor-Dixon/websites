#!/usr/bin/env python3
"""Create portfolio page on dadudekc.com using WordPress API"""

import os
import requests
from pathlib import Path

def get_wp_credentials():
    """Get WordPress credentials from site config"""
    # Hardcoded credentials from config/site_configs.json for dadudekc.com
    wp_url = "https://dadudekc.com"
    wp_user = "DadudeKC@Gmail.com"
    wp_app_pass = "KHtl XOwZ FNgJ WTzF HUqc mUvP"

    return wp_url, wp_user, wp_app_pass

def create_portfolio_page(wp_url, wp_user, wp_app_pass):
    """Create portfolio page using WordPress REST API"""

    # WordPress REST API endpoint for pages
    api_url = f"{wp_url}/wp-json/wp/v2/pages"

    # Page data
    page_data = {
        'title': 'Portfolio',
        'content': '<!-- wp:paragraph -->\n<p>Welcome to my portfolio of shipped systems and solved problems. Here you\'ll find detailed case studies of projects that went from concept to completion.</p>\n<!-- /wp:paragraph -->',
        'status': 'publish',
        'slug': 'portfolio',
        'template': 'page-portfolio.php'
    }

    try:
        # Make API request
        response = requests.post(
            api_url,
            auth=(wp_user, wp_app_pass),
            json=page_data,
            headers={'Content-Type': 'application/json'}
        )

        if response.status_code == 201:
            page_data = response.json()
            print(f"✅ Portfolio page created successfully!")
            print(f"   Page ID: {page_data['id']}")
            print(f"   URL: {page_data['link']}")
            return True
        elif response.status_code == 400 and 'already exists' in response.text.lower():
            print("ℹ️  Portfolio page already exists")
            return True
        else:
            print(f"❌ Failed to create portfolio page: {response.status_code}")
            print(f"   Response: {response.text}")
            return False

    except Exception as e:
        print(f"❌ Error creating portfolio page: {e}")
        return False

def main():
    print("🎨 Creating portfolio page on dadudekc.com...")

    wp_url, wp_user, wp_app_pass = get_wp_credentials()
    if not wp_url:
        return

    success = create_portfolio_page(wp_url, wp_user, wp_app_pass)

    if success:
        print("\n📝 Next steps:")
        print("   1. Check the portfolio page at https://dadudekc.com/portfolio")
        print("   2. Update navigation menu to include portfolio link if needed")
        print("   3. Import sample projects using the import script")
    else:
        print("\n❌ Portfolio page creation failed")
        print("   You may need to create the page manually in WordPress admin")

if __name__ == '__main__':
    main()