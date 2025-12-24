#!/usr/bin/env python3
"""
Publish Blog Post via WP-CLI
============================

Publishes a blog post to WordPress using WP-CLI over SSH.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import os
import json
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


def escape_for_shell(text: str) -> str:
    """Escape text for shell command."""
    # Replace single quotes with '\''
    text = text.replace("'", "'\\''")
    return text


def format_content_as_html(content: str) -> str:
    """Convert markdown-like content to HTML."""
    paragraphs = content.split('\n\n')
    html_parts = []
    
    for para in paragraphs:
        para = para.strip()
        if not para:
            continue
        
        # Headers
        if para.startswith('### '):
            html_parts.append(f'<h3>{para[4:]}</h3>')
        elif para.startswith('## '):
            html_parts.append(f'<h2>{para[3:]}</h2>')
        elif para.startswith('# '):
            html_parts.append(f'<h1>{para[2:]}</h1>')
        # Lists
        elif para.startswith('* ') or para.startswith('- '):
            items = [line.strip()[2:] for line in para.split('\n') if line.strip().startswith(('* ', '- '))]
            if items:
                html_parts.append('<ul>')
                for item in items:
                    # Handle bold and italic
                    item = item.replace('**', '<strong>', 1).replace('**', '</strong>', 1)
                    item = item.replace('*', '<em>', 1).replace('*', '</em>', 1)
                    html_parts.append(f'<li>{item}</li>')
                html_parts.append('</ul>')
        # Bold text
        elif '**' in para:
            parts = para.split('**')
            formatted = ''
            for i, part in enumerate(parts):
                if i % 2 == 1:
                    formatted += f'<strong>{part}</strong>'
                else:
                    formatted += part
            html_parts.append(f'<p>{formatted}</p>')
        # Italic text
        elif '*' in para and not para.startswith('*'):
            parts = para.split('*')
            formatted = ''
            for i, part in enumerate(parts):
                if i % 2 == 1:
                    formatted += f'<em>{part}</em>'
                else:
                    formatted += part
            html_parts.append(f'<p>{formatted}</p>')
        else:
            html_parts.append(f'<p>{para}</p>')
    
    return '\n'.join(html_parts)


def publish_post_via_wpcli(site_domain: str, title: str, content: str, status: str = 'publish') -> bool:
    """Publish a blog post using WP-CLI over SSH."""
    if not PARAMIKO_AVAILABLE:
        print("❌ paramiko library required")
        return False
    
    creds = load_credentials(site_domain)
    if not creds:
        print(f"❌ No credentials found for {site_domain}")
        return False
    
    print(f"📝 Publishing blog post via WP-CLI...")
    print(f"   Site: {site_domain}")
    print(f"   Title: {title}")
    print(f"   Status: {status}")
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], 
                   password=creds['password'], timeout=10)
        
        wp_path = f"/home/{creds['username']}/{creds['remote_path']}"
        
        # Format content as HTML
        html_content = format_content_as_html(content)
        
        # Escape for shell
        escaped_title = escape_for_shell(title)
        escaped_content = escape_for_shell(html_content)
        
        # Create post via WP-CLI
        command = f"cd {wp_path} && wp post create --post_title='{escaped_title}' --post_content='{escaped_content}' --post_status={status} --allow-root 2>&1"
        
        print(f"   Creating post...")
        stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()
        result = output if output else error
        
        if "Success:" in result or "Created post" in result or "ID:" in result:
            # Extract post ID
            post_id = None
            for line in result.split('\n'):
                if 'ID:' in line or 'post' in line.lower():
                    parts = line.split()
                    for i, part in enumerate(parts):
                        if part.isdigit():
                            post_id = part
                            break
                    if post_id:
                        break
            
            if post_id:
                # Get post URL
                url_cmd = f"cd {wp_path} && wp post get {post_id} --field=url --allow-root 2>&1"
                stdin, stdout, stderr = ssh.exec_command(url_cmd, timeout=10)
                post_url = stdout.read().decode().strip()
                
                print(f"✅ Post published successfully!")
                print(f"   Post ID: {post_id}")
                if post_url:
                    print(f"   URL: {post_url}")
                else:
                    print(f"   URL: https://{site_domain}/?p={post_id}")
            else:
                print(f"✅ Post published successfully!")
                print(f"   {result[:200]}")
            
            ssh.close()
            return True
        else:
            print(f"❌ Failed to publish post")
            print(f"   {result[:500]}")
            ssh.close()
            return False
        
    except Exception as e:
        print(f"❌ SSH error: {e}")
        import traceback
        traceback.print_exc()
        return False


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Publish blog post to WordPress via WP-CLI'
    )
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--title', type=str, required=True, help='Post title')
    parser.add_argument('--content', type=str, help='Post content (or read from stdin)')
    parser.add_argument('--file', type=str, help='Read content from file')
    parser.add_argument('--status', type=str, default='publish', choices=['draft', 'publish'], help='Post status')
    
    args = parser.parse_args()
    
    # Get content
    if args.file:
        with open(args.file, 'r', encoding='utf-8') as f:
            content = f.read()
    elif args.content:
        content = args.content
    else:
        # Read from stdin
        content = sys.stdin.read()
    
    print("\n" + "="*60)
    print("📝 WORDPRESS BLOG POST PUBLISHER (WP-CLI)")
    print("="*60)
    
    success = publish_post_via_wpcli(
        args.site,
        args.title,
        content,
        args.status
    )
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())


