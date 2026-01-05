#!/usr/bin/env python3
"""
Convert Episode to Dadudekc Autoblogger Format
=============================================

Converts Digital Dreamscape episodes to dadudekc.com autoblogger compatible format
for routing through the Victor autoblogger system.
"""

import sys
from pathlib import Path
from typing import Dict, Any

# Add config to path for importing
sys.path.insert(0, str(Path(__file__).parent.parent.parent / "config"))
from paths import paths


class DadudekcEpisodeConverter:
    """Converts episodes to dadudekc autoblogger format"""

    def __init__(self):
        self.dadudekc_drafts = paths.content / "drafts" / "dadudekc"
        self.dadudekc_drafts.mkdir(exist_ok=True)

    def convert_episode(self, episode_id: str) -> str:
        """Convert an episode to dadudekc format"""

        # Find the episode file
        episode_pattern = f"{episode_id}_*.md"
        episode_files = list((paths.content / "episodes").glob(episode_pattern))

        if not episode_files:
            raise FileNotFoundError(f"Episode file not found: {episode_pattern}")

        episode_file = episode_files[0]

        # Read episode content
        with open(episode_file, 'r', encoding='utf-8') as f:
            content = f.read()

        # Convert to dadudekc format
        dadudekc_content = self._transform_for_dadudekc(content, episode_id)

        # Save dadudekc version
        dadudekc_filename = f"{episode_id}_dadudekc.md"
        dadudekc_file = self.dadudekc_drafts / dadudekc_filename

        with open(dadudekc_file, 'w', encoding='utf-8') as f:
            f.write(dadudekc_content)

        return str(dadudekc_file)

    def _transform_for_dadudekc(self, content: str, episode_id: str) -> str:
        """Transform episode content for dadudekc autoblogger"""

        lines = content.split('\n')
        transformed_lines = []

        # Add dadudekc header
        transformed_lines.extend([
            "---",
            "layout: post",
            f"title: \"{episode_id}: Digital Dreamscape Episode\"",
            "date: " + self._get_current_date(),
            "categories: digital-dreamscape infrastructure-architecture",
            "tags: [digital-dreamscape, episode, agent-work, infrastructure]",
            "author: victor",
            "featured: true",
            "---",
            "",
            "# Digital Dreamscape Lore Codex - Episode Import",
            "",
            "> *This episode was automatically generated from agent development logs and imported into the Digital Dreamscape universe.*",
            "",
            "---",
            ""
        ])

        # Process content sections
        in_system_state = False
        in_execution_log = False

        for line in lines:
            # Skip the original markdown title
            if line.startswith(f"# {episode_id}:"):
                continue

            # Handle section markers
            if line.startswith("## [SYSTEM STATE]"):
                in_system_state = True
                in_execution_log = False
                transformed_lines.extend([
                    "## Episode Metadata",
                    "",
                    "```yaml"
                ])
                continue
            elif line.startswith("## [EXECUTION LOG]"):
                in_system_state = False
                in_execution_log = True
                if transformed_lines[-1] != "":
                    transformed_lines.append("```")
                    transformed_lines.append("")
                transformed_lines.extend([
                    "## Development Log",
                    "",
                    "> *The following content is the original development log that was converted into this episode:*",
                    ""
                ])
                continue
            elif line.startswith("## ["):
                in_system_state = False
                in_execution_log = False
                # Convert section headers
                section_name = line[4:-1]  # Remove ## [ and ]
                transformed_lines.extend([
                    f"## {section_name}",
                    ""
                ])
                continue

            # Process content based on section
            if in_system_state:
                if line.startswith("**") and ":" in line:
                    # Convert metadata to YAML
                    key_value = line.split(":", 1)
                    if len(key_value) == 2:
                        key = key_value[0].replace("**", "").strip()
                        value = key_value[1].replace("**", "").strip()
                        transformed_lines.append(f"{key}: {value}")
                elif line.strip() == "":
                    # End of system state
                    pass
                else:
                    transformed_lines.append(line)
            elif in_execution_log:
                # Keep execution log content but clean it up
                if line.startswith("# "):
                    # Convert headers to smaller headers
                    transformed_lines.append("###" + line[1:])
                else:
                    transformed_lines.append(line)
            else:
                # Regular content
                transformed_lines.append(line)

        # Add footer
        transformed_lines.extend([
            "",
            "---",
            "",
            "## Episode Classification",
            "",
            "- **Questline:** Infrastructure Architecture",
            "- **Artifact Type:** Development Artifact",
            "- **Agent:** Agent-4 (Captain)",
            "- **Canon Status:** Declared Canonical",
            "- **Publication Route:** Dadudekc Autoblogger → Digital Dreamscape",
            "",
            "*This episode contributes to the evolving Digital Dreamscape universe and represents a significant advancement in multi-agent development coordination.*",
            "",
            "📚 [Return to Digital Dreamscape Lore Codex](/blog/)",
            "",
            "---",
            f"*Episode {episode_id} - Automatically published via dadudekc autoblogger routing*"
        ])

        return '\n'.join(transformed_lines)

    def _get_current_date(self) -> str:
        """Get current date in Jekyll format"""
        from datetime import datetime
        return datetime.now().strftime('%Y-%m-%d %H:%M:%S %z')


def main():
    """Main conversion function"""
    if len(sys.argv) < 2:
        print("Usage: python convert_episode_to_dadudekc.py <episode_id>")
        print("Example: python convert_episode_to_dadudekc.py EP-3259")
        sys.exit(1)

    episode_id = sys.argv[1]

    converter = DadudekcEpisodeConverter()

    try:
        output_file = converter.convert_episode(episode_id)
        print(f"🎭 Episode {episode_id} converted for Dadudekc autoblogger!")
        print(f"📄 Output: {output_file}")
        print("✅ Ready for dadudekc autoblogger publication")
        print("\nNext step:")
        print(f"python ops/deployment/publish_with_autoblogger.py --site dadudekc --content-file {output_file}")

    except Exception as e:
        print(f"❌ Conversion failed: {e}")
        sys.exit(1)


if __name__ == "__main__":
    main()