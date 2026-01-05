#!/usr/bin/env python3
"""Test the NEW quality assessment system (V2)"""

import sys
from pathlib import Path

# Add paths for our consolidated services
WEBSITES_DIR = Path(__file__).parent.parent.parent
SERVICES_DIR = WEBSITES_DIR / "scripts" / "services"
sys.path.insert(0, str(WEBSITES_DIR))
sys.path.insert(0, str(SERVICES_DIR))

# Use consolidated services instead of deprecated modules
from content_processing_service import ContentProcessingService, EpisodeData
from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, QualityMetrics, ContentCategory


def test_quality_assessment_v2():
    """Test the NEW quality assessment system with 12 criteria"""

    # Initialize our consolidated services
    quality_scorer = ConsolidatedQualityAssessmentService()
    processing_service = ContentProcessingService(quality_scorer)

    # Test cases with different quality levels
    test_cases = [
        {
            "title": "High Quality Technical Episode",
            "content": """
# The Race Condition That Almost Broke Production

So there I was, 2 AM on a Thursday, staring at logs that made no sense. Our payment processing system was losing transactions randomly - like once every 500 requests. In development? Perfect. In staging? Perfect. Production? Chaos.

The issue was subtle. We had this shared cache map that multiple goroutines were hitting. One goroutine writing payment states, another reading to validate transactions. No mutex. Classic race condition, but finding it was a nightmare.

What made it worse? The bug only triggered under load. Our local testing never hit the concurrency patterns that production saw. I'd spend hours with `go run -race`, but it was clean. The real issue? Different timing in the production environment.

The fix was embarrassing. One line: add a sync.RWMutex. But the lesson? Race conditions don't show up in testing. You need to design for concurrency from day one.

Now I ask: "What breaks if two instances run this code simultaneously?" If the answer isn't "nothing", it's not thread-safe.

That race condition detector in the IDE? tbh, it's saved me more times than I can count. Don't skip the concurrency checks. Your future self will thank you.

The system? Rock solid now. And I sleep better at night.
""",
            "category": ContentCategory.TECHNICAL,
            "expected_quality": "high"
        },
        {
            "title": "Medium Quality Status Update",
            "content": """
## Status Update

Working on the integration system. Made some progress on the API endpoints. Need to test the authentication flow. Will update tomorrow with results.

Task completed:
- API design review
- Initial implementation
- Basic testing

Next steps:
- Integration testing
- Error handling
- Documentation
""",
            "category": ContentCategory.OPERATIONAL,
            "expected_quality": "medium"
        },
        {
            "title": "Low Quality Noise",
            "content": """
Status: In progress
Updated: 2024-01-01
Notes: Working on it
""",
            "category": ContentCategory.OPERATIONAL,
            "expected_quality": "low"
        },
        {
            "title": "Rejected Noise",
            "content": """
OK
Done
Test
""",
            "category": ContentCategory.OPERATIONAL,
            "expected_quality": "rejected"
        },
        {
            "title": "Narrative/Reflection High Quality",
            "content": """
# The Architecture Decision That Almost Killed the Company

Six months ago, we made what seemed like a small choice: event sourcing for our domain events. The team pushed back hard. "Overkill!" they said. "We're moving fast - CRUD tables would be simpler!" The product manager wanted features yesterday. The CEO questioned the timeline.

But I fought for it. Something in my gut said this was important. We spent an extra two weeks building the event store, the projectors, the replay capabilities. The team grumbled. "Why complicate things?" they asked.

Then the crisis hit. A critical bug corrupted our core transaction data. With traditional CRUD, we'd have lost everything. Recovery would take weeks. But with event sourcing? We replayed the events from the last good backup. System back online in 4 hours.

The audit requirements came next. "Show us every change to customer balances," regulators demanded. With events? Trivial. We had perfect audit trails from day one.

The complex correlations the data team needed? Event streams made it possible. "What happened to this account in the last 30 days?" became a simple query.

Now I see it clearly: that "over-engineering" was actually strategic thinking. The extra week bought us months of confidence. The "complexity" became our competitive moat.

The lesson? Sometimes over-engineering is just engineering. Invest in fundamentals. Build for the hard problems you don't see yet. Your future self won't just thank you - they'll celebrate.

That architectural foundation? It's the reason we're still in business. And growing faster than ever.

What complex decision are you avoiding today that your future self needs desperately?
""",
            "category": ContentCategory.REFLECTION,
            "expected_quality": "high"
        }
    ]

    print("🧪 Testing NEW Quality Assessment System (V2)")
    print("=" * 60)
    print("Using 12-criteria advanced metrics instead of 6 basic ones")
    print()

    for i, test_case in enumerate(test_cases, 1):
        print(f"\n{i}. Testing: {test_case['title']}")
        print(f"   Expected: {test_case['expected_quality']}")
        print(f"   Category: {test_case['category'].value}")

        # Create episode data using our new EpisodeData class
        episode = EpisodeData(
            source_file='test_file.md',
            content_type='devlog',
            agent_id='test-agent',
            timestamp='2024-01-01',
            title=test_case['title'],
            raw_content=test_case['content'].strip(),
            category=test_case['category'],
            tags=[test_case['category'].value],
            episode_id=f'test-{i:03d}'
        )

        # Apply Victor voice transformation
        processed = processing_service.apply_victor_transformation(episode)

        # Get the detailed metrics
        metrics = processed.quality_metrics
        score = processed.quality_score
        ready = processed.publish_ready

        # Debug: Show original vs transformed content
        print(f"   Original: {episode.raw_content[:100]}...")
        print(f"   Victor Voice: {processed.victor_content[:120]}...")

        # Debug: Check what Victor phrases are found
        victor_phrases_found = []
        for phrase in ['idk', 'tbh', 'kinda', 'tryna', 'lowkey', 'gon', 'wanna', 'js', 'so now', 'for real though', 'lowkey feel like', 'but also', 'its basically', 'makes sense']:
            if phrase in processed.victor_content.lower():
                victor_phrases_found.append(phrase)

        print(f"   Victor phrases found: {victor_phrases_found}")
        print()

        # Show overall results
        print(f"   🎯 Overall Score: {score:.3f} ({metrics.quality_tier.value})")
        print(f"   📤 Publish Ready: {'✅ Yes' if ready else '❌ No'}")
        print()

        # Show detailed breakdown by category
        print("   📊 Detailed Metrics Breakdown:")
        print(f"      Content Density: {metrics.content_density:.3f}")
        print(
            f"      Structural Integrity: {metrics.structural_integrity:.3f}")
        print(f"      Factual Accuracy: {metrics.factual_accuracy:.3f}")
        print(f"      Storytelling Flow: {metrics.storytelling_flow:.3f}")
        print(f"      Emotional Resonance: {metrics.emotional_resonance:.3f}")
        print(f"      Insight Density: {metrics.insight_density:.3f}")
        print(
            f"      Victor Voice Authenticity: {metrics.victor_voice_authenticity:.3f}")
        print(f"      Readability Score: {metrics.readability_score:.3f}")
        print(f"      Conversational Flow: {metrics.conversational_flow:.3f}")
        print(f"      Shareability Score: {metrics.shareability_score:.3f}")
        print(f"      Timelessness: {metrics.timelessness:.3f}")
        print(f"      Formatting Quality: {metrics.formatting_quality:.3f}")
        print()

        # Show metadata
        print("   📋 Content Metadata:")
        print(f"      Word Count: {metrics.word_count}")
        print(f"      Sentence Count: {metrics.sentence_count}")
        print(f"      Technical Terms: {metrics.technical_term_count}")
        print()

        # Show quality tier analysis
        tier = metrics.quality_tier
        expected_ready = test_case['expected_quality'] in ['high', 'medium']

        print("   🎖️  Quality Tier Analysis:")
        print(f"      Tier: {tier}")
        print(f"      Score Range: {metrics.overall_score:.3f}")
        print(
            f"      Expected Ready: {'✅' if expected_ready else '❌'} {test_case['expected_quality']}")
        print(f"      Actual Ready: {'✅' if ready else '❌'} {tier.value.lower()}")

        # Check if result matches expectation (with some tolerance)
        tier_matches = (
            (test_case['expected_quality'] == 'high' and tier in ['PLATINUM', 'GOLD']) or
            (test_case['expected_quality'] == 'medium' and tier in ['GOLD', 'SILVER', 'BRONZE']) or
            (test_case['expected_quality'] == 'low' and tier in ['BRONZE', 'REJECTED']) or
            (test_case['expected_quality'] ==
             'rejected' and tier == 'REJECTED')
        )

        print(f"      Match: {'✅' if tier_matches else '❌'}")
        print()

        # Show top contributing factors
        print("   🏆 Top Contributing Factors:")
        scores_dict = {
            'Content Density': metrics.content_density,
            'Structural Integrity': metrics.structural_integrity,
            'Factual Accuracy': metrics.factual_accuracy,
            'Storytelling Flow': metrics.storytelling_flow,
            'Emotional Resonance': metrics.emotional_resonance,
            'Insight Density': metrics.insight_density,
            'Victor Voice Authenticity': metrics.victor_voice_authenticity,
            'Readability Score': metrics.readability_score,
            'Conversational Flow': metrics.conversational_flow,
            'Shareability Score': metrics.shareability_score,
            'Timelessness': metrics.timelessness,
            'Formatting Quality': metrics.formatting_quality
        }

        top_factors = sorted(scores_dict.items(),
                             key=lambda x: x[1], reverse=True)[:5]
        for factor, factor_score in top_factors:
            print(f"      {factor}: {factor_score:.3f}")
        print()

        print("-" * 80)


