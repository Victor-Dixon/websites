#!/usr/bin/env python3
"""
=============================================================================
BLOG MANAGER - WordPress Blog Post Management Tool
=============================================================================

A simple, autonomous tool for agents to manage WordPress blog posts across
all sites. Supports listing, creating, editing, and deleting posts.

QUICK START:
    # List all posts on a site
    python tools/blog_manager.py list ariajet.site

    # Create a new post
    python tools/blog_manager.py create ariajet.site --title "My Post" --content "<p>Hello!</p>"

    # Delete a post
    python tools/blog_manager.py delete ariajet.site --id 1

    # Edit a post
    python tools/blog_manager.py edit ariajet.site --id 5 --title "New Title"

CONFIGURATION:
    Site credentials are loaded from: config/site_configs.json
    
    Each site needs:
    - rest_api.username
    - rest_api.app_password  
    - rest_api.site_url (or site_url)
    
    AND/OR for WP-CLI operations:
    - sftp.host
    - sftp.username
    - sftp.password
    - sftp.port
    - sftp.remote_path

Author: Agent System
Date: 2024-12-29
=============================================================================
"""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path
from typing import Any, Optional

# Add repo root to path
REPO_ROOT = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(REPO_ROOT))

try:
    import requests
    from requests.auth import HTTPBasicAuth
    REQUESTS_AVAILABLE = True
except ImportError:
    REQUESTS_AVAILABLE = False

try:
    import paramiko
    PARAMIKO_AVAILABLE = True
except ImportError:
    PARAMIKO_AVAILABLE = False


# =============================================================================
# CONFIGURATION LOADING
# =============================================================================

def load_site_configs() -> dict:
    """Load site configurations from config/site_configs.json"""
    config_path = REPO_ROOT / "config" / "site_configs.json"
    if not config_path.exists():
        print(f"❌ Config file not found: {config_path}")
        print("   Create config/site_configs.json with your site credentials")
        sys.exit(1)
    
    with open(config_path) as f:
        return json.load(f)


def get_site_config(site: str) -> dict:
    """Get configuration for a specific site"""
    configs = load_site_configs()
    
    # Try exact match first
    if site in configs:
        return configs[site]
    
    # Try partial match
    for domain, config in configs.items():
        if site in domain or domain in site:
            return config
    
    print(f"❌ Site '{site}' not found in config/site_configs.json")
    print(f"   Available sites: {', '.join(configs.keys())}")
    sys.exit(1)


def get_rest_api_auth(config: dict) -> tuple[str, HTTPBasicAuth]:
    """Get REST API base URL and auth from config"""
    if not REQUESTS_AVAILABLE:
        print("❌ requests library not installed. Run: pip install requests")
        sys.exit(1)
    
    api = config.get("rest_api", {})
    base_url = api.get("site_url") or config.get("site_url", "")
    username = api.get("username", "")
    password = api.get("app_password", "")
    
    if not all([base_url, username, password]):
        print("❌ REST API credentials incomplete in config")
        print(f"   site_url: {'✅' if base_url else '❌ Missing'}")
        print(f"   username: {'✅' if username else '❌ Missing'}")
        print(f"   app_password: {'✅' if password else '❌ Missing'}")
        sys.exit(1)
    
    return base_url.rstrip("/"), HTTPBasicAuth(username, password)


# =============================================================================
# SSH/WP-CLI OPERATIONS (for operations that need elevated permissions)
# =============================================================================

def get_ssh_connection(config: dict, site: str) -> Optional[Any]:
    """Get SSH connection for WP-CLI operations"""
    if not PARAMIKO_AVAILABLE:
        return None
    
    sftp = config.get("sftp", {})
    host = sftp.get("host")
    username = sftp.get("username")
    password = sftp.get("password")
    port = sftp.get("port", 65002)
    
    if not all([host, username, password]):
        return None
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(host, port=port, username=username, password=password, timeout=30)
        return ssh
    except Exception as e:
        print(f"⚠️  SSH connection failed: {e}")
        return None


def run_wp_cli(config: dict, site: str, command: str) -> tuple[bool, str]:
    """Run a WP-CLI command via SSH"""
    ssh = get_ssh_connection(config, site)
    if not ssh:
        return False, "SSH not available"
    
    sftp = config.get("sftp", {})
    remote_path = sftp.get("remote_path", "")
    username = sftp.get("username", "")
    
    # Build full path
    if remote_path and not remote_path.startswith("/"):
        wp_path = f"/home/{username}/{remote_path}"
    else:
        wp_path = remote_path or f"/home/{username}/public_html"
    
    full_command = f"cd {wp_path} && wp {command}"
    
    try:
        stdin, stdout, stderr = ssh.exec_command(full_command, timeout=60)
        output = stdout.read().decode("utf-8")
        error = stderr.read().decode("utf-8")
        ssh.close()
        
        if "Error" in error or "error" in error.lower():
            return False, error
        return True, output or error
    except Exception as e:
        ssh.close()
        return False, str(e)


