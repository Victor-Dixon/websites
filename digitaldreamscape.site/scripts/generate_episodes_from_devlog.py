#!/usr/bin/env python3
"""
Episode Generator for Digital Dreamscape
Converts devlog entries into episode format
"""

import os
import re
from datetime import datetime
from pathlib import Path

class EpisodeGenerator:
    def __init__(self, devlog_path, template_path, output_dir):
        self.devlog_path = Path(devlog_path)
        self.template_path = Path(template_path)
        self.output_dir = Path(output_dir)
        self.episode_counter = 145  # Starting after our template episode

    def parse_devlog(self):
        """Parse devlog.md and extract episode data"""
        with open(self.devlog_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Split by cycle headers
        cycles = re.split(r'^## (.+?)$', content, flags=re.MULTILINE)[1:]

        episodes = []
        for i in range(0, len(cycles), 2):
            if i + 1 < len(cycles):
                title = cycles[i].strip()
                body = cycles[i + 1].strip()

                # Extract cycle number
                cycle_match = re.search(r'Cycle (\d+)', title)
                cycle_num = int(cycle_match.group(1)) if cycle_match else len(episodes) + 1

                episodes.append({
                    'cycle': cycle_num,
                    'title': title,
                    'content': body,
                    'episode_id': f"EP-{self.episode_counter:03d}",
                    'questline': 'documentation-cleanup'
                })
                self.episode_counter += 1

        return episodes

    def extract_episode_sections(self, content):
        """Extract structured sections from episode content"""
        sections = {}

        # Files Deleted section
        files_match = re.search(r'### Files Deleted \((.+?)\)(.+?)(?=###|\n---|\n##|\Z)', content, re.DOTALL)
        if files_match:
            sections['files_deleted'] = {
                'count': files_match.group(1),
                'details': files_match.group(2).strip()
            }

        # SSOT Consolidation section
        ssot_match = re.search(r'### SSOT Consolidation(.+?)(?=###|\n---|\n##|\Z)', content, re.DOTALL)
        if ssot_match:
            sections['ssot_consolidation'] = ssot_match.group(1).strip()

        # Impact section
        impact_match = re.search(r'### Impact(.+?)(?=###|\n---|\n##|\Z)', content, re.DOTALL)
        if impact_match:
            sections['impact'] = impact_match.group(1).strip()

        # Status section
        status_match = re.search(r'### Status(.+?)(?=###|\n---|\n##|\Z)', content, re.DOTALL)
        if status_match:
            sections['status'] = status_match.group(1).strip()

        return sections

    def generate_episode_html(self, episode_data, sections):
        """Generate HTML for a single episode"""

        # Extract key information for episode structure
        cycle_num = episode_data['cycle']
        title = episode_data['title']
        episode_id = episode_data['episode_id']

        # Determine episode state based on content
        if 'COMPLETE' in title:
            state = 'resolved'
            canon = True
        else:
            state = 'active'
            canon = False

        # Calculate progress (cycles 1-7 out of estimated 8-10 total)
        progress_percent = min(100, (cycle_num / 8) * 100)

        # Extract key artifacts from sections
        artifacts_created = []
        if 'files_deleted' in sections:
            count_match = re.search(r'(\d+) violations? removed', sections['files_deleted']['count'])
            if count_match:
                artifacts_created.append(f"{count_match.group(1)} redundant files eliminated")

        if 'ssot_consolidation' in sections:
            ssot_lines = [line.strip('- ✅ ') for line in sections['ssot_consolidation'].split('\n') if line.strip().startswith('- ✅')]
            artifacts_created.extend(ssot_lines[:3])  # Limit to top 3

        # Extract open loops (next targets)
        open_loops = []
        if sections.get('status'):
            next_match = re.search(r'### Next Cycle Targets(.+?)(?=###|\n---|\n##|\Z)', sections['status'], re.DOTALL)
            if next_match:
                targets = re.findall(r'\d+\. \*\*(.+?)\*\*', next_match.group(1))
                open_loops.extend(targets[:3])  # Top 3 next targets

        html_content = f"""<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{episode_id}: {title.replace('✅ COMPLETE', '').strip()} - Digital Dreamscape</title>
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

        /* Loop Binding */
        .loop-binding {{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }}

        .binding-section h3 {{
            font-size: 1.1rem;
            color: var(--energy-secondary);
            margin-bottom: 15px;
            font-family: 'JetBrains Mono', monospace;
        }}

        .binding-section ul {{
            margin: 0;
            padding-left: 20px;
        }}

        .binding-section li {{
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }}

        /* Episode Closure */
        .episode-closure {{
            background: rgba(99, 102, 241, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 25px;
            margin-top: 40px;
            text-align: center;
            font-family: 'JetBrains Mono', monospace;
        }}

        .episode-closure p {{
            margin: 0;
            color: var(--text-primary);
            line-height: 1.6;
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
            width: {int(progress_percent)}%;
        }}

        .progress-text {{
            font-size: 0.9rem;
            color: var(--text-muted);
        }}

        .related-episodes {{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }}

        .related-episode {{
            background: var(--void-surface);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }}

        .related-episode:hover {{
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2);
        }}

        .related-episode .episode-id {{
            font-size: 0.8rem;
            color: var(--energy-primary);
            margin-bottom: 10px;
        }}

        .related-episode h4 {{
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: var(--text-primary);
        }}

        .related-episode p {{
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 15px;
        }}

        .related-episode .status {{
            font-size: 0.8rem;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }}

        /* Navigation */
        .episode-navigation {{
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }}

        .nav-link {{
            flex: 1;
            background: var(--void-surface);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            padding: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }}

        .nav-link:hover {{
            transform: translateY(-3px);
            border-color: var(--energy-primary);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.2);
        }}

        .nav-direction {{
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 5px;
            font-family: 'JetBrains Mono', monospace;
        }}

        .nav-title {{
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }}

        .nav-description {{
            font-size: 0.9rem;
            color: var(--text-secondary);
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
                <span class="episode-icon">📚</span>
                <span class="episode-id">{episode_id}</span>
                <span>Episode</span>
            </div>

            <h1 class="episode-title">documentation cleanup cycle {cycle_num}</h1>

            <div class="episode-meta">
                <div class="meta-item">
                    <span>📚</span>
                    <span>Questline: documentation-cleanup</span>
                </div>
                <div class="meta-item">
                    <span>📅</span>
                    <span>Era: 2026</span>
                </div>
                <div class="meta-item">
                    <span>⚡</span>
                    <span>State: {state}</span>
                </div>
                <div class="meta-item">
                    <span>📝</span>
                    <span>Source: devlog</span>
                </div>
            </div>

            <div class="episode-excerpt">
                <p>documentation entropy reduction. cycle {cycle_num} of systematic cleanup. redundant files identified and eliminated.</p>
            </div>
        </section>

        <!-- Episode Content -->
        <section class="episode-content">
            <!-- Canon Declaration -->
            {'<div class="canon-authority"><div class="canon-seal">[CANON AUTHORITY GRANTED]</div><p>This episode establishes binding precedent for documentation cleanup protocols. Future development assumes these consolidation patterns as standard operating procedure.</p></div>' if canon else ''}

            <h2>[SYSTEM STATE]</h2>
            <p>documentation entropy had accumulated across the codebase.</p>
            <p>redundant files and overlapping documentation created maintenance overhead.</p>
            <p>system pressure revealed the need for consolidation.</p>

            <h2>[EXECUTION LOG]</h2>
            <p>systematic file analysis was applied across all directories.</p>
            <p>duplicate and redundant documentation was identified.</p>
            <p>single sources of truth were established and maintained.</p>

            <h2>[ARTIFACTS FORGED]</h2>
            <ul>
"""

        # Add artifacts
        for artifact in artifacts_created[:5]:  # Limit to 5 artifacts
            html_content += f"                <li>{artifact}</li>\n"

        html_content += f"""
            </ul>

            <h2>[OPEN LOOPS IDENTIFIED]</h2>
"""

        # Add open loops
        if open_loops:
            for loop in open_loops:
                html_content += f"            <p>{loop}</p>\n"
        else:
            html_content += f"            <p>cleanup cycle {cycle_num + 1} targets identified.</p>\n"

        # Add loop binding section
        html_content += f"""
            <h2>[FUTURE STATE BINDING]</h2>
            <div class="loop-binding">
                <div class="binding-section">
                    <h3>🔒 NOW BLOCKED</h3>
                    <ul>
                        <li>documentation entropy accumulation</li>
                        <li>file redundancy proliferation</li>
                        <li>overlapping maintenance paths</li>
                    </ul>
                </div>
                <div class="binding-section">
                    <h3>🔓 NOW UNLOCKED</h3>
                    <ul>
                        <li>cycle {cycle_num + 1} documentation cleanup</li>
                        <li>consolidation template standardization</li>
                        <li>mission system overlap resolution</li>
                    </ul>
                </div>
            </div>

            <h2>[ARCHIVAL NOTE]</h2>
            <p>this cleanup cycle was part of systematic codebase stewardship.</p>
            <p>each cycle reduced complexity while preserving essential information.</p>
            <p>the system surfaces what requires attention through entropy patterns.</p>

            <div class="episode-closure">
                <p>episode state: {state}<br>
                questline progression: {cycle_num}/8 complete<br>
                {'canon authority granted' if canon else 'future dependencies established'}</p>
            </div>
        </section>

        <!-- Questline Integration -->
        <section class="questline-section">
            <div class="questline-header">
                <h2 class="questline-title">questline: documentation-cleanup</h2>
                <div class="questline-progress">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <span class="progress-text">{cycle_num}/8 complete</span>
                </div>
            </div>

            <p>this episode advances the documentation-cleanup questline. cycle {cycle_num} of systematic codebase consolidation. entropy reduction protocols established.</p>
        </section>

        <!-- Footer -->
        <footer class="episode-footer">
            <p>{episode_id} • documentation-cleanup questline • {'canon authority granted' if canon else 'state recorded'}</p>
            <p>nothing is lost • systems evolve • binding precedent established</p>
        </footer>
    </div>
</body>

</html>"""

        return html_content

    def generate_all_episodes(self):
        """Generate all episodes from devlog"""
        episodes = self.parse_devlog()

        # Ensure output directory exists
        self.output_dir.mkdir(parents=True, exist_ok=True)

        generated_files = []

        for episode in episodes:
            sections = self.extract_episode_sections(episode['content'])
            html_content = self.generate_episode_html(episode, sections)

            filename = f"{episode['episode_id'].lower().replace('-', '_')}_documentation_cleanup_cycle_{episode['cycle']}.html"
            filepath = self.output_dir / filename

            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(html_content)

            generated_files.append(filepath)
            print(f"Generated: {filepath}")

        return generated_files

if __name__ == "__main__":
    generator = EpisodeGenerator(
        devlog_path="D:/Mygames/HCshinobi/data/devlog.md",
        template_path="D:/websites/digitaldreamscape.site/episode_sample_design.html",
        output_dir="D:/websites/digitaldreamscape.site/episodes"
    )

    generated_files = generator.generate_all_episodes()
    print(f"\nGenerated {len(generated_files)} episode files in {generator.output_dir}")