#!/usr/bin/env python3
"""
Analyze Website Directory Duplicates
=====================================

Identifies duplicate website directories at root vs nested locations.
"""

import json
import os
from pathlib import Path


def analyze_duplicates():
    """Analyze duplicate website directories."""
    root_dir = Path(".")
    websites_dir = root_dir / "websites"
    
    # Get root-level site directories
    root_sites = [
        d.name
        for d in root_dir.iterdir()
        if d.is_dir()
        and "." in d.name
        and d.name not in ["_incoming", ".benchmarks", ".pytest_cache", "websites"]
    ]
    
    # Get nested websites/ directories
    nested_sites = []
    if websites_dir.exists():
        nested_sites = [
            d.name
            for d in websites_dir.iterdir()
            if d.is_dir() and "." in d.name
        ]
    
    # Find duplicates
    duplicates = sorted(set(root_sites) & set(nested_sites))
    root_only = sorted(set(root_sites) - set(nested_sites))
    nested_only = sorted(set(nested_sites) - set(root_sites))
    
    # Build analysis
    analysis = {
        "root_level_sites": sorted(root_sites),
        "nested_websites_sites": sorted(nested_sites),
        "duplicates": duplicates,
        "root_only": root_only,
        "nested_only": nested_only,
        "total_duplicates": len(duplicates),
    }
    
    return analysis


def main():
    """Main entry point."""
    analysis = analyze_duplicates()
    
    print("=" * 70)
    print("WEBSITE DIRECTORY DUPLICATION ANALYSIS")
    print("=" * 70)
    print()
    
    print(f"Root Level Sites: {len(analysis['root_level_sites'])}")
    for site in analysis["root_level_sites"]:
        print(f"  - {site}")
    print()
    
    print(f"Nested websites/ Sites: {len(analysis['nested_websites_sites'])}")
    for site in analysis["nested_websites_sites"]:
        print(f"  - {site}")
    print()
    
    print(f"DUPLICATES FOUND: {analysis['total_duplicates']}")
    for site in analysis["duplicates"]:
        print(f"  ⚠️  {site} (exists in both locations)")
    print()
    
    if analysis["root_only"]:
        print(f"Root Only (not in websites/): {len(analysis['root_only'])}")
        for site in analysis["root_only"]:
            print(f"  - {site}")
        print()
    
    if analysis["nested_only"]:
        print(f"Nested Only (not at root): {len(analysis['nested_only'])}")
        for site in analysis["nested_only"]:
            print(f"  - {site}")
        print()
    
    # Save JSON report
    report_path = Path("docs/consolidation/website_duplicates_analysis.json")
    report_path.parent.mkdir(parents=True, exist_ok=True)
    report_path.write_text(json.dumps(analysis, indent=2), encoding="utf-8")
    print(f"✅ Analysis saved to {report_path}")
    
    return 0


if __name__ == "__main__":
    exit(main())


