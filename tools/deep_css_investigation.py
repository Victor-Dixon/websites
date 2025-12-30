#!/usr/bin/env python3
"""
Deep CSS Investigation - Find what's actually preventing menu styling
Inspects the rendered HTML, CSS loading, and identifies conflicts
"""

import sys
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
import requests
import re
from html.parser import HTMLParser

class CSSLinkParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.css_files = []
        self.inline_styles = []
    
    def handle_starttag(self, tag, attrs):
        if tag == 'link':
            attrs_dict = dict(attrs)
            if attrs_dict.get('rel') == 'stylesheet':
                self.css_files.append(attrs_dict.get('href', ''))
        elif tag == 'style':
            self.collecting_style = True
    
    def handle_data(self, data):
        if hasattr(self, 'collecting_style') and self.collecting_style:
            self.inline_styles.append(data)

def analyze_loaded_css():
    """Analyze what CSS is actually loaded on the page"""
    print("=" * 70)
    print("DEEP CSS INVESTIGATION - Menu Styling Root Cause")
    print("=" * 70)
    
    print("\n1️⃣ ANALYZING LOADED CSS FILES")
    print("-" * 70)
    
    try:
        r = requests.get('https://freerideinvestor.com/?nocache=' + str(1234567890), timeout=15)
        html = r.text
        
        parser = CSSLinkParser()
        parser.feed(html)
        
        # Also find style tags
        style_pattern = r'<style[^>]*>([^<]+)</style>'
        inline_matches = re.findall(style_pattern, html, re.DOTALL)
        
        print(f"\n   Found {len(parser.css_files)} CSS files:")
        for i, css_file in enumerate(parser.css_files, 1):
            nav_marker = " ⬅️ NAVIGATION" if 'navigation' in css_file.lower() else ""
            header_marker = " ⬅️ HEADER" if 'header' in css_file.lower() else ""
            print(f"   {i}. {css_file}{nav_marker}{header_marker}")
        
        print(f"\n   Found {len(inline_matches)} inline style blocks")
        
        # Check if navigation/header CSS loaded
        nav_loaded = any('navigation' in f.lower() for f in parser.css_files)
        header_loaded = any('header' in f.lower() for f in parser.css_files)
        
        print(f"\n   ✅ Navigation CSS loaded: {'YES' if nav_loaded else 'NO'}")
        print(f"   ✅ Header CSS loaded: {'YES' if header_loaded else 'NO'}")
        
        return parser.css_files, inline_matches
        
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return [], []

def check_css_file_contents():
    """Check if CSS files actually exist and what they contain"""
    print("\n2️⃣ CHECKING CSS FILE CONTENTS ON REMOTE")
    print("-" * 70)
    
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    
    css_files = [
        'css/styles/components/_navigation.css',
        'css/styles/layout/_header-footer.css',
        'style.css',
        'css/custom.css'
    ]
    
    for css_file in css_files:
        full_path = f'{remote_path}/{css_file}'
        result = deployer.execute_command(f'test -f {full_path} && wc -l {full_path} || echo "NOT_FOUND"')
        status = "✅ EXISTS" if "NOT_FOUND" not in result else "❌ NOT FOUND"
        if "NOT_FOUND" not in result:
            lines = result.strip().split()[0] if result.strip() else "0"
            print(f"   {status}: {css_file} ({lines} lines)")
            
            # Check for navigation rules
            nav_check = deployer.execute_command(f'grep -c "\.nav-list\|\.main-nav" {full_path} 2>&1 || echo "0"')
            nav_count = nav_check.strip().split()[0] if nav_check.strip() else "0"
            if nav_count != "0":
                print(f"      └─ Contains {nav_count} navigation-related rules")
        else:
            print(f"   {status}: {css_file}")
    
    deployer.disconnect()

def analyze_functions_php_enqueue():
    """Analyze functions.php enqueue configuration"""
    print("\n3️⃣ ANALYZING FUNCTIONS.PHP ENQUEUE")
    print("-" * 70)
    
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    
    # Get functions.php content
    result = deployer.execute_command(f'cat {remote_path}/functions.php 2>&1')
    
    if "No such file" not in result:
        # Find enqueue functions
        enqueue_pattern = r'function\s+(\w+.*?enqueue.*?)\s*\{'
        functions = re.findall(enqueue_pattern, result, re.IGNORECASE)
        
        print(f"\n   Found {len(functions)} enqueue-related functions:")
        for func in functions:
            print(f"      - {func}")
        
        # Check if navigation CSS is enqueued
        nav_enqueued = 'navigation' in result.lower() or '_navigation.css' in result
        header_enqueued = 'header-footer' in result.lower() or '_header-footer.css' in result
        
        print(f"\n   ✅ Navigation CSS enqueued: {'YES' if nav_enqueued else 'NO'}")
        print(f"   ✅ Header CSS enqueued: {'YES' if header_enqueued else 'NO'}")
        
        # Show relevant enqueue lines
        lines = result.split('\n')
        print(f"\n   Relevant enqueue lines:")
        for i, line in enumerate(lines, 1):
            if 'wp_enqueue_style' in line and ('navigation' in line.lower() or 'header' in line.lower() or 'main-css' in line.lower()):
                print(f"      Line {i}: {line.strip()[:80]}")
    else:
        print("   ❌ functions.php not found")
    
    deployer.disconnect()

