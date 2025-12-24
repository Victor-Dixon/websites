#!/usr/bin/env python3
"""
Deploy Themes via WordPress REST API
====================================

Uploads and activates themes using WordPress REST API with Application Passwords.
Uses credentials from configs/site_configs.json.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import json
import base64
import zipfile
import tempfile
from pathlib import Path
from typing import Dict, Optional, Tuple

try:
    import requests
    REQUESTS_AVAILABLE = True
except ImportError:
    REQUESTS_AVAILABLE = False
    print("⚠️  'requests' library not installed. Install with: pip install requests")

# Theme deployment configuration
THEME_CONFIGS = {
    "houstonsipqueen.com": {
        "theme_name": "houstonsipqueen",
        "theme_path": "websites/houstonsipqueen.com/wp/wp-content/themes/houstonsipqueen",
    },
    "digitaldreamscape.site": {
        "theme_name": "digitaldreamscape",
        "theme_path": "websites/digitaldreamscape.site/wp/wp-content/themes/digitaldreamscape",
    }
}


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


def create_theme_zip(theme_path: Path) -> Optional[Path]:
    """Create a ZIP archive of the theme directory."""
    try:
        temp_dir = Path(tempfile.gettempdir())
        zip_path = temp_dir / f"{theme_path.name}.zip"
        
        with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            for file_path in theme_path.rglob('*'):
                if file_path.is_file():
                    arcname = file_path.relative_to(theme_path)
                    zipf.write(file_path, arcname)
        
        return zip_path
    except Exception as e:
        print(f"❌ Error creating ZIP: {e}")
        return None


def upload_theme_via_rest_api(site_url: str, username: str, app_password: str, zip_path: Path) -> bool:
    """Upload theme ZIP via WordPress REST API."""
    if not REQUESTS_AVAILABLE:
        print("❌ 'requests' library required for REST API upload")
        return False
    
    try:
        print(f"📤 Uploading theme via REST API...")
        
        # WordPress REST API endpoint for theme installation
        # Note: WordPress doesn't have a direct REST API for theme upload
        # We'll need to use the admin-ajax.php or create a custom endpoint
        # For now, we'll provide instructions for manual upload
        
        # Alternative: Use WordPress Filesystem API via REST
        # This requires a custom plugin or direct file system access
        
        print("⚠️  WordPress REST API doesn't support direct theme ZIP upload.")
        print("   Using alternative method: SFTP upload + REST API activation")
        return False
        
    except Exception as e:
        print(f"❌ REST API upload error: {e}")
        return False


def activate_theme_via_rest_api(site_url: str, username: str, app_password: str, theme_name: str) -> bool:
    """Activate theme using WordPress REST API."""
    if not REQUESTS_AVAILABLE:
        return False
    
    try:
        print(f"🎨 Activating theme '{theme_name}' via REST API...")
        
        # WordPress REST API for themes (WordPress 5.9+)
        api_url = f"{site_url.rstrip('/')}/wp-json/wp/v2/themes"
        
        auth_string = f"{username}:{app_password}"
        auth_bytes = auth_string.encode('ascii')
        auth_b64 = base64.b64encode(auth_bytes).decode('ascii')
        headers = {
            'Authorization': f'Basic {auth_b64}',
            'Content-Type': 'application/json'
        }
        
        # First, get list of themes
        response = requests.get(api_url, headers=headers, timeout=30)
        
        if response.status_code == 200:
            themes = response.json()
            theme_found = any(t.get('stylesheet') == theme_name for t in themes)
            
            if theme_found:
                # Activate theme
                activate_url = f"{api_url}/{theme_name}"
                activate_data = {"status": "active"}
                
                activate_response = requests.post(
                    activate_url,
                    headers=headers,
                    json=activate_data,
                    timeout=30
                )
                
                if activate_response.status_code in [200, 201]:
                    print(f"✅ Theme '{theme_name}' activated!")
                    return True
                else:
                    print(f"⚠️  Activation response: {activate_response.status_code}")
                    print(f"   {activate_response.text}")
                    return False
            else:
                print(f"⚠️  Theme '{theme_name}' not found in installed themes")
                print("   Theme must be uploaded first")
                return False
        else:
            print(f"❌ Could not access themes API: {response.status_code}")
            print(f"   {response.text}")
            return False
            
    except Exception as e:
        print(f"❌ REST API activation error: {e}")
        return False


def deploy_theme(site_domain: str, theme_config: dict, site_configs: dict) -> Tuple[bool, bool]:
    """Deploy theme using site_configs.json credentials."""
    print(f"\n{'='*60}")
    print(f"🎨 THEME DEPLOYMENT: {site_domain}")
    print(f"{'='*60}\n")
    
    base_path = Path("D:/websites")
    theme_path = base_path / theme_config["theme_path"]
    
    if not theme_path.exists():
        print(f"❌ Theme path not found: {theme_path}")
        return False, False
    
    # Verify theme files
    required_files = ['style.css', 'functions.php', 'index.php']
    missing_files = [f for f in required_files if not (theme_path / f).exists()]
    if missing_files:
        print(f"❌ Missing required theme files: {missing_files}")
        return False, False
    
    print(f"✅ Theme files verified: {theme_path}")
    
    # Get site configuration
    site_config = site_configs.get(site_domain, {})
    rest_api = site_config.get('rest_api', {})
    
    username = rest_api.get('username')
    app_password = rest_api.get('app_password')
    site_url = rest_api.get('site_url', site_config.get('site_url', f"https://{site_domain}"))
    
    if not username or not app_password:
        print(f"❌ Missing REST API credentials for {site_domain}")
        print("   Please add username and app_password to configs/site_configs.json")
        return False, False
    
    # Create ZIP package
    print("📦 Creating theme ZIP package...")
    zip_path = create_theme_zip(theme_path)
    if not zip_path:
        return False, False
    
    print(f"✅ ZIP created: {zip_path}")
    
    # Note: WordPress REST API doesn't support direct theme ZIP upload
    # We need to use SFTP or manual upload, then activate via REST API
    print("\n⚠️  WordPress REST API doesn't support theme ZIP upload.")
    print("   Please upload theme manually, then we'll activate it via REST API.")
    print(f"   ZIP file: {zip_path}")
    print(f"   Upload to: {site_url}/wp-admin/theme-install.php?browse=upload")
    
    # Try to activate (assuming theme is already uploaded)
    activation_success = activate_theme_via_rest_api(
        site_url, username, app_password, theme_config["theme_name"]
    )
    
    return False, activation_success  # Upload not done via API, activation attempted


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Deploy WordPress themes via REST API'
    )
    parser.add_argument('--site', type=str, help='Site domain')
    parser.add_argument('--all', action='store_true', help='Deploy all themes')
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("🎨 WORDPRESS THEME DEPLOYMENT (REST API)")
    print("="*60)
    
    site_configs = load_site_configs()
    if not site_configs:
        print("\n❌ Could not load site_configs.json!")
        return 1
    
    if args.all:
        for site_domain, theme_config in THEME_CONFIGS.items():
            deploy_theme(site_domain, theme_config, site_configs)
    elif args.site:
        if args.site not in THEME_CONFIGS:
            print(f"❌ Unknown site: {args.site}")
            return 1
        deploy_theme(args.site, THEME_CONFIGS[args.site], site_configs)
    else:
        parser.print_help()
        return 1
    
    return 0


if __name__ == '__main__':
    exit(main())

