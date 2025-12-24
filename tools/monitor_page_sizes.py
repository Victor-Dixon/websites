#!/usr/bin/env python3
"""
Monitor and Optimize Page Sizes
=================================

Monitors page sizes across all WordPress sites and identifies optimization opportunities.

Author: Agent-7
Date: 2025-12-22
"""

import sys
import requests
from pathlib import Path
from typing import Dict, List

ALL_SITES = [
    "ariajet.site",
    "crosbyultimateevents.com",
    "dadudekc.com",
    "digitaldreamscape.site",
    "freerideinvestor.com",
    "houstonsipqueen.com",
    "prismblossom.online",
    "southwestsecret.com",
    "tradingrobotplug.com",
    "weareswarm.online",
    "weareswarm.site",
]


def check_page_size(url: str) -> Dict:
    """Check page size and provide optimization recommendations."""
    try:
        response = requests.get(url, timeout=10, headers={
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
        
        if response.status_code != 200:
            return {
                'url': url,
                'status': 'error',
                'status_code': response.status_code,
                'size_kb': 0,
                'recommendations': ['Fix HTTP error']
            }
        
        size_bytes = len(response.content)
        size_kb = round(size_bytes / 1024, 2)
        
        recommendations = []
        
        # Check for optimization opportunities
        if size_kb > 500:
            recommendations.append('Page size exceeds 500KB - consider image optimization')
        if size_kb > 1000:
            recommendations.append('Page size exceeds 1MB - critical optimization needed')
        
        # Check for common optimization opportunities
        content = response.text
        if content.count('<img') > 10:
            recommendations.append('Many images detected - consider lazy loading')
        if 'style=' in content and content.count('style=') > 20:
            recommendations.append('Inline styles detected - move to external CSS')
        if content.count('<script') > 10:
            recommendations.append('Many scripts detected - consider deferring/async')
        
        return {
            'url': url,
            'status': 'ok',
            'status_code': response.status_code,
            'size_kb': size_kb,
            'size_bytes': size_bytes,
            'recommendations': recommendations
        }
        
    except Exception as e:
        return {
            'url': url,
            'status': 'error',
            'error': str(e),
            'size_kb': 0,
            'recommendations': ['Connection error']
        }


def main():
    """Main execution."""
    print("=" * 70)
    print("📊 MONITORING PAGE SIZES")
    print("=" * 70)
    print()
    
    results = []
    
    for site in ALL_SITES:
        url = f"https://{site}"
        print(f"🔍 Checking {site}...")
        result = check_page_size(url)
        results.append(result)
        
        if result['status'] == 'ok':
            print(f"   ✅ {result['size_kb']} KB ({result['status_code']})")
            if result['recommendations']:
                for rec in result['recommendations']:
                    print(f"      💡 {rec}")
        else:
            print(f"   ❌ Error: {result.get('error', result.get('status_code', 'Unknown'))}")
        print()
    
    # Summary
    print("=" * 70)
    print("📊 SUMMARY")
    print("=" * 70)
    print()
    
    successful = [r for r in results if r['status'] == 'ok']
    total_size = sum(r['size_kb'] for r in successful)
    avg_size = total_size / len(successful) if successful else 0
    max_size = max((r['size_kb'] for r in successful), default=0)
    min_size = min((r['size_kb'] for r in successful), default=0)
    
    print(f"Total sites checked: {len(ALL_SITES)}")
    print(f"Successful checks: {len(successful)}")
    print(f"Average page size: {avg_size:.2f} KB")
    print(f"Largest page: {max_size:.2f} KB")
    print(f"Smallest page: {min_size:.2f} KB")
    print()
    
    # Sites needing optimization
    needs_optimization = [r for r in successful if r['size_kb'] > 500 or r['recommendations']]
    
    if needs_optimization:
        print("⚠️  Sites needing optimization:")
        for r in needs_optimization:
            print(f"   - {r['url']}: {r['size_kb']} KB")
            for rec in r['recommendations']:
                print(f"     • {rec}")
    else:
        print("✅ All page sizes are within acceptable range (<500KB)")
    
    print()
    print("💡 Optimization recommendations:")
    print("   - Enable GZIP compression")
    print("   - Optimize images (WebP format, lazy loading)")
    print("   - Minify CSS/JS")
    print("   - Enable browser caching")
    print("   - Defer non-critical JavaScript")
    
    return 0


if __name__ == "__main__":
    sys.exit(main())

