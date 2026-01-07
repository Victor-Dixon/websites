"""
Content Processing Service
=========================

Responsible for parsing raw content into structured episodes and applying
Victor voice transformations.
"""

import re
import hashlib
from pathlib import Path
from typing import Dict, List, Any, Optional, Tuple
from dataclasses import dataclass
from datetime import datetime
from enum import Enum
import logging

# Consolidated quality assessment - replaces episode_quality_scorer
from consolidated_quality_assessment import ConsolidatedQualityAssessmentService, QualityMetrics, ContentCategory

logger = logging.getLogger(__name__)

@dataclass
class EpisodeData:
    """Structured episode data ready for processing"""
    source_file: str
    content_type: str  # 'devlog', 'conversation', 'coordination', 'discord'
    agent_id: Optional[str]
    timestamp: str
    title: str
    raw_content: str
    category: ContentCategory
    tags: List[str]
    episode_id: str
    metadata: Dict[str, Any] = None

    def __post_init__(self):
        if self.metadata is None:
            self.metadata = {}

@dataclass
class ProcessedEpisode:
    """Fully processed episode with Victor's voice"""
    episode_data: EpisodeData
    victor_content: str
    blog_title: str
    excerpt: str
    publish_ready: bool
    quality_score: float
    quality_metrics: QualityMetrics
    processing_metadata: Dict[str, Any] = None

    def __post_init__(self):
        if self.processing_metadata is None:
            self.processing_metadata = {}

