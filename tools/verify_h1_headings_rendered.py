#!/usr/bin/env python3
"""
Verify H1 Headings in Rendered HTML
===================================

Checks the actual rendered HTML of pages to verify H1 count.

Author: Agent-7
Date: 2025-12-22
"""

import sys
import re
import requests
from pathlib import Path

SITES_TO_CHECK = {
    "crosbyultimateevents.com": "https://crosbyultimateevents.com",
    "houstonsipqueen.com": "https://houstonsipqueen.com",
    "prismblossom.online": "https://prismblossom.online",
    "tradingrobotplug.com": "https://tradingrobotplug.com",
}


def check_rendered_h1(site_name: str, url: str):
    """Check H1 count in rendered HTML."""
    print(f"\n{'='*70}")
    print(f"🔍 CHECKING RENDERED H1: {site_name}")
    print(f"{'='*70}")
    print(f"   URL: {url}")
    
    try:
        response = requests.get(url, timeout=10, headers={
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        
        if response.status_code != 200:
            print(f"   ❌ HTTP {response.status_code}")
            return False
        
        html = response.text
        
        # Find all H1 headings
        h1_pattern = r'<h1[^>]*>.*?</h1>'
        h1_matches = re.findall(h1_pattern, html, re.IGNORECASE | re.DOTALL)
        
        print(f"   📊 Found {len(h1_matches)} H1 heading(s)")
        
        if len(h1_matches) > 1:
            print(f"   ⚠️  MULTIPLE H1s DETECTED:")
            for i, h1 in enumerate(h1_matches[:5], 1):
                # Extract text content
                text_match = re.search(r'<h1[^>]*>(.*?)</h1>', h1, re.IGNORECASE | re.DOTALL)
                text = text_match.group(1).strip() if text_match else h1[:50]
                text = re.sub(r'<[^>]+>', '', text)  # Remove HTML tags
                text = text[:60]  # Truncate
                print(f"      H1 #{i}: {text}")
            return False
        elif len(h1_matches) == 1:
            text_match = re.search(r'<h1[^>]*>(.*?)</h1>', h1_matches[0], re.IGNORECASE | re.DOTALL)
            text = text_match.group(1).strip() if text_match else ""
            text = re.sub(r'<[^>]+>', '', text)  # Remove HTML tags
            print(f"   ✅ Single H1 found: {text[:60]}")
            return True
        else:
            print(f"   ⚠️  No H1 headings found")
            return True  # Not an error, just no H1
        
    except Exception as e:
        print(f"   ❌ Error: {e}")
        return False


def main():
    """Main execution."""
    print("=" * 70)
    print("🔍 VERIFYING H1 HEADINGS IN RENDERED HTML")
    print("=" * 70)
    print()
    
    results = {}
    
    for site_name, url in SITES_TO_CHECK.items():
        success = check_rendered_h1(site_name, url)
        results[site_name] = "✅ OK" if success else "⚠️  MULTIPLE H1s"
    
    # Summary
    print("\n" + "=" * 70)
    print("📊 SUMMARY")
    print("=" * 70)
    print()
    
    for site_name, result in results.items():
        print(f"  {site_name}: {result}")
    
    issues = sum(1 for r in results.values() if "MULTIPLE" in r)
    
    if issues == 0:
        print("\n✅ All sites have correct H1 structure (1 or 0 H1s per page)")
        return 0
    else:
        print(f"\n⚠️  {issues} site(s) still have multiple H1 headings")
        return 1


if __name__ == "__main__":
    sys.exit(main())

