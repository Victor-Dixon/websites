#!/usr/bin/env python3
"""
Fix Episode Categories - Apply proper categories to existing Digital Dreamscape posts
==================================================================================

This script reads existing Digital Dreamscape posts and assigns proper categories
based on questline classifications extracted from episode content.
"""

import os
import requests
from typing import Dict, List, Optional
from pathlib import Path

# Add config to path for importing
import sys
sys.path.insert(0, str(Path(__file__).parent.parent.parent / "config"))
from paths import paths


class EpisodeCategoryFixer:
    """Fixes categories for existing Digital Dreamscape episodes"""

    def __init__(self):
        # Load environment variables
        self.base_url = os.environ.get('DREAM_WP_URL', '').replace('/wp-json/wp/v2', '')
        self.username = os.environ.get('DREAM_WP_USER')
        self.app_password = os.environ.get('DREAM_WP_APP_PASS')

        # Questline to category mapping
        self.questline_categories = {
            'infrastructure-architecture': 'Infrastructure & Architecture',
            'agent-coordination': 'Agent Coordination',
            'digitaldreamscape-chronicles': 'Digital Dreamscape Chronicles',
            'canon-automation': 'Canon Automation',
            'development-operations': 'Development Operations',
            'system-debugging': 'System Debugging',
            'general': 'General Episodes'
        }

    def fix_all_episodes(self) -> None:
        """Fix categories for all Digital Dreamscape episodes"""

        print("🔧 Fixing categories for all Digital Dreamscape episodes...")

        # Get all episodes from the episodes directory
        episodes_dir = paths.content / "episodes"
        if not episodes_dir.exists():
            print(f"❌ Episodes directory not found: {episodes_dir}")
            return

        episode_files = list(episodes_dir.glob("EP-*.md"))
        print(f"📂 Found {len(episode_files)} episodes to process")

        fixed_count = 0
        for episode_file in episode_files:
            if self.fix_episode_categories(episode_file):
                fixed_count += 1

        print(f"\n✅ Fixed categories for {fixed_count}/{len(episode_files)} episodes")

    def fix_episode_categories(self, episode_path: Path) -> bool:
        """Fix categories for a single episode"""

        episode_id = episode_path.stem.split('_')[0]
        print(f"🎭 Processing {episode_id}...")

        # Extract questline from episode
        questline = self.extract_questline_from_episode(episode_path)
        if not questline:
            print(f"  ⚠️ No questline found for {episode_id}")
            return False

        # Get category ID
        category_id = self.get_or_create_category(questline)
        if not category_id:
            print(f"  ❌ Could not get category for questline: {questline}")
            return False

        # Find the post by title (episode ID)
        post_id = self.find_post_by_title(f"{episode_id}:")
        if not post_id:
            print(f"  ⚠️ Post not found for {episode_id}")
            return False

        # Update post categories
        if self.update_post_categories(post_id, [category_id]):
            print(f"  ✅ Assigned category '{self.questline_categories.get(questline, 'General Episodes')}' to {episode_id}")
            return True
        else:
            print(f"  ❌ Failed to update categories for {episode_id}")
            return False

    def extract_questline_from_episode(self, episode_path: Path) -> Optional[str]:
        """Extract questline from episode file"""

        try:
            with open(episode_path, 'r', encoding='utf-8') as f:
                content = f.read()

            # Look for questline in [SYSTEM STATE] section
            lines = content.split('\n')
            in_system_state = False

            for line in lines:
                if '[SYSTEM STATE]' in line:
                    in_system_state = True
                    continue
                elif line.startswith('## [') and in_system_state:
                    break  # End of system state

                if in_system_state and '**Questline:**' in line:
                    # Extract questline value
                    questline = line.split('**Questline:**')[1].strip()
                    return questline

        except Exception as e:
            print(f"⚠️ Error extracting questline from {episode_path}: {e}")

        return None

    def get_or_create_category(self, questline: str) -> Optional[int]:
        """Get category ID for questline, creating if necessary"""

        if not self._has_wp_credentials():
            # Return default category ID (uncategorized = 1) if no credentials
            return 1

        category_name = self.questline_categories.get(questline, 'General Episodes')

        # Try to find existing category
        category_id = self._find_category(category_name)
        if category_id:
            return category_id

        # Create new category
        category_id = self._create_category(category_name)
        return category_id or 1  # Fallback to uncategorized

    def _find_category(self, category_name: str) -> Optional[int]:
        """Find existing category by name"""

        try:
            response = requests.get(
                f'{self.base_url}/wp-json/wp/v2/categories',
                auth=(self.username, self.app_password),
                params={'search': category_name}
            )

            if response.status_code == 200:
                categories = response.json()
                for cat in categories:
                    if cat.get('name', '').lower() == category_name.lower():
                        return cat.get('id')

        except Exception as e:
            print(f"⚠️ Error finding category '{category_name}': {e}")

        return None

    def _create_category(self, category_name: str) -> Optional[int]:
        """Create a new WordPress category"""

        try:
            # Generate slug
            slug = category_name.lower().replace(' ', '-').replace('&', 'and')

            payload = {
                'name': category_name,
                'slug': slug,
                'description': f'Digital Dreamscape episodes for {category_name.lower()}'
            }

            response = requests.post(
                f'{self.base_url}/wp-json/wp/v2/categories',
                auth=(self.username, self.app_password),
                json=payload
            )

            if response.status_code in [200, 201]:
                category_data = response.json()
                category_id = category_data.get('id')
                print(f"✅ Created category '{category_name}' (ID: {category_id})")
                return category_id
            else:
                print(f"❌ Failed to create category '{category_name}': {response.status_code}")
                print(f"   Response: {response.text[:200]}...")

        except Exception as e:
            print(f"❌ Error creating category '{category_name}': {e}")

        return None

    def find_post_by_title(self, title_prefix: str) -> Optional[int]:
        """Find post by title prefix"""

        if not self._has_wp_credentials():
            return None

        try:
            # Search for posts with the title prefix
            response = requests.get(
                f'{self.base_url}/wp-json/wp/v2/posts',
                auth=(self.username, self.app_password),
                params={
                    'search': title_prefix,
                    'per_page': 50
                }
            )

            if response.status_code == 200:
                posts = response.json()
                for post in posts:
                    if title_prefix in post.get('title', {}).get('rendered', ''):
                        return post.get('id')

        except Exception as e:
            print(f"⚠️ Error finding post with title prefix '{title_prefix}': {e}")

        return None

    def update_post_categories(self, post_id: int, category_ids: List[int]) -> bool:
        """Update post categories"""

        if not self._has_wp_credentials():
            print("⚠️ No WordPress credentials - categories not updated")
            return False

        try:
            payload = {
                'categories': category_ids
            }

            response = requests.post(
                f'{self.base_url}/wp-json/wp/v2/posts/{post_id}',
                auth=(self.username, self.app_password),
                json=payload
            )

            if response.status_code in [200, 201]:
                print(f"✅ Updated categories for post {post_id}")
                return True
            else:
                print(f"❌ Failed to update post {post_id}: {response.status_code}")
                print(f"   Response: {response.text[:200]}...")

        except Exception as e:
            print(f"❌ Error updating post {post_id}: {e}")

        return False

    def _has_wp_credentials(self) -> bool:
        """Check if WordPress credentials are available"""
        return all([self.base_url, self.username, self.app_password])

    def generate_category_report(self) -> None:
        """Generate a report of current categories and needed fixes"""

        print("📊 Digital Dreamscape Episode Category Report")
        print("=" * 50)

        # Get all episodes
        episodes_dir = paths.content / "episodes"
        episode_files = list(episodes_dir.glob("EP-*.md")) if episodes_dir.exists() else []

        print(f"📂 Total episodes: {len(episode_files)}")
        print()

        # Analyze episodes by questline
        questline_counts = {}
        uncategorized_episodes = []

        for episode_file in episode_files:
            questline = self.extract_questline_from_episode(episode_file)
            if questline:
                questline_counts[questline] = questline_counts.get(questline, 0) + 1

                # Check if post exists and has correct category
                episode_id = episode_file.stem.split('_')[0]
                post_id = self.find_post_by_title(f"{episode_id}:")
                if post_id:
                    # In a real implementation, we'd check the post's current categories
                    # For now, we'll just note that they need checking
                    pass
                else:
                    uncategorized_episodes.append(episode_id)
            else:
                uncategorized_episodes.append(episode_file.stem.split('_')[0])

        print("🎯 Episodes by Questline:")
        for questline, count in questline_counts.items():
            category_name = self.questline_categories.get(questline, 'Unknown')
            print(f"  • {questline}: {count} episodes → '{category_name}'")

        print()
        print(f"❓ Episodes needing category assignment: {len(uncategorized_episodes)}")
        if uncategorized_episodes:
            print("   " + ", ".join(uncategorized_episodes[:10]) + ("..." if len(uncategorized_episodes) > 10 else ""))

        print()
        print("🏷️ Required WordPress Categories:")
        for questline, category_name in self.questline_categories.items():
            count = questline_counts.get(questline, 0)
            status = "✅" if count > 0 else "❌"
            print(f"  {status} {category_name} ({count} episodes)")


def main():
    """Main function"""

    import sys

    if len(sys.argv) < 2:
        print("Usage: python fix_episode_categories.py <command>")
        print("Commands:")
        print("  fix-all     - Fix categories for all episodes")
        print("  report      - Generate category status report")
        print("  fix <episode_file> - Fix categories for specific episode")
        return

    fixer = EpisodeCategoryFixer()
    command = sys.argv[1]

    if command == 'fix-all':
        fixer.fix_all_episodes()
    elif command == 'report':
        fixer.generate_category_report()
    elif command == 'fix' and len(sys.argv) >= 3:
        episode_file = sys.argv[2]
        success = fixer.fix_episode_categories(Path(episode_file))
        print(f"✅ Fixed categories for {episode_file}" if success else f"❌ Failed to fix categories for {episode_file}")
    else:
        print(f"❌ Unknown command: {command}")


if __name__ == "__main__":
    main()