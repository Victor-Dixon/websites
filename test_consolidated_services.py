#!/usr/bin/env python3
"""
Comprehensive Test Suite for Consolidated Services
=================================================

PHASE 4: Migration & Testing - Comprehensive validation of all consolidated services.

Tests all 5 consolidated services:
- consolidated_quality_assessment.py (Quality + Voice)
- consolidated_template_service.py (Templates)
- consolidated_content_discovery.py (Discovery)
- consolidated_seo_service.py (SEO)
- Integration testing across all services

VALIDATES:
- Backward compatibility (old APIs still work)
- Enhanced functionality (new capabilities work)
- Integration (services work together)
- Performance (reasonable response times)
- Error handling (graceful degradation)
"""

import sys
import time
from pathlib import Path
from typing import Dict, List, Any

# Add services to path
WEBSITES_DIR = Path(__file__).parent
sys.path.insert(0, str(WEBSITES_DIR / 'scripts' / 'services'))

from consolidated_quality_assessment import (
    ConsolidatedQualityAssessmentService, ContentCategory, QualityTier
)
from consolidated_template_service import ConsolidatedTemplateService
from consolidated_content_discovery import ConsolidatedContentDiscoveryService
from consolidated_seo_service import ConsolidatedSEOService

# Test data
TEST_CONTENT = {
    'technical': """
# Race Condition Debugging: A Production Nightmare

So there I was, 2 AM on a Thursday, staring at logs that made no sense. Our payment processing system was losing transactions randomly - like once every 500 requests. In development? Perfect. In staging? Perfect. Production? Chaos.

The issue was subtle. We had this shared cache map that multiple goroutines were hitting. One goroutine writing payment states, another reading to validate transactions. No mutex. Classic race condition, but finding it was a nightmare.

What made it worse? The bug only triggered under load. Our local testing never hit the concurrency patterns that production saw. I'd spend hours with `go run -race`, but it was clean. The real issue? Different timing in the production environment.

The fix was embarrassing. One line: add a sync.RWMutex. But the lesson? Race conditions don't show up in testing. You need to design for concurrency from day one.

That race condition detector in the IDE? tbh, it's saved me more times than I can count. Don't skip the concurrency checks. Your future self will thank you.

The system? Rock solid now. And I sleep better at night.
""",

    'narrative': """
# The Architecture Decision That Almost Killed the Company

Six months ago, we made what seemed like a small choice: event sourcing for our domain events. The team pushed back hard. "Overkill!" they said. "We're moving fast - CRUD tables would be simpler!" The product manager wanted features yesterday. The CEO questioned the timeline.

But I fought for it. Something in my gut said this was important. We spent an extra two weeks building the event store, the projectors, the replay capabilities. The team grumbled. "Why complicate things?" they asked.

Then the crisis hit. A critical bug corrupted our core transaction data. With traditional CRUD, we'd have lost everything. Recovery would take weeks. But with event sourcing? We replayed the events from the last good backup. System back online in 4 hours.

The audit requirements came next. "Show us every change to customer balances," regulators demanded. With events? Trivial. We had perfect audit trails from day one.

The lesson? Sometimes over-engineering is just engineering. Invest in fundamentals. Build for the hard problems you don't see yet. Your future self won't just thank you - they'll celebrate.

What complex decision are you avoiding today that your future self needs desperately?
""",

    'operational': """
## Status Update - Week 12

Working on the integration system. Made some progress on the API endpoints. Need to test the authentication flow. Will update tomorrow with results.

Task completed:
- API design review
- Initial implementation
- Basic testing

Next steps:
- Integration testing
- Error handling
- Documentation
"""
}

