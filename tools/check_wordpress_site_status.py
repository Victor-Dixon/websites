#!/usr/bin/env python3
"""
Check WordPress site status and active theme remotely.
"""

import requests
from bs4 import BeautifulSoup
import json
from datetime import datetime

class WordPressSiteChecker:
    def __init__(self, site_url):
        self.site_url = site_url.rstrip('/')
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })

    def check_site_status(self):
        """Check basic site status"""
        try:
            response = self.session.get(self.site_url, timeout=10)
            return {
                'status_code': response.status_code,
                'accessible': response.status_code == 200,
                'content_length': len(response.content),
                'server': response.headers.get('Server', 'Unknown'),
                'content_type': response.headers.get('Content-Type', 'Unknown')
            }
        except Exception as e:
            return {
                'status_code': None,
                'accessible': False,
                'error': str(e)
            }

    def get_wordpress_info(self):
        """Try to get WordPress information from meta tags"""
        try:
            response = self.session.get(self.site_url)
            if response.status_code != 200:
                return None

            soup = BeautifulSoup(response.content, 'html.parser')

            # Check for WordPress generator meta tag
            generator = soup.find('meta', attrs={'name': 'generator'})
            wp_version = None
            if generator and 'WordPress' in generator.get('content', ''):
                wp_version = generator['content'].replace('WordPress ', '')

            # Check for theme info in HTML comments or classes
            theme_info = None
            for comment in soup.find_all(string=lambda text: isinstance(text, str)):
                if 'Theme Name:' in comment:
                    theme_info = comment.strip()
                    break

            # Check body classes for theme info
            body = soup.find('body')
            theme_class = None
            if body:
                classes = body.get('class', [])
                for cls in classes:
                    if 'theme-' in cls:
                        theme_class = cls.replace('theme-', '')
                        break

            return {
                'wordpress_version': wp_version,
                'theme_info': theme_info,
                'theme_class': theme_class,
                'has_wordpress': wp_version is not None
            }

        except Exception as e:
            return {
                'error': str(e),
                'has_wordpress': False
            }

    def check_for_500_error_details(self):
        """Try to get more details about 500 errors"""
        try:
            response = self.session.get(self.site_url)
            if response.status_code == 500:
                # Check if there's any content in the response
                content = response.text.strip()
                if len(content) < 100:  # Very little content suggests minimal error page
                    return {
                        'error_type': 'minimal_500',
                        'content_length': len(content),
                        'content_preview': content[:200] if content else 'Empty response'
                    }
                else:
                    return {
                        'error_type': 'detailed_500',
                        'content_length': len(content)
                    }
            return None
        except Exception as e:
            return {
                'error_type': 'connection_error',
                'error': str(e)
            }

def main():
    sites = [
        'https://freerideinvestor.com',
        'https://prismblossom.online'
    ]

    print("🔍 WordPress Site Status Check")
    print("=" * 50)

    for site_url in sites:
        print(f"\n📋 Checking {site_url}")
        print("-" * 30)

        checker = WordPressSiteChecker(site_url)

        # Basic status
        status = checker.check_site_status()
        print(f"Status Code: {status.get('status_code', 'N/A')}")
        print(f"Accessible: {'✅' if status.get('accessible') else '❌'}")
        print(f"Content Length: {status.get('content_length', 0)} bytes")

        if status.get('accessible'):
            # WordPress info
            wp_info = checker.get_wordpress_info()
            if wp_info:
                print(f"WordPress: {'✅' if wp_info.get('has_wordpress') else '❌'}")
                if wp_info.get('wordpress_version'):
                    print(f"Version: {wp_info['wordpress_version']}")
                if wp_info.get('theme_info'):
                    print(f"Theme: {wp_info['theme_info']}")
                if wp_info.get('theme_class'):
                    print(f"Theme Class: {wp_info['theme_class']}")
        else:
            # 500 error details
            error_details = checker.check_for_500_error_details()
            if error_details:
                print(f"Error Type: {error_details.get('error_type', 'Unknown')}")
                if 'content_preview' in error_details:
                    print(f"Content Preview: {error_details['content_preview']}")

    print("\n" + "=" * 50)

if __name__ == '__main__':
    main()