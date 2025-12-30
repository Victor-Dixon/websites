#!/usr/bin/env python3
"""Verify theme is actually active on WordPress site"""

import sys
import os
from pathlib import Path

try:
    import paramiko
    from dotenv import load_dotenv
except ImportError:
    print("❌ Missing dependencies")
    sys.exit(1)

env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
if env_path.exists():
    load_dotenv(env_path)

host = os.getenv("HOSTINGER_HOST", "157.173.214.121")
username = os.getenv("HOSTINGER_USER", "u996867598")
password = os.getenv("HOSTINGER_PASS", "Falcons#1247")
port = int(os.getenv("HOSTINGER_PORT", "65002"))

def check_active_theme(site_domain):
    """Check which theme is currently active."""
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password)
        
        wp_path = f"/home/{username}/domains/{site_domain}/public_html"
        command = f"cd {wp_path} && wp theme list --status=active --allow-root 2>&1"
        
        stdin, stdout, stderr = ssh.exec_command(command)
        output = stdout.read().decode()
        error = stderr.read().decode()
        
        ssh.close()
        
        print(f"\n{'='*60}")
        print(f"{site_domain} - Active Theme:")
        print(f"{'='*60}")
        if output.strip():
            print(output)
        if error.strip():
            print(f"Error: {error}")
        print()
        
        return output
        
    except Exception as e:
        print(f"❌ Error: {e}")
        return None

# Check all sites
check_active_theme("dadudekc.com")
check_active_theme("weareswarm.online")
check_active_theme("tradingrobotplug.com")