# =============================================================================
# BLOG POST OPERATIONS
# =============================================================================

def list_posts(site: str, status: str = "any", limit: int = 20) -> None:
    """List all posts on a site"""
    config = get_site_config(site)
    base_url, auth = get_rest_api_auth(config)
    
    print(f"📋 Fetching posts from {site}...")
    
    # Build params - only add status if not "any" (default fetch is published only)
    params = {"per_page": limit}
    if status != "any":
        params["status"] = status
    # Note: Some WP installs don't allow querying multiple statuses, so we just fetch published by default
    
    try:
        resp = requests.get(f"{base_url}/wp-json/wp/v2/posts", auth=auth, params=params, timeout=30)
        
        if resp.status_code != 200:
            print(f"❌ Failed to fetch posts: HTTP {resp.status_code}")
            print(f"   {resp.text[:300]}")
            return
        
        posts = resp.json()
        
        if not posts:
            print("📭 No posts found")
            return
        
        print(f"\n{'ID':<6} {'Status':<10} {'Date':<12} {'Title'}")
        print("-" * 70)
        for post in posts:
            post_id = post.get("id", "?")
            title = post.get("title", {}).get("rendered", "Untitled")[:40]
            status = post.get("status", "?")
            date = post.get("date", "")[:10]
            print(f"{post_id:<6} {status:<10} {date:<12} {title}")
        
        print(f"\n✅ Found {len(posts)} post(s)")
        
    except Exception as e:
        print(f"❌ Error: {e}")


def create_post(
    site: str,
    title: str,
    content: str,
    status: str = "draft",
    excerpt: str = "",
    categories: list = None,
    tags: list = None,
) -> None:
    """Create a new blog post"""
    config = get_site_config(site)
    
    print(f"📝 Creating post on {site}...")
    
    # Try REST API first
    try:
        base_url, auth = get_rest_api_auth(config)
        payload = {
            "title": title,
            "content": content,
            "status": status,
        }
        if excerpt:
            payload["excerpt"] = excerpt
        if categories:
            payload["categories"] = categories
        if tags:
            payload["tags"] = tags
        
        resp = requests.post(f"{base_url}/wp-json/wp/v2/posts", auth=auth, json=payload, timeout=30)
        
        if resp.status_code in (200, 201):
            data = resp.json()
            print(f"✅ Post created successfully!")
            print(f"   ID: {data.get('id')}")
            print(f"   Title: {data.get('title', {}).get('rendered')}")
            print(f"   Status: {data.get('status')}")
            print(f"   Link: {data.get('link')}")
            return
        elif resp.status_code == 401:
            print("⚠️  REST API lacks create permission, trying WP-CLI...")
        else:
            print(f"⚠️  REST API failed (HTTP {resp.status_code}), trying WP-CLI...")
    except Exception as e:
        print(f"⚠️  REST API error: {e}, trying WP-CLI...")
    
    # Fallback to WP-CLI via SSH
    # Escape content for shell - use base64 to handle complex HTML
    import base64
    content_b64 = base64.b64encode(content.encode()).decode()
    
    # Build WP-CLI command
    cmd_parts = [f'post create --post_title="{title}" --post_status={status}']
    cmd_parts.append(f'--post_content="$(echo {content_b64} | base64 -d)"')
    if excerpt:
        cmd_parts.append(f'--post_excerpt="{excerpt}"')
    cmd_parts.append('--porcelain')  # Return just the post ID
    
    success, output = run_wp_cli(config, site, ' '.join(cmd_parts))
    
    if success and output.strip().isdigit():
        post_id = output.strip()
        print(f"✅ Post created via WP-CLI!")
        print(f"   ID: {post_id}")
        print(f"   Title: {title}")
        print(f"   Status: {status}")
        
        # Get the link
        success2, link_output = run_wp_cli(config, site, f'post get {post_id} --field=url')
        if success2 and link_output.strip():
            print(f"   Link: {link_output.strip()}")
    else:
        print(f"❌ Failed to create post")
        print(f"   {output}")


