#!/usr/bin/env python3
"""
Episode Category Manager for Digital Dreamscape
==============================================

Manages WordPress category assignment for Digital Dreamscape episodes
based on questline classifications.
"""

import os
import requests
from typing import Dict, List, Optional, Any
from pathlib import Path
from dotenv import load_dotenv

# Load environment
load_dotenv()

class EpisodeCategoryManager:
    """Manages WordPress categories for Digital Dreamscape episodes"""

    def __init__(self):
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

        # Cache for category IDs
        self.category_cache = {}

    def get_category_id(self, questline: str) -> Optional[int]:
        """Get WordPress category ID for a questline, creating if necessary"""

        # Normalize questline
        questline = questline.lower().replace('_', '-')

        # Get category name
        category_name = self.questline_categories.get(questline, 'General Episodes')

        # Check cache first
        if category_name in self.category_cache:
            return self.category_cache[category_name]

        # Try to find existing category
        category_id = self._find_category(category_name)
        if category_id:
            self.category_cache[category_name] = category_id
            return category_id

        # Create new category
        category_id = self._create_category(category_name)
        if category_id:
            self.category_cache[category_name] = category_id
            return category_id

        return None

    def _find_category(self, category_name: str) -> Optional[int]:
        """Find existing category by name"""

        if not self._has_wp_credentials():
            return None

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

        if not self._has_wp_credentials():
            print(f"⚠️ Cannot create category '{category_name}' - no WP credentials")
            return None

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

    def extract_questline_from_episode(self, episode_path: str) -> str:
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

        return 'general'

    def assign_categories_to_episode(self, episode_path: str) -> List[int]:
        """Assign appropriate categories to an episode"""

        questline = self.extract_questline_from_episode(episode_path)
        print(f"🎯 Episode questline: {questline}")

        category_id = self.get_category_id(questline)

        if category_id:
            return [category_id]
        else:
            # Fallback to uncategorized (usually ID 1)
            print("⚠️ Using uncategorized fallback")
            return [1]  # WordPress uncategorized category

    def _has_wp_credentials(self) -> bool:
        """Check if WordPress credentials are available"""
        return all([self.base_url, self.username, self.app_password])

    def get_all_categories(self) -> Dict[str, Any]:
        """Get all current WordPress categories"""

        if not self._has_wp_credentials():
            return {}

        try:
            response = requests.get(
                f'{self.base_url}/wp-json/wp/v2/categories',
                auth=(self.username, self.app_password)
            )

            if response.status_code == 200:
                categories = response.json()
                return {cat.get('name', ''): cat.get('id', 0) for cat in categories}

        except Exception as e:
            print(f"❌ Error getting categories: {e}")

        return {}

    def ensure_digital_dreamscape_categories(self) -> None:
        """Ensure all Digital Dreamscape categories exist"""

        print("🏗️ Ensuring Digital Dreamscape categories exist...")

        for questline, category_name in self.questline_categories.items():
            category_id = self.get_category_id(questline)
            if category_id:
                print(f"✅ {category_name} (ID: {category_id})")
            else:
                print(f"❌ Failed to create {category_name}")

        print("✅ Category setup complete")


def main():
    """Main function for testing"""

    import sys

    if len(sys.argv) < 2:
        print("Usage: python episode_category_manager.py <command> [args...]")
        print("Commands:")
        print("  ensure-categories    - Ensure all DD categories exist")
        print("  get-categories       - List all current categories")
        print("  assign <episode_file> - Assign categories to episode file")
        print("  test-questline <questline> - Test questline to category mapping")
        return

    manager = EpisodeCategoryManager()
    command = sys.argv[1]

    if command == 'ensure-categories':
        manager.ensure_digital_dreamscape_categories()

    elif command == 'get-categories':
        categories = manager.get_all_categories()
        print("📂 Current WordPress Categories:")
        for name, cat_id in categories.items():
            print(f"  {cat_id}: {name}")

    elif command == 'assign' and len(sys.argv) >= 3:
        episode_file = sys.argv[2]
        categories = manager.assign_categories_to_episode(episode_file)
        print(f"📂 Assigned categories: {categories}")

    elif command == 'test-questline' and len(sys.argv) >= 3:
        questline = sys.argv[2]
        category_name = manager.questline_categories.get(questline, 'General Episodes')
        category_id = manager.get_category_id(questline)
        print(f"🎯 Questline: {questline}")
        print(f"📂 Category: {category_name}")
        print(f"🆔 Category ID: {category_id}")

    else:
        print(f"❌ Unknown command: {command}")


if __name__ == "__main__":
    main()