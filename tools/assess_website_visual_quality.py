#!/usr/bin/env python3
"""
Assess Website Visual Quality
==============================

Comprehensive assessment of website visual quality and professionalism.
Checks for common visual issues that could impact user experience.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import requests
from pathlib import Path
from datetime import datetime


SITES = [
    "prismblossom.online",
    "southwestsecret.com",
    "crosbyultimateevents.com",
    "digitaldreamscape.site",
    "ariajet.site",
    "houstonsipqueen.com",
    "freerideinvestor.com",
    "tradingrobotplug.com",
    "weareswarm.online",
    "weareswarm.site",
]


def check_site_quality(site_url: str):
    """Check visual quality indicators for a site."""
    print(f"\n{'='*70}")
    print(f"🔍 ASSESSING: {site_url}")
    print(f"{'='*70}")
    
    results = {
        'url': site_url,
        'accessible': False,
        'status_code': None,
        'load_time': None,
        'content_length': None,
        'has_issues': [],
        'has_critical_issues': False
    }
    
    try:
        start_time = datetime.now()
        response = requests.get(f"https://{site_url}", timeout=10, allow_redirects=True)
        end_time = datetime.now()
        
        results['accessible'] = True
        results['status_code'] = response.status_code
        results['load_time'] = (end_time - start_time).total_seconds()
        results['content_length'] = len(response.content)
        
        content = response.text.lower()
        
        # Check for common issues
        if response.status_code != 200:
            results['has_issues'].append(f"Non-200 status code: {response.status_code}")
            results['has_critical_issues'] = True
        
        if 'error' in content and ('fatal' in content or 'warning' in content):
            results['has_issues'].append("PHP errors/warnings detected")
            results['has_critical_issues'] = True
        
        if 'hello world' in content:
            results['has_issues'].append("Default 'Hello World' content detected")
            results['has_critical_issues'] = True
        
        if results['load_time'] > 3.0:
            results['has_issues'].append(f"Slow load time: {results['load_time']:.2f}s")
        
        if results['content_length'] < 5000:
            results['has_issues'].append(f"Very small page size: {results['content_length']} bytes")
        
        print(f"Status: {results['status_code']}")
        print(f"Load Time: {results['load_time']:.2f}s")
        print(f"Content Size: {results['content_length']:,} bytes")
        
        if results['has_issues']:
            print(f"⚠️  Issues Found:")
            for issue in results['has_issues']:
                print(f"   - {issue}")
        else:
            print("✅ No obvious issues detected")
        
        return results
        
    except requests.exceptions.Timeout:
        results['has_issues'].append("Request timeout")
        results['has_critical_issues'] = True
        print("❌ Request timeout")
        return results
    except Exception as e:
        results['has_issues'].append(f"Error: {str(e)}")
        results['has_critical_issues'] = True
        print(f"❌ Error: {e}")
        return results


def main():
    """Main execution."""
    print("=" * 70)
    print("🎨 WEBSITE VISUAL QUALITY ASSESSMENT")
    print("=" * 70)
    print()
    print("Assessing visual quality and professionalism indicators...")
    print()
    
    all_results = []
    
    for site in SITES:
        results = check_site_quality(site)
        all_results.append(results)
    
    # Summary
    print("\n" + "=" * 70)
    print("📊 SUMMARY")
    print("=" * 70)
    print()
    
    accessible_count = sum(1 for r in all_results if r['accessible'])
    critical_issues_count = sum(1 for r in all_results if r['has_critical_issues'])
    avg_load_time = sum(r['load_time'] for r in all_results if r['load_time']) / max(accessible_count, 1)
    
    print(f"✅ Accessible: {accessible_count}/{len(SITES)}")
    print(f"⚠️  Critical Issues: {critical_issues_count}/{len(SITES)}")
    print(f"⚡ Average Load Time: {avg_load_time:.2f}s")
    print()
    
    sites_with_issues = [r for r in all_results if r['has_critical_issues']]
    if sites_with_issues:
        print("Sites with Critical Issues:")
        for r in sites_with_issues:
            print(f"  - {r['url']}: {', '.join(r['has_issues'])}")
        print()
    
    print("💡 Recommendation:")
    if critical_issues_count == 0:
        print("   All sites are accessible and functioning properly.")
        print("   Visual quality should be verified manually via browser inspection.")
    else:
        print(f"   {critical_issues_count} site(s) need immediate attention.")
    
    return 0 if critical_issues_count == 0 else 1


if __name__ == "__main__":
    sys.exit(main())

