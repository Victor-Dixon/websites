#!/usr/bin/env python3
"""
Process Blog Post in Sections
==============================

Processes a long blog post in manageable sections to avoid timeouts.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
from voice_pattern_processor import VoicePatternProcessor


def split_into_sections(content: str, max_section_size: int = 800) -> list:
    """Split content into manageable sections."""
    paragraphs = content.split('\n\n')
    sections = []
    current_section = []
    current_size = 0
    
    for para in paragraphs:
        para_size = len(para)
        if current_size + para_size > max_section_size and current_section:
            sections.append('\n\n'.join(current_section))
            current_section = [para]
            current_size = para_size
        else:
            current_section.append(para)
            current_size += para_size
    
    if current_section:
        sections.append('\n\n'.join(current_section))
    
    return sections


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(description='Process post in sections')
    parser.add_argument('--file', type=str, required=True, help='Input file')
    parser.add_argument('--title', type=str, default='', help='Post title')
    parser.add_argument('--output', type=str, help='Output file')
    
    args = parser.parse_args()
    
    # Read content
    with open(args.file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Split into sections
    sections = split_into_sections(content, max_section_size=800)
    print(f"📝 Processing {len(sections)} sections...")
    
    # Process each section
    processor = VoicePatternProcessor()
    processed_sections = []
    
    for i, section in enumerate(sections, 1):
        print(f"\n   Processing section {i}/{len(sections)}...")
        processed = processor.apply_voice_patterns(
            section, 
            args.title if i == 1 else "",  # Only include title for first section
            model="mistral:latest"
        )
        processed_sections.append(processed)
        print(f"   ✅ Section {i} processed")
    
    # Combine sections
    result = '\n\n'.join(processed_sections)
    
    # Output
    if args.output:
        with open(args.output, 'w', encoding='utf-8') as f:
            f.write(result)
        print(f"\n✅ All sections processed and saved to {args.output}")
    else:
        print("\n" + "="*60)
        print("PROCESSED CONTENT:")
        print("="*60)
        print(result)


if __name__ == '__main__':
    main()


