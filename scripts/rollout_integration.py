#!/usr/bin/env python3
"""
Rollout Integration - Wire Stage/Shadow/Live Modes into Publish Entrypoints
==========================================================================

Provides seamless integration of rollout modes into existing publishing workflows:

1. **Environment Variable Control**: Set ROLLOUT_MODE=stage|shadow|live
2. **Automatic Mode Detection**: Falls back to LIVE mode if not specified
3. **Drop-in Replacement**: Existing scripts work unchanged
4. **Enhanced Logging**: Mode-aware logging and reporting

Usage:
    export ROLLOUT_MODE=shadow
    python publish_episode_with_categories.py episode_file.md
"""

import os
import sys
import json
from pathlib import Path
from typing import Dict, Any, Optional, Callable
from datetime import datetime

# Add scripts to path
sys.path.insert(0, str(Path(__file__).parent))

from rollout_manager import RolloutManager, create_rollout_config, RolloutMode

class RolloutIntegration:
    """Integrates rollout modes into existing publish workflows"""

    def __init__(self):
        self.manager = RolloutManager()
        self.mode = self._detect_rollout_mode()
        self.output_dir = Path("rollout_output")
        self.output_dir.mkdir(parents=True, exist_ok=True)

        print(f"🎭 Rollout Integration Active - Mode: {self.mode.value.upper()}")

    def _detect_rollout_mode(self) -> RolloutMode:
        """Detect rollout mode from environment variable"""
        mode_env = os.getenv('ROLLOUT_MODE', 'live').lower()

        mode_map = {
            'stage': RolloutMode.STAGE,
            'shadow': RolloutMode.SHADOW,
            'live': RolloutMode.LIVE
        }

        if mode_env not in mode_map:
            print(f"⚠️  Unknown ROLLOUT_MODE '{mode_env}', defaulting to LIVE")
            return RolloutMode.LIVE

        return mode_map[mode_env]

    def process_content_for_publishing(self, content_data: Dict[str, Any],
                                     publish_function: Optional[Callable] = None) -> Dict[str, Any]:
        """
        Process content through rollout pipeline

        Args:
            content_data: Content to process (title, content, category, etc.)
            publish_function: Optional function to call for publishing (only in LIVE mode)

        Returns:
            Processing result with rollout-aware behavior
        """
        content_id = content_data.get('id', f"content_{int(datetime.now().timestamp())}")

        # Create rollout configuration
        config = create_rollout_config(
            mode=self.mode.value,
            content_id=content_id,
            content_data=content_data,
            publish_function=publish_function,
            output_dir=self.output_dir
        )

        # Execute rollout
        result = self.manager.execute_rollout(config)

        # Log results based on mode
        self._log_rollout_result(result)

        # Return mode-appropriate response
        return self._format_response(result)

    def _log_rollout_result(self, result):
        """Log rollout results with mode-appropriate detail"""
        mode_indicator = {
            RolloutMode.STAGE: "📋",
            RolloutMode.SHADOW: "👤",
            RolloutMode.LIVE: "🔥"
        }

        print(f"{mode_indicator[result.mode]} {result.mode.value.upper()} Mode Result: {'✅ SUCCESS' if result.success else '❌ FAILED'}")
        print(f"   Processing time: {result.processing_time:.3f}s")
        print(f"   Stages completed: {len(result.stage_outputs)}")

        if result.diff_reports:
            print(f"   📊 Diff reports generated: {len(result.diff_reports)}")

        if result.errors:
            print(f"   🚨 Errors: {len(result.errors)}")
            for error in result.errors[:3]:  # Show first 3 errors
                print(f"      • {error}")

        # Mode-specific logging
        if result.mode == RolloutMode.SHADOW and result.diff_reports:
            avg_match = sum(r.get('match_score', 0) for r in result.diff_reports) / len(result.diff_reports)
            print(".1%")

        elif result.mode == RolloutMode.STAGE:
            services_status = result.stage_outputs.get('services_status', {})
            if all(services_status.values()):
                print("   ✅ All services ready for rollout")
            else:
                print("   ⚠️  Service availability issues detected")

    def _format_response(self, result) -> Dict[str, Any]:
        """Format response based on rollout mode"""
        base_response = {
            'success': result.success,
            'mode': result.mode.value,
            'content_id': result.content_id,
            'processing_time': result.processing_time,
            'errors': result.errors
        }

        if result.mode == RolloutMode.STAGE:
            # Stage mode: Return validation results
            base_response.update({
                'validation_passed': result.success,
                'services_ready': result.stage_outputs.get('services_status', {}),
                'configuration_valid': result.stage_outputs.get('output_dir_ready', False)
            })

        elif result.mode == RolloutMode.SHADOW:
            # Shadow mode: Return diff analysis
            base_response.update({
                'pipeline_completed': bool(result.stage_outputs.get('consolidated_result', {}).get('success')),
                'diff_reports_count': len(result.diff_reports),
                'quality_score': result.stage_outputs.get('consolidated_result', {}).get('quality_score', 0),
                'diff_reports_available': len(result.diff_reports) > 0
            })

        elif result.mode == RolloutMode.LIVE:
            # Live mode: Return publishing results
            consolidated = result.stage_outputs.get('consolidated_result', {})
            base_response.update({
                'published': result.stage_outputs.get('published', False),
                'quality_score': consolidated.get('quality_score', 0),
                'stages_completed': len(consolidated.get('stages_completed', [])),
                'final_output_ready': consolidated.get('final_output') is not None
            })

        return base_response

