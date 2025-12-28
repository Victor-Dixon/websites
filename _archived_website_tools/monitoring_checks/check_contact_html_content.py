#!/usr/bin/env python3
"""Check contact page HTML content"""

import requests
from bs4 import BeautifulSoup

url = "https://freerideinvestor.com/contact/"

try:
    r = requests.get(url, timeout=10)
    html = r.text
    
    print(f"Status Code: {r.status_code}")
    print(f"HTML Length: {len(html)} characters")
    
    soup = BeautifulSoup(html, 'html.parser')
    
    # Check for key elements
    hero = soup.find('section', class_='hero')
    contact_info = soup.find('section', class_='contact-info-section')
    contact_form = soup.find('section', class_='contact-form-section')
    form_tag = soup.find('form', class_='contact-form')
    discord = soup.find('div', class_='discord-section')
    
    print(f"\n✅ Hero section: {hero is not None}")
    print(f"✅ Contact info section: {contact_info is not None}")
    print(f"✅ Contact form section: {contact_form is not None}")
    print(f"✅ Form tag: {form_tag is not None}")
    print(f"✅ Discord section: {discord is not None}")
    
    # Get main content
    main = soup.find('main', class_='site-main')
    if main:
        print(f"\n📄 Main content length: {len(str(main))} characters")
        # Check if container exists
        container = main.find('div', class_='container')
        print(f"✅ Container in main: {container is not None}")
        if container:
            print(f"   Container content length: {len(str(container))} characters")
    
    # Check for error messages
    if 'critical error' in html.lower() or 'fatal error' in html.lower():
        print("\n❌ ERROR DETECTED IN HTML")
        error_pos = html.lower().find('error')
        if error_pos > 0:
            print(f"   Error context: {html[max(0, error_pos-100):error_pos+200]}")
    
except Exception as e:
    print(f"❌ Error: {e}")

