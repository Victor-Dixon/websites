#!/usr/bin/env python3
"""
Activate Theme via WordPress REST API
=====================================

Activates a WordPress theme using REST API with Application Passwords.
Uses credentials from configs/site_configs.json.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import json
import base64
from pathlib import Path
from typing import Dict, Optional

try:
    import requests
    from requests.auth import HTTPBasicAuth
    REQUESTS_AVAILABLE = True
except ImportError:
    REQUESTS_AVAILABLE = False
    print("❌ 'requests' library not installed. Install with: pip install requests")


def load_site_configs():
    """Load site configurations from configs/site_configs.json"""
    config_path = Path("D:/websites/configs/site_configs.json")
    if not config_path.exists():
        config_path = Path(__file__).parent.parent.parent / "configs" / "site_configs.json"
    
    if config_path.exists():
        try:
            with open(config_path, 'r') as f:
                return json.load(f)
        except Exception as e:
            print(f"❌ Could not load site_configs.json: {e}")
            return {}
    return {}


def activate_theme_via_rest_api(site_domain: str, theme_name: str, site_configs: dict) -> bool:
    """Activate theme using WordPress REST API."""
    if not REQUESTS_AVAILABLE:
        print("❌ 'requests' library required")
        return False
    
    site_config = site_configs.get(site_domain, {})
    rest_api = site_config.get('rest_api', {})
    
    username = rest_api.get('username')
    app_password = rest_api.get('app_password')
    site_url = rest_api.get('site_url', site_config.get('site_url', f"https://{site_domain}"))
    
    if not username or not app_password:
        print(f"❌ Missing REST API credentials for {site_domain}")
        print("   Please add username and app_password to configs/site_configs.json")
        return False
    
    print(f"🎨 Activating theme '{theme_name}' via REST API...")
    print(f"   Site: {site_url}")
    
    try:
        # WordPress REST API for themes (WordPress 5.9+)
        api_url = f"{site_url.rstrip('/')}/wp-json/wp/v2/themes"
        
        auth = HTTPBasicAuth(username, app_password)
        
        # First, get list of themes
        print("   Fetching themes list...")
        response = requests.get(api_url, auth=auth, timeout=30)
        
        if response.status_code == 200:
            themes = response.json()
            print(f"   Found {len(themes)} installed themes")
            
            # Find our theme
            theme_found = None
            for theme in themes:
                stylesheet = theme.get('stylesheet', '')
                if stylesheet == theme_name:
                    theme_found = theme
                    break
            
            if theme_found:
                print(f"   ✅ Found theme: {theme_name}")
                
                # Check if already active
                if theme_found.get('status') == 'active':
                    print(f"   ✅ Theme '{theme_name}' is already active!")
                    return True
                
                # Activate theme
                # Note: WordPress REST API doesn't have a direct activate endpoint
                # We need to use a custom endpoint or WP-CLI via REST
                # Alternative: Use admin-ajax or custom plugin endpoint
                
                # Try using the theme activation endpoint (if available)
                activate_url = f"{api_url}/{theme_name}"
                activate_data = {"status": "active"}
                
                print(f"   Attempting activation...")
                activate_response = requests.post(
                    activate_url,
                    auth=auth,
                    json=activate_data,
                    timeout=30
                )
                
                if activate_response.status_code in [200, 201]:
                    print(f"✅ Theme '{theme_name}' activated!")
                    return True
                else:
                    print(f"⚠️  Activation response: {activate_response.status_code}")
                    print(f"   {activate_response.text[:200]}")
                    print("\n   WordPress REST API may not support direct theme activation.")
                    print("   Please activate manually in WordPress admin:")
                    print(f"   1. Go to: {site_url}/wp-admin/themes.php")
                    print(f"   2. Find theme: {theme_name}")
                    print(f"   3. Click 'Activate'")
                    return False
            else:
                print(f"⚠️  Theme '{theme_name}' not found in installed themes")
                print("   Available themes:")
                for theme in themes[:5]:  # Show first 5
                    print(f"      - {theme.get('stylesheet', 'unknown')}")
                print("\n   Theme files are uploaded but WordPress hasn't detected them yet.")
                print("   Try:")
                print("   1. Refresh the themes page in WordPress admin")
                print("   2. Clear WordPress cache")
                print("   3. Check file permissions on server")
                return False
        else:
            print(f"❌ Could not access themes API: {response.status_code}")
            print(f"   {response.text[:200]}")
            return False
            
    except Exception as e:
        print(f"❌ REST API activation error: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Activate WordPress theme via REST API'
    )
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--theme', type=str, required=True, help='Theme name')
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("🎨 WORDPRESS THEME ACTIVATION (REST API)")
    print("="*60)
    
    site_configs = load_site_configs()
    if not site_configs:
        print("\n❌ Could not load site_configs.json!")
        return 1
    
    success = activate_theme_via_rest_api(
        args.site,
        args.theme,
        site_configs
    )
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())

