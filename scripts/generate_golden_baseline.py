#!/usr/bin/env python3
"""
Golden Master Baseline Generator
================================

Generates baseline outputs for all golden test fixtures using the current
consolidated content pipeline. These outputs serve as the "golden" reference
for regression testing.
"""

import sys
import json
import time
from pathlib import Path
from typing import Dict, List, Any, Optional
from dataclasses import dataclass, asdict
import difflib

# Add services to path
sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent / "services"))

from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, ContentCategory
from consolidated_seo_service import ConsolidatedSEOService
from consolidated_template_service import ConsolidatedTemplateService
from consolidated_content_discovery import ConsolidatedContentDiscoveryService

@dataclass
class PipelineResult:
    """Complete pipeline processing result"""
    fixture_id: str
    quality_metrics: Dict[str, Any]
    victor_voice_result: Optional[Dict[str, Any]]
    seo_analysis: Dict[str, Any]
    template_rendering: Dict[str, Any]
    processing_time: float
    errors: List[str]

    def to_dict(self) -> Dict[str, Any]:
        """Convert to dictionary for JSON serialization"""
        return {
            "fixture_id": self.fixture_id,
            "quality_metrics": self.quality_metrics,
            "victor_voice_result": self.victor_voice_result,
            "seo_analysis": self.seo_analysis,
            "template_rendering": self.template_rendering,
            "processing_time": self.processing_time,
            "errors": self.errors,
            "timestamp": time.time(),
            "version": "consolidated_v1"
        }

