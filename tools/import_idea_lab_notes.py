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

from tools.blog_manager import get_site_config, create_post, get_rest_api_auth
import requests
from requests.auth import HTTPBasicAuth


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
    """Create a note in WordPress Idea Lab."""
    if dry_run:
        print(f"  Would create: {idea['title']}")
        print(f"    Category: {idea['category']}")
        print(f"    Tags: {', '.join(idea.get('tags', []))}")
        return True
    
    config = get_site_config(site)
    
    # Build content
    content = f"""<p><strong>Category:</strong> {idea['category']}</p>
<p>{idea['title']}</p>"""
    
    if idea.get('tags'):
        tags_str = ', '.join(idea['tags'])
        content += f'\n<p><strong>Tags:</strong> {tags_str}</p>'
    
    try:
        # Try to create as custom post type 'note' first
        base_url, auth = get_rest_api_auth(config)
        
        # Try custom post type endpoint
        payload = {
            "title": idea['title'],
            "content": content,
            "status": "draft",
        }
        
        # Try note post type endpoint
        resp = requests.post(
            f"{base_url}/wp-json/wp/v2/note",
            auth=auth,
            json=payload,
            timeout=30
        )
        
        if resp.status_code in (200, 201):
            print(f"✅ Created note: {idea['title']}")
            return True
        elif resp.status_code == 404:
            # Note post type not available, fall back to regular posts
            print(f"  Note: Using regular posts (note post type not available)")
            create_post(
                site=site,
                title=idea['title'],
                content=content,
                status='draft',
                excerpt=f"Idea Lab: {idea['category']}",
                tags=idea.get('tags', [])
            )
            return True
        else:
            print(f"⚠️  Note endpoint returned {resp.status_code}, trying regular posts...")
            create_post(
                site=site,
                title=idea['title'],
                content=content,
                status='draft',
                excerpt=f"Idea Lab: {idea['category']}",
                tags=idea.get('tags', [])
            )
            return True
    except Exception as e:
        print(f"❌ Error creating {idea['title']}: {e}")
        # Fallback to regular post creation
        try:
            create_post(
                site=site,
                title=idea['title'],
                content=content,
                status='draft',
                excerpt=f"Idea Lab: {idea['category']}",
                tags=idea.get('tags', [])
            )
            return True
        except:
            return False


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