def edit_post(
    site: str,
    post_id: int,
    title: str = None,
    content: str = None,
    status: str = None,
    excerpt: str = None,
) -> None:
    """Edit an existing blog post"""
    config = get_site_config(site)
    
    print(f"✏️  Editing post {post_id} on {site}...")
    
    payload = {}
    if title is not None:
        payload["title"] = title
    if content is not None:
        payload["content"] = content
    if status is not None:
        payload["status"] = status
    if excerpt is not None:
        payload["excerpt"] = excerpt
    
    if not payload:
        print("⚠️  No changes specified")
        return
    
    # Try REST API first
    try:
        base_url, auth = get_rest_api_auth(config)
        resp = requests.post(f"{base_url}/wp-json/wp/v2/posts/{post_id}", auth=auth, json=payload, timeout=30)
        
        if resp.status_code == 200:
            data = resp.json()
            print(f"✅ Post updated successfully!")
            print(f"   ID: {data.get('id')}")
            print(f"   Title: {data.get('title', {}).get('rendered')}")
            print(f"   Status: {data.get('status')}")
            print(f"   Link: {data.get('link')}")
            return
        elif resp.status_code == 401:
            print("⚠️  REST API lacks edit permission, trying WP-CLI...")
        else:
            print(f"⚠️  REST API failed (HTTP {resp.status_code}), trying WP-CLI...")
    except Exception as e:
        print(f"⚠️  REST API error: {e}, trying WP-CLI...")
    
    # Fallback to WP-CLI via SSH
    import base64
    
    cmd_parts = [f'post update {post_id}']
    if title is not None:
        cmd_parts.append(f'--post_title="{title}"')
    if content is not None:
        content_b64 = base64.b64encode(content.encode()).decode()
        cmd_parts.append(f'--post_content="$(echo {content_b64} | base64 -d)"')
    if status is not None:
        cmd_parts.append(f'--post_status={status}')
    if excerpt is not None:
        cmd_parts.append(f'--post_excerpt="{excerpt}"')
    
    success, output = run_wp_cli(config, site, ' '.join(cmd_parts))
    
    if success and "Success" in output:
        print(f"✅ Post updated via WP-CLI!")
        print(f"   {output.strip()}")
    else:
        print(f"❌ Failed to edit post")
        print(f"   {output}")


def delete_post(site: str, post_id: int, force: bool = True) -> None:
    """Delete a blog post"""
    config = get_site_config(site)
    
    print(f"🗑️  Deleting post {post_id} from {site}...")
    
    # Try REST API first
    try:
        base_url, auth = get_rest_api_auth(config)
        params = {"force": "true"} if force else {}
        resp = requests.delete(f"{base_url}/wp-json/wp/v2/posts/{post_id}", auth=auth, params=params, timeout=30)
        
        if resp.status_code == 200:
            data = resp.json()
            print(f"✅ Post deleted successfully!")
            print(f"   Title: {data.get('title', {}).get('rendered', 'Unknown')}")
            print(f"   {'Permanently deleted' if force else 'Moved to trash'}")
            return
        elif resp.status_code == 401:
            print("⚠️  REST API lacks delete permission, trying WP-CLI...")
        else:
            print(f"⚠️  REST API failed (HTTP {resp.status_code}), trying WP-CLI...")
    except Exception as e:
        print(f"⚠️  REST API error: {e}, trying WP-CLI...")
    
    # Fallback to WP-CLI via SSH
    force_flag = "--force" if force else ""
    success, output = run_wp_cli(config, site, f"post delete {post_id} {force_flag}")
    
    if success and "Success" in output:
        print(f"✅ Post deleted via WP-CLI!")
        print(f"   {output.strip()}")
    else:
        print(f"❌ Failed to delete post")
        print(f"   {output}")


def get_post(site: str, post_id: int) -> None:
    """Get details of a specific post"""
    config = get_site_config(site)
    base_url, auth = get_rest_api_auth(config)
    
    print(f"🔍 Fetching post {post_id} from {site}...")
    
    try:
        resp = requests.get(f"{base_url}/wp-json/wp/v2/posts/{post_id}", auth=auth, timeout=30)
        
        if resp.status_code == 200:
            post = resp.json()
            print(f"\n{'='*60}")
            print(f"ID:      {post.get('id')}")
            print(f"Title:   {post.get('title', {}).get('rendered')}")
            print(f"Status:  {post.get('status')}")
            print(f"Date:    {post.get('date')}")
            print(f"Link:    {post.get('link')}")
            print(f"Slug:    {post.get('slug')}")
            print(f"{'='*60}")
            print(f"\nExcerpt:\n{post.get('excerpt', {}).get('rendered', 'N/A')[:300]}")
            print(f"\nContent Preview:\n{post.get('content', {}).get('rendered', 'N/A')[:500]}...")
        else:
            print(f"❌ Post not found: HTTP {resp.status_code}")
            
    except Exception as e:
        print(f"❌ Error: {e}")


