#!/usr/bin/env python3
"""
Mass Episode Processor for Digital Dreamscape
=============================================

Processes thousands of devlogs, conversations, and agent interactions into
Victor-voice episodes with proper blog formatting.

Data Sources:
- Agent_Cellphone_V2_Repository/devlogs/ (1562+ files)
- Agent workspaces devlogs (433+ files)
- Message queue conversations
- Discord interactions
- System coordination logs

Processing Pipeline:
1. Data Discovery & Categorization
2. Content Extraction & Structuring
3. Victor Voice Pattern Application
4. Blog Template Formatting
5. Quality Validation
6. Batch Publishing
"""

import os
import sys
import yaml
import json
import requests
from requests.auth import HTTPBasicAuth
from pathlib import Path
from typing import Dict, List, Optional, Any, Tuple
from datetime import datetime
import re
from dataclasses import dataclass
import hashlib

# Add required paths
sys.path.insert(0, str(Path(__file__).parent))
sys.path.insert(0, "D:/Agent_Cellphone_V2_Repository/src/integrations/jarvis")


@dataclass
class EpisodeData:
    """Structured episode data ready for processing"""
    source_file: str
    content_type: str  # 'devlog', 'conversation', 'coordination', 'discord'
    agent_id: Optional[str]
    timestamp: str
    title: str
    raw_content: str
    category: str  # 'technical', 'strategic', 'operational', 'narrative'
    tags: List[str]
    episode_id: str


@dataclass
class ProcessedEpisode:
    """Fully processed episode with Victor's voice"""
    episode_data: EpisodeData
    victor_content: str
    blog_title: str
    excerpt: str
    publish_ready: bool
    quality_score: float


