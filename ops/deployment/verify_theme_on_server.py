#!/usr/bin/env python3
"""
Verify Theme Files on Server
============================

Checks if theme files exist on the server and lists available themes.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import os
from pathlib import Path

try:
    import paramiko
    PARAMIKO_AVAILABLE = True
except ImportError:
    PARAMIKO_AVAILABLE = False
    print("❌ paramiko library not installed. Install with: pip install paramiko")

try:
    from dotenv import load_dotenv
    DOTENV_AVAILABLE = True
except ImportError:
    DOTENV_AVAILABLE = False


def load_credentials(site_domain: str):
    """Load credentials from multiple sources."""
    import json
    
    # Try Hostinger env vars
    if DOTENV_AVAILABLE:
        env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
        if env_path.exists():
            load_dotenv(env_path)
    
    hostinger_creds = {
        "host": os.getenv("HOSTINGER_HOST"),
        "username": os.getenv("HOSTINGER_USER"),
        "password": os.getenv("HOSTINGER_PASS"),
        "port": int(os.getenv("HOSTINGER_PORT", "65002"))
    }
    
    # Try sites.json
    sites_json_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if sites_json_path.exists():
        try:
            with open(sites_json_path, 'r') as f:
                sites = json.load(f)
                site_creds = sites.get(site_domain)
                if site_creds:
                    return {
                        "host": site_creds.get('host') or hostinger_creds['host'],
                        "username": site_creds.get('username') or hostinger_creds['username'],
                        "password": site_creds.get('password') or hostinger_creds['password'],
                        "port": site_creds.get('port', hostinger_creds['port']),
                        "remote_path": site_creds.get('remote_path', f"domains/{site_domain}/public_html")
                    }
        except Exception as e:
            print(f"⚠️  Could not load sites.json: {e}")
    
    # Use Hostinger defaults
    if all([hostinger_creds['host'], hostinger_creds['username'], hostinger_creds['password']]):
        return {
            **hostinger_creds,
            "remote_path": f"domains/{site_domain}/public_html"
        }
    
    return None


def verify_theme_files(site_domain: str, theme_name: str):
    """Verify theme files exist on server and list themes."""
    if not PARAMIKO_AVAILABLE:
        print("❌ paramiko library required")
        return False
    
    creds = load_credentials(site_domain)
    if not creds:
        print(f"❌ No credentials found for {site_domain}")
        return False
    
    print(f"🔍 Verifying theme '{theme_name}' on {site_domain}...")
    print(f"   Host: {creds['host']}:{creds['port']}")
    print(f"   Path: {creds['remote_path']}")
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], 
                   password=creds['password'], timeout=10)
        
        # Build full theme path
        theme_path = f"{creds['remote_path']}/wp-content/themes/{theme_name}"
        full_paths = [
            theme_path,
            f"/home/{creds['username']}/{theme_path}",
        ]
        
        print(f"\n📁 Checking theme directory...")
        for path in full_paths:
            print(f"   Checking: {path}")
            cmd = f"test -d {path} && echo 'EXISTS' || echo 'NOT_FOUND'"
            stdin, stdout, stderr = ssh.exec_command(cmd, timeout=10)
            result = stdout.read().decode().strip()
            if result == "EXISTS":
                print(f"   ✅ Directory exists: {path}")
                
                # List files in theme directory
                print(f"\n📄 Listing theme files...")
                list_cmd = f"ls -la {path}"
                stdin, stdout, stderr = ssh.exec_command(list_cmd, timeout=10)
                files = stdout.read().decode()
                print(files)
                
                # Check for required files
                required_files = ['style.css', 'functions.php', 'index.php']
                for req_file in required_files:
                    check_cmd = f"test -f {path}/{req_file} && echo 'EXISTS' || echo 'MISSING'"
                    stdin, stdout, stderr = ssh.exec_command(check_cmd, timeout=10)
                    status = stdout.read().decode().strip()
                    if status == "EXISTS":
                        print(f"   ✅ {req_file}")
                    else:
                        print(f"   ❌ {req_file} - MISSING")
                
                break
            else:
                print(f"   ❌ Directory not found: {path}")
        
        # List all installed themes via WP-CLI
        print(f"\n🎨 Listing installed themes via WP-CLI...")
        wp_paths = [
            creds['remote_path'],
            f"/home/{creds['username']}/{creds['remote_path']}",
        ]
        
        for wp_path in wp_paths:
            print(f"   Trying WP path: {wp_path}")
            cmd = f"cd {wp_path} && wp theme list --allow-root 2>&1"
            stdin, stdout, stderr = ssh.exec_command(cmd, timeout=30)
            output = stdout.read().decode()
            error = stderr.read().decode()
            
            if output and "Error" not in output:
                print(f"   ✅ WP-CLI working!")
                print(f"\n   Installed themes:")
                print(output)
                
                # Check if our theme is in the list
                if theme_name in output:
                    print(f"\n   ✅ Theme '{theme_name}' found in WordPress!")
                else:
                    print(f"\n   ⚠️  Theme '{theme_name}' NOT found in WordPress list")
                    print(f"   Files exist but WordPress hasn't detected the theme yet.")
                    print(f"   Try:")
                    print(f"   1. Refresh themes page in WordPress admin")
                    print(f"   2. Clear WordPress cache")
                    print(f"   3. Check file permissions")
                break
            elif error:
                print(f"   ⚠️  WP-CLI error: {error[:200]}")
        
        ssh.close()
        return True
        
    except Exception as e:
        print(f"❌ SSH error: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Verify theme files on server'
    )
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--theme', type=str, required=True, help='Theme name')
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("🔍 THEME VERIFICATION ON SERVER")
    print("="*60)
    
    verify_theme_files(args.site, args.theme)
    
    return 0


if __name__ == '__main__':
    exit(main())