def list_sites() -> None:
    """List all available sites"""
    configs = load_site_configs()
    print("\n📋 Available Sites:")
    print("-" * 50)
    for domain in sorted(configs.keys()):
        config = configs[domain]
        has_rest = bool(config.get("rest_api", {}).get("app_password"))
        has_sftp = bool(config.get("sftp", {}).get("password"))
        methods = []
        if has_rest:
            methods.append("REST")
        if has_sftp:
            methods.append("SFTP")
        print(f"  • {domain:<35} [{', '.join(methods)}]")
    print()


# =============================================================================
# CLI INTERFACE
# =============================================================================

def main():
    parser = argparse.ArgumentParser(
        description="WordPress Blog Manager - Manage blog posts across all sites",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  # List all posts
  python tools/blog_manager.py list ariajet.site
  
  # Create a draft post
  python tools/blog_manager.py create ariajet.site --title "My New Post" --content "<p>Content here</p>"
  
  # Create and publish immediately  
  python tools/blog_manager.py create ariajet.site --title "Live Post" --content "<p>Hello world!</p>" --status publish
  
  # Edit a post's title
  python tools/blog_manager.py edit ariajet.site --id 5 --title "Updated Title"
  
  # Delete a post permanently
  python tools/blog_manager.py delete ariajet.site --id 1
  
  # View a specific post
  python tools/blog_manager.py get ariajet.site --id 5
  
  # List all configured sites
  python tools/blog_manager.py sites
        """
    )
    
    subparsers = parser.add_subparsers(dest="command", help="Command to run")
    
    # List posts
    list_parser = subparsers.add_parser("list", help="List all posts on a site")
    list_parser.add_argument("site", help="Site domain (e.g., ariajet.site)")
    list_parser.add_argument("--status", default="any", help="Filter by status (publish, draft, any)")
    list_parser.add_argument("--limit", type=int, default=20, help="Max posts to show")
    
    # Create post
    create_parser = subparsers.add_parser("create", help="Create a new blog post")
    create_parser.add_argument("site", help="Site domain")
    create_parser.add_argument("--title", required=True, help="Post title")
    create_parser.add_argument("--content", help="Post content (HTML)")
    create_parser.add_argument("--content-file", help="Read content from file")
    create_parser.add_argument("--status", default="draft", choices=["draft", "publish", "pending", "private"], help="Post status")
    create_parser.add_argument("--excerpt", help="Post excerpt")
    
    # Edit post
    edit_parser = subparsers.add_parser("edit", help="Edit an existing post")
    edit_parser.add_argument("site", help="Site domain")
    edit_parser.add_argument("--id", type=int, required=True, help="Post ID to edit")
    edit_parser.add_argument("--title", help="New title")
    edit_parser.add_argument("--content", help="New content (HTML)")
    edit_parser.add_argument("--content-file", help="Read content from file")
    edit_parser.add_argument("--status", choices=["draft", "publish", "pending", "private"], help="New status")
    edit_parser.add_argument("--excerpt", help="New excerpt")
    
    # Delete post
    delete_parser = subparsers.add_parser("delete", help="Delete a blog post")
    delete_parser.add_argument("site", help="Site domain")
    delete_parser.add_argument("--id", type=int, required=True, help="Post ID to delete")
    delete_parser.add_argument("--trash", action="store_true", help="Move to trash instead of permanent delete")
    
    # Get post
    get_parser = subparsers.add_parser("get", help="Get details of a specific post")
    get_parser.add_argument("site", help="Site domain")
    get_parser.add_argument("--id", type=int, required=True, help="Post ID")
    
    # List sites
    subparsers.add_parser("sites", help="List all configured sites")
    
    args = parser.parse_args()
    
    if not args.command:
        parser.print_help()
        return
    
    # Handle commands
    if args.command == "sites":
        list_sites()
    
    elif args.command == "list":
        list_posts(args.site, args.status, args.limit)
    
    elif args.command == "create":
        content = args.content
        if args.content_file:
            content = Path(args.content_file).read_text()
        if not content:
            print("❌ Content required (use --content or --content-file)")
            return
        create_post(args.site, args.title, content, args.status, args.excerpt or "")
    
    elif args.command == "edit":
        content = args.content
        if args.content_file:
            content = Path(args.content_file).read_text()
        edit_post(args.site, args.id, args.title, content, args.status, args.excerpt)
    
    elif args.command == "delete":
        delete_post(args.site, args.id, force=not args.trash)
    
    elif args.command == "get":
        get_post(args.site, args.id)


if __name__ == "__main__":
    main()