class VictorVoiceProcessor:
    """Handles Victor voice transformations with improved patterns"""

    def __init__(self):
        self.victor_patterns = {
            # Signature phrases (high authenticity)
            'signature_phrases': {
                'idk': 'I don\'t know',
                'tbh': 'to be honest',
                'kinda': 'kind of',
                'tryna': 'trying to',
                'lowkey': 'kind of secretly',
                'gon': 'going to',
                'wanna': 'want to',
                'js': 'just',
                'so now': 'so now',
                'for real though': 'for real though',
                'lowkey feel like': 'I kind of feel like',
                'but also': 'but also',
                'its basically': 'it\'s basically',
                'makes sense': 'makes sense'
            },

            # Conversational transformations
            'conversational': {
                'I think': 'idk',
                'I believe': 'lowkey feel like',
                'However': 'but also',
                'Therefore': 'so now',
                'Actually': 'tbh',
                'Really': 'for real though',
                'Just': 'js',
                'Want to': 'wanna',
                'Trying to': 'tryna',
                'Going to': 'gon',
                'Kind of': 'kinda',
                'Sort of': 'kinda',
                'Because': 'cs',
                'Very': 'super',
                'Really': 'super',
                'Extremely': 'super'
            },

            # Emphasis and personality
            'emphasis': {
                'amazing': 'crazy amazing',
                'incredible': 'wild',
                'brilliant': 'genius level',
                'challenging': 'kinda tough',
                'difficult': 'super tricky',
                'complex': 'kinda complex',
                'simple': 'actually pretty simple',
                'easy': 'actually pretty easy'
            }
        }

        # Patterns to avoid over-transformation
        self.formal_preserve = [
            'API', 'URL', 'HTTP', 'JSON', 'XML', 'SQL', 'Git', 'Python', 'JavaScript'
        ]

    def apply_victor_voice(self, content: str, category: ContentCategory, intensity: float = 0.7) -> str:
        """
        Apply Victor's voice patterns to content.

        Args:
            content: Raw content to transform
            category: Content category for style adjustment
            intensity: How strongly to apply transformations (0.0-1.0)

        Returns:
            Content with Victor's voice applied
        """
        result = content

        # Clean up content first
        result = self._clean_content(result)

        # Apply transformations based on intensity
        if intensity > 0.3:
            result = self._apply_conversational_transforms(result, intensity)
        if intensity > 0.5:
            result = self._apply_emphasis_transforms(result, category, intensity)
        if intensity > 0.7:
            result = self._add_victor_flourishes(result, category)

        # Fix punctuation and formatting
        result = self._fix_punctuation(result)

        return result

    def _clean_content(self, content: str) -> str:
        """Clean and prepare content for transformation"""
        # Remove markdown headers for processing
        content = re.sub(r'^#+\s*', '', content, flags=re.MULTILINE)

        # Clean up excessive whitespace
        content = re.sub(r'\n{3,}', '\n\n', content)

        # Normalize ellipses
        content = re.sub(r'\.{3,}', '...', content)

        return content.strip()

    def _apply_conversational_transforms(self, content: str, intensity: float) -> str:
        """Apply conversational transformations"""
        result = content

        # Apply signature phrases first (highest priority)
        for victor_phrase, full_meaning in self.victor_patterns['signature_phrases'].items():
            if victor_phrase in result.lower():
                # Only replace if it makes sense in context
                pattern = r'\b' + re.escape(victor_phrase) + r'\b'
                result = re.sub(pattern, victor_phrase, result, flags=re.IGNORECASE)

        # Apply conversational transforms
        for formal, victor in self.victor_patterns['conversational'].items():
            if intensity > 0.5 or formal in ['I think', 'I believe', 'Just']:  # Always apply common ones
                # Word boundary aware replacement
                pattern = r'\b' + re.escape(formal) + r'\b'
                result = re.sub(pattern, victor, result, flags=re.IGNORECASE)

        return result

    def _apply_emphasis_transforms(self, content: str, category: ContentCategory, intensity: float) -> str:
        """Apply emphasis and personality transforms"""
        result = content

        # Category-specific emphasis
        if category == ContentCategory.TECHNICAL:
            tech_emphasis = {
                'works': 'actually works',
                'fixed': 'finally fixed',
                'solved': 'figured out',
                'problem': 'weird issue'
            }
            for original, emphasis in tech_emphasis.items():
                if original in result.lower():
                    pattern = r'\b' + re.escape(original) + r'\b'
                    result = re.sub(pattern, emphasis, result, flags=re.IGNORECASE)

        elif category == ContentCategory.NARRATIVE:
            narrative_emphasis = {
                'learned': 'finally learned',
                'realized': 'suddenly realized',
                'experienced': 'went through'
            }
            for original, emphasis in narrative_emphasis.items():
                if original in result.lower():
                    pattern = r'\b' + re.escape(original) + r'\b'
                    result = re.sub(pattern, emphasis, result, flags=re.IGNORECASE)

        # General emphasis patterns
        for original, emphasis in self.victor_patterns['emphasis'].items():
            if original in result.lower() and intensity > 0.7:
                pattern = r'\b' + re.escape(original) + r'\b'
                result = re.sub(pattern, emphasis, result, flags=re.IGNORECASE)

        return result

    def _add_victor_flourishes(self, content: str, category: ContentCategory) -> str:
        """Add Victor-style flourishes and personality"""
        result = content

        # Add thoughtful pauses for reflection-heavy content
        if category in [ContentCategory.NARRATIVE, ContentCategory.REFLECTION]:
            if 'learned' in result.lower() or 'realized' in result.lower():
                # Add ellipses for thoughtful reflection
                if '...' not in result[-50:]:  # Not already at the end
                    result += '...'

        # Add engagement for technical content
        elif category == ContentCategory.TECHNICAL:
            if len(result.split()) > 20 and not result.endswith(('!', '?', '.')):
                # Add a Victor-style closer
                closers = ['makes sense', 'tbh', 'kinda cool']
                result += f" {closers[hash(result) % len(closers)]}"

        return result

    def _fix_punctuation(self, content: str) -> str:
        """Fix punctuation and formatting issues"""
        # Fix spacing around punctuation
        content = re.sub(r'\s+([.!?,])', r'\1', content)
        content = re.sub(r'([.!?])\s*([a-zA-Z])', r'\1 \2', content)

        # Ensure proper spacing after punctuation
        content = re.sub(r'([.!?])([A-Z])', r'\1 \2', content)

        # Fix multiple spaces
        content = re.sub(r'\s{2,}', ' ', content)

        # Ensure content ends with proper punctuation
        if content and not content.endswith(('.', '!', '?', '...')):
            content += '.'

        return content

