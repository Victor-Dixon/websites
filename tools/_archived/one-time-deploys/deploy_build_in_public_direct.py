#!/usr/bin/env python3
"""Deploy BUILD-IN-PUBLIC Phase 0 - Direct SFTP deployment"""

import sys
import os
from pathlib import Path

try:
    import paramiko
    from dotenv import load_dotenv
except ImportError:
    print("❌ Missing dependencies: pip install paramiko python-dotenv")
    sys.exit(1)

# Load .env
env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
if env_path.exists():
    load_dotenv(env_path)

# SFTP credentials
host = os.getenv("HOSTINGER_HOST", "157.173.214.121")
username = os.getenv("HOSTINGER_USER", "u996867598")
password = os.getenv("HOSTINGER_PASS", "Falcons#1247")
port = int(os.getenv("HOSTINGER_PORT", "65002"))

def deploy_theme(site_domain, local_theme_path, remote_theme_path):
    """Deploy theme files directly via SFTP."""
    print(f"\n{'='*60}")
    print(f"Deploying {site_domain}...")
    print(f"{'='*60}\n")
    
    local_path = Path(local_theme_path).resolve()
    if not local_path.exists():
        print(f"❌ Local path not found: {local_path}")
        return False
    
    try:
        # Connect
        print(f"🔌 Connecting to {host}:{port}...")
        transport = paramiko.Transport((host, port))
        transport.connect(username=username, password=password)
        sftp = paramiko.SFTPClient.from_transport(transport)
        print("✅ Connected!\n")
        
        deployed = 0
        failed = 0
        
        # Deploy all theme files
        for file_path in local_path.rglob('*'):
            if file_path.is_file() and file_path.suffix in ['.php', '.css', '.js']:
                relative = file_path.relative_to(local_path)
                remote_file = f"/home/{username}/{remote_theme_path}/{relative.as_posix()}".replace('\\', '/')
                
                print(f"📤 {relative}...", end=' ')
                
                try:
                    # Create remote directory
                    remote_dir = str(Path(remote_file).parent)
                    parts = remote_dir.strip('/').split('/')
                    current = ''
                    for part in parts:
                        if part:
                            current = f"{current}/{part}" if current else f"/{part}"
                            try:
                                sftp.stat(current)
                            except FileNotFoundError:
                                try:
                                    sftp.mkdir(current)
                                except:
                                    pass
                    
                    # Upload file - use absolute Windows path converted properly
                    local_file_str = str(file_path.resolve())
                    # Open file handle and upload
                    with open(local_file_str, 'rb') as f:
                        sftp.putfo(f, remote_file)
                    
                    deployed += 1
                    print("✅")
                except Exception as e:
                    failed += 1
                    print(f"❌ {e}")
        
        sftp.close()
        transport.close()
        
        print(f"\n✅ Deployed {deployed} files, {failed} failed")
        return failed == 0
        
    except Exception as e:
        print(f"❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return False

# Deploy
print("=" * 60)
print("BUILD-IN-PUBLIC Phase 0 Deployment")
print("=" * 60)

# dadudekc.com
deploy_theme(
    "dadudekc.com",
    "D:/websites/sites/dadudekc.com/wp/theme/dadudekc",
    "domains/dadudekc.com/public_html/wp-content/themes/dadudekc"
)

# weareswarm.online
deploy_theme(
    "weareswarm.online",
    "D:/websites/sites/weareswarm.online/wp/theme/swarm",
    "domains/weareswarm.online/public_html/wp-content/themes/swarm"
)

print("\n" + "=" * 60)
print("Deployment complete!")


