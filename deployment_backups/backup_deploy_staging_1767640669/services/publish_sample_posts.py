#!/usr/bin/env python3
"""
Publish sample blog posts to WordPress for design testing
"""

import os
from pathlib import Path
import requests
from requests.auth import HTTPBasicAuth
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

def publish_post(title: str, content: str, excerpt: str = None):
    """Publish a post to WordPress"""
    wp_url = os.environ.get('DREAM_WP_URL')
    wp_user = os.environ.get('DREAM_WP_USER')
    wp_pass = os.environ.get('DREAM_WP_APP_PASS')

    if not all([wp_url, wp_user, wp_pass]):
        print("❌ Missing WordPress credentials")
        return None

    api_url = f"{wp_url}/wp-json/wp/v2/posts"

    payload = {
        "title": title,
        "content": content,
        "status": "publish",
        "excerpt": excerpt or title
    }

    auth = HTTPBasicAuth(wp_user, wp_pass)

    try:
        response = requests.post(api_url, auth=auth, json=payload, timeout=30)
        if response.status_code in [200, 201]:
            data = response.json()
            print(f"✅ Published: {title}")
            print(f"   URL: {data.get('link')}")
            return data.get('link')
        else:
            print(f"❌ Failed to publish {title}: HTTP {response.status_code}")
            print(f"   Response: {response.text[:200]}")
            return None
    except Exception as e:
        print(f"❌ Error publishing {title}: {e}")
        return None

