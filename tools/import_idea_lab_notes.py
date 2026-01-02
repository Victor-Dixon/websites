#!/usr/bin/env python3
"""
Import Idea Lab Notes from IDEA_LAB_NOTES.md to dadudekc.com

Parses the IDEA_LAB_NOTES.md file and creates WordPress notes/posts
in the Idea Lab on dadudekc.com.
"""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path
from typing import Any

# Add repo root to path
REPO_ROOT = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(REPO_ROOT))

import requests
from requests.auth import HTTPBasicAuth

# Import site config directly to avoid dependency issues
def get_site_config(site: str) -> dict:
    """Get site configuration from config/site_configs.json."""
    import json
    config_path = REPO_ROOT / "config" / "site_configs.json"
    if config_path.exists():
        with open(config_path, 'r') as f:
            configs = json.load(f)
            return configs.get(site, {})
    return {}

def get_rest_api_auth(config: dict) -> tuple[str, requests.auth.HTTPBasicAuth]:
    """Get REST API authentication details."""
    username = config.get('rest_api', {}).get('username')
    app_password = config.get('rest_api', {}).get('app_password')
    site_url = config.get('rest_api', {}).get('site_url') or config.get('site_url')

    if not all([username, app_password, site_url]):
        raise ValueError(f"Missing auth config for site")

    auth = HTTPBasicAuth(username, app_password)
    api_base = f"{site_url}/wp-json/wp/v2"

    return api_base, auth

def create_post(site: str, title: str, content: str, status: str = 'draft', excerpt: str = None, tags: list = None) -> bool:
    """Create a WordPress post via REST API."""
    config = get_site_config(site)
    if not config:
        print(f"❌ No config found for site: {site}")
        return False

    try:
        api_base, auth = get_rest_api_auth(config)
    except ValueError as e:
        print(f"❌ {e}")
        return False

    # Prepare post data
    post_data = {
        'title': title,
        'content': content,
        'status': status
    }

    if excerpt:
        post_data['excerpt'] = excerpt

    # WordPress API expects tag IDs, not names. For now, we'll skip tags to avoid complexity
    # if tags:
    #     post_data['tags'] = ','.join(tags)

    # Make API request
    api_url = f"{api_base}/posts"

    try:
        response = requests.post(api_url, json=post_data, auth=auth, timeout=30)
        if response.status_code in [200, 201]:
            data = response.json()
            print(f"✅ Created post: {data.get('title', {}).get('rendered', title)} (ID: {data.get('id')})")
            return True
        else:
            print(f"❌ Failed to create post: HTTP {response.status_code} - {response.text[:200]}")
            return False
    except Exception as e:
        print(f"❌ Error creating post: {e}")
        return False


