#!/usr/bin/env python3
"""Check contact page HTML structure"""

import requests

url = "https://freerideinvestor.com/contact/"

r = requests.get(url, timeout=10)
html = r.text

main_count = html.lower().count('<main')
print(f"Main tags found: {main_count}")

main_pos = html.lower().find('<main')
if main_pos > 0:
    print("\nFirst main tag context:")
    print(html[max(0, main_pos-50):main_pos+300])

# Check for hero section
hero_pos = html.lower().find('hero blog-hero')
if hero_pos > 0:
    print("\n✅ Hero section found")
    print(html[hero_pos-100:hero_pos+200])
else:
    print("\n❌ Hero section not found")

# Check for container
container_pos = html.lower().find('<div class="container">')
if container_pos > 0:
    print("\n✅ Container found")
else:
    print("\n❌ Container not found")

