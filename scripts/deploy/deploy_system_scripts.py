#!/usr/bin/env python3
"""
Deploy system scripts to Digital Dreamscape live server
"""

import paramiko
import sys
import os

# Add config to path for importing
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', '..', 'config'))

from paths import paths

def deploy_scripts(site_name: str = "digitaldreamscape.site"):
    """Deploy system scripts to the specified website on live server"""

    # Get server configuration (could be moved to config)
    host = '157.173.214.121'
    port = 65002
    username = 'u996867598'
    password = 'Falcons#1247'

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, port, username, password)

    try:
        print(f"📤 Deploying system scripts to {site_name}...")

        # Get website path using path manager
        site_path = paths.get_website_path(site_name)

        # Scripts to deploy (relative to site directory)
        script_files = [
            'canon_declaration_system.php',
            'system_status.php'
        ]

        # Convert to full paths
        scripts = [site_path / script_file for script_file in script_files]

        sftp = ssh.open_sftp()

        uploaded_count = 0
        for script_path in scripts:
            if script_path.exists():
                filename = script_path.name
                remote_path = f'domains/{site_name}/public_html/{filename}'

                print(f"📁 Uploading {filename}...")
                sftp.put(str(script_path), remote_path)
                print(f"✅ Uploaded {filename}")
                uploaded_count += 1
            else:
                print(f"⚠️ Local file not found: {script_path}")

        sftp.close()

        if uploaded_count > 0:
            # Make scripts executable
            script_names = [f.name for f in scripts if f.exists()]
            if script_names:
                chmod_cmd = f'cd domains/{site_name}/public_html && chmod +x {" ".join(script_names)}'
                stdin, stdout, stderr = ssh.exec_command(chmod_cmd)
                error = stderr.read().decode().strip()

                if error:
                    print(f"⚠️ Permission setting warning: {error}")

        print(f"\n✅ Script deployment completed! ({uploaded_count} files uploaded)")

    except Exception as e:
        print(f"❌ Error deploying scripts: {e}")

    finally:
        ssh.close()

if __name__ == "__main__":
    # Allow site name as command line argument
    site_name = sys.argv[1] if len(sys.argv) > 1 else "digitaldreamscape.site"
    deploy_scripts(site_name)