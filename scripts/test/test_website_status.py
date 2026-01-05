#!/usr/bin/env python3
"""
Test website HTTP status
"""

import requests
import sys

def test_website(url):
    """Test website HTTP status."""
    try:
        print(f"🌐 Testing {url}...")
        response = requests.get(url, timeout=10)

        print(f"   Status Code: {response.status_code}")
        print(f"   Response Size: {len(response.content)} bytes")

        if response.status_code == 200:
            print(f"   ✅ SUCCESS: Website is accessible")
            return True
        elif response.status_code == 500:
            print(f"   ❌ FAILED: HTTP 500 Internal Server Error")
            return False
        else:
            print(f"   ⚠️  WARNING: Unexpected status code {response.status_code}")
            return False

    except requests.exceptions.RequestException as e:
        print(f"   ❌ ERROR: {e}")
        return False

def main():
    """Test both websites."""
    print("🌐 WEBSITE STATUS TEST")
    print("=" * 40)

    sites = [
        'https://freerideinvestor.com',
        'https://prismblossom.online'
    ]

    results = {}
    for site in sites:
        results[site] = test_website(site)
        print()

    print("📊 SUMMARY")
    print("-" * 20)
    for site, success in results.items():
        status = "✅ WORKING" if success else "❌ BROKEN"
        print(f"{site}: {status}")

    all_working = all(results.values())
    if all_working:
        print("\n🎉 ALL WEBSITES ARE WORKING!")
    else:
        print("\n⚠️  SOME WEBSITES STILL HAVE ISSUES")

if __name__ == "__main__":
    main()