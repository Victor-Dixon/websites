#!/usr/bin/env python3
"""
CI Static Scan - Check for Deprecated Import Usage
==================================================

This script scans the codebase for direct imports of deprecated modules
and fails CI if any are found.

Used in Phase 4 to enforce consolidated-only usage.
"""

import sys
import subprocess
import re
from pathlib import Path
from typing import List, Dict, Any

class DeprecatedImportChecker:
    """Checks for deprecated imports that should use consolidated services"""

    DEPRECATED_PATTERNS = [
        {
            'pattern': r'from episode_quality_scorer import',
            'replacement': 'from consolidated_quality_assessment import ConsolidatedQualityAssessmentService',
            'reason': 'Quality assessment consolidated into unified service'
        },
        {
            'pattern': r'from victor_voice_processor import',
            'replacement': 'from consolidated_quality_assessment import ConsolidatedQualityAssessmentService',
            'reason': 'Victor voice processing integrated into quality assessment'
        },
        {
            'pattern': r'from template_engine import',
            'replacement': 'from consolidated_template_service import ConsolidatedTemplateService',
            'reason': 'Template management consolidated into unified service'
        },
        {
            'pattern': r'from content_discovery_service import',
            'replacement': 'from consolidated_content_discovery import ConsolidatedContentDiscoveryService',
            'reason': 'Content discovery consolidated into unified service'
        },
        {
            'pattern': r'from seo_enhancement_processor import',
            'replacement': 'from consolidated_seo_service import ConsolidatedSEOService',
            'reason': 'SEO processing consolidated into unified service'
        }
    ]

    def __init__(self, base_path: Path):
        self.base_path = base_path
        self.violations: List[Dict[str, Any]] = []

    def scan_files(self, directories: List[str] = None) -> bool:
        """Scan files for deprecated imports. Returns True if violations found."""
        if directories is None:
            directories = ['src', 'scripts', 'tests', 'websites']

        for directory in directories:
            dir_path = self.base_path / directory
            if dir_path.exists():
                self._scan_directory(dir_path)

        return len(self.violations) > 0

    def _scan_directory(self, directory: Path):
        """Scan a directory for Python files with deprecated imports"""
        for file_path in directory.rglob('*.py'):
            # Skip certain files that are allowed to import deprecated modules
            skip_files = [
                # Legacy modules themselves (contain warnings)
                'episode_quality_scorer.py',
                'victor_voice_processor.py',
                'template_engine.py',
                'content_discovery_service.py',
                'seo_enhancement_processor.py',
                # Consolidated services (may import legacy for compatibility)
                'consolidated_quality_assessment.py',
                'consolidated_template_service.py',
                'consolidated_content_discovery.py',
                'consolidated_seo_service.py',
                # CI and testing infrastructure
                'check_deprecated_imports.py',
                # Migration and testing scripts
                'migration_matrix.md',
                'test_quality_v2.py'  # Allow during migration period
            ]

            if file_path.name in skip_files:
                continue

            # Skip scripts in ci/ directory except the checker itself
            if 'ci' in file_path.parts and file_path.name != 'check_deprecated_imports.py':
                continue

            self._check_file(file_path)

    def _check_file(self, file_path: Path):
        """Check a single file for deprecated imports"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
        except Exception as e:
            print(f"Warning: Could not read {file_path}: {e}")
            return

        lines = content.split('\n')

        for line_num, line in enumerate(lines, 1):
            for pattern_info in self.DEPRECATED_PATTERNS:
                if re.search(pattern_info['pattern'], line.strip()):
                    self.violations.append({
                        'file': str(file_path.relative_to(self.base_path)),
                        'line': line_num,
                        'line_content': line.strip(),
                        'deprecated_pattern': pattern_info['pattern'],
                        'replacement': pattern_info['replacement'],
                        'reason': pattern_info['reason']
                    })

    def report_violations(self):
        """Report all violations found"""
        if not self.violations:
            print("✅ No deprecated import violations found!")
            return

        print("❌ DEPRECATED IMPORT VIOLATIONS FOUND!")
        print("=" * 60)

        for violation in self.violations:
            print(f"\n📁 {violation['file']}:{violation['line']}")
            print(f"   ❌ {violation['line_content']}")
            print(f"   ✅ Replace with: {violation['replacement']}")
            print(f"   ℹ️  Reason: {violation['reason']}")

        print(f"\n🔴 TOTAL VIOLATIONS: {len(self.violations)}")
        print("\n🚨 CI FAILURE: Fix deprecated imports before merging!")
        print("   Use the migration matrix: docs/migration_matrix.md")

    def get_violation_count(self) -> int:
        """Get the number of violations found"""
        return len(self.violations)


def main():
    """Main CI check function"""
    base_path = Path(__file__).parent.parent.parent

    checker = DeprecatedImportChecker(base_path)

    print("🔍 Scanning for deprecated imports...")
    has_violations = checker.scan_files()

    checker.report_violations()

    # Exit with error code if violations found
    sys.exit(1 if has_violations else 0)


if __name__ == "__main__":
    main()