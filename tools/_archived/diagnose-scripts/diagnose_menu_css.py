#!/usr/bin/env python3
"""
Diagnose Menu CSS Issues on freerideinvestor.com
Checks what CSS files are loaded and what styles are being applied
"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
import requests
import re

def check_css_files_loaded():
    """Check what CSS files are actually loaded on the live site"""
    print("🔍 Checking CSS files loaded on live site...")
    try:
        r = requests.get('https://freerideinvestor.com/?nocache=999999', timeout=10)
        html = r.text
        
        # Find all CSS links
        css_pattern = r'<link[^>]*href=["\']([^"\']+\.css[^"\']*)["\']'
        css_files = re.findall(css_pattern, html)
        
        print(f"\n✅ Found {len(css_files)} CSS files:")
        for i, css_file in enumerate(css_files[:15], 1):
            print(f"  {i}. {css_file}")
            
        # Check for navigation CSS
        nav_css_found = any('navigation' in f.lower() or '_navigation' in f for f in css_files)
        header_css_found = any('header-footer' in f.lower() or '_header' in f for f in css_files)
        
        print(f"\n📊 Navigation CSS loaded: {'✅ YES' if nav_css_found else '❌ NO'}")
        print(f"📊 Header CSS loaded: {'✅ YES' if header_css_found else '❌ NO'}")
        
        return css_files
    except Exception as e:
        print(f"❌ Error checking CSS files: {e}")
        return []

def check_remote_css_file(deployer, file_path):
    """Check if a CSS file exists on remote"""
    remote_path = f'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern/{file_path}'
    result = deployer.execute_command(f'test -f {remote_path} && echo "EXISTS" || echo "NOT_FOUND"')
    return "EXISTS" in result

def main():
    print("=" * 60)
    print("MENU CSS DIAGNOSTIC TOOL")
    print("=" * 60)
    
    # Check CSS files loaded
    css_files = check_css_files_loaded()
    
    # Check remote files
    print("\n🔍 Checking remote CSS files...")
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    
    files_to_check = [
        'css/styles/components/_navigation.css',
        'css/styles/layout/_header-footer.css',
        'inc/assets/enqueue.php',
        'style.css',
        'header.php'
    ]
    
    for file_path in files_to_check:
        full_path = f'{remote_path}/{file_path}'
        result = deployer.execute_command(f'test -f {full_path} && echo "EXISTS" || echo "NOT_FOUND"')
        status = "✅ EXISTS" if "EXISTS" in result else "❌ NOT FOUND"
        print(f"  {status}: {file_path}")
    
    # Check enqueue.php content
    print("\n🔍 Checking enqueue.php for CSS enqueues...")
    enqueue_path = f'{remote_path}/inc/assets/enqueue.php'
    result = deployer.execute_command(f'grep -i "wp_enqueue_style.*navigation\|wp_enqueue_style.*header" {enqueue_path} 2>&1 | head -10')
    if result.strip():
        print("  Found navigation/header enqueues:")
        for line in result.strip().split('\n')[:5]:
            print(f"    {line}")
    else:
        print("  ⚠️  No navigation/header CSS enqueues found!")
    
    deployer.disconnect()
    
    print("\n" + "=" * 60)
    print("DIAGNOSTIC COMPLETE")
    print("=" * 60)

if __name__ == '__main__':
    main()

