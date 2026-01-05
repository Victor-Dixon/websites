#!/usr/bin/env python3
"""
E2E Pipeline Integration Tests + Performance Benchmarks
======================================================

Comprehensive end-to-end testing of the consolidated content pipeline:
1. Content Discovery → Quality Assessment → Victor Voice → SEO → Template Rendering
2. Performance benchmarking with regression thresholds
3. Automated report generation to reports/phase4_*

Builds on golden master framework for comprehensive pipeline validation.
"""

import sys
import json
import time
import statistics
from pathlib import Path
from typing import Dict, List, Any, Optional, Tuple
from dataclasses import dataclass, field
from datetime import datetime

# Add services to path
sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent / "services"))

from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, ContentCategory
from consolidated_seo_service import ConsolidatedSEOService
from consolidated_template_service import ConsolidatedTemplateService
from consolidated_content_discovery import ConsolidatedContentDiscoveryService

@dataclass
class E2EPipelineResult:
    """Complete E2E pipeline execution result"""
    fixture_id: str
    pipeline_success: bool
    stages_completed: List[str]
    quality_score: float
    processing_time: float
    memory_usage: Optional[float]
    errors: List[str]
    outputs: Dict[str, Any] = field(default_factory=dict)

@dataclass
class PerformanceMetrics:
    """Performance benchmarking metrics"""
    total_tests: int
    successful_tests: int
    failed_tests: int
    avg_processing_time: float
    median_processing_time: float
    p95_processing_time: float
    min_processing_time: float
    max_processing_time: float
    throughput_items_per_sec: float
    memory_peak_mb: Optional[float]
    regression_thresholds: Dict[str, float]

@dataclass
class E2ETestSuiteResult:
    """Complete E2E test suite results"""
    timestamp: str
    duration: float
    results: List[E2EPipelineResult]
    performance: PerformanceMetrics
    summary: Dict[str, Any]

