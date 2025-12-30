#!/usr/bin/env python3
"""
Archive Completed Tasks from MASTER_TASK_LOG.md
================================================

Moves all completed tasks to an archive section and updates project state.

Author: Agent-5 (Business Intelligence Specialist)
Date: 2025-12-22
"""

import re
from pathlib import Path
from datetime import datetime

TASK_LOG_PATH = Path("MASTER_TASK_LOG.md")
COMPLETED_INDICATORS = [
    r'\[x\]',
    r'\[X\]',
    r'✅',
    r'COMPLETE',
    r'COMPLETED',
    r'DONE',
    r'FIXES DEPLOYED',
    r'DEPLOYED',
    r'FIXED',
]

def is_completed_task(line: str) -> bool:
    """Check if a task line indicates completion."""
    line_upper = line.upper()
    return any(
        re.search(pattern, line, re.IGNORECASE) is not None
        for pattern in COMPLETED_INDICATORS
    )

def extract_section_name(line: str) -> str:
    """Extract section name from markdown header."""
    match = re.match(r'^(#{1,3})\s+(.+)$', line)
    if match:
        level, name = match.groups()
        return name.strip()
    return None

def main():
    print("=" * 70)
    print("ARCHIVING COMPLETED TASKS FROM MASTER_TASK_LOG.md")
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
    
    # Separate completed and active tasks
    active_lines = []
    completed_lines = []
    archive_section_started = False
    
    current_section = "ROOT"
    in_completed_section = False
    
    for i, line in enumerate(lines):
        # Check if we're entering the archive section
        if line.strip() == "## COMPLETED TASKS ARCHIVE":
            archive_section_started = True
            active_lines.append(line)
            continue
        
        # Extract section headers
        section_match = re.match(r'^(#{2,3})\s+(.+)$', line)
        if section_match:
            level, section_name = section_match.groups()
            current_section = section_name.strip()
            if archive_section_started:
                in_completed_section = True
            else:
                in_completed_section = False
        
        # Check if task is completed (only for task lines, not in archive section)
        if not in_completed_section and line.strip().startswith('- ['):
            if is_completed_task(line):
                completed_lines.append((i, line, current_section))
                continue
        
        # Keep all non-completed lines in active
        active_lines.append(line)
    
    print(f"   Found {len(completed_lines)} completed tasks")
    print()
    
    # Create archive section
    if completed_lines:
        archive_header = [
            "\n",
            "## COMPLETED TASKS ARCHIVE\n",
            "\n",
            f"**Last Updated:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n",
            f"**Total Archived Tasks:** {len(completed_lines)}\n",
            "\n",
            "---\n",
            "\n"
        ]
        
        # Group by section
        by_section = {}
        for line_num, line, section in completed_lines:
            if section not in by_section:
                by_section[section] = []
            by_section[section].append(line)
        
        # Append archive to active lines
        active_lines.extend(archive_header)
        
        for section_name in sorted(by_section.keys()):
            active_lines.append(f"### {section_name}\n\n")
            for task_line in by_section[section_name]:
                active_lines.append(task_line)
            active_lines.append("\n")
    
    # Write updated file
    print(f"💾 Writing updated {TASK_LOG_PATH}...")
    with open(TASK_LOG_PATH, 'w', encoding='utf-8') as f:
        f.writelines(active_lines)
    
    print(f"   ✅ Archived {len(completed_lines)} completed tasks")
    print(f"   ✅ Updated file with {len(active_lines)} lines")
    print()
    
    # Summary
    print("=" * 70)
    print("SUMMARY")
    print("=" * 70)
    print(f"Completed tasks archived: {len(completed_lines)}")
    print(f"Sections with completed tasks: {len(by_section)}")
    print()
    print("✅ Archive complete!")

if __name__ == "__main__":
    main()

