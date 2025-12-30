#!/usr/bin/env python3
"""
Generic Post Content Updater
============================

Updates post content for any WordPress site using unified_wordpress_manager.
Works with all sites in site_configs.json.

Usage:
    python tools/generic_update_post_content.py --site example.com --post-slug my-post --content "<h1>Title</h1><p>Content</p>"
    python tools/generic_update_post_content.py --site example.com --post-id 123 --content-file content.html
    python tools/generic_update_post_content.py --site example.com --post-slug my-post --content "<p>New content</p>" --append

Agent-7: Web Development Specialist
"""

import argparse
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from unified_wordpress_manager import UnifiedWordPressManager


def find_post_by_slug(manager: UnifiedWordPressManager, slug: str) -> int:
    """Find post ID by slug using WP-CLI."""
    if not manager.deployer:
        return None
    
    if not manager.deployer.connect():
        return None
    
    try:
        remote_path = getattr(manager.deployer, 'remote_path', '')
        if not remote_path:
            remote_path = f"domains/{manager.site_domain}/public_html"
        
        command = f"cd {remote_path} && wp post list --name={slug} --format=json --allow-root"
        result = manager.deployer.execute_command(command)
        
        if result and result.strip() != "[]":
            import json
            try:
                posts = json.loads(result)
                if posts:
                    return posts[0]['ID']
            except:
                pass
        
        return None
    finally:
        manager.deployer.disconnect()


def get_post_content(manager: UnifiedWordPressManager, post_id: int) -> str:
    """Get current post content."""
    if not manager.deployer:
        return None
    
    if not manager.deployer.connect():
        return None
    
    try:
        remote_path = getattr(manager.deployer, 'remote_path', '')
        if not remote_path:
            remote_path = f"domains/{manager.site_domain}/public_html"
        
        command = f"cd {remote_path} && wp post get {post_id} --field=post_content --allow-root"
        result = manager.deployer.execute_command(command)
        
        return result.strip() if result else None
    finally:
        manager.deployer.disconnect()


def update_post_content(manager: UnifiedWordPressManager, post_id: int, content: str, append: bool = False) -> bool:
    """Update post content using WP-CLI."""
    if not manager.deployer:
        return False
    
    if not manager.deployer.connect():
        return False
    
    try:
        remote_path = getattr(manager.deployer, 'remote_path', '')
        if not remote_path:
            remote_path = f"domains/{manager.site_domain}/public_html"
        
        # If appending, get current content first
        if append:
            current_content = get_post_content(manager, post_id)
            if current_content:
                content = current_content + "\n\n" + content
        
        import shlex
        content_escaped = shlex.quote(content)
        
        command = f"cd {remote_path} && wp post update {post_id} --post_content={content_escaped} --allow-root"
        result = manager.deployer.execute_command(command)
        
        if "Success" in result or "Updated" in result or f"post {post_id}" in result.lower():
            return True
        return False
    finally:
        manager.deployer.disconnect()


def main():
    """Main execution."""
    parser = argparse.ArgumentParser(description='Update post content for any WordPress site')
    parser.add_argument('--site', required=True, help='Site domain (e.g., example.com)')
    parser.add_argument('--post-slug', help='Post slug to find')
    parser.add_argument('--post-id', type=int, help='Post ID (if known)')
    parser.add_argument('--content', help='HTML content to set')
    parser.add_argument('--content-file', help='Path to file containing HTML content')
    parser.add_argument('--append', action='store_true', help='Append to existing content instead of replacing')
    
    args = parser.parse_args()
    
    print("=" * 70)
    print(f"UPDATE POST CONTENT - {args.site}")
    print("=" * 70)
    print()
    
    # Load site configs
    sys.path.insert(0, str(Path(__file__).parent.parent / "ops" / "deployment"))
    from simple_wordpress_deployer import load_site_configs
    site_configs = load_site_configs()
    
    if args.site not in site_configs:
        print(f"❌ Site {args.site} not found in site_configs.json")
        print(f"   Available sites: {', '.join(site_configs.keys())}")
        return
    
    # Initialize manager
    manager = UnifiedWordPressManager(args.site, site_configs.get(args.site))
    
    # Get content
    if args.content_file:
        content_file = Path(args.content_file)
        if not content_file.exists():
            print(f"❌ Content file not found: {content_file}")
            return
        with open(content_file, 'r', encoding='utf-8') as f:
            content = f.read()
    elif args.content:
        content = args.content
    else:
        print("❌ Either --content or --content-file is required")
        return
    
    print(f"📝 Content length: {len(content)} characters")
    if args.append:
        print("   Mode: Append to existing content")
    else:
        print("   Mode: Replace content")
    print()
    
    # Find post
    post_id = None
    
    if args.post_id:
        post_id = args.post_id
        print(f"✅ Using provided post ID: {post_id}")
    elif args.post_slug:
        print(f"🔍 Finding post: {args.post_slug}")
        post_id = find_post_by_slug(manager, args.post_slug)
        
        if post_id:
            print(f"✅ Found post (ID: {post_id})")
        else:
            print(f"❌ Post '{args.post_slug}' not found")
            return
    else:
        print("❌ Either --post-slug or --post-id is required")
        return
    
    print()
    
    # Update content
    print(f"📝 Updating post content...")
    if update_post_content(manager, post_id, content, args.append):
        print("✅ Post updated successfully")
    else:
        print("❌ Failed to update post")
        return
    
    print()
    print("=" * 70)
    print("✅ UPDATE COMPLETE")
    print("=" * 70)
    print()
    print(f"View the updated post:")
    print(f"https://{args.site}/?p={post_id}")

if __name__ == "__main__":
    main()

