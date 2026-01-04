#!/usr/bin/env python3
"""
Sample Devlog to Episode Converter
Creates representative episodes from each devlog category
"""

import os
import re
from datetime import datetime
from pathlib import Path
from typing import Dict, List, Optional
import json

class SampleDevlogConverter:
    def _get_next_episode_number(self) -> int:
        """Find the highest existing episode number and return the next available number"""
        episodes_dir = Path("D:/websites/digitaldreamscape.site/episodes")

        if not episodes_dir.exists():
            return 200  # Fallback if episodes directory doesn't exist

        max_episode = 199  # Start from 200

        for file_path in episodes_dir.glob("ep_*.html"):
            # Extract episode number from filename like "ep_3138_agent-sessions_session.html"
            match = re.search(r'ep_(\d+)_', file_path.name)
            if match:
                episode_num = int(match.group(1))
                max_episode = max(max_episode, episode_num)

        return max_episode + 1

    def __init__(self, base_output_dir: str):
        self.base_output_dir = Path(base_output_dir)
        # Find the highest existing episode number and continue from there
        self.episode_counter = self._get_next_episode_number()

        # Sample size per category
        self.samples_per_category = {
            'agent-sessions': 10,
            'mission-reports': 5,
            'system-events': 5,
            'repository-analysis': 5,
            'root-devlogs': 5
        }

    def get_sample_files(self, category: str) -> List[Path]:
        """Get a representative sample of files for each category"""
        base_paths = {
            'agent-sessions': Path("D:/Agent_Cellphone_V2_Repository/swarm_brain/devlogs/agent_sessions"),
            'mission-reports': Path("D:/Agent_Cellphone_V2_Repository/swarm_brain/devlogs/mission_reports"),
            'system-events': Path("D:/Agent_Cellphone_V2_Repository/swarm_brain/devlogs/system_events"),
            'repository-analysis': Path("D:/Agent_Cellphone_V2_Repository/swarm_brain/devlogs/repository_analysis"),
            'root-devlogs': Path("D:/Agent_Cellphone_V2_Repository/devlogs")
        }

        path = base_paths.get(category)
        if not path or not path.exists():
            return []

        # Get all markdown files and sample evenly
        all_files = list(path.glob("*.md"))
        sample_size = min(self.samples_per_category[category], len(all_files))

        if sample_size == 0:
            return []

        # Take evenly distributed samples
        step = max(1, len(all_files) // sample_size)
        samples = [all_files[i] for i in range(0, len(all_files), step)][:sample_size]

        return samples

    def categorize_devlog(self, filepath: Path) -> Optional[str]:
        """Categorize a devlog file based on its path"""
        path_str = str(filepath)

        if 'agent_sessions' in path_str:
            return 'agent-sessions'
        elif 'mission_reports' in path_str:
            return 'mission-reports'
        elif 'system_events' in path_str:
            return 'system-events'
        elif 'repository_analysis' in path_str:
            return 'repository-analysis'
        elif 'devlogs' in path_str and 'swarm_brain' not in path_str:
            return 'root-devlogs'

        return None

    def get_mission_reports_files(self) -> List[Path]:
        """Get all mission reports devlog files"""
        mission_reports_dir = Path("D:/Agent_Cellphone_V2_Repository/swarm_brain/devlogs/mission_reports")
        if not mission_reports_dir.exists():
            return []

        return list(mission_reports_dir.glob("*.md"))

    def extract_devlog_metadata(self, filepath: Path) -> Dict:
        """Extract rich metadata and content from devlog file"""
        metadata = {
            'title': '',
            'date': '',
            'agent': '',
            'status': 'active',
            'category': '',
            'summary': '',
            'deliverables': [],
            'technical_achievements': [],
            'quantitative_metrics': [],
            'strategies_used': [],
            'impact_assessment': [],
            'narrative_sections': {},
            'raw_content_sections': {}
        }

        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()

            # Normalize text: replace em dashes with ellipses
            content = content.replace('—', '...')

            # Extract title from first header
            title_match = re.search(r'^#\s+(.+)$', content, re.MULTILINE)
            if title_match:
                metadata['title'] = title_match.group(1).strip()

            # Extract metadata fields
            date_match = re.search(r'\*\*Date\*\*:\s*([^\n]+)', content)
            if date_match:
                metadata['date'] = date_match.group(1).strip()

            agent_match = re.search(r'\*\*Agent\*\*:\s*([^\n]+)', content)
            if agent_match:
                metadata['agent'] = agent_match.group(1).strip()

            status_match = re.search(r'\*\*Status\*\*:\s*([^\n]+)', content)
            if status_match:
                status = status_match.group(1).strip()
                metadata['status'] = 'resolved' if '✅' in status or 'COMPLETE' in status or 'ACHIEVEMENT' in status else 'active'

            # Extract detailed technical content
            self._extract_technical_content(content, metadata)

            # Extract deliverables
            self._extract_deliverables(content, metadata)

            # Build summary from key sections
            metadata['summary'] = self._build_narrative_summary(metadata)

        except Exception as e:
            print(f"Error parsing {filepath}: {e}")

        return metadata

    def _extract_technical_content(self, content: str, metadata: Dict):
        """Extract technical achievements, metrics, and strategies"""

        # Extract technical achievements (file operations, code changes, etc.)
        achievement_patterns = [
            r'### \d+\.\s*([^\n]+)',
            r'-\s*\*\*([^:]+)\*\*',
            r'### ([A-Z][^#\n]+)',
            r'\*\*([A-Z][^*]+)\*\*:'
        ]

        for pattern in achievement_patterns:
            matches = re.findall(pattern, content)
            for match in matches:
                if len(match.strip()) > 10:  # Filter out short matches
                    metadata['technical_achievements'].append(match.strip())

        # Extract quantitative metrics (line counts, percentages, numbers)
        metric_patterns = [
            r'(\d+(?:,\d+)?(?:\.\d+)?)\s*(?:lines?|percent|%|KB|MB|files?)',
            r'(?:Before|After):\s*([^\n]+)',
            r'(?:Reduction|Eliminated):\s*([^\n]+)',
            r'(\d+)\s*(?:files?|modules?|agents?)'
        ]

        for pattern in metric_patterns:
            matches = re.findall(pattern, content, re.IGNORECASE)
            metadata['quantitative_metrics'].extend([match for match in matches if match])

        # Extract strategies and approaches
        strategy_patterns = [
            r'Strategy:\s*([^\n]+)',
            r'Approach:\s*([^\n]+)',
            r'Methodology:\s*([^\n]+)',
            r'Technique:\s*([^\n]+)'
        ]

        for pattern in strategy_patterns:
            matches = re.findall(pattern, content, re.IGNORECASE)
            metadata['strategies_used'].extend(matches)

        # Extract impact statements
        impact_patterns = [
            r'Impact:\s*([^\n]+)',
            r'Outcome:\s*([^\n]+)',
            r'Result:\s*([^\n]+)',
            r'Achievement:\s*([^\n]+)'
        ]

        for pattern in impact_patterns:
            matches = re.findall(pattern, content, re.IGNORECASE)
            metadata['impact_assessment'].extend(matches)

        # Extract major sections for narrative building
        section_headers = [
            'HISTORIC ACHIEVEMENT',
            'AGENT-1 CRITICAL ELIMINATIONS',
            'COORDINATION SUMMARY',
            'EXECUTION SUMMARY',
            'DELIVERABLES SUMMARY',
            'TECHNICAL ACHIEVEMENTS',
            'MISSION OVERVIEW',
            'SWARM COORDINATION',
            'ACTIONS TAKEN',
            'AGENT ASSIGNMENTS',
            'EXECUTION COMPLETE'
        ]

        for header in section_headers:
            section_match = re.search(rf'## .*?{header}.*?$(.+?)(?=##|\Z)', content, re.MULTILINE | re.DOTALL | re.IGNORECASE)
            if section_match:
                metadata['raw_content_sections'][header.lower().replace(' ', '_')] = section_match.group(1).strip()

    def _extract_deliverables(self, content: str, metadata: Dict):
        """Extract deliverables and artifacts"""
        # Look for deliverables sections
        deliverables_patterns = [
            r'## ✅.*?(?:DELIVERABLE|ACHIEVEMENT).*$([.\s\S]*?)(?=##|\Z)',
            r'### .*?(?:DELIVERABLE|CREATED|PRODUCED).*$([.\s\S]*?)(?=##|\Z)',
            r'Files?\s*(?:Created|Produced|Generated):\s*$([.\s\S]*?)(?=\n\n|\Z)'
        ]

        deliverables = []
        for pattern in deliverables_patterns:
            matches = re.findall(pattern, content, re.MULTILINE | re.IGNORECASE)
            for match in matches:
                # Extract individual items
                items = re.findall(r'-\s*([^\n]+)', match)
                deliverables.extend([item.strip() for item in items if item.strip()])

        # Also look for specific file mentions
        file_matches = re.findall(r'`([^`]+\.py)`|\*\*([^*]+\.py)\*\*', content)
        for match in file_matches:
            file_name = match[0] or match[1]
            if file_name and file_name not in deliverables:
                deliverables.append(f"Created {file_name}")

        metadata['deliverables'] = list(set(deliverables))  # Remove duplicates

    def _build_narrative_summary(self, metadata: Dict) -> str:
        """Build a compelling narrative summary from extracted content"""

        # Start with the core achievement
        summary_parts = []

        if metadata['technical_achievements']:
            # Take the most significant achievement
            primary_achievement = metadata['technical_achievements'][0]
            summary_parts.append(primary_achievement[:100])

        if metadata['quantitative_metrics']:
            # Add key metrics
            metrics = metadata['quantitative_metrics'][:2]  # Limit to 2
            for metric in metrics:
                if '%' in metric or 'lines' in metric.lower():
                    summary_parts.append(f"achieving {metric}")

        if metadata['strategies_used']:
            # Add strategy if space
            strategy = metadata['strategies_used'][0][:50]
            summary_parts.append(f"through {strategy}")

        if metadata['impact_assessment']:
            # Add impact
            impact = metadata['impact_assessment'][0][:60]
            summary_parts.append(f"with {impact}")

        summary = '. '.join(summary_parts)
        return summary[:300] + ('...' if len(summary) > 300 else '')

    def create_episode_html(self, devlog_path: Path, category: str, metadata: Dict) -> str:
        """Create episode HTML from devlog metadata"""
        episode_id = f"EP-{self.episode_counter:03d}"
        self.episode_counter += 1

        # Questline definitions
        questlines = {
            'agent-sessions': {'title': 'agent coordination protocols', 'icon': '🤖', 'desc': 'individual agent work sessions and task execution'},
            'mission-reports': {'title': 'swarm mission coordination', 'icon': '🐝', 'desc': 'multi-agent task delegation and coordination'},
            'system-events': {'title': 'system evolution milestones', 'icon': '⚡', 'desc': 'critical system achievements and infrastructure changes'},
            'repository-analysis': {'title': 'codebase intelligence gathering', 'icon': '🔍', 'desc': 'repository evaluation and integration opportunities'},
            'root-devlogs': {'title': 'agent initiative campaigns', 'icon': '🚀', 'desc': 'major agent-led initiatives and project campaigns'}
        }

        questline = questlines.get(category, {'title': category, 'icon': '📄', 'desc': 'agent coordination'})

        is_canon = metadata['status'] == 'resolved'
        agent_name = metadata['agent'] or 'Swarm Agent'
        episode_title = metadata['title'] or f"{category.replace('-', ' ').title()} Session"

        # Build rich narrative content from extracted data
        narrative_content = self._build_rich_narrative(metadata, category)

        html = f"""<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{episode_id}: {episode_title} - Digital Dreamscape</title>
    <style>
        :root {{
            --void-black: #0a0a0f;
            --void-dark: #1a1a2e;
            --void-surface: #2a2a4e;
            --energy-primary: #6366f1;
            --energy-secondary: #8b5cf6;
            --energy-tertiary: #ec4899;
            --text-primary: #ffffff;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
        }}

        * {{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }}

        body {{
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--void-black);
            color: var(--text-primary);
            line-height: 1.6;
        }}

        .container {{
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }}

        .site-header {{
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.95), rgba(139, 92, 246, 0.95));
            padding: 20px 0;
            margin-bottom: 40px;
        }}

        .site-title {{
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }}

        .episode-hero {{
            background: linear-gradient(135deg, var(--void-dark), var(--void-black));
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 40px;
            text-align: center;
        }}

        .episode-badge {{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 50px;
            padding: 12px 24px;
            margin-bottom: 20px;
        }}

        .episode-icon {{
            font-size: 1.5rem;
        }}

        .episode-id {{
            font-weight: bold;
            color: var(--energy-primary);
            font-family: 'JetBrains Mono', monospace;
        }}

        .episode-title {{
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--energy-primary), var(--energy-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }}

        .episode-meta {{
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }}

        .meta-item {{
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            color: var(--text-muted);
        }}

        .episode-excerpt {{
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
        }}

        .episode-content {{
            background: var(--void-surface);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 40px;
        }}

        .episode-content h2 {{
            font-size: 2rem;
            font-weight: 700;
            margin: 40px 0 20px 0;
            color: var(--energy-primary);
            border-bottom: 2px solid rgba(99, 102, 241, 0.3);
            padding-bottom: 10px;
        }}

        .episode-content h3 {{
            font-size: 1.5rem;
            font-weight: 600;
            margin: 30px 0 15px 0;
            color: var(--energy-secondary);
        }}

        .episode-content p {{
            margin-bottom: 20px;
            color: var(--text-secondary);
            line-height: 1.7;
        }}

        .episode-content ul {{
            margin: 20px 0;
            padding-left: 30px;
        }}

        .episode-content li {{
            margin-bottom: 10px;
            color: var(--text-secondary);
        }}

        .metric-highlight {{
            background: rgba(99, 102, 241, 0.1);
            border-left: 3px solid var(--energy-primary);
            padding: 15px;
            margin: 20px 0;
            font-family: 'JetBrains Mono', monospace;
        }}

        .achievement-section {{
            background: rgba(139, 92, 246, 0.05);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }}

        .achievement-title {{
            font-weight: 600;
            color: var(--energy-secondary);
            margin-bottom: 10px;
        }}

        .canon-authority {{
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.05));
            border: 2px solid var(--energy-primary);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            text-align: center;
        }}

        .canon-seal {{
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--energy-primary);
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }}

        .questline-section {{
            background: var(--void-dark);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
        }}

        .questline-title {{
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--energy-secondary);
            margin-bottom: 20px;
        }}

        .episode-closure {{
            background: rgba(99, 102, 241, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
            text-align: center;
            font-family: 'JetBrains Mono', monospace;
        }}

        .episode-footer {{
            text-align: center;
            padding: 40px 0;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }}
    </style>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">🌌 Digital Dreamscape</h1>
        </div>
    </header>

    <div class="container">
        <section class="episode-hero">
            <div class="episode-badge">
                <span class="episode-icon">{questline['icon']}</span>
                <span class="episode-id">{episode_id}</span>
                <span>Episode</span>
            </div>

            <h1 class="episode-title">{episode_title.lower()}</h1>

            <div class="episode-meta">
                <div class="meta-item">
                    <span>🏛️</span>
                    <span>Questline: {category.replace('-', ' ')}</span>
                </div>
                <div class="meta-item">
                    <span>📅</span>
                    <span>Era: 2026</span>
                </div>
                <div class="meta-item">
                    <span>⚡</span>
                    <span>State: {metadata['status']}</span>
                </div>
                <div class="meta-item">
                    <span>🤖</span>
                    <span>Agent: {agent_name}</span>
                </div>
            </div>

            <div class="episode-excerpt">
                <p>{metadata['summary'] or f'agent session in {category.replace("-", " ")} domain.'}</p>
            </div>
        </section>

        <section class="episode-content">
            {'<div class="canon-authority"><div class="canon-seal">[CANON AUTHORITY GRANTED]</div><p>This episode establishes binding precedent for ' + category.replace('-', ' ') + ' protocols. Future development assumes these patterns as standard operating procedure.</p></div>' if is_canon else ''}

{narrative_content}
        </section>

        <section class="questline-section">
            <div class="questline-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="questline-title">questline: {category.replace('-', ' ')}</h2>
            </div>

            <p>this episode advances the {category.replace('-', ' ')} questline. {questline['desc']}. distributed intelligence continues to evolve.</p>
        </section>

        <footer class="episode-footer">
            <p>{episode_id} • {category.replace('-', ' ')} questline • {'canon authority granted' if is_canon else 'state recorded'}</p>
            <p>nothing is lost • systems evolve • agents coordinate</p>
        </footer>
    </div>
</body>

</html>"""

        return html

    def _build_rich_narrative(self, metadata: Dict, category: str) -> str:
        """Build rich narrative content from extracted technical details"""

        content_parts = []

        # System State - set the context
        content_parts.append("<h2>[SYSTEM STATE]</h2>")
        if metadata['agent']:
            content_parts.append(f"<p>agent {metadata['agent'].lower()} initiated critical systems operation.</p>")
        else:
            content_parts.append("<p>swarm intelligence activated for coordinated execution.</p>")

        if metadata['technical_achievements']:
            primary_achievement = metadata['technical_achievements'][0]
            content_parts.append(f"<p>primary objective: {primary_achievement.lower()}.</p>")

        content_parts.append("<p>distributed systems engaged across multiple domains.</p>")

        # Execution Log - show the technical work
        content_parts.append("<h2>[EXECUTION LOG]</h2>")

        if metadata['strategies_used']:
            strategy = metadata['strategies_used'][0]
            content_parts.append(f"<p>strategic approach: {strategy.lower()}.</p>")

        if metadata['quantitative_metrics']:
            content_parts.append("<p>quantitative outcomes achieved:</p><ul>")
            for metric in metadata['quantitative_metrics'][:5]:  # Limit to 5 metrics
                content_parts.append(f"<li>{metric}</li>")
            content_parts.append("</ul>")

        if metadata['technical_achievements']:
            content_parts.append("<p>technical milestones reached:</p><ul>")
            for achievement in metadata['technical_achievements'][1:6]:  # Skip first one, take next 5
                content_parts.append(f"<li>{achievement.lower()}</li>")
            content_parts.append("</ul>")

        # Artifacts Forged - deliverables and outputs
        content_parts.append("<h2>[ARTIFACTS FORGED]</h2>")
        if metadata['deliverables']:
            content_parts.append("<ul>")
            for deliverable in metadata['deliverables'][:8]:  # Limit to 8
                content_parts.append(f"<li>{deliverable.lower()}</li>")
            content_parts.append("</ul>")
        else:
            content_parts.append("<p>operational deliverables processed and integrated.</p>")
            content_parts.append("<p>system artifacts committed to persistent storage.</p>")

        # Strategic Impact - show the broader implications
        content_parts.append("<h2>[STRATEGIC IMPACT]</h2>")

        if metadata['impact_assessment']:
            for impact in metadata['impact_assessment'][:3]:
                content_parts.append(f"<p>{impact.lower()}.</p>")

        # Add key metrics as highlights if available
        if metadata['quantitative_metrics']:
            metrics_to_highlight = metadata['quantitative_metrics'][:3]
            for metric in metrics_to_highlight:
                if '%' in metric or 'lines' in metric.lower() or any(char.isdigit() for char in metric):
                    content_parts.append(f'<div class="metric-highlight">{metric}</div>')

        # Agent Impact - personal contribution
        agent_name = metadata['agent'] or 'swarm agent'
        content_parts.append("<h2>[AGENT IMPACT]</h2>")
        content_parts.append(f"<p>{agent_name.lower()} demonstrated advanced technical proficiency.</p>")
        content_parts.append(f"<p>contributions integrated into swarm knowledge base.</p>")
        content_parts.append(f"<p>patterns established for future {category.replace('-', ' ')} operations.</p>")

        # Archival Note - reflection
        content_parts.append("<h2>[ARCHIVAL NOTE]</h2>")
        content_parts.append("<p>this operation represents peak swarm coordination.</p>")
        content_parts.append("<p>technical excellence achieved through distributed intelligence.</p>")
        content_parts.append("<p>patterns captured for continuous system evolution.</p>")

        # Episode closure
        content_parts.append(f'''<div class="episode-closure">
            <p>episode state: {metadata['status']}<br>
            questline progression: {category.replace('-', ' ')} advanced<br>
            {'canon authority granted' if metadata['status'] == 'resolved' else 'active operation'}</p>
        </div>''')

        return '\n'.join(content_parts)

    def process_samples(self):
        """Process sample files from each category"""
        categories = ['agent-sessions', 'mission-reports', 'system-events', 'repository-analysis', 'root-devlogs']
        all_episodes = []

        for category in categories:
            print(f"Processing {category} samples...")
            sample_files = self.get_sample_files(category)
            print(f"Found {len(sample_files)} files for {category}")

            for filepath in sample_files:
                metadata = self.extract_devlog_metadata(filepath)
                if metadata['title']:  # Only process if we got a title
                    episode_html = self.create_episode_html(filepath, category, metadata)

                    filename = f"ep_{self.episode_counter-1:03d}_{category}_sample.html"
                    output_path = self.base_output_dir / 'episodes' / filename

                    output_path.parent.mkdir(parents=True, exist_ok=True)
                    with open(output_path, 'w', encoding='utf-8') as f:
                        f.write(episode_html)

                    all_episodes.append({
                        'category': category,
                        'metadata': metadata,
                        'filename': filename
                    })

                    print(f"Generated: {filename}")

        return all_episodes

    def convert_mission_reports_full(self):
        """Convert all mission reports devlogs to episodes"""
        print("🔄 Starting full mission reports conversion...")

        mission_files = self.get_mission_reports_files()
        print(f"📊 Found {len(mission_files)} mission reports to convert")

        all_episodes = []
        batch_size = 20  # Process in smaller batches for mission reports

        for i in range(0, len(mission_files), batch_size):
            batch = mission_files[i:i + batch_size]
            print(f"📦 Processing batch {i//batch_size + 1} of {(len(mission_files) + batch_size - 1)//batch_size}")

            for filepath in batch:
                metadata = self.extract_devlog_metadata(filepath)
                if metadata['title']:
                    episode_html = self.create_episode_html(filepath, 'mission-reports', metadata)

                    filename = f"ep_{self.episode_counter:03d}_mission-reports_episode.html"
                    output_path = self.base_output_dir / 'episodes' / filename

                    output_path.parent.mkdir(parents=True, exist_ok=True)
                    with open(output_path, 'w', encoding='utf-8') as f:
                        f.write(episode_html)

                    all_episodes.append({
                        'category': 'mission-reports',
                        'metadata': metadata,
                        'filename': filename,
                        'episode_id': f"EP-{self.episode_counter:03d}"
                    })

                    self.episode_counter += 1
                    print(f"✅ Generated: {filename}")

        print(f"🎉 Mission reports conversion complete! {len(all_episodes)} episodes created.")
        return all_episodes

    def create_sample_index(self, episodes):
        """Create an index page showing all sample episodes"""
        index_html = """<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devlog Episode Samples - Digital Dreamscape</title>
    <style>
        :root {
            --void-black: #0a0a0f;
            --void-dark: #1a1a2e;
            --void-surface: #2a2a4e;
            --energy-primary: #6366f1;
            --energy-secondary: #8b5cf6;
            --energy-tertiary: #ec4899;
            --text-primary: #ffffff;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--void-black);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .site-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.95), rgba(139, 92, 246, 0.95));
            padding: 20px 0;
            margin-bottom: 40px;
        }

        .site-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .hero {
            background: linear-gradient(135deg, var(--void-dark), var(--void-black));
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 40px;
            text-align: center;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--energy-primary), var(--energy-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .stat-item {
            background: var(--void-surface);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--energy-primary);
            display: block;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .category-section {
            background: var(--void-surface);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 30px;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .category-icon {
            font-size: 1.5rem;
        }

        .category-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--energy-secondary);
        }

        .episode-list {
            display: grid;
            gap: 15px;
        }

        .episode-item {
            background: var(--void-dark);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 8px;
            padding: 15px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .episode-item:hover {
            border-color: var(--energy-primary);
            transform: translateY(-2px);
        }

        .episode-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .episode-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        .footer {
            text-align: center;
            padding: 40px 0;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>

<body>
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">🌌 Digital Dreamscape</h1>
        </div>
    </header>

    <div class="container">
        <section class="hero">
            <h1 class="hero-title">devlog episode samples</h1>
            <p class="hero-description">representative episodes from the swarm brain devlog archive. thousands of agent sessions, mission reports, and system events converted to narrative format.</p>
        </section>

        <div class="stats">
            <div class="stat-item">
                <span class="stat-number">3,351</span>
                <span class="stat-label">total devlog files</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">35</span>
                <span class="stat-label">sample episodes created</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">5</span>
                <span class="stat-label">questline categories</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">2026</span>
                <span class="stat-label">intelligence era</span>
            </div>
        </div>

        <div class="categories">
"""

        # Group episodes by category
        by_category = {}
        for ep in episodes:
            cat = ep['category']
            if cat not in by_category:
                by_category[cat] = []
            by_category[cat].append(ep)

        category_info = {
            'agent-sessions': {'icon': '🤖', 'title': 'Agent Sessions', 'desc': 'Individual agent work sessions and task execution'},
            'mission-reports': {'icon': '🐝', 'title': 'Mission Reports', 'desc': 'Multi-agent task delegation and coordination'},
            'system-events': {'icon': '⚡', 'title': 'System Events', 'desc': 'Critical system achievements and infrastructure changes'},
            'repository-analysis': {'icon': '🔍', 'title': 'Repository Analysis', 'desc': 'Codebase evaluation and integration opportunities'},
            'root-devlogs': {'icon': '🚀', 'title': 'Agent Initiatives', 'desc': 'Major agent-led initiatives and project campaigns'}
        }

        for category, eps in by_category.items():
            info = category_info.get(category, {'icon': '📄', 'title': category.title(), 'desc': ''})
            index_html += f"""
            <div class="category-section">
                <div class="category-header">
                    <span class="category-icon">{info['icon']}</span>
                    <h2 class="category-title">{info['title']}</h2>
                </div>
                <p style="color: var(--text-secondary); margin-bottom: 20px; font-size: 0.9rem;">{info['desc']}</p>
                <div class="episode-list">
"""

            for ep in eps:
                metadata = ep['metadata']
                title = metadata['title'][:50] + '...' if len(metadata['title']) > 50 else metadata['title']
                index_html += f"""
                    <a href="episodes/{ep['filename']}" class="episode-item">
                        <div class="episode-title">{title}</div>
                        <div class="episode-meta">agent: {metadata['agent'] or 'swarm'} • {metadata['date'] or '2026'} • {metadata['status']}</div>
                    </a>
"""

            index_html += """
                </div>
            </div>
"""

        index_html += """
        </div>

        <footer class="footer">
            <p>devlog archive • swarm intelligence • distributed execution</p>
            <p>nothing is lost • systems evolve • agents coordinate</p>
        </footer>
    </div>
</body>

</html>"""

        index_path = self.base_output_dir / 'devlog-samples.html'
        with open(index_path, 'w', encoding='utf-8') as f:
            f.write(index_html)

        print(f"Created sample index: {index_path}")

def main():
    converter = SampleDevlogConverter("D:/websites/digitaldreamscape.site")
    episodes = converter.process_samples()
    converter.create_sample_index(episodes)

    print(f"\nCompleted! Generated {len(episodes)} sample episodes from devlog archive.")
    print("Open devlog-samples.html to explore the different categories.")

if __name__ == "__main__":
    main()