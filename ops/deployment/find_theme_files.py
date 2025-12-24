#!/usr/bin/env python3
"""
Find Theme Files on Server
==========================

Searches for theme files on the server to verify upload location.

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

try:
    from dotenv import load_dotenv
    env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
    if env_path.exists():
        load_dotenv(env_path)
except:
    pass


def find_theme_files(site_domain: str, theme_name: str):
    """Find where theme files are on the server."""
    if not PARAMIKO_AVAILABLE:
        print("❌ paramiko required")
        return
    
    import json
    
    # Get credentials
    sites_json_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    sites = {}
    if sites_json_path.exists():
        with open(sites_json_path, 'r') as f:
            sites = json.load(f)
    
    site_creds = sites.get(site_domain, {})
    
    host = site_creds.get('host') or os.getenv("HOSTINGER_HOST")
    username = site_creds.get('username') or os.getenv("HOSTINGER_USER")
    password = site_creds.get('password') or os.getenv("HOSTINGER_PASS")
    port = site_creds.get('port') or int(os.getenv("HOSTINGER_PORT", "65002"))
    remote_path = site_creds.get('remote_path', f"domains/{site_domain}/public_html")
    
    print(f"🔍 Searching for theme '{theme_name}' on {site_domain}...")
    print(f"   Host: {host}:{port}")
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password, timeout=10)
        
        # Try to find the theme directory
        search_paths = [
            f"{remote_path}/wp-content/themes/{theme_name}",
            f"/home/{username}/{remote_path}/wp-content/themes/{theme_name}",
            f"wp-content/themes/{theme_name}",
            f"public_html/wp-content/themes/{theme_name}",
        ]
        
        print(f"\n📁 Searching for theme directory...")
        found = False
        for search_path in search_paths:
            cmd = f"test -d {search_path} && echo 'FOUND:{search_path}' || echo 'NOT_FOUND'"
            stdin, stdout, stderr = ssh.exec_command(cmd, timeout=10)
            result = stdout.read().decode().strip()
            
            if result.startswith("FOUND:"):
                actual_path = result.split(":", 1)[1]
                print(f"   ✅ Found: {actual_path}")
                found = True
                
                # List files
                list_cmd = f"ls -la {actual_path}"
                stdin, stdout, stderr = ssh.exec_command(list_cmd, timeout=10)
                files = stdout.read().decode()
                print(f"\n   Files in theme directory:")
                print(files)
                break
        
        if not found:
            print(f"   ❌ Theme directory not found in expected locations")
            print(f"\n   Checking what's actually in wp-content/themes...")
            
            # List themes directory
            themes_paths = [
                f"{remote_path}/wp-content/themes",
                f"/home/{username}/{remote_path}/wp-content/themes",
            ]
            
            for themes_path in themes_paths:
                cmd = f"test -d {themes_path} && ls -la {themes_path} || echo 'NOT_FOUND'"
                stdin, stdout, stderr = ssh.exec_command(cmd, timeout=10)
                result = stdout.read().decode()
                
                if "NOT_FOUND" not in result:
                    print(f"   ✅ Themes directory: {themes_path}")
                    print(f"\n   Installed themes:")
                    print(result)
                    break
        
        # Check if files were uploaded to wrong location
        print(f"\n🔍 Searching for style.css file...")
        search_cmd = f"find /home/{username} -name 'style.css' -path '*/{theme_name}/*' 2>/dev/null | head -5"
        stdin, stdout, stderr = ssh.exec_command(search_cmd, timeout=30)
        found_files = stdout.read().decode()
        
        if found_files:
            print(f"   ✅ Found style.css files:")
            for line in found_files.strip().split('\n'):
                if line:
                    print(f"      {line}")
        else:
            print(f"   ❌ No style.css found for theme '{theme_name}'")
        
        ssh.close()
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()


def main():
    import argparse
    parser = argparse.ArgumentParser()
    parser.add_argument('--site', required=True)
    parser.add_argument('--theme', required=True)
    args = parser.parse_args()
    
    find_theme_files(args.site, args.theme)


if __name__ == '__main__':
    main()

