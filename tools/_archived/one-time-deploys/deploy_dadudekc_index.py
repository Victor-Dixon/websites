#!/usr/bin/env python3
"""Deploy updated index.php for dadudekc.com"""

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

local_file = Path("D:/websites/sites/dadudekc.com/wp/theme/dadudekc/index.php").resolve()
remote_file = f"/home/{username}/domains/dadudekc.com/public_html/wp-content/themes/dadudekc/index.php"

try:
    transport = paramiko.Transport((host, port))
    transport.connect(username=username, password=password)
    sftp = paramiko.SFTPClient.from_transport(transport)
    
    with open(local_file, 'rb') as f:
        sftp.putfo(f, remote_file)
    
    sftp.close()
    transport.close()
    
    print("✅ Deployed updated index.php")
    
    # Clear cache
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port=port, username=username, password=password)
    
    wp_path = f"/home/{username}/domains/dadudekc.com/public_html"
    ssh.exec_command(f"cd {wp_path} && wp cache flush --allow-root")
    ssh.exec_command(f"cd {wp_path} && wp rewrite flush --allow-root")
    ssh.close()
    
    print("✅ Cache cleared")
    
except Exception as e:
    print(f"❌ Error: {e}")
    import traceback
    traceback.print_exc()


