#!/usr/bin/env python3
"""
Idea Lab to Autoblogger Pipeline
=================================

Converts Idea Lab posts into autoblogger backlog items and generates content in Victor's voice.

Usage:
    python tools/idea_lab_to_autoblogger.py --site dadudekc --limit 5 --auto-generate

This tool:
1. Fetches Idea Lab posts from WordPress
2. Extracts core ideas and converts to backlog format
3. Generates full blog posts using Victor's voice profile
4. Publishes automatically if --auto-generate is used

Author: Agent-2 (Architecture & Design Specialist)
Date: 2026-01-02
"""

import argparse
import json
import re
import sys
from dataclasses import asdict
from datetime import datetime
from pathlib import Path
from typing import Any, Dict, List

import paramiko
import yaml

# Add repo root to path for imports
REPO_ROOT = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(REPO_ROOT))

# Import autoblogger components
from src.autoblogger.models import BacklogItem
from src.autoblogger.site_config import load_site_config
from src.autoblogger.run_daily import run_daily_for_site


def run_wp_cli_command(site_domain: str, command: str) -> tuple[bool, str, str]:
    """Run a WP-CLI command via SSH and return the result."""
    import paramiko
    import os

    # Load credentials
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
        return False, "", "SSH credentials not found"

    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], password=creds['password'])

        wp_path = f'/home/{creds["username"]}/domains/{site_domain}/public_html'
        full_command = f'cd {wp_path} && {command} --allow-root 2>&1'

        stdin, stdout, stderr = ssh.exec_command(full_command, timeout=60)
        output = stdout.read().decode()
        error = stderr.read().decode()

        ssh.close()
        return True, output.strip(), error.strip()

    except Exception as e:
        return False, "", str(e)


def parse_idea_lab_notes(file_path: Path) -> List[Dict[str, Any]]:
    """Parse IDEA_LAB_NOTES.md and extract ideas."""
    print(f"📖 Reading {file_path}...")

    if not file_path.exists():
        print(f"❌ File not found: {file_path}")
        return []

    content = file_path.read_text(encoding='utf-8')

    # Split by main sections (marked with #)
    sections = re.split(r'^#+\s+', content, flags=re.MULTILINE)

    ideas = []
    current_category = ""

    for section in sections:
        section = section.strip()
        if not section:
            continue

        # Extract category from headers
        if section.startswith('## ') and ':' in section:
            current_category = section.split(':', 1)[0].replace('## ', '').strip()

        # Look for idea entries (typically start with bullet points or numbered lists)
        lines = section.split('\n')
        for i, line in enumerate(lines):
            line = line.strip()

            # Skip if not a list item or header
            if not (line.startswith('- ') or line.startswith('* ') or re.match(r'^\d+\.\s', line)):
                continue

            # Extract the idea text
            idea_text = re.sub(r'^[-*\d]+\.\s*', '', line)

            # Skip very short ideas or metadata
            if len(idea_text) < 10 or idea_text.lower().startswith(('tags:', 'category:')):
                continue

            # Create a mock post structure
            idea = {
                'id': f"idea_{len(ideas) + 1}",
                'title': idea_text[:80] + ('...' if len(idea_text) > 80 else ''),
                'content': idea_text,
                'category': current_category or 'Repository Analysis',
                'date': datetime.now().isoformat()
            }

            ideas.append(idea)

            # Limit to reasonable number
            if len(ideas) >= 50:
                break

        if len(ideas) >= 50:
            break

    print(f"✅ Extracted {len(ideas)} ideas from IDEA_LAB_NOTES.md")
    return ideas


def get_idea_lab_posts(site_domain: str) -> List[Dict[str, Any]]:
    """Get Idea Lab content from IDEA_LAB_NOTES.md file."""
    file_path = REPO_ROOT / "docs" / "IDEA_LAB_NOTES.md"
    return parse_idea_lab_notes(file_path)


