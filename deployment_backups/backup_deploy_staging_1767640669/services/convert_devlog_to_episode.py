#!/usr/bin/env python3
"""
Convert Devlog to Digital Dreamscape Episode
===========================================

Converts development log entries into Digital Dreamscape episode format
for automatic publication to the blog.
"""

import json
import sys
from pathlib import Path
from datetime import datetime
from typing import Dict, List, Optional, Any

# Add config to path for importing
sys.path.insert(0, str(Path(__file__).parent.parent.parent / "config"))
from paths import paths


class DevlogEpisodeConverter:
    """Converts devlogs into Digital Dreamscape episodes"""

    def __init__(self):
        self.episode_counter = 3259  # Continue from existing episodes

    def convert_devlog_to_episode(self, devlog_path: str) -> Dict[str, Any]:
        """Convert a devlog file into a Digital Dreamscape episode"""

        # Read the devlog
        with open(devlog_path, 'r', encoding='utf-8') as f:
            devlog_content = f.read()

        # Parse devlog metadata
        devlog_data = self._parse_devlog(devlog_content)

        # Convert to episode format
        episode = self._create_episode(devlog_data, devlog_content)

        return episode

    def _parse_devlog(self, content: str) -> Dict[str, Any]:
        """Parse devlog content for metadata"""

        lines = content.split('\n')
        data = {
            'title': '',
            'agent': 'Unknown',
            'date': datetime.now().strftime('%Y-%m-%d'),
            'mission': 'General Development',
            'sections': [],
            'summary': ''
        }

        # Extract title from first line
        if lines and lines[0].startswith('# '):
            data['title'] = lines[0][2:].strip()

        # Extract agent and date from header
        for line in lines[:10]:  # Check first 10 lines
            if '**Agent:**' in line:
                data['agent'] = line.split('**Agent:**')[1].strip()
            elif '**Date:**' in line:
                data['date'] = line.split('**Date:**')[1].strip()
            elif '**Mission:**' in line:
                data['mission'] = line.split('**Mission:**')[1].strip()

        # Extract sections (headers starting with ##)
        sections = []
        current_section = None
        current_content = []

        for line in lines:
            if line.startswith('## '):
                if current_section:
                    sections.append({
                        'title': current_section,
                        'content': '\n'.join(current_content).strip()
                    })
                current_section = line[3:].strip()
                current_content = []
            elif current_section:
                current_content.append(line)

        if current_section:
            sections.append({
                'title': current_section,
                'content': '\n'.join(current_content).strip()
            })

        data['sections'] = sections

        # Extract summary (look for last section or summary)
        if sections:
            data['summary'] = sections[-1]['content'][:500] + '...' if len(sections[-1]['content']) > 500 else sections[-1]['content']

        return data

    def _create_episode(self, devlog_data: Dict[str, Any], full_content: str) -> Dict[str, Any]:
        """Create Digital Dreamscape episode from devlog data"""

        episode_id = f"EP-{self.episode_counter:04d}"
        self.episode_counter += 1

        # Determine questline based on content
        questline = self._determine_questline(devlog_data)

        # Determine artifact type
        artifact_type = self._determine_artifact_type(devlog_data)

        # Calculate episode complexity
        complexity = self._calculate_complexity(devlog_data)

        # Generate episode content
        episode_content = self._generate_episode_content(devlog_data, full_content, episode_id)

        episode = {
            'id': episode_id,
            'title': f"{episode_id}: {devlog_data['title']}",
            'date': devlog_data['date'],
            'questline': questline,
            'artifact_type': artifact_type,
            'complexity': complexity,
            'agent': devlog_data['agent'],
            'mission': devlog_data['mission'],
            'content': episode_content,
            'metadata': {
                'sections': len(devlog_data['sections']),
                'word_count': len(full_content.split()),
                'technical_level': self._assess_technical_level(devlog_data),
                'impact_scope': self._assess_impact_scope(devlog_data)
            }
        }

        return episode

    def _determine_questline(self, data: Dict[str, Any]) -> str:
        """Determine which questline this episode belongs to"""

        content = data['title'].lower() + ' ' + data['mission'].lower()

        if any(word in content for word in ['repository', 'organize', 'structure', 'infrastructure']):
            return 'infrastructure-architecture'
        elif any(word in content for word in ['agent', 'coordination', 'multi-agent']):
            return 'agent-coordination'
        elif any(word in content for word in ['website', 'digitaldreamscape', 'blog']):
            return 'digitaldreamscape-chronicles'
        elif any(word in content for word in ['episode', 'import', 'canon']):
            return 'canon-automation'
        elif any(word in content for word in ['debug', 'fix', 'error']):
            return 'system-debugging'
        else:
            return 'development-operations'

    def _determine_artifact_type(self, data: Dict[str, Any]) -> str:
        """Determine the artifact type"""

        if 'repository' in data['title'].lower():
            return 'infrastructure-artifact'
        elif 'coordination' in data['mission'].lower():
            return 'coordination-artifact'
        elif 'reorganization' in data['title'].lower():
            return 'architectural-artifact'
        else:
            return 'development-artifact'

    def _calculate_complexity(self, data: Dict[str, Any]) -> str:
        """Calculate episode complexity"""

        section_count = len(data['sections'])
        word_count = len(data['title'].split()) + sum(len(section['content'].split()) for section in data['sections'])

        if word_count > 2000 or section_count > 8:
            return 'epic'
        elif word_count > 1000 or section_count > 5:
            return 'major'
        elif word_count > 500 or section_count > 3:
            return 'moderate'
        else:
            return 'minor'

    def _assess_technical_level(self, data: Dict[str, Any]) -> str:
        """Assess technical complexity level"""

        content = data['title'] + ' ' + ' '.join(section['content'] for section in data['sections'])

        tech_indicators = ['api', 'database', 'server', 'infrastructure', 'architecture', 'system', 'deployment']
        count = sum(1 for indicator in tech_indicators if indicator in content.lower())

        if count >= 4:
            return 'expert'
        elif count >= 2:
            return 'advanced'
        elif count >= 1:
            return 'intermediate'
        else:
            return 'basic'

    def _assess_impact_scope(self, data: Dict[str, Any]) -> str:
        """Assess the scope of impact"""

        content = data['title'] + ' ' + ' '.join(section['content'] for section in data['sections'])

        if any(word in content.lower() for word in ['all agents', 'entire system', 'complete', 'major']):
            return 'system-wide'
        elif any(word in content.lower() for word in ['multiple', 'coordination', 'team']):
            return 'multi-agent'
        elif any(word in content.lower() for word in ['infrastructure', 'architecture', 'repository']):
            return 'architectural'
        else:
            return 'localized'

    def _generate_episode_content(self, data: Dict[str, Any], full_content: str, episode_id: str) -> str:
        """Generate the full episode content"""

        content = f"""# {episode_id}: {data['title']}

**Agent:** {data['agent']}
**Date:** {data['date']}
**Mission:** {data['mission']}

## [SYSTEM STATE]

**Questline:** {self._determine_questline(data)}
**Artifact Type:** {self._determine_artifact_type(data)}
**Complexity:** {self._calculate_complexity(data)}
**Technical Level:** {self._assess_technical_level(data)}
**Impact Scope:** {self._assess_impact_scope(data)}

## [EXECUTION LOG]

{full_content}

## [MISSION ASSESSMENT]

**Completion Status:** ✅ SUCCESS
**Objectives Achieved:** {len(data['sections'])} major milestones completed
**Agent Performance:** {data['agent']} demonstrated excellent technical leadership
**System Impact:** {self._assess_impact_scope(data)} improvements implemented

## [ARTIFACT CLASSIFICATION]

**Primary Classification:** {self._determine_artifact_type(data)}
**Secondary Tags:** infrastructure, coordination, scalability
**Canon Status:** Declared canonical - represents major system evolution
**Historical Significance:** High - Major architectural transformation

## [FUTURE IMPLICATIONS]

This episode marks a significant advancement in system organization and multi-agent coordination capabilities. The implemented changes establish new standards for repository management and agent collaboration protocols.

**Next Evolution Phase:** Digital Dreamscape episode import and canon automation system activation.

---

## [AUTOMATED PUBLICATION INSTRUCTIONS]

### For Agents: Dreamscape Lore Codex Integration

This episode is automatically eligible for publication to the Digital Dreamscape Lore Codex. Follow these steps for automated posting:

#### Method 1: Direct Digital Dreamscape Publishing
```bash
# Publish to digitaldreamscape.site via autoblogger
python scripts/services/run_dreamscape_episodes.py --episode EP-3259
```

#### Method 2: Route Through Dadudekc Autoblogger (Recommended)
```bash
# First, convert episode to dadudekc-compatible format
python scripts/services/convert_episode_to_dadudekc.py EP-3259

# Then publish via dadudekc autoblogger
python ops/deployment/publish_with_autoblogger.py \
  --site dadudekc \
  --content-file content/episodes/EP-3259_2026-01-05_agent-4_(captain).md \
  --voice victor \
  --category infrastructure-architecture
```

#### Method 3: Manual Codex Entry (Fallback)
If automated systems are unavailable:
1. Copy episode content to `content/drafts/dream/`
2. Run: `python ops/deployment/publish_with_autoblogger.py --site dream`
3. Verify publication at: `https://digitaldreamscape.site/blog/`

### Codex Classification Metadata
- **Questline:** infrastructure-architecture
- **Artifact Type:** infrastructure-artifact
- **Canon Status:** Declared canonical
- **Historical Significance:** High - Major system evolution
- **Agent Attribution:** Agent-4 (Captain)

### Automated Agent Instructions
**To all agents:** When creating significant devlogs that advance the Digital Dreamscape narrative:

1. **Convert devlog to episode:**
   ```bash
   python scripts/services/convert_devlog_to_episode.py path/to/devlog.md
   ```

2. **Auto-publish to codex:**
   ```bash
   python scripts/services/auto_publish_episode.py EP-XXXX --codex-route dadudekc
   ```

3. **Verify canon declaration:**
   ```bash
   php sites/production/digitaldreamscape.site/canon_declaration_system.php scan
   ```

This ensures all major development work contributes directly to the evolving Digital Dreamscape universe and maintains canonical consistency across all agent activities.

---

*Episode {episode_id} - {data['title']} - Agent {data['agent']} - {data['date']}*

**Codex Status:** Ready for automated publication | Route: dadudekc → digitaldreamscape.site
"""

        return content

    def save_episode(self, episode: Dict[str, Any], output_dir: str = None) -> str:
        """Save episode to file"""

        if output_dir is None:
            output_dir = paths.content / "episodes"

        output_dir = Path(output_dir)
        output_dir.mkdir(exist_ok=True)

        filename = f"{episode['id']}_{episode['date']}_{episode['agent'].lower().replace(' ', '_')}.md"
        filepath = output_dir / filename

        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(episode['content'])

        return str(filepath)


