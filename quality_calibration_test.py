#!/usr/bin/env python3
"""Test different quality score thresholds to calibrate the system"""

import sys
from pathlib import Path

# Add paths for our new services
WEBSITES_DIR = Path(__file__).parent
sys.path.insert(0, str(WEBSITES_DIR / 'scripts' / 'services'))

from episode_quality_scorer import EpisodeQualityScorer, QualityMetrics, ContentCategory


def test_score_calibration():
    """Test different score thresholds for quality tiers"""

    scorer = EpisodeQualityScorer()

    # Test content that should definitely be high quality
    high_quality_content = """
# The Race Condition That Almost Broke Production

So there I was, 2 AM on a Thursday, staring at logs that made no sense. Our payment processing system was losing transactions randomly - like once every 500 requests. In development? Perfect. In staging? Perfect. Production? Chaos.

The issue was subtle. We had this shared cache map that multiple goroutines were hitting. One goroutine writing payment states, another reading to validate transactions. No mutex. Classic race condition, but finding it was a nightmare.

What made it worse? The bug only triggered under load. Our local testing never hit the concurrency patterns that production saw. I'd spend hours with `go run -race`, but it was clean. The real issue? Different timing in the production environment.

The fix was embarrassing. One line: add a sync.RWMutex. But the lesson? Race conditions don't show up in testing. You need to design for concurrency from day one.

That race condition detector in the IDE? tbh, it's saved me more times than I can count. Don't skip the concurrency checks. Your future self will thank you.

The system? Rock solid now. And I sleep better at night.
"""

    metrics = scorer.score_episode(high_quality_content, ContentCategory.TECHNICAL)

    print("🎯 Quality Score Calibration Analysis")
    print("=" * 50)
    print(f"Current Score: {metrics.overall_score:.3f}")
    print(f"Current Tier: {metrics.quality_tier}")
    print()

    # Test different threshold scenarios
    thresholds = [
        ("Current System", 0.0, 0.3, 0.5, 0.7, 0.85),  # Current thresholds
        ("More Lenient", 0.0, 0.2, 0.4, 0.6, 0.75),     # Lower standards
        ("Conservative", 0.0, 0.4, 0.6, 0.8, 0.9),      # Higher standards
        ("Balanced", 0.0, 0.25, 0.45, 0.65, 0.8),       # Middle ground
    ]

    print("📊 Threshold Analysis for High-Quality Content:")
    print("-" * 50)

    for name, rej_thresh, bronze_thresh, silver_thresh, gold_thresh, plat_thresh in thresholds:
        # Calculate tier based on thresholds
        score = metrics.overall_score
        if score < bronze_thresh:
            tier = "REJECTED" if score >= rej_thresh else "REJECTED"
        elif score < silver_thresh:
            tier = "BRONZE"
        elif score < gold_thresh:
            tier = "SILVER"
        elif score < plat_thresh:
            tier = "GOLD"
        else:
            tier = "PLATINUM"

        publish_ready = tier not in ["REJECTED"]

        print(f"{name:15} | Score: {score:.3f} | Tier: {tier:8} | Ready: {'✅' if publish_ready else '❌'}")

    print()
    print("💡 Recommendation:")
    print("Based on the test content quality, consider adjusting thresholds to be")
    print("more lenient for high-quality narrative content while maintaining")
    print("strict standards for technical accuracy and factual content.")


if __name__ == "__main__":
    test_score_calibration()