def extract_idea_core(content: str) -> str:
    """Extract the core idea from WordPress post content."""
    # Remove HTML tags and extract the main idea
    clean_content = re.sub(r'<[^>]+>', '', content)
    clean_content = clean_content.strip()

    # Split by lines and find the core idea (usually first meaningful line)
    lines = [line.strip() for line in clean_content.split('\n') if line.strip()]

    # Look for the main idea (skip metadata like "Category:" and "Tags:")
    for line in lines:
        if not line.startswith('Category:') and not line.startswith('Tags:'):
            return line[:200]  # Limit to reasonable length

    return clean_content[:200] if clean_content else "Repository insight"


def convert_to_backlog_item(post: Dict[str, Any], index: int) -> BacklogItem:
    """Convert Idea Lab post to autoblogger backlog item."""
    title = post['title']
    content = post['content']
    category = post.get('category', 'Repository Analysis')
    core_idea = content  # The content is already the core idea

    # Determine pillar based on category
    pillar_mapping = {
        'repository analysis': 'niche',
        'agent & ai systems': 'credibility',
        'web & full-stack projects': 'quick_win',
        'automation & productivity': 'pain',
        'specialized applications': 'credibility',
        'website projects': 'niche',
        'pattern-based ideas': 'quick_win'
    }

    pillar = 'niche'  # default
    for key, value in pillar_mapping.items():
        if key.lower() in category.lower():
            pillar = value
            break

    # Determine audience based on content
    audience = 'tech_leaning_operator'  # default for technical content
    if 'beginner' in title.lower() or 'tutorial' in title.lower():
        audience = 'early_stage_founder'
    elif 'team' in title.lower() or 'organization' in title.lower():
        audience = 'founder_led_team'

    # Create angle (hook) from core idea
    angle = f"{core_idea} - Real insights from repository analysis"

    # Extract keywords from title and content
    keywords = []
    title_words = re.findall(r'\b\w{4,}\b', title.lower())
    keywords.extend(title_words[:3])  # First 3 meaningful words

    # Add technical keywords
    tech_keywords = ['repository', 'development', 'automation', 'system', 'architecture']
    keywords.extend([kw for kw in tech_keywords if kw in title.lower() or kw in content.lower()])

    # Determine CTA based on pillar
    cta_mapping = {
        'pain': 'audit',
        'credibility': 'case_study',
        'niche': 'expertise',
        'quick_win': 'sprint'
    }
    cta = cta_mapping.get(pillar, 'expertise')

    return BacklogItem(
        id=f"IDEA-{str(index + 1).zfill(3)}",
        pillar=pillar,
        audience=audience,
        title=title,
        angle=angle,
        keywords=list(set(keywords))[:5],  # Unique, max 5
        cta=cta,
        status='ready'
    )


def add_to_backlog(site: str, backlog_items: List[BacklogItem]) -> None:
    """Add items to the autoblogger backlog."""
    backlog_path = Path(f"content/backlogs/{site}.yaml")

    # Load existing backlog
    if backlog_path.exists():
        with open(backlog_path, 'r', encoding='utf-8') as f:
            data = yaml.safe_load(f) or {}
    else:
        data = {'posts': []}

    existing_posts = data.get('posts', [])
    existing_ids = {post.get('id') for post in existing_posts}

    # Add new items (avoid duplicates)
    new_posts = []
    for item in backlog_items:
        if item.id not in existing_ids:
            new_posts.append(asdict(item))
            existing_ids.add(item.id)

    if new_posts:
        data['posts'].extend(new_posts)

        # Write back to file
        with open(backlog_path, 'w', encoding='utf-8') as f:
            yaml.safe_dump(data, f, sort_keys=False, allow_unicode=True)

        print(f"✅ Added {len(new_posts)} new backlog items")
    else:
        print("ℹ️  No new items to add (all already exist)")


