#!/usr/bin/env python3
"""
Create WordPress Pages for Digital Dreamscape
============================================

Creates required pages (Blog, About, Streaming, Community) for digitaldreamscape.site
using WP-CLI over SSH.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-21
"""

import sys
import os
from pathlib import Path
from typing import Dict, Optional

try:
    import paramiko
    PARAMIKO_AVAILABLE = True
except ImportError:
    PARAMIKO_AVAILABLE = False
    print("❌ paramiko library not installed. Install with: pip install paramiko")

try:
    from dotenv import load_dotenv
    DOTENV_AVAILABLE = True
except ImportError:
    DOTENV_AVAILABLE = False


def load_credentials(site_domain: str):
    """Load credentials from multiple sources."""
    import json
    
    # Try Hostinger env vars
    if DOTENV_AVAILABLE:
        env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
        if env_path.exists():
            load_dotenv(env_path)
    
    hostinger_creds = {
        "host": os.getenv("HOSTINGER_HOST"),
        "username": os.getenv("HOSTINGER_USER"),
        "password": os.getenv("HOSTINGER_PASS"),
        "port": int(os.getenv("HOSTINGER_PORT", "65002"))
    }
    
    # Try sites.json
    sites_json_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if sites_json_path.exists():
        try:
            with open(sites_json_path, 'r') as f:
                sites = json.load(f)
                site_creds = sites.get(site_domain)
                if site_creds:
                    return {
                        "host": site_creds.get('host') or hostinger_creds['host'],
                        "username": site_creds.get('username') or hostinger_creds['username'],
                        "password": site_creds.get('password') or hostinger_creds['password'],
                        "port": site_creds.get('port', hostinger_creds['port']),
                        "remote_path": site_creds.get('remote_path', f"domains/{site_domain}/public_html")
                    }
        except Exception as e:
            print(f"⚠️  Could not load sites.json: {e}")
    
    # Use Hostinger defaults
    if all([hostinger_creds['host'], hostinger_creds['username'], hostinger_creds['password']]):
        return {
            **hostinger_creds,
            "remote_path": f"domains/{site_domain}/public_html"
        }
    
    return None


def create_wordpress_page(ssh, wp_path: str, title: str, slug: str, content: str = "") -> bool:
    """Create a WordPress page using WP-CLI."""
    if not content:
        content = f"<!-- {title} page content coming soon -->"
    
    # Escape content for shell
    content_escaped = content.replace('"', '\\"').replace('$', '\\$')
    
    # Create page
    command = f'cd {wp_path} && wp post create --post_type=page --post_title="{title}" --post_name="{slug}" --post_content="{content_escaped}" --post_status=publish --allow-root 2>&1'
    
    stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
    output = stdout.read().decode()
    error = stderr.read().decode()
    result = output if output else error
    
    if "Success:" in result or "Created post" in result:
        print(f"   ✅ Created page: {title} ({slug})")
        return True
    elif "already exists" in result.lower() or "duplicate" in result.lower():
        print(f"   ⚠️  Page already exists: {title} ({slug})")
        return True
    else:
        print(f"   ❌ Failed to create page: {title}")
        print(f"      {result[:200]}")
        return False


def create_digitaldreamscape_pages(site_domain: str):
    """Create required pages for digitaldreamscape.site."""
    if not PARAMIKO_AVAILABLE:
        print("❌ paramiko library required")
        return False
    
    creds = load_credentials(site_domain)
    if not creds:
        print(f"❌ No credentials found for {site_domain}")
        return False
    
    print(f"📄 Creating WordPress pages for {site_domain}...")
    print(f"   Host: {creds['host']}:{creds['port']}")
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], 
                   password=creds['password'], timeout=10)
        
        # Build WP path
        wp_path = f"/home/{creds['username']}/{creds['remote_path']}"
        
        print(f"   WP Path: {wp_path}")
        
        # Pages to create
        pages = [
            {
                "title": "Blog",
                "slug": "blog",
                "content": "<!-- Blog page - WordPress will display posts here -->"
            },
            {
                "title": "About",
                "slug": "about",
                "content": "<!-- About page content coming soon -->"
            },
            {
                "title": "Streaming",
                "slug": "streaming",
                "content": "<!-- Streaming page content coming soon -->"
            },
            {
                "title": "Community",
                "slug": "community",
                "content": "<!-- Community page content coming soon -->"
            }
        ]
        
        print(f"\n📝 Creating {len(pages)} pages...")
        created = 0
        for page in pages:
            if create_wordpress_page(ssh, wp_path, page['title'], page['slug'], page['content']):
                created += 1
        
        print(f"\n✅ Created {created}/{len(pages)} pages")
        
        # List all pages
        print(f"\n📋 Listing all pages...")
        list_cmd = f"cd {wp_path} && wp post list --post_type=page --format=table --allow-root 2>&1"
        stdin, stdout, stderr = ssh.exec_command(list_cmd, timeout=30)
        output = stdout.read().decode()
        if output:
            print(output)
        
        ssh.close()
        return created == len(pages)
        
    except Exception as e:
        print(f"❌ SSH error: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Create WordPress pages for digitaldreamscape.site'
    )
    parser.add_argument('--site', type=str, default='digitaldreamscape.site', help='Site domain')
    
    args = parser.parse_args()
    
    print("\n" + "="*60)
    print("📄 CREATE WORDPRESS PAGES")
    print("="*60)
    
    success = create_digitaldreamscape_pages(args.site)
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())

