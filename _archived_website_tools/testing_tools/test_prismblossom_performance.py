#!/usr/bin/env python3
"""
Test prismblossom.online Performance
====================================

Tests site performance after optimizations.
Target: Verify load time <3s.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import time
import requests
from pathlib import Path
from datetime import datetime


def test_site_performance():
    """Test site performance."""
    site_url = "https://prismblossom.online"
    target_time = 3.0  # seconds
    
    print("=" * 70)
    print(f"⚡ TESTING PERFORMANCE: {site_url}")
    print("=" * 70)
    print()
    print(f"Target load time: <{target_time}s")
    print()
    
    results = []
    
    # Run 3 tests
    for i in range(3):
        print(f"Test {i+1}/3...", end=" ", flush=True)
        
        try:
            start_time = time.time()
            response = requests.get(site_url, timeout=30, allow_redirects=True)
            end_time = time.time()
            
            load_time = end_time - start_time
            results.append(load_time)
            
            status = "✅" if load_time < target_time else "⚠️"
            print(f"{status} {load_time:.2f}s (Status: {response.status_code})")
            
        except Exception as e:
            print(f"❌ Error: {e}")
            results.append(None)
    
    print()
    print("=" * 70)
    print("📊 PERFORMANCE RESULTS")
    print("=" * 70)
    
    valid_results = [r for r in results if r is not None]
    
    if valid_results:
        avg_time = sum(valid_results) / len(valid_results)
        min_time = min(valid_results)
        max_time = max(valid_results)
        
        print(f"Average load time: {avg_time:.2f}s")
        print(f"Minimum load time: {min_time:.2f}s")
        print(f"Maximum load time: {max_time:.2f}s")
        print()
        
        if avg_time < target_time:
            print(f"✅ SUCCESS: Average load time ({avg_time:.2f}s) is below target ({target_time}s)")
            print()
            print("🎉 Performance optimization successful!")
            return True
        else:
            improvement = 16.61 - avg_time
            improvement_pct = (improvement / 16.61) * 100
            print(f"⚠️  Average load time ({avg_time:.2f}s) is above target ({target_time}s)")
            print(f"   Improvement: {improvement:.2f}s ({improvement_pct:.1f}% faster than original 16.61s)")
            print()
            print("💡 Additional optimizations may be needed:")
            print("   - Check database query performance")
            print("   - Optimize images (compress, use WebP)")
            print("   - Review and disable unused plugins")
            print("   - Consider upgrading hosting plan")
            return False
    else:
        print("❌ All tests failed")
        return False


def main():
    """Main execution."""
    success = test_site_performance()
    return 0 if success else 1


if __name__ == "__main__":
    sys.exit(main())


