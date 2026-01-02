#!/usr/bin/env python3
"""
Rename Idea Lab Posts to Eliminate Apparent Duplicates
======================================================

Renames posts with identical titles to distinguish them by context/category
while preserving all valuable content.

Author: Agent-2 (Architecture & Design Specialist)
Date: 2026-01-02
"""

import os
import sys
from collections import defaultdict

def run_wp_cli_command(site_domain: str, command: str) -> tuple[bool, str, str]:
    """Run a WP-CLI command via SSH and return the result."""
    import paramiko

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

        stdin, stdout, stderr = ssh.exec_command(full_command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()

        ssh.close()
        return True, output.strip(), error.strip()

    except Exception as e:
        return False, "", str(e)


def get_idea_lab_posts(site_domain: str) -> list[dict]:
    """Get Idea Lab posts with content and excerpts."""
    success, output, error = run_wp_cli_command(
        site_domain,
        "wp post list --post_type=post --posts_per_page=-1 --format=json --fields=ID,post_title,post_content,post_excerpt,post_date"
    )

    if not success:
        print(f"❌ Failed to get posts: {error}")
        return []

    try:
        import json
        all_posts = json.loads(output)

        # Filter for Idea Lab posts (those with AI/Intelligence related titles)
        idea_lab_posts = []
        for post in all_posts:
            title = post.get('post_title', '').lower()
            excerpt = post.get('post_excerpt', '')
            if any(keyword in title for keyword in [
                'ai', 'intelligence', 'debugging', 'testing', 'wordpress',
                'agent', 'automation', 'system', 'architecture', 'content'
            ]) or excerpt.startswith('Idea Lab:'):
                idea_lab_posts.append(post)

        print(f"📊 Found {len(idea_lab_posts)} Idea Lab posts")
        return idea_lab_posts

    except json.JSONDecodeError as e:
        print(f"❌ Failed to parse JSON: {e}")
        return []


def extract_context_from_excerpt(excerpt: str) -> str:
    """Extract context information from the excerpt."""
    if not excerpt or not excerpt.startswith('Idea Lab:'):
        return ""

    # Extract category from "Idea Lab: [Category]"
    category = excerpt.replace('Idea Lab:', '').strip()

    # Create readable context labels
    context_mapping = {
        'High-Value Repositories Identified:': 'General Overview',
        'Agent & AI Systems: Auto_Blogger': 'Auto_Blogger Project',
        'Agent & AI Systems: Agent_Cellphone_V2_Repository': 'Agent_Cellphone_V2',
        'Agent & AI Systems: AI_Debugger_Assistant': 'AI_Debugger_Assistant',
        'Web & Full-Stack Projects: basicbot': 'BasicBot Project',
        'Web & Full-Stack Projects: bolt-project': 'Bolt Project',
        'Automation & Productivity: contract-leads': 'Contract Leads System',
        'Specialized Applications: bible-application': 'Bible Application',
        'Specialized Applications: dreambank': 'Dreambank Project',
        'Website Projects: DaDudeKC-Website': 'DaDudeKC Website',
        'Pattern-Based Ideas': 'General Patterns'
    }

    for key, label in context_mapping.items():
        if key in category:
            return label

    return category[:30] + ('...' if len(category) > 30 else '')


def find_posts_to_rename(posts: list[dict]) -> dict[str, list[dict]]:
    """Find posts with identical titles that need renaming."""
    title_groups = defaultdict(list)

    for post in posts:
        title = post['post_title'].strip().lower()
        title_groups[title].append(post)

    # Return only groups with multiples (potential duplicates)
    duplicates = {title: posts for title, posts in title_groups.items() if len(posts) > 1}
    return duplicates


def rename_posts(site_domain: str, duplicates: dict[str, list[dict]]) -> int:
    """Rename posts to distinguish them by context."""
    renamed_count = 0

    for original_title_lower, posts in duplicates.items():
        print(f"\n📝 Processing '{original_title_lower}' - {len(posts)} posts:")

        # Sort by excerpt to group similar contexts together
        posts.sort(key=lambda x: x.get('post_excerpt', ''))

        for i, post in enumerate(posts):
            post_id = post['ID']
            current_title = post['post_title']
            excerpt = post.get('post_excerpt', '')
            context = extract_context_from_excerpt(excerpt)

            # Create new title
            if i == 0 and len(posts) > 1:
                # First post keeps original title but adds context if available
                if context:
                    new_title = f"{current_title} ({context})"
                else:
                    new_title = current_title
            elif context:
                # Subsequent posts get context in title
                new_title = f"{current_title} ({context})"
            else:
                # Fallback: add index
                new_title = f"{current_title} (#{i+1})"

            if new_title != current_title:
                print(f"   Renaming Post {post_id}:")
                print(f"      From: '{current_title}'")
                print(f"      To:   '{new_title}'")

                # Escape for shell
                escaped_title = new_title.replace("'", "'\\''")

                success, output, error = run_wp_cli_command(
                    site_domain,
                    f"wp post update {post_id} --post_title='{escaped_title}'"
                )

                if success and ("Success:" in output or "Updated" in output.lower()):
                    print(f"      ✅ Renamed successfully")
                    renamed_count += 1
                else:
                    print(f"      ❌ Failed to rename: {error}")
            else:
                print(f"   Keeping Post {post_id} title unchanged: '{current_title}'")

    return renamed_count


def main():
    """Main execution."""
    site_domain = "dadudekc.com"

    print("🏷️ RENAMING IDEA LAB POSTS TO ELIMINATE DUPLICATES")
    print("=" * 60)

    # Get posts
    print("📋 Fetching Idea Lab posts...")
    posts = get_idea_lab_posts(site_domain)

    if not posts:
        print("❌ No Idea Lab posts found")
        return 1

    # Find posts to rename
    print("\n🔍 Finding posts with identical titles...")
    duplicates = find_posts_to_rename(posts)

    if not duplicates:
        print("✅ No duplicate titles found!")
        return 0

    total_posts_to_rename = sum(len(posts) - 1 for posts in duplicates.values())  # Don't rename the first one in each group
    print(f"📊 Found {len(duplicates)} title groups with potential duplicates")
    print(f"🎯 Posts to rename: {total_posts_to_rename}")

    # Show what will be renamed
    print("\n📋 Rename Plan:")
    for title, posts in sorted(duplicates.items(), key=lambda x: len(x[1]), reverse=True):
        print(f"   • '{title[:50]}{'...' if len(title) > 50 else ''}' - Keep 1, Rename {len(posts)-1}")

    # Proceed with renaming
    print("\n🏷️ Starting renaming...")
    renamed_count = rename_posts(site_domain, duplicates)

    print("\n🎉 Renaming Complete!")
    print(f"✅ Renamed {renamed_count} posts")
    print(f"📊 All Idea Lab posts now have unique titles")

    # Final verification
    final_posts = get_idea_lab_posts(site_domain)
    final_duplicates = find_posts_to_rename(final_posts)

    if final_duplicates:
        remaining = len(final_duplicates)
        print(f"⚠️  {remaining} title groups still have duplicates - may need manual review")
    else:
        print(f"✅ All duplicates eliminated! Every post now has a unique title.")

    return 0 if not final_duplicates else 1


if __name__ == '__main__':
    exit(main())