#!/usr/bin/env python3
"""
Mass Devlog to Episode Converter
Converts thousands of devlog files into Digital Dreamscape episodes
"""

import os
import re
from datetime import datetime
from pathlib import Path
from typing import Dict, List, Optional
import json

class DevlogEpisodeConverter:
    def __init__(self, base_output_dir: str):
        self.base_output_dir = Path(base_output_dir)
        self.episode_counter = 152  # Starting after our documentation cleanup episodes
        self.questlines = {}

        # Questline definitions
        self.questline_definitions = {
            'agent-sessions': {
                'title': 'agent coordination protocols',
                'description': 'individual agent work sessions and task execution',
                'domain': 'execution',
                'icon': '🤖'
            },
            'mission-reports': {
                'title': 'swarm mission coordination',
                'description': 'multi-agent task delegation and coordination',
                'domain': 'coordination',
                'icon': '🐝'
            },
            'system-events': {
                'title': 'system evolution milestones',
                'description': 'critical system achievements and infrastructure changes',
                'domain': 'infrastructure',
                'icon': '⚡'
            },
            'repository-analysis': {
                'title': 'codebase intelligence gathering',
                'description': 'repository evaluation and integration opportunities',
                'domain': 'analysis',
                'icon': '🔍'
            },
            'root-devlogs': {
                'title': 'agent initiative campaigns',
                'description': 'major agent-led initiatives and project campaigns',
                'domain': 'strategy',
                'icon': '🚀'
            }
        }

    def categorize_devlog(self, filepath: Path) -> Optional[str]:
        """Categorize a devlog file based on its path and content"""
        path_str = str(filepath)

        # Check path-based categorization first
        if 'swarm_brain/devlogs/agent_sessions' in path_str:
            return 'agent-sessions'
        elif 'swarm_brain/devlogs/mission_reports' in path_str:
            return 'mission-reports'
        elif 'swarm_brain/devlogs/system_events' in path_str:
            return 'system-events'
        elif 'swarm_brain/devlogs/repository_analysis' in path_str:
            return 'repository-analysis'
        elif 'devlogs/' in path_str and 'swarm_brain' not in path_str:
            return 'root-devlogs'

        # Content-based fallback
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read(1000)  # Read first 1000 chars

            if 'Agent-' in content and 'Status' in content:
                return 'agent-sessions'
            elif 'DELEGATION' in content or 'mission' in content.lower():
                return 'mission-reports'
            elif 'MILESTONE' in content or 'CRITICAL' in content:
                return 'system-events'
            elif 'repo' in content.lower() or 'repository' in content.lower():
                return 'repository-analysis'

        except:
            pass

        return None

    def extract_devlog_metadata(self, filepath: Path) -> Dict:
        """Extract metadata from devlog file"""
        metadata = {
            'title': '',
            'date': '',
            'agent': '',
            'status': 'active',
            'category': '',
            'summary': '',
            'deliverables': [],
            'artifacts': []
        }

        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()

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
                metadata['status'] = 'resolved' if '✅' in status or 'COMPLETE' in status else 'active'

            # Extract deliverables/artifacts
            deliverables_section = re.search(r'## ✅.*DELIVERABLE.*$(.+?)(?=##|\Z)', content, re.MULTILINE | re.DOTALL)
            if deliverables_section:
                deliverables_text = deliverables_section.group(1)
                deliverables = re.findall(r'-\s*\*\*([^:]+)\*\*', deliverables_text)
                metadata['deliverables'] = deliverables

            # Extract summary/objective
            summary_match = re.search(r'## 🎯.*$(.+?)(?=##|\Z)', content, re.MULTILINE | re.DOTALL)
            if summary_match:
                metadata['summary'] = summary_match.group(1).strip()[:200] + '...' if len(summary_match.group(1).strip()) > 200 else summary_match.group(1).strip()

        except Exception as e:
            print(f"Error parsing {filepath}: {e}")

        return metadata

    def create_episode_html(self, devlog_path: Path, category: str, metadata: Dict) -> str:
        """Create episode HTML from devlog metadata"""
        episode_id = f"EP-{self.episode_counter:03d}"
        questline_info = self.questline_definitions.get(category, {})

        # Determine canon status based on metadata
        is_canon = metadata['status'] == 'resolved'

        # Extract episode content sections
        deliverables_count = len(metadata['deliverables'])
        agent_name = metadata['agent'] or 'Swarm Agent'
        episode_title = metadata['title'] or f"{category.replace('-', ' ').title()} Session"

        html = f"""<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{episode_id}: {episode_title} - Digital Dreamscape</title>
    <style>
        /* Digital Dreamscape Visual Grammar */
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

        /* Header */
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

        /* Episode Hero */
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

        /* Content */
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

        .episode-content ul,
        .episode-content ol {{
            margin: 20px 0;
            padding-left: 30px;
        }}

        .episode-content li {{
            margin-bottom: 10px;
            color: var(--text-secondary);
        }}

        .episode-content strong {{
            color: var(--text-primary);
            font-weight: 600;
        }}

        /* Canon Authority */
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

        .canon-authority p {{
            font-size: 1.1rem;
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
        }}

        /* Questline Integration */
        .questline-section {{
            background: var(--void-dark);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
        }}

        .questline-header {{
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }}

        .questline-title {{
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--energy-secondary);
        }}

        .questline-progress {{
            display: flex;
            align-items: center;
            gap: 15px;
            font-family: 'JetBrains Mono', monospace;
        }}

        .progress-bar {{
            width: 200px;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }}

        .progress-fill {{
            height: 100%;
            background: linear-gradient(90deg, var(--energy-secondary), var(--energy-tertiary));
            width: 60%;
        }}

        .progress-text {{
            font-size: 0.9rem;
            color: var(--text-muted);
        }}

        /* Footer */
        .episode-footer {{
            text-align: center;
            padding: 40px 0;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }}

        /* Responsive */
        @media (max-width: 768px) {{
            .episode-title {{
                font-size: 2rem;
            }}

            .episode-meta {{
                flex-direction: column;
                gap: 15px;
            }}

            .episode-navigation {{
                flex-direction: column;
            }}

            .related-episodes {{
                grid-template-columns: 1fr;
            }}

            .loop-binding {{
                grid-template-columns: 1fr;
                gap: 20px;
            }}

            .canon-authority {{
                padding: 20px;
            }}

            .episode-closure {{
                padding: 20px;
            }}
        }}
    </style>
</head>

<body>
    <!-- Site Header -->
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">🌌 Digital Dreamscape</h1>
        </div>
    </header>

    <div class="container">
        <!-- Episode Hero -->
        <section class="episode-hero">
            <div class="episode-badge">
                <span class="episode-icon">{questline_info.get('icon', '📄')}</span>
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

        <!-- Episode Content -->
        <section class="episode-content">
            <!-- Canon Declaration -->
            {'<div class="canon-authority"><div class="canon-seal">[CANON AUTHORITY GRANTED]</div><p>This episode establishes binding precedent for ' + category.replace('-', ' ') + ' protocols. Future development assumes these patterns as standard operating procedure.</p></div>' if is_canon else ''}

            <h2>[SYSTEM STATE]</h2>
            <p>agent {agent_name.lower()} activated session protocols.</p>
            <p>task execution initiated in {category.replace('-', ' ')} domain.</p>
            <p>system resources allocated for mission completion.</p>

            <h2>[EXECUTION LOG]</h2>
            <p>agent coordination established with swarm intelligence.</p>
            <p>task parameters processed and validated.</p>
            <p>execution protocols engaged across distributed systems.</p>

            <h2>[ARTIFACTS FORGED]</h2>
            <ul>"""

        # Add deliverables as artifacts
        if metadata['deliverables']:
            for deliverable in metadata['deliverables'][:8]:  # Limit to 8
                html += f"                <li>{deliverable.lower()}</li>\n"
        else:
            html += f"                <li>task execution completed</li>\n"
            html += f"                <li>agent session documented</li>\n"
            html += f"                <li>system state updated</li>\n"

        html += f"""
            </ul>

            <h2>[AGENT IMPACT]</h2>
            <p>{agent_name.lower()} contributed to {category.replace('-', ' ')} objectives.</p>
            <p>swarm coordination enhanced through distributed execution.</p>
            <p>system intelligence expanded through collaborative processing.</p>

            <h2>[ARCHIVAL NOTE]</h2>
            <p>this agent session represents distributed intelligence in action.</p>
            <p>each agent contributes unique capabilities to the collective mission.</p>
            <p>the swarm learns and adapts through coordinated execution.</p>

            <div class="episode-closure">
                <p>episode state: {metadata['status']}<br>
                questline progression: {category.replace('-', ' ')} active<br>
                {'canon authority granted' if is_canon else 'agent coordination maintained'}</p>
            </div>
        </section>

        <!-- Questline Integration -->
        <section class="questline-section">
            <div class="questline-header">
                <h2 class="questline-title">questline: {category.replace('-', ' ')}</h2>
                <div class="questline-progress">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <span class="progress-text">active</span>
                </div>
            </div>

            <p>this episode advances the {category.replace('-', ' ')} questline. {questline_info.get('description', 'agent coordination and task execution')}. distributed intelligence continues to evolve.</p>
        </section>

        <!-- Footer -->
        <footer class="episode-footer">
            <p>{episode_id} • {category.replace('-', ' ')} questline • {'canon authority granted' if is_canon else 'state recorded'}</p>
            <p>nothing is lost • systems evolve • agents coordinate</p>
        </footer>
    </div>
</body>

</html>"""

        return html

    def process_devlog_file(self, filepath: Path) -> Optional[Dict]:
        """Process a single devlog file and return episode data"""
        category = self.categorize_devlog(filepath)
        if not category:
            return None

        metadata = self.extract_devlog_metadata(filepath)

        # Skip if we can't extract meaningful metadata
        if not metadata['title']:
            return None

        return {
            'filepath': filepath,
            'category': category,
            'metadata': metadata,
            'episode_id': f"EP-{self.episode_counter:03d}"
        }

    def process_batch(self, devlog_files: List[Path], batch_size: int = 50) -> List[Dict]:
        """Process a batch of devlog files"""
        episodes = []

        for i, filepath in enumerate(devlog_files):
            if i % batch_size == 0:
                print(f"Processing batch {i//batch_size + 1}...")

            episode_data = self.process_devlog_file(filepath)
            if episode_data:
                episodes.append(episode_data)
                self.episode_counter += 1

        return episodes

    def generate_episodes(self, episodes: List[Dict], output_dir: Path):
        """Generate HTML files for episodes"""
        output_dir.mkdir(parents=True, exist_ok=True)

        for episode_data in episodes:
            html_content = self.create_episode_html(
                episode_data['filepath'],
                episode_data['category'],
                episode_data['metadata']
            )

            filename = f"{episode_data['episode_id'].lower().replace('-', '_')}_{episode_data['category']}_session.html"
            filepath = output_dir / filename

            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(html_content)

            print(f"Generated: {filepath}")

    def create_questline_indexes(self, episodes: List[Dict], output_dir: Path):
        """Create questline index pages"""
        questline_episodes = {}

        # Group episodes by questline
        for episode in episodes:
            category = episode['category']
            if category not in questline_episodes:
                questline_episodes[category] = []
            questline_episodes[category].append(episode)

        # Create index for each questline
        for category, eps in questline_episodes.items():
            self.create_questline_index(category, eps, output_dir)

    def create_questline_index(self, category: str, episodes: List[Dict], output_dir: Path):
        """Create a questline index page"""
        questline_info = self.questline_definitions.get(category, {})
        questline_dir = output_dir / 'questlines'
        questline_dir.mkdir(parents=True, exist_ok=True)

        html_content = f"""<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Questline: {category.replace('-', ' ').title()} - Digital Dreamscape</title>
    <style>
        /* Digital Dreamscape Visual Grammar */
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

        /* Header */
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

        /* Questline Hero */
        .questline-hero {{
            background: linear-gradient(135deg, var(--void-dark), var(--void-black));
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 40px;
            text-align: center;
        }}

        .questline-badge {{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 50px;
            padding: 12px 24px;
            margin-bottom: 20px;
        }}

        .questline-icon {{
            font-size: 1.5rem;
        }}

        .questline-id {{
            font-weight: bold;
            color: var(--energy-primary);
            font-family: 'JetBrains Mono', monospace;
        }}

        .questline-title {{
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--energy-primary), var(--energy-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }}

        .questline-meta {{
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

        .questline-description {{
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
        }}

        /* Episodes Grid */
        .episodes-section {{
            background: var(--void-surface);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 16px;
            padding: 40px;
            margin-bottom: 40px;
        }}

        .episodes-title {{
            font-size: 2rem;
            font-weight: 700;
            color: var(--energy-primary);
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(99, 102, 241, 0.3);
            padding-bottom: 10px;
        }}

        .episodes-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }}

        .episode-card {{
            background: var(--void-dark);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }}

        .episode-card:hover {{
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2);
            border-color: var(--energy-primary);
        }}

        .episode-header {{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }}

        .episode-id {{
            font-family: 'JetBrains Mono', monospace;
            font-weight: bold;
            color: var(--energy-primary);
            font-size: 1.1rem;
        }}

        .episode-status {{
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: var(--text-muted);
            background: rgba(99, 102, 241, 0.1);
            padding: 4px 8px;
            border-radius: 12px;
        }}

        .episode-title {{
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
            line-height: 1.3;
        }}

        .episode-description {{
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-bottom: 15px;
        }}

        .episode-meta {{
            font-size: 0.85rem;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }}

        /* Footer */
        .questline-footer {{
            text-align: center;
            padding: 40px 0;
            border-top: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }}
    </style>
</head>

<body>
    <!-- Site Header -->
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">🌌 Digital Dreamscape</h1>
        </div>
    </header>

    <div class="container">
        <!-- Questline Hero -->
        <section class="questline-hero">
            <div class="questline-badge">
                <span class="questline-icon">{questline_info.get('icon', '📄')}</span>
                <span class="questline-id">QUESTLINE</span>
                <span>{category.replace('-', ' ').title()}</span>
            </div>

            <h1 class="questline-title">{questline_info.get('title', category.replace('-', ' '))}</h1>

            <div class="questline-meta">
                <div class="meta-item">
                    <span>🏛️</span>
                    <span>Domain: {questline_info.get('domain', 'execution')}</span>
                </div>
                <div class="meta-item">
                    <span>📅</span>
                    <span>Era: 2026</span>
                </div>
                <div class="meta-item">
                    <span>⚡</span>
                    <span>State: active</span>
                </div>
                <div class="meta-item">
                    <span>📊</span>
                    <span>Episodes: {len(episodes)}</span>
                </div>
            </div>

            <div class="questline-description">
                <p>{questline_info.get('description', f'agent coordination in {category.replace("-", " ")} domain')}</p>
            </div>
        </section>

        <!-- Episodes Grid -->
        <section class="episodes-section">
            <h2 class="episodes-title">episode archive</h2>
            <div class="episodes-grid">"""

        for episode in episodes[:50]:  # Limit to first 50 for performance
            metadata = episode['metadata']
            episode_id = episode['episode_id']
            is_canon = metadata['status'] == 'resolved'

            html_content += f"""
                <a href="../episodes/{episode_id.lower().replace('-', '_')}_{category}_session.html" class="episode-card">
                    <div class="episode-header">
                        <span class="episode-id">{episode_id}</span>
                        <span class="episode-status">{'canon' if is_canon else 'active'}</span>
                    </div>
                    <h3 class="episode-title">{metadata['title'][:60]}{'...' if len(metadata['title']) > 60 else ''}</h3>
                    <p class="episode-description">{metadata['summary'][:100]}{'...' if len(metadata['summary'] or '') > 100 else ''}</p>
                    <div class="episode-meta">agent: {metadata['agent'] or 'swarm'} • {metadata['date'] or '2026'}</div>
                </a>"""

        html_content += """
            </div>
        </section>

        <!-- Footer -->
        <footer class="questline-footer">
            <p>questline: """ + category.replace('-', ' ') + """ • distributed intelligence active</p>
            <p>nothing is lost • systems evolve • agents coordinate</p>
        </footer>
    </div>
</body>

</html>"""

        filepath = questline_dir / f"{category}.html"
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(html_content)

        print(f"Created questline index: {filepath}")

