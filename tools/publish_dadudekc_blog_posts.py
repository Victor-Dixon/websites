#!/usr/bin/env python3
"""
Publish Existing Blog Posts to dadudekc.com
==========================================

Publishes the existing blog posts from websites/dadudekc.com/blog-posts/
to the live WordPress site using WP-CLI over SSH.

Author: Agent-2 (Architecture & Design Specialist)
Date: 2026-01-02
"""

import os
import sys
from pathlib import Path
from typing import Dict, List, Optional, Tuple
import yaml
import markdown
from bs4 import BeautifulSoup

# Import the WP-CLI publisher
sys.path.append(str(Path(__file__).parent.parent / 'ops' / 'deployment'))
from publish_post_wpcli import publish_post_via_wpcli


def extract_frontmatter_and_content(markdown_text: str) -> Tuple[Dict, str]:
    """Extract frontmatter and content from markdown text."""
    if not markdown_text.startswith('---'):
        return {}, markdown_text

    parts = markdown_text.split('---', 2)
    if len(parts) < 3:
        return {}, markdown_text

    try:
        frontmatter = yaml.safe_load(parts[1]) or {}
        content = parts[2].strip()
        return frontmatter, content
    except yaml.YAMLError:
        return {}, markdown_text


def convert_markdown_to_html(markdown_content: str) -> str:
    """Convert markdown content to HTML."""
    # Configure markdown extensions
    extensions = [
        'extra',           # Extra features like tables, footnotes
        'codehilite',      # Code highlighting
        'toc',             # Table of contents
        'meta',            # Metadata
    ]

    # Convert markdown to HTML
    html_content = markdown.markdown(markdown_content, extensions=extensions)

    # Clean up the HTML
    soup = BeautifulSoup(html_content, 'html.parser')

    # Add some basic styling classes and structure
    for h1 in soup.find_all('h1'):
        h1['class'] = h1.get('class', []) + ['entry-title']

    for h2 in soup.find_all('h2'):
        h2['class'] = h2.get('class', []) + ['section-title']

    for h3 in soup.find_all('h3'):
        h3['class'] = h3.get('class', []) + ['subsection-title']

    for pre in soup.find_all('pre'):
        pre['class'] = pre.get('class', []) + ['code-block']

    for blockquote in soup.find_all('blockquote'):
        blockquote['class'] = blockquote.get('class', []) + ['highlight-box']

    return str(soup)


def process_html_file(html_file_path: str) -> Tuple[str, str]:
    """Process HTML file and extract title and content."""
    with open(html_file_path, 'r', encoding='utf-8') as f:
        html_content = f.read()

    soup = BeautifulSoup(html_content, 'html.parser')

    # Extract title
    title_tag = soup.find('title')
    title = title_tag.get_text() if title_tag else "Business Intelligence Showcase"

    # Remove the head section and keep only body content
    body = soup.find('body')
    if body:
        # Remove any script/style tags from body
        for script in body.find_all(['script', 'style']):
            script.decompose()
        content = str(body)
    else:
        # Fallback: use the entire HTML
        content = html_content

    return title, content


