#!/usr/bin/env python3
"""
Diagnose Crosby Ultimate Events Text Rendering Issue
=====================================================

Diagnoses the text rendering issue where spaces are missing between words.
Checks HTML source, CSS, and database content.

Author: Agent-2 (Architecture & Design Specialist)
Date: 2025-12-22
"""

import requests
import re
from pathlib import Path
import sys

# Add deployment tools to path
sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))

try:
    from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs
    DEPLOYER_AVAILABLE = True
except ImportError:
    DEPLOYER_AVAILABLE = False

def check_html_source():
    """Check if HTML source has broken text or if it's a rendering issue."""
    print("🔍 Checking HTML source from live site...")
    
    try:
        response = requests.get("https://crosbyultimateevents.com", timeout=10)
        html = response.text
        
        # Check for broken text patterns
        broken_patterns = [
            r'cro\s+byultimateevent',
            r'Con\s+ultation',
            r'ervice\s+',
            r'comprehen\s+ive',
            r'occa\s+ion',
        ]
        
        issues_found = []
        for pattern in broken_patterns:
            matches = re.findall(pattern, html, re.IGNORECASE)
            if matches:
                issues_found.append(f"Found broken text pattern: {pattern} - {len(matches)} occurrences")
        
        # Check for correct text
        correct_patterns = [
            r'crosbyultimateevents\.com',
            r'Book Consultation',
            r'services',
            r'comprehensive',
            r'occasion',
        ]
        
        correct_found = []
        for pattern in correct_patterns:
            matches = re.findall(pattern, html, re.IGNORECASE)
            if matches:
                correct_found.append(f"Found correct text: {pattern} - {len(matches)} occurrences")
        
        print(f"\n📊 Results:")
        print(f"   Broken text patterns found: {len(issues_found)}")
        print(f"   Correct text patterns found: {len(correct_found)}")
        
        if issues_found:
            print(f"\n❌ ISSUE IN HTML SOURCE:")
            for issue in issues_found:
                print(f"   - {issue}")
            return "HTML_SOURCE"
        elif correct_found:
            print(f"\n✅ HTML source has correct text - issue is CSS rendering")
            return "CSS_RENDERING"
        else:
            print(f"\n⚠️  Could not determine - need manual inspection")
            return "UNKNOWN"
            
    except Exception as e:
        print(f"❌ Error checking HTML: {e}")
        return "ERROR"

def check_css_for_issues():
    """Check CSS file for text rendering issues."""
    print("\n🔍 Checking CSS file...")
    
    css_path = Path(__file__).parent.parent / "sites" / "crosbyultimateevents.com" / "wp" / "theme" / "crosbyultimateevents" / "style.css"
    
    if not css_path.exists():
        print(f"❌ CSS file not found: {css_path}")
        return
    
    with open(css_path, 'r', encoding='utf-8') as f:
        css = f.read()
    
    # Check for problematic CSS
    problematic_patterns = [
        (r'word-spacing:\s*-', 'Negative word-spacing'),
        (r'letter-spacing:\s*-[0-9]', 'Negative letter-spacing (except buttons)'),
    ]
    
    issues = []
    for pattern, description in problematic_patterns:
        matches = re.findall(pattern, css)
        if matches:
            issues.append(f"{description}: {len(matches)} occurrences")
    
    if issues:
        print(f"⚠️  Potential CSS issues found:")
        for issue in issues:
            print(f"   - {issue}")
    else:
        print(f"✅ No obvious CSS issues found")
    
    # Check if our fixes are present
    if 'word-spacing: normal !important' in css:
        print(f"✅ Text rendering fixes found in CSS")
    else:
        print(f"⚠️  Text rendering fixes not found in CSS")

def check_database_content():
    """Check WordPress database for broken content."""
    if not DEPLOYER_AVAILABLE:
        print("\n⚠️  Deployer not available - skipping database check")
        return
    
    print("\n🔍 Checking database content...")
    
    site_configs = load_site_configs()
    if 'crosbyultimateevents.com' not in site_configs:
        print("❌ Site config not found")
        return
    
    deployer = SimpleWordPressDeployer('crosbyultimateevents.com', site_configs)
    if not deployer.connect():
        print("❌ Could not connect to server")
        return
    
    try:
        # Check site title
        site_title = deployer.execute_command("wp option get blogname --path=/home/u996867598/domains/crosbyultimateevents.com/public_html")
        print(f"   Site title: {site_title.strip()}")
        
        # Check if title has broken text
        if ' ' in site_title and not any(char in site_title for char in ['  ', 'cro by', 'Con ult']):
            print("   ✅ Site title looks correct")
        else:
            print(f"   ⚠️  Site title may have issues: {site_title}")
        
    finally:
        deployer.disconnect()

def main():
    """Main diagnostic function."""
    print("=" * 60)
    print("Crosby Ultimate Events Text Rendering Diagnostic")
    print("=" * 60)
    
    # Check HTML source
    source_issue = check_html_source()
    
    # Check CSS
    check_css_for_issues()
    
    # Check database
    check_database_content()
    
    print("\n" + "=" * 60)
    print("Diagnostic Summary")
    print("=" * 60)
    
    if source_issue == "HTML_SOURCE":
        print("❌ ISSUE: Broken text is in HTML source (database/content issue)")
        print("   Fix: Update WordPress content in database")
    elif source_issue == "CSS_RENDERING":
        print("✅ HTML source is correct - issue is CSS rendering")
        print("   Fix: CSS fixes have been applied, deploy and test")
    else:
        print("⚠️  Could not determine root cause - manual inspection needed")
    
    print("\n📋 Next Steps:")
    print("   1. Deploy CSS fixes to live site")
    print("   2. Clear WordPress cache")
    print("   3. Clear browser cache")
    print("   4. Test site in browser")
    print("   5. If issue persists, check WordPress database content")

if __name__ == "__main__":
    main()

