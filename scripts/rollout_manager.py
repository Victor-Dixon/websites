#!/usr/bin/env python3
"""
Rollout Manager - Stage/Shadow/Live Execution Modes
==================================================

Implements progressive rollout system for Phase 4 production deployment:

- **STAGE**: Validate configuration and setup without processing
- **SHADOW**: Run full pipeline, write diff reports, no publishing
- **LIVE**: Use consolidated output only (full production mode)

Integrates with existing publish entrypoints for seamless rollout control.
"""

import os
import sys
import json
import time
import difflib
from pathlib import Path
from typing import Dict, List, Any, Optional, Union, Callable
from enum import Enum
from dataclasses import dataclass, field
from datetime import datetime

# Add services to path
sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, str(Path(__file__).parent / "services"))

from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, ContentCategory
from consolidated_seo_service import ConsolidatedSEOService
from consolidated_template_service import ConsolidatedTemplateService
from consolidated_content_discovery import ConsolidatedContentDiscoveryService

class RolloutMode(Enum):
    """Rollout execution modes for progressive deployment"""
    STAGE = "stage"      # Configuration validation only
    SHADOW = "shadow"    # Full processing, diff reports, no publishing
    LIVE = "live"        # Consolidated output only (production)

@dataclass
class RolloutConfig:
    """Configuration for rollout execution"""
    mode: RolloutMode
    content_id: str
    content_data: Dict[str, Any]
    publish_function: Optional[Callable] = None
    output_dir: Path = None
    enable_diffs: bool = True
    enable_metrics: bool = True

    def __post_init__(self):
        if self.output_dir is None:
            self.output_dir = Path("rollout_output")

@dataclass
class RolloutResult:
    """Result of rollout execution"""
    success: bool
    mode: RolloutMode
    content_id: str
    processing_time: float
    stage_outputs: Dict[str, Any] = field(default_factory=dict)
    diff_reports: List[Dict[str, Any]] = field(default_factory=list)
    errors: List[str] = field(default_factory=list)
    metrics: Dict[str, Any] = field(default_factory=dict)

@dataclass
class DiffReport:
    """Detailed diff report for shadow mode comparison"""
    content_id: str
    stage: str
    legacy_output: Any
    consolidated_output: Any
    differences: List[str]
    match_score: float  # 0.0 to 1.0
    timestamp: str

