#!/usr/bin/env python3
"""
Complete Root to Canonical Migration
=====================================

Moves ALL remaining content from root-level site directories to canonical
websites/websites/ structure, then removes empty root directories.
"""

import json
import shutil
from pathlib import Path
from typing import Dict, List


def get_all_items(directory: Path, exclude_hidden: bool = True) -> List[Path]:
    """Get all items in a directory."""
    if not directory.exists():
        return []
    
    items = []
    for item in directory.iterdir():
        if exclude_hidden and item.name.startswith(".") and item.name != ".gitignore":
            continue
        items.append(item)
    return items


def migrate_all_content():
    """Migrate all content from root to canonical locations."""
    root_dir = Path(".")
    websites_dir = root_dir / "websites"
    
    sites_to_migrate = [
        "ariajet.site",
        "crosbyultimateevents.com",
        "dadudekc.com",
        "houstonsipqueen.com",
        "prismblossom.online",
        "southwestsecret.com",
    ]
    
    results = {
        "migrated": [],
        "errors": [],
        "removed_directories": [],
    }
    
    print("=" * 70)
    print("COMPLETE ROOT TO CANONICAL MIGRATION")
    print("=" * 70)
    print()
    
    for site in sites_to_migrate:
        root_path = root_dir / site
        canonical_path = websites_dir / site
        
        if not root_path.exists():
            print(f"⏭️  {site}: Does not exist, skipping")
            print()
            continue
        
        # Get all items in root directory
        items = get_all_items(root_path)
        
        if not items:
            print(f"🗑️  {site}: Empty, removing directory")
            try:
                shutil.rmtree(root_path)
                results["removed_directories"].append(site)
                print(f"   ✅ Removed successfully")
            except Exception as e:
                print(f"   ❌ Error removing: {e}")
                results["errors"].append({"site": site, "action": "remove", "error": str(e)})
            print()
            continue
        
        print(f"📁 {site}: {len(items)} items to migrate")
        
        # Ensure canonical directory exists
        canonical_path.mkdir(parents=True, exist_ok=True)
        
        for item in items:
            target = canonical_path / item.name
            
            # Check if target already exists
            if target.exists():
                # Compare if it's the same
                if item.is_file() and target.is_file():
                    if item.stat().st_size == target.stat().st_size:
                        print(f"   ⚠️  SKIP: {item.name} (already exists, same size)")
                        # Remove source since it's a duplicate
                        try:
                            if item.is_dir():
                                shutil.rmtree(item)
                            else:
                                item.unlink()
                            print(f"      ✅ Removed duplicate source")
                        except Exception as e:
                            print(f"      ⚠️  Could not remove source: {e}")
                        continue
                    else:
                        print(f"   ⚠️  CONFLICT: {item.name} exists but different size")
                        # Rename source
                        backup_name = f"{item.name}.backup"
                        target_backup = canonical_path / backup_name
                        if not target_backup.exists():
                            target.rename(target_backup)
                            print(f"      📦 Backed up existing to {backup_name}")
            
            print(f"   Migrate: {item.name}")
            print(f"      From: {item}")
            print(f"      To: {target}")
            
            try:
                if item.is_dir():
                    if target.exists():
                        # Merge directories
                        print(f"      📦 Merging into existing directory")
                        for subitem in item.rglob("*"):
                            if subitem.is_file():
                                rel_path = subitem.relative_to(item)
                                target_file = target / rel_path
                                target_file.parent.mkdir(parents=True, exist_ok=True)
                                if not target_file.exists():
                                    shutil.copy2(subitem, target_file)
                                    print(f"         ✅ Copied: {rel_path}")
                    else:
                        shutil.copytree(item, target)
                        print(f"      ✅ Copied directory")
                    # Remove source
                    shutil.rmtree(item)
                    print(f"      ✅ Removed source")
                else:
                    shutil.copy2(item, target)
                    print(f"      ✅ Copied file")
                    # Remove source
                    item.unlink()
                    print(f"      ✅ Removed source")
                
                results["migrated"].append({
                    "site": site,
                    "item": item.name,
                    "from": str(item),
                    "to": str(target),
                })
            except Exception as e:
                error_msg = f"Error migrating {item.name}: {e}"
                print(f"      ❌ {error_msg}")
                results["errors"].append({
                    "site": site,
                    "item": item.name,
                    "error": str(e),
                })
        
        # Check if root directory is now empty
        remaining = get_all_items(root_path)
        if not remaining:
            print(f"   🗑️  Root directory now empty - removing")
            try:
                shutil.rmtree(root_path)
                results["removed_directories"].append(site)
                print(f"   ✅ Removed successfully")
            except Exception as e:
                print(f"   ⚠️  Could not remove: {e}")
        else:
            print(f"   ⚠️  Root directory still has {len(remaining)} items: {[i.name for i in remaining]}")
        
        print()
    
    print("=" * 70)
    print("MIGRATION SUMMARY")
    print("=" * 70)
    print(f"Items migrated: {len(results['migrated'])}")
    print(f"Directories removed: {len(results['removed_directories'])}")
    print(f"Errors: {len(results['errors'])}")
    print()
    
    if results["removed_directories"]:
        print("Removed directories:")
        for site in results["removed_directories"]:
            print(f"  ✅ {site}")
        print()
    
    if results["errors"]:
        print("Errors:")
        for error in results["errors"]:
            print(f"  ❌ {error['site']}/{error.get('item', 'directory')}: {error['error']}")
        print()
    
    # Save results
    results_path = Path("docs/consolidation/complete_migration_results.json")
    results_path.parent.mkdir(parents=True, exist_ok=True)
    results_path.write_text(json.dumps(results, indent=2), encoding="utf-8")
    print(f"✅ Results saved to {results_path}")
    
    return results


if __name__ == "__main__":
    results = migrate_all_content()
    exit(0 if not results["errors"] else 1)

