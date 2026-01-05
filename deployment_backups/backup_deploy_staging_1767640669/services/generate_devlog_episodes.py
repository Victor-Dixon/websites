#!/usr/bin/env python3
"""
Generate devlog episodes from recent development activity
"""

import requests
from requests.auth import HTTPBasicAuth
import os
from pathlib import Path

def publish_devlog_episode(title, content, excerpt):
    """Publish a devlog episode to Digital Dreamscape"""

    # Load environment variables
    wp_url = os.environ.get('DREAM_WP_URL', 'https://digitaldreamscape.site')
    wp_user = os.environ.get('DREAM_WP_USER', 'dadudekc@gmail.com')
    wp_pass = os.environ.get('DREAM_WP_APP_PASS', 'DuFX5WsrzkMPqJC0czhiaZCh')

    # Publish to WordPress
    api_url = f'{wp_url}/wp-json/wp/v2/posts'
    auth = HTTPBasicAuth(wp_user, wp_pass)

    data = {
        'title': title,
        'content': content,
        'excerpt': excerpt,
        'status': 'publish'
    }

    print(f'📝 Publishing: {title}')
    try:
        response = requests.post(api_url, json=data, auth=auth, timeout=30)

        if response.status_code == 201:
            post_data = response.json()
            post_url = post_data.get('link', 'URL not available')
            print(f'✅ Published: {post_url}')
            return True
        else:
            print(f'❌ Failed: HTTP {response.status_code}')
            return False
    except Exception as e:
        print(f'❌ Error: {e}')
        return False