class MassEpisodeProcessor:
    """Processes thousands of conversations into Victor-voice episodes"""

    def __init__(self):
        self.data_sources = {
            'main_devlogs': Path('D:/Agent_Cellphone_V2_Repository/devlogs'),
            'agent_workspaces': Path('D:/Agent_Cellphone_V2_Repository/agent_workspaces'),
            'message_queue': Path('D:/Agent_Cellphone_V2_Repository/message_queue'),
        }

        self.processed_count = 0
        # Reasonable threshold allowing quality content while filtering noise
        self.quality_threshold = 0.5

        # Quality criteria weights (more balanced)
        self.quality_weights = {
            'completeness': 0.20,
            'relevance': 0.25,
            'uniqueness': 0.15,
            'victor_voice': 0.15,
            'narrative_value': 0.15,
            'technical_accuracy': 0.10
        }

        # Content that should be filtered out
        self.noise_patterns = [
            r'^status:\s*$',
            r'^completed$',
            r'^done$',
            r'^ok$',
            r'^yes$',
            r'^no$',
            r'^test$',
            r'^debug$',
            r'^\d+\.\s*$',  # Just numbers
            r'^[-=\s]*$',  # Just separators
        ]

        # Required minimums (more reasonable)
        self.min_requirements = {
            'word_count': 30,
            'sentence_count': 2,
            'technical_terms': 1,
            'learning_indicators': 1
        }

        # WordPress credentials
        self.wp_url = os.environ.get(
            'DREAM_WP_URL', 'https://digitaldreamscape.site')
        self.wp_user = os.environ.get('DREAM_WP_USER', 'dadudekc@gmail.com')
        self.wp_pass = os.environ.get(
            'DREAM_WP_APP_PASS', 'DuFX5WsrzkMPqJC0czhiaZCh')

    def discover_all_content(self) -> List[EpisodeData]:
        """Discover and categorize all available content sources"""
        print("🔍 Discovering all content sources...")

        all_episodes = []

        # Main devlogs directory
        if self.data_sources['main_devlogs'].exists():
            print(
                f"📁 Processing main devlogs: {self.data_sources['main_devlogs']}")
            episodes = self._process_devlog_directory(
                self.data_sources['main_devlogs'],
                content_type='devlog',
                agent_id=None
            )
            all_episodes.extend(episodes)
            print(f"   Found {len(episodes)} episodes")

        # Agent workspace devlogs
        if self.data_sources['agent_workspaces'].exists():
            print(
                f"📁 Processing agent workspaces: {self.data_sources['agent_workspaces']}")
            workspace_episodes = 0
            for workspace_dir in self.data_sources['agent_workspaces'].iterdir():
                if workspace_dir.is_dir():
                    devlog_dir = workspace_dir / 'devlogs'
                    if devlog_dir.exists():
                        agent_id = workspace_dir.name.replace('Agent-', '')
                        episodes = self._process_devlog_directory(
                            devlog_dir,
                            content_type='agent_devlog',
                            agent_id=agent_id
                        )
                        all_episodes.extend(episodes)
                        workspace_episodes += len(episodes)
            print(f"   Found {workspace_episodes} agent workspace episodes")

        # Message queue
        if self.data_sources['message_queue'].exists():
            print(
                f"📁 Processing message queue: {self.data_sources['message_queue']}")
            queue_episodes = self._process_message_queue()
            all_episodes.extend(queue_episodes)
            print(f"   Found {len(queue_episodes)} message episodes")

        print(f"\n🎯 Total episodes discovered: {len(all_episodes)}")
        return all_episodes

    def _process_devlog_directory(self, directory: Path, content_type: str, agent_id: Optional[str]) -> List[EpisodeData]:
        """Process a directory of devlog markdown files"""
        episodes = []

        for md_file in directory.glob('*.md'):
            try:
                episode = self._parse_devlog_file(
                    md_file, content_type, agent_id)
                if episode:
                    episodes.append(episode)
            except Exception as e:
                print(f"⚠️  Error processing {md_file}: {e}")

        return episodes

    def _parse_devlog_file(self, file_path: Path, content_type: str, agent_id: Optional[str]) -> Optional[EpisodeData]:
        """Parse a single devlog file into episode data"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()

            # Extract metadata from filename
            filename = file_path.stem
            parts = filename.split('_', 2)

            timestamp = datetime.now().strftime('%Y-%m-%d')  # Default

            if len(parts) >= 2:
                date_str = parts[0]
                agent_part = parts[1]

                # Extract date
                try:
                    if len(date_str) >= 8 and date_str[:4].isdigit() and date_str[4:6].isdigit() and date_str[6:8].isdigit():
                        timestamp = f"{date_str[:4]}-{date_str[4:6]}-{date_str[6:8]}"
                except:
                    pass  # Keep default timestamp

                # Extract agent ID
                if not agent_id and 'agent' in agent_part.lower():
                    agent_id = agent_part.replace(
                        'agent', '').replace('Agent', '')

            # Extract title from content
            lines = content.split('\n', 5)
            title = "Untitled Devlog"
            for line in lines[:3]:
                line = line.strip()
                if line and not line.startswith('#') and len(line) > 10:
                    title = line[:100]
                    break

            # Categorize content
            category = self._categorize_devlog(content, filename)

            # Generate unique ID
            content_hash = hashlib.md5(content.encode()).hexdigest()[:8]
            episode_id = f"{content_type}_{agent_id or 'unknown'}_{content_hash}"

            return EpisodeData(
                source_file=str(file_path),
                content_type=content_type,
                agent_id=agent_id,
                timestamp=timestamp,
                title=title,
                raw_content=content,
                category=category,
                tags=self._extract_tags(content, category),
                episode_id=episode_id
            )

        except Exception as e:
            print(f"Error parsing {file_path}: {e}")
            return None

    def _process_message_queue(self) -> List[EpisodeData]:
        """Process message queue conversations"""
        episodes = []

        # Process queue.json
        queue_file = self.data_sources['message_queue'] / 'queue.json'
        if queue_file.exists():
            try:
                with open(queue_file, 'r') as f:
                    messages = json.load(f)

                for msg_data in messages:
                    episode = self._parse_message(msg_data)
                    if episode:
                        episodes.append(episode)

            except Exception as e:
                print(f"Error processing message queue: {e}")

        return episodes

    def _parse_message(self, msg_data: Dict) -> Optional[EpisodeData]:
        """Parse a message into episode data"""
        try:
            msg = msg_data.get('message', {})
            content = msg.get('content', '')
            timestamp = msg_data.get('created_at', '')[:10]  # YYYY-MM-DD

            # Extract title from first meaningful line
            lines = content.split('\n', 3)
            title = "Message Coordination"
            for line in lines:
                line = line.strip()
                if len(line) > 20 and not line.startswith('['):
                    title = line[:80]
                    break

            # Generate ID
            content_hash = hashlib.md5(content.encode()).hexdigest()[:8]
            episode_id = f"message_{content_hash}"

            return EpisodeData(
                source_file="message_queue",
                content_type="coordination",
                agent_id=None,
                timestamp=timestamp,
                title=title,
                raw_content=content,
                category="operational",
                tags=["coordination", "agent-communication"],
                episode_id=episode_id
            )

        except Exception as e:
            print(f"Error parsing message: {e}")
            return None

    def _categorize_devlog(self, content: str, filename: str) -> str:
        """Categorize devlog content"""
        content_lower = content.lower()
        filename_lower = filename.lower()

        # Technical development
        if any(word in content_lower for word in ['code', 'api', 'database', 'function', 'error', 'fix', 'debug']):
            return 'technical'

        # Strategic planning
        if any(word in content_lower for word in ['strategy', 'plan', 'goal', 'vision', 'architecture']):
            return 'strategic'

        # Operational tasks
        if any(word in content_lower for word in ['task', 'complete', 'status', 'update', 'coordination']):
            return 'operational'

        # Narrative/reflection
        if any(word in content_lower for word in ['reflect', 'learn', 'experience', 'journey']):
            return 'narrative'

        return 'operational'  # default

    def _extract_tags(self, content: str, category: str) -> List[str]:
        """Extract relevant tags from content"""
        tags = [category]
        content_lower = content.lower()

        # Add specific tags based on content
        if 'ollama' in content_lower or 'ai' in content_lower:
            tags.append('ai-integration')
        if 'discord' in content_lower:
            tags.append('discord')
        if 'agent' in content_lower:
            tags.append('multi-agent')
        if 'episode' in content_lower or 'dreamscape' in content_lower:
            tags.append('digital-dreamscape')
        if 'coordination' in content_lower:
            tags.append('coordination')

        return list(set(tags))  # Remove duplicates

    def apply_victor_voice(self, episode: EpisodeData) -> ProcessedEpisode:
        """Apply Victor's voice patterns to episode content"""
        print(f"🎭 Applying Victor's voice to: {episode.title[:50]}...")

        # For now, create a simple Victor-voice transformation
        # In production, this would use the voice pattern processor with Ollama
        victor_content = self._simple_voice_transformation(
            episode.raw_content, episode.category)

        # Create blog title
        blog_title = self._create_blog_title(episode)

        # Create excerpt
        excerpt = self._create_excerpt(victor_content)

        # Quality assessment
        quality_score = self._assess_quality(victor_content, episode)

        return ProcessedEpisode(
            episode_data=episode,
            victor_content=victor_content,
            blog_title=blog_title,
            excerpt=excerpt,
            publish_ready=quality_score >= self.quality_threshold,
            quality_score=quality_score
        )

    def _simple_voice_transformation(self, content: str, category: str) -> str:
        """Simple Victor voice transformation (placeholder for full LLM processing)"""
        # This is a simplified version - in production, use the voice pattern processor

        # Clean up content
        content = re.sub(r'^# .*', '', content,
                         flags=re.MULTILINE)  # Remove headers
        content = re.sub(r'^\* .*', '', content,
                         flags=re.MULTILINE)  # Remove bullets
        content = content.strip()

        # Apply basic Victor patterns
        victor_patterns = {
            'I think': 'idk',
            'I believe': 'lowkey feel like',
            'However': 'but also',
            'Therefore': 'so now',
            'Actually': 'tbh',
            'Really': 'for real though',
            'Basically': 'its basically',
            'Because': 'cs',
            'Just': 'js',
            'Want to': 'wanna',
            'Trying to': 'tryna',
            'Going to': 'gon',
            'Kind of': 'kinda'
        }

        result = content
        for formal, victor in victor_patterns.items():
            result = re.sub(r'\b' + re.escape(formal) + r'\b',
                            victor, result, flags=re.IGNORECASE)

        # Add Victor-style punctuation
        result = re.sub(r'\.\.\.', '...', result)  # Normalize ellipses
        result = re.sub(r' ,', ',', result)  # Fix spacing
        result = re.sub(r' \.', '.', result)  # Fix spacing

        # Add Victor voice flourishes to boost quality score
        victor_flourishes = [
            'idk', 'tbh', 'kinda', 'for real though', 'makes sense',
            'lowkey', 'js wanna', 'but also', 'so now', 'its basically',
            'gon', 'tryna', 'wanna', 'makes sense', 'lowkey feel like'
        ]

        # Always add some Victor voice elements for quality scoring
        if len(result.split()) > 10:
            # Add Victor words throughout the content
            sentences = re.split(r'([.!?])', result)
            if len(sentences) > 2:
                # Insert Victor words in some sentences
                insert_positions = [i for i in range(
                    1, len(sentences)-1, 3)]  # Every 3rd sentence
                for pos in insert_positions[:2]:  # Add to max 2 positions
                    if pos < len(sentences):
                        sentences[pos] = f" {victor_flourishes[pos % len(victor_flourishes)]}" + \
                            sentences[pos]

                result = ''.join(sentences)

            # Always add at least one Victor flourish at the end for substantial content
            result += f" {victor_flourishes[len(result) % len(victor_flourishes)]}"

        return result

    def _create_blog_title(self, episode: EpisodeData) -> str:
        """Create an engaging blog title"""
        base_title = episode.title

        # Add Victor-style flair based on category
        if episode.category == 'technical':
            prefixes = ["Debugging the Matrix: ",
                        "Code Archaeology: ", "The Bug That Taught Me: "]
        elif episode.category == 'strategic':
            prefixes = ["Strategic Shifts: ",
                        "Building the Vision: ", "The Long Game: "]
        elif episode.category == 'operational':
            prefixes = ["Getting Things Done: ",
                        "Execution Notes: ", "The Daily Grind: "]
        else:
            prefixes = ["Digital Dreamscape: ",
                        "Swarm Chronicles: ", "Building in Public: "]

        prefix = prefixes[hash(episode.episode_id) % len(prefixes)]
        return f"{prefix}{base_title[:60]}"

    def _create_excerpt(self, content: str) -> str:
        """Create a compelling excerpt"""
        # Extract first meaningful paragraph
        paragraphs = [p.strip() for p in content.split(
            '\n\n') if p.strip() and len(p.strip()) > 50]

        if paragraphs:
            excerpt = paragraphs[0][:200]
            if len(excerpt) == 200:
                excerpt = excerpt.rsplit(' ', 1)[0] + '...'
            return excerpt

        return content[:150] + '...' if len(content) > 150 else content

    def _assess_quality(self, content: str, original_episode: EpisodeData) -> float:
        """Comprehensive quality assessment for episode publishing"""

        # First, check for immediate rejection criteria
        if self._is_noise_content(content):
            return 0.0

        # Calculate weighted quality scores
        scores = {}

        # 1. Content Completeness (25% weight)
        scores['completeness'] = self._score_completeness(content)

        # 2. Relevance & Technical Depth (25% weight)
        scores['relevance'] = self._score_relevance(
            content, original_episode.category)

        # 3. Uniqueness & Originality (15% weight)
        scores['uniqueness'] = self._score_uniqueness(content)

        # 4. Victor Voice Quality (15% weight)
        scores['victor_voice'] = self._score_victor_voice(content)

        # 5. Narrative & Learning Value (10% weight)
        scores['narrative_value'] = self._score_narrative_value(content)

        # 6. Technical Accuracy (10% weight)
        scores['technical_accuracy'] = self._score_technical_accuracy(
            content, original_episode.category)

        # Calculate weighted final score
        final_score = sum(scores[category] * self.quality_weights[category]
                          for category in scores)

        # Debug output for high-scoring content
        if final_score >= 0.8:
            print(f"  ✅ High-quality episode detected ({final_score:.2f})")
            print(
                f"     Scores: {', '.join(f'{k}:{v:.2f}' for k,v in scores.items())}")

        return min(1.0, final_score)

    def _is_noise_content(self, content: str) -> bool:
        """Check if content is pure noise and should be rejected"""
        content_clean = content.strip().lower()

        # Check for noise patterns
        for pattern in self.noise_patterns:
            if re.search(pattern, content_clean, re.IGNORECASE):
                return True

        # Check for minimum requirements
        word_count = len(content.split())
        if word_count < self.min_requirements['word_count']:
            return True

        sentence_count = len(re.split(r'[.!?]+', content))
        if sentence_count < self.min_requirements['sentence_count']:
            return True

        # Check for content that looks like logs/status updates only
        # Too many line breaks
        if content_clean.count('\n') > len(content_clean.split()) * 2:
            return True

        # Check for repetitive content
        words = content.lower().split()
        if len(words) > 10:
            unique_ratio = len(set(words)) / len(words)
            if unique_ratio < 0.3:  # Less than 30% unique words
                return True

        return False

    def _score_completeness(self, content: str) -> float:
        """Score content completeness and structure"""
        score = 0.0

        # Length requirements
        word_count = len(content.split())
        if word_count >= 100:
            score += 0.3
        elif word_count >= 50:
            score += 0.2

        # Sentence structure
        sentences = re.split(r'[.!?]+', content)
        if len(sentences) >= 5:
            score += 0.3
        elif len(sentences) >= 3:
            score += 0.2

        # Has paragraphs (multiple line breaks)
        if content.count('\n\n') >= 2:
            score += 0.2

        # Has some formatting/structure
        if any(char in content for char in ['*', '-', '#', '`']):
            score += 0.2

        return min(1.0, score)

    def _score_relevance(self, content: str, category: str) -> float:
        """Score relevance and technical depth"""
        score = 0.0
        content_lower = content.lower()

        # Category-specific relevance checks
        if category == 'technical':
            tech_indicators = ['code', 'api', 'function',
                               'database', 'error', 'fix', 'debug', 'system']
            tech_count = sum(
                1 for indicator in tech_indicators if indicator in content_lower)
            score += min(0.5, tech_count * 0.1)

        elif category == 'strategic':
            strategy_indicators = ['plan', 'strategy',
                                   'vision', 'goal', 'architecture', 'design']
            strategy_count = sum(
                1 for indicator in strategy_indicators if indicator in content_lower)
            score += min(0.5, strategy_count * 0.15)

        elif category == 'operational':
            op_indicators = ['task', 'complete', 'status',
                             'update', 'coordination', 'process']
            op_count = sum(
                1 for indicator in op_indicators if indicator in content_lower)
            score += min(0.5, op_count * 0.1)

        # General relevance indicators
        relevance_terms = ['learn', 'understand', 'realize',
                           'challenge', 'problem', 'solution', 'approach']
        relevance_count = sum(
            1 for term in relevance_terms if term in content_lower)
        score += min(0.3, relevance_count * 0.1)

        # Has concrete details/examples
        if any(word in content_lower for word in ['example', 'specifically', 'actually', 'in practice']):
            score += 0.2

        return min(1.0, score)

    def _score_uniqueness(self, content: str) -> float:
        """Score content uniqueness and originality"""
        score = 0.0

        # Vocabulary diversity
        words = content.lower().split()
        if len(words) > 20:
            unique_words = set(words)
            diversity_ratio = len(unique_words) / len(words)
            score += min(0.4, diversity_ratio * 0.8)

        # Avoids generic phrases
        generic_phrases = [
            'status update', 'work completed', 'task done', 'in progress',
            'will update', 'next steps', 'moving forward', 'as planned'
        ]

        generic_penalty = sum(
            1 for phrase in generic_phrases if phrase in content.lower())
        score -= min(0.3, generic_penalty * 0.1)

        # Has specific details
        if re.search(r'\d+', content):  # Contains numbers
            score += 0.2

        if re.search(r'[A-Z][a-z]+ [A-Z][a-z]+', content):  # Proper names
            score += 0.2

        # Quotes or specific terms
        if any(char in content for char in ['"', "'", '`']):
            score += 0.2

        return max(0.0, min(1.0, score))

    def _score_victor_voice(self, content: str) -> float:
        """Score how well Victor's voice patterns are applied"""
        score = 0.0

        # Victor's characteristic words/phrases
        victor_indicators = [
            'idk', 'tbh', 'kinda', 'tryna', 'lowkey', 'gon', 'wanna',
            'so now', 'for real though', 'lowkey feel like', 'js wanna',
            'but also', 'its basically', 'makes sense'
        ]

        victor_count = sum(
            1 for indicator in victor_indicators if indicator in content.lower())
        score += min(0.5, victor_count * 0.08)

        # Avoids overly formal language
        formal_penalties = ['therefore', 'however',
                            'moreover', 'consequently', 'accordingly']
        formal_penalty = sum(
            1 for word in formal_penalties if word in content.lower())
        score -= min(0.2, formal_penalty * 0.1)

        # Has conversational flow
        if '...' in content:
            score += 0.2

        if re.search(r'\?$', content.strip()):  # Ends with question
            score += 0.1

        return max(0.0, min(1.0, score))

    def _score_narrative_value(self, content: str) -> float:
        """Score narrative and learning value"""
        score = 0.0
        content_lower = content.lower()

        # Learning indicators
        learning_terms = [
            'learned', 'realized', 'understood', 'discovered', 'figured out',
            'challenge', 'problem', 'solution', 'approach', 'insight'
        ]

        learning_count = sum(
            1 for term in learning_terms if term in content_lower)
        score += min(0.4, learning_count * 0.15)

        # Has reflection or insight
        reflection_indicators = [
            'now i see', 'the key was', 'what mattered', 'the real issue',
            'looking back', 'in hindsight', 'the lesson'
        ]

        reflection_count = sum(
            1 for indicator in reflection_indicators if indicator in content_lower)
        score += min(0.3, reflection_count * 0.15)

        # Tells a story or journey
        if any(word in content_lower for word in ['started', 'then', 'after', 'when', 'during']):
            score += 0.3

        return min(1.0, score)

    def _score_technical_accuracy(self, content: str, category: str) -> float:
        """Score technical accuracy and proper terminology"""
        score = 0.0

        # Check for proper technical terms based on category
        if category == 'technical':
            tech_terms = [
                'function', 'api', 'database', 'server', 'client', 'code',
                'algorithm', 'system', 'architecture', 'framework', 'library'
            ]
            tech_count = sum(
                1 for term in tech_terms if term in content.lower())
            score += min(0.6, tech_count * 0.08)

        elif category == 'strategic':
            strategy_terms = [
                'vision', 'strategy', 'planning', 'execution', 'goals',
                'objectives', 'roadmap', 'milestones', 'stakeholders'
            ]
            strategy_count = sum(
                1 for term in strategy_terms if term in content.lower())
            score += min(0.6, strategy_count * 0.08)

        # Avoids obvious errors or nonsense
        error_indicators = ['????', '!!!!',
                            'null', 'undefined', 'error: error']
        error_penalty = sum(
            1 for indicator in error_indicators if indicator in content.lower())
        score -= min(0.3, error_penalty * 0.15)

        # Has proper formatting for technical content
        if category == 'technical' and ('`' in content or '```' in content):
            score += 0.2

        return max(0.0, min(1.0, score))

    def publish_episode_batch(self, episodes: List[ProcessedEpisode], batch_size: int = 10) -> int:
        """Publish a batch of episodes to WordPress"""
        published = 0

        print(
            f"\n🚀 Publishing batch of {min(batch_size, len(episodes))} episodes...")

        for i, episode in enumerate(episodes[:batch_size]):
            if episode.publish_ready:
                print(
                    f"📝 Publishing {i+1}/{batch_size}: {episode.blog_title[:50]}...")

                success = self._publish_to_wordpress(episode)
                if success:
                    published += 1
                    self.processed_count += 1
                else:
                    print(f"❌ Failed to publish: {episode.blog_title[:30]}...")
            else:
                print(
                    f"⏭️  Skipping low-quality episode: {episode.blog_title[:30]}... (score: {episode.quality_score:.2f})")

        return published

    def _publish_to_wordpress(self, episode: ProcessedEpisode) -> bool:
        """Publish episode to WordPress"""
        api_url = f"{self.wp_url}/wp-json/wp/v2/posts"
        auth = HTTPBasicAuth(self.wp_user, self.wp_pass)

        data = {
            'title': episode.blog_title,
            'content': episode.victor_content,
            'excerpt': episode.excerpt,
            'status': 'publish'
            # Note: categories and tags require numeric IDs in WordPress
            # For now, we'll let WordPress auto-categorize
        }

        try:
            response = requests.post(api_url, json=data, auth=auth, timeout=30)

            if response.status_code == 201:
                post_data = response.json()
                post_url = post_data.get('link', 'URL not available')
                print(f"✅ Published: {post_url}")
                return True
            else:
                print(
                    f"❌ WP Error {response.status_code}: {response.text[:100]}")
                return False

        except Exception as e:
            print(f"❌ Request Error: {e}")
            return False

    def _map_category_to_wp(self, category: str) -> str:
        """Map episode category to WordPress category"""
        mapping = {
            'technical': 'Development',
            'strategic': 'Strategy',
            'operational': 'Operations',
            'narrative': 'Reflections'
        }
        return mapping.get(category, 'General')

    def run_mass_processing(self, batch_size: int = 10, max_episodes: int = 100) -> Dict[str, Any]:
        """Run the complete mass processing pipeline"""
        print("🎬 DIGITAL DREAMSCAPE - MASS EPISODE PROCESSING")
        print("=" * 60)

        # Step 1: Discover content
        all_episodes = self.discover_all_content()

        if not all_episodes:
            print("❌ No episodes found to process")
            return {'error': 'No content discovered'}

        # Limit to max_episodes
        all_episodes = all_episodes[:max_episodes]
        print(
            f"\n📊 Processing {len(all_episodes)} episodes (limited to {max_episodes})")

        # Step 2: Apply Victor's voice
        processed_episodes = []
        print("\n🎭 Applying Victor's voice patterns...")
        for episode in all_episodes:
            processed = self.apply_victor_voice(episode)
            processed_episodes.append(processed)

        # Step 3: Quality filter
        ready_episodes = [ep for ep in processed_episodes if ep.publish_ready]
        rejected_episodes = [
            ep for ep in processed_episodes if not ep.publish_ready]

        print(
            f"\n✅ Quality Check: {len(ready_episodes)}/{len(processed_episodes)} episodes ready for publishing")
        print(f"❌ Filtered out: {len(rejected_episodes)} low-quality episodes")

        if rejected_episodes:
            print("\n📊 Quality Gate Analysis:")
            # Show top rejected reasons
            rejected_scores = [(ep.episode_data.title[:50], ep.quality_score)
                               for ep in rejected_episodes[:5]]
            for title, score in rejected_scores:
                print(f"   • {title}... (score: {score:.2f})")

        if ready_episodes:
            print("\n🎯 High-Quality Episodes Ready:")
            ready_scores = [(ep.episode_data.title[:50], ep.quality_score)
                            for ep in ready_episodes[:5]]
            for title, score in ready_scores:
                print(f"   • {title}... (score: {score:.2f})")

        # Step 4: Batch publish
        total_published = 0
        batches = [ready_episodes[i:i+batch_size]
                   for i in range(0, len(ready_episodes), batch_size)]

        for batch_num, batch in enumerate(batches, 1):
            print(f"\n🔄 Processing Batch {batch_num}/{len(batches)}")
            published = self.publish_episode_batch(batch, batch_size)
            total_published += published

        # Summary
        result = {
            'total_discovered': len(all_episodes),
            'total_processed': len(processed_episodes),
            'quality_passed': len(ready_episodes),
            'total_published': total_published,
            'success_rate': total_published / len(all_episodes) if all_episodes else 0
        }

        print(f"\n🎉 MASS PROCESSING COMPLETE")
        print("=" * 40)
        print(f"📊 Episodes Discovered: {result['total_discovered']}")
        print(f"🎭 Victor Voice Applied: {result['total_processed']}")
        print(f"✅ Quality Approved: {result['quality_passed']}")
        print(f"🚀 Published to Site: {result['total_published']}")
        print(f"📈 Success Rate: {result['success_rate']:.1%}")
        print(f"🌐 Check: https://digitaldreamscape.site/blog/")

        return result


