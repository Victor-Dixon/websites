#!/usr/bin/env python3
"""
Add navigation links between episodes in the documentation-cleanup questline
"""

import os
from pathlib import Path

class EpisodeNavigator:
    def __init__(self, episodes_dir):
        self.episodes_dir = Path(episodes_dir)
        self.episodes = self.get_episode_files()

    def get_episode_files(self):
        """Get all episode files sorted by episode number"""
        files = []
        for file in self.episodes_dir.glob("*.html"):
            if file.name.startswith("ep_"):
                # Extract episode number from filename
                parts = file.name.split("_")
                if len(parts) >= 2:
                    ep_num = int(parts[1])
                    files.append((ep_num, file))

        return sorted(files)

    def get_episode_info(self, ep_num):
        """Get episode title and description based on episode number"""
        cycle_info = {
            145: ("Documentation Cleanup Cycle 1", "files deleted, SSOT consolidation established"),
            146: ("Documentation Cleanup Cycle 2", "archive cleaned, hotspots identified"),
            147: ("Documentation Cleanup Cycle 3", "legacy entry points removed"),
            148: ("Documentation Cleanup Cycle 4", "README consolidation complete"),
            149: ("Documentation Cleanup Cycle 5", "test files and completed docs removed"),
            150: ("Documentation Cleanup Cycle 6", "mission system overlap analyzed"),
            151: ("Documentation Cleanup Cycle 7", "jutsu consolidation plan moved")
        }
        return cycle_info.get(ep_num, (f"Episode {ep_num}", "documentation cleanup cycle"))

    def generate_navigation_html(self, current_ep_num):
        """Generate navigation HTML for current episode"""
        episodes = [ep[0] for ep in self.episodes]
        current_idx = episodes.index(current_ep_num) if current_ep_num in episodes else 0

        nav_html = '        <!-- Navigation -->\n        <nav class="episode-navigation">\n'

        # Previous episode
        if current_idx > 0:
            prev_ep = episodes[current_idx - 1]
            prev_title, prev_desc = self.get_episode_info(prev_ep)
            nav_html += f'''            <a href="ep_{prev_ep:03d}_documentation_cleanup_cycle_{prev_ep-144}.html" class="nav-link">
                <div class="nav-direction">← previous</div>
                <div class="nav-title">EP-{prev_ep:03d}: {prev_title}</div>
                <div class="nav-description">{prev_desc}</div>
            </a>
'''
        else:
            nav_html += '''            <a href="#" class="nav-link" style="opacity: 0.5; pointer-events: none;">
                <div class="nav-direction">← previous</div>
                <div class="nav-title">Questline Start</div>
                <div class="nav-description">beginning of cleanup</div>
            </a>
'''

        # Questline overview
        nav_html += '''            <a href="index.html" class="nav-link">
                <div class="nav-direction">↑ questline</div>
                <div class="nav-title">documentation-cleanup</div>
                <div class="nav-description">all cleanup episodes</div>
            </a>
'''

        # Next episode
        if current_idx < len(episodes) - 1:
            next_ep = episodes[current_idx + 1]
            next_title, next_desc = self.get_episode_info(next_ep)
            nav_html += f'''            <a href="ep_{next_ep:03d}_documentation_cleanup_cycle_{next_ep-144}.html" class="nav-link">
                <div class="nav-direction">next →</div>
                <div class="nav-title">EP-{next_ep:03d}: {next_title}</div>
                <div class="nav-description">{next_desc}</div>
            </a>
'''
        else:
            nav_html += '''            <a href="#" class="nav-link" style="opacity: 0.5; pointer-events: none;">
                <div class="nav-direction">next →</div>
                <div class="nav-title">Questline Complete</div>
                <div class="nav-description">all cycles finished</div>
            </a>
'''

        nav_html += '        </nav>\n'
        return nav_html

    def generate_related_episodes_html(self, current_ep_num):
        """Generate related episodes section"""
        html = '''            <div class="related-episodes">
'''

        # Show previous 2 and next 2 episodes
        episodes = [ep[0] for ep in self.episodes]
        current_idx = episodes.index(current_ep_num) if current_ep_num in episodes else 0

        # Get episodes to show (previous 2, current, next 2, but exclude current)
        show_episodes = []
        for i in range(max(0, current_idx - 2), min(len(episodes), current_idx + 3)):
            if episodes[i] != current_ep_num:
                show_episodes.append(episodes[i])

        for ep_num in show_episodes[:4]:  # Limit to 4 related episodes
            title, desc = self.get_episode_info(ep_num)
            status = "canon" if ep_num < current_ep_num else ("active" if ep_num == current_ep_num else "planned")

            html += f'''                <div class="related-episode">
                    <div class="episode-id">EP-{ep_num:03d}</div>
                    <h4>{title}</h4>
                    <p>{desc}</p>
                    <div class="status">{status}</div>
                </div>
'''

        html += '''            </div>'''
        return html

    def update_episode_file(self, filepath, current_ep_num):
        """Update a single episode file with navigation"""
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # Generate navigation sections
        nav_html = self.generate_navigation_html(current_ep_num)
        related_html = self.generate_related_episodes_html(current_ep_num)

        # Replace existing navigation placeholder or add after questline section
        if '<!-- Navigation -->' in content:
            content = content.replace('        <!-- Navigation -->\n        <nav class="episode-navigation">\n            <!-- Navigation content will be generated -->\n        </nav>', nav_html)
        else:
            # Find questline section and add navigation after it
            questline_end = content.find('        </section>\n\n        <!-- Footer -->')
            if questline_end != -1:
                content = content[:questline_end] + '\n' + nav_html + '\n' + content[questline_end:]

        # Update related episodes section
        if '<div class="related-episodes">' in content:
            # Find the related episodes section and replace it
            start = content.find('            <div class="related-episodes">')
            end = content.find('            </div>', start) + 13
            if start != -1 and end != -1:
                content = content[:start] + related_html + content[end:]

        # Write back the updated content
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)

    def update_all_episodes(self):
        """Update all episode files with proper navigation"""
        for ep_num, filepath in self.episodes:
            print(f"Updating navigation for EP-{ep_num:03d}")
            self.update_episode_file(filepath, ep_num)

        print(f"Updated navigation for {len(self.episodes)} episodes")

if __name__ == "__main__":
    navigator = EpisodeNavigator("D:/websites/digitaldreamscape.site/episodes")
    navigator.update_all_episodes()