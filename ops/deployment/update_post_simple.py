#!/usr/bin/env python3
"""
Update WordPress Post (Simple - Using publish_post_wpcli method)
==================================================================

Uses the same proven method as publish_post_wpcli.py but for updates.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import os
import paramiko
from pathlib import Path
from dotenv import load_dotenv
import json


def load_credentials(site_domain: str):
    """Load credentials from multiple sources."""
    if Path("D:/Agent_Cellphone_V2_Repository/.env").exists():
        load_dotenv(Path("D:/Agent_Cellphone_V2_Repository/.env"))
    
    hostinger_creds = {
        "host": os.getenv("HOSTINGER_HOST"),
        "username": os.getenv("HOSTINGER_USER"),
        "password": os.getenv("HOSTINGER_PASS"),
        "port": int(os.getenv("HOSTINGER_PORT", "65002"))
    }
    
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
    
    if all([hostinger_creds['host'], hostinger_creds['username'], hostinger_creds['password']]):
        return {
            **hostinger_creds,
            "remote_path": f"domains/{site_domain}/public_html"
        }
    
    return None


def escape_for_shell(text: str) -> str:
    """Escape text for shell command."""
    return text.replace("'", "'\\''")


def format_content_as_html(content: str) -> str:
    """Convert markdown-like content to HTML."""
    paragraphs = content.split('\n\n')
    html_parts = []
    
    for para in paragraphs:
        para = para.strip()
        if not para:
            continue
        
        if para.startswith('### '):
            html_parts.append(f'<h3>{para[4:]}</h3>')
        elif para.startswith('## '):
            html_parts.append(f'<h2>{para[3:]}</h2>')
        elif para.startswith('# '):
            html_parts.append(f'<h1>{para[2:]}</h1>')
        elif para.startswith('* ') or para.startswith('- '):
            items = [line.strip()[2:] for line in para.split('\n') if line.strip().startswith(('* ', '- '))]
            if items:
                html_parts.append('<ul>')
                for item in items:
                    item = item.replace('**', '<strong>', 1).replace('**', '</strong>', 1)
                    item = item.replace('*', '<em>', 1).replace('*', '</em>', 1)
                    html_parts.append(f'<li>{item}</li>')
                html_parts.append('</ul>')
        elif '**' in para:
            parts = para.split('**')
            formatted = ''
            for i, part in enumerate(parts):
                if i % 2 == 1:
                    formatted += f'<strong>{part}</strong>'
                else:
                    formatted += part
            html_parts.append(f'<p>{formatted}</p>')
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


def update_post(site_domain: str, post_id: str, content: str, title: str = None) -> bool:
    """Update WordPress post using WP-CLI."""
    creds = load_credentials(site_domain)
    if not creds:
        print(f"❌ No credentials found for {site_domain}")
        return False
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], 
                   password=creds['password'], timeout=10)
        
        wp_path = f"/home/{creds['username']}/{creds['remote_path']}"
        
        # Format content as HTML
        html_content = format_content_as_html(content)
        
        # Escape for shell
        escaped_content = escape_for_shell(html_content)
        
        # Update post via WP-CLI
        if title:
            escaped_title = escape_for_shell(title)
            command = f"cd {wp_path} && wp post update {post_id} --post_title='{escaped_title}' --post_content='{escaped_content}' --allow-root 2>&1"
        else:
            command = f"cd {wp_path} && wp post update {post_id} --post_content='{escaped_content}' --allow-root 2>&1"
        
        print(f"   Updating post...")
        stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()
        result = output if output else error
        
        if "Success:" in result or "Updated post" in result or (not error and "Error" not in result):
            print(f"✅ Post {post_id} updated successfully")
            
            # Get post URL
            url_cmd = f"cd {wp_path} && wp post get {post_id} --field=url --allow-root 2>&1"
            stdin, stdout, stderr = ssh.exec_command(url_cmd, timeout=10)
            post_url = stdout.read().decode().strip()
            
            if post_url:
                print(f"   URL: {post_url}")
            
            ssh.close()
            return True
        else:
            print(f"❌ Failed to update post")
            print(f"   {result[:500]}")
            if error:
                print(f"   Error: {error[:500]}")
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
    
    parser = argparse.ArgumentParser(description='Update WordPress post')
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--post-id', type=str, required=True, help='Post ID')
    parser.add_argument('--file', type=str, help='Content file')
    parser.add_argument('--content', type=str, help='Content directly')
    parser.add_argument('--title', type=str, help='Update title')
    
    args = parser.parse_args()
    
    # Get content
    if args.file:
        with open(args.file, 'r', encoding='utf-8') as f:
            content = f.read()
    elif args.content:
        content = args.content
    else:
        content = sys.stdin.read()
    
    print("\n" + "="*60)
    print("📝 UPDATING WORDPRESS POST")
    print("="*60)
    print(f"Site: {args.site}")
    print(f"Post ID: {args.post_id}")
    
    success = update_post(args.site, args.post_id, content, args.title)
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())


