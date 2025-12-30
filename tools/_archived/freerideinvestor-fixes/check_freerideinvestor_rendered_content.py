#!/usr/bin/env python3
"""Check rendered HTML and WordPress homepage settings."""

import sys
import requests
from bs4 import BeautifulSoup
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_rendered_html():
    """Check what's actually rendered in the HTML."""
    url = "https://freerideinvestor.com"
    r = requests.get(url, timeout=10)
    soup = BeautifulSoup(r.text, 'html.parser')
    
    main = soup.find('main')
    print("=" * 70)
    print("RENDERED HTML ANALYSIS")
    print("=" * 70)
    print(f"Main tag found: {main is not None}")
    if main:
        print(f"Main content text: {main.get_text()[:300]}")
        print(f"Main HTML: {str(main)[:800]}")
    
    articles = soup.find_all('article')
    print(f"Articles found: {len(articles)}")
    for i, article in enumerate(articles):
        print(f"  Article {i+1}: {article.get_text()[:100]}")
    
    content_area = soup.find('div', class_='content-area')
    print(f"Content area found: {content_area is not None}")
    
    print(f"Full HTML length: {len(r.text)} bytes")
    print(f"Body text length: {len(soup.find('body').get_text()) if soup.find('body') else 0} chars")
    print()


def check_wordpress_settings():
    """Check WordPress homepage settings."""
    site_configs = load_site_configs()
    deployer = SimpleWordPressDeployer("freerideinvestor.com", site_configs)
    deployer.connect()
    
    remote_path = "domains/freerideinvestor.com/public_html"
    
    show_on_front = deployer.execute_command(
        f"cd {remote_path} && wp option get show_on_front --allow-root 2>/dev/null || echo 'posts'"
    ).strip()
    
    page_on_front = deployer.execute_command(
        f"cd {remote_path} && wp option get page_on_front --allow-root 2>/dev/null || echo '0'"
    ).strip()
    
    print("=" * 70)
    print("WORDPRESS HOMEPAGE SETTINGS")
    print("=" * 70)
    print(f"Show on front: {show_on_front}")
    print(f"Page on front ID: {page_on_front}")
    
    if show_on_front == "page" and page_on_front != "0":
        # Check if that page exists and has content
        page_content = deployer.execute_command(
            f"cd {remote_path} && wp post get {page_on_front} --field=content --allow-root 2>/dev/null || echo 'not found'"
        )
        print(f"Front page content length: {len(page_content)} chars")
        if len(page_content) < 50:
            print("   ⚠️  Front page has very little content")
    
    deployer.disconnect()


if __name__ == "__main__":
    check_rendered_html()
    check_wordpress_settings()