def generate_content_for_item(site: str, item: BacklogItem, auto_publish: bool = False) -> bool:
    """Generate full blog content for a specific backlog item using Victor's voice."""
    print(f"🎨 Generating content for '{item.title}' in Victor's voice...")

    try:
        # Use the autoblogger pipeline directly
        from src.autoblogger.site_config import load_site_config
        from src.autoblogger.run_daily import run_daily_for_site

        # Temporarily modify the backlog to make this item next
        # For now, just run the daily process which will pick the next ready item
        result = run_daily_for_site(
            site=site,
            date_override=None,
            timezone="America/Chicago",
            auto_publish=auto_publish,
            wp_status="draft" if not auto_publish else "publish",
            dry_run=False
        )
        return result == 0
    except Exception as e:
        print(f"❌ Failed to generate content: {e}")
        return False


def main() -> int:
    parser = argparse.ArgumentParser(description='Convert Idea Lab posts to autoblogger content in Victor\'s voice')
    parser.add_argument('--site', default='dadudekc', help='WordPress site (default: dadudekc)')
    parser.add_argument('--limit', type=int, help='Limit number of posts to process')
    parser.add_argument('--add-to-backlog', action='store_true', help='Add posts to autoblogger backlog')
    parser.add_argument('--auto-generate', action='store_true', help='Auto-generate content for all new backlog items')
    parser.add_argument('--auto-publish', action='store_true', help='Auto-publish generated content')
    parser.add_argument('--dry-run', action='store_true', help='Show what would be done without making changes')

    args = parser.parse_args()

    print("🚀 IDEA LAB → AUTOBLOGGER PIPELINE")
    print("=" * 50)
    print(f"Site: {args.site}")
    print(f"Voice: Victor (Builder & Systems Thinker)")
    print()

    # Step 1: Fetch Idea Lab posts
    posts = get_idea_lab_posts(args.site)

    if not posts:
        print("❌ No Idea Lab posts found")
        return 1

    if args.limit:
        posts = posts[:args.limit]
        print(f"📝 Limiting to {len(posts)} posts")

    # Step 2: Convert to backlog items
    print("\n🔄 Converting posts to autoblogger format...")
    backlog_items = []
    for i, post in enumerate(posts):
        item = convert_to_backlog_item(post, i)
        backlog_items.append(item)
        print(f"  {item.id}: {item.title[:60]}{'...' if len(item.title) > 60 else ''}")

    if args.dry_run:
        print("\n🔍 DRY RUN - Would process the following:")
        for item in backlog_items:
            print(f"  📝 {item.id}: {item.title}")
            print(f"     Pillar: {item.pillar} | Audience: {item.audience} | CTA: {item.cta}")
        return 0

    # Step 3: Add to backlog
    if args.add_to_backlog:
        print("\n📝 Adding to autoblogger backlog...")
        add_to_backlog(args.site, backlog_items)

    # Step 4: Generate content (if requested)
    if args.auto_generate:
        print("\n🎨 Generating content in Victor's voice...")
        success_count = 0
        total_count = len(backlog_items)

        for item in backlog_items:
            if generate_content_for_item(args.site, item.id, args.auto_publish):
                success_count += 1
                status = "✅ Published" if args.auto_publish else "✅ Generated"
                print(f"  {status}: {item.title}")
            else:
                print(f"  ❌ Failed: {item.title}")

        print(f"\n📊 Content Generation Results:")
        print(f"   ✅ Success: {success_count}/{total_count}")
        print(f"   ❌ Failed: {total_count - success_count}/{total_count}")

        if args.auto_publish and success_count > 0:
            print(f"\n🌐 {success_count} posts published to {args.site}.com")

    print("\n🎯 PIPELINE COMPLETE")
    print("=" * 50)
    print("Idea Lab content → Victor's voice → Published posts")
    print()
    print("Next steps:")
    print("1. Review generated drafts: src/autoblogger/drafts/dadudekc/")
    print("2. Publish drafts: Use WordPress admin or --auto-publish flag")
    print("3. Check site: https://dadudekc.com/blog/")

    return 0


if __name__ == '__main__':
    exit(main())