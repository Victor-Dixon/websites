#!/usr/bin/env python3
"""
Example Publish Script with Rollout Integration
===============================================

Demonstrates how existing publish scripts can integrate rollout modes
without changing their core logic.

Usage:
    # Stage mode - validation only
    export ROLLOUT_MODE=stage
    python example_publish_with_rollout.py

    # Shadow mode - full processing, diff reports
    export ROLLOUT_MODE=shadow
    python example_publish_with_rollout.py

    # Live mode - production deployment
    export ROLLOUT_MODE=live
    python example_publish_with_rollout.py
"""

import sys
from pathlib import Path

# Add rollout integration
sys.path.insert(0, str(Path(__file__).parent))
from rollout_integration import process_content_with_rollout

def mock_publish_function(content_output):
    """Mock publishing function for demonstration"""
    print(f"   📤 Mock publishing content: {content_output.get('title', 'Untitled')}")
    print(f"   📊 Content length: {len(content_output.get('content', ''))}")
    return {
        'success': True,
        'published_id': f"mock_{content_output.get('title', 'untitled').lower().replace(' ', '_')}",
        'url': f"https://example.com/{content_output.get('title', 'untitled').lower().replace(' ', '_')}"
    }

def main():
    """Example publish workflow with rollout integration"""

    # Example content to publish
    content_data = {
        'id': 'example_tech_article',
        'title': 'Understanding Microservices Architecture',
        'content': '''
# Understanding Microservices Architecture

Microservices have revolutionized how we build and deploy applications. But what exactly are they, and why do they matter?

## What are Microservices?

Microservices are an architectural approach where a single application is composed of many loosely coupled and independently deployable smaller services.

## Benefits

- **Scalability**: Scale individual services based on demand
- **Technology Diversity**: Use different technologies for different services
- **Team Autonomy**: Teams can work independently on different services
- **Fault Isolation**: Failure in one service doesn't bring down the entire application

## Challenges

- **Complexity**: Distributed systems are inherently more complex
- **Data Consistency**: Maintaining consistency across services
- **Testing**: Integration testing becomes more critical
- **Operational Overhead**: More services mean more to monitor and maintain

The key is finding the right balance between complexity and benefits for your specific use case.
        '''.strip(),
        'category': 'technical',
        'tags': ['architecture', 'microservices', 'scalability'],
        'author': 'Digital Dreamscape'
    }

    print("🚀 Example Publish with Rollout Integration")
    print("=" * 50)
    print(f"Content: {content_data['title']}")
    print(f"Category: {content_data['category']}")
    print()

    # Process content through rollout pipeline
    # This automatically handles stage/shadow/live modes based on ROLLOUT_MODE env var
    result = process_content_with_rollout(
        content_data=content_data,
        publish_function=mock_publish_function
    )

    print("\n📊 Processing Results:")
    print(f"   Mode: {result['mode'].upper()}")
    print(f"   Success: {'✅ YES' if result['success'] else '❌ NO'}")
    print(".3f")

    # Mode-specific result display
    if result['mode'] == 'stage':
        print("   📋 Stage Validation:")
        print(f"   Services Ready: {result.get('services_ready', {})}")
        print(f"   Configuration Valid: {result.get('configuration_valid', False)}")

    elif result['mode'] == 'shadow':
        print("   👤 Shadow Analysis:")
        print(f"   Pipeline Completed: {result.get('pipeline_completed', False)}")
        print(f"   Diff Reports: {result.get('diff_reports_count', 0)}")
        print(".3f")

    elif result['mode'] == 'live':
        print("   🔥 Live Publishing:")
        print(f"   Published: {result.get('published', False)}")
        print(f"   Quality Score: {result.get('quality_score', 0):.3f}")
        print(f"   Stages Completed: {result.get('stages_completed', 0)}")

    if result.get('errors'):
        print(f"\n🚨 Errors ({len(result['errors'])}):")
        for error in result['errors'][:3]:  # Show first 3
            print(f"   • {error}")

    print("\n✨ Rollout integration complete!")
    print("   Existing publish scripts can be updated similarly.")
    print("   Set ROLLOUT_MODE environment variable to control execution mode.")

if __name__ == "__main__":
    main()