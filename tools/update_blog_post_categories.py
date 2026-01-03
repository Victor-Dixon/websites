#!/usr/bin/env python3
"""
Update Blog Post Categories for Style Variations
===============================================

Assigns appropriate categories to existing blog posts so they use
the correct style variations instead of looking AI-generated.

Author: Agent-2 (Architecture & Design Specialist)
Date: 2026-01-02
"""

import os
import sys
import json
from pathlib import Path

# Add the autoblogger path for imports
sys.path.insert(0, str(Path(__file__).parent.parent / "src" / "autoblogger"))

try:
    from wp_publisher import load_wp_env, publish_wordpress_post
except ImportError:
    print("❌ Could not import WordPress publisher")
    sys.exit(1)


def get_existing_posts():
    """Get list of existing published posts."""
    posts = [
        {
            'id': 154,
            'title': 'How I Built an AI-Assisted Development Workflow After Cursor\'s $1-Per-Request Trap',
            'categories': ['technical', 'development', 'ai-assisted-development'],
            'style': 'technical'
        },
        {
            'id': 155,
            'title': 'Business Intelligence Showcase - Advanced Analytics & Automation',
            'categories': ['business-intelligence', 'showcase', 'analysis'],
            'style': 'magazine'
        }
    ]
    return posts


def update_post_categories(site_domain: str, post_id: int, categories: list) -> bool:
    """Update post categories via WP-CLI."""
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
        print("❌ SSH credentials not found")
        return False

    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], password=creds['password'])

        wp_path = f'/home/{creds["username"]}/domains/{site_domain}/public_html'

        # Get category IDs for the category names
        category_ids = []
        for category in categories:
            # Get category ID by slug
            cmd = f'cd {wp_path} && wp term get category {category} --field=term_id --allow-root 2>&1'
            stdin, stdout, stderr = ssh.exec_command(cmd)
            cat_id = stdout.read().decode().strip()
            if cat_id and cat_id.isdigit():
                category_ids.append(cat_id)
            else:
                # Create category if it doesn't exist
                create_cmd = f'cd {wp_path} && wp term create category {category} --allow-root --porcelain'
                stdin, stdout, stderr = ssh.exec_command(create_cmd)
                new_cat_id = stdout.read().decode().strip()
                if new_cat_id and new_cat_id.isdigit():
                    category_ids.append(new_cat_id)

        if category_ids:
            # Set post categories
            cat_string = ','.join(category_ids)
            set_cmd = f'cd {wp_path} && wp post term set {post_id} category {cat_string} --allow-root 2>&1'
            stdin, stdout, stderr = ssh.exec_command(set_cmd)
            result = stdout.read().decode() + stderr.read().decode()

            if "Success:" in result or "updated" in result.lower():
                print(f"✅ Updated post {post_id} categories: {categories}")
                return True
            else:
                print(f"❌ Failed to update post {post_id}: {result}")
                return False
        else:
            print(f"❌ No category IDs found for post {post_id}")
            return False

    except Exception as e:
        print(f"❌ SSH error: {e}")
        return False
    finally:
        try:
            ssh.close()
        except:
            pass

    return False


def main():
    """Main execution."""
    site_domain = "dadudekc.com"

    print("🎨 UPDATING BLOG POST CATEGORIES FOR STYLE VARIATIONS")
    print("=" * 60)

    posts = get_existing_posts()

    success_count = 0
    for post in posts:
        print(f"\n📝 Updating: {post['title'][:50]}...")
        print(f"   Style: {post['style']}")
        print(f"   Categories: {post['categories']}")

        if update_post_categories(site_domain, post['id'], post['categories']):
            success_count += 1
        else:
            print(f"❌ Failed to update post {post['id']}")

    print(f"\n{'═' * 60}")
    print("📊 UPDATE RESULTS")
    print(f"{'═' * 60}")
    print(f"✅ Successfully updated: {success_count}/{len(posts)} posts")

    if success_count == len(posts):
        print("\n🎉 All posts updated! Blog now has diverse styling:")
        print("   - Technical style: AI workflow post")
        print("   - Magazine style: Business Intelligence showcase")
        print("\n🌐 Visit your blog to see the new styles!")

    return 0 if success_count == len(posts) else 1


if __name__ == '__main__':
    exit(main())