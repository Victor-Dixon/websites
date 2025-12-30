#!/usr/bin/env python3
"""
Test freerideinvestor.com with Default Theme
===========================================

Temporarily switches to a default WordPress theme to test if the empty content
issue is theme-specific or a broader WordPress problem.

Author: Agent-1
Date: 2025-12-22
"""

import sys
import time
import requests
from pathlib import Path
from bs4 import BeautifulSoup

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def test_site_content():
    """Test if site has content."""
    url = "https://freerideinvestor.com"
    r = requests.get(url, timeout=10)
    soup = BeautifulSoup(r.text, 'html.parser')
    
    main = soup.find('main')
    body_text = soup.find('body').get_text() if soup.find('body') else ''
    articles = soup.find_all('article')
    
    return {
        "has_main": main is not None,
        "body_text_length": len(body_text),
        "article_count": len(articles),
        "status_code": r.status_code
    }


def main():
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    
    if not deployer.connect():
        print("❌ Failed to connect")
        return 1
    
    try:
        remote_path = "domains/freerideinvestor.com/public_html"
        
        # Get current theme
        current_theme = deployer.execute_command(
            f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>/dev/null"
        ).strip()
        
        print("=" * 70)
        print("TESTING WITH DEFAULT THEME")
        print("=" * 70)
        print(f"Current theme: {current_theme}")
        
        # Test current state
        print("\n1️⃣  Testing current theme...")
        before = test_site_content()
        print(f"   Main tag: {before['has_main']}")
        print(f"   Body text: {before['body_text_length']} chars")
        print(f"   Articles: {before['article_count']}")
        
        # Switch to default theme
        default_theme = "twentytwentyfour"
        print(f"\n2️⃣  Switching to default theme: {default_theme}...")
        result = deployer.execute_command(
            f"cd {remote_path} && wp theme activate {default_theme} --allow-root 2>/dev/null"
        )
        
        if "Success" in result or "Switched" in result or not result.strip():
            print(f"   ✅ Switched to {default_theme}")
            
            # Wait a moment for theme to activate
            print("   ⏳ Waiting 3 seconds for theme activation...")
            time.sleep(3)
            
            # Test with default theme
            print("\n3️⃣  Testing with default theme...")
            after = test_site_content()
            print(f"   Main tag: {after['has_main']}")
            print(f"   Body text: {after['body_text_length']} chars")
            print(f"   Articles: {after['article_count']}")
            
            # Compare results
            print("\n" + "=" * 70)
            print("COMPARISON")
            print("=" * 70)
            if after['has_main'] and not before['has_main']:
                print("✅ DEFAULT THEME WORKS - Issue is theme-specific!")
                print("   The problem is in freerideinvestor-modern theme")
            elif after['body_text_length'] > before['body_text_length'] * 2:
                print("✅ DEFAULT THEME SHOWS MORE CONTENT - Issue is theme-specific!")
                print("   The problem is in freerideinvestor-modern theme")
            elif not after['has_main'] and not before['has_main']:
                print("⚠️  BOTH THEMES HAVE SAME ISSUE - Problem is not theme-specific")
                print("   Check WordPress posts/pages, database, or configuration")
            else:
                print("⚠️  Results are similar - need further investigation")
            
            # Switch back to original theme
            print(f"\n4️⃣  Switching back to original theme: {current_theme}...")
            deployer.execute_command(
                f"cd {remote_path} && wp theme activate {current_theme} --allow-root 2>/dev/null"
            )
            print(f"   ✅ Switched back to {current_theme}")
            
        else:
            print(f"   ❌ Failed to switch theme: {result}")
            return 1
        
        return 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    finally:
        deployer.disconnect()


if __name__ == "__main__":
    sys.exit(main())






