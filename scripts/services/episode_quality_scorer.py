"""
Episode Quality Scorer - Advanced metrics for Digital Dreamscape episodes
=======================================================================

Replaces the basic quality assessment with sophisticated metrics that better
capture what makes a compelling episode.
"""

from dataclasses import dataclass, field
from typing import Dict, List, Any, Optional, Tuple
import re
from enum import Enum
import nltk
from collections import Counter
import math

class ContentCategory(Enum):
    """Enhanced content categories"""
    TECHNICAL = "technical"
    STRATEGIC = "strategic"
    OPERATIONAL = "operational"
    NARRATIVE = "narrative"
    LEARNING = "learning"
    REFLECTION = "reflection"

@dataclass
class QualityMetrics:
    """Comprehensive quality metrics for episode evaluation"""

    # Core Content Quality (40%)
    content_density: float = 0.0  # Information per word ratio
    structural_integrity: float = 0.0  # Logical flow and organization
    factual_accuracy: float = 0.0  # Technical correctness

    # Narrative Quality (25%)
    storytelling_flow: float = 0.0  # Narrative coherence
    emotional_resonance: float = 0.0  # Reader engagement potential
    insight_density: float = 0.0  # Learning moments per content

    # Voice & Style (20%)
    victor_voice_authenticity: float = 0.0  # How well Victor's personality shines through
    readability_score: float = 0.0  # Flesch reading ease for casual content
    conversational_flow: float = 0.0  # Natural dialogue patterns

    # Engagement Potential (10%)
    shareability_score: float = 0.0  # Viral potential
    timelessness: float = 0.0  # Long-term value

    # Technical Quality (5%)
    formatting_quality: float = 0.0  # Proper markdown/code formatting

    # Metadata
    category: ContentCategory = ContentCategory.OPERATIONAL
    word_count: int = 0
    sentence_count: int = 0
    technical_term_count: int = 0
    personal_anecdote_count: int = 0

    @property
    def overall_score(self) -> float:
        """Calculate weighted overall score"""
        # Adjusted weights to be more realistic - high-quality content should score well
        weights = {
            'content_density': 0.12,      # Slightly reduced - was too generous
            'structural_integrity': 0.08,  # Reduced - good structure is important but not everything
            'factual_accuracy': 0.12,     # Technical correctness matters
            'storytelling_flow': 0.10,    # Narrative coherence
            'emotional_resonance': 0.10,  # Reader engagement
            'insight_density': 0.10,      # Learning value - increased
            'victor_voice_authenticity': 0.15,  # Victor's personality - increased
            'readability_score': 0.08,    # Conversational readability
            'conversational_flow': 0.08,  # Natural dialogue
            'shareability_score': 0.05,   # Viral potential
            'timelessness': 0.04,         # Long-term value
            'formatting_quality': 0.08    # Technical presentation - increased from 0
        }

        score = sum(getattr(self, metric) * weight for metric, weight in weights.items())
        return min(1.0, max(0.0, score))

    @property
    def quality_tier(self) -> str:
        """Get quality tier based on score"""
        score = self.overall_score
        if score >= 0.80: return "PLATINUM"   # Episode-worthy
        elif score >= 0.65: return "GOLD"     # High quality
        elif score >= 0.50: return "SILVER"   # Good quality
        elif score >= 0.40: return "BRONZE"   # Acceptable
        else: return "REJECTED"               # Needs work

