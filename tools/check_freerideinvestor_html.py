#!/usr/bin/env python3
"""Check rendered HTML for content."""

import requests

r = requests.get('https://freerideinvestor.com', timeout=10)
html = r.text

print(f"Status: {r.status_code}")
print(f"HTML length: {len(html)}")
print(f"Has <main tag: {'<main' in html}")
print(f"Has hero-section: {'hero-section' in html}")
print(f"Has site-main: {'site-main' in html}")
print(f"Has main-content: {'main-content' in html}")
print(f"Has container: {'container' in html}")

# Find body content
body_start = html.find('<body')
if body_start > 0:
    body_end = html.find('</body>', body_start)
    if body_end > 0:
        body = html[body_start:body_end]
        print(f"\nBody length: {len(body)}")
        
        # Check for main content
        if '<main' in body:
            main_start = body.find('<main')
            main_end = body.find('</main>', main_start)
            if main_end > 0:
                main_content = body[main_start:main_end+7]
                print(f"\nMain tag found! Length: {len(main_content)}")
                print(f"First 500 chars of main:")
                print(main_content[:500])
            else:
                print("\n⚠️  <main> tag found but no closing tag")
        else:
            print("\n❌ No <main> tag found in body")
            print("First 1000 chars of body:")
            print(body[:1000])
            print("\nSearching for content...")
            if 'section' in body.lower():
                print("✅ Found 'section'")
            if 'article' in body.lower():
                print("✅ Found 'article'")
            div_count = body.lower().count('<div')
            print(f"Divs found: {div_count}")

