#!/usr/bin/env python3
"""
Consolidated Quality Assessment Service
======================================

UNIFIED QUALITY SYSTEM - Phase 3 Consolidation
Consolidates quality assessment logic from 15+ files into 1 core service.

CONSOLIDATED FROM:
- episode_quality_scorer.py (primary 12-criteria system)
- content_processing_service.py (quality integration)
- mass_episode_processor_v2.py (legacy quality logic)
- digital_dreamscape_pipeline.py (quality gates)
- victor_voice_processor.py (voice quality validation)
- seo_enhancement_processor.py (SEO quality scoring)
- episode_publishing_service.py (quality checks)
- Plus additional scattered quality logic

PROVIDES:
- Single source of truth for all quality assessment
- Unified API for all quality-related operations
- Backward compatibility with existing systems
- Enhanced metrics and better calibration
"""

from dataclasses import dataclass, field
from typing import Dict, List, Any, Optional, Tuple, Union
from enum import Enum
import re
import math
from datetime import datetime
import logging

logger = logging.getLogger(__name__)


class ContentCategory(Enum):
    """Enhanced content categories for Digital Dreamscape"""
    TECHNICAL = "technical"
    STRATEGIC = "strategic"
    OPERATIONAL = "operational"
    NARRATIVE = "narrative"
    LEARNING = "learning"
    REFLECTION = "reflection"


class QualityTier(Enum):
    """Quality tiers with clear thresholds"""
    PLATINUM = "PLATINUM"   # Episode-worthy (0.80+)
    GOLD = "GOLD"          # High quality (0.65-0.79)
    SILVER = "SILVER"      # Good quality (0.50-0.64)
    BRONZE = "BRONZE"      # Acceptable (0.40-0.49)
    REJECTED = "REJECTED"  # Needs work (<0.40)


@dataclass
class QualityMetrics:
    """Comprehensive quality metrics - consolidated from multiple sources"""

    # Core Content Quality (35%)
    content_density: float = 0.0          # Information per word ratio
    structural_integrity: float = 0.0     # Logical flow and organization
    factual_accuracy: float = 0.0         # Technical correctness

    # Narrative Quality (25%)
    storytelling_flow: float = 0.0        # Narrative coherence
    emotional_resonance: float = 0.0      # Reader engagement potential
    insight_density: float = 0.0          # Learning moments per content

    # Voice & Style (20%)
    victor_voice_authenticity: float = 0.0  # Victor's personality authenticity
    readability_score: float = 0.0        # Flesch reading ease for casual content
    conversational_flow: float = 0.0      # Natural dialogue patterns

    # Engagement Potential (10%)
    shareability_score: float = 0.0       # Viral potential
    timelessness: float = 0.0             # Long-term value

    # Technical Quality (10%)
    formatting_quality: float = 0.0       # Proper markdown/code formatting
    seo_score: float = 0.0               # SEO optimization quality

    # Metadata
    category: ContentCategory = ContentCategory.OPERATIONAL
    word_count: int = 0
    sentence_count: int = 0
    technical_term_count: int = 0
    personal_anecdote_count: int = 0
    victor_phrase_count: int = 0
    seo_keyword_count: int = 0

    @property
    def overall_score(self) -> float:
        """Calculate weighted overall score - calibrated for better accuracy"""
        weights = {
            # Core Content (35%)
            'content_density': 0.10,
            'structural_integrity': 0.10,
            'factual_accuracy': 0.15,

            # Narrative (25%)
            'storytelling_flow': 0.08,
            'emotional_resonance': 0.08,
            'insight_density': 0.09,

            # Voice & Style (20%)
            'victor_voice_authenticity': 0.12,
            'readability_score': 0.05,
            'conversational_flow': 0.03,

            # Engagement (10%)
            'shareability_score': 0.05,
            'timelessness': 0.05,

            # Technical (10%)
            'formatting_quality': 0.05,
            'seo_score': 0.05
        }

        score = sum(getattr(self, metric) * weight for metric, weight in weights.items())
        return min(1.0, max(0.0, score))

    @property
    def quality_tier(self) -> QualityTier:
        """Get quality tier based on calibrated score - aligned with original system"""
        score = self.overall_score
        if score >= 0.80: return QualityTier.PLATINUM
        elif score >= 0.65: return QualityTier.GOLD
        elif score >= 0.50: return QualityTier.SILVER
        elif score >= 0.40: return QualityTier.BRONZE
        else: return QualityTier.REJECTED

    @property
    def publish_ready(self) -> bool:
        """Determine if content is ready for publication"""
        return self.quality_tier != QualityTier.REJECTED

    @property
    def tier_description(self) -> str:
        """Get human-readable tier description"""
        tier_descriptions = {
            QualityTier.PLATINUM: "Episode-worthy - publish immediately",
            QualityTier.GOLD: "High quality - ready for publication",
            QualityTier.SILVER: "Good quality - publish with minor edits",
            QualityTier.BRONZE: "Acceptable - needs improvement before publishing",
            QualityTier.REJECTED: "Needs significant work - do not publish"
        }
        return tier_descriptions[self.quality_tier]


