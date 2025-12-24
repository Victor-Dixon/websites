#!/usr/bin/env python3
"""Test menu consistency across all pages"""

import requests
from bs4 import BeautifulSoup

pages = {
    "Homepage": "https://freerideinvestor.com/",
    "Blog": "https://freerideinvestor.com/blog/",
    "About": "https://freerideinvestor.com/about/",
    "Contact": "https://freerideinvestor.com/contact/"
}

results = {}

for page_name, url in pages.items():
    try:
        r = requests.get(url, timeout=10)
        soup = BeautifulSoup(r.text, 'html.parser')
        
        # Find navigation menu
        nav = soup.find('nav', class_='main-nav')
        nav_list = soup.find('ul', class_='nav-list') or soup.find('ul', id='primary-menu')
        
        # Extract menu items
        menu_items = []
        if nav_list:
            for li in nav_list.find_all('li'):
                link = li.find('a')
                if link:
                    menu_items.append({
                        'text': link.get_text(strip=True),
                        'href': link.get('href', '')
                    })
        
        results[page_name] = {
            'status': r.status_code,
            'nav_found': nav is not None,
            'menu_items': menu_items,
            'menu_count': len(menu_items)
        }
        
        print(f"\n{page_name}:")
        print(f"  Status: {r.status_code}")
        print(f"  Nav found: {nav is not None}")
        print(f"  Menu items: {len(menu_items)}")
        for item in menu_items:
            print(f"    - {item['text']}: {item['href']}")
            
    except Exception as e:
        print(f"\n{page_name}: ERROR - {e}")
        results[page_name] = {'error': str(e)}

# Check consistency
print("\n" + "="*60)
print("CONSISTENCY CHECK")
print("="*60)

if all('menu_items' in results.get(p, {}) for p in pages.keys()):
    # Get menu items from first page
    first_page = list(pages.keys())[0]
    reference_items = results[first_page]['menu_items']
    
    print(f"\nReference menu (from {first_page}):")
    for item in reference_items:
        print(f"  - {item['text']}")
    
    all_consistent = True
    for page_name in list(pages.keys())[1:]:
        page_items = results[page_name].get('menu_items', [])
        if page_items != reference_items:
            print(f"\n❌ {page_name} menu differs:")
            print(f"   Expected: {[i['text'] for i in reference_items]}")
            print(f"   Found: {[i['text'] for i in page_items]}")
            all_consistent = False
        else:
            print(f"\n✅ {page_name} menu matches")
    
    if all_consistent:
        print("\n✅ All menus are consistent across all pages!")
else:
    print("\n⚠️  Could not verify consistency - some pages had errors")