def test_victor_voice_transformation():
    """Test Victor voice transformation specifically"""
    print("\n🎭 Testing Victor Voice Transformation")
    print("=" * 50)

    victor_processor = ConsolidatedQualityAssessmentService()

    test_content = """
I think the problem is with the database connection. I believe we need to add proper error handling. However, the current approach should work. Therefore, let's implement it this way.

I want to make sure this is working correctly. Just check the logs and you'll see.
"""

    print("Original Content:")
    print(test_content.strip())
    print()

    # Test different intensities and categories
    test_configs = [
        (ContentCategory.TECHNICAL, 0.6, "Technical - Low Intensity"),
        (ContentCategory.TECHNICAL, 0.9, "Technical - High Intensity"),
        (ContentCategory.NARRATIVE, 0.7, "Narrative - Medium Intensity"),
        (ContentCategory.REFLECTION, 0.8, "Reflection - Medium-High Intensity")
    ]

    for category, intensity, description in test_configs:
        print(f"{description}:")
        transformed = victor_processor.apply_victor_voice(
            test_content, category, intensity)
        print(transformed.strip())
        print()


def test_individual_scorers():
    """Test individual scoring components"""
    print("\n🔬 Testing Individual Scoring Components")
    print("=" * 50)

    quality_scorer = ConsolidatedQualityAssessmentService()

    test_content = """
# The Architecture Decision That Saved Us

Looking back, choosing microservices felt like overkill. But when we needed to scale the payment processing independently from user auth, having separate services made it trivial.

The key insight: decoupling isn't just about technology - it's about business agility. What felt complex upfront became our competitive advantage.

Now I always ask: "What if this component needs to scale 100x?" If the answer requires rewriting everything, it's not decoupled enough.
"""

    print("Test Content:")
    print(test_content.strip())
    print()

    # Test category detection
    detected_category = quality_scorer._detect_category(test_content)
    print(f"📂 Detected Category: {detected_category.value}")
    print()

    # Test individual scorers
    scores = quality_scorer.score_episode(test_content, detected_category)

    print("🎯 Individual Component Scores (0.0-1.0):")
    print(f"      Content Density: {scores.content_density:.3f}")
    print(f"      Structural Integrity: {scores.structural_integrity:.3f}")
    print(f"      Factual Accuracy: {scores.factual_accuracy:.3f}")
    print(f"      Storytelling Flow: {scores.storytelling_flow:.3f}")
    print(f"      Emotional Resonance: {scores.emotional_resonance:.3f}")
    print(f"      Insight Density: {scores.insight_density:.3f}")
    print(
        f"      Victor Voice Authenticity: {scores.victor_voice_authenticity:.3f}")
    print(f"      Readability Score: {scores.readability_score:.3f}")
    print(f"      Conversational Flow: {scores.conversational_flow:.3f}")
    print(f"      Shareability Score: {scores.shareability_score:.3f}")
    print(f"      Timelessness: {scores.timelessness:.3f}")
    print(f"      Formatting Quality: {scores.formatting_quality:.3f}")
    print()

    print(
        f"🏆 Overall Score: {scores.overall_score:.3f} ({scores.quality_tier.value})")


if __name__ == "__main__":
    try:
        test_quality_assessment_v2()
        test_victor_voice_transformation()
        test_individual_scorers()

        print("\n🎉 All tests completed successfully!")
        print("Use this output to understand how the new quality metrics work.")

    except Exception as e:
        print(f"\n❌ Test failed with error: {e}")
        import traceback
        traceback.print_exc()
