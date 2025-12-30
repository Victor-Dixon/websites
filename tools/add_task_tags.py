#!/usr/bin/env python3
"""
Add Task Tags to MASTER_TASK_LOG.md
====================================

Adds #onboarding and #swarm-brain tags to relevant tasks for better discoverability.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import re
from pathlib import Path

TASK_LOG_PATH = Path("MASTER_TASK_LOG.md")

# Patterns to detect and their corresponding tags
TAG_PATTERNS = {
    r'onboarding|agent.?2|batch.?4|architecture.?review': '#onboarding',
    r'swarm.?brain|discord|devlog|cycle.?accomplishment': '#swarm-brain',
    r'v2.?compliance|refactor': '#v2-compliance',
    r'analytics|ga4|facebook.?pixel|utm': '#analytics',
    r'seo|meta.?description|title.?tag': '#seo',
    r'website.?quality|text.?rendering|menu': '#website-quality',
    r'deployment|sftp|wordpress': '#deployment',
    r'trading.?robot|trading.?platform': '#trading-robot',
}

def should_add_tag(line: str, tag: str) -> bool:
    """Check if tag should be added (not already present)."""
    # Check if tag already exists
    if tag in line:
        return False
    # Check if task already has tags section
    if '**Tags:**' in line:
        return True  # Add to existing tags
    return True

def add_tags_to_line(line: str) -> str:
    """Add appropriate tags to a task line."""
    # Only process task lines (starting with - [)
    if not line.strip().startswith('- ['):
        return line
    
    detected_tags = []
    line_lower = line.lower()
    
    # Detect tags based on patterns
    for pattern, tag in TAG_PATTERNS.items():
        if re.search(pattern, line_lower, re.IGNORECASE):
            if tag not in detected_tags:
                detected_tags.append(tag)
    
    # If no tags detected, return original line
    if not detected_tags:
        return line
    
    # Check if tags already exist
    if '**Tags:**' in line:
        # Add to existing tags
        existing_tags = re.findall(r'#\w+(?:-\w+)*', line)
        for tag in detected_tags:
            if tag not in existing_tags:
                existing_tags.append(tag)
        # Replace existing tags
        tags_str = ' '.join(sorted(set(existing_tags)))
        line = re.sub(r'\*\*Tags:\*\*.*', f'**Tags:** {tags_str}', line)
    else:
        # Add new tags section before [Agent-X]
        agent_match = re.search(r'\[Agent-[\d\s]+\]', line)
        if agent_match:
            tags_str = ' '.join(sorted(detected_tags))
            line = line[:agent_match.start()] + f' **Tags:** {tags_str} ' + line[agent_match.start():]
        else:
            # Add at end if no agent tag
            tags_str = ' '.join(sorted(detected_tags))
            line = line.rstrip() + f' **Tags:** {tags_str}\n'
    
    return line

def main():
    print("=" * 70)
    print("ADDING TASK TAGS TO MASTER_TASK_LOG.md")
    print("=" * 70)
    print()
    
    if not TASK_LOG_PATH.exists():
        print(f"❌ ERROR: {TASK_LOG_PATH} not found")
        return
    
    # Read the file
    print(f"📄 Reading {TASK_LOG_PATH}...")
    with open(TASK_LOG_PATH, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    print(f"   Found {len(lines)} lines")
    print()
    
    # Process lines
    updated_lines = []
    tags_added_count = 0
    
    for line in lines:
        original_line = line
        updated_line = add_tags_to_line(line)
        
        if updated_line != original_line:
            tags_added_count += 1
        
        updated_lines.append(updated_line)
    
    # Write updated file
    if tags_added_count > 0:
        print(f"💾 Writing updated {TASK_LOG_PATH}...")
        with open(TASK_LOG_PATH, 'w', encoding='utf-8') as f:
            f.writelines(updated_lines)
        
        print(f"   ✅ Added tags to {tags_added_count} tasks")
    else:
        print("   ℹ️  No tags to add (tags may already be present)")
    
    print()
    print("=" * 70)
    print("SUMMARY")
    print("=" * 70)
    print(f"Lines processed: {len(lines)}")
    print(f"Tags added: {tags_added_count}")
    print()
    print("✅ Tagging complete!")

if __name__ == "__main__":
    main()

