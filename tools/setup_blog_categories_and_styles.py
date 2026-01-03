#!/usr/bin/env python3
"""
Setup Blog Categories and Style Variations
==========================================

Creates the necessary categories and assigns styles to existing blog posts
to enable diverse blog styling instead of AI-generated appearance.

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


def run_wp_cli_command(site_domain: str, command: str) -> str:
    """Run a WP-CLI command via SSH and return the output."""
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
        return ""

    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], password=creds['password'])

        wp_path = f'/home/{creds["username"]}/domains/{site_domain}/public_html'
        full_command = f'cd {wp_path} && {command} --allow-root 2>&1'

        stdin, stdout, stderr = ssh.exec_command(full_command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()
        result = output if output else error

        ssh.close()
        return result.strip()

    except Exception as e:
        print(f"❌ SSH error: {e}")
        return ""


def get_or_create_categories(site_domain: str) -> dict:
    """Get existing categories or create them if they don't exist."""
    categories = {
        'technical': 'Technical writing and development tutorials',
        'development': 'Software development and programming',
        'ai-assisted-development': 'AI-powered development workflows',
        'business-intelligence': 'Business intelligence and analytics',
        'showcase': 'Project showcases and demonstrations',
        'analysis': 'In-depth analysis and insights'
    }

    category_ids = {}

    print("📂 Setting up blog categories...")

    for slug, description in categories.items():
        # Check if category already exists
        check_cmd = f'wp term get category {slug} --field=term_id'
        result = run_wp_cli_command(site_domain, check_cmd)

        if result and result.isdigit():
            category_ids[slug] = result
            print(f"✅ Category '{slug}' exists (ID: {result})")
        else:
            # Try to create category
            create_cmd = f'wp term create category {slug} --description="{description}" --porcelain'
            result = run_wp_cli_command(site_domain, create_cmd)

            if result and result.isdigit():
                category_ids[slug] = result
                print(f"✅ Created category '{slug}' (ID: {result})")
            else:
                print(f"⚠️ Could not get/create category '{slug}': {result}")

    return category_ids


def assign_post_categories(site_domain: str, category_ids: dict) -> bool:
    """Assign categories to existing blog posts."""
    post_assignments = {
        154: ['technical', 'development', 'ai-assisted-development'],  # AI workflow post
        155: ['business-intelligence', 'showcase', 'analysis']  # BI showcase post
    }

    print("\n📝 Assigning categories to blog posts...")

    success_count = 0
    for post_id, category_slugs in post_assignments.items():
        category_id_list = []
        for slug in category_slugs:
            if slug in category_ids:
                category_id_list.append(category_ids[slug])

        if category_id_list:
            cat_string = ','.join(category_id_list)
            assign_cmd = f'wp post term set {post_id} category {cat_string}'
            result = run_wp_cli_command(site_domain, assign_cmd)

            if "Success:" in result or "updated" in result.lower():
                print(f"✅ Updated post {post_id} with categories: {category_slugs}")
                success_count += 1
            else:
                print(f"❌ Failed to update post {post_id}: {result}")
        else:
            print(f"❌ No valid category IDs for post {post_id}")

    return success_count == len(post_assignments)


def test_blog_styles(site_domain: str) -> None:
    """Test that blog posts are using the correct styles."""
    print("\n🧪 Testing blog style assignments...")

    test_urls = [
        f"https://{site_domain}/?p=154",  # Should be technical style
        f"https://{site_domain}/?p=155",  # Should be magazine style
    ]

    import requests

    for url in test_urls:
        try:
            response = requests.get(url, timeout=10)
            if 'blog-style-technical' in response.text:
                print(f"✅ {url} - Technical style detected")
            elif 'blog-style-magazine' in response.text:
                print(f"✅ {url} - Magazine style detected")
            else:
                print(f"⚠️ {url} - Style class not detected")
        except Exception as e:
            print(f"❌ Error testing {url}: {e}")


def main():
    """Main execution."""
    site_domain = "dadudekc.com"

    print("🎨 SETTING UP BLOG STYLE VARIATIONS")
    print("=" * 50)
    print("This will create diverse blog styles to replace AI-generated appearance")
    print("=" * 50)

    # Step 1: Get/create categories
    category_ids = get_or_create_categories(site_domain)

    if not category_ids:
        print("❌ No categories available, aborting")
        return 1

    # Step 2: Assign categories to posts
    if assign_post_categories(site_domain, category_ids):
        print("\n🎉 Blog style setup complete!")
        print("\n📋 Style Assignments:")
        print("   Post 154 (AI Workflow): Technical Style")
        print("   Post 155 (BI Showcase): Magazine Style")

        # Step 3: Test the setup
        test_blog_styles(site_domain)

        print("\n🌐 Visit your blog posts to see the new diverse styles:")
        print("   - Technical style: Code-focused with table of contents")
        print("   - Magazine style: Feature-style with author bio")

        return 0
    else:
        print("❌ Failed to assign categories to all posts")
        return 1


if __name__ == '__main__':
    exit(main())