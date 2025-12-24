#!/usr/bin/env python3
"""
Test southwestsecret.com Site
==============================

Tests the site and clears cache if needed.

Author: Agent-7
Date: 2025-12-22
"""

import sys
import requests
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def test_and_fix():
    """Test site and clear cache."""
    print("=" * 70)
    print("🌐 TESTING: southwestsecret.com")
    print("=" * 70)
    print()
    
    # Test site
    print("📡 Testing site...")
    try:
        response = requests.get("https://southwestsecret.com", timeout=10, headers={
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Cache-Control': 'no-cache'
        })
        
        print(f"   Status Code: {response.status_code}")
        print(f"   Response Size: {len(response.content)} bytes")
        
        if response.status_code == 200:
            print("   ✅ Site is accessible!")
            return True
        elif response.status_code == 500:
            print("   ⚠️  Still returning HTTP 500")
            print("   Attempting to clear cache...")
            
            # Clear cache via WP-CLI
            site_configs = load_site_configs()
            deployer = SimpleWordPressDeployer("southwestsecret.com", site_configs)
            
            if deployer.connect():
                remote_path = getattr(deployer, 'remote_path', '') or "domains/southwestsecret.com/public_html"
                
                # Clear LiteSpeed cache
                print("   🧹 Clearing LiteSpeed cache...")
                cache_cmd = f"cd {remote_path} && wp litespeed-purge all 2>&1"
                cache_result = deployer.execute_command(cache_cmd)
                print(f"   Result: {cache_result[:200]}")
                
                # Clear WordPress cache
                print("   🧹 Clearing WordPress cache...")
                wp_cache_cmd = f"cd {remote_path} && wp cache flush 2>&1"
                wp_cache_result = deployer.execute_command(wp_cache_cmd)
                print(f"   Result: {wp_cache_result[:200]}")
                
                deployer.disconnect()
                
                # Wait a moment
                print("   ⏳ Waiting 3 seconds...")
                time.sleep(3)
                
                # Test again
                print("   📡 Testing again...")
                response2 = requests.get("https://southwestsecret.com", timeout=10, headers={
                    'User-Agent': 'Mozilla/5.0',
                    'Cache-Control': 'no-cache'
                })
                
                print(f"   Status Code: {response2.status_code}")
                
                if response2.status_code == 200:
                    print("   ✅ Site is now accessible after cache clear!")
                    return True
                else:
                    print(f"   ⚠️  Still returning HTTP {response2.status_code}")
                    return False
        else:
            print(f"   ⚠️  Unexpected status code: {response.status_code}")
            return False
            
    except Exception as e:
        print(f"   ❌ Error testing site: {e}")
        return False


if __name__ == "__main__":
    sys.exit(0 if test_and_fix() else 1)