class ContentProcessingService:
    """Service for processing raw content into structured episodes"""

    def __init__(self, quality_scorer: Optional[ConsolidatedQualityAssessmentService] = None):
        self.quality_scorer = quality_scorer or ConsolidatedQualityAssessmentService()
        self.victor_processor = VictorVoiceProcessor()
        self.quality_threshold = 0.4  # Even more reasonable threshold while we calibrate

    def process_raw_content(self, raw_content_data: Dict[str, Any]) -> Optional[EpisodeData]:
        """
        Process raw content data into structured EpisodeData.

        Args:
            raw_content_data: Dict containing content metadata from discovery service

        Returns:
            EpisodeData if successfully parsed, None otherwise
        """
        try:
            content_type = raw_content_data.get('content_type', 'devlog')
            file_path = raw_content_data.get('file_path', '')
            agent_id = raw_content_data.get('agent_id')
            raw_content = raw_content_data.get('raw_content', '')

            if not raw_content.strip():
                logger.warning(f"Empty content in {file_path}")
                return None

            # Parse based on content type
            if content_type in ['devlog', 'agent_workspace']:
                return self._parse_devlog_content(raw_content_data)
            elif content_type == 'coordination':
                return self._parse_message_content(raw_content_data)
            else:
                logger.warning(f"Unknown content type: {content_type}")
                return None

        except Exception as e:
            logger.error(f"Error processing content from {raw_content_data.get('file_path', 'unknown')}: {e}")
            return None

    def _parse_devlog_content(self, content_data: Dict[str, Any]) -> Optional[EpisodeData]:
        """Parse devlog markdown content"""
        file_path = content_data['file_path']
        agent_id = content_data.get('agent_id')
        raw_content = content_data['raw_content']

        # Extract metadata from filename
        filename = Path(file_path).stem
        timestamp = self._extract_timestamp_from_filename(filename)
        category = self._categorize_content(raw_content, filename)

        # Extract title
        title = self._extract_title(raw_content)

        # Generate unique ID
        content_hash = hashlib.md5(raw_content.encode()).hexdigest()[:8]
        episode_id = f"{category.value}_{agent_id or 'unknown'}_{content_hash}"

        # Extract tags
        tags = self._extract_tags(raw_content, category)

        return EpisodeData(
            source_file=file_path,
            content_type='devlog',
            agent_id=agent_id,
            timestamp=timestamp,
            title=title,
            raw_content=raw_content,
            category=category,
            tags=tags,
            episode_id=episode_id
        )

    def _parse_message_content(self, content_data: Dict[str, Any]) -> Optional[EpisodeData]:
        """Parse message/coordination content"""
        raw_content = content_data.get('raw_content', '')
        timestamp = content_data.get('timestamp', '')

        if not raw_content.strip():
            return None

        # Extract title from first meaningful line
        lines = raw_content.split('\n', 3)
        title = "Message Coordination"
        for line in lines:
            line = line.strip()
            if len(line) > 20 and not line.startswith('['):
                title = line[:80]
                break

        # Generate ID
        content_hash = hashlib.md5(raw_content.encode()).hexdigest()[:8]
        episode_id = f"message_{content_hash}"

        return EpisodeData(
            source_file=content_data.get('file_path', 'message_queue'),
            content_type="coordination",
            agent_id=None,
            timestamp=timestamp[:10] if timestamp else datetime.now().strftime('%Y-%m-%d'),
            title=title,
            raw_content=raw_content,
            category=ContentCategory.OPERATIONAL,
            tags=["coordination", "agent-communication"],
            episode_id=episode_id
        )

    def apply_victor_transformation(self, episode: EpisodeData) -> ProcessedEpisode:
        """
        Apply Victor's voice transformation and quality assessment.

        Args:
            episode: Structured episode data

        Returns:
            ProcessedEpisode with Victor voice and quality metrics
        """
        logger.debug(f"🎭 Applying Victor's voice to: {episode.title[:50]}...")

        # Apply Victor voice transformation
        intensity = self._calculate_voice_intensity(episode.category)
        victor_content = self.victor_processor.apply_victor_voice(
            episode.raw_content,
            episode.category,
            intensity
        )

        # Generate blog title and excerpt
        blog_title = self._generate_blog_title(episode)
        excerpt = self._generate_excerpt(victor_content)

        # Quality assessment
        quality_metrics = self.quality_scorer.score_episode(victor_content, episode.category)

        return ProcessedEpisode(
            episode_data=episode,
            victor_content=victor_content,
            blog_title=blog_title,
            excerpt=excerpt,
            publish_ready=quality_metrics.overall_score >= self.quality_threshold,
            quality_score=quality_metrics.overall_score,
            quality_metrics=quality_metrics
        )

    def _calculate_voice_intensity(self, category: ContentCategory) -> float:
        """Calculate how strongly to apply Victor voice based on category"""
        intensity_map = {
            ContentCategory.TECHNICAL: 0.6,  # Technical content needs clarity
            ContentCategory.STRATEGIC: 0.8,  # Strategic content benefits from personality
            ContentCategory.OPERATIONAL: 0.7, # Operational content is straightforward
            ContentCategory.NARRATIVE: 0.9,  # Narrative content should be engaging
            ContentCategory.LEARNING: 0.8,   # Learning content needs personality
            ContentCategory.REFLECTION: 0.9  # Reflection content should be authentic
        }
        return intensity_map.get(category, 0.7)

    def _extract_timestamp_from_filename(self, filename: str) -> str:
        """Extract timestamp from filename patterns"""
        # Try various date patterns
        patterns = [
            r'(\d{4})-(\d{2})-(\d{2})',  # YYYY-MM-DD
            r'(\d{4})(\d{2})(\d{2})',    # YYYYMMDD
            r'(\d{2})(\d{2})(\d{4})',    # MMDDYYYY
        ]

        for pattern in patterns:
            match = re.search(pattern, filename)
            if match:
                if len(match.groups()) == 3:
                    y, m, d = match.groups()
                    # Handle different formats
                    if len(y) == 4:  # YYYY-MM-DD or YYYYMMDD
                        return f"{y}-{m.zfill(2)}-{d.zfill(2)}"
                    elif len(d) == 4:  # MMDDYYYY
                        return f"{d}-{m.zfill(2)}-{y.zfill(2)}"

        return datetime.now().strftime('%Y-%m-%d')

    def _categorize_content(self, content: str, filename: str) -> ContentCategory:
        """Categorize content based on analysis"""
        return self.quality_scorer._detect_category(content)

    def _extract_title(self, content: str) -> str:
        """Extract title from content"""
        lines = content.split('\n', 5)

        for line in lines[:3]:
            line = line.strip()
            if line and not line.startswith('#') and len(line) > 10:
                return line[:100]

        return "Untitled Devlog"

    def _extract_tags(self, content: str, category: ContentCategory) -> List[str]:
        """Extract relevant tags from content"""
        tags = [category.value]

        content_lower = content.lower()

        # Add specific tags based on content analysis
        tag_mappings = {
            'ai': 'ai-integration',
            'ollama': 'ai-integration',
            'discord': 'discord',
            'agent': 'multi-agent',
            'episode': 'digital-dreamscape',
            'dreamscape': 'digital-dreamscape',
            'coordination': 'coordination',
            'api': 'technical',
            'code': 'technical',
            'debug': 'technical',
            'database': 'technical',
            'planning': 'strategic',
            'strategy': 'strategic',
            'vision': 'strategic'
        }

        for keyword, tag in tag_mappings.items():
            if keyword in content_lower:
                tags.append(tag)

        return list(set(tags))  # Remove duplicates

    def _generate_blog_title(self, episode: EpisodeData) -> str:
        """Generate an engaging blog title"""
        base_title = episode.title

        # Category-specific title prefixes
        prefixes = {
            ContentCategory.TECHNICAL: ["Debugging the Matrix: ", "Code Archaeology: ", "The Bug That Taught Me: "],
            ContentCategory.STRATEGIC: ["Strategic Shifts: ", "Building the Vision: ", "The Long Game: "],
            ContentCategory.OPERATIONAL: ["Getting Things Done: ", "Execution Notes: ", "The Daily Grind: "],
            ContentCategory.NARRATIVE: ["Digital Dreamscape: ", "Swarm Chronicles: ", "Building in Public: "],
            ContentCategory.LEARNING: ["Learning Journey: ", "Knowledge Gained: ", "The Aha Moment: "],
            ContentCategory.REFLECTION: ["Reflecting On: ", "Looking Back: ", "Lessons Learned: "]
        }

        category_prefixes = prefixes.get(episode.category, prefixes[ContentCategory.NARRATIVE])
        prefix = category_prefixes[hash(episode.episode_id) % len(category_prefixes)]

        return f"{prefix}{base_title[:60]}"

    def _generate_excerpt(self, content: str) -> str:
        """Generate a compelling excerpt"""
        # Extract first meaningful paragraph
        paragraphs = [p.strip() for p in content.split('\n\n') if p.strip() and len(p.strip()) > 50]

        if paragraphs:
            excerpt = paragraphs[0][:200]
            if len(excerpt) == 200:
                excerpt = excerpt.rsplit(' ', 1)[0] + '...'
            return excerpt

        return content[:150] + '...' if len(content) > 150 else content