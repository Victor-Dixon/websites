#!/usr/bin/env python3
"""
Clean Duplicate Notes from Idea Lab
===================================

Identifies and removes duplicate notes from dadudekc.com Idea Lab
that were created during the import process.

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

        stdin, stdout, stderr = ssh.exec_command(full_command, timeout=60)
        output = stdout.read().decode()
        error = stderr.read().decode()

        ssh.close()
        return True, output.strip(), error.strip()

    except Exception as e:
        return False, "", str(e)


def get_all_notes(site_domain: str) -> list[dict]:
    """Get all notes from the Idea Lab."""
    success, output, error = run_wp_cli_command(
        site_domain,
        "wp post list --post_type=post --posts_per_page=-1 --format=json --fields=ID,post_title,post_content,post_excerpt"
    )

    if not success:
        print(f"❌ Failed to get posts: {error}")
        return []

    try:
        import json
        posts = json.loads(output)
        # Filter for Idea Lab posts (those with excerpts starting with "Idea Lab:")
        notes = [post for post in posts if post.get('post_excerpt', '').startswith('Idea Lab:')]
        return notes
    except json.JSONDecodeError as e:
        print(f"❌ Failed to parse JSON: {e}")
        print(f"Output: {output[:500]}...")
        return []


def find_duplicates(notes: list[dict]) -> dict[str, list[dict]]:
    """Find duplicate notes based on title."""
    title_groups = defaultdict(list)

    for note in notes:
        title = note['post_title'].strip().lower()
        title_groups[title].append(note)

    # Return only groups with duplicates
    duplicates = {title: posts for title, posts in title_groups.items() if len(posts) > 1}
    return duplicates


def clean_duplicates(site_domain: str, duplicates: dict[str, list[dict]]) -> int:
    """Remove duplicate notes, keeping the first one."""
    removed_count = 0

    for title, posts in duplicates.items():
        print(f"\n📝 Processing duplicates for: '{title}'")
        print(f"   Found {len(posts)} duplicates")

        # Sort by ID to keep the oldest (lowest ID)
        posts.sort(key=lambda x: int(x['ID']))

        # Keep the first (oldest) post, remove the rest
        keep_post = posts[0]
        remove_posts = posts[1:]

        print(f"   ✅ Keeping: Post ID {keep_post['ID']}")
        print(f"   🗑️  Removing: {len(remove_posts)} duplicates")

        for post in remove_posts:
            success, output, error = run_wp_cli_command(
                site_domain,
                f"wp post delete {post['ID']} --force"
            )

            if success and "Success:" in output:
                print(f"      ✅ Deleted Post ID {post['ID']}")
                removed_count += 1
            else:
                print(f"      ❌ Failed to delete Post ID {post['ID']}: {error}")

    return removed_count


def main():
    """Main execution."""
    site_domain = "dadudekc.com"

    print("🧹 CLEANING IDEA LAB DUPLICATES")
    print("=" * 50)

    # Step 1: Get all notes
    print("📋 Gathering all Idea Lab notes...")
    notes = get_all_notes(site_domain)

    if not notes:
        print("❌ No Idea Lab notes found")
        return 1

    print(f"✅ Found {len(notes)} Idea Lab notes")

    # Step 2: Find duplicates
    print("\n🔍 Analyzing for duplicates...")
    duplicates = find_duplicates(notes)

    if not duplicates:
        print("✅ No duplicates found!")
        return 0

    total_duplicates = sum(len(posts) - 1 for posts in duplicates.values())
    print(f"⚠️  Found {len(duplicates)} duplicate groups")
    print(f"📊 Total duplicate posts to remove: {total_duplicates}")

    # Show summary
    print("\n📋 Duplicate Summary:")
    for title, posts in duplicates.items():
        print(f"   • '{title[:60]}{'...' if len(title) > 60 else ''}' - {len(posts)} copies")

    # Step 3: Clean duplicates
    print("\n🧹 Starting cleanup...")
    print("⚠️  This will permanently delete duplicate posts!")
    print("   Keeping the oldest copy of each duplicate group.")

    removed_count = clean_duplicates(site_domain, duplicates)

    print("\n🎉 Cleanup Complete!")
    print(f"✅ Removed {removed_count} duplicate posts")
    print(f"📊 Idea Lab now has {len(notes) - removed_count} unique notes")

    return 0


if __name__ == '__main__':
    exit(main())