class EpisodeQualityScorer:
    """Advanced quality scorer using ML-inspired metrics"""

    def __init__(self):
        # Technical term databases
        self.technical_terms = {
            'technical': [
                'api', 'database', 'server', 'client', 'framework', 'library',
                'algorithm', 'function', 'method', 'class', 'object', 'variable',
                'debug', 'error', 'exception', 'log', 'trace', 'stack', 'memory',
                'performance', 'optimization', 'scalability', 'architecture'
            ],
            'strategic': [
                'vision', 'strategy', 'planning', 'roadmap', 'milestone',
                'stakeholder', 'objective', 'goal', 'execution', 'leadership',
                'decision', 'priority', 'resource', 'timeline', 'deliverable'
            ],
            'operational': [
                'task', 'process', 'workflow', 'automation', 'integration',
                'deployment', 'monitoring', 'maintenance', 'coordination',
                'communication', 'status', 'update', 'progress', 'deadline'
            ]
        }

        # Victor voice patterns (weighted by authenticity)
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

        # Emotional engagement indicators
        self.emotional_indicators = {
            'curiosity': ['wonder', 'curious', 'interesting', 'fascinating', 'weird'],
            'frustration': ['frustrating', 'annoying', 'problem', 'issue', 'challenge'],
            'satisfaction': ['finally', 'worked', 'success', 'accomplished', 'proud'],
            'reflection': ['learned', 'realized', 'now i see', 'looking back', 'hindsight']
        }

    def score_episode(self, content: str, category: ContentCategory) -> QualityMetrics:
        """Comprehensive quality scoring"""
        metrics = QualityMetrics(category=category)

        # Basic content analysis
        words = content.split()
        sentences = re.split(r'[.!?]+', content)
        metrics.word_count = len(words)
        metrics.sentence_count = len([s for s in sentences if s.strip()])

        # Technical term analysis
        metrics.technical_term_count = self._count_technical_terms(content, category)

        # Core quality metrics
        metrics.content_density = self._calculate_content_density(content, words)
        metrics.structural_integrity = self._assess_structural_integrity(content)
        metrics.factual_accuracy = self._assess_factual_accuracy(content, category)

        # Narrative quality
        metrics.storytelling_flow = self._assess_storytelling_flow(content, sentences)
        metrics.emotional_resonance = self._assess_emotional_resonance(content)
        metrics.insight_density = self._calculate_insight_density(content)

        # Voice & style
        metrics.victor_voice_authenticity = self._score_victor_voice(content)
        metrics.readability_score = self._calculate_readability(content)
        metrics.conversational_flow = self._assess_conversational_flow(content)

        # Engagement potential
        metrics.shareability_score = self._calculate_shareability(content)
        metrics.timelessness = self._assess_timelessness(content)

        # Technical quality
        metrics.formatting_quality = self._assess_formatting_quality(content)

        return metrics

    def _calculate_content_density(self, content: str, words: List[str]) -> float:
        """Measure information density (unique concepts per word)"""
        if not words:
            return 0.0

        # Remove stop words for better density calculation
        stop_words = {'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'shall'}

        meaningful_words = [w.lower() for w in words if w.lower() not in stop_words and len(w) > 2]

        if not meaningful_words:
            return 0.0

        # Calculate lexical diversity
        unique_words = set(meaningful_words)
        diversity_ratio = len(unique_words) / len(meaningful_words)

        # Length factor - longer content can have lower diversity and still be dense
        length_factor = min(0.3, len(words) / 200)  # Favor longer content but not too much

        # Technical content bonus (reduced)
        category = self._detect_category(content)
        technical_bonus = min(0.15, self._count_technical_terms(content, category) / max(1, len(words) * 0.1))

        # Specific details bonus (numbers, quotes, etc.)
        specific_indicators = len(re.findall(r'\d+', content)) + len(re.findall(r'["`]', content))
        specific_bonus = min(0.15, specific_indicators / max(1, len(words) * 0.05))

        # Calculate density with more reasonable expectations
        base_density = diversity_ratio * 0.7 + length_factor + technical_bonus + specific_bonus

        return min(1.0, max(0.0, base_density))

    def _assess_structural_integrity(self, content: str) -> float:
        """Assess logical flow and organization"""
        score = 0.0

        # Check for logical connectors
        logical_connectors = ['because', 'therefore', 'however', 'although', 'since', 'so', 'then', 'after', 'before', 'when', 'while']
        connector_count = sum(1 for conn in logical_connectors if conn in content.lower())
        score += min(0.3, connector_count * 0.05)

        # Check for chronological flow
        time_indicators = ['first', 'then', 'next', 'after', 'before', 'during', 'while', 'finally', 'eventually']
        time_count = sum(1 for ind in time_indicators if ind in content.lower())
        score += min(0.2, time_count * 0.04)

        # Check for paragraph structure
        paragraphs = [p.strip() for p in content.split('\n\n') if p.strip()]
        if len(paragraphs) >= 3:
            score += 0.2
        elif len(paragraphs) >= 2:
            score += 0.1

        # Check for topic coherence (avoid topic jumping)
        sentences = re.split(r'[.!?]+', content)
        topic_shifts = 0
        for i in range(1, min(len(sentences), 10)):
            # Simple heuristic: look for abrupt topic changes
            if len(sentences[i].split()) < 3:
                topic_shifts += 1

        coherence_penalty = min(0.2, topic_shifts * 0.05)
        score -= coherence_penalty

        return max(0.0, min(1.0, score))

    def _assess_factual_accuracy(self, content: str, category: ContentCategory) -> float:
        """Assess technical correctness and factual accuracy"""
        score = 0.0

        # Category-specific accuracy checks
        if category == ContentCategory.TECHNICAL:
            # Check for proper technical terminology usage
            tech_terms = self.technical_terms['technical']
            proper_usage = sum(1 for term in tech_terms if term in content.lower())
            score += min(0.6, proper_usage * 0.05)

            # Check for code-like elements
            if re.search(r'`[^`]+`', content) or '```' in content:
                score += 0.3

        elif category == ContentCategory.STRATEGIC:
            strategy_terms = self.technical_terms['strategic']
            proper_usage = sum(1 for term in strategy_terms if term in content.lower())
            score += min(0.6, proper_usage * 0.08)

        # General accuracy indicators
        # Avoid obvious errors
        error_indicators = ['????', '!!!!', 'null', 'undefined', 'error: error', 'fixme', 'todo']
        error_penalty = sum(1 for ind in error_indicators if ind in content.lower())
        score -= min(0.3, error_penalty * 0.1)

        # Prefer specific details over vague language
        specific_indicators = ['specifically', 'exactly', 'precisely', 'actually', 'in particular']
        specific_bonus = sum(1 for ind in specific_indicators if ind in content.lower())
        score += min(0.2, specific_bonus * 0.05)

        return max(0.0, min(1.0, score))

    def _assess_storytelling_flow(self, content: str, sentences: List[str]) -> float:
        """Assess narrative coherence and storytelling quality"""
        score = 0.0

        # Check for story elements
        story_elements = ['started', 'then', 'after', 'when', 'during', 'finally', 'eventually']
        story_flow = sum(1 for elem in story_elements if elem in content.lower())
        score += min(0.3, story_flow * 0.05)

        # Check for personal anecdotes
        anecdote_indicators = ['i remember', 'one time', 'there was this', 'i was trying', 'i thought']
        anecdotes = sum(1 for ind in anecdote_indicators if ind in content.lower())
        score += min(0.3, anecdotes * 0.08)

        # Check for problem-solution structure
        problem_words = ['problem', 'issue', 'challenge', 'bug', 'error', 'stuck']
        solution_words = ['solution', 'fixed', 'resolved', 'worked', 'figured out', 'learned']

        has_problem = any(word in content.lower() for word in problem_words)
        has_solution = any(word in content.lower() for word in solution_words)

        if has_problem and has_solution:
            score += 0.4

        return min(1.0, score)

    def _assess_emotional_resonance(self, content: str) -> float:
        """Assess emotional engagement potential"""
        score = 0.0

        # Check for emotional indicators across categories
        total_emotional_words = 0
        for emotion_type, indicators in self.emotional_indicators.items():
            count = sum(1 for ind in indicators if ind in content.lower())
            total_emotional_words += count
            # Weight curiosity and reflection higher
            if emotion_type in ['curiosity', 'reflection']:
                score += min(0.2, count * 0.03)
            else:
                score += min(0.15, count * 0.02)

        # Check for intensity markers
        intensity_markers = ['really', 'so much', 'absolutely', 'totally', 'completely', 'extremely']
        intensity_count = sum(1 for marker in intensity_markers if marker in content.lower())
        score += min(0.2, intensity_count * 0.02)

        # Check for rhetorical questions (engagement)
        question_count = content.count('?')
        score += min(0.2, question_count * 0.03)

        return min(1.0, score)

    def _calculate_insight_density(self, content: str) -> float:
        """Calculate density of learning moments and insights"""
        score = 0.0

        # Learning indicators
        learning_signals = [
            'learned', 'realized', 'understood', 'discovered', 'figured out',
            'now i see', 'the key was', 'what mattered', 'the lesson',
            'important to remember', 'next time', 'mistake', 'improvement'
        ]

        learning_count = sum(1 for signal in learning_signals if signal in content.lower())
        score += min(0.6, learning_count * 0.08)

        # Insight depth indicators
        deep_insights = [
            'actually means', 'really about', 'underlying', 'root cause',
            'bigger picture', 'long term', 'systemic', 'paradigm'
        ]

        deep_count = sum(1 for insight in deep_insights if insight in content.lower())
        score += min(0.4, deep_count * 0.1)

        return min(1.0, score)

    def _score_victor_voice(self, content: str) -> float:
        """Score how authentically Victor's voice comes through"""
        score = 0.0
        content_lower = content.lower()

        # Check for signature phrases - more generous scoring
        signature_count = 0
        signature_weight_sum = 0.0
        for phrase, weight in self.victor_voice_patterns['signature_phrases'].items():
            if phrase in content_lower:
                signature_count += 1
                signature_weight_sum += weight

        # Base score from signature phrases (more generous)
        if signature_count > 0:
            avg_weight = signature_weight_sum / signature_count
            score += min(0.6, avg_weight * 0.4 + (signature_count - 1) * 0.1)

        # Check for conversational markers - increased impact
        conversational_count = sum(1 for marker in self.victor_voice_patterns['conversational_markers']
                                 if marker in content_lower)
        score += min(0.3, conversational_count * 0.04)

        # Check for slang conversion usage - more generous
        slang_conversion_bonus = 0.0
        for formal, victor_version in self.victor_voice_patterns['slang_conversions'].items():
            if victor_version in content_lower:
                slang_conversion_bonus += 0.08  # Increased from 0.05

        score += min(0.3, slang_conversion_bonus)

        # Bonus for overall conversational tone
        if score > 0.2:  # If we have some Victor elements
            # Check for lack of formal language (bonus)
            formal_words = ['therefore', 'however', 'moreover', 'consequently', 'accordingly', 'furthermore']
            formal_count = sum(1 for word in formal_words if word in content_lower)
            if formal_count == 0:
                score += 0.1  # Bonus for conversational tone

        return max(0.0, min(1.0, score))

    def _calculate_readability(self, content: str) -> float:
        """Calculate readability score appropriate for casual content"""
        words = content.split()
        sentences = [s for s in re.split(r'[.!?]+', content) if s.strip()]

        if not words or not sentences:
            return 0.0

        # Simplified Flesch Reading Ease for casual content
        # Lower scores = more complex (but we want conversational complexity)
        avg_words_per_sentence = len(words) / len(sentences)
        avg_syllables_per_word = sum(self._count_syllables(word) for word in words) / len(words)

        # Adjusted formula for conversational content
        readability = 206.835 - (1.015 * avg_words_per_sentence) - (84.6 * avg_syllables_per_word)

        # Normalize to 0-1 scale (lower readability scores are better for casual content)
        # Flesch scores: 0-30 = very difficult, 90-100 = very easy
        # We want scores around 60-80 for conversational content
        if readability < 30:
            score = 0.0  # Too complex
        elif readability > 90:
            score = 0.3  # Too simple
        elif 60 <= readability <= 80:
            score = 1.0  # Perfect conversational range
        else:
            # Linear interpolation
            if readability < 60:
                score = 0.3 + (0.7 * (readability - 30) / 30)
            else:
                score = 0.3 + (0.7 * (90 - readability) / 10)

        return score

    def _assess_conversational_flow(self, content: str) -> float:
        """Assess natural conversational patterns"""
        score = 0.0

        # Check for question marks (conversational)
        question_ratio = content.count('?') / max(1, len(content.split()))
        score += min(0.2, question_ratio * 50)  # Bonus for questions

        # Check for ellipses (thoughtful pauses)
        ellipsis_count = content.count('...')
        score += min(0.2, ellipsis_count * 0.05)

        # Check for contractions (conversational)
        contraction_pattern = r"\b\w+n't\b|\b\w+'re\b|\b\w+'ve\b|\b\w+'ll\b|\bI'm\b|\bI'd\b"
        contractions = len(re.findall(contraction_pattern, content))
        score += min(0.3, contractions * 0.02)

        # Check for filler words (but not too many)
        fillers = ['like', 'you know', 'kinda', 'sorta', 'probably', 'i guess']
        filler_count = sum(1 for filler in fillers if filler in content.lower())
        if filler_count > 0 and filler_count <= 5:
            score += 0.3
        elif filler_count > 10:
            score -= 0.2  # Too many fillers

        return max(0.0, min(1.0, score))

    def _calculate_shareability(self, content: str) -> float:
        """Calculate viral/shareability potential"""
        score = 0.0

        # Check for shareable elements
        shareable_indicators = [
            'mind blown', 'game changer', 'never thought', 'crazy', 'wild',
            'brilliant', 'genius', 'amazing', 'incredible', 'shocking'
        ]

        shareable_count = sum(1 for ind in shareable_indicators if ind in content.lower())
        score += min(0.4, shareable_count * 0.08)

        # Check for controversy or strong opinions
        strong_opinions = ['worst', 'best', 'terrible', 'awesome', 'hate', 'love', 'always', 'never']
        opinion_count = sum(1 for op in strong_opinions if op in content.lower())
        score += min(0.3, opinion_count * 0.03)

        # Check for universal appeal
        universal_topics = ['everyone', 'anyone', 'always', 'never', 'people', 'world', 'life']
        universal_count = sum(1 for topic in universal_topics if topic in content.lower())
        score += min(0.3, universal_count * 0.02)

        return min(1.0, score)

    def _assess_timelessness(self, content: str) -> float:
        """Assess long-term value and relevance"""
        score = 0.0

        # Timeless topics
        timeless_indicators = [
            'fundamental', 'principle', 'pattern', 'best practice', 'lesson learned',
            'approach', 'methodology', 'strategy', 'philosophy', 'mindset'
        ]

        timeless_count = sum(1 for ind in timeless_indicators if ind in content.lower())
        score += min(0.5, timeless_count * 0.08)

        # Avoid time-sensitive content
        time_sensitive_penalty = 0.0
        time_indicators = ['today', 'yesterday', 'tomorrow', 'next week', 'last month', 'this year']
        time_penalty = sum(1 for ind in time_indicators if ind in content.lower())
        score -= min(0.3, time_penalty * 0.05)

        # Prefer evergreen advice
        evergreen_signals = ['always', 'never', 'generally', 'typically', 'usually', 'tends to']
        evergreen_bonus = sum(1 for sig in evergreen_signals if sig in content.lower())
        score += min(0.3, evergreen_bonus * 0.03)

        return max(0.0, min(1.0, score))

    def _assess_formatting_quality(self, content: str) -> float:
        """Assess technical formatting quality"""
        score = 0.0

        # Check for proper markdown
        if '**' in content: score += 0.2  # Bold
        if '*' in content: score += 0.1   # Italic
        if '`' in content: score += 0.3   # Code
        if '\n\n' in content: score += 0.2  # Paragraphs
        if re.search(r'^\s*[-*+]\s', content, re.MULTILINE): score += 0.2  # Lists

        return min(1.0, score)

    def _count_technical_terms(self, content: str, category: ContentCategory) -> int:
        """Count technical terms based on category"""
        content_lower = content.lower()
        category_key = category.value if category.value in self.technical_terms else 'technical'
        return sum(1 for term in self.technical_terms[category_key] if term in content_lower)

    def _detect_category(self, content: str) -> ContentCategory:
        """Auto-detect content category"""
        content_lower = content.lower()

        # Technical detection
        tech_score = sum(1 for term in self.technical_terms['technical'] if term in content_lower)

        # Strategic detection
        strategy_score = sum(1 for term in self.technical_terms['strategic'] if term in content_lower)

        # Operational detection
        op_score = sum(1 for term in self.technical_terms['operational'] if term in content_lower)

        # Narrative/reflection detection
        narrative_indicators = ['learned', 'realized', 'experience', 'journey', 'reflect']
        narrative_score = sum(1 for ind in narrative_indicators if ind in content_lower)

        scores = {
            ContentCategory.TECHNICAL: tech_score,
            ContentCategory.STRATEGIC: strategy_score,
            ContentCategory.OPERATIONAL: op_score,
            ContentCategory.NARRATIVE: narrative_score
        }

        return max(scores, key=scores.get)

    def _count_syllables(self, word: str) -> int:
        """Simple syllable counting for readability calculation"""
        word = word.lower()
        count = 0
        vowels = "aeiouy"
        if word[0] in vowels:
            count += 1
        for index in range(1, len(word)):
            if word[index] in vowels and word[index - 1] not in vowels:
                count += 1
        if word.endswith("e"):
            count -= 1
        if count == 0:
            count += 1
        return count

    def get_quality_report(self, metrics: QualityMetrics) -> str:
        """Generate detailed quality report"""
        report = f"""
Episode Quality Report
======================

Overall Score: {metrics.overall_score:.3f} ({metrics.quality_tier})
Category: {metrics.category.value}
Word Count: {metrics.word_count}

Detailed Metrics:
-----------------
Content Quality (40%):
  • Content Density: {metrics.content_density:.3f}
  • Structural Integrity: {metrics.structural_integrity:.3f}
  • Factual Accuracy: {metrics.factual_accuracy:.3f}

Narrative Quality (25%):
  • Storytelling Flow: {metrics.storytelling_flow:.3f}
  • Emotional Resonance: {metrics.emotional_resonance:.3f}
  • Insight Density: {metrics.insight_density:.3f}

Voice & Style (20%):
  • Victor Voice Authenticity: {metrics.victor_voice_authenticity:.3f}
  • Readability Score: {metrics.readability_score:.3f}
  • Conversational Flow: {metrics.conversational_flow:.3f}

Engagement Potential (10%):
  • Shareability Score: {metrics.shareability_score:.3f}
  • Timelessness: {metrics.timelessness:.3f}

Technical Quality (5%):
  • Formatting Quality: {metrics.formatting_quality:.3f}

Metadata:
  • Sentences: {metrics.sentence_count}
  • Technical Terms: {metrics.technical_term_count}
  • Personal Anecdotes: {metrics.personal_anecdote_count}
"""
        return report