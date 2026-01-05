#!/usr/bin/env python3
"""
Test all configured websites
"""

import requests
import json

def test_website(url):
    """Test website and return status."""
    try:
        response = requests.get(url, timeout=10)
        return response.status_code, len(response.content)
    except:
        return 'ERROR', 0

def main():
    """Test all configured websites."""
    # Load site configurations
    with open('config/site_configs.json', 'r') as f:
        site_configs = json.load(f)

    print('🔍 TESTING ALL CONFIGURED WEBSITES')
    print('=' * 50)

    results = {}
    for site_name, config in site_configs.items():
        url = config['site_url']
        status, size = test_website(url)
        results[site_name] = {'status': status, 'size': size}

        status_icon = '✅' if status == 200 else '❌' if status == 500 else '⚠️'
        print(f'{status_icon} {site_name}: HTTP {status} ({size} bytes)')

    print('\n📊 SUMMARY')
    print('-' * 30)
    working = sum(1 for r in results.values() if r['status'] == 200)
    broken = sum(1 for r in results.values() if r['status'] == 500)
    other = len(results) - working - broken

    print(f'✅ Working: {working} sites')
    print(f'❌ HTTP 500: {broken} sites')
    print(f'⚠️  Other issues: {other} sites')

    if broken > 0:
        broken_sites = [site for site, r in results.items() if r['status'] == 500]
        print(f'\n🚨 HTTP 500 sites: {broken_sites}')

if __name__ == "__main__":
    main()