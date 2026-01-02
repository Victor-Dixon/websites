#!/usr/bin/env python3
"""
Assign Blog Categories for Style Variations
==========================================

Assigns categories to existing blog posts via SSH to enable
diverse blog styling instead of AI-generated appearance.

Author: Agent-2 (Architecture & Design Specialist)
Date: 2026-01-02
"""

import os
import sys

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

def assign_post_categories(site_domain: str = "dadudekc.com"):
    """Assign categories to existing posts."""
    print("🎨 Assigning blog categories for style variations...")

    # Post assignments
    assignments = [
        (154, ["technical", "development", "ai-assisted-development"], "AI Workflow Post → Technical Style"),
        (155, ["business-intelligence", "showcase", "analysis"], "BI Showcase Post → Magazine Style")
    ]

    success_count = 0

    for post_id, categories, description in assignments:
        print(f"\n📝 {description}")

        # Create categories first (ignore errors if they exist)
        category_ids = []
        for category in categories:
            print(f"   Ensuring category exists: {category}")

            # Try to get existing category
            success, output, error = run_wp_cli_command(site_domain, f'wp term get category {category} --field=term_id')
            if success and output and output.isdigit():
                category_ids.append(output)
                print(f"   ✅ Category '{category}' exists (ID: {output})")
            else:
                # Try to create category
                success, output, error = run_wp_cli_command(site_domain, f'wp term create category {category} --porcelain')
                if success and output and output.isdigit():
                    category_ids.append(output)
                    print(f"   ✅ Created category '{category}' (ID: {output})")
                else:
                    print(f"   ⚠️ Could not get/create category '{category}': {error}")

        # Assign categories to post
        if category_ids:
            cat_string = ','.join(category_ids)
            print(f"   Assigning category IDs: {cat_string}")
            success, output, error = run_wp_cli_command(site_domain, f'wp post term set {post_id} category {cat_string}')

            if success and ("Success:" in output or "updated" in output.lower()):
                print(f"   ✅ Successfully assigned categories to post {post_id}")
                success_count += 1
            else:
                print(f"   ❌ Failed to assign categories: {error}")
        else:
            print(f"   ❌ No valid category IDs for post {post_id}")

    print(f"\n📊 Results: {success_count}/{len(assignments)} posts updated")

    if success_count == len(assignments):
        print("\n🎉 Success! Blog posts now have diverse styles:")
        print("   - Post 154: Technical style (code-focused, TOC, monospace fonts)")
        print("   - Post 155: Magazine style (feature layout, author bio, gradient headings)")
        print("\n🌐 Visit your blog posts to see the new diverse styles!")
        print("   https://dadudekc.com/?p=154 (Technical)")
        print("   https://dadudekc.com/?p=155 (Magazine)")

    return success_count == len(assignments)

def main():
    """Main execution."""
    print("🎨 BLOG STYLE ASSIGNMENT SCRIPT")
    print("=" * 40)

    success = assign_post_categories()
    return 0 if success else 1

if __name__ == '__main__':
    exit(main())