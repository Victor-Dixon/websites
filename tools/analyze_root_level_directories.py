#!/usr/bin/env python3
"""
Analyze Root-Level Site Directories
====================================

Determines what remains in root-level site directories and whether they should be kept or removed.
"""

import json
from pathlib import Path


def analyze_root_directories():
    """Analyze what's left in root-level site directories."""
    root_dir = Path(".")
    websites_dir = root_dir / "websites"
    
    # Site directories at root level
    root_sites = [
        "ariajet.site",
        "crosbyultimateevents.com",
        "dadudekc.com",
        "houstonsipqueen.com",
        "prismblossom.online",
        "southwestsecret.com",
    ]
    
    analysis = {}
    
    for site in root_sites:
        root_path = root_dir / site
        nested_path = websites_dir / site if websites_dir.exists() else None
        
        if not root_path.exists():
            continue
        
        # Get files/dirs in root
        root_items = []
        if root_path.exists():
            for item in root_path.iterdir():
                root_items.append({
                    "name": item.name,
                    "type": "directory" if item.is_dir() else "file",
                    "size": item.stat().st_size if item.is_file() else None,
                })
        
        # Check if nested exists
        nested_exists = nested_path.exists() if nested_path else False
        
        analysis[site] = {
            "root_path": str(root_path),
            "nested_path": str(nested_path) if nested_path else None,
            "nested_exists": nested_exists,
            "root_items": root_items,
            "root_item_count": len(root_items),
            "should_keep": len(root_items) > 0,  # Keep if has content
            "should_remove": len(root_items) == 0,  # Remove if empty
        }
    
    return analysis


def main():
    """Main entry point."""
    analysis = analyze_root_directories()
    
    print("=" * 70)
    print("ROOT-LEVEL SITE DIRECTORY ANALYSIS")
    print("=" * 70)
    print()
    
    for site, data in sorted(analysis.items()):
        print(f"📁 {site}")
        print(f"   Root path: {data['root_path']}")
        print(f"   Nested path: {data['nested_path']}")
        print(f"   Items in root: {data['root_item_count']}")
        
        if data['root_items']:
            print("   Contents:")
            for item in data['root_items'][:10]:  # Show first 10
                item_type = "📂" if item['type'] == 'directory' else "📄"
                size_info = f" ({item['size']} bytes)" if item['size'] else ""
                print(f"     {item_type} {item['name']}{size_info}")
            if len(data['root_items']) > 10:
                print(f"     ... and {len(data['root_items']) - 10} more")
        
        if data['should_remove']:
            print("   ⚠️  RECOMMENDATION: REMOVE (empty)")
        elif data['should_keep']:
            print("   ✅ RECOMMENDATION: KEEP (has content)")
        
        print()
    
    # Summary
    total_items = sum(d['root_item_count'] for d in analysis.values())
    empty_dirs = [s for s, d in analysis.items() if d['should_remove']]
    dirs_with_content = [s for s, d in analysis.items() if d['should_keep']]
    
    print("=" * 70)
    print("SUMMARY")
    print("=" * 70)
    print(f"Total root-level site directories: {len(analysis)}")
    print(f"Total items across all directories: {total_items}")
    print(f"Empty directories (can remove): {len(empty_dirs)}")
    if empty_dirs:
        for site in empty_dirs:
            print(f"  - {site}")
    print(f"Directories with content (keep): {len(dirs_with_content)}")
    if dirs_with_content:
        for site in dirs_with_content:
            print(f"  - {site} ({analysis[site]['root_item_count']} items)")
    print()
    
    # Save report
    report_path = Path("docs/consolidation/root_level_directory_analysis.json")
    report_path.parent.mkdir(parents=True, exist_ok=True)
    report_path.write_text(json.dumps(analysis, indent=2), encoding="utf-8")
    print(f"✅ Analysis saved to {report_path}")
    
    return 0


if __name__ == "__main__":
    exit(main())

