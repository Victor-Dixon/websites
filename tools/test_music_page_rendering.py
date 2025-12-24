#!/usr/bin/env python3
"""Test music page rendering"""

import sys
import requests
from pathlib import Path

project_root = Path(__file__).parent.parent
sys.path.insert(0, str(project_root))

def test_page_rendering():
    url = "https://ariajet.site/music/"
    
    print(f"🔍 Testing music page rendering...")
    print("=" * 60)
    
    try:
        response = requests.get(url, timeout=10)
        html = response.text
        
        print(f"✅ Status Code: {response.status_code}")
        print(f"📄 HTML Length: {len(html)} characters")
        
        # Check for key elements
        checks = {
            "main tag": '<main' in html.lower(),
            "container class": 'class="container"' in html or "class='container'" in html,
            "music-page class": 'music-page' in html.lower(),
            "page-title": 'page-title' in html.lower(),
            "audio player": '<audio' in html.lower(),
            "oxygen smino": 'oxygen' in html.lower() and 'smino' in html.lower(),
            "have_posts content": 'Oxygen (Smino Mix)' in html or 'track-title' in html.lower(),
        }
        
        print(f"\n📊 Content Checks:")
        for check, result in checks.items():
            status = "✅" if result else "❌"
            print(f"   {status} {check}: {result}")
        
        # Extract main content area
        if '<main' in html.lower():
            main_start = html.lower().find('<main')
            main_end = html.lower().find('</main>', main_start)
            if main_end > main_start:
                main_content = html[main_start:main_end+7]
                print(f"\n📝 Main Content (first 500 chars):")
                print(main_content[:500])
        
        # Check if template is being used
        if 'page-music.php' in html or 'Music Page' in html:
            print(f"\n✅ Template appears to be loaded")
        else:
            print(f"\n⚠️  Template might not be loading correctly")
            
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    test_page_rendering()