def parse_idea_lab_notes(md_file: Path) -> list[dict[str, Any]]:
    """Parse IDEA_LAB_NOTES.md and extract ideas."""
    if not md_file.exists():
        print(f"❌ File not found: {md_file}")
        return []
    
    with open(md_file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    ideas = []
    current_category = None
    current_tags = []
    
    # Parse sections
    lines = content.split('\n')
    i = 0
    while i < len(lines):
        line = lines[i].strip()
        
        # Category headers (### 1. Repository Analysis Series)
        if line.startswith('### ') and not line.startswith('####'):
            current_category = line.replace('### ', '').split('**')[0].strip()
            # Extract tags if present (look on same line or next line)
            tags_match = re.search(r'\*\*Tag:\*\*\s*`([^`]+)`', line)
            if not tags_match and i + 1 < len(lines):
                tags_match = re.search(r'\*\*Tag:\*\*\s*`([^`]+)`', lines[i + 1])
            current_tags = tags_match.group(1).split(', ') if tags_match else []
        
        # Individual ideas (bullets with "Idea:")
        elif line.startswith('- **') and 'Idea:' in line:
            idea_text = re.search(r'Idea:\s*"([^"]+)"', line)
            if idea_text:
                idea_title = idea_text.group(1)
                # Look for tags on same or next few lines
                idea_tags = current_tags.copy()
                for j in range(i, min(i+5, len(lines))):
                    tags_match = re.search(r'Tags:\s*`([^`]+)`', lines[j])
                    if tags_match:
                        idea_tags = tags_match.group(1).split(', ')
                        break
                
                ideas.append({
                    'title': idea_title,
                    'category': current_category or 'Uncategorized',
                    'tags': idea_tags,
                    'type': 'note'
                })
        
        # Repository-specific ideas (#### Agent & AI Systems)
        elif line.startswith('#### '):
            section_name = line.replace('#### ', '').strip()
            # Look ahead for repository entries
            j = i + 1
            while j < len(lines) and not lines[j].strip().startswith('####'):
                repo_line = lines[j].strip()
                if repo_line.startswith('- **') and not 'Idea:' in repo_line:
                    repo_match = re.search(r'\*\*([^*]+)\*\*', repo_line)
                    if repo_match:
                        repo_name = repo_match.group(1)
                        # Look for ideas under this repo
                        k = j + 1
                        while k < len(lines) and k < j + 15:
                            if 'Idea:' in lines[k]:
                                idea_match = re.search(r'Idea:\s*"([^"]+)"', lines[k])
                                if idea_match:
                                    tags_match = re.search(r'Tags:\s*`([^`]+)`', '\n'.join(lines[k:k+3]))
                                    tags = tags_match.group(1).split(', ') if tags_match else current_tags.copy()
                                    ideas.append({
                                        'title': idea_match.group(1),
                                        'category': f"{section_name}: {repo_name}",
                                        'tags': tags,
                                        'type': 'note'
                                    })
                            k += 1
                j += 1
        
        # Pattern-based ideas
        elif line.startswith('- **Idea:**') or (line.startswith('- ') and '"' in line and not line.startswith('- **')):
            idea_match = re.search(r'Idea:\s*"([^"]+)"', line)
            if not idea_match:
                idea_match = re.search(r'"([^"]+)"', line)
            if idea_match:
                tags_match = re.search(r'Tags:\s*`([^`]+)`', '\n'.join(lines[i:i+3]))
                tags = tags_match.group(1).split(', ') if tags_match else current_tags.copy()
                ideas.append({
                    'title': idea_match.group(1),
                    'category': current_category or 'Pattern-Based',
                    'tags': tags,
                    'type': 'note'
                })
        
        i += 1
    
    return ideas


def create_idea_note(site: str, idea: dict[str, Any], dry_run: bool = False) -> bool:
    """Create a note in WordPress Idea Lab using WP-CLI."""
    if dry_run:
        print(f"  Would create: {idea['title']}")
        print(f"    Category: {idea['category']}")
        print(f"    Tags: {', '.join(idea.get('tags', []))}")
        return True

    # Build content
    content = f"""<p><strong>Category:</strong> {idea['category']}</p>
<p>{idea['title']}</p>"""

    if idea.get('tags'):
        tags_str = ', '.join(idea['tags'])
        content += f'\n<p><strong>Tags:</strong> {tags_str}</p>'

    # Escape content for shell
    escaped_title = idea['title'].replace("'", "'\\''")
    escaped_content = content.replace("'", "'\\''")

    # Use WP-CLI via SSH to create the post
    import paramiko
    import os

    # Load SSH credentials
    env_path = 'D:/Agent_Cellphone_V2_Repository/.env'
    if os.path.exists(env_path):
        from dotenv import load_dotenv
        load_dotenv(env_path)

    creds = {
        'host': os.getenv('HOSTINGER_HOST'),
        'username': os.getenv('HOSTINGER_USER'),
        'password': os.getenv('HOSTINGER_PASS'),
        'port': int(os.getenv('HOSTINGER_PORT', '65002'))
    }

    if not all(creds.values()):
        print(f"❌ SSH credentials not found for {site}")
        return False

    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], password=creds['password'])

        wp_path = f'/home/{creds["username"]}/domains/{site}/public_html'
        excerpt_text = f"Idea Lab: {idea['category']}"
        escaped_excerpt = excerpt_text.replace("'", "'\\''")
        command = f"cd {wp_path} && wp post create --post_title='{escaped_title}' --post_content='{escaped_content}' --post_status=draft --post_excerpt='{escaped_excerpt}' --allow-root"

        stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()

        if "Success:" in output or "Created post" in output:
            print(f"✅ Created note: {idea['title']}")
            return True
        else:
            print(f"❌ Failed to create note: {error or output}")
            return False

    except Exception as e:
        print(f"❌ SSH error: {e}")
        return False
    finally:
        try:
            ssh.close()
        except:
            pass


def main() -> int:
    parser = argparse.ArgumentParser(description='Import Idea Lab notes from IDEA_LAB_NOTES.md')
    parser.add_argument(
        '--file',
        type=Path,
        default=Path('/home/dream/Development/projects/repositories/IDEA_LAB_NOTES.md'),
        help='Path to IDEA_LAB_NOTES.md file'
    )
    parser.add_argument(
        '--site',
        default='dadudekc.com',
        help='WordPress site to import to'
    )
    parser.add_argument(
        '--dry-run',
        action='store_true',
        help='Show what would be created without actually creating'
    )
    parser.add_argument(
        '--limit',
        type=int,
        help='Limit number of ideas to import'
    )
    
    args = parser.parse_args()
    
    print(f"📖 Parsing {args.file}...")
    ideas = parse_idea_lab_notes(args.file)
    
    if not ideas:
        print("❌ No ideas found in file")
        return 1
    
    print(f"✅ Found {len(ideas)} ideas")
    
    if args.limit:
        ideas = ideas[:args.limit]
        print(f"📝 Limiting to {len(ideas)} ideas")
    
    if args.dry_run:
        print("\n🔍 DRY RUN - Would create the following:")
    else:
        print(f"\n📤 Importing to {args.site}...")
    
    success = 0
    failed = 0
    
    for i, idea in enumerate(ideas, 1):
        print(f"\n[{i}/{len(ideas)}] {idea['title']}")
        if create_idea_note(args.site, idea, dry_run=args.dry_run):
            success += 1
        else:
            failed += 1
    
    print(f"\n{'='*60}")
    print(f"✅ Success: {success}")
    print(f"❌ Failed: {failed}")
    print(f"📊 Total: {len(ideas)}")
    
    return 0 if failed == 0 else 1


if __name__ == '__main__':
    sys.exit(main())

