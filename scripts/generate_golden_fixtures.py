#!/usr/bin/env python3
"""
Generate Golden Master Test Fixtures
====================================

Creates 45+ comprehensive test fixtures covering all content types,
categories, and edge cases for the consolidated content pipeline.
"""

import json
import os
from pathlib import Path
from typing import Dict, List, Any
import random

class GoldenFixtureGenerator:
    """Generates comprehensive golden test fixtures"""

    CATEGORIES = ["technical", "strategic", "operational", "narrative", "learning", "reflection"]
    CONTENT_TYPES = ["devlog", "conversation", "coordination", "discord"]
    QUALITY_TIERS = ["PLATINUM", "GOLD", "SILVER", "BRONZE", "REJECTED"]

    # Sample content templates for different scenarios
    CONTENT_TEMPLATES = {
        "technical_high": """
# Advanced Performance Optimization Techniques

Working on a high-throughput system that needed to handle 10k requests per second. The bottleneck was clear: our database queries were N+1 hell. Each request triggered 47 separate DB calls. Ouch.

The solution? Query consolidation with eager loading. Instead of fetching users, then their posts, then comments - one massive join query. Result? From 47 queries to 3. Response time dropped from 2.3 seconds to 147ms.

But the real insight? Premature optimization is the root of all evil, but planned optimization is your competitive advantage. Know your bottlenecks before they break you.

The system now scales beautifully. And I sleep better knowing we can handle 5x traffic without breaking a sweat.
""",

        "technical_medium": """
## Database Migration Strategy

Implementing a zero-downtime database migration for our user authentication system. The challenge: migrating 2M user records while keeping the system live.

Current approach: Dual-writing to both old and new tables, then gradually cut over. Using feature flags to control the migration path.

Key considerations:
- Backward compatibility during transition
- Rollback strategy if issues arise
- Data consistency validation
- Performance impact monitoring

This should be straightforward, but database migrations always have surprises.
""",

        "operational_status": """
## Daily Status Update

Completed the API documentation for the payment service. Fixed 3 validation bugs in the error handling. Need to coordinate with frontend team on the new response format.

Blockers: Waiting for security review approval.
Next: Integration testing with the mobile app.
""",

        "strategic_planning": """
# Q2 Strategic Planning Session

The board meeting was intense. They're pushing for 3x growth, but our infrastructure isn't ready. Current architecture supports 100k daily active users. Target? 500k by end of Q2.

The numbers don't lie: we need microservices. Monolithic scaling won't cut it. But the team resists - "too complex" they say. Yet staying monolithic means we can't scale features independently.

My take: invest now or pay later. The extra 2 months building services buys us 6 months of confidence. Sometimes over-engineering is just engineering done right.

What strategic bet are you avoiding that your future self desperately needs?
""",

        "reflection_personal": """
# The Leadership Lesson I Learned the Hard Way

Two years ago, I made a critical mistake. I was leading a team of 8 developers, and we were behind schedule. Instead of having the tough conversation about scope, I pushed everyone to work weekends.

The result? Burnout. Two developers quit within a month. Morale tanked. We shipped, but the team was broken.

The lesson? Leadership isn't about pushing harder - it's about setting the right direction. Clear priorities, realistic timelines, and genuine care for your people.

Now I ask different questions: "What's the most important thing we can ship this quarter?" "How do we protect the team's well-being?" "What can we learn from this?"

That leadership failure shaped me. I lead differently now. More thoughtfully, more humanely. And the results? Better products, happier teams, sustainable growth.

What leadership mistake taught you your most valuable lesson?
""",

        "learning_tutorial": """
# Building Resilient APIs: A Practical Guide

APIs are the connective tissue of modern applications. But building them resilient? That's an art.

Start with the basics: proper error handling. Don't let one bad request crash your service. Use circuit breakers for downstream calls. Implement retries with exponential backoff.

But the real resilience comes from design. Make your APIs idempotent. Design for failure. What happens when the database is slow? When a service is down? When traffic spikes?

My framework: RED metrics (Rate, Errors, Duration) plus custom business metrics. Monitor everything. Alert on anomalies.

The resilient API isn't complex - it's thoughtful. It anticipates failure and handles it gracefully.

What's your strategy for building resilient systems?
""",

        "narrative_story": """
# The Night the System Went Down

It was 3:17 AM when the alert hit. "Database connection pool exhausted." Our core payment system was down. Customers couldn't complete purchases. Revenue stopped.

The on-call engineer (me) jumped into action. First check: monitoring dashboards. Connection pool at 100% utilization. Cause? A memory leak in the payment processor.

The fix was surgical: restart the service, implement connection pool monitoring, add circuit breaker. System back online in 23 minutes.

But the real story? The preparation. We had runbooks. We had monitoring. We had practiced this scenario. When crisis hit, we were ready.

That night taught me: systems fail. But well-prepared teams don't. Invest in reliability engineering. It pays dividends when you need it most.

What's your crisis readiness plan?
""",

        "coordination_meeting": """
## Team Standup - Sprint Planning

Attendees: Sarah (PM), Mike (Lead Dev), Alex (QA), Jordan (DevOps)

Topics:
- Sprint goal: Complete payment integration
- Blockers: API documentation incomplete
- Action items: Sarah to finalize requirements, Mike to review architecture

Next steps: Daily check-ins, bi-weekly demos.
""",

        "discord_community": """
Hey team, just pushed the new authentication flow. Uses JWT with refresh tokens, proper expiration handling. Should fix the session timeout issues we've been seeing.

Tested locally and staging. Let me know if you hit any edge cases. The mobile app integration might need some tweaks on the token refresh logic.

cc @frontend @mobile
""",

        "edge_case_empty": "",

        "edge_case_very_short": "OK",

        "edge_case_no_title": "This is just some random content without proper structure or title. It's basically noise that shouldn't be published.",

        "edge_case_code_only": "```\nfunction processData(data) {\n    return data.map(item => item.value);\n}\n```",

        "edge_case_urls_only": "Check out these links: https://example.com/page1 https://example.com/page2 https://github.com/user/repo",

        "edge_case_emojis_only": "🚀 ✨ 🎯 💡 🔥 ⚡",

        "edge_case_numbers_only": "2024 01 15 14 30 45 100 500 1000 10000",

        "edge_case_repeated": "This content is just repeated text. This content is just repeated text. This content is just repeated text. This content is just repeated text."
    }

    def __init__(self, fixtures_dir: Path):
        self.fixtures_dir = fixtures_dir
        self.fixtures_dir.mkdir(parents=True, exist_ok=True)

    def generate_fixtures(self, count: int = 50) -> List[Dict[str, Any]]:
        """Generate comprehensive test fixtures"""
        fixtures = []

        # Generate targeted fixtures for each category/type combination
        for category in self.CATEGORIES:
            for content_type in self.CONTENT_TYPES:
                # Generate multiple fixtures per combination
                for i in range(2):  # 2 fixtures per category/type combo
                    fixture = self._generate_fixture(category, content_type, i)
                    fixtures.append(fixture)

        # Add edge cases
        edge_cases = [
            ("empty_content", "operational", "edge_case_empty"),
            ("very_short", "operational", "edge_case_very_short"),
            ("no_title", "operational", "edge_case_no_title"),
            ("code_only", "technical", "edge_case_code_only"),
            ("urls_only", "operational", "edge_case_urls_only"),
            ("emojis_only", "narrative", "edge_case_emojis_only"),
            ("numbers_only", "operational", "edge_case_numbers_only"),
            ("repeated_content", "operational", "edge_case_repeated")
        ]

        for edge_id, category, template_key in edge_cases:
            fixture = self._generate_edge_fixture(edge_id, category, template_key)
            fixtures.append(fixture)

        # Trim to requested count
        fixtures = fixtures[:count]

        # Save all fixtures
        for fixture in fixtures:
            self._save_fixture(fixture)

        return fixtures

    def _generate_fixture(self, category: str, content_type: str, index: int) -> Dict[str, Any]:
        """Generate a single fixture"""
        fixture_id = f"{category}_{content_type}_{index:02d}"

        # Select appropriate content template
        template_key = self._select_template(category, content_type)

        # Generate expected quality based on content
        expected_tier, publish_ready = self._predict_quality(category, template_key)

        fixture = {
            "id": fixture_id,
            "title": self._generate_title(category, content_type, index),
            "category": category,
            "content_type": content_type,
            "raw_content": self.CONTENT_TEMPLATES[template_key].strip(),
            "tags": self._generate_tags(category, content_type),
            "expected_quality_tier": expected_tier,
            "expected_publish_ready": publish_ready,
            "metadata": {
                "template_used": template_key,
                "generated": True,
                "test_category": f"{category}_{content_type}"
            }
        }

        return fixture

    def _generate_edge_fixture(self, edge_id: str, category: str, template_key: str) -> Dict[str, Any]:
        """Generate edge case fixture"""
        expected_tier, publish_ready = self._predict_quality(category, template_key)

        fixture = {
            "id": edge_id,
            "title": self._generate_edge_title(edge_id),
            "category": category,
            "content_type": "edge_case",
            "raw_content": self.CONTENT_TEMPLATES[template_key].strip(),
            "tags": ["edge_case", "testing"],
            "expected_quality_tier": expected_tier,
            "expected_publish_ready": publish_ready,
            "metadata": {
                "template_used": template_key,
                "generated": True,
                "test_category": "edge_case"
            }
        }

        return fixture

    def _select_template(self, category: str, content_type: str) -> str:
        """Select appropriate content template"""
        # Map category + content_type to template
        template_map = {
            ("technical", "devlog"): "technical_high",
            ("technical", "conversation"): "technical_medium",
            ("strategic", "devlog"): "strategic_planning",
            ("operational", "devlog"): "operational_status",
            ("reflection", "devlog"): "reflection_personal",
            ("learning", "devlog"): "learning_tutorial",
            ("narrative", "devlog"): "narrative_story",
            ("operational", "coordination"): "coordination_meeting",
            ("technical", "discord"): "discord_community"
        }

        key = (category, content_type)
        return template_map.get(key, "operational_status")

    def _predict_quality(self, category: str, template_key: str) -> tuple[str, bool]:
        """Predict expected quality tier and publish readiness"""
        # Quality predictions based on content analysis
        quality_map = {
            "technical_high": ("BRONZE", True),
            "technical_medium": ("BRONZE", True),
            "strategic_planning": ("SILVER", True),
            "operational_status": ("REJECTED", False),
            "reflection_personal": ("REJECTED", False),  # Too long for current thresholds
            "learning_tutorial": ("BRONZE", True),
            "narrative_story": ("BRONZE", True),
            "coordination_meeting": ("REJECTED", False),
            "discord_community": ("REJECTED", False),
            "edge_case_empty": ("REJECTED", False),
            "edge_case_very_short": ("REJECTED", False),
            "edge_case_no_title": ("REJECTED", False),
            "edge_case_code_only": ("REJECTED", False),
            "edge_case_urls_only": ("REJECTED", False),
            "edge_case_emojis_only": ("REJECTED", False),
            "edge_case_numbers_only": ("REJECTED", False),
            "edge_case_repeated": ("REJECTED", False)
        }

        return quality_map.get(template_key, ("REJECTED", False))

    def _generate_title(self, category: str, content_type: str, index: int) -> str:
        """Generate appropriate title"""
        title_templates = {
            "technical": [f"Technical Solution {index+1}", f"Debug Session {index+1}", f"Performance Optimization {index+1}"],
            "strategic": [f"Strategic Planning {index+1}", f"Architecture Decision {index+1}", f"Roadmap Discussion {index+1}"],
            "operational": [f"Status Update {index+1}", f"System Check {index+1}", f"Maintenance Report {index+1}"],
            "narrative": [f"Team Story {index+1}", f"Project Journey {index+1}", f"Development Adventure {index+1}"],
            "learning": [f"Learning Experience {index+1}", f"Tutorial Insights {index+1}", f"Knowledge Share {index+1}"],
            "reflection": [f"Personal Reflection {index+1}", f"Leadership Lesson {index+1}", f"Career Milestone {index+1}"]
        }

        templates = title_templates.get(category, ["Content Item"])
        return templates[index % len(templates)]

    def _generate_edge_title(self, edge_id: str) -> str:
        """Generate title for edge cases"""
        title_map = {
            "empty_content": "Empty Content",
            "very_short": "Very Short Content",
            "no_title": "Content Without Title",
            "code_only": "Code Snippet Only",
            "urls_only": "URL List Only",
            "emojis_only": "Emoji Content Only",
            "numbers_only": "Numeric Content Only",
            "repeated_content": "Repeated Content"
        }
        return title_map.get(edge_id, "Edge Case Content")

    def _generate_tags(self, category: str, content_type: str) -> List[str]:
        """Generate relevant tags"""
        base_tags = [category, content_type]

        category_tags = {
            "technical": ["coding", "debugging", "performance"],
            "strategic": ["planning", "architecture", "leadership"],
            "operational": ["status", "maintenance", "monitoring"],
            "narrative": ["story", "experience", "journey"],
            "learning": ["tutorial", "knowledge", "skills"],
            "reflection": ["personal", "growth", "lessons"]
        }

        specific_tags = category_tags.get(category, [])
        return base_tags + random.sample(specific_tags, min(2, len(specific_tags)))

    def _save_fixture(self, fixture: Dict[str, Any]) -> None:
        """Save fixture to JSON file"""
        fixture_path = self.fixtures_dir / f"{fixture['id']}.json"
        with open(fixture_path, 'w', encoding='utf-8') as f:
            json.dump(fixture, f, indent=2, ensure_ascii=False)


def main():
    """Generate golden master fixtures"""
    fixtures_dir = Path(__file__).parent.parent / "tests" / "golden_master" / "fixtures"

    generator = GoldenFixtureGenerator(fixtures_dir)
    fixtures = generator.generate_fixtures(50)

    print(f"✅ Generated {len(fixtures)} golden master test fixtures")
    print(f"📁 Saved to: {fixtures_dir}")

    # Summary by category
    categories = {}
    for fixture in fixtures:
        cat = fixture['category']
        categories[cat] = categories.get(cat, 0) + 1

    print("\n📊 Fixture Summary by Category:")
    for category, count in sorted(categories.items()):
        print(f"   {category}: {count} fixtures")

    print("\n🎯 Ready for golden master testing!")
    print("   Run: python scripts/generate_golden_baseline.py")


if __name__ == "__main__":
    main()