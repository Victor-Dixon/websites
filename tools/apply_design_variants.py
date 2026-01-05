#!/usr/bin/env python3
"""
Apply Design Variants to Specific Posts
========================================

Adds design_variant custom fields to specific WordPress posts for testing new designs.

Usage:
    python tools/apply_design_variants.py --post-url "https://dadudekc.com/a-professional-review-of-my-vibe-coded-project-dream-os-2/" --variant "dream-os"

Or apply multiple at once:
    python tools/apply_design_variants.py --apply-presets
"""

import argparse
import json
import sys
from pathlib import Path

# Add repo root to path
REPO_ROOT = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(REPO_ROOT))

# Import deployment tools
from ops.deployment.simple_wordpress_deployer import SimpleWordPressDeployer, load_site_configs


def run_wp_cli_command(site_domain: str, command: str) -> tuple[bool, str, str]:
    """Run a WP-CLI command via SSH."""
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

        stdin, stdout, stderr = ssh.exec_command(full_command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()

        ssh.close()
        return True, output.strip(), error.strip()

    except Exception as e:
        return False, "", str(e)


def get_post_id_from_url(site_domain: str, post_url: str) -> int | None:
    """Extract post ID from WordPress post URL."""
    # Extract slug from URL
    if site_domain in post_url:
        slug = post_url.split(site_domain)[1].strip('/')
        if slug:
            # Query WordPress for post by slug
            success, output, error = run_wp_cli_command(
                site_domain,
                f"wp post list --post_type=post --name='{slug}' --format=json --fields=ID"
            )

            if success and output:
                try:
                    posts = json.loads(output)
                    if posts:
                        return int(posts[0]['ID'])
                except json.JSONDecodeError:
                    pass

    return None


def apply_design_variant(site_domain: str, post_id: int, variant: str) -> bool:
    """Apply a design variant to a specific post."""
    print(f"🎨 Applying design variant '{variant}' to post ID {post_id}...")

    success, output, error = run_wp_cli_command(
        site_domain,
        f"wp post meta set {post_id} design_variant '{variant}'"
    )

    if success:
        print(f"✅ Successfully applied '{variant}' variant to post {post_id}")
        return True
    else:
        print(f"❌ Failed to apply variant: {error}")
        return False


def apply_presets(site_domain: str) -> None:
    """Apply preset design variants to specific posts."""
    print("🎯 Applying preset design variants...")

    presets = [
        {
            'url': 'https://dadudekc.com/a-professional-review-of-my-vibe-coded-project-dream-os-2/',
            'variant': 'dream-os'
        },
        {
            'url': 'https://dadudekc.com/business-intelligence-showcase-advanced-analytics-automation/',
            'variant': 'tech-blueprint'
        },
        {
            'url': 'https://dadudekc.com/how-i-built-an-ai-assisted-development-workflow-after-cursors-1-per-request-trap/',
            'variant': 'minimalist'
        }
    ]

    for preset in presets:
        post_id = get_post_id_from_url(site_domain, preset['url'])
        if post_id:
            apply_design_variant(site_domain, post_id, preset['variant'])
        else:
            print(f"❌ Could not find post ID for: {preset['url']}")


def main() -> int:
    parser = argparse.ArgumentParser(description='Apply design variants to WordPress posts')
    parser.add_argument('--site', default='dadudekc.com', help='WordPress site domain')
    parser.add_argument('--post-url', help='Full URL of the post to modify')
    parser.add_argument('--post-id', type=int, help='Post ID to modify')
    parser.add_argument('--variant', help='Design variant to apply')
    parser.add_argument('--apply-presets', action='store_true', help='Apply preset variants to recommended posts')

    args = parser.parse_args()

    if args.apply_presets:
        apply_presets(args.site)
        return 0

    if not ((args.post_url or args.post_id) and args.variant):
        print("❌ Specify either --post-url or --post-id, and --variant")
        print("\nExamples:")
        print("  python tools/apply_design_variants.py --post-url 'https://dadudekc.com/post-slug/' --variant dream-os")
        print("  python tools/apply_design_variants.py --post-id 123 --variant minimalist")
        print("  python tools/apply_design_variants.py --apply-presets")
        return 1

    # Get post ID
    post_id = args.post_id
    if not post_id and args.post_url:
        post_id = get_post_id_from_url(args.site, args.post_url)
        if not post_id:
            print(f"❌ Could not find post ID for URL: {args.post_url}")
            return 1

    # Apply variant
    if apply_design_variant(args.site, post_id, args.variant):
        print(f"\n🎉 Design variant '{args.variant}' applied successfully!")
        print(f"📝 View your post to see the new design: https://{args.site}/?p={post_id}")
        return 0
    else:
        return 1


if __name__ == '__main__':
    exit(main())