class E2EPipelineTester:
    """Comprehensive E2E pipeline testing and benchmarking"""

    # Performance regression thresholds (adjust based on baseline)
    REGRESSION_THRESHOLDS = {
        "max_processing_time": 2.0,  # seconds
        "avg_processing_time": 0.5,  # seconds
        "p95_processing_time": 1.0,  # seconds
        "throughput_min": 2.0,       # items/second
        "success_rate_min": 0.95     # 95% success rate
    }

    def __init__(self, fixtures_dir: Path, reports_dir: Path):
        self.fixtures_dir = fixtures_dir
        self.reports_dir = reports_dir
        self.reports_dir.mkdir(parents=True, exist_ok=True)

        # Initialize consolidated services
        self.quality_service = ConsolidatedQualityAssessmentService()
        self.seo_service = ConsolidatedSEOService()
        self.template_service = ConsolidatedTemplateService()
        self.discovery_service = ConsolidatedContentDiscoveryService()

    def run_e2e_test_suite(self) -> E2ETestSuiteResult:
        """Run complete E2E pipeline test suite with performance benchmarking"""
        print("🚀 Running E2E Pipeline Integration Tests")
        print("=" * 60)

        start_time = time.time()
        results = []

        # Load test fixtures
        fixtures = self._load_fixtures()

        # Run E2E pipeline for each fixture
        for i, fixture in enumerate(fixtures, 1):
            print(f"   {i:2d}/{len(fixtures)} E2E Testing: {fixture['id']}")
            result = self._run_e2e_pipeline(fixture)
            results.append(result)

            status = "✅ PASS" if result.pipeline_success else "❌ FAIL"
            print(f"      {status} ({result.processing_time:.3f}s)")

        total_duration = time.time() - start_time

        # Calculate performance metrics
        performance = self._calculate_performance_metrics(results, total_duration)

        # Create summary
        summary = self._create_test_summary(results, performance)

        # Generate reports
        self._generate_reports(results, performance, summary)

        suite_result = E2ETestSuiteResult(
            timestamp=datetime.now().isoformat(),
            duration=total_duration,
            results=results,
            performance=performance,
            summary=summary
        )

        self._report_results(suite_result)
        return suite_result

    def _load_fixtures(self) -> List[Dict[str, Any]]:
        """Load E2E test fixtures (subset of golden master fixtures)"""
        fixtures = []
        for fixture_file in self.fixtures_dir.glob("*.json"):
            with open(fixture_file, 'r', encoding='utf-8') as f:
                fixture = json.load(f)
                # Use a subset for E2E testing (every 5th fixture to keep test time reasonable)
                if hash(fixture['id']) % 5 == 0:
                    fixtures.append(fixture)

        # Ensure we have at least some fixtures
        if len(fixtures) < 5:
            # Load first 5 fixtures if subset is too small
            all_fixtures = []
            for fixture_file in self.fixtures_dir.glob("*.json"):
                with open(fixture_file, 'r', encoding='utf-8') as f:
                    all_fixtures.append(json.load(f))
            fixtures = sorted(all_fixtures, key=lambda x: x['id'])[:5]

        return sorted(fixtures, key=lambda x: x['id'])

    def _run_e2e_pipeline(self, fixture: Dict[str, Any]) -> E2EPipelineResult:
        """Run complete E2E pipeline: Discovery → Quality → Voice → SEO → Template"""
        fixture_id = fixture['id']
        content = fixture['raw_content']
        category = ContentCategory(fixture['category'])

        start_time = time.time()
        stages_completed = []
        errors = []
        outputs = {}

        try:
            # Stage 1: Content Validation (simulating discovery)
            stages_completed.append("content_validation")
            if not content or len(content.strip()) < 10:
                raise ValueError("Content too short or empty")

            # Stage 2: Quality Assessment
            quality_metrics = self.quality_service.assess_content_quality(content, category)
            outputs['quality'] = {
                'score': quality_metrics.overall_score,
                'tier': quality_metrics.quality_tier.value,
                'publish_ready': quality_metrics.publish_ready
            }
            stages_completed.append("quality_assessment")

            # Stage 3: Victor Voice Processing (if quality is acceptable)
            victor_output = None
            if quality_metrics.publish_ready and len(content) > 50:
                try:
                    victor_output = self.quality_service.apply_victor_voice(
                        content, category, intensity=0.7
                    )
                    outputs['victor_voice'] = {
                        'processed': True,
                        'length': len(victor_output) if victor_output else 0
                    }
                    stages_completed.append("victor_voice")
                except Exception as e:
                    errors.append(f"Victor voice failed: {e}")

            # Stage 4: SEO Enhancement
            seo_output = {}
            try:
                seo_analysis = self.seo_service.analyze_seo(content)
                if hasattr(seo_analysis, 'primary_keyword'):
                    seo_output = {
                        'primary_keyword': getattr(seo_analysis, 'primary_keyword', ''),
                        'seo_score': getattr(seo_analysis, 'seo_score', 0.0)
                    }
                outputs['seo'] = seo_output
                stages_completed.append("seo_enhancement")
            except Exception as e:
                errors.append(f"SEO analysis failed: {e}")

            # Stage 5: Template Rendering (if publishable)
            template_output = {}
            if quality_metrics.publish_ready:
                try:
                    template_result = self.template_service.render_content({
                        'title': fixture['title'],
                        'content': victor_output or content,
                        'excerpt': content[:200] + "..." if len(content) > 200 else content
                    })
                    template_output = {
                        'rendered': True,
                        'length': len(template_result.html_content) if hasattr(template_result, 'html_content') else 0
                    }
                    stages_completed.append("template_rendering")
                except Exception as e:
                    errors.append(f"Template rendering failed: {e}")

            processing_time = time.time() - start_time

            return E2EPipelineResult(
                fixture_id=fixture_id,
                pipeline_success=len(stages_completed) >= 4,  # Require 4+ stages for success
                stages_completed=stages_completed,
                quality_score=quality_metrics.overall_score,
                processing_time=processing_time,
                memory_usage=None,  # Could add memory tracking later
                errors=errors,
                outputs=outputs
            )

        except Exception as e:
            processing_time = time.time() - start_time
            errors.append(f"Pipeline failed: {e}")

            return E2EPipelineResult(
                fixture_id=fixture_id,
                pipeline_success=False,
                stages_completed=stages_completed,
                quality_score=0.0,
                processing_time=processing_time,
                memory_usage=None,
                errors=errors,
                outputs=outputs
            )

    def _calculate_performance_metrics(self, results: List[E2EPipelineResult], total_duration: float) -> PerformanceMetrics:
        """Calculate comprehensive performance metrics"""
        if not results:
            return PerformanceMetrics(
                total_tests=0, successful_tests=0, failed_tests=0,
                avg_processing_time=0, median_processing_time=0, p95_processing_time=0,
                min_processing_time=0, max_processing_time=0,
                throughput_items_per_sec=0, memory_peak_mb=None,
                regression_thresholds=self.REGRESSION_THRESHOLDS
            )

        processing_times = [r.processing_time for r in results]
        successful_tests = sum(1 for r in results if r.pipeline_success)

        return PerformanceMetrics(
            total_tests=len(results),
            successful_tests=successful_tests,
            failed_tests=len(results) - successful_tests,
            avg_processing_time=statistics.mean(processing_times),
            median_processing_time=statistics.median(processing_times),
            p95_processing_time=statistics.quantiles(processing_times, n=20)[18] if len(processing_times) >= 20 else max(processing_times),
            min_processing_time=min(processing_times),
            max_processing_time=max(processing_times),
            throughput_items_per_sec=len(results) / total_duration if total_duration > 0 else 0,
            memory_peak_mb=None,  # Could implement memory tracking
            regression_thresholds=self.REGRESSION_THRESHOLDS
        )

    def _create_test_summary(self, results: List[E2EPipelineResult], performance: PerformanceMetrics) -> Dict[str, Any]:
        """Create comprehensive test summary"""
        # Group results by category
        category_stats = {}
        for result in results:
            # Extract category from fixture ID (format: category_contenttype_index)
            category = result.fixture_id.split('_')[0]
            if category not in category_stats:
                category_stats[category] = {'total': 0, 'success': 0, 'avg_time': 0}
            category_stats[category]['total'] += 1
            if result.pipeline_success:
                category_stats[category]['success'] += 1
            category_stats[category]['avg_time'] += result.processing_time

        # Calculate averages
        for stats in category_stats.values():
            if stats['total'] > 0:
                stats['avg_time'] /= stats['total']
                stats['success_rate'] = stats['success'] / stats['total']

        # Performance regression analysis
        regressions = self._check_performance_regressions(performance)

        return {
            'category_breakdown': category_stats,
            'stage_completion_rates': self._calculate_stage_completion_rates(results),
            'performance_regressions': regressions,
            'quality_score_distribution': self._calculate_quality_distribution(results)
        }

    def _calculate_stage_completion_rates(self, results: List[E2EPipelineResult]) -> Dict[str, float]:
        """Calculate completion rates for each pipeline stage"""
        stages = ['content_validation', 'quality_assessment', 'victor_voice', 'seo_enhancement', 'template_rendering']
        completion_rates = {}

        for stage in stages:
            completed = sum(1 for r in results if stage in r.stages_completed)
            completion_rates[stage] = completed / len(results) if results else 0

        return completion_rates

    def _calculate_quality_distribution(self, results: List[E2EPipelineResult]) -> Dict[str, int]:
        """Calculate distribution of quality scores"""
        distribution = {'excellent': 0, 'good': 0, 'acceptable': 0, 'poor': 0}

        for result in results:
            if result.quality_score >= 0.8:
                distribution['excellent'] += 1
            elif result.quality_score >= 0.6:
                distribution['good'] += 1
            elif result.quality_score >= 0.4:
                distribution['acceptable'] += 1
            else:
                distribution['poor'] += 1

        return distribution

    def _check_performance_regressions(self, performance: PerformanceMetrics) -> Dict[str, bool]:
        """Check for performance regressions against thresholds"""
        regressions = {}

        thresholds = performance.regression_thresholds

        regressions['max_time_regression'] = performance.max_processing_time > thresholds['max_processing_time']
        regressions['avg_time_regression'] = performance.avg_processing_time > thresholds['avg_processing_time']
        regressions['p95_time_regression'] = performance.p95_processing_time > thresholds['p95_processing_time']
        regressions['throughput_regression'] = performance.throughput_items_per_sec < thresholds['throughput_min']
        regressions['success_rate_regression'] = (performance.successful_tests / performance.total_tests) < thresholds['success_rate_min']

        return regressions

    def _generate_reports(self, results: List[E2EPipelineResult], performance: PerformanceMetrics, summary: Dict[str, Any]):
        """Generate comprehensive reports to reports/phase4_*"""
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")

        # Main E2E results report
        e2e_report = {
            'timestamp': timestamp,
            'test_type': 'e2e_pipeline_integration',
            'performance': {
                'total_tests': performance.total_tests,
                'successful_tests': performance.successful_tests,
                'failed_tests': performance.failed_tests,
                'avg_processing_time': performance.avg_processing_time,
                'median_processing_time': performance.median_processing_time,
                'p95_processing_time': performance.p95_processing_time,
                'throughput_items_per_sec': performance.throughput_items_per_sec,
                'success_rate': performance.successful_tests / performance.total_tests if performance.total_tests > 0 else 0
            },
            'regressions': summary['performance_regressions'],
            'category_breakdown': summary['category_breakdown'],
            'stage_completion': summary['stage_completion_rates'],
            'quality_distribution': summary['quality_score_distribution']
        }

        # Save main report
        report_file = self.reports_dir / f"phase4_e2e_results_{timestamp}.json"
        with open(report_file, 'w', encoding='utf-8') as f:
            json.dump(e2e_report, f, indent=2, ensure_ascii=False)

        # Save detailed results
        detailed_file = self.reports_dir / f"phase4_e2e_detailed_{timestamp}.json"
        detailed_results = []
        for result in results:
            detailed_results.append({
                'fixture_id': result.fixture_id,
                'success': result.pipeline_success,
                'stages_completed': result.stages_completed,
                'quality_score': result.quality_score,
                'processing_time': result.processing_time,
                'errors': result.errors,
                'outputs': result.outputs
            })

        with open(detailed_file, 'w', encoding='utf-8') as f:
            json.dump(detailed_results, f, indent=2, ensure_ascii=False)

        # Performance benchmark report
        perf_report = {
            'timestamp': timestamp,
            'benchmark_type': 'pipeline_performance',
            'metrics': {
                'avg_latency_ms': performance.avg_processing_time * 1000,
                'p95_latency_ms': performance.p95_processing_time * 1000,
                'throughput_items_sec': performance.throughput_items_per_sec,
                'success_rate_percent': (performance.successful_tests / performance.total_tests * 100) if performance.total_tests > 0 else 0
            },
            'thresholds': performance.regression_thresholds,
            'regressions_detected': any(summary['performance_regressions'].values())
        }

        perf_file = self.reports_dir / f"phase4_performance_benchmark_{timestamp}.json"
        with open(perf_file, 'w', encoding='utf-8') as f:
            json.dump(perf_report, f, indent=2, ensure_ascii=False)

        print(f"📊 Reports saved to: {self.reports_dir}")
        print(f"   - E2E Results: {report_file.name}")
        print(f"   - Detailed Results: {detailed_file.name}")
        print(f"   - Performance Benchmark: {perf_file.name}")

    def _report_results(self, suite_result: E2ETestSuiteResult):
        """Report test suite results to console"""
        print("\n📊 E2E Pipeline Test Results")
        print("=" * 50)

        perf = suite_result.performance
        summary = suite_result.summary

        print(f"📈 Total Tests: {perf.total_tests}")
        print(f"✅ Successful: {perf.successful_tests}")
        print(f"❌ Failed: {perf.failed_tests}")
        print(f"⏱️  Avg Time: {perf.avg_processing_time:.3f}s")
        print(f"📊 Success Rate: {perf.successful_tests / perf.total_tests * 100:.1f}%")
        # Stage completion
        print("\n🔧 Pipeline Stage Completion:")
        for stage, rate in summary['stage_completion_rates'].items():
            print(".1%"
        # Quality distribution
        print("\n🏆 Quality Score Distribution:")
        for level, count in summary['quality_distribution'].items():
            print(f"   {level.capitalize()}: {count}")

        # Performance regressions
        regressions = summary['performance_regressions']
        if any(regressions.values()):
            print("\n🚨 PERFORMANCE REGRESSIONS DETECTED!")
            for regression_type, detected in regressions.items():
                if detected:
                    print(f"   ❌ {regression_type.replace('_', ' ').title()}")
        else:
            print("\n✅ No performance regressions detected")
        print(".2f"
def main():
    """Run E2E pipeline tests with performance benchmarking"""
    fixtures_dir = Path(__file__).parent.parent / "tests" / "golden_master" / "fixtures"
    reports_dir = Path(__file__).parent.parent / "reports"

    if not fixtures_dir.exists():
        print(f"❌ Fixtures directory not found: {fixtures_dir}")
        print("   Run: python scripts/generate_golden_fixtures.py")
        sys.exit(1)

    tester = E2EPipelineTester(fixtures_dir, reports_dir)
    result = tester.run_e2e_test_suite()

    # Exit with error code if there were failures or regressions
    has_regressions = any(result.summary['performance_regressions'].values())
    has_failures = result.performance.failed_tests > 0

    if has_regressions or has_failures:
        print("\n❌ E2E TESTS FAILED - Check reports for details")
        sys.exit(1)
    else:
        print("\n🎉 E2E TESTS PASSED - All systems operational!")
        sys.exit(0)

if __name__ == "__main__":
    main()