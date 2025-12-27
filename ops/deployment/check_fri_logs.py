#!/usr/bin/env python3
"""
Check FreeRideInvestor Server Files
===================================

Attempts to list files in the root directory to see if we can access wp-config.php
or existing error logs.

Author: Agent-7
"""

import sys
import os
from pathlib import Path

# Add current directory to path
sys.path.insert(0, str(Path(__file__).parent))

from simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs

def check_server_files():
    site_key = "freerideinvestor.com"
    site_configs = load_site_configs()
    
    if not site_configs:
        print("❌ Could not load site configurations!")
        return
        
    deployer = SimpleWordPressDeployer(site_key, site_configs)
    
    print(f"📡 Connecting to {site_key}...")
    if not deployer.connect():
        print("❌ Connection failed")
        return

    # Try to list files in public_html
    try:
        # Get base path
        username = deployer.site_config.get('username') or 'u996867598'
        remote_path = f"/home/{username}/domains/{site_key}/public_html"
        
        print(f"📂 Listing contents of {remote_path}...")
        files = deployer.sftp.listdir(remote_path)
        
        print("\nFiles found:")
        for f in files:
            if 'log' in f or 'config' in f or 'debug' in f:
                print(f" - {f}")
                
        # Check if debug.log exists in wp-content
        debug_log_path = f"{remote_path}/wp-content/debug.log"
        try:
            deployer.sftp.stat(debug_log_path)
            print(f"\n✅ Found existing debug.log at {debug_log_path}")
            
            # Read last 20 lines
            print("   Tail of debug.log:")
            cmd = f"tail -n 20 {debug_log_path}"
            output = deployer.execute_command(cmd)
            print(output)
            
        except FileNotFoundError:
            print(f"\nℹ️  No debug.log found at {debug_log_path}")

        # Check for error_log
        error_log_path = f"{remote_path}/error_log"
        try:
            deployer.sftp.stat(error_log_path)
            print(f"\n✅ Found error_log at {error_log_path}")
             # Read last 20 lines
            print("   Tail of error_log:")
            cmd = f"tail -n 20 {error_log_path}"
            output = deployer.execute_command(cmd)
            print(output)
        except FileNotFoundError:
            print(f"\nℹ️  No error_log found at {error_log_path}")

    except Exception as e:
        print(f"❌ Error listing files: {e}")
    finally:
        deployer.disconnect()

if __name__ == "__main__":
    check_server_files()