# Global integration instance
_rollout_integration = None

def get_rollout_integration():
    """Get or create rollout integration instance"""
    global _rollout_integration
    if _rollout_integration is None:
        _rollout_integration = RolloutIntegration()
    return _rollout_integration

def process_content_with_rollout(content_data: Dict[str, Any],
                                publish_function: Optional[Callable] = None) -> Dict[str, Any]:
    """
    Main integration function for existing publish scripts

    Drop-in replacement for direct content processing. Automatically handles
    rollout modes based on ROLLOUT_MODE environment variable.

    Usage in existing scripts:
        # Replace: result = process_content(content_data)
        # With:     result = process_content_with_rollout(content_data, publish_function)
    """
    integration = get_rollout_integration()
    return integration.process_content_for_publishing(content_data, publish_function)

# Convenience functions for different rollout modes
def validate_content_only(content_data: Dict[str, Any]) -> Dict[str, Any]:
    """Stage mode: Validate content without processing"""
    manager = RolloutManager()
    config = create_rollout_config('stage', content_data.get('id', 'validation'), content_data)
    result = manager.execute_rollout(config)
    return {
        'valid': result.success,
        'services_ready': result.stage_outputs.get('services_status', {}),
        'errors': result.errors
    }

def process_with_diff_analysis(content_data: Dict[str, Any]) -> Dict[str, Any]:
    """Shadow mode: Process and generate diff reports"""
    manager = RolloutManager()
    config = create_rollout_config('shadow', content_data.get('id', 'shadow'), content_data)
    result = manager.execute_rollout(config)
    return {
        'processed': result.success,
        'diff_reports': result.diff_reports,
        'quality_score': result.stage_outputs.get('consolidated_result', {}).get('quality_score', 0),
        'errors': result.errors
    }

def process_for_production(content_data: Dict[str, Any], publish_function: Callable) -> Dict[str, Any]:
    """Live mode: Full production processing and publishing"""
    manager = RolloutManager()
    config = create_rollout_config('live', content_data.get('id', 'live'), content_data, publish_function)
    result = manager.execute_rollout(config)
    return {
        'published': result.success and result.stage_outputs.get('published', False),
        'quality_score': result.stage_outputs.get('consolidated_result', {}).get('quality_score', 0),
        'processing_time': result.processing_time,
        'errors': result.errors
    }

if __name__ == "__main__":
    # Demo script
    print("🎭 Rollout Integration Demo")
    print("=" * 40)

    # Test content
    test_content = {
        'id': 'demo_content',
        'title': 'Rollout Integration Demo',
        'content': 'This is a demonstration of the rollout integration system for Phase 4.',
        'category': 'technical'
    }

    # Test different modes
    modes_to_test = ['stage', 'shadow', 'live']

    for mode in modes_to_test:
        print(f"\n🧪 Testing {mode.upper()} mode:")
        os.environ['ROLLOUT_MODE'] = mode

        # Reset integration instance to pick up new mode
        global _rollout_integration
        _rollout_integration = None

        result = process_content_with_rollout(test_content)
        print(f"   Mode: {result['mode']}")
        print(f"   Success: {result['success']}")
        print(".3f")

        # Mode-specific output
        if mode == 'stage':
            print(f"   Services ready: {result.get('services_ready', {})}")
        elif mode == 'shadow':
            print(f"   Diff reports: {result.get('diff_reports_count', 0)}")
        elif mode == 'live':
            print(f"   Published: {result.get('published', False)}")