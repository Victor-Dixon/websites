#!/usr/bin/env python3
"""
Golden Master Test Runner
=========================

Runs regression tests against the golden master baseline.
Compares current pipeline outputs against expected results and reports differences.

Supports normalization rules for whitespace, JSON key order, and other expected variations.
"""

import sys
import json
import difflib
import time
from pathlib import Path
from typing import Dict, List, Any, Optional, Tuple
from dataclasses import dataclass
import statistics

# Add services to path
sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent / "services"))

from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, ContentCategory
from consolidated_seo_service import ConsolidatedSEOService
from consolidated_template_service import ConsolidatedTemplateService
from consolidated_content_discovery import ConsolidatedContentDiscoveryService

@dataclass
class TestResult:
    """Result of running a single golden test"""
    fixture_id: str
    passed: bool
    differences: List[str]
    current_result: Dict[str, Any]
    expected_result: Dict[str, Any]
    processing_time: float
    normalized: bool

@dataclass
class TestSuiteResult:
    """Results of the complete golden test suite"""
    total_tests: int
    passed_tests: int
    failed_tests: int
    average_processing_time: float
    total_processing_time: float
    results: List[TestResult]
    summary: Dict[str, Any]

class GoldenTestRunner:
    """Runs golden master regression tests"""

    def __init__(self, fixtures_dir: Path, baseline_file: Path):
        self.fixtures_dir = fixtures_dir
        self.baseline_file = baseline_file

        # Load baseline
        with open(baseline_file, 'r', encoding='utf-8') as f:
            self.baseline = json.load(f)

        # Initialize services
        self.quality_service = ConsolidatedQualityAssessmentService()
        self.seo_service = ConsolidatedSEOService()
        self.template_service = ConsolidatedTemplateService()
        self.discovery_service = ConsolidatedContentDiscoveryService()

    def run_test_suite(self) -> TestSuiteResult:
        """Run the complete golden test suite"""
        print("🧪 Running Golden Master Test Suite")
        print("=" * 50)

        results = []
        start_time = time.time()

        # Load fixtures
        fixtures = self._load_fixtures()

        for i, fixture in enumerate(fixtures, 1):
            fixture_id = fixture['id']
            print(f"   {i:2d}/{len(fixtures)} Testing: {fixture_id}")

            try:
                result = self._run_single_test(fixture)
                results.append(result)

                status = "✅ PASS" if result.passed else "❌ FAIL"
                print(f"      {status} ({result.processing_time:.3f}s)")

            except Exception as e:
                # Create failed result for exceptions
                result = TestResult(
                    fixture_id=fixture_id,
                    passed=False,
                    differences=[f"Exception: {str(e)}"],
                    current_result={},
                    expected_result=self.baseline['results'].get(fixture_id, {}),
                    processing_time=0.0,
                    normalized=False
                )
                results.append(result)
                print(f"      ❌ FAIL - Exception: {e}")

        total_time = time.time() - start_time

        # Calculate statistics
        passed_tests = sum(1 for r in results if r.passed)
        failed_tests = len(results) - passed_tests
        avg_time = total_time / len(results) if results else 0

        # Create summary
        summary = self._create_summary(results)

        suite_result = TestSuiteResult(
            total_tests=len(results),
            passed_tests=passed_tests,
            failed_tests=failed_tests,
            average_processing_time=avg_time,
            total_processing_time=total_time,
            results=results,
            summary=summary
        )

        self._report_results(suite_result)
        return suite_result

    def _load_fixtures(self) -> List[Dict[str, Any]]:
        """Load all fixture files"""
        fixtures = []
        for fixture_file in self.fixtures_dir.glob("*.json"):
            with open(fixture_file, 'r', encoding='utf-8') as f:
                fixture = json.load(f)
                fixtures.append(fixture)

        # Sort for consistent processing order
        fixtures.sort(key=lambda x: x['id'])
        return fixtures

    def _run_single_test(self, fixture: Dict[str, Any]) -> TestResult:
        """Run a single golden test"""
        fixture_id = fixture['id']
        expected_result = self.baseline['results'].get(fixture_id, {})

        if not expected_result:
            return TestResult(
                fixture_id=fixture_id,
                passed=False,
                differences=["No expected result found in baseline"],
                current_result={},
                expected_result={},
                processing_time=0.0,
                normalized=False
            )

        # Generate current result
        start_time = time.time()
        current_result = self._generate_current_result(fixture)
        processing_time = time.time() - start_time

        # Compare with expected
        differences, normalized = self._compare_results(current_result, expected_result)

        passed = len(differences) == 0

        return TestResult(
            fixture_id=fixture_id,
            passed=passed,
            differences=differences,
            current_result=current_result,
            expected_result=expected_result,
            processing_time=processing_time,
            normalized=normalized
        )

    def _generate_current_result(self, fixture: Dict[str, Any]) -> Dict[str, Any]:
        """Generate current result using the same logic as baseline generation"""
        # This is simplified - in practice you'd reuse the baseline generation logic
        category = ContentCategory(fixture['category'])
        content = fixture['raw_content']

        # Quality assessment
        quality_metrics = self.quality_service.assess_content_quality(content, category)

        # Convert to dict format
        result = {
            "fixture_id": fixture['id'],
            "quality_metrics": {
                "overall_score": quality_metrics.overall_score,
                "quality_tier": quality_metrics.quality_tier.value if hasattr(quality_metrics.quality_tier, 'value') else str(quality_metrics.quality_tier),
                "publish_ready": quality_metrics.publish_ready,
                "word_count": quality_metrics.word_count,
                "sentence_count": quality_metrics.sentence_count,
                "content_density": quality_metrics.content_density,
                "structural_integrity": quality_metrics.structural_integrity,
                "factual_accuracy": quality_metrics.factual_accuracy,
                "victor_voice_authenticity": quality_metrics.victor_voice_authenticity,
                "readability_score": quality_metrics.readability_score
            },
            "victor_voice_result": None,
            "seo_analysis": {},
            "template_rendering": {},
            "processing_time": 0.0,  # Will be set by caller
            "errors": [],
            "timestamp": time.time(),
            "version": "current_test"
        }

        return result

    def _compare_results(self, current: Dict[str, Any], expected: Dict[str, Any]) -> Tuple[List[str], bool]:
        """Compare current result with expected, with normalization"""
        differences = []
        normalized = False

        # Normalize timestamps (they will always differ)
        current_norm = self._normalize_result(current)
        expected_norm = self._normalize_result(expected)

        # Convert to JSON strings for comparison
        current_json = json.dumps(current_norm, sort_keys=True, indent=2)
        expected_json = json.dumps(expected_norm, sort_keys=True, indent=2)

        if current_json != expected_json:
            # Generate diff
            diff = list(difflib.unified_diff(
                expected_json.splitlines(keepends=True),
                current_json.splitlines(keepends=True),
                fromfile='expected',
                tofile='current',
                lineterm=''
            ))

            if diff:
                differences.append("Results differ:")
                differences.extend(diff[:20])  # Limit diff output
                if len(diff) > 20:
                    differences.append(f"... and {len(diff) - 20} more lines")

        return differences, normalized

    def _normalize_result(self, result: Dict[str, Any]) -> Dict[str, Any]:
        """Normalize result for comparison (remove timestamps, etc.)"""
        normalized = json.loads(json.dumps(result))  # Deep copy

        # Remove timestamp fields
        if 'timestamp' in normalized:
            del normalized['timestamp']
        if 'generated_at' in normalized:
            del normalized['generated_at']

        # Normalize processing time (allow some variance)
        if 'processing_time' in normalized:
            # Round to 3 decimal places
            normalized['processing_time'] = round(normalized['processing_time'], 3)

        return normalized

    def _create_summary(self, results: List[TestResult]) -> Dict[str, Any]:
        """Create summary statistics"""
        if not results:
            return {}

        processing_times = [r.processing_time for r in results if r.processing_time > 0]

        summary = {
            "total_fixtures": len(results),
            "passed": sum(1 for r in results if r.passed),
            "failed": sum(1 for r in results if not r.passed),
            "processing_times": {
                "mean": statistics.mean(processing_times) if processing_times else 0,
                "median": statistics.median(processing_times) if processing_times else 0,
                "min": min(processing_times) if processing_times else 0,
                "max": max(processing_times) if processing_times else 0
            },
            "failure_categories": self._categorize_failures(results)
        }

        return summary

    def _categorize_failures(self, results: List[TestResult]) -> Dict[str, int]:
        """Categorize types of failures"""
        categories = {}

        for result in results:
            if not result.passed:
                # Simple categorization based on differences
                if result.differences:
                    first_diff = result.differences[0].lower()
                    if 'quality' in first_diff:
                        categories['quality_scoring'] = categories.get('quality_scoring', 0) + 1
                    elif 'seo' in first_diff:
                        categories['seo_analysis'] = categories.get('seo_analysis', 0) + 1
                    elif 'template' in first_diff:
                        categories['template_rendering'] = categories.get('template_rendering', 0) + 1
                    elif 'exception' in first_diff:
                        categories['exceptions'] = categories.get('exceptions', 0) + 1
                    else:
                        categories['other'] = categories.get('other', 0) + 1
                else:
                    categories['unknown'] = categories.get('unknown', 0) + 1

        return categories

    def _report_results(self, suite_result: TestSuiteResult):
        """Report test suite results"""
        print("\n📊 Golden Master Test Results")
        print("=" * 50)

        print(f"📈 Total Tests: {suite_result.total_tests}")
        print(f"✅ Passed: {suite_result.passed_tests}")
        print(f"❌ Failed: {suite_result.failed_tests}")
        print(f"⏱️  Average Time: {suite_result.average_processing_time:.3f}s")
        if suite_result.failed_tests > 0:
            print(f"\n🔍 Failure Analysis:")
            for category, count in suite_result.summary.get('failure_categories', {}).items():
                print(f"   {category}: {count}")

        if suite_result.failed_tests > 0:
            print(f"\n🚨 FAILURES DETECTED!")
            print("   Review detailed results above and update baseline if changes are expected.")
            print("   To update baseline: python scripts/generate_golden_baseline.py")
        else:
            print("\n🎉 ALL TESTS PASSED!")
            print("   Golden master regression tests successful.")

def main():
    """Run golden master tests"""
    fixtures_dir = Path(__file__).parent.parent / "tests" / "golden_master" / "fixtures"
    baseline_file = Path(__file__).parent.parent / "tests" / "golden_master" / "expected_outputs" / "golden_baseline.json"

    if not baseline_file.exists():
        print(f"❌ Baseline file not found: {baseline_file}")
        print("   Run: python scripts/generate_golden_baseline.py")
        sys.exit(1)

    if not fixtures_dir.exists():
        print(f"❌ Fixtures directory not found: {fixtures_dir}")
        print("   Run: python scripts/generate_golden_fixtures.py")
        sys.exit(1)

    runner = GoldenTestRunner(fixtures_dir, baseline_file)
    result = runner.run_test_suite()

    # Exit with error code if tests failed
    sys.exit(1 if result.failed_tests > 0 else 0)

if __name__ == "__main__":
    main()