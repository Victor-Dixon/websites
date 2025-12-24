#!/usr/bin/env python3
"""
Activate Theme via SSH/WP-CLI
==============================

Activates WordPress themes using WP-CLI over SSH.
Uses SFTP credentials from sites.json or Hostinger env vars.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import os
from pathlib import Path
from typing import Dict, Optional

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


def load_hostinger_credentials():
    """Load Hostinger credentials from environment or .env file."""
    if DOTENV_AVAILABLE:
        env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
        if env_path.exists():
            load_dotenv(env_path)
    
    host = os.getenv("HOSTINGER_HOST")
    username = os.getenv("HOSTINGER_USER")
    password = os.getenv("HOSTINGER_PASS")
    port = int(os.getenv("HOSTINGER_PORT", "65002"))
    
    if all([host, username, password]):
        return {
            "host": host,
            "username": username,
            "password": password,
            "port": port
        }
    return None


def load_site_credentials(site_domain: str):
    """Load site-specific credentials from sites.json."""
    import json
    
    sites_json_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if sites_json_path.exists():
        try:
            with open(sites_json_path, 'r') as f:
                sites = json.load(f)
                return sites.get(site_domain)
        except Exception as e:
            print(f"⚠️  Could not load sites.json: {e}")
    return None


def activate_theme_via_ssh(site_domain: str, theme_name: str) -> bool:
    """Activate theme using WP-CLI over SSH."""
    if not PARAMIKO_AVAILABLE:
        print("❌ paramiko library required for SSH")
        return False
    
    # Get credentials
    site_creds = load_site_credentials(site_domain)
    hostinger_creds = load_hostinger_credentials()
    
    if not site_creds and not hostinger_creds:
        print(f"❌ No credentials found for {site_domain}")
        return False
    
    # Use site-specific or Hostinger defaults
    if site_creds:
        host = site_creds.get('host')
        username = site_creds.get('username')
        password = site_creds.get('password')
        port = site_creds.get('port', 22)
        remote_path = site_creds.get('remote_path', '')
    else:
        host = hostinger_creds['host']
        username = hostinger_creds['username']
        password = hostinger_creds['password']
        port = hostinger_creds['port']
        # Build remote path from domain
        remote_path = f"domains/{site_domain}/public_html"
    
    if not all([host, username, password]):
        print(f"❌ Incomplete credentials for {site_domain}")
        return False
    
    print(f"🎨 Activating theme '{theme_name}' via SSH...")
    print(f"   Host: {host}:{port}")
    print(f"   User: {username}")
    print(f"   Path: {remote_path}")
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password, timeout=10)
        
        # Try multiple WP-CLI path variations
        paths_to_try = [
            remote_path,
            f"/home/{username}/{remote_path}",
            f"/home/{username}/public_html",
            "/home/*/public_html"
        ]
        
        for wp_path in paths_to_try:
            if not wp_path:
                continue
            
            print(f"   Trying WP path: {wp_path}")
            
            # First, check if WP-CLI is available
            check_cmd = f"which wp"
            stdin, stdout, stderr = ssh.exec_command(check_cmd)
            wp_cli_path = stdout.read().decode().strip()
            
            if not wp_cli_path:
                print("   ⚠️  WP-CLI not found in PATH")
                # Try common locations
                wp_cli_path = "wp"
            
            # Try to activate theme
            command = f"cd {wp_path} && {wp_cli_path} theme activate {theme_name} --allow-root 2>&1"
            stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
            
            output = stdout.read().decode()
            error = stderr.read().decode()
            result = output if output else error
            
            print(f"   Output: {result[:200]}")
            
            if "Success" in result or "Activated" in result or "Theme activated" in result.lower():
                print(f"✅ Theme '{theme_name}' activated!")
                ssh.close()
                return True
            elif "Error" not in result and result.strip():
                # Might be success even without explicit message
                print(f"   Checking theme status...")
                status_cmd = f"cd {wp_path} && {wp_cli_path} theme list --status=active --field=name --allow-root 2>&1"
                stdin, stdout, stderr = ssh.exec_command(status_cmd, timeout=30)
                active_theme = stdout.read().decode().strip()
                if theme_name in active_theme:
                    print(f"✅ Theme '{theme_name}' is now active!")
                    ssh.close()
                    return True
        
        ssh.close()
        print(f"⚠️  Could not activate theme via WP-CLI")
        print("   Theme files are uploaded. Please activate manually:")
        print(f"   1. Go to: https://{site_domain}/wp-admin/themes.php")
        print(f"   2. Find theme: {theme_name}")
        print(f"   3. Click 'Activate'")
        return False
        
    except Exception as e:
        print(f"❌ SSH error: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Activate WordPress theme via SSH/WP-CLI'
    )
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--theme', type=str, required=True, help='Theme name')
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("🎨 WORDPRESS THEME ACTIVATION (SSH/WP-CLI)")
    print("="*60)
    
    success = activate_theme_via_ssh(args.site, args.theme)
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())

