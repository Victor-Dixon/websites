#!/usr/bin/env python3
"""Create WordPress pages for weareswarm.online BUILD-IN-PUBLIC"""

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

def create_page(site_domain, title, slug, template):
    """Create WordPress page with template."""
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password)
        
        wp_path = f"/home/{username}/domains/{site_domain}/public_html"
        
        # Check if exists
        check = f"cd {wp_path} && wp post list --post_type=page --name={slug} --field=ID --allow-root 2>&1"
        stdin, stdout, stderr = ssh.exec_command(check)
        page_id = stdout.read().decode().strip()
        
        if page_id and page_id.isdigit():
            # Update template
            update = f"cd {wp_path} && wp post meta update {page_id} _wp_page_template {template} --allow-root 2>&1"
            stdin, stdout, stderr = ssh.exec_command(update)
            print(f"✅ Updated '{title}' (ID: {page_id}) template to {template}")
            ssh.close()
            return page_id
        else:
            # Create page
            create = f"cd {wp_path} && wp post create --post_type=page --post_title='{title}' --post_name={slug} --post_status=publish --post_content='' --allow-root 2>&1"
            stdin, stdout, stderr = ssh.exec_command(create)
            output = stdout.read().decode()
            error = stderr.read().decode()
            
            # Extract ID from output
            if 'Success' in output or 'Created' in output:
                page_id = output.split()[-1] if output.split()[-1].isdigit() else None
                if not page_id:
                    # Try to extract from stderr or try listing again
                    check2 = f"cd {wp_path} && wp post list --post_type=page --name={slug} --field=ID --allow-root 2>&1"
                    stdin, stdout, stderr = ssh.exec_command(check2)
                    page_id = stdout.read().decode().strip()
                
                if page_id and page_id.isdigit():
                    # Set template
                    update = f"cd {wp_path} && wp post meta update {page_id} _wp_page_template {template} --allow-root 2>&1"
                    stdin, stdout, stderr = ssh.exec_command(update)
                    print(f"✅ Created '{title}' (ID: {page_id}) with template {template}")
                    ssh.close()
                    return page_id
            else:
                print(f"❌ Failed to create '{title}': {error}")
        
        ssh.close()
        return None
    except Exception as e:
        print(f"❌ Error: {e}")
        return None

print("=" * 60)
print("Creating weareswarm.online BUILD-IN-PUBLIC Pages")
print("=" * 60)

create_page("weareswarm.online", "Swarm Manifesto", "swarm-manifesto", "page-swarm-manifesto.php")
create_page("weareswarm.online", "How the Swarm Works", "how-the-swarm-works", "page-how-the-swarm-works.php")

print("\n✅ Pages created!")


