#!/usr/bin/env python3
"""Activate BUILD-IN-PUBLIC Phase 0 themes"""

import sys
import os
from pathlib import Path

try:
    import paramiko
    import requests
    from dotenv import load_dotenv
except ImportError:
    print("❌ Missing dependencies")
    sys.exit(1)

env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
if env_path.exists():
    load_dotenv(env_path)

def activate_via_rest_api(site_url, username, app_password, theme_name):
    """Activate theme via WordPress REST API."""
    try:
        api_url = f"{site_url}/wp-json/wp/v2/themes"
        auth = (username, app_password)
        
        # Get all themes
        response = requests.get(api_url, auth=auth, timeout=10)
        if response.status_code != 200:
            print(f"❌ Cannot get themes: {response.status_code}")
            return False
        
        themes = response.json()
        theme_id = None
        
        for theme in themes:
            if theme.get('stylesheet') == theme_name or theme.get('name') == theme_name:
                theme_id = theme.get('stylesheet')
                break
        
        if not theme_id:
            print(f"❌ Theme '{theme_name}' not found")
            return False
        
        # Activate theme
        activate_url = f"{site_url}/wp-json/wp/v2/themes/{theme_id}"
        activate_response = requests.post(
            f"{activate_url}?action=activate",
            auth=auth,
            timeout=10
        )
        
        if activate_response.status_code in [200, 204]:
            print(f"✅ Theme '{theme_name}' activated via REST API")
            return True
        else:
            print(f"❌ Activation failed: {activate_response.status_code}")
            return False
            
    except Exception as e:
        print(f"❌ REST API error: {e}")
        return False

def activate_via_wp_cli(site_domain, theme_name):
    """Activate theme via WP-CLI over SSH."""
    host = os.getenv("HOSTINGER_HOST", "157.173.214.121")
    username = os.getenv("HOSTINGER_USER", "u996867598")
    password = os.getenv("HOSTINGER_PASS", "Falcons#1247")
    port = int(os.getenv("HOSTINGER_PORT", "65002"))
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password)
        
        wp_path = f"/home/{username}/domains/{site_domain}/public_html"
        command = f"cd {wp_path} && wp theme activate {theme_name}"
        
        stdin, stdout, stderr = ssh.exec_command(command)
        output = stdout.read().decode()
        error = stderr.read().decode()
        
        ssh.close()
        
        if "Success" in output or "Activated" in output or not error:
            print(f"✅ Theme '{theme_name}' activated via WP-CLI")
            return True
        else:
            print(f"⚠️  Output: {output}")
            if error:
                print(f"   Error: {error}")
            return False
            
    except Exception as e:
        print(f"❌ SSH error: {e}")
        return False

def activate_theme(site_domain, theme_name, site_config=None):
    """Activate theme using best available method."""
    print(f"\n🎨 Activating {theme_name} on {site_domain}...")
    
    # Try REST API first if credentials available
    if site_config and 'rest_api' in site_config:
        rest_api = site_config['rest_api']
        username = rest_api.get('username')
        app_password = rest_api.get('app_password')
        site_url = rest_api.get('site_url', f"https://{site_domain}")
        
        if username and app_password:
            if activate_via_rest_api(site_url, username, app_password, theme_name):
                return True
    
    # Fallback to WP-CLI
    print("   Trying WP-CLI...")
    return activate_via_wp_cli(site_domain, theme_name)

# Load site configs for REST API
site_configs = {}
config_path = Path("D:/websites/configs/site_configs.json")
if config_path.exists():
    import json
    with open(config_path, 'r') as f:
        site_configs = json.load(f)

print("=" * 60)
print("ACTIVATING BUILD-IN-PUBLIC Phase 0 THEMES")
print("=" * 60)

# Activate dadudekc.com
dadudekc_config = site_configs.get("dadudekc.com", {})
activate_theme("dadudekc.com", "dadudekc", dadudekc_config)

# Activate weareswarm.online
weareswarm_config = site_configs.get("weareswarm.online", {})
activate_theme("weareswarm.online", "swarm", weareswarm_config)

print("\n" + "=" * 60)
print("Theme activation complete!")


