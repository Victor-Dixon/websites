#!/usr/bin/env python3
"""
Update WordPress Post Content (Fixed)
=====================================

Properly updates WordPress post content via WP-CLI.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import os
import paramiko
from pathlib import Path
from dotenv import load_dotenv
import json
import re


def load_credentials(site_domain: str) -> dict:
    """Load SSH/SFTP credentials for the site."""
    env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
    if env_path.exists():
        load_dotenv(env_path)
    
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
                    creds = {
                        "host": site_creds.get('host') or hostinger_creds['host'],
                        "username": site_creds.get('username') or hostinger_creds['username'],
                        "password": site_creds.get('password') or hostinger_creds['password'],
                        "port": site_creds.get('port', hostinger_creds['port']),
                        "remote_path": site_creds.get('remote_path', f"domains/{site_domain}/public_html")
                    }
                else:
                    creds = {**hostinger_creds, "remote_path": f"domains/{site_domain}/public_html"}
        except Exception:
            creds = {**hostinger_creds, "remote_path": f"domains/{site_domain}/public_html"}
    else:
        creds = {**hostinger_creds, "remote_path": f"domains/{site_domain}/public_html"}
    
    return creds


def markdown_to_html(content: str) -> str:
    """Convert markdown to HTML."""
    html = content
    
    # Headers
    html = re.sub(r'^### (.*?)$', r'<h3>\1</h3>', html, flags=re.MULTILINE)
    html = re.sub(r'^## (.*?)$', r'<h2>\1</h2>', html, flags=re.MULTILINE)
    html = re.sub(r'^# (.*?)$', r'<h1>\1</h1>', html, flags=re.MULTILINE)
    
    # Bold
    html = re.sub(r'\*\*(.*?)\*\*', r'<strong>\1</strong>', html)
    
    # Italic
    html = re.sub(r'\*(.*?)\*', r'<em>\1</em>', html)
    
    # Lists
    lines = html.split('\n')
    in_list = False
    result = []
    
    for line in lines:
        stripped = line.strip()
        if stripped.startswith('* ') or stripped.startswith('- '):
            if not in_list:
                result.append('<ul>')
                in_list = True
            item = stripped[2:].strip()
            result.append(f'<li>{item}</li>')
        else:
            if in_list:
                result.append('</ul>')
                in_list = False
            if stripped:
                if not stripped.startswith('<'):
                    result.append(f'<p>{stripped}</p>')
                else:
                    result.append(stripped)
            elif not in_list:
                result.append('')
    
    if in_list:
        result.append('</ul>')
    
    return '\n'.join(result)


def update_post_content(site_domain: str, post_id: str, content: str, title: str = None) -> bool:
    """Update WordPress post content via WP-CLI."""
    creds = load_credentials(site_domain)
    
    if not all([creds.get('host'), creds.get('username'), creds.get('password')]):
        print(f"❌ Incomplete credentials for {site_domain}")
        return False
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], 
                   password=creds['password'], timeout=10)
        
        wp_path = f"/home/{creds['username']}/{creds['remote_path']}"
        
        # Convert markdown to HTML if needed
        if not content.strip().startswith('<'):
            html_content = markdown_to_html(content)
        else:
            html_content = content
        
        # Write content to remote temp file via SFTP
        sftp = ssh.open_sftp()
        remote_temp = f'/tmp/wp_post_{post_id}_{os.getpid()}.html'
        
        try:
            with sftp.open(remote_temp, 'w') as f:
                f.write(html_content)
        finally:
            sftp.close()
        
        # Use WP-CLI with file path directly
        # WP-CLI can read content from a file using @filename syntax
        if title:
            title_escaped = title.replace("'", "'\\''")
            command = f"cd {wp_path} && wp post update {post_id} --post_content=@{remote_temp} --post_title='{title_escaped}' --allow-root 2>&1"
        else:
            command = f"cd {wp_path} && wp post update {post_id} --post_content=@{remote_temp} --allow-root 2>&1"
        
        stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()
        result = output if output else error
        
        # Clean up temp file
        try:
            ssh.exec_command(f"rm -f {remote_temp}", timeout=5)
        except:
            pass
        
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
            print(f"   Output: {result[:500]}")
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
    
    parser = argparse.ArgumentParser(description='Update WordPress post content')
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--post-id', type=str, required=True, help='Post ID')
    parser.add_argument('--file', type=str, help='Content file (markdown or HTML)')
    parser.add_argument('--content', type=str, help='Content directly')
    parser.add_argument('--title', type=str, help='Update title too')
    
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
    print("📝 UPDATING WORDPRESS POST (FIXED)")
    print("="*60)
    print(f"Site: {args.site}")
    print(f"Post ID: {args.post_id}")
    
    success = update_post_content(args.site, args.post_id, content, args.title)
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())