def main():
    converter = DevlogEpisodeConverter("D:/websites/digitaldreamscape.site")

    # Collect all devlog files
    devlog_dirs = [
        Path("D:/Agent_Cellphone_V2_Repository/devlogs"),
        Path("D:/Agent_Cellphone_V2_Repository/swarm_brain/devlogs")
    ]

    all_devlog_files = []
    for devlog_dir in devlog_dirs:
        if devlog_dir.exists():
            all_devlog_files.extend(devlog_dir.rglob("*.md"))

    print(f"Found {len(all_devlog_files)} devlog files to process")

    # Process in batches to avoid memory issues
    batch_size = 100
    all_episodes = []

    for i in range(0, len(all_devlog_files), batch_size):
        batch = all_devlog_files[i:i + batch_size]
        print(f"Processing batch {i//batch_size + 1} of {len(all_devlog_files)//batch_size + 1}")

        episodes = converter.process_batch(batch, batch_size)
        all_episodes.extend(episodes)

        print(f"Batch completed: {len(episodes)} episodes generated")

    print(f"Total episodes processed: {len(all_episodes)}")

    # Generate HTML files
    episodes_dir = Path("D:/websites/digitaldreamscape.site/episodes")
    converter.generate_episodes(all_episodes, episodes_dir)

    # Create questline indexes
    converter.create_questline_indexes(all_episodes, Path("D:/websites/digitaldreamscape.site"))

    print("Episode generation complete!")

if __name__ == "__main__":
    main()