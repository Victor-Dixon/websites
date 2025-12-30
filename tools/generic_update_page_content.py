#!/usr/bin/env python3
"""
Generic Page Content Updater
=============================

Updates page content for any WordPress site using unified_wordpress_manager.
Works with all sites in site_configs.json.

Usage:
    python tools/generic_update_page_content.py --site example.com --page about --content "<h1>About</h1><p>Content here</p>"
    python tools/generic_update_page_content.py --site example.com --page-slug about --content-file content.html
    python tools/generic_update_page_content.py --site example.com --page-id 5 --content "<h1>Updated</h1>"

Agent-7: Web Development Specialist
"""

import argparse
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from unified_wordpress_manager import UnifiedWordPressManager, DeploymentMethod


def find_page_by_slug(manager: UnifiedWordPressManager, slug: str) -> int:
    """Find page ID by slug using WP-CLI."""
    if not manager.deployer:
        return None
    
    if not manager.deployer.connect():
        return None
    
    try:
        remote_path = getattr(manager.deployer, 'remote_path', '')
        if not remote_path:
            remote_path = f"domains/{manager.site_domain}/public_html"
        
        command = f"cd {remote_path} && wp post list --post_type=page --name={slug} --format=json --allow-root"
        result = manager.deployer.execute_command(command)
        
        if result and result.strip() != "[]":
            import json
            try:
                pages = json.loads(result)
                if pages:
                    return pages[0]['ID']
            except:
                pass
        
        # Try by title
        command = f"cd {remote_path} && wp post list --post_type=page --s='{slug}' --format=json --allow-root"
        result = manager.deployer.execute_command(command)
        
        if result and result.strip() != "[]":
            import json
            try:
                pages = json.loads(result)
                for page in pages:
                    if slug.lower() in page.get('post_title', '').lower():
                        return page['ID']
            except:
                pass
        
        return None
    finally:
        manager.deployer.disconnect()


def update_page_content(manager: UnifiedWordPressManager, page_id: int, content: str) -> bool:
    """Update page content using WP-CLI."""
    if not manager.deployer:
        return False
    
    if not manager.deployer.connect():
        return False
    
    try:
        remote_path = getattr(manager.deployer, 'remote_path', '')
        if not remote_path:
            remote_path = f"domains/{manager.site_domain}/public_html"
        
        import shlex
        content_escaped = shlex.quote(content)
        
        command = f"cd {remote_path} && wp post update {page_id} --post_content={content_escaped} --allow-root"
        result = manager.deployer.execute_command(command)
        
        if "Success" in result or "Updated" in result or f"post {page_id}" in result.lower():
            return True
        return False
    finally:
        manager.deployer.disconnect()


def main():
    """Main execution."""
    parser = argparse.ArgumentParser(description='Update page content for any WordPress site')
    parser.add_argument('--site', required=True, help='Site domain (e.g., example.com)')
    parser.add_argument('--page', help='Page slug or title to find')
    parser.add_argument('--page-slug', help='Page slug (alternative to --page)')
    parser.add_argument('--page-id', type=int, help='Page ID (if known)')
    parser.add_argument('--content', help='HTML content to set')
    parser.add_argument('--content-file', help='Path to file containing HTML content')
    
    args = parser.parse_args()
    
    print("=" * 70)
    print(f"UPDATE PAGE CONTENT - {args.site}")
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
    print()
    
    # Find page
    page_id = None
    
    if args.page_id:
        page_id = args.page_id
        print(f"✅ Using provided page ID: {page_id}")
    elif args.page or args.page_slug:
        page_slug = args.page_slug or args.page
        print(f"🔍 Finding page: {page_slug}")
        page_id = find_page_by_slug(manager, page_slug)
        
        if page_id:
            print(f"✅ Found page (ID: {page_id})")
        else:
            print(f"❌ Page '{page_slug}' not found")
            return
    else:
        print("❌ Either --page, --page-slug, or --page-id is required")
        return
    
    print()
    
    # Update content
    print(f"📝 Updating page content...")
    if update_page_content(manager, page_id, content):
        print("✅ Page updated successfully")
    else:
        print("❌ Failed to update page")
        return
    
    print()
    print("=" * 70)
    print("✅ UPDATE COMPLETE")
    print("=" * 70)
    print()
    print(f"View the updated page:")
    print(f"https://{args.site}/?p={page_id}")

if __name__ == "__main__":
    main()