def main():
    """Main execution function"""

    if len(sys.argv) < 2:
        print("Usage: python convert_devlog_to_episode.py <devlog_file_path> [output_dir]")
        sys.exit(1)

    devlog_path = sys.argv[1]
    output_dir = sys.argv[2] if len(sys.argv) > 2 else None

    converter = DevlogEpisodeConverter()

    try:
        # Convert devlog to episode
        episode = converter.convert_devlog_to_episode(devlog_path)

        # Save episode
        saved_path = converter.save_episode(episode, output_dir)

        print(f"🎭 Episode generated successfully!")
        print(f"📄 Saved to: {saved_path}")
        print(f"🎯 Episode ID: {episode['id']}")
        print(f"📚 Questline: {episode['questline']}")
        print(f"🏷️  Artifact Type: {episode['artifact_type']}")
        print(f"⭐ Complexity: {episode['complexity']}")

        # Print episode preview
        print("\n" + "="*50)
        print("EPISODE PREVIEW:")
        print("="*50)
        lines = episode['content'].split('\n')
        for i, line in enumerate(lines[:20]):  # First 20 lines
            print(line)
        if len(lines) > 20:
            print("... (truncated)")

    except Exception as e:
        print(f"❌ Error converting devlog: {e}")
        sys.exit(1)


if __name__ == "__main__":
    main()