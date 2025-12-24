#!/usr/bin/env python3
"""
Move Theme to Correct Location
==============================

Moves theme files from wrong location to correct WordPress themes directory.

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
    print("❌ paramiko required")

try:
    from dotenv import load_dotenv
    env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
    if env_path.exists():
        load_dotenv(env_path)
except:
    pass


def move_theme_files(site_domain: str, theme_name: str):
    """Move theme files to correct location."""
    if not PARAMIKO_AVAILABLE:
        return False
    
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
    
    print(f"📦 Moving theme '{theme_name}' to correct location...")
    print(f"   Site: {site_domain}")
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password, timeout=10)
        
        # Source: where files currently are
        source_path = f"/home/{username}/wp-content/themes/{theme_name}"
        
        # Destination: where WordPress expects them
        dest_path = f"/home/{username}/{remote_path}/wp-content/themes/{theme_name}"
        
        print(f"   Source: {source_path}")
        print(f"   Destination: {dest_path}")
        
        # Check if source exists
        check_cmd = f"test -d {source_path} && echo 'EXISTS' || echo 'NOT_FOUND'"
        stdin, stdout, stderr = ssh.exec_command(check_cmd, timeout=10)
        if "EXISTS" not in stdout.read().decode():
            print(f"   ❌ Source directory not found: {source_path}")
            ssh.close()
            return False
        
        # Create destination directory
        print(f"   Creating destination directory...")
        mkdir_cmd = f"mkdir -p {dest_path}"
        stdin, stdout, stderr = ssh.exec_command(mkdir_cmd, timeout=10)
        
        # Move files
        print(f"   Moving theme files...")
        move_cmd = f"cp -r {source_path}/* {dest_path}/ && echo 'SUCCESS' || echo 'FAILED'"
        stdin, stdout, stderr = ssh.exec_command(move_cmd, timeout=30)
        result = stdout.read().decode().strip()
        
        if "SUCCESS" in result or not stderr.read().decode():
            print(f"   ✅ Files moved successfully!")
            
            # Verify files in destination
            verify_cmd = f"ls -la {dest_path}"
            stdin, stdout, stderr = ssh.exec_command(verify_cmd, timeout=10)
            files = stdout.read().decode()
            print(f"\n   Files in destination:")
            print(files)
            
            # Set correct permissions
            print(f"   Setting permissions...")
            chmod_cmd = f"chmod -R 755 {dest_path} && chmod -R 644 {dest_path}/*.php {dest_path}/*.css 2>/dev/null; echo 'DONE'"
            stdin, stdout, stderr = ssh.exec_command(chmod_cmd, timeout=10)
            
            ssh.close()
            return True
        else:
            error = stderr.read().decode()
            print(f"   ❌ Move failed: {error}")
            ssh.close()
            return False
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    import argparse
    parser = argparse.ArgumentParser()
    parser.add_argument('--site', required=True)
    parser.add_argument('--theme', required=True)
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("📦 MOVE THEME TO CORRECT LOCATION")
    print("="*60)
    
    success = move_theme_files(args.site, args.theme)
    
    if success:
        print(f"\n✅ Theme files moved to correct location!")
        print(f"   Now try activating the theme:")
        print(f"   python ops/deployment/activate_theme_ssh.py --site {args.site} --theme {args.theme}")
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())

