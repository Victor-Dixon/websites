#!/usr/bin/env python3
"""Check WordPress post content."""
import paramiko
import os
from pathlib import Path
from dotenv import load_dotenv

load_dotenv(Path('D:/Agent_Cellphone_V2_Repository/.env'))

ssh = paramiko.SSHClient()
ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
ssh.connect(os.getenv('HOSTINGER_HOST'), port=int(os.getenv('HOSTINGER_PORT', '65002')), 
           username=os.getenv('HOSTINGER_USER'), password=os.getenv('HOSTINGER_PASS'), timeout=10)

wp_path = "/home/u996867598/domains/digitaldreamscape.site/public_html"

# Get post content
stdin, stdout, stderr = ssh.exec_command(
    f'cd {wp_path} && wp post get 9 --field=content --allow-root 2>&1',
    timeout=10
)
content = stdout.read().decode()
error = stderr.read().decode()

print("Content length:", len(content))
print("First 500 chars:")
print(content[:500])
print("\nError:", error[:200] if error else "None")

ssh.close()


