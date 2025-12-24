#!/usr/bin/env python3
"""
Remove Empty Root-Level Site Directories
=========================================

Removes empty root-level site directories after content migration.
"""

import shutil
from pathlib import Path


def remove_empty_directories():
    """Remove empty root-level site directories."""
    root_dir = Path(".")
    
    sites_to_check = [
        "ariajet.site",
        "crosbyultimateevents.com",
        "dadudekc.com",
        "houstonsipqueen.com",
        "prismblossom.online",
        "southwestsecret.com",
    ]
    
    removed = []
    kept = []
    
    print("=" * 70)
    print("REMOVING EMPTY ROOT-LEVEL SITE DIRECTORIES")
    print("=" * 70)
    print()
    
    for site in sites_to_check:
        site_path = root_dir / site
        
        if not site_path.exists():
            continue
        
        # Check for non-hidden items
        items = [item for item in site_path.iterdir() if not item.name.startswith(".")]
        
        if not items:
            print(f"🗑️  Removing empty directory: {site}")
            try:
                shutil.rmtree(site_path)
                removed.append(site)
                print(f"   ✅ Removed successfully")
            except Exception as e:
                print(f"   ❌ Error: {e}")
        else:
            print(f"⚠️  Keeping {site} ({len(items)} items remaining)")
            for item in items:
                print(f"   - {item.name}")
            kept.append(site)
        print()
    
    print("=" * 70)
    print("SUMMARY")
    print("=" * 70)
    print(f"Directories removed: {len(removed)}")
    if removed:
        for site in removed:
            print(f"  ✅ {site}")
    print(f"Directories kept: {len(kept)}")
    if kept:
        for site in kept:
            print(f"  ⚠️  {site}")
    print()
    
    return removed, kept


if __name__ == "__main__":
    removed, kept = remove_empty_directories()
    exit(0 if not kept else 1)

