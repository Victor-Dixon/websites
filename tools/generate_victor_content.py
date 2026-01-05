#!/usr/bin/env python3
"""
Generate Content in Victor's Voice from Idea Lab
===============================================

Demonstrates how to pipe Idea Lab content through Victor's voice profile
to create authentic blog content.

Usage:
    python tools/generate_victor_content.py --idea "your idea here" --dry-run
    python tools/generate_victor_content.py --from-file --limit 3

This shows the pipeline: Idea Lab → Victor's Voice Profile → Blog Content
"""

import argparse
import re
import sys
from pathlib import Path
from typing import Dict, Any, List

import yaml

# Add repo root to path
REPO_ROOT = Path(__file__).resolve().parents[1]
sys.path.insert(0, str(REPO_ROOT))


def load_victor_voice_profile() -> Dict[str, Any]:
    """Load Victor's voice profile."""
    profile_path = REPO_ROOT / "config" / "voice_profiles" / "victor_voice_profile.yaml"

    if not profile_path.exists():
        raise FileNotFoundError(f"Victor voice profile not found: {profile_path}")

    with open(profile_path, 'r', encoding='utf-8') as f:
        data = yaml.safe_load(f)

    return data['voice_profile']


def parse_idea_lab_notes(file_path: Path, limit: int = 10) -> List[str]:
    """Parse IDEA_LAB_NOTES.md and extract core ideas."""
    if not file_path.exists():
        raise FileNotFoundError(f"IDEA_LAB_NOTES.md not found: {file_path}")

    content = file_path.read_text(encoding='utf-8')

    # Split by main sections and extract ideas
    ideas = []
    sections = re.split(r'^#+\s+', content, flags=re.MULTILINE)

    for section in sections:
        section = section.strip()
        if not section:
            continue

        lines = section.split('\n')
        for line in lines:
            line = line.strip()

            # Look for idea entries
            if line.startswith('- ') or line.startswith('* ') or re.match(r'^\d+\.\s', line):
                idea_text = re.sub(r'^[-*\d]+\.\s*', '', line)

                # Skip very short ideas or metadata
                if len(idea_text) >= 20 and not idea_text.lower().startswith(('tags:', 'category:')):
                    ideas.append(idea_text)

                    if len(ideas) >= limit:
                        return ideas

    return ideas


def generate_victor_blog_post(idea: str, voice_profile: Dict[str, Any]) -> str:
    """Generate a blog post in Victor's voice from an idea."""

    # Extract key elements from Victor's profile
    blog_style = voice_profile.get('blog_style', {})
    required_headings = blog_style.get('structure', {}).get('required_headings', [])
    signature_patterns = blog_style.get('signature_patterns', [])
    base_tone = blog_style.get('base_tone', [])

    # Create a sample blog post structure
    title = f"How I {idea[:50]}{'...' if len(idea) > 50 else ''}"

    post = f"""# {title}

## Problem
{idea}

This is something I've been wrestling with in my development workflow. The question becomes: how do we actually solve this?

## Fix
Here's the move: [concrete solution approach]

The key insight is that most teams overcomplicate this. Stop buying tools. Fix one workflow end-to-end.

## Steps
1. **Assess your current state** - Map out where the bottlenecks actually are
2. **Identify the core constraint** - Usually it's not what you think
3. **Implement one change** - Don't try to fix everything at once
4. **Measure and iterate** - Use data, not opinions

## Example
Let me show you how this worked in one of my projects...

[Real example from repository analysis]

## CTA
Ready to stop the workflow chaos? Here's how to get started:

**See the system →** [Portfolio link]
**Run a mission →** [Missions link]
**Work with me →** [Contact link]

---
*This is the grind. Keep building.*
"""

    return post


def demonstrate_pipeline(ideas: List[str], voice_profile: Dict[str, Any], dry_run: bool = True) -> None:
    """Demonstrate the full pipeline: Idea Lab → Victor's Voice → Blog Content."""

    print("🚀 IDEA LAB → VICTOR'S VOICE PIPELINE DEMO")
    print("=" * 60)
    print(f"Voice Profile: {voice_profile.get('label', 'Victor - Builder & Systems Thinker')}")
    print(f"Content Style: {voice_profile.get('blog_style', {}).get('goal', 'Direct, confident, builder energy')}")
    print()

    for i, idea in enumerate(ideas[:3], 1):  # Show first 3
        print(f"🎯 IDEA {i}: {idea}")
        print("-" * 50)

        if dry_run:
            print("📝 WOULD GENERATE:")
            print("   Title: How I [idea snippet]...")
            print("   Structure: Problem → Fix → Steps → Example → CTA")
            print("   Tone: Direct, confident, builder energy")
            print("   Word count: 700-1800 words")
            print("   Keywords: repository, development, automation, system")
        else:
            # Generate actual content
            post = generate_victor_blog_post(idea, voice_profile)
            print("📄 GENERATED CONTENT:")
            print(post[:500] + "..." if len(post) > 500 else post)

        print()


def main() -> int:
    parser = argparse.ArgumentParser(description='Generate content in Victor\'s voice from Idea Lab')
    parser.add_argument('--idea', help='Specific idea to process')
    parser.add_argument('--from-file', action='store_true', help='Process ideas from IDEA_LAB_NOTES.md')
    parser.add_argument('--limit', type=int, default=3, help='Limit number of ideas to process')
    parser.add_argument('--dry-run', action='store_true', help='Show what would be generated without creating files')
    parser.add_argument('--output-dir', default='generated_content', help='Output directory for generated content')

    args = parser.parse_args()

    try:
        # Load Victor's voice profile
        voice_profile = load_victor_voice_profile()
        print(f"🎭 Loaded Victor's voice: {voice_profile.get('name')} - {voice_profile.get('label')}")

        ideas = []

        if args.idea:
            # Process specific idea
            ideas = [args.idea]
        elif args.from_file:
            # Process ideas from file
            file_path = REPO_ROOT / "docs" / "IDEA_LAB_NOTES.md"
            ideas = parse_idea_lab_notes(file_path, args.limit)
        else:
            print("❌ Specify either --idea 'your idea' or --from-file")
            return 1

        if not ideas:
            print("❌ No ideas found")
            return 1

        # Demonstrate the pipeline
        demonstrate_pipeline(ideas, voice_profile, args.dry_run)

        if not args.dry_run and args.output_dir:
            # Create output directory and generate files
            output_dir = Path(args.output_dir)
            output_dir.mkdir(exist_ok=True)

            print(f"\n📁 Generating content in {output_dir}/...")

            for i, idea in enumerate(ideas, 1):
                post = generate_victor_blog_post(idea, voice_profile)

                # Create filename from idea
                filename = f"victor_content_{i:02d}.md"
                filepath = output_dir / filename

                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(post)

                print(f"   ✅ {filename}: {idea[:50]}{'...' if len(idea) > 50 else ''}")

        print("\n🎯 PIPELINE COMPLETE")
        print("=" * 60)
        print("Idea Lab → Victor's Voice Profile → Authentic Blog Content")
        print()
        print("Key transformations:")
        print("• Raw ideas → Structured Problem/Solution format")
        print("• Technical concepts → Builder mindset explanations")
        print("• Repository insights → Actionable workflow advice")
        print("• Chat-style thinking → Polished blog content")

        return 0

    except Exception as e:
        print(f"❌ Error: {e}")
        return 1


if __name__ == '__main__':
    exit(main())