#!/usr/bin/env python3
"""
Verify Menu Consistency Across Pages - freerideinvestor.com
===========================================================

Checks that menu styling is consistent across different pages.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import requests
from bs4 import BeautifulSoup
from pathlib import Path

PAGES_TO_CHECK = [
    "https://freerideinvestor.com/",
    "https://freerideinvestor.com/blog",
    "https://freerideinvestor.com/about",
    "https://freerideinvestor.com/contact",
]

def check_page_menu(page_url):
    """Check menu styling on a specific page."""
    try:
        response = requests.get(page_url, timeout=10)
        response.raise_for_status()
        
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Find menu navigation
        nav = soup.find('nav', class_='main-nav')
        if not nav:
            return {
                'url': page_url,
                'found': False,
                'error': 'Menu navigation not found'
            }
        
        # Find nav-list
        nav_list = nav.find('ul', class_='nav-list')
        if not nav_list:
            nav_list = nav.find('ul')
        
        # Check for menu items
        menu_items = nav_list.find_all('li') if nav_list else []
        
        # Check for inline styles that might override our CSS
        has_inline_styles = False
        inline_styles = []
        
        if nav_list and nav_list.get('style'):
            has_inline_styles = True
            inline_styles.append(f"nav-list: {nav_list.get('style')}")
        
        for link in nav.find_all('a'):
            if link.get('style'):
                has_inline_styles = True
                inline_styles.append(f"link: {link.get('style')}")
        
        return {
            'url': page_url,
            'found': True,
            'menu_items_count': len(menu_items),
            'has_inline_styles': has_inline_styles,
            'inline_styles': inline_styles,
            'nav_classes': nav.get('class', []),
        }
    except Exception as e:
        return {
            'url': page_url,
            'found': False,
            'error': str(e)
        }

def main():
    print("=" * 70)
    print("VERIFYING MENU CONSISTENCY ACROSS PAGES")
    print("=" * 70)
    print()
    
    results = []
    for page_url in PAGES_TO_CHECK:
        print(f"Checking: {page_url}")
        result = check_page_menu(page_url)
        results.append(result)
        if result.get('found'):
            print(f"  ✅ Menu found - {result.get('menu_items_count', 0)} items")
            if result.get('has_inline_styles'):
                print(f"  ⚠️  Warning: Inline styles found (may override CSS)")
                for style in result.get('inline_styles', []):
                    print(f"     - {style}")
            else:
                print(f"  ✅ No inline styles (CSS should apply)")
        else:
            print(f"  ❌ {result.get('error', 'Unknown error')}")
        print()
    
    print("=" * 70)
    print("SUMMARY")
    print("=" * 70)
    
    found_pages = [r for r in results if r.get('found')]
    pages_with_inline_styles = [r for r in found_pages if r.get('has_inline_styles')]
    
    print(f"Pages checked: {len(results)}")
    print(f"Menus found: {len(found_pages)}")
    print(f"Pages with inline styles: {len(pages_with_inline_styles)}")
    
    if pages_with_inline_styles:
        print("\n⚠️  ISSUES FOUND:")
        for result in pages_with_inline_styles:
            print(f"  - {result['url']}")
            for style in result.get('inline_styles', []):
                print(f"    {style}")
    else:
        print("\n✅ No inline styles found - CSS should be consistent")
    
    print()

if __name__ == "__main__":
    main()

