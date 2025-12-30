#!/usr/bin/env python3
"""
Root Cause Analysis for Menu Styling Issues
Identifies what's truly causing the menu styling problems
"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
import requests
import re

def analyze_css_cascade():
    """Analyze CSS cascade and loading order"""
    print("\n" + "=" * 60)
    print("CSS CASCADE ANALYSIS")
    print("=" * 60)
    
    print("\n1️⃣ Checking CSS files loaded on live site...")
    try:
        r = requests.get('https://freerideinvestor.com/?nocache=' + str(1234567890), timeout=10)
        html = r.text
        
        # Find all CSS links
        css_pattern = r'<link[^>]*href=["\']([^"\']+\.css[^"\']*)["\']'
        css_files = re.findall(css_pattern, html)
        
        print(f"\n   Found {len(css_files)} CSS files:")
        for i, css_file in enumerate(css_files[:20], 1):
            nav_indicator = " (NAVIGATION)" if 'navigation' in css_file.lower() else ""
            header_indicator = " (HEADER)" if 'header' in css_file.lower() else ""
            print(f"   {i}. {css_file}{nav_indicator}{header_indicator}")
        
        # Check for inline styles
        inline_pattern = r'<style[^>]*>([^<]+)</style>'
        inline_styles = re.findall(inline_pattern, html, re.DOTALL)
        print(f"\n   Found {len(inline_styles)} inline style blocks")
        
        # Check for navigation CSS
        nav_css_found = any('navigation' in f.lower() or '_navigation' in f for f in css_files)
        header_css_found = any('header-footer' in f.lower() or '_header' in f for f in css_files)
        
        print(f"\n   Navigation CSS in loaded files: {'✅ YES' if nav_css_found else '❌ NO'}")
        print(f"   Header CSS in loaded files: {'✅ YES' if header_css_found else '❌ NO'}")
        
        return css_files, inline_styles
        
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return [], []

def check_enqueue_configuration():
    """Check how CSS is being enqueued"""
    print("\n" + "=" * 60)
    print("ENQUEUE CONFIGURATION ANALYSIS")
    print("=" * 60)
    
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    
    print("\n2️⃣ Checking enqueue functions...")
    
    # Check inc/assets.php
    print("\n   📄 inc/assets.php:")
    result = deployer.execute_command(f'grep -n "wp_enqueue_style" {remote_path}/inc/assets.php 2>&1 | head -20')
    if result.strip() and 'No such file' not in result:
        for line in result.strip().split('\n')[:10]:
            print(f"      {line}")
    else:
        print("      ❌ File not found or no enqueue statements")
    
    # Check functions.php
    print("\n   📄 functions.php:")
    result = deployer.execute_command(f'grep -n "wp_enqueue_style\|wp_add_inline_style" {remote_path}/functions.php 2>&1 | head -20')
    if result.strip():
        for line in result.strip().split('\n')[:10]:
            print(f"      {line}")
    
    # Check if inc/assets/enqueue.php exists
    print("\n   📄 inc/assets/enqueue.php:")
    result = deployer.execute_command(f'test -f {remote_path}/inc/assets/enqueue.php && echo "EXISTS" || echo "NOT_FOUND"')
    exists = "EXISTS" in result
    print(f"      {'✅ EXISTS' if exists else '❌ NOT FOUND'}")
    
    if exists:
        result = deployer.execute_command(f'grep -n "wp_enqueue_style" {remote_path}/inc/assets/enqueue.php 2>&1 | head -10')
        if result.strip():
            for line in result.strip().split('\n')[:5]:
                print(f"      {line}")
    
    deployer.disconnect()
    
    return exists

def check_css_file_existence():
    """Check if CSS files exist on remote"""
    print("\n" + "=" * 60)
    print("CSS FILE EXISTENCE CHECK")
    print("=" * 60)
    
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    
    print("\n3️⃣ Checking CSS files on remote server...")
    
    css_files_to_check = [
        'css/styles/components/_navigation.css',
        'css/styles/layout/_header-footer.css',
        'css/styles/main.css',
        'css/styles/utilities/_responsive-enhancements.css',
        'css/custom.css',
        'style.css',
    ]
    
    for css_file in css_files_to_check:
        full_path = f'{remote_path}/{css_file}'
        result = deployer.execute_command(f'test -f {full_path} && echo "EXISTS" || echo "NOT_FOUND"')
        status = "✅ EXISTS" if "EXISTS" in result else "❌ NOT FOUND"
        print(f"   {status}: {css_file}")
    
    deployer.disconnect()

def check_css_conflicts():
    """Check for CSS conflicts and specificity issues"""
    print("\n" + "=" * 60)
    print("CSS CONFLICT ANALYSIS")
    print("=" * 60)
    
    print("\n4️⃣ Analyzing potential CSS conflicts...")
    
    base = Path('D:/websites/websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern')
    
    # Find all CSS files that might affect navigation
    css_files = list(base.rglob('*.css'))
    
    nav_rules = []
    header_rules = []
    
    for css_file in css_files:
        try:
            content = css_file.read_text(encoding='utf-8', errors='ignore')
            
            # Check for navigation-related rules
            if '.nav-list' in content or '.main-nav' in content or '.navigation' in content:
                lines = content.split('\n')
                for i, line in enumerate(lines, 1):
                    if any(selector in line for selector in ['.nav-list', '.main-nav', '.navigation']):
                        nav_rules.append({
                            'file': str(css_file.relative_to(base)),
                            'line': i,
                            'rule': line.strip()[:100]
                        })
            
            # Check for header-related rules
            if '.site-header' in content or 'header' in content.lower():
                lines = content.split('\n')
                for i, line in enumerate(lines, 1):
                    if '.site-header' in line:
                        header_rules.append({
                            'file': str(css_file.relative_to(base)),
                            'line': i,
                            'rule': line.strip()[:100]
                        })
        except:
            pass
    
    print(f"\n   Found {len(nav_rules)} navigation-related CSS rules:")
    for rule in nav_rules[:10]:
        print(f"      {rule['file']}:{rule['line']} - {rule['rule']}")
    
    print(f"\n   Found {len(header_rules)} header-related CSS rules:")
    for rule in header_rules[:10]:
        print(f"      {rule['file']}:{rule['line']} - {rule['rule']}")
    
    return nav_rules, header_rules

def check_load_order():
    """Check file loading order from functions.php and inc/helpers/load-files.php"""
    print("\n" + "=" * 60)
    print("FILE LOAD ORDER ANALYSIS")
    print("=" * 60)
    
    base = Path('D:/websites/websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern')
    
    print("\n5️⃣ Checking file load order...")
    
    # Check functions.php for require/require_once
    functions_file = base / 'functions.php'
    if functions_file.exists():
        content = functions_file.read_text(encoding='utf-8', errors='ignore')
        requires = re.findall(r'require.*?["\']([^"\']+)["\']', content)
        print(f"\n   Functions.php requires:")
        for req in requires[:10]:
            print(f"      - {req}")
    
    # Check inc/helpers/load-files.php
    load_files = base / 'inc/helpers/load-files.php'
    if load_files.exists():
        content = load_files.read_text(encoding='utf-8', errors='ignore')
        loads = re.findall(r'load_files\(["\']([^"\']+)["\']', content)
        print(f"\n   load-files.php loads:")
        for load in loads:
            print(f"      - {load}")

def main():
    print("=" * 60)
    print("MENU STYLING ROOT CAUSE ANALYSIS")
    print("=" * 60)
    
    # Run all analyses
    css_files, inline_styles = analyze_css_cascade()
    enqueue_exists = check_enqueue_configuration()
    check_css_file_existence()
    nav_rules, header_rules = check_css_conflicts()
    check_load_order()
    
    # Summary
    print("\n" + "=" * 60)
    print("ROOT CAUSE SUMMARY")
    print("=" * 60)
    
    print("\n🔍 Key Findings:")
    
    if not any('navigation' in f.lower() for f in css_files):
        print("   ❌ ISSUE 1: Navigation CSS not being loaded")
        print("      → Navigation styles are defined but not enqueued")
    
    nav_rules_count = len([r for r in nav_rules if 'style.css' not in r['file']])
    if nav_rules_count > 10:
        print(f"   ⚠️  ISSUE 2: Multiple CSS files with navigation rules ({nav_rules_count})")
        print("      → Potential CSS conflicts and specificity wars")
    
    if not enqueue_exists:
        print("   ⚠️  ISSUE 3: inc/assets/enqueue.php not found")
        print("      → Theme might be using inc/assets.php instead")
    
    print("\n💡 Recommended Solutions:")
    print("   1. Ensure navigation CSS is directly enqueued (not via @import)")
    print("   2. Consolidate navigation styles into fewer files")
    print("   3. Use CSS custom properties (variables) for consistency")
    print("   4. Ensure proper CSS loading order (dependencies)")
    print("   5. Use !important sparingly and only as last resort")
    
    print("\n" + "=" * 60)

if __name__ == '__main__':
    main()

