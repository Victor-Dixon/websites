#!/usr/bin/env python3
"""
Delete Hello World Post
======================

Deletes the default WordPress "Hello world!" post via WP-CLI.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import paramiko
import json
import os
import sys
from pathlib import Path
from dotenv import load_dotenv

# Load credentials
env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
if env_path.exists():
    load_dotenv(env_path)

host = os.getenv("HOSTINGER_HOST")
username = os.getenv("HOSTINGER_USER")
password = os.getenv("HOSTINGER_PASS")
port = int(os.getenv("HOSTINGER_PORT", "65002"))

if not all([host, username, password]):
    print("❌ Missing Hostinger credentials")
    sys.exit(1)

# Connect and delete
try:
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, username=username, password=password, port=port)
    
    print("🔍 Searching for 'Hello world!' post...")
    stdin, stdout, stderr = ssh.exec_command(
        'cd domains/digitaldreamscape.site/public_html && wp post list --format=json --name=hello-world'
    )
    
    output = stdout.read().decode().strip()
    if not output:
        print("✅ No 'Hello world!' post found")
        ssh.close()
        sys.exit(0)
    
    posts = json.loads(output)
    if not posts:
        print("✅ No 'Hello world!' post found")
        ssh.close()
        sys.exit(0)
    
    for post in posts:
        post_id = post['ID']
        post_title = post['post_title']
        print(f"🗑️  Deleting post ID {post_id}: '{post_title}'")
        
        stdin, stdout, stderr = ssh.exec_command(
            f'cd domains/digitaldreamscape.site/public_html && wp post delete {post_id} --force'
        )
        
        result = stdout.read().decode().strip()
        error = stderr.read().decode().strip()
        
        if error and "Error" in error:
            print(f"❌ Error: {error}")
        else:
            print(f"✅ Deleted: {post_title}")
    
    ssh.close()
    print("✅ Done!")
    
except Exception as e:
    print(f"❌ Error: {e}")
    sys.exit(1)

