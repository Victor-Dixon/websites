#!/usr/bin/env python3
"""Verify TradingRobotPlug site content"""

import requests

try:
    r = requests.get('https://tradingrobotplug.com', timeout=10)
    content = r.text.lower()
    
    checks = {
        'hero section': 'hero' in content or ('get started' in content and 'waitlist' in content),
        'waitlist form': 'waitlist' in content or ('email' in content and 'form' in content),
        'CTA buttons': 'get started' in content or 'sign up' in content or 'join' in content,
        'dark theme': 'tradingrobot' in content or 'dark' in content,
        'navigation': 'navigation' in content or 'menu' in content
    }
    
    print("=" * 60)
    print("TradingRobotPlug Content Verification")
    print("=" * 60)
    print(f"\nStatus Code: {r.status_code}")
    print(f"Content Length: {len(r.text)} characters\n")
    
    print("Content Checks:")
    for check, result in checks.items():
        print(f"  {check}: {'✅' if result else '❌'}")
    
    # Extract title
    if '<title>' in r.text:
        title_start = r.text.find('<title>') + 7
        title_end = r.text.find('</title>')
        title = r.text[title_start:title_end]
        print(f"\nPage Title: {title}")
    
    # Check for key phrases
    key_phrases = ['hero', 'waitlist', 'automated trading', 'get started', 'sign up']
    print("\nKey Phrases Found:")
    for phrase in key_phrases:
        count = content.count(phrase)
        print(f"  '{phrase}': {count} occurrence(s)")
    
except Exception as e:
    print(f"❌ Error: {e}")


