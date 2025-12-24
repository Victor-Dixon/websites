#!/usr/bin/env python3
"""
Verify Home Template Styles Are Rendering
==========================================

Checks if the stunning blog styles are actually in the rendered HTML.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
import requests

def verify_styles():
    """Verify styles are rendering."""
    site_url = "https://freerideinvestor.com/blog/"
    
    print("=" * 70)
    print(f"🔍 VERIFYING HOME TEMPLATE STYLES: {site_url}")
    print("=" * 70)
    print()
    
    try:
        response = requests.get(site_url, timeout=10)
        html = response.text
        
        # Check for key class names
        checks = {
            'stunning-blog-page': 'stunning-blog-page' in html,
            'stunning-blog-hero': 'stunning-blog-hero' in html,
            'stunning-posts-grid': 'stunning-posts-grid' in html,
            'stunning-post-card': 'stunning-post-card' in html,
            'stunning-post-thumbnail': 'stunning-post-thumbnail' in html,
        }
        
        print("1️⃣ Checking for key CSS classes in HTML:")
        for class_name, found in checks.items():
            status = "✅" if found else "❌"
            print(f"   {status} {class_name}: {'Found' if found else 'NOT FOUND'}")
        
        # Check for CSS styles
        print()
        print("2️⃣ Checking for CSS styles:")
        has_styles = '<style>' in html and 'stunning-blog' in html
        print(f"   {'✅' if has_styles else '❌'} CSS styles in HTML: {'Found' if has_styles else 'NOT FOUND'}")
        
        if has_styles:
            # Extract style block
            style_start = html.find('<style>')
            if style_start != -1:
                style_end = html.find('</style>', style_start)
                if style_end != -1:
                    style_block = html[style_start:style_end+8]
                    if 'stunning-blog-page' in style_block:
                        print("   ✅ stunning-blog-page styles found in <style> block")
        
        # Check for hero section content
        print()
        print("3️⃣ Checking for hero section content:")
        has_hero_title = 'Trading Insights' in html or 'trading insights' in html.lower()
        print(f"   {'✅' if has_hero_title else '❌'} Hero section title: {'Found' if has_hero_title else 'NOT FOUND'}")
        
        return all(checks.values()) and has_styles
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution."""
    success = verify_styles()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())

