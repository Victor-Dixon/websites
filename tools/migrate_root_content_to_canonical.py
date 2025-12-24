#!/usr/bin/env python3
"""
Migrate Root-Level Content to Canonical Structure
=================================================

Moves all remaining content from root-level site directories to canonical
websites/websites/ structure.
"""

import json
import shutil
from pathlib import Path
from typing import Dict, List


def analyze_content_migration():
    """Analyze what needs to be migrated."""
    root_dir = Path(".")
    websites_dir = root_dir / "websites"
    
    sites_to_migrate = {
        "ariajet.site": {
            "description": "Games, static HTML",
            "root_path": root_dir / "ariajet.site",
            "canonical_path": websites_dir / "ariajet.site",
        },
        "crosbyultimateevents.com": {
            "description": "Setup docs, landing pages",
            "root_path": root_dir / "crosbyultimateevents.com",
            "canonical_path": websites_dir / "crosbyultimateevents.com",
        },
        "dadudekc.com": {
            "description": "Blog posts",
            "root_path": root_dir / "dadudekc.com",
            "canonical_path": websites_dir / "dadudekc.com",
        },
        "southwestsecret.com": {
            "description": "Static assets, games, music, theme",
            "root_path": root_dir / "southwestsecret.com",
            "canonical_path": websites_dir / "southwestsecret.com",
        },
    }
    
    migration_plan = {}
    
    for site, info in sites_to_migrate.items():
        root_path = info["root_path"]
        canonical_path = info["canonical_path"]
        
        if not root_path.exists():
            continue
        
        # Get all items in root directory
        items_to_migrate = []
        for item in root_path.iterdir():
            # Skip hidden/system files
            if item.name.startswith('.') and item.name != '.gitignore':
                continue
            
            items_to_migrate.append({
                "name": item.name,
                "type": "directory" if item.is_dir() else "file",
                "path": str(item),
                "target": str(canonical_path / item.name),
            })
        
        migration_plan[site] = {
            "description": info["description"],
            "root_path": str(root_path),
            "canonical_path": str(canonical_path),
            "items": items_to_migrate,
            "item_count": len(items_to_migrate),
        }
    
    return migration_plan


def migrate_content(dry_run: bool = True):
    """Migrate content from root to canonical locations."""
    migration_plan = analyze_content_migration()
    
    results = {
        "migrated": [],
        "errors": [],
        "skipped": [],
    }
    
    print("=" * 70)
    print("ROOT-LEVEL CONTENT MIGRATION TO CANONICAL STRUCTURE")
    print("=" * 70)
    print(f"Mode: {'DRY RUN' if dry_run else 'EXECUTE'}")
    print()
    
    for site, plan in migration_plan.items():
        print(f"📁 {site}")
        print(f"   Description: {plan['description']}")
        print(f"   Items to migrate: {plan['item_count']}")
        print()
        
        canonical_path = Path(plan["canonical_path"])
        
        # Ensure canonical directory exists
        if not dry_run:
            canonical_path.mkdir(parents=True, exist_ok=True)
        
        for item_info in plan["items"]:
            source = Path(item_info["path"])
            target = Path(item_info["target"])
            
            # Check if target already exists
            if target.exists():
                print(f"   ⚠️  SKIP: {item_info['name']} (target exists)")
                results["skipped"].append({
                    "site": site,
                    "item": item_info["name"],
                    "reason": "Target already exists",
                })
                continue
            
            print(f"   {'[DRY RUN] ' if dry_run else ''}Migrate: {item_info['name']}")
            print(f"      From: {source}")
            print(f"      To: {target}")
            
            if not dry_run:
                try:
                    if source.is_dir():
                        shutil.copytree(source, target)
                        # Remove source after successful copy
                        shutil.rmtree(source)
                    else:
                        shutil.copy2(source, target)
                        # Remove source after successful copy
                        source.unlink()
                    
                    results["migrated"].append({
                        "site": site,
                        "item": item_info["name"],
                        "from": str(source),
                        "to": str(target),
                    })
                    print(f"      ✅ Migrated successfully")
                except Exception as e:
                    error_msg = f"Error migrating {item_info['name']}: {e}"
                    print(f"      ❌ {error_msg}")
                    results["errors"].append({
                        "site": site,
                        "item": item_info["name"],
                        "error": str(e),
                    })
            print()
        
        # Check if root directory is now empty (except .git, .gitignore)
        if not dry_run:
            root_path = Path(plan["root_path"])
            remaining = [
                item
                for item in root_path.iterdir()
                if not item.name.startswith(".")
            ]
            if not remaining:
                print(f"   🗑️  Root directory empty - can be removed")
                print()
    
    print("=" * 70)
    print("MIGRATION SUMMARY")
    print("=" * 70)
    print(f"Items migrated: {len(results['migrated'])}")
    print(f"Items skipped: {len(results['skipped'])}")
    print(f"Errors: {len(results['errors'])}")
    print()
    
    if results["errors"]:
        print("Errors:")
        for error in results["errors"]:
            print(f"  - {error['site']}/{error['item']}: {error['error']}")
        print()
    
    # Save results
    results_path = Path("docs/consolidation/root_content_migration_results.json")
    results_path.parent.mkdir(parents=True, exist_ok=True)
    results_path.write_text(json.dumps(results, indent=2), encoding="utf-8")
    print(f"✅ Results saved to {results_path}")
    
    return results


def main():
    """Main entry point."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description="Migrate root-level content to canonical structure"
    )
    parser.add_argument(
        "--execute",
        action="store_true",
        help="Execute migration (default is dry run)",
    )
    
    args = parser.parse_args()
    
    results = migrate_content(dry_run=not args.execute)
    
    if not args.execute:
        print()
        print("💡 This was a DRY RUN. Use --execute to perform actual migration.")
    
    return 0 if not results["errors"] else 1


if __name__ == "__main__":
    exit(main())

