#!/usr/bin/env python3
"""
Targeted Cleanup of Idea Lab Duplicates
======================================

Efficiently removes the 56 duplicate posts identified in the Idea Lab,
keeping only one copy of each unique idea.

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


def get_idea_lab_duplicates(site_domain: str) -> dict[str, list[dict]]:
    """Get only Idea Lab duplicates."""
    # Get all posts
    success, output, error = run_wp_cli_command(
        site_domain,
        "wp post list --post_type=post --posts_per_page=-1 --format=json --fields=ID,post_title,post_date"
    )

    if not success:
        print(f"❌ Failed to get posts: {error}")
        return {}

    try:
        import json
        all_posts = json.loads(output)

        # Filter for Idea Lab posts (those with AI/Intelligence related titles)
        idea_lab_posts = []
        for post in all_posts:
            title = post.get('post_title', '').lower()
            if any(keyword in title for keyword in [
                'ai', 'intelligence', 'debugging', 'testing', 'wordpress',
                'agent', 'automation', 'system', 'architecture', 'content'
            ]):
                idea_lab_posts.append(post)

        # Group by title
        title_groups = defaultdict(list)
        for post in idea_lab_posts:
            title = post['post_title'].strip().lower()
            title_groups[title].append(post)

        # Return only duplicates
        duplicates = {title: posts for title, posts in title_groups.items() if len(posts) > 1}
        return duplicates

    except json.JSONDecodeError as e:
        print(f"❌ Failed to parse JSON: {e}")
        return {}


def cleanup_duplicates_batch(site_domain: str, duplicates: dict[str, list[dict]], batch_size: int = 10) -> int:
    """Clean up duplicates in batches to avoid timeouts."""
    removed_count = 0
    all_posts_to_remove = []

    # Collect all duplicate posts to remove (keep oldest of each group)
    for title, posts in duplicates.items():
        # Sort by date to keep oldest
        posts.sort(key=lambda x: x['post_date'])
        # Keep first (oldest), remove rest
        all_posts_to_remove.extend(posts[1:])

    print(f"🗑️  Total posts to remove: {len(all_posts_to_remove)}")

    # Process in batches
    for i in range(0, len(all_posts_to_remove), batch_size):
        batch = all_posts_to_remove[i:i+batch_size]
        print(f"\n📦 Processing batch {i//batch_size + 1}/{(len(all_posts_to_remove)-1)//batch_size + 1} ({len(batch)} posts)")

        for post in batch:
            post_id = post['ID']
            title = post['post_title'][:40] + ('...' if len(post['post_title']) > 40 else '')

            success, output, error = run_wp_cli_command(
                site_domain,
                f"wp post delete {post_id} --force"
            )

            if success and ("Success:" in output or "Deleted" in output):
                print(f"   ✅ Deleted: {title} (ID: {post_id})")
                removed_count += 1
            else:
                print(f"   ❌ Failed: {title} (ID: {post_id}) - {error}")

    return removed_count


def main():
    """Main execution."""
    site_domain = "dadudekc.com"

    print("🎯 TARGETED IDEA LAB DUPLICATE CLEANUP")
    print("=" * 50)

    # Get duplicates
    print("🔍 Finding Idea Lab duplicates...")
    duplicates = get_idea_lab_duplicates(site_domain)

    if not duplicates:
        print("✅ No duplicates found!")
        return 0

    total_duplicates = sum(len(posts) - 1 for posts in duplicates.values())
    unique_titles = len(duplicates)

    print(f"📊 Found {unique_titles} titles with duplicates")
    print(f"🗑️  {total_duplicates} duplicate posts to remove")

    # Show what will be cleaned
    print(f"\n📋 Cleanup Plan:")
    for title, posts in sorted(duplicates.items(), key=lambda x: len(x[1]), reverse=True):
        keep_date = min(post['post_date'] for post in posts)
        print(f"   • '{title[:50]}{'...' if len(title) > 50 else ''}' - Keep 1, Remove {len(posts)-1} (oldest: {keep_date[:10]})")

    # Proceed with cleanup
    print(f"\n🧹 Starting cleanup...")
    removed_count = cleanup_duplicates_batch(site_domain, duplicates, batch_size=5)

    print(f"\n🎉 Cleanup Complete!")
    print(f"✅ Removed {removed_count} duplicate posts")
    print(f"📊 Idea Lab now has {unique_titles} unique notes")

    # Final verification
    final_duplicates = get_idea_lab_duplicates(site_domain)
    if final_duplicates:
        remaining = sum(len(posts) - 1 for posts in final_duplicates.values())
        print(f"⚠️  {remaining} duplicates still remain - may need another pass")
    else:
        print(f"✅ All duplicates successfully removed!")

    return 0 if not final_duplicates else 1


if __name__ == '__main__':
    exit(main())