class RolloutManager:
    """Manages progressive rollout execution across different modes"""

    def __init__(self):
        # Initialize consolidated services
        self.quality_service = ConsolidatedQualityAssessmentService()
        self.seo_service = ConsolidatedSEOService()
        self.template_service = ConsolidatedTemplateService()
        self.discovery_service = ConsolidatedContentDiscoveryService()

        # Legacy service stubs for comparison (would be imported in real implementation)
        self.legacy_quality_service = None  # EpisodeQualityScorer stub
        self.legacy_seo_service = None      # SEOEnhancementProcessor stub
        self.legacy_template_service = None # TemplateEngine stub

    def execute_rollout(self, config: RolloutConfig) -> RolloutResult:
        """Execute content processing according to rollout mode"""
        start_time = time.time()

        print(f"🚀 Executing {config.mode.value.upper()} mode rollout for: {config.content_id}")

        try:
            if config.mode == RolloutMode.STAGE:
                result = self._execute_stage_mode(config)
            elif config.mode == RolloutMode.SHADOW:
                result = self._execute_shadow_mode(config)
            elif config.mode == RolloutMode.LIVE:
                result = self._execute_live_mode(config)
            else:
                raise ValueError(f"Unknown rollout mode: {config.mode}")

            result.processing_time = time.time() - start_time
            result.metrics['total_processing_time'] = result.processing_time

            return result

        except Exception as e:
            processing_time = time.time() - start_time
            return RolloutResult(
                success=False,
                mode=config.mode,
                content_id=config.content_id,
                processing_time=processing_time,
                errors=[f"Rollout execution failed: {str(e)}"]
            )

    def _execute_stage_mode(self, config: RolloutConfig) -> RolloutResult:
        """Stage mode: Validate configuration and setup without processing"""
        print("   📋 STAGE MODE: Validating configuration and setup...")

        errors = []
        stage_outputs = {}

        # Validate content data structure
        required_fields = ['title', 'content', 'category']
        for field in required_fields:
            if field not in config.content_data:
                errors.append(f"Missing required field: {field}")

        # Validate category
        try:
            category = ContentCategory(config.content_data.get('category', 'operational'))
            stage_outputs['category_validated'] = category.value
        except ValueError as e:
            errors.append(f"Invalid category: {e}")

        # Check service availability
        services_status = {
            'quality_service': self.quality_service is not None,
            'seo_service': self.seo_service is not None,
            'template_service': self.template_service is not None
        }
        stage_outputs['services_status'] = services_status

        if not all(services_status.values()):
            errors.append("Required services not available")

        # Validate output directory
        config.output_dir.mkdir(parents=True, exist_ok=True)
        stage_outputs['output_dir_ready'] = True

        success = len(errors) == 0
        print(f"   {'✅' if success else '❌'} Stage validation {'PASSED' if success else 'FAILED'}")

        return RolloutResult(
            success=success,
            mode=RolloutMode.STAGE,
            content_id=config.content_id,
            processing_time=0.0,  # Set by caller
            stage_outputs=stage_outputs,
            errors=errors
        )

    def _execute_shadow_mode(self, config: RolloutConfig) -> RolloutResult:
        """Shadow mode: Run full pipeline, write diff reports, no publishing"""
        print("   👤 SHADOW MODE: Running full pipeline with diff analysis...")

        # First run consolidated pipeline
        consolidated_result = self._run_consolidated_pipeline(config.content_data)

        # Generate diff reports comparing legacy vs consolidated
        diff_reports = []
        if config.enable_diffs:
            diff_reports = self._generate_shadow_diffs(
                config.content_id,
                config.content_data,
                consolidated_result
            )

        # Write diff reports but don't publish
        if diff_reports:
            self._write_diff_reports(config, diff_reports)

        success = consolidated_result['success'] and len(diff_reports) > 0
        print(f"   {'✅' if success else '❌'} Shadow execution {'PASSED' if success else 'FAILED'}")

        return RolloutResult(
            success=success,
            mode=RolloutMode.SHADOW,
            content_id=config.content_id,
            processing_time=0.0,  # Set by caller
            stage_outputs={'consolidated_result': consolidated_result},
            diff_reports=diff_reports
        )

    def _execute_live_mode(self, config: RolloutConfig) -> RolloutResult:
        """Live mode: Use consolidated output only (full production)"""
        print("   🔥 LIVE MODE: Running consolidated pipeline for production...")

        # Run consolidated pipeline
        consolidated_result = self._run_consolidated_pipeline(config.content_data)

        # Publish using consolidated output
        publish_success = False
        if consolidated_result['success'] and config.publish_function:
            try:
                publish_result = config.publish_function(consolidated_result['final_output'])
                publish_success = publish_result.get('success', False)
                print(f"   📤 Publishing result: {'✅ SUCCESS' if publish_success else '❌ FAILED'}")
            except Exception as e:
                consolidated_result['errors'].append(f"Publishing failed: {e}")

        success = consolidated_result['success'] and publish_success
        print(f"   {'✅' if success else '❌'} Live execution {'PASSED' if success else 'FAILED'}")

        return RolloutResult(
            success=success,
            mode=RolloutMode.LIVE,
            content_id=config.content_id,
            processing_time=0.0,  # Set by caller
            stage_outputs={'consolidated_result': consolidated_result, 'published': publish_success}
        )

    def _run_consolidated_pipeline(self, content_data: Dict[str, Any]) -> Dict[str, Any]:
        """Run the full consolidated content pipeline"""
        result = {
            'success': False,
            'stages_completed': [],
            'quality_score': 0.0,
            'final_output': None,
            'processing_steps': [],
            'errors': []
        }

        try:
            content = content_data.get('content', '')
            title = content_data.get('title', 'Untitled')
            category = ContentCategory(content_data.get('category', 'operational'))

            # Stage 1: Quality Assessment
            quality_metrics = self.quality_service.assess_content_quality(content, category)
            result['quality_score'] = quality_metrics.overall_score
            result['stages_completed'].append('quality_assessment')
            result['processing_steps'].append({
                'stage': 'quality_assessment',
                'score': quality_metrics.overall_score,
                'tier': quality_metrics.quality_tier.value,
                'publish_ready': quality_metrics.publish_ready
            })

            # Stage 2: Victor Voice (if quality allows)
            victor_content = content
            if quality_metrics.publish_ready and len(content) > 50:
                try:
                    victor_content = self.quality_service.apply_victor_voice(
                        content, category, intensity=0.7
                    )
                    result['stages_completed'].append('victor_voice')
                    result['processing_steps'].append({
                        'stage': 'victor_voice',
                        'transformed_length': len(victor_content)
                    })
                except Exception as e:
                    result['errors'].append(f"Victor voice failed: {e}")

            # Stage 3: SEO Enhancement
            try:
                seo_analysis = self.seo_service.analyze_seo(victor_content)
                result['stages_completed'].append('seo_enhancement')
                result['processing_steps'].append({
                    'stage': 'seo_enhancement',
                    'primary_keyword': getattr(seo_analysis, 'primary_keyword', ''),
                    'seo_score': getattr(seo_analysis, 'seo_score', 0.0)
                })
            except Exception as e:
                result['errors'].append(f"SEO enhancement failed: {e}")

            # Stage 4: Template Rendering (if publishable)
            final_output = None
            if quality_metrics.publish_ready:
                try:
                    template_result = self.template_service.render_content({
                        'title': title,
                        'content': victor_content,
                        'excerpt': content[:200] + "..." if len(content) > 200 else content
                    })

                    final_output = {
                        'title': title,
                        'content': template_result.html_content if hasattr(template_result, 'html_content') else str(template_result),
                        'quality_score': quality_metrics.overall_score,
                        'processing_metadata': result['processing_steps']
                    }

                    result['stages_completed'].append('template_rendering')
                    result['processing_steps'].append({
                        'stage': 'template_rendering',
                        'output_length': len(final_output['content'])
                    })

                except Exception as e:
                    result['errors'].append(f"Template rendering failed: {e}")

            result['success'] = len(result['stages_completed']) >= 3  # Require core pipeline completion
            result['final_output'] = final_output

        except Exception as e:
            result['errors'].append(f"Pipeline execution failed: {e}")

        return result

    def _generate_shadow_diffs(self, content_id: str, content_data: Dict[str, Any],
                             consolidated_result: Dict[str, Any]) -> List[Dict[str, Any]]:
        """Generate diff reports comparing legacy vs consolidated outputs"""
        diff_reports = []

        # Generate mock legacy results for comparison (in real implementation, would run legacy pipeline)
        legacy_result = self._generate_mock_legacy_result(content_data)

        # Compare key outputs
        comparisons = [
            ('quality_score', consolidated_result.get('quality_score', 0),
             legacy_result.get('quality_score', 0)),
            ('content_length', len(consolidated_result.get('final_output', {}).get('content', '')),
             len(legacy_result.get('final_output', {}).get('content', ''))),
            ('stages_completed', len(consolidated_result.get('stages_completed', [])),
             len(legacy_result.get('stages_completed', [])))
        ]

        for metric, consolidated_val, legacy_val in comparisons:
            differences = []
            match_score = 1.0

            if isinstance(consolidated_val, (int, float)) and isinstance(legacy_val, (int, float)):
                diff = abs(consolidated_val - legacy_val)
                if consolidated_val != 0:
                    match_score = 1.0 - min(diff / abs(consolidated_val), 1.0)
                if diff > 0.01:  # Significant difference threshold
                    differences.append(f"{metric}: {legacy_val} → {consolidated_val} (diff: {diff:.3f})")
            elif consolidated_val != legacy_val:
                differences.append(f"{metric}: '{legacy_val}' → '{consolidated_val}'")
                match_score = 0.0

            if differences:
                diff_reports.append({
                    'content_id': content_id,
                    'stage': 'comparison',
                    'metric': metric,
                    'legacy_output': legacy_val,
                    'consolidated_output': consolidated_val,
                    'differences': differences,
                    'match_score': match_score,
                    'timestamp': datetime.now().isoformat()
                })

        return diff_reports

    def _generate_mock_legacy_result(self, content_data: Dict[str, Any]) -> Dict[str, Any]:
        """Generate mock legacy pipeline result for shadow mode comparison"""
        # In real implementation, this would run the actual legacy pipeline
        # For now, generate plausible mock results
        content = content_data.get('content', '')
        return {
            'success': True,
            'quality_score': 0.35,  # Slightly different from consolidated
            'stages_completed': ['quality_assessment', 'seo_enhancement', 'template_rendering'],
            'final_output': {
                'content': f"<div class='legacy-output'>{content[:100]}...</div>",
                'title': content_data.get('title', 'Untitled')
            },
            'processing_steps': [
                {'stage': 'quality_assessment', 'score': 0.35, 'tier': 'BRONZE'},
                {'stage': 'seo_enhancement', 'primary_keyword': 'legacy'},
                {'stage': 'template_rendering', 'output_length': len(content) + 50}
            ]
        }

    def _write_diff_reports(self, config: RolloutConfig, diff_reports: List[Dict[str, Any]]):
        """Write shadow mode diff reports to output directory"""
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        report_file = config.output_dir / f"shadow_diffs_{config.content_id}_{timestamp}.json"

        with open(report_file, 'w', encoding='utf-8') as f:
            json.dump({
                'content_id': config.content_id,
                'mode': 'shadow',
                'timestamp': timestamp,
                'diff_reports': diff_reports,
                'summary': {
                    'total_diffs': len(diff_reports),
                    'avg_match_score': sum(r['match_score'] for r in diff_reports) / len(diff_reports) if diff_reports else 1.0
                }
            }, f, indent=2, ensure_ascii=False)

        print(f"   📊 Shadow diff report saved: {report_file}")

