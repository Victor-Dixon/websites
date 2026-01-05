#!/usr/bin/env python3
"""
Detailed website testing to capture error messages
"""

import requests

def test_website_detailed(url):
    """Test website and capture detailed response."""
    try:
        print(f'🌐 Testing {url}...')
        response = requests.get(url, timeout=10)

        print(f'   Status Code: {response.status_code}')
        print(f'   Response Size: {len(response.content)} bytes')
        print(f'   Content-Type: {response.headers.get("content-type", "None")}')

        if response.content:
            content = response.content.decode('utf-8', errors='ignore')[:1000]
            print(f'   Response Preview:')
            print(f'   {content}')
        else:
            print('   No content returned')

        return response

    except Exception as e:
        print(f'   ❌ Error: {e}')
        return None

def main():
    """Test both websites in detail."""
    print("🔍 DETAILED WEBSITE ERROR ANALYSIS")
    print("=" * 50)

    sites = ['https://freerideinvestor.com', 'https://prismblossom.online']

    for site in sites:
        response = test_website_detailed(site)
        print()

if __name__ == "__main__":
    main()