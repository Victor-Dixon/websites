#!/usr/bin/env python3
"""Fix BUILD-IN-PUBLIC Phase 0 visibility issues"""

import sys
import os
from pathlib import Path

try:
    import paramiko
    import requests
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

def clear_wp_cache(site_domain):
    """Clear WordPress cache via WP-CLI."""
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password)
        
        wp_path = f"/home/{username}/domains/{site_domain}/public_html"
        
        # Clear all caches
        commands = [
            "wp cache flush --allow-root",
            "wp rewrite flush --allow-root",
            "wp transient delete --all --allow-root"
        ]
        
        print(f"\n🧹 Clearing cache for {site_domain}...")
        for cmd in commands:
            full_cmd = f"cd {wp_path} && {cmd}"
            stdin, stdout, stderr = ssh.exec_command(full_cmd)
            output = stdout.read().decode()
            error = stderr.read().decode()
            if output:
                print(f"   {cmd}: {output.strip()}")
            if error and 'Error' in error:
                print(f"   ⚠️  {cmd}: {error.strip()}")
        
        ssh.close()
        print(f"✅ Cache cleared for {site_domain}")
        return True
    except Exception as e:
        print(f"❌ Error clearing cache: {e}")
        return False

def create_wordpress_page(site_domain, title, slug, template_name, content=""):
    """Create WordPress page with specific template via WP-CLI."""
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password)
        
        wp_path = f"/home/{username}/domains/{site_domain}/public_html"
        
        # Check if page exists
        check_cmd = f"cd {wp_path} && wp post list --post_type=page --name={slug} --field=ID --allow-root"
        stdin, stdout, stderr = ssh.exec_command(check_cmd)
        existing_id = stdout.read().decode().strip()
        
        if existing_id:
            print(f"   Page '{title}' already exists (ID: {existing_id})")
            # Update template
            update_cmd = f"cd {wp_path} && wp post meta update {existing_id} _wp_page_template {template_name} --allow-root"
            stdin, stdout, stderr = ssh.exec_command(update_cmd)
            print(f"   ✅ Updated template to {template_name}")
            ssh.close()
            return existing_id
        else:
            # Create new page
            create_cmd = f"cd {wp_path} && wp post create --post_type=page --post_title='{title}' --post_name={slug} --post_status=publish --post_content='{content}' --allow-root"
            stdin, stdout, stderr = ssh.exec_command(create_cmd)
            new_id = stdout.read().decode().strip()
            
            if new_id and new_id.isdigit():
                # Set template
                template_cmd = f"cd {wp_path} && wp post meta update {new_id} _wp_page_template {template_name} --allow-root"
                stdin, stdout, stderr = ssh.exec_command(template_cmd)
                print(f"   ✅ Created page '{title}' (ID: {new_id}) with template {template_name}")
                ssh.close()
                return new_id
            else:
                error = stderr.read().decode()
                print(f"   ❌ Failed to create page: {error}")
                ssh.close()
                return None
                
    except Exception as e:
        print(f"❌ Error creating page: {e}")
        return None

print("=" * 60)
print("BUILD-IN-PUBLIC Phase 0 Visibility Fixes")
print("=" * 60)

# Fix dadudekc.com - Clear cache first
print("\n1. Fixing dadudekc.com visibility...")
clear_wp_cache("dadudekc.com")

# Fix weareswarm.online - Create pages and clear cache
print("\n2. Fixing weareswarm.online visibility...")

# Create Swarm Manifesto page
print("   Creating 'Swarm Manifesto' page...")
create_wordpress_page(
    "weareswarm.online",
    "Swarm Manifesto",
    "swarm-manifesto",
    "page-swarm-manifesto.php",
    "The Swarm Manifesto outlines our core principles and approach."
)

# Create How the Swarm Works page
print("   Creating 'How the Swarm Works' page...")
create_wordpress_page(
    "weareswarm.online",
    "How the Swarm Works",
    "how-the-swarm-works",
    "page-how-the-swarm-works.php",
    "Learn how our multi-agent system operates."
)

# Clear cache
clear_wp_cache("weareswarm.online")

print("\n" + "=" * 60)
print("Visibility fixes complete!")
print("=" * 60)
print("\nNext steps:")
print("1. Verify BUILD-IN-PUBLIC sections are visible on dadudekc.com")
print("2. Verify pages are accessible on weareswarm.online")
print("3. Update navigation links if needed")