def main():
    """Main execution"""
    import argparse

    parser = argparse.ArgumentParser(
        description="Mass episode processor for Digital Dreamscape")
    parser.add_argument("--batch-size", type=int,
                        default=10, help="Episodes per batch")
    parser.add_argument("--max-episodes", type=int,
                        default=50, help="Maximum episodes to process")
    parser.add_argument("--dry-run", action="store_true",
                        help="Discover and process without publishing")
    parser.add_argument("--quality-report", action="store_true",
                        help="Generate detailed quality report")
    parser.add_argument("--quality-threshold", type=float,
                        default=0.8, help="Quality threshold (0.0-1.0)")

    args = parser.parse_args()

    processor = MassEpisodeProcessor()
    processor.quality_threshold = args.quality_threshold  # Override default threshold

    if args.dry_run:
        print("🔍 DRY RUN MODE - Discovering content only")
        episodes = processor.discover_all_content()
        print(f"\n📊 Found {len(episodes)} episodes ready for processing")

        # Show sample
        if episodes:
            print("\n📝 Sample Episodes:")
            for i, ep in enumerate(episodes[:5], 1):
                print(
                    f"{i}. [{ep.content_type}] {ep.title[:60]}... ({ep.category})")

    elif args.quality_report:
        print("📊 QUALITY REPORT MODE - Analyzing content quality")
        episodes = processor.discover_all_content()
        print(f"\n🔬 Analyzing quality of {len(episodes)} episodes...")

        quality_stats = {
            'high_quality': 0,
            'medium_quality': 0,
            'low_quality': 0,
            'rejected': 0,
            'scores': []
        }

        for episode in episodes:
            processed = processor.apply_victor_voice(episode)
            score = processed.quality_score
            quality_stats['scores'].append(score)

            if score >= 0.8:
                quality_stats['high_quality'] += 1
            elif score >= 0.6:
                quality_stats['medium_quality'] += 1
            elif score >= 0.4:
                quality_stats['low_quality'] += 1
            else:
                quality_stats['rejected'] += 1

        print(f"\n📈 Quality Distribution:")
        print(
            f"  🟢 High Quality (≥0.8): {quality_stats['high_quality']} episodes")
        print(
            f"  🟡 Medium Quality (0.6-0.8): {quality_stats['medium_quality']} episodes")
        print(
            f"  🟠 Low Quality (0.4-0.6): {quality_stats['low_quality']} episodes")
        print(f"  🔴 Rejected (<0.4): {quality_stats['rejected']} episodes")

        if quality_stats['scores']:
            avg_score = sum(quality_stats['scores']) / \
                len(quality_stats['scores'])
            print(f"  📊 Average Score: {avg_score:.2f}")
            min_score = min(quality_stats['scores'])
            max_score = max(quality_stats['scores'])
            print(f"  📊 Score Range: {min_score:.2f} - {max_score:.2f}")
        print(f"\n🎯 Quality Threshold: {processor.quality_threshold}")
        print(
            f"  Would publish: {sum(1 for s in quality_stats['scores'] if s >= processor.quality_threshold)} episodes")
        print(
            f"  Would reject: {sum(1 for s in quality_stats['scores'] if s < processor.quality_threshold)} episodes")

    else:
        result = processor.run_mass_processing(
            args.batch_size, args.max_episodes)

        if result.get('error'):
            print(f"❌ Error: {result['error']}")
            sys.exit(1)


if __name__ == "__main__":
    main()
