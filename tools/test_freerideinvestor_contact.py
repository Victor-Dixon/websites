#!/usr/bin/env python3
"""Test freerideinvestor.com contact page"""

import requests

url = "https://freerideinvestor.com/contact/"

print(f"🔍 Testing contact page: {url}")
print("=" * 60)

try:
    response = requests.get(url, timeout=10)
    html = response.text
    
    print(f"✅ Status Code: {response.status_code}")
    print(f"📄 HTML Length: {len(html)} characters")
    
    # Check for key elements
    checks = {
        "contact form": '<form' in html.lower(),
        "Send Message button": 'Send Message' in html or 'send message' in html.lower(),
        "contact form container": 'contact-form-container' in html.lower(),
        "hero section": 'contact-hero' in html.lower(),
        "Discord section": 'discord-section' in html.lower(),
        "Email Us": 'Email Us' in html or 'email us' in html.lower(),
        "contact page container": 'contact-page-container' in html.lower(),
    }
    
    print(f"\n📊 Content Checks:")
    for check, result in checks.items():
        status = "✅" if result else "❌"
        print(f"   {status} {check}: {result}")
    
    # Extract main content
    if '<main' in html.lower():
        main_start = html.lower().find('<main')
        main_end = html.lower().find('</main>', main_start)
        if main_end > main_start:
            main_content = html[main_start:main_end+7]
            print(f"\n📝 Main Content (first 1000 chars):")
            print(main_content[:1000])
            
except Exception as e:
    print(f"❌ Error: {e}")

