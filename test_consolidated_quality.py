#!/usr/bin/env python3
"""Test the consolidated quality assessment service"""

import sys
from pathlib import Path

# Add paths for our new services
WEBSITES_DIR = Path(__file__).parent
sys.path.insert(0, str(WEBSITES_DIR / 'scripts' / 'services'))

from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, ContentCategory

def test_consolidated_service():
    """Test the new consolidated quality assessment service"""

    service = ConsolidatedQualityAssessmentService()

    test_content = """
# The Race Condition That Almost Broke Production

So there I was, 2 AM on a Thursday, staring at logs that made no sense. Our payment processing system was losing transactions randomly - like once every 500 requests.

The issue was subtle. We had this shared cache map that multiple goroutines were hitting. One goroutine writing payment states, another reading to validate transactions. No mutex. Classic race condition.

What made it worse? The bug only triggered under load. The fix was embarrassing. One line: add a sync.RWMutex. But the lesson? Race conditions don't show up in testing. You need to design for concurrency from day one.

That race condition detector in the IDE? tbh, it's saved me more times than I can count.
"""

    metrics = service.assess_content_quality(test_content, ContentCategory.TECHNICAL)

    print("🎯 CONSOLIDATED QUALITY ASSESSMENT TEST")
    print("=" * 50)
    print(f"Overall Score: {metrics.overall_score:.3f}")
    print(f"Quality Tier: {metrics.quality_tier.value}")
    print(f"Publish Ready: {'✅ YES' if metrics.publish_ready else '❌ NO'}")
    print(f"Description: {metrics.tier_description}")
    print()
    print("📊 Detailed Metrics:")
    print(f"  Victor Voice Authenticity: {metrics.victor_voice_authenticity:.3f}")
    print(f"  Content Density: {metrics.content_density:.3f}")
    print(f"  Factual Accuracy: {metrics.factual_accuracy:.3f}")
    print(f"  Technical Terms Found: {metrics.technical_term_count}")
    print(f"  Victor Phrases Found: {metrics.victor_phrase_count}")
    print()
    print("🔍 Threshold Analysis:")
    print(f"  PLATINUM (0.75+): {'✅' if metrics.overall_score >= 0.75 else '❌'}")
    print(f"  GOLD (0.60+): {'✅' if metrics.overall_score >= 0.60 else '❌'}")
    print(f"  SILVER (0.45+): {'✅' if metrics.overall_score >= 0.45 else '❌'}")
    print(f"  BRONZE (0.35+): {'✅' if metrics.overall_score >= 0.35 else '❌'}")

if __name__ == "__main__":
    test_consolidated_service()