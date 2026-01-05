#!/usr/bin/env python3
"""
Cleanup Duplicate Systems - Phase 1 Execution
=============================================

Executes immediate cleanup actions from SYSTEMS_AUDIT_REPORT.md
"""

import os
import sys
from pathlib import Path
import shutil
import hashlib
from typing import Dict, List, Set

class SystemCleanup:
    """Automated cleanup of duplicate systems"""

    def __init__(self):
        self.root_dir = Path(__file__).parent
        self.services_dir = self.root_dir / "scripts" / "services"
        self.backup_dir = self.root_dir / "archive" / "cleanup_backup"

        # Create backup directory
        self.backup_dir.mkdir(parents=True, exist_ok=True)

    def audit_duplicate_files(self) -> Dict[str, List[Path]]:
        """Audit for exact duplicate files"""
        duplicates = {}

        # Check for duplicate service files
        service_files = list(self.services_dir.glob("*.py"))

        # Group by filename
        file_groups = {}
        for file_path in service_files:
            filename = file_path.name
            if filename not in file_groups:
                file_groups[filename] = []
            file_groups[filename].append(file_path)

        # Find duplicates (same filename, different paths)
        for filename, paths in file_groups.items():
            if len(paths) > 1:
                # Check if files are actually identical
                if self._files_identical(paths):
                    duplicates[filename] = paths

        return duplicates

    def _files_identical(self, file_paths: List[Path]) -> bool:
        """Check if multiple files are identical"""
        if len(file_paths) < 2:
            return False

        # Read first file
        try:
            with open(file_paths[0], 'rb') as f:
                first_content = f.read()
            first_hash = hashlib.md5(first_content).hexdigest()
        except Exception:
            return False

        # Compare all others
        for file_path in file_paths[1:]:
            try:
                with open(file_path, 'rb') as f:
                    content = f.read()
                file_hash = hashlib.md5(content).hexdigest()
                if file_hash != first_hash:
                    return False
            except Exception:
                return False

        return True

    def audit_functional_duplicates(self) -> Dict[str, List[str]]:
        """Audit for functional duplicates (same functionality, different names)"""
        functional_duplicates = {
            "victor_voice": [],
            "quality_scorer": [],
            "template_system": [],
            "content_discovery": [],
            "seo_processor": []
        }

        # Scan all Python files for functionality
        all_py_files = []
        all_py_files.extend(self.services_dir.glob("*.py"))
        all_py_files.extend((self.root_dir / "src" / "autoblogger").glob("*.py"))

        for py_file in all_py_files:
            try:
                with open(py_file, 'r', encoding='utf-8') as f:
                    content = f.read().lower()

                filename = str(py_file)

                # Check for Victor voice functionality
                if any(term in content for term in ['victor', 'voice', 'idk', 'tbh']):
                    functional_duplicates["victor_voice"].append(filename)

                # Check for quality scoring
                if any(term in content for term in ['quality', 'score', 'assessment', 'metrics']):
                    functional_duplicates["quality_scorer"].append(filename)

                # Check for template systems
                if any(term in content for term in ['template', 'render', 'block', 'html']):
                    functional_duplicates["template_system"].append(filename)

                # Check for content discovery
                if any(term in content for term in ['discover', 'scan', 'find', 'search']):
                    functional_duplicates["content_discovery"].append(filename)

                # Check for SEO processing
                if any(term in content for term in ['seo', 'keyword', 'meta', 'search']):
                    functional_duplicates["seo_processor"].append(filename)

            except Exception as e:
                print(f"Error reading {py_file}: {e}")

        return functional_duplicates

    def execute_cleanup_phase1(self) -> Dict[str, any]:
        """Execute Phase 1 cleanup actions"""
        results = {
            "files_backed_up": [],
            "files_deleted": [],
            "errors": [],
            "warnings": []
        }

        print("🔍 Starting Phase 1 Cleanup...")
        print("=" * 50)

        # 1. Audit duplicates
        print("\n1. Auditing for duplicates...")
        exact_duplicates = self.audit_duplicate_files()
        functional_duplicates = self.audit_functional_duplicates()

        print(f"Found {len(exact_duplicates)} sets of exact duplicate files")
        print(f"Found functional duplicates in {len(functional_duplicates)} categories")

        # 2. Backup files before deletion
        print("\n2. Creating backups...")
        files_to_backup = []

        # Add exact duplicates (keep first, backup rest)
        for filename, paths in exact_duplicates.items():
            files_to_backup.extend(paths[1:])  # Backup all except first

        # Add known obsolete files
        obsolete_files = [
            self.services_dir / "mass_episode_processor.py",  # Legacy monolithic
        ]

        for obsolete in obsolete_files:
            if obsolete.exists():
                files_to_backup.append(obsolete)

        # Perform backups
        for file_path in files_to_backup:
            try:
                backup_path = self.backup_dir / file_path.name
                shutil.copy2(file_path, backup_path)
                results["files_backed_up"].append(str(file_path))
                print(f"  ✓ Backed up: {file_path.name}")
            except Exception as e:
                results["errors"].append(f"Backup failed for {file_path}: {e}")

        # 3. Delete obsolete files
        print("\n3. Deleting obsolete files...")
        files_to_delete = []

        # Delete exact duplicates (keep first occurrence)
        for filename, paths in exact_duplicates.items():
            files_to_delete.extend(paths[1:])  # Delete all except first

        # Delete known obsolete files
        for obsolete in obsolete_files:
            if obsolete.exists():
                files_to_delete.append(obsolete)

        # Perform deletions
        for file_path in files_to_delete:
            try:
                file_path.unlink()
                results["files_deleted"].append(str(file_path))
                print(f"  ✓ Deleted: {file_path.name}")
            except Exception as e:
                results["errors"].append(f"Deletion failed for {file_path}: {e}")

        # 4. Generate cleanup report
        print("\n4. Generating cleanup report...")
        self._generate_cleanup_report(results, exact_duplicates, functional_duplicates)

        print(f"\n✅ Phase 1 Cleanup Complete")
        print(f"   Backed up: {len(results['files_backed_up'])} files")
        print(f"   Deleted: {len(results['files_deleted'])} files")
        print(f"   Errors: {len(results['errors'])}")

        return results

    def _generate_cleanup_report(self, results: Dict, exact_duplicates: Dict,
                               functional_duplicates: Dict):
        """Generate detailed cleanup report"""

        report_path = self.root_dir / "CLEANUP_REPORT.md"

        with open(report_path, 'w') as f:
            f.write("# Cleanup Report - Phase 1 Execution\n\n")
            f.write(f"Generated: {Path(__file__).name}\n\n")

            f.write("## Summary\n\n")
            f.write(f"- **Files Backed Up:** {len(results['files_backed_up'])}\n")
            f.write(f"- **Files Deleted:** {len(results['files_deleted'])}\n")
            f.write(f"- **Errors:** {len(results['errors'])}\n\n")

            f.write("## Exact Duplicate Files Removed\n\n")
            for filename, paths in exact_duplicates.items():
                f.write(f"### {filename}\n")
                for path in paths:
                    status = "❌ DELETED" if str(path) in results['files_deleted'] else "✅ KEPT"
                    f.write(f"- {status}: {path}\n")
                f.write("\n")

            f.write("## Functional Duplicates Identified\n\n")
            for category, files in functional_duplicates.items():
                if len(files) > 1:
                    f.write(f"### {category.replace('_', ' ').title()}\n")
                    for file in files:
                        f.write(f"- {file}\n")
                    f.write("\n")

            f.write("## Files Backed Up\n\n")
            for file in results['files_backed_up']:
                f.write(f"- {file}\n")

            f.write("\n## Files Deleted\n\n")
            for file in results['files_deleted']:
                f.write(f"- {file}\n")

            if results['errors']:
                f.write("\n## Errors\n\n")
                for error in results['errors']:
                    f.write(f"- {error}\n")

            f.write("\n## Next Steps\n\n")
            f.write("1. Review backed up files before permanent deletion\n")
            f.write("2. Test that remaining systems still function\n")
            f.write("3. Proceed to Phase 2: Architecture consolidation\n")
            f.write("4. See SYSTEMS_AUDIT_REPORT.md for detailed consolidation plan\n")

        print(f"📋 Cleanup report generated: {report_path}")

    def dry_run_cleanup(self) -> Dict[str, any]:
        """Perform dry run to show what would be cleaned up"""
        print("🔍 Dry Run - Phase 1 Cleanup Analysis")
        print("=" * 50)

        exact_duplicates = self.audit_duplicate_files()
        functional_duplicates = self.audit_functional_duplicates()

        print(f"\nExact duplicate files that would be removed:")
        total_to_remove = 0
        for filename, paths in exact_duplicates.items():
            print(f"\n{filename}:")
            for i, path in enumerate(paths):
                if i == 0:
                    print(f"  ✅ KEEP: {path}")
                else:
                    print(f"  ❌ REMOVE: {path}")
                    total_to_remove += 1

        print(f"\nFunctional duplicates identified:")
        for category, files in functional_duplicates.items():
            if len(files) > 1:
                print(f"\n{category.replace('_', ' ').title()}:")
                for file in files:
                    print(f"  - {file}")

        print(f"\nSUMMARY:")
        print(f"- Exact duplicates to remove: {total_to_remove}")
        print(f"- Functional duplicate categories: {len([c for c in functional_duplicates.values() if len(c) > 1])}")
        print(f"- Files would be backed up to: {self.backup_dir}")

        return {
            "exact_duplicates": exact_duplicates,
            "functional_duplicates": functional_duplicates,
            "total_to_remove": total_to_remove
        }

def main():
    """Main cleanup execution"""
    import argparse

    parser = argparse.ArgumentParser(description="Cleanup duplicate content processing systems")
    parser.add_argument('--dry-run', action='store_true', help='Show what would be cleaned without doing it')
    parser.add_argument('--execute', action='store_true', help='Execute Phase 1 cleanup')

    args = parser.parse_args()

    cleanup = SystemCleanup()

    if args.dry_run:
        cleanup.dry_run_cleanup()
    elif args.execute:
        results = cleanup.execute_cleanup_phase1()
        print(f"\nCleanup complete. See CLEANUP_REPORT.md for details.")
    else:
        print("Use --dry-run to preview or --execute to perform cleanup")
        print("\nAvailable actions:")
        print("- python cleanup_duplicate_systems.py --dry-run    # Preview cleanup")
        print("- python cleanup_duplicate_systems.py --execute   # Execute cleanup")

if __name__ == "__main__":
    main()