#!/usr/bin/env python3
"""
Analyze Idea Lab Duplicates
==========================

Quick analysis of duplicate notes in dadudekc.com Idea Lab
without performing cleanup.

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


def get_duplicate_analysis(site_domain: str) -> dict:
    """Get comprehensive duplicate analysis."""
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

        # Filter for Idea Lab posts
        idea_lab_posts = []
        regular_posts = []

        for post in all_posts:
            if post.get('post_title', '').startswith(('How I Built', 'Business Intelligence', 'AI Workflow', 'BI Showcase')) or \
               'AI' in post.get('post_title', '') or 'Intelligence' in post.get('post_title', ''):
                idea_lab_posts.append(post)
            else:
                regular_posts.append(post)

        print(f"📊 Total posts: {len(all_posts)}")
        print(f"📝 Idea Lab posts: {len(idea_lab_posts)}")
        print(f"📄 Regular posts: {len(regular_posts)}")

        # Analyze Idea Lab duplicates
        title_groups = defaultdict(list)
        for post in idea_lab_posts:
            title = post['post_title'].strip().lower()
            title_groups[title].append(post)

        duplicates = {title: posts for title, posts in title_groups.items() if len(posts) > 1}

        print(f"\n🔍 Duplicate Analysis:")
        print(f"   Unique titles: {len(title_groups)}")
        print(f"   Titles with duplicates: {len(duplicates)}")

        total_duplicates = sum(len(posts) - 1 for posts in duplicates.values())
        print(f"   Total duplicate posts: {total_duplicates}")

        # Show worst offenders
        print(f"\n📋 Top Duplicates:")
        sorted_duplicates = sorted(duplicates.items(), key=lambda x: len(x[1]), reverse=True)
        for title, posts in sorted_duplicates[:10]:
            print(f"   • '{title[:50]}{'...' if len(title) > 50 else ''}' - {len(posts)} copies")

        return {
            'total_posts': len(all_posts),
            'idea_lab_posts': len(idea_lab_posts),
            'regular_posts': len(regular_posts),
            'unique_titles': len(title_groups),
            'duplicate_groups': len(duplicates),
            'total_duplicates': total_duplicates,
            'top_duplicates': sorted_duplicates[:10]
        }

    except json.JSONDecodeError as e:
        print(f"❌ Failed to parse JSON: {e}")
        return {}


def main():
    """Main execution."""
    site_domain = "dadudekc.com"

    print("📊 IDEA LAB DUPLICATE ANALYSIS")
    print("=" * 40)

    analysis = get_duplicate_analysis(site_domain)

    if not analysis:
        return 1

    print(f"\n✅ Analysis Complete")
    print(f"   Idea Lab now has {analysis['idea_lab_posts'] - analysis['total_duplicates']} unique notes")
    print(f"   {analysis['total_duplicates']} duplicates identified for cleanup")

    return 0


if __name__ == '__main__':
    exit(main())