class GoldenBaselineGenerator:
    """Generates golden master baselines for regression testing"""

    def __init__(self, fixtures_dir: Path, output_dir: Path):
        self.fixtures_dir = fixtures_dir
        self.output_dir = output_dir
        self.output_dir.mkdir(parents=True, exist_ok=True)

        # Initialize consolidated services
        self.quality_service = ConsolidatedQualityAssessmentService()
        self.seo_service = ConsolidatedSEOService()
        self.template_service = ConsolidatedTemplateService()
        self.discovery_service = ConsolidatedContentDiscoveryService()

    def generate_baseline(self) -> Dict[str, Any]:
        """Generate baseline outputs for all fixtures"""
        print("🎯 Generating Golden Master Baseline")
        print("=" * 50)

        fixtures = self._load_fixtures()
        results = {}

        for i, fixture in enumerate(fixtures, 1):
            print(f"   {i:2d}/{len(fixtures)} Processing: {fixture['id']}")

            try:
                result = self._process_fixture(fixture)
                results[fixture['id']] = result.to_dict()

                # Save individual result
                self._save_result(fixture['id'], result)

            except Exception as e:
                print(f"   ❌ Error processing {fixture['id']}: {e}")
                error_result = PipelineResult(
                    fixture_id=fixture['id'],
                    quality_metrics={},
                    victor_voice_result=None,
                    seo_analysis={},
                    template_rendering={},
                    processing_time=0.0,
                    errors=[str(e)]
                )
                results[fixture['id']] = error_result.to_dict()
                self._save_result(fixture['id'], error_result)

        # Save consolidated baseline
        self._save_consolidated_baseline(results)

        return results

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

    def _process_fixture(self, fixture: Dict[str, Any]) -> PipelineResult:
        """Process a single fixture through the complete pipeline"""
        start_time = time.time()
        errors = []

        try:
            category = ContentCategory(fixture['category'])
            content = fixture['raw_content']

            # 1. Quality Assessment
            quality_metrics = self.quality_service.assess_content_quality(content, category)

            # 2. Victor Voice Processing (if content is suitable)
            victor_result = None
            if quality_metrics.publish_ready and len(content) > 100:
                try:
                    victor_result = self.quality_service.apply_victor_voice(
                        content, category, intensity=0.7
                    )
                    if hasattr(victor_result, 'transformed_content'):
                        victor_result = {
                            "transformed_content": victor_result.transformed_content,
                            "voice_confidence": getattr(victor_result, 'voice_confidence_score', 0.0),
                            "phrases_found": getattr(victor_result, 'proof_elements_found', [])
                        }
                except Exception as e:
                    errors.append(f"Victor voice processing failed: {e}")

            # 3. SEO Analysis
            seo_analysis = {}
            try:
                seo_result = self.seo_service.analyze_seo(content)
                if hasattr(seo_result, 'primary_keyword'):
                    seo_analysis = {
                        "primary_keyword": getattr(seo_result, 'primary_keyword', ''),
                        "seo_score": getattr(seo_result, 'seo_score', 0.0),
                        "intent": getattr(seo_result, 'intent', 'unknown'),
                        "readability_score": getattr(seo_result, 'readability_score', 0.0)
                    }
            except Exception as e:
                errors.append(f"SEO analysis failed: {e}")

            # 4. Template Rendering (if publishable)
            template_result = {}
            if quality_metrics.publish_ready:
                try:
                    # Select appropriate template
                    template_name = self._select_template(category, fixture.get('content_type', 'devlog'))
                    rendered = self.template_service.render_content(
                        {
                            "title": fixture['title'],
                            "content": content,
                            "excerpt": content[:200] + "..." if len(content) > 200 else content
                        },
                        template_name
                    )
                    template_result = {
                        "template_used": template_name,
                        "render_success": True,
                        "output_length": len(rendered.html_content) if hasattr(rendered, 'html_content') else len(str(rendered))
                    }
                except Exception as e:
                    errors.append(f"Template rendering failed: {e}")
                    template_result = {"render_success": False, "error": str(e)}

            processing_time = time.time() - start_time

            # Convert quality metrics to dict
            quality_dict = {
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
            }

            return PipelineResult(
                fixture_id=fixture['id'],
                quality_metrics=quality_dict,
                victor_voice_result=victor_result,
                seo_analysis=seo_analysis,
                template_rendering=template_result,
                processing_time=processing_time,
                errors=errors
            )

        except Exception as e:
            processing_time = time.time() - start_time
            errors.append(f"Pipeline processing failed: {e}")

            return PipelineResult(
                fixture_id=fixture['id'],
                quality_metrics={},
                victor_voice_result=None,
                seo_analysis={},
                template_rendering={},
                processing_time=processing_time,
                errors=errors
            )

    def _select_template(self, category: ContentCategory, content_type: str) -> str:
        """Select appropriate template based on content"""
        # Simple template selection logic
        template_map = {
            ContentCategory.TECHNICAL: "autoblogger_technical_post",
            ContentCategory.STRATEGIC: "autoblogger_strategy_post",
            ContentCategory.OPERATIONAL: "autoblogger_general_post",
            ContentCategory.NARRATIVE: "autoblogger_story_post",
            ContentCategory.LEARNING: "autoblogger_tutorial_post",
            ContentCategory.REFLECTION: "autoblogger_reflection_post"
        }

        return template_map.get(category, "autoblogger_general_post")

    def _save_result(self, fixture_id: str, result: PipelineResult) -> None:
        """Save individual result file"""
        result_file = self.output_dir / f"{fixture_id}_result.json"
        with open(result_file, 'w', encoding='utf-8') as f:
            json.dump(result.to_dict(), f, indent=2, ensure_ascii=False)

    def _save_consolidated_baseline(self, results: Dict[str, Any]) -> None:
        """Save consolidated baseline file"""
        baseline_file = self.output_dir / "golden_baseline.json"
        with open(baseline_file, 'w', encoding='utf-8') as f:
            json.dump({
                "metadata": {
                    "description": "Golden Master Baseline for Consolidated Content Pipeline",
                    "version": "consolidated_v1",
                    "generated_at": time.time(),
                    "fixture_count": len(results),
                    "services_version": "phase3_consolidated"
                },
                "results": results
            }, f, indent=2, ensure_ascii=False)

        print(f"✅ Saved consolidated baseline: {baseline_file}")


def main():
    """Generate golden master baseline"""
    fixtures_dir = Path(__file__).parent.parent / "tests" / "golden_master" / "fixtures"
    output_dir = Path(__file__).parent.parent / "tests" / "golden_master" / "expected_outputs"

    if not fixtures_dir.exists():
        print(f"❌ Fixtures directory not found: {fixtures_dir}")
        print("   Run: python scripts/generate_golden_fixtures.py")
        sys.exit(1)

    generator = GoldenBaselineGenerator(fixtures_dir, output_dir)
    results = generator.generate_baseline()

    print("\n🎉 Golden Master Baseline Generated!")
    print(f"   📁 Output directory: {output_dir}")
    print(f"   📊 Total fixtures processed: {len(results)}")

    # Summary statistics
    success_count = sum(1 for r in results.values() if not r.get('errors', []))
    error_count = len(results) - success_count

    print(f"   ✅ Successful: {success_count}")
    print(f"   ❌ Errors: {error_count}")

    if success_count > 0:
        avg_time = sum(r.get('processing_time', 0) for r in results.values()) / success_count
        print(f"   ⏱️  Average processing time: {avg_time:.3f}s")

    print("\n🎯 Golden baseline ready for regression testing!")
    print("   Run: python scripts/run_golden_tests.py")


if __name__ == "__main__":
    main()