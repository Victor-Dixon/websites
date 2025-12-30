#!/usr/bin/env python3
"""Verify actual deployment status by checking remote server"""

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

def check_remote_files(site_domain, theme_name, expected_files):
    """Check if files actually exist on remote server."""
    print(f"\n{'='*60}")
    print(f"Checking {site_domain} - theme: {theme_name}")
    print(f"{'='*60}\n")
    
    remote_path = f"/home/{username}/domains/{site_domain}/public_html/wp-content/themes/{theme_name}"
    
    try:
        transport = paramiko.Transport((host, port))
        transport.connect(username=username, password=password)
        sftp = paramiko.SFTPClient.from_transport(transport)
        
        print(f"📡 Connected, checking: {remote_path}\n")
        
        found = 0
        missing = []
        
        for file_name in expected_files:
            remote_file = f"{remote_path}/{file_name}"
            try:
                sftp.stat(remote_file)
                found += 1
                print(f"✅ {file_name}")
            except FileNotFoundError:
                missing.append(file_name)
                print(f"❌ {file_name} - NOT FOUND")
        
        # List directory contents
        print(f"\n📁 Directory listing:")
        try:
            files = sftp.listdir(remote_path)
            for f in sorted(files):
                print(f"   - {f}")
        except Exception as e:
            print(f"   ❌ Cannot list directory: {e}")
            print(f"   Directory might not exist: {remote_path}")
        
        sftp.close()
        transport.close()
        
        print(f"\n📊 Summary: {found}/{len(expected_files)} files found")
        if missing:
            print(f"   Missing: {', '.join(missing)}")
        
        return found == len(expected_files)
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False

# Check dadudekc.com
print("=" * 60)
print("DEPLOYMENT VERIFICATION")
print("=" * 60)

dadudekc_files = ["style.css", "functions.php", "header.php", "footer.php", "front-page.php", "page-contact.php", "index.php"]
dadudekc_ok = check_remote_files("dadudekc.com", "dadudekc", dadudekc_files)

weareswarm_files = ["style.css", "functions.php", "header.php", "footer.php", "index.php", "page-swarm-manifesto.php", "page-how-the-swarm-works.php", "front-page.php"]
weareswarm_ok = check_remote_files("weareswarm.online", "swarm", weareswarm_files)

print("\n" + "=" * 60)
print("VERIFICATION SUMMARY")
print("=" * 60)
print(f"dadudekc.com: {'✅ DEPLOYED' if dadudekc_ok else '❌ NOT DEPLOYED'}")
print(f"weareswarm.online: {'✅ DEPLOYED' if weareswarm_ok else '❌ NOT DEPLOYED'}")