class ConsolidatedQualityAssessmentService:
    """
    UNIFIED QUALITY ASSESSMENT SERVICE
    =================================

    Consolidates all quality assessment logic from 15+ files into a single,
    comprehensive service with backward compatibility.
    """

    def __init__(self):
        """Initialize with all quality assessment databases"""
        self._setup_technical_databases()
        self._setup_voice_patterns()
        self._setup_emotional_indicators()
        self._setup_seo_databases()

    def _setup_technical_databases(self):
        """Technical term databases for different categories"""
        self.technical_terms = {
            ContentCategory.TECHNICAL: [
                'api', 'database', 'server', 'client', 'framework', 'library',
                'algorithm', 'function', 'method', 'class', 'object', 'variable',
                'debug', 'error', 'exception', 'log', 'trace', 'stack', 'memory',
                'performance', 'optimization', 'scalability', 'architecture',
                'async', 'await', 'promise', 'callback', 'event', 'stream'
            ],
            ContentCategory.STRATEGIC: [
                'vision', 'strategy', 'planning', 'roadmap', 'milestone',
                'stakeholder', 'objective', 'goal', 'execution', 'leadership',
                'decision', 'priority', 'resource', 'timeline', 'deliverable',
                'quarterly', 'annual', 'budget', 'forecast', 'projection'
            ],
            ContentCategory.OPERATIONAL: [
                'task', 'process', 'workflow', 'automation', 'integration',
                'deployment', 'monitoring', 'maintenance', 'coordination',
                'communication', 'status', 'update', 'progress', 'deadline',
                'sprint', 'backlog', 'ticket', 'merge', 'pull request'
            ]
        }

    def _setup_voice_patterns(self):
        """Victor voice patterns and authenticity markers"""
        self.victor_voice_patterns = {
            'signature_phrases': {
                'idk': 0.9, 'tbh': 0.9, 'kinda': 0.8, 'tryna': 0.8,
                'lowkey': 0.8, 'gon': 0.7, 'wanna': 0.8, 'js': 0.7,
                'so now': 0.8, 'for real though': 0.9, 'lowkey feel like': 0.9,
                'but also': 0.8, 'its basically': 0.7, 'makes sense': 0.8
            },
            'slang_conversions': {
                'I think': 'idk', 'I believe': 'lowkey feel like',
                'However': 'but also', 'Therefore': 'so now',
                'Actually': 'tbh', 'Really': 'for real though'
            },
            'conversational_markers': [
                '...', 'y\'know', 'like', 'kinda', 'sorta', 'probably',
                'i guess', 'maybe', 'kinda weird', 'makes sense though'
            ]
        }

    def _setup_emotional_indicators(self):
        """Emotional engagement indicators"""
        self.emotional_indicators = {
            'curiosity': ['wonder', 'curious', 'interesting', 'fascinating', 'weird'],
            'frustration': ['frustrating', 'annoying', 'problem', 'issue', 'challenge'],
            'satisfaction': ['finally', 'worked', 'success', 'accomplished', 'proud'],
            'reflection': ['learned', 'realized', 'now i see', 'looking back', 'hindsight']
        }

    def _setup_seo_databases(self):
        """SEO-related databases and patterns"""
        self.seo_keywords = {
            'primary': ['how to', 'best', 'guide', 'tutorial', 'tips'],
            'secondary': ['using', 'with', 'for', 'in', 'on', 'to'],
            'branded': ['trading robot plug', 'free ride investor', 'dadu dekc']
        }

    def assess_content_quality(self, content: str, category: Union[str, ContentCategory] = None) -> QualityMetrics:
        """
        COMPREHENSIVE QUALITY ASSESSMENT
        ================================

        Unified method that consolidates all quality assessment logic from:
        - episode_quality_scorer.py (12 criteria)
        - content_processing_service.py (integration)
        - victor_voice_processor.py (voice validation)
        - seo_enhancement_processor.py (SEO scoring)
        - digital_dreamscape_pipeline.py (quality gates)

        Args:
            content: The content to assess
            category: Content category (auto-detected if not provided)

        Returns:
            Comprehensive QualityMetrics object
        """
        if isinstance(category, str):
            category = ContentCategory(category.lower())
        elif category is None:
            category = self._detect_category(content)

        metrics = QualityMetrics(category=category)

        # Basic content analysis
        words = content.split()
        sentences = re.split(r'[.!?]+', content)
        metrics.word_count = len(words)
        metrics.sentence_count = len([s for s in sentences if s.strip()])

        # Consolidated quality assessments
        self._assess_content_density(content, metrics)
        self._assess_structural_integrity(content, metrics)
        self._assess_factual_accuracy(content, category, metrics)
        self._assess_storytelling_flow(content, metrics)
        self._assess_emotional_resonance(content, metrics)
        self._assess_insight_density(content, metrics)
        self._assess_victor_voice_authenticity(content, metrics)
        self._assess_readability(content, metrics)
        self._assess_conversational_flow(content, metrics)
        self._assess_shareability(content, metrics)
        self._assess_timelessness(content, metrics)
        self._assess_formatting_quality(content, metrics)
        self._assess_seo_quality(content, metrics)

        return metrics

    def _detect_category(self, content: str) -> ContentCategory:
        """Auto-detect content category based on keywords and patterns"""
        content_lower = content.lower()

        # Technical indicators
        technical_score = sum(1 for term in self.technical_terms[ContentCategory.TECHNICAL]
                            if term in content_lower)

        # Strategic indicators
        strategic_score = sum(1 for term in self.technical_terms[ContentCategory.STRATEGIC]
                            if term in content_lower)

        # Operational indicators
        operational_score = sum(1 for term in self.technical_terms[ContentCategory.OPERATIONAL]
                              if term in content_lower)

        # Category determination
        max_score = max(technical_score, strategic_score, operational_score)
        if max_score == 0:
            return ContentCategory.NARRATIVE  # Default for uncategorized

        if technical_score == max_score:
            return ContentCategory.TECHNICAL
        elif strategic_score == max_score:
            return ContentCategory.STRATEGIC
        else:
            return ContentCategory.OPERATIONAL

    def _assess_content_density(self, content: str, metrics: QualityMetrics):
        """Assess information density - consolidated from multiple sources"""
        words = content.split()
        if not words:
            metrics.content_density = 0.0
            return

        # Technical term density
        technical_terms = []
        for category_terms in self.technical_terms.values():
            technical_terms.extend(category_terms)

        term_count = sum(1 for word in words if word.lower() in technical_terms)
        metrics.technical_term_count = term_count
        term_density = term_count / len(words)

        # Insight density (questions, lessons, realizations)
        insight_indicators = ['lesson', 'learned', 'realized', 'insight', 'key takeaway', 'important']
        insight_count = sum(1 for indicator in insight_indicators if indicator in content.lower())

        # Combine factors
        base_density = min(1.0, term_density * 2.0)  # Technical terms
        insight_bonus = min(0.3, insight_count * 0.1)  # Insights
        length_penalty = max(0, 1.0 - (len(words) / 1000))  # Prefer substantial content

        metrics.content_density = min(1.0, base_density + insight_bonus + length_penalty)

    def _assess_structural_integrity(self, content: str, metrics: QualityMetrics):
        """Assess structural organization - consolidated logic"""
        # Headers and sections
        header_count = len(re.findall(r'^#{1,6}\s+', content, re.MULTILINE))
        section_score = min(1.0, header_count / 5.0)  # Expect 2-5 headers

        # List structures
        list_items = len(re.findall(r'^[-*+]\s+', content, re.MULTILINE))
        list_score = min(1.0, list_items / 10.0)  # Expect some lists

        # Code blocks
        code_blocks = len(re.findall(r'```', content))
        code_score = min(0.5, code_blocks / 4.0)  # Bonus for code examples

        # Transitions and flow
        transition_words = ['however', 'therefore', 'but', 'also', 'then', 'next', 'finally']
        transition_count = sum(1 for word in transition_words if word in content.lower())
        flow_score = min(0.5, transition_count / 5.0)

        metrics.structural_integrity = (section_score * 0.4 + list_score * 0.3 +
                                       code_score * 0.2 + flow_score * 0.1)

    def _assess_factual_accuracy(self, content: str, category: ContentCategory, metrics: QualityMetrics):
        """Assess factual correctness - enhanced for different categories"""
        if category == ContentCategory.TECHNICAL:
            # Technical content: check for concrete examples and explanations
            has_examples = bool(re.search(r'```\w*\n.*?\n```', content, re.DOTALL))
            has_explanations = len(re.findall(r'(because|so|therefore|this means)', content.lower())) > 2
            accuracy_score = 0.8 if (has_examples and has_explanations) else 0.5
        elif category == ContentCategory.STRATEGIC:
            # Strategic content: check for reasoning and outcomes
            has_reasoning = len(re.findall(r'(because|so that|to achieve)', content.lower())) > 1
            has_outcomes = bool(re.search(r'(result|outcome|impact|benefit)', content.lower()))
            accuracy_score = 0.7 if (has_reasoning and has_outcomes) else 0.4
        else:
            # Operational/Narrative: check for timeline and specifics
            has_timeline = bool(re.search(r'(then|next|after|before|when)', content.lower()))
            has_specifics = len(re.findall(r'\d+', content)) > 2  # Numbers indicate specifics
            accuracy_score = 0.6 if (has_timeline or has_specifics) else 0.3

        metrics.factual_accuracy = accuracy_score

    def _assess_storytelling_flow(self, content: str, metrics: QualityMetrics):
        """Assess narrative flow and coherence"""
        # Problem-solution structure
        has_problem = bool(re.search(r'(problem|issue|challenge|struggle)', content.lower()))
        has_solution = bool(re.search(r'(solution|fixed|solved|worked)', content.lower()))
        problem_solution_score = 0.5 if (has_problem and has_solution) else 0.0

        # Personal anecdote detection
        anecdote_indicators = ['i was', 'i had', 'i thought', 'i realized', 'i learned']
        anecdote_count = sum(1 for indicator in anecdote_indicators if indicator in content.lower())
        metrics.personal_anecdote_count = anecdote_count
        anecdote_score = min(0.5, anecdote_count / 3.0)

        # Temporal flow
        temporal_words = ['then', 'next', 'after', 'before', 'during', 'while', 'finally']
        temporal_count = sum(1 for word in temporal_words if word in content.lower())
        temporal_score = min(0.3, temporal_count / 5.0)

        metrics.storytelling_flow = problem_solution_score + anecdote_score + temporal_score

    def _assess_emotional_resonance(self, content: str, metrics: QualityMetrics):
        """Assess emotional engagement potential"""
        total_emotional_words = 0
        for emotion_type, indicators in self.emotional_indicators.items():
            count = sum(1 for indicator in indicators if indicator in content.lower())
            total_emotional_words += count

        # Intensity based on emotion type
        intensity_multipliers = {
            'curiosity': 1.2,  # High engagement
            'frustration': 1.0,  # Medium engagement
            'satisfaction': 0.8,  # Good engagement
            'reflection': 0.9   # Thoughtful engagement
        }

        weighted_emotions = 0
        for emotion_type, indicators in self.emotional_indicators.items():
            emotion_count = sum(1 for indicator in indicators if indicator in content.lower())
            weighted_emotions += emotion_count * intensity_multipliers[emotion_type]

        metrics.emotional_resonance = min(1.0, weighted_emotions / 8.0)  # Expect some emotional content

    def _assess_insight_density(self, content: str, metrics: QualityMetrics):
        """Assess density of learning moments and insights"""
        insight_indicators = [
            'lesson learned', 'key takeaway', 'important to remember', 'realized that',
            'now i understand', 'the key insight', 'what i learned', 'mistake i made',
            'would do differently', 'advice for others', 'pro tip', 'best practice'
        ]

        insight_count = sum(1 for indicator in insight_indicators if indicator in content.lower())
        insight_score = min(1.0, insight_count / 3.0)  # Expect 2-3 insights for high score

        # Bonus for specific, actionable insights
        actionable_indicators = ['always', 'never', 'instead', 'try', 'avoid', 'recommend']
        actionable_count = sum(1 for indicator in actionable_indicators if indicator in content.lower())
        actionable_bonus = min(0.3, actionable_count / 5.0)

        metrics.insight_density = insight_score + actionable_bonus

    def _assess_victor_voice_authenticity(self, content: str, metrics: QualityMetrics):
        """Assess Victor voice authenticity - integrated with VictorVoiceProcessor"""
        try:
            # Import here to avoid circular dependency
            from victor_voice_processor import VictorVoiceProcessor

            # Use the actual Victor voice processor for transformation
            processor = VictorVoiceProcessor()
            result = processor.apply_victor_voice(content)

            # Use the processor's confidence score
            metrics.victor_voice_authenticity = result.voice_confidence_score
            metrics.victor_phrase_count = result.proof_elements_found

        except ImportError:
            # Fallback to basic pattern matching if processor not available
            content_lower = content.lower()
            total_score = 0.0
            phrase_count = 0

            # Signature phrases
            for phrase, weight in self.victor_voice_patterns['signature_phrases'].items():
                if phrase in content_lower:
                    total_score += weight
                    phrase_count += 1

            metrics.victor_phrase_count = phrase_count

            # Conversational markers
            marker_count = sum(1 for marker in self.victor_voice_patterns['conversational_markers']
                              if marker in content_lower)
            marker_score = min(0.4, marker_count / 8.0)

            # Slang conversion usage
            conversion_score = 0.2 if phrase_count > 0 else 0.0

            metrics.victor_voice_authenticity = min(1.0, total_score + marker_score + conversion_score)

    def _assess_readability(self, content: str, metrics: QualityMetrics):
        """Assess readability for casual content - Flesch-like scoring"""
        sentences = re.split(r'[.!?]+', content)
        words = content.split()
        syllables = self._count_syllables(content)

        if not sentences or not words:
            metrics.readability_score = 0.0
            return

        avg_sentence_length = len(words) / len(sentences)
        avg_syllables_per_word = syllables / len(words)

        # Simplified Flesch Reading Ease for casual content
        # Higher scores for shorter sentences and simpler words
        readability = 206.835 - (1.015 * avg_sentence_length) - (84.6 * avg_syllables_per_word)

        # Convert to 0-1 scale (Flesch typically 0-100, but we want 0-1)
        # For casual content, we want scores around 60-80 on Flesch scale
        optimal_flesch = 70.0
        deviation = abs(readability - optimal_flesch)
        readability_penalty = min(1.0, deviation / 30.0)  # 30 point deviation = full penalty

        metrics.readability_score = max(0.0, 1.0 - readability_penalty)

    def _count_syllables(self, text: str) -> int:
        """Count syllables in text (simplified algorithm)"""
        text = text.lower()
        count = 0
        vowels = "aeiouy"
        prev_char_was_vowel = False

        for char in text:
            if char in vowels:
                if not prev_char_was_vowel:
                    count += 1
                prev_char_was_vowel = True
            else:
                prev_char_was_vowel = False

        # Adjust for silent e
        if text.endswith('e'):
            count -= 1
        if text.endswith('le') and len(text) > 2 and text[-3] not in vowels:
            count += 1

        return max(1, count)

    def _assess_conversational_flow(self, content: str, metrics: QualityMetrics):
        """Assess natural conversational patterns"""
        # Question marks indicate engagement
        question_count = content.count('?')
        question_score = min(0.3, question_count / 3.0)

        # Contractions indicate casual tone
        contractions = len(re.findall(r"\b\w+n't|\b\w+'s|\b\w+'re|\b\w+'ve|\b\w+'ll|\bI'm|\bI've|\bIt's", content))
        contraction_score = min(0.3, contractions / 8.0)

        # Parenthetical asides
        aside_indicators = ['btw', 'by the way', 'actually', 'you know', 'like i said']
        aside_count = sum(1 for indicator in aside_indicators if indicator in content.lower())
        aside_score = min(0.4, aside_count / 2.0)

        metrics.conversational_flow = question_score + contraction_score + aside_score

    def _assess_shareability(self, content: str, metrics: QualityMetrics):
        """Assess viral potential and shareability"""
        # Hook strength (first 100 words)
        first_100_words = ' '.join(content.split()[:100])
        hook_indicators = ['shocking', 'unbelievable', 'crazy', 'insane', 'wild', 'mind-blowing']
        hook_score = 0.3 if any(indicator in first_100_words.lower() for indicator in hook_indicators) else 0.0

        # Controversy or strong opinions
        opinion_indicators = ['worst mistake', 'best decision', 'never do', 'always do', 'complete disaster']
        opinion_score = 0.2 if any(indicator in content.lower() for indicator in opinion_indicators) else 0.0

        # Universal appeal
        universal_topics = ['productivity', 'leadership', 'growth', 'success', 'failure', 'learning']
        universal_score = 0.2 if any(topic in content.lower() for topic in universal_topics) else 0.0

        # Length appropriateness (not too long, not too short)
        word_count = len(content.split())
        length_score = 0.3 if 300 <= word_count <= 1500 else 0.1

        metrics.shareability_score = hook_score + opinion_score + universal_score + length_score

    def _assess_timelessness(self, content: str, metrics: QualityMetrics):
        """Assess long-term value and relevance"""
        # Timeless topics vs time-bound
        timeless_indicators = ['principle', 'fundamental', 'strategy', 'approach', 'methodology', 'best practice']
        timeless_score = 0.4 if any(indicator in content.lower() for indicator in timeless_indicators) else 0.1

        # Specific examples vs generic advice
        specific_indicators = ['when i', 'in my case', 'i found that', 'my experience']
        specific_score = 0.3 if any(indicator in content.lower() for indicator in specific_indicators) else 0.1

        # Actionable advice
        actionable_indicators = ['try this', 'do this', 'avoid that', 'recommend', 'suggest']
        actionable_score = 0.3 if any(indicator in content.lower() for indicator in actionable_indicators) else 0.1

        metrics.timelessness = timeless_score + specific_score + actionable_score

    def _assess_formatting_quality(self, content: str, metrics: QualityMetrics):
        """Assess technical formatting quality"""
        score = 0.0

        # Code blocks properly formatted
        code_blocks = re.findall(r'```(\w+)?\n.*?\n```', content, re.DOTALL)
        if code_blocks:
            score += 0.3
            # Bonus for language specification
            if any(block.startswith('```') and len(block.split('\n')[0]) > 3 for block in code_blocks):
                score += 0.2

        # Headers properly formatted
        headers = re.findall(r'^#{1,6}\s+\w+', content, re.MULTILINE)
        if headers:
            score += min(0.2, len(headers) / 5.0)

        # Lists properly formatted
        lists = re.findall(r'^[-*+]\s+\w+', content, re.MULTILINE)
        if lists:
            score += min(0.2, len(lists) / 8.0)

        # Links properly formatted (markdown)
        links = re.findall(r'\[([^\]]+)\]\(([^)]+)\)', content)
        if links:
            score += 0.1

        metrics.formatting_quality = min(1.0, score)

    def _assess_seo_quality(self, content: str, metrics: QualityMetrics):
        """Assess SEO optimization quality - consolidated from seo_enhancement_processor"""
        score = 0.0

        # Title optimization (assume first line is title)
        first_line = content.split('\n')[0].strip()
        if first_line and not first_line.startswith('#'):
            # Title should be compelling and keyword-rich
            title_keywords = sum(1 for keyword in self.seo_keywords['primary']
                               if keyword in first_line.lower())
            if title_keywords > 0:
                score += 0.3

        # Keyword density (not too sparse, not keyword stuffing)
        all_keywords = []
        for keyword_list in self.seo_keywords.values():
            all_keywords.extend(keyword_list)

        keyword_count = sum(1 for keyword in all_keywords if keyword in content.lower())
        metrics.seo_keyword_count = keyword_count

        word_count = len(content.split())
        if word_count > 0:
            keyword_density = keyword_count / word_count
            if 0.01 <= keyword_density <= 0.05:  # Optimal keyword density
                score += 0.4
            elif keyword_density > 0.05:  # Keyword stuffing penalty
                score += 0.1

        # Internal linking potential
        linkable_phrases = ['click here', 'learn more', 'read this', 'check out']
        linkable_count = sum(1 for phrase in linkable_phrases if phrase in content.lower())
        if linkable_count > 0:
            score += 0.3

        metrics.seo_score = min(1.0, score)

    # BACKWARD COMPATIBILITY METHODS
    # ==============================

    def score_episode(self, content: str, category: ContentCategory) -> QualityMetrics:
        """Legacy method for backward compatibility with episode_quality_scorer.py"""
        return self.assess_content_quality(content, category)

    def validate_voice_quality(self, content: str) -> Dict[str, Any]:
        """Legacy method for backward compatibility with victor_voice_processor.py"""
        try:
            from victor_voice_processor import VictorVoiceProcessor
            processor = VictorVoiceProcessor()
            result = processor.validate_voice_quality(content)
            return result
        except ImportError:
            # Fallback to metrics-based validation
            metrics = self.assess_content_quality(content)
            return {
                'victor_voice_score': metrics.victor_voice_authenticity,
                'phrase_count': metrics.victor_phrase_count,
                'overall_quality': metrics.overall_score,
                'publish_ready': metrics.publish_ready
            }

    def apply_victor_voice(self, content: str, category=None, intensity=None):
        """Legacy method for backward compatibility with victor_voice_processor.py"""
        try:
            from victor_voice_processor import VictorVoiceProcessor, VoiceIntensity
            processor = VictorVoiceProcessor()

            # Convert float intensity to enum
            if isinstance(intensity, float):
                if intensity >= 0.9:
                    intensity_enum = VoiceIntensity.MAXIMUM
                elif intensity >= 0.7:
                    intensity_enum = VoiceIntensity.STRONG
                elif intensity >= 0.5:
                    intensity_enum = VoiceIntensity.MEDIUM
                else:
                    intensity_enum = VoiceIntensity.LIGHT
            else:
                intensity_enum = intensity or VoiceIntensity.MEDIUM

            result = processor.apply_victor_voice(content, category, intensity_enum)
            return result.transformed_content
        except ImportError:
            # Basic fallback transformation
            return self._basic_voice_transformation(content)

    def _basic_voice_transformation(self, content: str) -> str:
        """Basic Victor voice transformation for fallback"""
        # Simple transformations for backward compatibility
        transformations = {
            'I think': 'idk',
            'I believe': 'lowkey feel like',
            'However': 'but also',
            'Therefore': 'so now',
            'Actually': 'tbh',
            'Really': 'for real though'
        }

        result = content
        for formal, victor in transformations.items():
            result = result.replace(formal, victor)

        return result

    def validate_seo_quality(self, content: str, seo_analysis: Any = None) -> Dict[str, Any]:
        """Legacy method for backward compatibility with seo_enhancement_processor.py"""
        metrics = self.assess_content_quality(content)
        return {
            'seo_score': metrics.seo_score,
            'keyword_count': metrics.seo_keyword_count,
            'overall_quality': metrics.overall_score,
            'publish_ready': metrics.publish_ready
        }

    def check_quality_gate(self, content: str, threshold: float = 0.35) -> Dict[str, Any]:
        """Legacy method for backward compatibility with digital_dreamscape_pipeline.py"""
        metrics = self.assess_content_quality(content)
        return {
            'passed': metrics.overall_score >= threshold,
            'score': metrics.overall_score,
            'tier': metrics.quality_tier.value,
            'reason': metrics.tier_description
        }


# GLOBAL INSTANCE for backward compatibility
_consolidated_service = None

def get_quality_service() -> ConsolidatedQualityAssessmentService:
    """Get singleton instance of the consolidated quality service"""
    global _consolidated_service
    if _consolidated_service is None:
        _consolidated_service = ConsolidatedQualityAssessmentService()
    return _consolidated_service


# BACKWARD COMPATIBILITY IMPORTS
# ==============================

# For episode_quality_scorer.py compatibility
EpisodeQualityScorer = ConsolidatedQualityAssessmentService
QualityMetrics = QualityMetrics  # Re-export for compatibility

# For other services compatibility
def assess_content_quality(content: str, category: ContentCategory = None) -> QualityMetrics:
    """Global function for easy access - consolidates all quality assessment needs"""
    return get_quality_service().assess_content_quality(content, category)


if __name__ == "__main__":
    # Quick test
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
    print(f"Victor Voice Authenticity: {metrics.victor_voice_authenticity:.3f}")
    print(f"Content Density: {metrics.content_density:.3f}")
    print(f"Technical Terms Found: {metrics.technical_term_count}")