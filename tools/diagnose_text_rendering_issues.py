#!/usr/bin/env python3
"""
Diagnose Text Rendering Issues
===============================

Investigates text spacing/rendering problems across WordPress sites.
Checks CSS for word-break, letter-spacing, font loading, and text-spacing issues.

Author: Agent-1
Date: 2025-12-22
"""

import sys
import re
from pathlib import Path
from typing import Dict, List

sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def check_css_for_text_issues(deployer, site_key, theme_name):
    """Check CSS files for text rendering issues."""
    remote_path = "domains/{}/public_html".format(site_key)
    css_path = f"{remote_path}/wp-content/themes/{theme_name}/style.css"
    
    print(f"\n📄 Checking style.css for {site_key}...")
    
    # Check if file exists
    exists = deployer.execute_command(f"test -f {css_path} && echo 'EXISTS' || echo 'MISSING'")
    if "MISSING" in exists:
        print(f"   ⚠️  style.css not found")
        return {}
    
    content = deployer.execute_command(f"cat {css_path}")
    
    issues = {
        "word_break": [],
        "word_wrap": [],
        "letter_spacing": [],
        "text_spacing": [],
        "font_family": [],
        "text_transform": [],
    }
    
    lines = content.split('\n')
    for i, line in enumerate(lines, 1):
        # Check for word-break
        if 'word-break' in line.lower():
            issues["word_break"].append(f"Line {i}: {line.strip()}")
        
        # Check for word-wrap/overflow-wrap
        if 'word-wrap' in line.lower() or 'overflow-wrap' in line.lower():
            issues["word_wrap"].append(f"Line {i}: {line.strip()}")
        
        # Check for letter-spacing (can cause spacing issues if negative)
        if 'letter-spacing' in line.lower():
            issues["letter_spacing"].append(f"Line {i}: {line.strip()}")
        
        # Check for text-spacing (CSS Text Level 3)
        if 'text-spacing' in line.lower():
            issues["text_spacing"].append(f"Line {i}: {line.strip()}")
        
        # Check for font-family declarations (font loading issues)
        if 'font-family' in line.lower():
            issues["font_family"].append(f"Line {i}: {line.strip()[:100]}")
        
        # Check for text-transform (unlikely but possible)
        if 'text-transform' in line.lower() and 'uppercase' in line.lower():
            issues["text_transform"].append(f"Line {i}: {line.strip()}")
    
    return issues


def check_functions_php_for_fonts(deployer, site_key, theme_name):
    """Check functions.php for font enqueuing."""
    remote_path = "domains/{}/public_html".format(site_key)
    functions_path = f"{remote_path}/wp-content/themes/{theme_name}/functions.php"
    
    print(f"\n📄 Checking functions.php for font loading...")
    
    exists = deployer.execute_command(f"test -f {functions_path} && echo 'EXISTS' || echo 'MISSING'")
    if "MISSING" in exists:
        print(f"   ⚠️  functions.php not found")
        return {}
    
    content = deployer.execute_command(f"cat {functions_path}")
    
    font_loading = {
        "wp_enqueue_style": [],
        "google_fonts": [],
        "font_face": [],
        "font_display": [],
    }
    
    lines = content.split('\n')
    for i, line in enumerate(lines, 1):
        if 'wp_enqueue_style' in line and ('font' in line.lower() or 'google' in line.lower()):
            font_loading["wp_enqueue_style"].append(f"Line {i}: {line.strip()[:100]}")
        
        if 'fonts.googleapis.com' in line or 'fonts.gstatic.com' in line:
            font_loading["google_fonts"].append(f"Line {i}: {line.strip()[:100]}")
        
        if '@font-face' in line:
            font_loading["font_face"].append(f"Line {i}: {line.strip()[:100]}")
        
        if 'font-display' in line:
            font_loading["font_display"].append(f"Line {i}: {line.strip()[:100]}")
    
    return font_loading