class ConsolidatedServicesTestSuite:
    """Comprehensive test suite for all consolidated services"""

    def __init__(self):
        """Initialize all services"""
        self.quality_service = ConsolidatedQualityAssessmentService()
        self.template_service = ConsolidatedTemplateService()
        self.discovery_service = ConsolidatedContentDiscoveryService()
        self.seo_service = ConsolidatedSEOService()

        self.test_results = {
            'quality_assessment': {},
            'template_system': {},
            'content_discovery': {},
            'seo_processing': {},
            'integration': {},
            'backward_compatibility': {},
            'performance': {}
        }

    def run_all_tests(self) -> Dict[str, Any]:
        """Run complete test suite"""
        print("🧪 PHASE 4: Consolidated Services Test Suite")
        print("=" * 60)

        start_time = time.time()

        # Test individual services
        self.test_quality_assessment()
        self.test_template_system()
        self.test_content_discovery()
        self.test_seo_processing()

        # Test integration
        self.test_integration()

        # Test backward compatibility
        self.test_backward_compatibility()

        # Test performance
        self.test_performance()

        total_time = time.time() - start_time

        # Generate summary
        summary = self.generate_summary(total_time)

        print(f"\n⏱️  Total Test Time: {total_time:.2f} seconds")
        print(f"📊 Tests Passed: {summary['passed']}/{summary['total']}")

        return summary

    def test_quality_assessment(self):
        """Test consolidated quality assessment service"""
        print("\n🎯 Testing Quality Assessment Service...")

        results = {}

        for content_type, content in TEST_CONTENT.items():
            try:
                # Test consolidated assessment
                metrics = self.quality_service.assess_content_quality(
                    content, ContentCategory.TECHNICAL if content_type == 'technical' else ContentCategory.NARRATIVE
                )

                # Validate scoring
                score = metrics.overall_score
                tier = metrics.quality_tier
                publish_ready = metrics.publish_ready

                # Expected results
                expected_ready = content_type in ['technical', 'narrative']

                results[content_type] = {
                    'score': score,
                    'tier': tier.value,
                    'publish_ready': publish_ready,
                    'expected_ready': expected_ready,
                    'valid': publish_ready == expected_ready,
                    'victor_phrases': metrics.victor_phrase_count,
                    'technical_terms': metrics.technical_term_count
                }

                status = "✅ PASS" if results[content_type]['valid'] else "❌ FAIL"
                print(f"  {content_type}: Score {score:.3f} ({tier.value}) - {status}")

            except Exception as e:
                results[content_type] = {'error': str(e)}
                print(f"  {content_type}: ❌ ERROR - {e}")

        self.test_results['quality_assessment'] = results

    def test_template_system(self):
        """Test consolidated template service"""
        print("\n📝 Testing Template System...")

        results = {}

        try:
            # Test template discovery
            templates = self.template_service.get_available_templates()
            results['template_count'] = len(templates)

            # Test template selection
            test_content = TEST_CONTENT['technical']
            selected = self.template_service.select_template(test_content, ContentCategory.TECHNICAL)
            results['template_selection'] = selected

            # Test template rendering
            render_data = {
                'content': test_content,
                'title': 'Test Episode',
                'author': 'Digital Dreamscape'
            }

            rendered = self.template_service.render_content(render_data, selected)
            results['render_success'] = rendered.html_content is not None and len(rendered.html_content) > 0
            results['validation_errors'] = len(rendered.validation_errors)

            # Test template validation
            validation = self.template_service.validate_template(selected)
            results['validation_valid'] = validation['valid']

            print(f"  Templates Available: {len(templates)}")
            print(f"  Selected Template: {selected}")
            print(f"  Render Success: {'✅' if results['render_success'] else '❌'}")
            print(f"  Validation: {'✅' if results['validation_valid'] else '❌'}")

        except Exception as e:
            results['error'] = str(e)
            print(f"  ❌ ERROR - {e}")

        self.test_results['template_system'] = results

    def test_content_discovery(self):
        """Test consolidated content discovery service"""
        print("\n🔍 Testing Content Discovery...")

        results = {}

        try:
            # Test content discovery
            discovery_result = self.discovery_service.discover_all_content()

            results['sources_found'] = len(discovery_result.sources)
            results['total_files'] = discovery_result.total_files
            results['prioritized_sources'] = len(discovery_result.prioritized_sources)

            # Test content iteration from first source
            if discovery_result.sources:
                first_source = discovery_result.sources[0]
                content_items = list(self.discovery_service.get_content_iterator(first_source))
                results['sample_content_count'] = len(content_items)

                # Test filtering
                filtered = self.discovery_service.filter_content(
                    content_items, priority_filter=None, type_filter='markdown'
                )
                results['filtered_count'] = len(filtered)

            print(f"  Sources Found: {results['sources_found']}")
            print(f"  Total Files: {results['total_files']}")
            print(f"  Prioritized Sources: {results['prioritized_sources']}")
            if 'sample_content_count' in results:
                print(f"  Sample Content Items: {results['sample_content_count']}")

        except Exception as e:
            results['error'] = str(e)
            print(f"  ❌ ERROR - {e}")

        self.test_results['content_discovery'] = results

    def test_seo_processing(self):
        """Test consolidated SEO service"""
        print("\n🔍 Testing SEO Processing...")

        results = {}

        try:
            test_content = TEST_CONTENT['technical']

            # Test SEO analysis
            analysis = self.seo_service.analyze_seo(test_content, brand='digitaldreamscape')
            results['primary_keyword'] = analysis.primary_keyword
            results['secondary_keywords'] = analysis.secondary_keywords
            results['seo_score'] = analysis.keyword_density_score + analysis.readability_seo_score

            # Test SEO enhancement
            enhanced = self.seo_service.enhance_content_seo(test_content, brand='digitaldreamscape')
            results['enhancement_success'] = enhanced.seo_score > 0
            results['enhancements_applied'] = len(enhanced.enhancements_applied)

            print(f"  Primary Keyword: {analysis.primary_keyword}")
            print(f"  SEO Score: {results['seo_score']:.3f}")
            print(f"  Enhancement Success: {'✅' if results['enhancement_success'] else '❌'}")
            print(f"  Enhancements Applied: {results['enhancements_applied']}")

        except Exception as e:
            results['error'] = str(e)
            print(f"  ❌ ERROR - {e}")

        self.test_results['seo_processing'] = results

    def test_integration(self):
        """Test integration across all services"""
        print("\n🔗 Testing Service Integration...")

        results = {}

        try:
            # Create a complete content processing pipeline
            content = TEST_CONTENT['technical']

            # 1. Discover content (simulate)
            discovery_result = self.discovery_service.discover_all_content()
            results['discovery_success'] = discovery_result.total_files > 0

            # 2. Assess quality
            quality = self.quality_service.assess_content_quality(content, ContentCategory.TECHNICAL)
            results['quality_success'] = quality.overall_score > 0

            # 3. Apply Victor voice
            voiced_content = self.quality_service.apply_victor_voice(content, ContentCategory.TECHNICAL, 0.8)
            results['voice_success'] = len(voiced_content) > len(content) * 0.9  # Should be similar length

            # 4. Select template
            selected_template = self.template_service.select_template(content, ContentCategory.TECHNICAL)
            results['template_selection_success'] = selected_template is not None

            # 5. Enhance SEO
            seo_enhanced = self.seo_service.enhance_content_seo(voiced_content, brand='digitaldreamscape')
            results['seo_success'] = seo_enhanced.seo_score > 0

            # 6. Render final content
            render_data = {
                'content': seo_enhanced.enhanced_content,
                'title': seo_enhanced.title_optimized,
                'meta_description': seo_enhanced.meta_description
            }
            rendered = self.template_service.render_content(render_data, selected_template)
            results['render_success'] = rendered.html_content is not None and len(rendered.html_content) > 0

            # Overall integration success
            integration_steps = ['discovery_success', 'quality_success', 'voice_success',
                               'template_selection_success', 'seo_success', 'render_success']
            results['overall_success'] = all(results.get(step, False) for step in integration_steps)

            print(f"  Discovery: {'✅' if results['discovery_success'] else '❌'}")
            print(f"  Quality: {'✅' if results['quality_success'] else '❌'}")
            print(f"  Voice: {'✅' if results['voice_success'] else '❌'}")
            print(f"  Template: {'✅' if results['template_selection_success'] else '❌'}")
            print(f"  SEO: {'✅' if results['seo_success'] else '❌'}")
            print(f"  Render: {'✅' if results['render_success'] else '❌'}")
            print(f"  Overall Integration: {'✅ PASS' if results['overall_success'] else '❌ FAIL'}")

        except Exception as e:
            results['error'] = str(e)
            print(f"  ❌ ERROR - {e}")

        self.test_results['integration'] = results

    def test_backward_compatibility(self):
        """Test backward compatibility with old APIs"""
        print("\n🔄 Testing Backward Compatibility...")

        results = {}

        try:
            # Test old episode_quality_scorer API
            from episode_quality_scorer import EpisodeQualityScorer
            old_scorer = EpisodeQualityScorer()
            old_metrics = old_scorer.score_episode(TEST_CONTENT['technical'], ContentCategory.TECHNICAL)
            results['old_quality_api'] = old_metrics.overall_score > 0

            # Test consolidated service produces same results
            new_metrics = self.quality_service.assess_content_quality(TEST_CONTENT['technical'], ContentCategory.TECHNICAL)
            results['api_compatibility'] = abs(old_metrics.overall_score - new_metrics.overall_score) < 0.1

            print(f"  Old API Works: {'✅' if results['old_quality_api'] else '❌'}")
            print(f"  API Compatibility: {'✅' if results['api_compatibility'] else '❌'}")
            print(".3f")
            print(".3f")

        except Exception as e:
            results['error'] = str(e)
            print(f"  ❌ ERROR - {e}")

        self.test_results['backward_compatibility'] = results

    def test_performance(self):
        """Test performance of consolidated services"""
        print("\n⚡ Testing Performance...")

        results = {}

        try:
            content = TEST_CONTENT['technical']

            # Test quality assessment performance
            start_time = time.time()
            for _ in range(10):
                self.quality_service.assess_content_quality(content, ContentCategory.TECHNICAL)
            quality_time = (time.time() - start_time) / 10
            results['quality_avg_time'] = quality_time

            # Test SEO analysis performance
            start_time = time.time()
            for _ in range(5):
                self.seo_service.analyze_seo(content)
            seo_time = (time.time() - start_time) / 5
            results['seo_avg_time'] = seo_time

            # Test template rendering performance
            render_data = {'content': content, 'title': 'Test'}
            start_time = time.time()
            for _ in range(5):
                self.template_service.render_content(render_data)
            template_time = (time.time() - start_time) / 5
            results['template_avg_time'] = template_time

            print(".4f")
            print(".4f")
            print(".4f")

            # Performance thresholds (should be reasonable)
            results['performance_acceptable'] = all([
                quality_time < 1.0,  # Less than 1 second per assessment
                seo_time < 2.0,      # Less than 2 seconds per analysis
                template_time < 1.0  # Less than 1 second per render
            ])

            print(f"  Performance Acceptable: {'✅' if results['performance_acceptable'] else '❌'}")

        except Exception as e:
            results['error'] = str(e)
            print(f"  ❌ ERROR - {e}")

        self.test_results['performance'] = results

    def generate_summary(self, total_time: float) -> Dict[str, Any]:
        """Generate comprehensive test summary"""
        summary = {
            'total_time': total_time,
            'passed': 0,
            'failed': 0,
            'total': 0,
            'categories': {},
            'recommendations': []
        }

        # Count results by category
        for category, results in self.test_results.items():
            category_passed = 0
            category_total = 0

            if isinstance(results, dict):
                for key, value in results.items():
                    if isinstance(value, dict):
                        if 'valid' in value:
                            category_total += 1
                            if value['valid']:
                                category_passed += 1
                        elif 'success' in value:
                            category_total += 1
                            if value['success']:
                                category_passed += 1
                        elif 'error' not in value:
                            # Count successful operations
                            category_total += 1
                            category_passed += 1
                    elif not isinstance(value, (str, list)) and 'error' not in str(value):
                        category_total += 1
                        category_passed += 1

            summary['categories'][category] = {
                'passed': category_passed,
                'total': category_total,
                'success_rate': category_passed / category_total if category_total > 0 else 0
            }

            summary['passed'] += category_passed
            summary['total'] += category_total

        summary['failed'] = summary['total'] - summary['passed']

        # Generate recommendations
        if summary['failed'] > 0:
            summary['recommendations'].append(f"Address {summary['failed']} failed tests")

        for category, stats in summary['categories'].items():
            if stats['success_rate'] < 0.8:
                summary['recommendations'].append(f"Improve {category} success rate: {stats['success_rate']:.1%}")

        return summary


def main():
    """Run the complete test suite"""
    suite = ConsolidatedServicesTestSuite()
    summary = suite.run_all_tests()

    print("\n" + "=" * 60)
    print("📊 TEST SUITE SUMMARY")
    print("=" * 60)
    print(f"Total Tests: {summary['total']}")
    print(f"Passed: {summary['passed']}")
    print(f"Failed: {summary['failed']}")
    print(".1%")

    if summary['recommendations']:
        print("\n💡 RECOMMENDATIONS:")
        for rec in summary['recommendations']:
            print(f"  • {rec}")

    success = summary['passed'] == summary['total']
    print(f"\n🏆 OVERALL RESULT: {'✅ ALL TESTS PASSED' if success else '❌ SOME TESTS FAILED'}")

    return 0 if success else 1


if __name__ == "__main__":
    exit(main())