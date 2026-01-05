#!/usr/bin/env python3
"""
Publish Episode with Proper Categories
====================================

Enhanced episode publishing that assigns proper WordPress categories
based on questline classifications.
"""

import sys
from pathlib import Path

# Add config and scripts to path
sys.path.insert(0, str(Path(__file__).parent.parent.parent / "config"))
sys.path.insert(0, str(Path(__file__).parent.parent))

from paths import paths
from episode_category_manager import EpisodeCategoryManager


def publish_episode_with_categories(episode_file: str, status: str = "publish") -> bool:
    """Publish an episode with proper category assignment"""

    episode_path = Path(episode_file)
    if not episode_path.exists():
        print(f"❌ Episode file not found: {episode_file}")
        return False

    print(f"📤 Publishing episode: {episode_path.name}")
    print(f"📂 Status: {status}")

    # Initialize category manager
    category_manager = EpisodeCategoryManager()

    # Get categories for this episode
    categories = category_manager.assign_categories_to_episode(str(episode_path))

    if not categories:
        print("⚠️ No categories assigned, using default")
        categories = [1]  # WordPress uncategorized

    print(f"🏷️ Assigned categories: {categories}")

    # Read episode content
    with open(episode_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Extract title from first line
    lines = content.split('\n')
    title = "Digital Dreamscape Episode"
    for line in lines[:5]:  # Check first 5 lines
        if line.startswith('# '):
            title = line[2:].strip()
            break

    print(f"📝 Title: {title}")

    # Create excerpt (first 200 chars of content after title)
    excerpt_content = content
    # Remove the title and system state sections for excerpt
    if "## [EXECUTION LOG]" in excerpt_content:
        excerpt_start = excerpt_content.find("## [EXECUTION LOG]")
        if excerpt_start != -1:
            excerpt_content = excerpt_content[excerpt_start:]

    excerpt = excerpt_content.replace('#', '').replace('*', '').strip()[:200] + "..."

    print(f"📄 Excerpt: {excerpt[:50]}...")

    # Import WordPress publisher
    try:
        from src.autoblogger.wp_publisher import load_wp_env, publish_wordpress_post

        # Load WordPress config
        wp_cfg = load_wp_env(
            base_url_env='DREAM_WP_URL',
            user_env='DREAM_WP_USER',
            app_password_env='DREAM_WP_APP_PASS'
        )

        # Publish with categories
        result = publish_wordpress_post(
            cfg=wp_cfg,
            title=title,
            content=content,
            excerpt=excerpt,
            status=status,
            categories=categories
        )

        if result and 'id' in result:
            post_id = result['id']
            print("✅ Episode published successfully!")
            print(f"🆔 Post ID: {post_id}")
            print(f"🌐 View at: https://digitaldreamscape.site/?p={post_id}")
            return True
        else:
            print(f"❌ Publication failed: {result}")
            return False

    except Exception as e:
        print(f"❌ Publication error: {e}")
        return False


def main():
    """Main function"""
    if len(sys.argv) < 2:
        print("Usage: python publish_episode_with_categories.py <episode_file> [status]")
        print("Status: publish (default) or draft")
        return

    episode_file = sys.argv[1]
    status = sys.argv[2] if len(sys.argv) > 2 else "publish"

    success = publish_episode_with_categories(episode_file, status)

    if success:
        print("\n🎭 Episode successfully published with proper categories!")
        print("🏷️ Check WordPress admin to verify category assignment")
    else:
        print("\n❌ Episode publication failed")
        sys.exit(1)


if __name__ == "__main__":
    main()