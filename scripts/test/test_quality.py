#!/usr/bin/env python3
"""Test the quality assessment system"""

from mass_episode_processor import MassEpisodeProcessor, EpisodeData
import sys
from pathlib import Path

# Add paths
WEBSITES_DIR = Path(__file__).parent
AGENT_CELLPHONE_V2_REPO = WEBSITES_DIR.parent / "Agent_Cellphone_V2_Repository"
sys.path.insert(0, str(WEBSITES_DIR))
sys.path.insert(0, str(AGENT_CELLPHONE_V2_REPO / "systems" /
                "memory" / "memory" / "weaponization"))
sys.path.insert(0, str(AGENT_CELLPHONE_V2_REPO / "systems" /
                "output_flywheel" / "publication"))
sys.path.insert(0, str(AGENT_CELLPHONE_V2_REPO / "tools"))


def test_quality_assessment():
    """Test the quality assessment on sample content"""

    processor = MassEpisodeProcessor()

    # Test cases with different quality levels
    test_cases = [
        {
            "title": "High Quality Technical Episode",
            "content": """
# The Bug That Taught Me About Race Conditions

So I was debugging this weird issue where the system would occasionally lose data. It was happening randomly, like once every few days, and I couldn't reproduce it locally.

The problem? A classic race condition in the async processing pipeline. Two goroutines were accessing the same map without proper synchronization. One was writing, one was reading, and sometimes they'd clash.

What I learned: js don't assume concurrency is safe. Even with channels, you gotta be careful. The fix was simple - add a mutex around the critical section. But finding it took me 3 days of logging and staring at traces.

Now I always check for race conditions first when something behaves nondeterministically. That race condition detector in the IDE? Actually useful.
""",
            "category": "technical",
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
            "category": "operational",
            "expected_quality": "medium"
        },
        {
            "title": "Low Quality Noise",
            "content": """
Status: In progress
Updated: 2024-01-01
Notes: Working on it
""",
            "category": "operational",
            "expected_quality": "low"
        },
        {
            "title": "Rejected Noise",
            "content": """
OK
Done
Test
""",
            "category": "operational",
            "expected_quality": "rejected"
        }
    ]

    print("🧪 Testing Quality Assessment System")
    print("=" * 50)

    for i, test_case in enumerate(test_cases, 1):
        print(f"\n{i}. Testing: {test_case['title']}")
        print(f"   Expected: {test_case['expected_quality']}")

        # Create episode data
        episode = EpisodeData(
            source_file='test',
            content_type='test',
            agent_id=None,
            timestamp='2024-01-01',
            title=test_case['title'],
            raw_content=test_case['content'],
            category=test_case['category'],
            tags=[],
            episode_id='test-001'
        )

        # Apply quality assessment
        processed = processor.apply_victor_voice(episode)
        score = processed.quality_score
        ready = processed.publish_ready

        # Debug: Show transformed content
        print(f"   Transformed: {processed.victor_content[:100]}...")

        print(f"   Score: {score:.3f}")
        print(f"   Ready: {'✅ Yes' if ready else '❌ No'}")

        # Debug: Show component scores
        print("   Breakdown:")
        # Test individual components on the TRANSFORMED content
        comp_score = processor._score_completeness(processed.victor_content)
        rel_score = processor._score_relevance(
            processed.victor_content, test_case['category'])
        uni_score = processor._score_uniqueness(processed.victor_content)
        vic_score = processor._score_victor_voice(processed.victor_content)
        nar_score = processor._score_narrative_value(processed.victor_content)
        tech_score = processor._score_technical_accuracy(
            processed.victor_content, test_case['category'])

        print(
            f"     Completeness: {comp_score:.2f} ({processor.quality_weights['completeness']:.2f})")
        print(
            f"     Relevance: {rel_score:.2f} ({processor.quality_weights['relevance']:.2f})")
        print(
            f"     Uniqueness: {uni_score:.2f} ({processor.quality_weights['uniqueness']:.2f})")
        print(
            f"     Victor Voice: {vic_score:.2f} ({processor.quality_weights['victor_voice']:.2f})")
        print(
            f"     Narrative: {nar_score:.2f} ({processor.quality_weights['narrative_value']:.2f})")
        print(
            f"     Technical: {tech_score:.2f} ({processor.quality_weights['technical_accuracy']:.2f})")

        weighted_total = (comp_score * processor.quality_weights['completeness'] +
                          rel_score * processor.quality_weights['relevance'] +
                          uni_score * processor.quality_weights['uniqueness'] +
                          vic_score * processor.quality_weights['victor_voice'] +
                          nar_score * processor.quality_weights['narrative_value'] +
                          tech_score * processor.quality_weights['technical_accuracy'])
        print(f"     Weighted Total: {weighted_total:.3f}")

        # Debug Victor voice specifically
        victor_indicators = [
            'idk', 'tbh', 'kinda', 'tryna', 'lowkey', 'gon', 'wanna',
            'so now', 'for real though', 'lowkey feel like', 'js wanna',
            'but also', 'its basically', 'makes sense'
        ]
        victor_count = sum(
            1 for indicator in victor_indicators if indicator in processed.victor_content.lower())
        found_words = [
            ind for ind in victor_indicators if ind in processed.victor_content.lower()]
        print(f"     Victor words found: {victor_count} - {found_words}")

        # Quality level
        if score >= 0.8:
            level = "high"
        elif score >= 0.6:
            level = "medium"
        elif score >= 0.4:
            level = "low"
        else:
            level = "rejected"

        print(f"   Level: {level}")

        # Check if matches expectation
        expected_ready = test_case['expected_quality'] in ['high', 'medium']
        match = (ready == expected_ready)
        print(f"   Match: {'✅' if match else '❌'}")


if __name__ == "__main__":
    test_quality_assessment()