def create_rollout_config(mode: str, content_id: str, content_data: Dict[str, Any],
                         publish_function: Optional[Callable] = None) -> RolloutConfig:
    """Helper function to create rollout configuration"""
    return RolloutConfig(
        mode=RolloutMode(mode.lower()),
        content_id=content_id,
        content_data=content_data,
        publish_function=publish_function,
        output_dir=Path("rollout_output")
    )

# Example usage and integration points
def integrate_with_publish_entrypoint(content_data: Dict[str, Any], mode: str = "live") -> Dict[str, Any]:
    """
    Example integration function for existing publish entrypoints

    Usage in existing scripts:
        result = integrate_with_publish_entrypoint(content_data, mode=os.getenv('ROLLOUT_MODE', 'live'))
    """
    manager = RolloutManager()

    config = create_rollout_config(
        mode=mode,
        content_id=content_data.get('id', f"content_{int(time.time())}"),
        content_data=content_data,
        publish_function=lambda output: {'success': True, 'published_id': 'mock_id'}  # Mock publish function
    )

    result = manager.execute_rollout(config)

    return {
        'success': result.success,
        'mode': result.mode.value,
        'content_id': result.content_id,
        'processing_time': result.processing_time,
        'stage_outputs': result.stage_outputs,
        'diff_reports': result.diff_reports,
        'errors': result.errors
    }

if __name__ == "__main__":
    # Example usage
    test_content = {
        'title': 'Test Article',
        'content': 'This is a test article for rollout mode demonstration.',
        'category': 'technical'
    }

    print("🎭 Rollout Manager Demo")
    print("=" * 40)

    # Test all modes
    for mode in ['stage', 'shadow', 'live']:
        print(f"\nTesting {mode.upper()} mode:")
        result = integrate_with_publish_entrypoint(test_content, mode=mode)
        print(f"   Result: {'✅ SUCCESS' if result['success'] else '❌ FAILED'}")
        print(f"   Processing time: {result['processing_time']:.3f}s")
        if result['errors']:
            print(f"   Errors: {result['errors']}")