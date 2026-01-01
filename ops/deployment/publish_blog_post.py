#!/usr/bin/env python3
"""
Publish Blog Post to WordPress
==============================

Publishes a blog post to WordPress using REST API.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import json
import base64
from pathlib import Path
from typing import Dict, Optional

try:
    import requests
    from requests.auth import HTTPBasicAuth
    REQUESTS_AVAILABLE = True
except ImportError:
    REQUESTS_AVAILABLE = False
    print("❌ 'requests' library not installed. Install with: pip install requests")


def load_site_configs():
    """Load site configurations from configs/site_configs.json"""
    config_path = Path("D:/websites/configs/site_configs.json")
    if not config_path.exists():
        config_path = Path(__file__).parent.parent.parent / "configs" / "site_configs.json"
    
    if config_path.exists():
        try:
            with open(config_path, 'r') as f:
                return json.load(f)
        except Exception as e:
            print(f"❌ Could not load site_configs.json: {e}")
            return {}
    return {}


def publish_post_via_rest_api(site_domain: str, title: str, content: str, status: str = 'publish', categories: list = None, tags: list = None) -> bool:
    """Publish a blog post using WordPress REST API."""
    if not REQUESTS_AVAILABLE:
        print("❌ 'requests' library required")
        return False
    
    site_configs = load_site_configs()
    site_config = site_configs.get(site_domain, {})
    rest_api = site_config.get('rest_api', {})
    
    username = rest_api.get('username')
    app_password = rest_api.get('app_password')
    site_url = rest_api.get('site_url', site_config.get('site_url', f"https://{site_domain}"))
    
    if not username or not app_password:
        print(f"❌ Missing REST API credentials for {site_domain}")
        print("   Please add username and app_password to configs/site_configs.json")
        return False
    
    print(f"📝 Publishing blog post via REST API...")
    print(f"   Site: {site_url}")
    print(f"   Title: {title}")
    print(f"   Status: {status}")
    
    try:
        # WordPress REST API endpoint
        api_url = f"{site_url.rstrip('/')}/wp-json/wp/v2/posts"
        
        auth = HTTPBasicAuth(username, app_password)
        
        # Prepare post data
        post_data = {
            'title': title,
            'content': content,
            'status': status,
        }
        
        if categories:
            post_data['categories'] = categories
        
        if tags:
            post_data['tags'] = tags
        
        print(f"   Publishing post...")
        response = requests.post(
            api_url,
            auth=auth,
            json=post_data,
            timeout=30
        )
        
        if response.status_code in [200, 201]:
            post = response.json()
            post_id = post.get('id')
            post_url = post.get('link', f"{site_url}/?p={post_id}")
            
            print(f"✅ Post published successfully!")
            print(f"   Post ID: {post_id}")
            print(f"   URL: {post_url}")
            return True
        else:
            print(f"❌ Failed to publish post: {response.status_code}")
            print(f"   {response.text[:500]}")
            return False
            
    except Exception as e:
        print(f"❌ REST API error: {e}")
        import traceback
        traceback.print_exc()
        return False


def format_content_as_html(content: str) -> str:
    """Convert markdown-like content to HTML."""
    # Split into paragraphs
    paragraphs = content.split('\n\n')
    html_parts = []
    
    for para in paragraphs:
        para = para.strip()
        if not para:
            continue
        
        # Check for headers
        if para.startswith('### '):
            html_parts.append(f'<h3>{para[4:]}</h3>')
        elif para.startswith('## '):
            html_parts.append(f'<h2>{para[3:]}</h2>')
        elif para.startswith('# '):
            html_parts.append(f'<h1>{para[2:]}</h1>')
        # Check for lists
        elif para.startswith('* ') or para.startswith('- '):
            items = [line.strip()[2:] for line in para.split('\n') if line.strip().startswith(('* ', '- '))]
            if items:
                html_parts.append('<ul>')
                for item in items:
                    # Handle bold text
                    item = item.replace('**', '<strong>', 1).replace('**', '</strong>', 1)
                    html_parts.append(f'<li>{item}</li>')
                html_parts.append('</ul>')
        # Check for bold text
        elif '**' in para:
            # Simple bold replacement
            parts = para.split('**')
            formatted = ''
            for i, part in enumerate(parts):
                if i % 2 == 1:
                    formatted += f'<strong>{part}</strong>'
                else:
                    formatted += part
            html_parts.append(f'<p>{formatted}</p>')
        else:
            html_parts.append(f'<p>{para}</p>')
    
    return '\n'.join(html_parts)


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Publish blog post to WordPress'
    )
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--title', type=str, required=True, help='Post title')
    parser.add_argument('--content', type=str, help='Post content (or read from stdin)')
    parser.add_argument('--file', type=str, help='Read content from file')
    parser.add_argument('--status', type=str, default='publish', choices=['draft', 'publish'], help='Post status')
    parser.add_argument('--format', action='store_true', help='Format content as HTML')
    
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
    
    # Format if requested
    if args.format:
        content = format_content_as_html(content)
    
    print("\n" + "="*60)
    print("📝 WORDPRESS BLOG POST PUBLISHER")
    print("="*60)
    
    success = publish_post_via_rest_api(
        args.site,
        args.title,
        content,
        args.status
    )
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())