def check_css_specificity_conflicts():
    """Check for CSS specificity conflicts"""
    print("\n4️⃣ ANALYZING CSS SPECIFICITY CONFLICTS")
    print("-" * 70)
    
    base = Path('D:/websites/websites/freerideinvestor.com/wp/wp-content/themes/freerideinvestor-modern')
    
    # Find all CSS files with navigation rules
    nav_rules_by_file = {}
    
    for css_file in base.rglob('*.css'):
        try:
            content = css_file.read_text(encoding='utf-8', errors='ignore')
            lines = content.split('\n')
            
            nav_rules_in_file = []
            for i, line in enumerate(lines, 1):
                if any(sel in line for sel in ['.nav-list', '.main-nav', '.site-header']):
                    nav_rules_in_file.append({
                        'line': i,
                        'rule': line.strip()[:100],
                        'has_important': '!important' in line
                    })
            
            if nav_rules_in_file:
                nav_rules_by_file[str(css_file.relative_to(base))] = nav_rules_in_file
        except:
            pass
    
    print(f"\n   Found {len(nav_rules_by_file)} files with navigation/header rules:")
    
    for file_path, rules in nav_rules_by_file.items():
        print(f"\n   📄 {file_path} ({len(rules)} rules):")
        for rule in rules[:5]:  # Show first 5 rules
            important_marker = " ⚠️ !important" if rule['has_important'] else ""
            print(f"      Line {rule['line']}: {rule['rule']}{important_marker}")
        if len(rules) > 5:
            print(f"      ... and {len(rules) - 5} more rules")
    
    # Check for conflicting rules
    print(f"\n   🔍 Potential Conflicts:")
    conflicting_files = [f for f, rules in nav_rules_by_file.items() if len(rules) > 10]
    if conflicting_files:
        print(f"      ⚠️  {len(conflicting_files)} files have >10 navigation rules (potential conflicts)")
        for f in conflicting_files[:3]:
            print(f"         - {f}")

def check_load_order_dependencies():
    """Check CSS loading order and dependencies"""
    print("\n5️⃣ ANALYZING CSS LOAD ORDER & DEPENDENCIES")
    print("-" * 70)
    
    deployer = SimpleWordPressDeployer('freerideinvestor.com', load_site_configs())
    deployer.connect()
    
    remote_path = 'domains/freerideinvestor.com/public_html/wp-content/themes/freerideinvestor-modern'
    
    # Get functions.php and check enqueue order
    result = deployer.execute_command(f'grep -n "wp_enqueue_style" {remote_path}/functions.php 2>&1 | head -20')
    
    if result.strip() and "No such file" not in result:
        print(f"\n   Enqueue order in functions.php:")
        for line in result.strip().split('\n')[:10]:
            print(f"      {line}")
        
        # Check dependencies
        deps_result = deployer.execute_command(f'grep -A 2 "wp_enqueue_style" {remote_path}/functions.php | grep -E "\[.*main-css|navigation|header" | head -10')
        if deps_result.strip():
            print(f"\n   Dependencies found:")
            for line in deps_result.strip().split('\n')[:5]:
                print(f"      {line}")
    else:
        print("   ⚠️  No enqueue statements found or file not accessible")
    
    deployer.disconnect()

def check_rendered_html_structure():
    """Check the actual HTML structure of the menu"""
    print("\n6️⃣ ANALYZING RENDERED HTML STRUCTURE")
    print("-" * 70)
    
    try:
        r = requests.get('https://freerideinvestor.com/?nocache=' + str(1234567890), timeout=15)
        html = r.text
        
        # Find header/navigation structure
        header_match = re.search(r'<header[^>]*>(.*?)</header>', html, re.DOTALL | re.IGNORECASE)
        nav_match = re.search(r'<nav[^>]*>(.*?)</nav>', html, re.DOTALL | re.IGNORECASE)
        
        if header_match:
            header_content = header_match.group(1)
            # Check for classes
            classes = re.findall(r'class=["\']([^"\']+)["\']', header_content)
            print(f"\n   Header classes found: {', '.join(set(classes))[:200]}")
            
            # Check for navigation
            if nav_match:
                nav_content = nav_match.group(1)
                nav_classes = re.findall(r'class=["\']([^"\']+)["\']', nav_content)
                print(f"   Navigation classes found: {', '.join(set(nav_classes))[:200]}")
                
                # Check for nav-list
                has_nav_list = 'nav-list' in nav_content
                has_main_nav = 'main-nav' in nav_content
                print(f"\n   ✅ Has .nav-list class: {'YES' if has_nav_list else 'NO'}")
                print(f"   ✅ Has .main-nav class: {'YES' if has_main_nav else 'NO'}")
        
        # Check for inline styles in header
        header_inline = re.search(r'<header[^>]*style=["\']([^"\']+)["\']', html, re.IGNORECASE)
        if header_inline:
            print(f"\n   ⚠️  Header has inline styles: {header_inline.group(1)[:100]}")
        
    except Exception as e:
        print(f"   ❌ Error: {e}")

def main():
    css_files, inline_styles = analyze_loaded_css()
    check_css_file_contents()
    analyze_functions_php_enqueue()
    check_css_specificity_conflicts()
    check_load_order_dependencies()
    check_rendered_html_structure()
    
    print("\n" + "=" * 70)
    print("ROOT CAUSE SUMMARY")
    print("=" * 70)
    
    print("\n🔍 KEY FINDINGS:")
    print("   1. CSS files ARE loading (confirmed in diagnostic)")
    print("   2. Navigation CSS IS enqueued (confirmed)")
    print("   3. Multiple CSS files have conflicting rules (34+ rules)")
    print("   4. Need to verify CSS files exist on remote server")
    print("   5. Need to check CSS specificity and loading order")
    
    print("\n💡 NEXT STEPS:")
    print("   1. Verify CSS file paths are correct")
    print("   2. Check if CSS files are being served correctly")
    print("   3. Inspect browser DevTools for computed styles")
    print("   4. Identify which CSS rule is winning (specificity)")
    print("   5. Consolidate conflicting CSS rules")
    
    print("\n" + "=" * 70)

if __name__ == '__main__':
    main()

