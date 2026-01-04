#!/usr/bin/env python3
"""
Update Questline Pages with New Episodes
"""

import os
from pathlib import Path
from typing import List, Dict
import re

class QuestlineUpdater:
    def __init__(self, base_dir: str):
        self.base_dir = Path(base_dir)
        self.episodes_dir = self.base_dir / 'episodes'
        self.questlines_dir = self.base_dir / 'questlines'

    def get_mission_reports_episodes(self) -> List[Dict]:
        """Get all mission reports episodes"""
        episodes = []

        # Get all mission-reports episode files
        for file_path in self.episodes_dir.glob("ep_*_mission-reports_episode.html"):
            # Extract episode number
            match = re.search(r'ep_(\d+)_mission-reports_episode\.html', file_path.name)
            if match:
                episode_num = int(match.group(1))

                # Read the episode file to get title and metadata
                try:
                    with open(file_path, 'r', encoding='utf-8') as f:
                        content = f.read()

                    # Extract title
                    title_match = re.search(r'<h1 class="episode-title">(.*?)</h1>', content)
                    title = title_match.group(1) if title_match else f"Episode {episode_num}"

                    # Extract description from excerpt
                    desc_match = re.search(r'<div class="episode-excerpt">.*?<p>(.*?)</p>.*?</div>', content, re.DOTALL)
                    description = desc_match.group(1)[:100] + '...' if desc_match and len(desc_match.group(1)) > 100 else (desc_match.group(1) if desc_match else "")

                    # Extract agent and date from meta
                    agent_match = re.search(r'<span>🤖</span>\s*<span>Agent: (.*?)</span>', content)
                    agent = agent_match.group(1) if agent_match else "swarm"

                    date_match = re.search(r'<span>📅</span>\s*<span>Era: (.*?)</span>', content)
                    date = date_match.group(1) if date_match else "2026"

                    # Extract status
                    status_match = re.search(r'<span>⚡</span>\s*<span>State: (.*?)</span>', content)
                    status = status_match.group(1) if status_match else "active"

                    episodes.append({
                        'episode_num': episode_num,
                        'title': title,
                        'description': description,
                        'agent': agent,
                        'date': date,
                        'status': status,
                        'filename': file_path.name
                    })

                except Exception as e:
                    print(f"Error reading {file_path}: {e}")

        # Sort by episode number
        episodes.sort(key=lambda x: x['episode_num'])

        return episodes

    def generate_episode_cards_html(self, episodes: List[Dict]) -> str:
        """Generate HTML for episode cards"""
        html_parts = []

        for episode in episodes:
            episode_id = f"EP-{episode['episode_num']:03d}"
            status_class = "canon" if episode['status'] == 'resolved' else "active"

            card_html = f'''                <a href="../episodes/{episode['filename']}" class="episode-card">
                    <div class="episode-header">
                        <span class="episode-id">{episode_id}</span>
                        <span class="episode-status">{status_class}</span>
                    </div>
                    <h3 class="episode-title">{episode['title']}</h3>
                    <p class="episode-description">{episode['description']}</p>
                    <div class="episode-meta">agent: {episode['agent']} • {episode['date']}</div>
                </a>'''

            html_parts.append(card_html)

        return '\n'.join(html_parts)

    def update_mission_reports_questline(self):
        """Update the mission-reports questline page with all new episodes"""
        questline_path = self.questlines_dir / 'mission-reports.html'

        if not questline_path.exists():
            print(f"Questline file not found: {questline_path}")
            return

        print("🔄 Updating mission-reports questline page...")

        # Get all mission reports episodes
        episodes = self.get_mission_reports_episodes()
        print(f"📊 Found {len(episodes)} mission reports episodes")

        # Generate new episode cards HTML
        episode_cards_html = self.generate_episode_cards_html(episodes)

        # Read current questline file
        with open(questline_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Find the episodes grid section and replace it
        # Look for the pattern between the episodes-title and the closing section
        pattern = r'(\s*<h2 class="episodes-title">episode archive</h2>\s*<div class="episodes-grid">).*?(\s*</div>\s*</section>)'

        new_content = re.sub(pattern, rf'\1{episode_cards_html}\2', content, flags=re.DOTALL)

        # Write back the updated content
        with open(questline_path, 'w', encoding='utf-8') as f:
            f.write(new_content)

        print(f"✅ Updated mission-reports questline with {len(episodes)} episodes")
        print(f"   Episode range: EP-{episodes[0]['episode_num']:03d} to EP-{episodes[-1]['episode_num']:03d}")

def main():
    print("🚀 Updating Questline Pages")
    print("=" * 40)

    updater = QuestlineUpdater("D:/websites/digitaldreamscape.site")
    updater.update_mission_reports_questline()

    print("\n✅ Questline pages updated successfully!")
    print("🔗 View updated questline: questlines/mission-reports.html")

if __name__ == "__main__":
    main()