def create_sample_posts():
    """Create and publish 10 sample blog posts"""

    posts = [
        {
            "title": "Building in Public: The Ultimate Guide for 2025",
            "excerpt": "Building in public has become the gold standard for modern entrepreneurship",
            "content": """
# Building in Public: The Ultimate Guide for 2025

## Introduction

Building in public has become the gold standard for modern entrepreneurship. In 2025, transparency isn't just a buzzword—it's a strategic advantage that can accelerate your growth, build trust, and create unprecedented opportunities.

## The Evolution of Build in Public

What started as a niche approach for indie hackers has evolved into a fundamental business strategy. Companies like Stripe, Linear, and Superhuman built massive followings by sharing their development process openly.

## Strategic Benefits

### 1. Accelerated Learning
Building in public forces you to articulate your thinking clearly, leading to better decision-making and faster iteration cycles.

### 2. Community Building
Transparent development creates natural opportunities for community engagement and feedback loops.

### 3. Talent Acquisition
Top talent is drawn to companies that demonstrate transparency and thoughtful problem-solving.

## Conclusion

Building in public in 2025 isn't just about visibility—it's about creating genuine connections, accelerating learning, and building businesses that matter.
            """
        },
        {
            "title": "How to Monetize Your Stream Without Selling Out",
            "excerpt": "Ethical monetization strategies for content creators",
            "content": """
# How to Monetize Your Stream Without Selling Out

## The Creator Economy Challenge

Content creators face constant pressure to monetize while maintaining authenticity. The key is finding sustainable revenue streams that align with your values and audience expectations.

## Ethical Monetization Strategies

### 1. Membership Platforms
Patreon, Memberful, and Buy Me a Coffee offer direct support without compromising content quality.

### 2. Value-Added Products
Digital products, courses, and merchandise that genuinely help your audience.

### 3. Sponsorship Alignment
Partner with brands that share your values and appeal to your audience.

## Building Sustainable Income

Focus on creating genuine value first, then explore monetization opportunities that enhance rather than detract from your content.
            """
        },
        {
            "title": "AI-Powered Content Creation: Boost Your Productivity 10x",
            "excerpt": "Leveraging AI tools for content creation efficiency",
            "content": """
# AI-Powered Content Creation: Boost Your Productivity 10x

## The AI Revolution in Content

Artificial intelligence is transforming how we create, distribute, and optimize content. The key is using AI as a tool to enhance human creativity, not replace it.

## Practical AI Applications

### Content Ideation
AI can generate topic suggestions, headlines, and content structures based on trending topics and audience preferences.

### Writing Assistance
Grammar checking, style suggestions, and content optimization recommendations.

### Visual Content
AI-powered image generation, video editing, and graphic design tools.

## Maximizing Productivity

The most successful content creators use AI to handle routine tasks, freeing up time for strategic thinking and audience engagement.
            """
        },
        {
            "title": "Cracking the Social Media Algorithm in 2026",
            "excerpt": "Data-driven strategies for social media success",
            "content": """
# Cracking the Social Media Algorithm in 2026

## Algorithm Evolution

Social media algorithms continue to evolve, prioritizing meaningful engagement over vanity metrics. Success requires understanding platform-specific ranking factors.

## Key Strategies for 2026

### 1. Community Building
Focus on creating valuable communities rather than chasing viral moments.

### 2. Consistent Value Delivery
Regular, high-quality content that serves your audience's needs.

### 3. Engagement Optimization
Encourage meaningful interactions through questions, polls, and discussions.

## Measuring Success

Track engagement rates, audience growth, and conversion metrics rather than just follower counts.
            """
        },
        {
            "title": "The Ultimate Passive Income Blueprint for Developers",
            "excerpt": "Proven strategies for developer monetization",
            "content": """
# The Ultimate Passive Income Blueprint for Developers

## Developer Income Streams

Developers have unique opportunities to create passive income through digital products and services.

## Proven Strategies

### 1. Open Source Monetization
Build popular open source tools and libraries, then monetize through support, consulting, or premium features.

### 2. Educational Content
Create courses, tutorials, and educational products that generate ongoing revenue.

### 3. SaaS Products
Build software as a service products that require minimal maintenance after launch.

## Implementation Roadmap

Start with your existing skills and audience, then expand into new revenue streams gradually.
            """
        },
        {
            "title": "From 0 to 100K: The Startup Scaling Playbook",
            "excerpt": "Systematic approach to startup growth",
            "content": """
# From 0 to 100K: The Startup Scaling Playbook

## The Scaling Challenge

Moving from early-stage startup to sustainable business requires systematic growth strategies and operational excellence.

## Key Scaling Principles

### 1. Product-Market Fit
Ensure your product solves a real problem for a sizable market before scaling.

### 2. Sustainable Unit Economics
Focus on customer acquisition costs, lifetime value, and profitability from day one.

### 3. Operational Scalability
Build systems and processes that can grow with your team and customer base.

## Execution Framework

Systematic growth requires discipline, data-driven decision making, and continuous optimization.
            """
        },
        {
            "title": "Freelance Negotiation Mastery: Double Your Rates",
            "excerpt": "Confidence building for rate negotiation",
            "content": """
# Freelance Negotiation Mastery: Double Your Rates

## The Negotiation Mindset

Successful freelancers understand that pricing is about value, not time. Building confidence in your worth is the foundation of effective negotiation.

## Rate Setting Strategies

### 1. Value-Based Pricing
Price based on the value you deliver, not hours worked.

### 2. Market Research
Understand industry rates and position yourself competitively.

### 3. Confidence Building
Develop a strong portfolio and case studies that demonstrate your value.

## Negotiation Techniques

Preparation, practice, and understanding client psychology are key to successful rate increases.
            """
        },
        {
            "title": "The Complete Product Launch Checklist for 2026",
            "excerpt": "Comprehensive framework for successful product launches",
            "content": """
# The Complete Product Launch Checklist for 2026

## Launch Preparation

Successful product launches require meticulous planning and execution across multiple domains.

## Pre-Launch Checklist

### 1. Product Readiness
- Core functionality complete
- User testing completed
- Performance optimization finished

### 2. Marketing Preparation
- Target audience identified
- Messaging and positioning defined
- Launch campaign planned

### 3. Operational Readiness
- Support systems in place
- Customer onboarding prepared
- Technical infrastructure scaled

## Launch Execution

Coordinated execution across all channels ensures maximum impact and momentum.
            """
        },
        {
            "title": "Remote Work Productivity: Systems That Actually Work",
            "excerpt": "Battle-tested methods for remote work success",
            "content": """
# Remote Work Productivity: Systems That Actually Work

## The Remote Work Reality

Remote work offers flexibility but requires strong systems to maintain productivity and work-life balance.

## Proven Productivity Systems

### 1. Time Blocking
Dedicated time slots for different types of work and activities.

### 2. Communication Frameworks
Clear expectations and regular check-ins with team members.

### 3. Tool Optimization
Using the right tools for collaboration, project management, and communication.

## Work-Life Balance

Remote work blurs boundaries between work and personal life. Strong boundaries and routines are essential.
            """
        },
        {
            "title": "Personal Branding Blueprint: Build Authority in Your Niche",
            "excerpt": "Strategic positioning for personal brand success",
            "content": """
# Personal Branding Blueprint: Build Authority in Your Niche

## Authority Building Fundamentals

Personal branding is about consistently demonstrating expertise and value in your chosen niche.

## Strategic Positioning

### 1. Niche Selection
Choose a specific area where you can become the go-to expert.

### 2. Content Strategy
Create valuable content that demonstrates your expertise and helps your audience.

### 3. Consistency and Authenticity
Build trust through consistent, authentic communication and value delivery.

## Long-term Authority

Authority building takes time but creates significant advantages in career opportunities and business growth.
            """
        }
    ]

    published_urls = []

    for i, post in enumerate(posts, 1):
        print(f"\n📝 Publishing episode {i}/10: {post['title']}")
        url = publish_post(post['title'], post['content'], post['excerpt'])
        if url:
            published_urls.append(url)

    print(f"\n🎉 Published {len(published_urls)} episodes successfully!")
    print("\n📋 Published URLs:")
    for i, url in enumerate(published_urls, 1):
        print(f"{i}. {url}")

    return published_urls

if __name__ == "__main__":
    create_sample_posts()