def get_blog_posts() -> List[Dict]:
    """Get all blog posts from the dadudekc directory."""
    blog_dir = Path("websites/dadudekc.com/blog-posts")
    posts = []

    if not blog_dir.exists():
        print(f"❌ Blog posts directory not found: {blog_dir}")
        return posts

    # Process markdown files
    for md_file in blog_dir.glob("*.md"):
        if md_file.name == "README.md":
            continue

        print(f"📄 Found markdown file: {md_file.name}")

        try:
            with open(md_file, 'r', encoding='utf-8') as f:
                markdown_content = f.read()

            frontmatter, content = extract_frontmatter_and_content(markdown_content)

            # Convert markdown to HTML
            html_content = convert_markdown_to_html(content)

            posts.append({
                'file': str(md_file),
                'type': 'markdown',
                'title': frontmatter.get('title', md_file.stem.replace('-', ' ').title()),
                'content': html_content,
                'frontmatter': frontmatter,
                'excerpt': frontmatter.get('excerpt', ''),
                'categories': frontmatter.get('categories', []),
                'tags': frontmatter.get('tags', [])
            })

        except Exception as e:
            print(f"❌ Error processing {md_file}: {e}")
            continue

    # Process HTML files
    for html_file in blog_dir.glob("*.html"):
        print(f"📄 Found HTML file: {html_file.name}")

        try:
            title, content = process_html_file(str(html_file))

            posts.append({
                'file': str(html_file),
                'type': 'html',
                'title': title,
                'content': content,
                'excerpt': '',
                'categories': ['Business Intelligence'],
                'tags': ['analytics', 'automation', 'tools']
            })

        except Exception as e:
            print(f"❌ Error processing {html_file}: {e}")
            continue

    return posts


def publish_blog_posts(site_domain: str = "dadudekc.com", status: str = "publish") -> bool:
    """Publish all blog posts to the WordPress site."""
    print("\n" + "="*70)
    print("📝 PUBLISHING EXISTING BLOG POSTS TO DADUDEKC.COM")
    print("="*70)

    posts = get_blog_posts()

    if not posts:
        print("❌ No blog posts found to publish")
        return False

    print(f"📋 Found {len(posts)} blog posts to publish:")
    for i, post in enumerate(posts, 1):
        print(f"   {i}. {post['title']} ({post['type']})")

    success_count = 0
    failed_posts = []

    for i, post in enumerate(posts, 1):
        print(f"\n{'─'*50}")
        print(f"📝 Publishing {i}/{len(posts)}: {post['title']}")
        print(f"{'─'*50}")

        # Add some WordPress-specific formatting
        wp_content = post['content']

        # Add categories and tags if available
        categories = post.get('categories', [])
        tags = post.get('tags', [])

        try:
            success = publish_post_via_wpcli(
                site_domain=site_domain,
                title=post['title'],
                content=wp_content,
                status=status
            )

            if success:
                success_count += 1
                print(f"✅ Successfully published: {post['title']}")
            else:
                failed_posts.append(post['title'])
                print(f"❌ Failed to publish: {post['title']}")

        except Exception as e:
            failed_posts.append(post['title'])
            print(f"❌ Error publishing {post['title']}: {e}")

    print(f"\n{'═'*70}")
    print("📊 PUBLISHING RESULTS")
    print(f"{'═'*70}")
    print(f"✅ Successfully published: {success_count}/{len(posts)} posts")

    if failed_posts:
        print(f"❌ Failed posts: {len(failed_posts)}")
        for post in failed_posts:
            print(f"   - {post}")

    return success_count == len(posts)


def main():
    """Main execution."""
    import argparse

    parser = argparse.ArgumentParser(
        description='Publish existing blog posts to dadudekc.com via WP-CLI'
    )
    parser.add_argument(
        '--site',
        type=str,
        default='dadudekc.com',
        help='Site domain (default: dadudekc.com)'
    )
    parser.add_argument(
        '--status',
        type=str,
        default='publish',
        choices=['draft', 'publish'],
        help='Post status (default: publish)'
    )
    parser.add_argument(
        '--dry-run',
        action='store_true',
        help='Show what would be published without actually publishing'
    )

    args = parser.parse_args()

    if args.dry_run:
        print("🔍 DRY RUN MODE - Showing posts that would be published:")
        posts = get_blog_posts()

        for post in posts:
            print(f"\n📄 {post['title']}")
            print(f"   Type: {post['type']}")
            print(f"   File: {post['file']}")
            print(f"   Categories: {post.get('categories', [])}")
            print(f"   Tags: {post.get('tags', [])}")
            print(f"   Content preview: {post['content'][:200]}...")

        print(f"\n📊 Would publish {len(posts)} posts to {args.site}")
        return 0

    success = publish_blog_posts(args.site, args.status)
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())