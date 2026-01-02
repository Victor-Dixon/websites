#!/usr/bin/env python3
"""
Verify Duplicate Content in Idea Lab
====================================

Manually checks the actual content of posts flagged as duplicates
to ensure they are truly duplicates vs. different content with similar titles.

Author: Agent-2 (Architecture & Design Specialist)
Date: 2026-01-02
"""

import os
import sys
from collections import defaultdict
import hashlib

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


def get_idea_lab_posts_detailed(site_domain: str) -> list[dict]:
    """Get Idea Lab posts with full content for analysis."""
    # Get all posts with content
    success, output, error = run_wp_cli_command(
        site_domain,
        "wp post list --post_type=post --posts_per_page=-1 --format=json --fields=ID,post_title,post_content,post_date"
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
            if any(keyword in title for keyword in [
                'ai', 'intelligence', 'debugging', 'testing', 'wordpress',
                'agent', 'automation', 'system', 'architecture', 'content'
            ]):
                idea_lab_posts.append(post)

        print(f"📊 Found {len(idea_lab_posts)} Idea Lab posts")
        return idea_lab_posts

    except json.JSONDecodeError as e:
        print(f"❌ Failed to parse JSON: {e}")
        return []


def analyze_content_similarity(posts: list[dict]) -> dict:
    """Analyze content similarity to identify true duplicates vs. similar titles."""
    # Group by title
    title_groups = defaultdict(list)
    for post in posts:
        title = post['post_title'].strip().lower()
        title_groups[title].append(post)

    analysis_results = {}

    for title, posts_in_group in title_groups.items():
        if len(posts_in_group) == 1:
            continue  # Skip singles

        print(f"\n🔍 Analyzing '{title}' - {len(posts_in_group)} posts:")

        # Check content similarity
        contents = []
        content_hashes = []

        for post in posts_in_group:
            content = post.get('post_content', '').strip()
            contents.append(content)

            # Create hash of content for comparison
            content_hash = hashlib.md5(content.encode()).hexdigest()
            content_hashes.append(content_hash)

        # Check if all content is identical
        all_identical = len(set(content_hashes)) == 1

        # Check content lengths
        content_lengths = [len(content) for content in contents]
        length_variance = max(content_lengths) - min(content_lengths)

        # Analyze content differences
        if all_identical:
            duplicate_type = "EXACT_DUPLICATES"
            print("   ✅ EXACT DUPLICATES - All content identical")
        elif length_variance < 100:  # Very similar lengths
            duplicate_type = "NEAR_DUPLICATES"
            print(f"   ⚠️  NEAR DUPLICATES - Similar lengths (±{length_variance} chars)")
            # Show first few differences
            for i, content in enumerate(contents[:3]):
                preview = content[:200] + "..." if len(content) > 200 else content
                print(f"      Post {posts_in_group[i]['ID']}: {preview}")
        else:
            duplicate_type = "DIFFERENT_CONTENT"
            print(f"   ❌ DIFFERENT CONTENT - Length variance: {length_variance} chars")
            for i, post in enumerate(posts_in_group):
                print(f"      Post {post['ID']}: {len(post.get('post_content', ''))} chars - {post.get('post_date', '')[:10]}")

        analysis_results[title] = {
            'count': len(posts_in_group),
            'duplicate_type': duplicate_type,
            'content_lengths': content_lengths,
            'post_ids': [post['ID'] for post in posts_in_group],
            'post_dates': [post.get('post_date', '')[:10] for post in posts_in_group]
        }

    return analysis_results


def main():
    """Main execution."""
    site_domain = "dadudekc.com"

    print("🔬 VERIFYING IDEA LAB DUPLICATE CONTENT")
    print("=" * 50)

    # Get detailed post data
    print("📋 Fetching Idea Lab posts with content...")
    posts = get_idea_lab_posts_detailed(site_domain)

    if not posts:
        print("❌ No Idea Lab posts found")
        return 1

    # Analyze content similarity
    print("\n🔍 Analyzing content similarity...")
    analysis = analyze_content_similarity(posts)

    # Summarize findings
    exact_duplicates = 0
    near_duplicates = 0
    different_content = 0
    total_to_remove = 0

    print(f"\n📊 ANALYSIS SUMMARY")
    print("=" * 50)

    for title, data in sorted(analysis.items(), key=lambda x: x[1]['count'], reverse=True):
        count = data['count']
        dup_type = data['duplicate_type']

        if dup_type == "EXACT_DUPLICATES":
            exact_duplicates += 1
            total_to_remove += (count - 1)
            status = "✅ EXACT - Remove all but 1"
        elif dup_type == "NEAR_DUPLICATES":
            near_duplicates += 1
            status = "⚠️  NEAR - Manual review needed"
        else:
            different_content += 1
            status = "❌ DIFFERENT - Keep all"

        print(f"{status} | '{title[:50]}{'...' if len(title) > 50 else ''}' | {count} posts | IDs: {data['post_ids']}")

    print(f"\n🎯 FINAL RECOMMENDATIONS")
    print("=" * 50)
    print(f"✅ Exact duplicates: {exact_duplicates} groups ({total_to_remove} posts to remove)")
    print(f"⚠️  Near duplicates: {near_duplicates} groups (manual review needed)")
    print(f"❌ Different content: {different_content} groups (keep all)")

    if total_to_remove > 0:
        print(f"\n🧹 SAFE TO AUTO-REMOVE: {total_to_remove} posts from exact duplicate groups")
        print("   These are truly identical content that can be safely deleted.")

    if near_duplicates > 0:
        print(f"\n🔍 MANUAL REVIEW NEEDED: {near_duplicates} groups")
        print("   These have similar titles but potentially different content.")

    return 0


if __name__ == '__main__':
    exit(main())