def check_header_for_fonts(deployer, site_key, theme_name):
    """Check header.php for inline font loading."""
    remote_path = "domains/{}/public_html".format(site_key)
    header_path = f"{remote_path}/wp-content/themes/{theme_name}/header.php"
    
    print(f"\n📄 Checking header.php for inline font loading...")
    
    exists = deployer.execute_command(f"test -f {header_path} && echo 'EXISTS' || echo 'MISSING'")
    if "MISSING" in exists:
        print(f"   ⚠️  header.php not found")
        return []
    
    content = deployer.execute_command(f"cat {header_path}")
    
    font_refs = []
    lines = content.split('\n')
    for i, line in enumerate(lines, 1):
        if 'fonts.googleapis.com' in line or 'fonts.gstatic.com' in line or '@font-face' in line:
            font_refs.append(f"Line {i}: {line.strip()[:150]}")
    
    return font_refs


def diagnose_site(deployer, site_key, theme_name):
    """Diagnose text rendering issues for a site."""
    print("\n" + "=" * 70)
    print(f"DIAGNOSING TEXT RENDERING: {site_key}")
    print("=" * 70)
    
    css_issues = check_css_for_text_issues(deployer, site_key, theme_name)
    font_loading = check_functions_php_for_fonts(deployer, site_key, theme_name)
    header_fonts = check_header_for_fonts(deployer, site_key, theme_name)
    
    print("\n📊 CSS Issues Found:")
    if any(css_issues.values()):
        for category, items in css_issues.items():
            if items:
                print(f"\n   {category.upper()}:")
                for item in items[:5]:  # Show first 5
                    print(f"      {item}")
    else:
        print("   ✅ No obvious CSS text-spacing issues found")
    
    print("\n📊 Font Loading:")
    if any(font_loading.values()) or header_fonts:
        if font_loading["google_fonts"]:
            print("   Google Fonts detected:")
            for item in font_loading["google_fonts"][:3]:
                print(f"      {item}")
        if font_loading["font_face"]:
            print("   @font-face declarations:")
            for item in font_loading["font_face"][:3]:
                print(f"      {item}")
        if header_fonts:
            print("   Inline font loading in header:")
            for item in header_fonts[:3]:
                print(f"      {item}")
    else:
        print("   ⚠️  No font loading detected")
    
    return {
        "css_issues": css_issues,
        "font_loading": font_loading,
        "header_fonts": header_fonts,
    }


def main():
    """Main execution."""
    sites_to_check = [
        ("freerideinvestor.com", "freerideinvestor-modern"),
        ("crosbyultimateevents.com", None),  # Will detect theme
        ("houstonsipqueen.com", None),
        ("tradingrobotplug.com", None),
        ("ariajet.site", None),
    ]
    
    site_configs = load_site_configs()
    
    all_results = {}
    
    for site_key, theme_name in sites_to_check:
        deployer = SimpleWordPressDeployer(site_key, site_configs)
        
        if not deployer.connect():
            print(f"❌ Failed to connect to {site_key}")
            continue
        
        try:
            # Detect theme if not provided
            if not theme_name:
                remote_path = "domains/{}/public_html".format(site_key)
                active_theme = deployer.execute_command(
                    f"cd {remote_path} && wp theme list --status=active --field=name --allow-root 2>/dev/null || "
                    f"grep -r 'stylesheet' wp-content/themes/*/style.css | head -1 | cut -d'/' -f4"
                ).strip()
                theme_name = active_theme if active_theme else "default"
                print(f"📌 Detected theme: {theme_name}")
            
            result = diagnose_site(deployer, site_key, theme_name)
            all_results[site_key] = result
            
        except Exception as e:
            print(f"❌ Error diagnosing {site_key}: {e}")
        finally:
            deployer.disconnect()
    
    # Summary
    print("\n" + "=" * 70)
    print("SUMMARY")
    print("=" * 70)
    
    for site_key, result in all_results.items():
        print(f"\n{site_key}:")
        css_has_issues = any(result["css_issues"].values())
        has_fonts = any(result["font_loading"].values()) or result["header_fonts"]
        
        if css_has_issues:
            print("   ⚠️  CSS text-spacing properties found")
        if has_fonts:
            print("   ⚠️  Font loading detected (potential FOUT/FOIT)")
        if not css_has_issues and not has_fonts:
            print("   ✅ No obvious issues detected")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