def generate_devlog_episodes():
    """Generate episodes from recent devlog content"""

    print("🎭 Generating Devlog Episodes for Digital Dreamscape")
    print("=" * 60)

    episodes = [
        {
            "title": "Ollama Integration: From API Failures to Local AI Success",
            "content": """# 🤖 Ollama Integration: From API Failures to Local AI Success

## 🔍 The Discovery: Multiple AI Systems

After hours of debugging API failures, I discovered something crucial: **there were multiple blog generation systems** running in parallel, and I was looking at the wrong one.

### The System Inventory

**1. Voice Pattern Processor** (`ops/deployment/voice_pattern_processor.py`)
- ✅ **OLLAMA-FIRST**: Designed to use Ollama/Qwen as primary method
- ✅ **Smart Fallback**: Falls back to Mistral/OpenAI if Ollama unavailable
- ✅ **Victor Voice Integration**: Handles authentic voice pattern application
- ✅ **Working Implementation**: Successfully processes content with Qwen model

**2. Autoblogger System** (`src/autoblogger/`)
- ❌ **OPENAI-ONLY**: Originally hardcoded to use OpenAI API exclusively
- ❌ **No Ollama Support**: Completely bypassed local LLM capabilities
- ❌ **Failure Source**: This system was failing due to invalid OpenAI API key
- ✅ **NOW FIXED**: Updated to use Ollama/Qwen as primary method

## 🚨 The Root Cause

The autoblogger was failing at content generation because it only supported OpenAI, not the locally downloaded Qwen model. Meanwhile, the voice pattern processor was working perfectly with Ollama.

### The Real Pipeline Flow
```
Content Source: dream.yaml ✅ (episodes ready)
Calendar System: dream.yaml ✅ (January schedule)
Voice Processing: voice_pattern_processor.py ✅ (Ollama-enabled)
Publishing: publish_with_autoblogger.py ❌ (Wrong LLM client)
LLM Generation: autoblogger/llm_client.py ❌ (OpenAI-only)
```

## 🔧 The Fix: Autoblogger LLM Client Update

**File:** `src/autoblogger/llm_client.py`

### Key Changes:
1. **Ollama Discovery Integration** - Auto-discovers running Ollama instances
2. **Model Selection Logic** - Prefers Mistral > Dolphin > OpenAI fallback
3. **Cross-Platform Support** - Works on Linux Mint, Windows, macOS
4. **Timeout Optimization** - Faster failure detection

### Code Structure:
```python
# Auto-discover Ollama
discovery = OllamaDiscovery.discover()
if discovery.available:
    # Use Ollama with preferred model
    return generate_with_ollama(model, prompt)
else:
    # Fallback to OpenAI
    return generate_with_openai(model, prompt)
```

## 🎯 The Result

**Before:** Episodes stuck in backlog, API failures everywhere
**After:** Ollama integration working, episodes publishing successfully

## 💡 Key Lessons

1. **Local AI First**: Ollama provides better privacy, speed, and cost-effectiveness
2. **System Inventory**: Always map out all systems before debugging
3. **Fallback Design**: Multiple LLM options prevent single points of failure
4. **Cross-Platform**: Modern AI systems must work everywhere

## 🚀 Next Steps

The Digital Dreamscape autoblogger now uses **Ollama as primary, OpenAI as fallback**, creating a robust, privacy-focused content generation pipeline.

**Local AI isn't just an option anymore—it's the foundation.** 🤖⚡""",
            "excerpt": "How I discovered and fixed multiple AI systems causing API failures, implementing Ollama-first content generation"
        },
        {
            "title": "Swarm Coordination: Managing 8 AI Agents in Production",
            "content": """# 👥 Swarm Coordination: Managing 8 AI Agents in Production

## 📊 The Challenge: 8-Agent Production System

Managing a swarm of 8 specialized AI agents requires more than just code— it demands **operational discipline, clear boundaries, and relentless coordination**.

### Agent Roles (Current Swarm)
- **Agent-1**: Integration & Core Systems
- **Agent-2**: Architecture & Design
- **Agent-3**: Infrastructure & DevOps
- **Agent-4**: Captain (Strategic Oversight)
- **Agent-5**: Business Intelligence
- **Agent-6**: Coordination & Communication
- **Agent-7**: Web Development
- **Agent-8**: SSOT & System Integration

## 🎯 Coordination Principles

### 1. **Clear Authority Boundaries**
```
Victor: Decision Authority (vision, direction, final calls)
Swarm: Execution Authority (missions, implementation)
Thea: Narrative Authority (canon, coherence, continuity)
```

**No overlap, no confusion, maximum efficiency.**

### 2. **Task Assignment Protocol**
- **Agent-4 (Captain)**: Reviews Master Task Log, assigns critical issues
- **Direct A2A Messaging**: Assignments delivered via dedicated channels
- **Progress Tracking**: Each agent reports completion status
- **Escalation Path**: Issues bubble up through Agent-4 to Victor

### 3. **Repository Management**
- **Dual Repository Sync**: Websites + Agent_Cellphone_V2_Repository
- **Automated Commits**: Changes tracked and pushed regularly
- **Clean State**: No uncommitted work blocks progress

## 🚨 Current Critical Assignments

### Agent-3: Fix freerideinvestor.com Empty Page
- **Issue**: Completely empty page, no content visible
- **Scope**: Database connection, theme integrity investigation
- **Priority**: CRITICAL IMMEDIATE
- **Status**: Awaiting execution

### Agent-7: Website Quality Issues
- **weareswarm.online**: Text rendering broken
- **tradingrobotplug.com**: Placeholder content quality
- **crosbyultimateevents.com**: ✅ FIXED (text rendering)
- **Status**: Multiple sites need attention

## 📈 Swarm Performance Metrics

### Current Status
- **Active Agents**: 2 (reduced from full swarm)
- **Critical Issues**: 4 identified, 1 resolved
- **Infrastructure Tasks**: Trading robot pipeline advancing
- **Repository Health**: ✅ Clean and synchronized

### Communication Flow
```
Victor → Agent-4 → Individual Agents → Progress Reports → Agent-4 → Victor
     ↓           ↓           ↓            ↓            ↓         ↓
  Vision    Assignment  Execution    Updates     Consolidation Results
```

## 🎭 The Human Element

AI agents are powerful, but they need **human oversight**:
- **Victor**: Provides vision and final decisions
- **Captain**: Maintains operational discipline
- **Individual Agents**: Execute specialized tasks

## 💡 Key Insights

1. **Reduced Swarm Efficiency**: With only 2 active agents, progress slows but quality remains high
2. **Assignment Clarity**: Clear task boundaries prevent confusion
3. **Progress Tracking**: Regular status updates maintain momentum
4. **Escalation Works**: Issues are identified and addressed systematically

## 🚀 Scaling the Swarm

As the system grows, coordination becomes even more critical:
- **Task Prioritization**: Critical issues get immediate attention
- **Resource Allocation**: Agents assigned based on specialization
- **Quality Assurance**: Each delivery verified before completion

**The swarm isn't just agents—it's a coordinated system that turns individual capabilities into collective results.** 🤖⚡""",
            "excerpt": "Managing 8 specialized AI agents in production: coordination principles, task assignment, and operational discipline"
        },
        {
            "title": "Building Digital Dreamscape: Automated Episode Generation System",
            "content": """# 🌌 Building Digital Dreamscape: Automated Episode Generation System

## 🎭 The Vision: Living Narrative AI World

Digital Dreamscape isn't just a blog—it's a **living narrative system** where conversations become episodes, decisions create canon, and AI agents shape the story world.

## 🏗️ System Architecture

### Core Components

**1. Content Sources**
- **Agent Conversations**: Every coordination becomes potential narrative
- **DevLogs**: Development activity transformed into episodes
- **System Events**: Infrastructure changes become story elements

**2. Processing Pipeline**
```
Raw Content → Episode Generator → Voice Processing → Publishing
     ↓             ↓                ↓              ↓
  Conversations  Structured Episodes  Victor's Voice   Live Site
```

**3. AI Integration**
- **Ollama Primary**: Local AI for privacy and speed
- **OpenAI Fallback**: Cloud backup for reliability
- **Cross-Platform**: Works on Linux Mint, Windows, macOS

## 📚 Episode Categories

### Current Live Episodes (11)
1. **AI & Productivity**: Technical guides and automation
2. **Creator Economy**: Monetization and content strategies
3. **Business Development**: Scaling and entrepreneurship
4. **Urban Planning**: World-building (Green Pine)
5. **DevLog Series**: System development chronicles

### Content Pipeline Flow
```
DevLog → Episode Structure → Ollama Enhancement → WordPress Publishing
   ↓           ↓                ↓                 ↓
Activity   YAML Metadata    Victor's Voice     Live Episode
```

## 🤖 AI Agent Integration

### The Swarm (8 Agents)
- **Agent-1**: Integration & Core Systems
- **Agent-2**: Architecture & Design
- **Agent-3**: Infrastructure & DevOps
- **Agent-4**: Captain (Strategic Oversight)
- **Agent-5**: Business Intelligence
- **Agent-6**: Coordination & Communication
- **Agent-7**: Web Development
- **Agent-8**: SSOT & System Integration

### Authority Structure
```
Victor: Decision Authority (vision, strategy, final calls)
Swarm: Execution Authority (missions, implementation)
Thea: Narrative Authority (canon, coherence, story)
```

## 🎯 Key Features

### Automated Publishing
- **Calendar-Based**: Episodes publish on scheduled dates
- **Draft Mode**: Content reviewed before going live
- **Auto-Publish**: Fully automated for approved content

### Content Enhancement
- **Voice Processing**: Applies authentic "Victor's voice"
- **Ollama Integration**: Local AI content generation
- **Fallback Systems**: Multiple LLM options for reliability

### World Building
- **Narrative Continuity**: Canon maintained across episodes
- **Character Development**: Agent personalities evolve
- **Story Arcs**: Long-term narrative threads

## 🚀 Current Achievements

### ✅ Working Systems
- **11 Episodes Published** to digitaldreamscape.site
- **Ollama Integration** with automatic discovery
- **WordPress API** publishing with authentication
- **Cross-Platform** compatibility verified

### ✅ Recent Additions
- **Green Pine Lore Pack**: Urban planning narrative
- **DevLog Episodes**: Technical development chronicles
- **Automated Pipeline**: From conversation to published episode

## 💡 Technical Insights

### Architecture Decisions
1. **Local AI First**: Privacy, speed, cost-effectiveness
2. **Multi-System Design**: No single points of failure
3. **YAML-Driven Content**: Structured, maintainable episodes
4. **API-Based Publishing**: Direct WordPress integration

### Development Lessons
1. **System Inventory**: Map all systems before debugging
2. **Fallback Design**: Multiple options prevent failures
3. **Cross-Platform**: Modern systems work everywhere
4. **Narrative Focus**: Technical work becomes story

## 🌟 The Result

**Digital Dreamscape** is now a **living system** where:
- Agent conversations become structured episodes
- Development activity transforms into narrative content
- Technical challenges evolve into learning experiences
- AI agents contribute to an ongoing story world

**The system doesn't just work—it tells a story about building it.** 🌌⚡🤖""",
            "excerpt": "Building Digital Dreamscape: automated episode generation system that turns conversations and devlogs into published narrative content"
        }
    ]

    print(f"📚 Publishing {len(episodes)} devlog episodes...")

    success_count = 0
    for i, episode in enumerate(episodes, 1):
        print(f"\n🎭 Episode {i}/{len(episodes)}")
        if publish_devlog_episode(episode["title"], episode["content"], episode["excerpt"]):
            success_count += 1

    print(f"\n🎉 Published {success_count}/{len(episodes)} devlog episodes successfully!")
    print("🌐 Check https://digitaldreamscape.site/blog/ for the new episodes")

    return success_count

if __name__ == "__main__":
    generate_devlog_episodes()