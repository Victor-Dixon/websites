#!/usr/bin/env python3
"""
Consolidated SEO Service
========================

UNIFIED SEO PROCESSING - Phase 3 Consolidation
Consolidates SEO processing logic from 16+ files into 1 core service.

CONSOLIDATED FROM:
- seo_enhancement_processor.py (primary comprehensive SEO system)
- consolidated_quality_assessment.py (basic SEO scoring)
- digital_dreamscape_pipeline.py (SEO integration)
- content_pipeline.py (SEO pipeline steps)
- episode_publishing_service.py (meta tag handling)
- template_engine.py (SEO-friendly HTML generation)
- Plus additional scattered SEO logic

PROVIDES:
- Complete SEO analysis and enhancement pipeline
- Keyword research and optimization
- Title and meta description generation
- Internal linking suggestions
- SERP intent matching
- Backward compatibility with existing systems
"""

import re
from typing import Dict, List, Any, Optional, Tuple
from dataclasses import dataclass, field
from enum import Enum
from collections import Counter
import logging

logger = logging.getLogger(__name__)


class SERPIntent(Enum):
    """Search intent categories - consolidated from multiple sources"""
    INFORMATIONAL = "informational"  # How-to, what-is, explanations
    COMMERCIAL = "commercial"        # Reviews, comparisons, buying guides
    TRANSACTIONAL = "transactional"  # Direct actions, purchases
    NAVIGATIONAL = "navigational"     # Specific websites/brands


class ContentType(Enum):
    """Content type for SEO optimization"""
    TUTORIAL = "tutorial"
    CASE_STUDY = "case_study"
    EXPLANATION = "explanation"
    NEWS = "news"
    OPINION = "opinion"
    REFERENCE = "reference"
    DEVLOG = "devlog"
    EPISODE = "episode"


@dataclass
class SEOAnalysis:
    """Complete SEO analysis result - consolidated from multiple sources"""
    primary_keyword: str
    secondary_keywords: List[str]
    search_volume_estimate: int
    competition_level: str
    serp_intent: SERPIntent
    title_suggestions: List[str]
    meta_description: str
    headings_structure: Dict[str, List[str]]
    internal_links_suggestions: List[str]
    content_gaps: List[str]
    keyword_density_score: float = 0.0
    readability_seo_score: float = 0.0


@dataclass
class SEOEnhancedContent:
    """SEO-enhanced content result - consolidated"""
    original_content: str
    enhanced_content: str
    seo_analysis: SEOAnalysis
    seo_score: float
    enhancements_applied: List[str]
    title_optimized: str
    meta_description: str
    focus_keywords: List[str]


@dataclass
class KeywordAnalysis:
    """Keyword analysis result"""
    keyword: str
    search_volume: int
    competition: str
    cpc: float
    related_keywords: List[str]


class ConsolidatedSEOService:
    """
    UNIFIED SEO SERVICE
    ===================

    Consolidates all SEO processing logic from 16+ files into a single,
    comprehensive service with backward compatibility.
    """

    def __init__(self):
        """Initialize with SEO databases and patterns"""
        self._setup_keyword_databases()
        self._setup_intent_patterns()
        self._setup_competition_indicators()
        self._setup_content_type_patterns()

    def _setup_keyword_databases(self):
        """Setup keyword databases for different brands and topics"""
        self.brand_keywords = {
            'dadudekc': [
                'web development', 'javascript', 'react', 'portfolio',
                'full stack developer', 'coding tutorials', 'tech blog'
            ],
            'freerideinvestor': [
                'trading strategies', 'stock market', 'investment',
                'technical analysis', 'forex trading', 'crypto'
            ],
            'tradingrobotplug': [
                'automated trading', 'trading robots', 'algorithmic trading',
                'quantitative finance', 'trading systems', 'backtesting'
            ],
            'digitaldreamscape': [
                'content creation', 'ai content', 'blogging', 'seo',
                'digital marketing', 'content strategy'
            ]
        }

        self.intent_keywords = {
            SERPIntent.INFORMATIONAL: [
                'how to', 'what is', 'guide', 'tutorial', 'explained',
                'understanding', 'learn', 'tips for'
            ],
            SERPIntent.COMMERCIAL: [
                'best', 'review', 'comparison', 'vs', 'versus',
                'top rated', 'recommended', 'buying guide'
            ],
            SERPIntent.TRANSACTIONAL: [
                'download', 'buy', 'purchase', 'order', 'subscribe',
                'sign up', 'get started', 'pricing'
            ],
            SERPIntent.NAVIGATIONAL: [
                'official site', 'website', 'login', 'contact',
                'support', 'help', 'about'
            ]
        }

    def _setup_intent_patterns(self):
        """Setup patterns for detecting search intent"""
        self.intent_patterns = {
            SERPIntent.INFORMATIONAL: [
                re.compile(r'\b(how|what|why|when|where|who)\b.*\?', re.IGNORECASE),
                re.compile(r'\bguide|tutorial|explained|learn|tips\b', re.IGNORECASE)
            ],
            SERPIntent.COMMERCIAL: [
                re.compile(r'\bbest|top|review|comparison\b', re.IGNORECASE),
                re.compile(r'\bvs|versus|alternative|option\b', re.IGNORECASE)
            ],
            SERPIntent.TRANSACTIONAL: [
                re.compile(r'\bbuy|download|purchase|order|subscribe\b', re.IGNORECASE),
                re.compile(r'\bpricing|cost|fee|payment\b', re.IGNORECASE)
            ],
            SERPIntent.NAVIGATIONAL: [
                re.compile(r'\bofficial|website|site|login|contact\b', re.IGNORECASE)
            ]
        }

    def _setup_competition_indicators(self):
        """Setup indicators for competition level assessment"""
        self.competition_indicators = {
            'high': ['buy', 'price', 'cost', 'expensive', 'cheap', 'affordable'],
            'medium': ['guide', 'tutorial', 'how to', 'best', 'review'],
            'low': ['what is', 'explained', 'understanding', 'basics', 'introduction']
        }

    def _setup_content_type_patterns(self):
        """Setup patterns for content type detection"""
        self.content_type_patterns = {
            ContentType.TUTORIAL: [
                re.compile(r'\btutorial|guide|how.?to|step.?by.?step\b', re.IGNORECASE)
            ],
            ContentType.CASE_STUDY: [
                re.compile(r'\bcase.?study|success.?story|example|implementation\b', re.IGNORECASE)
            ],
            ContentType.EXPLANATION: [
                re.compile(r'\bexplained|what.?is|understanding|basics\b', re.IGNORECASE)
            ],
            ContentType.DEVLOG: [
                re.compile(r'\bdevlog|development|progress|update\b', re.IGNORECASE)
            ],
            ContentType.EPISODE: [
                re.compile(r'\bepisode|chapter|part|series\b', re.IGNORECASE)
            ]
        }

    def analyze_seo(self, content: str, brand: str = 'digitaldreamscape',
                   target_keyword: Optional[str] = None) -> SEOAnalysis:
        """
        COMPREHENSIVE SEO ANALYSIS
        =========================

        Consolidated SEO analysis from:
        - seo_enhancement_processor.py (keyword analysis, intent matching)
        - consolidated_quality_assessment.py (basic SEO scoring)
        - digital_dreamscape_pipeline.py (SEO integration)

        Args:
            content: The content to analyze
            brand: Target brand for keyword optimization
            target_keyword: Specific keyword to optimize for

        Returns:
            Complete SEOAnalysis object
        """

        # Primary keyword extraction
        primary_keyword = target_keyword or self._extract_primary_keyword(content, brand)

        # Secondary keywords
        secondary_keywords = self._extract_secondary_keywords(content, primary_keyword, brand)

        # Search volume estimation (simplified)
        search_volume = self._estimate_search_volume(primary_keyword, secondary_keywords)

        # Competition level
        competition_level = self._assess_competition_level(primary_keyword, content)

        # SERP intent detection
        serp_intent = self._detect_serp_intent(content, primary_keyword)

        # Title suggestions
        title_suggestions = self._generate_title_suggestions(content, primary_keyword, brand)

        # Meta description
        meta_description = self._generate_meta_description(content, primary_keyword)

        # Headings structure
        headings_structure = self._analyze_headings_structure(content)

        # Internal linking suggestions
        internal_links_suggestions = self._suggest_internal_links(content, brand)

        # Content gaps
        content_gaps = self._identify_content_gaps(content, primary_keyword)

        # Additional scoring
        keyword_density_score = self._calculate_keyword_density(content, primary_keyword, secondary_keywords)
        readability_seo_score = self._assess_readability_for_seo(content)

        return SEOAnalysis(
            primary_keyword=primary_keyword,
            secondary_keywords=secondary_keywords,
            search_volume_estimate=search_volume,
            competition_level=competition_level,
            serp_intent=serp_intent,
            title_suggestions=title_suggestions,
            meta_description=meta_description,
            headings_structure=headings_structure,
            internal_links_suggestions=internal_links_suggestions,
            content_gaps=content_gaps,
            keyword_density_score=keyword_density_score,
            readability_seo_score=readability_seo_score
        )

    def enhance_content_seo(self, content: str, brand: str = 'digitaldreamscape',
                           target_keyword: Optional[str] = None) -> SEOEnhancedContent:
        """
        COMPLETE SEO ENHANCEMENT
        =======================

        Consolidated SEO enhancement from:
        - seo_enhancement_processor.py (comprehensive enhancement)
        - digital_dreamscape_pipeline.py (SEO pipeline)
        - content_pipeline.py (SEO processing steps)

        Args:
            content: Original content
            brand: Target brand
            target_keyword: Target keyword

        Returns:
            SEOEnhancedContent with optimizations applied
        """

        # Perform SEO analysis
        seo_analysis = self.analyze_seo(content, brand, target_keyword)

        # Apply enhancements
        enhanced_content = content
        enhancements_applied = []

        # Optimize title
        title_optimized = self._optimize_title(content, seo_analysis.primary_keyword, brand)

        # Generate meta description
        meta_description = seo_analysis.meta_description

        # Enhance content with keyword optimization
        enhanced_content, keyword_enhancements = self._optimize_content_keywords(
            enhanced_content, seo_analysis.primary_keyword, seo_analysis.secondary_keywords
        )
        enhancements_applied.extend(keyword_enhancements)

        # Improve headings structure
        enhanced_content, heading_enhancements = self._optimize_headings(enhanced_content, seo_analysis.headings_structure)
        enhancements_applied.extend(heading_enhancements)

        # Add internal linking suggestions (as comments for manual review)
        if seo_analysis.internal_links_suggestions:
            link_comment = f"\n<!-- SEO Suggestion: Consider internal links to: {', '.join(seo_analysis.internal_links_suggestions[:3])} -->\n"
            enhanced_content += link_comment

        # Calculate final SEO score
        seo_score = self._calculate_overall_seo_score(enhanced_content, seo_analysis)

        return SEOEnhancedContent(
            original_content=content,
            enhanced_content=enhanced_content,
            seo_analysis=seo_analysis,
            seo_score=seo_score,
            enhancements_applied=enhancements_applied,
            title_optimized=title_optimized,
            meta_description=meta_description,
            focus_keywords=[seo_analysis.primary_keyword] + seo_analysis.secondary_keywords[:5]
        )

    def _extract_primary_keyword(self, content: str, brand: str) -> str:
        """Extract primary keyword from content"""
        # Get brand-specific keywords
        brand_keywords = self.brand_keywords.get(brand, [])

        # Find most relevant keyword
        content_lower = content.lower()
        keyword_scores = {}

        # Score keywords by frequency and position
        for keyword in brand_keywords:
            count = content_lower.count(keyword.lower())
            # Bonus for keywords in title/first paragraph
            first_200 = content_lower[:200]
            position_bonus = 2 if keyword.lower() in first_200 else 1

            keyword_scores[keyword] = count * position_bonus

        # Return highest scoring keyword, or extract from content
        if keyword_scores:
            return max(keyword_scores.items(), key=lambda x: x[1])[0]

        # Fallback: extract most common meaningful words
        words = re.findall(r'\b[a-z]{4,}\b', content_lower)
        word_freq = Counter(words)
        # Filter out common stop words
        stop_words = {'that', 'with', 'have', 'this', 'will', 'your', 'from', 'they', 'know', 'want', 'been', 'good', 'much', 'some', 'time', 'very', 'when', 'come', 'here', 'just', 'like', 'long', 'make', 'many', 'over', 'such', 'take', 'than', 'them', 'well', 'were'}
        filtered_words = [word for word in word_freq.most_common(10) if word[0] not in stop_words]

        return filtered_words[0][0] if filtered_words else 'content'

    def _extract_secondary_keywords(self, content: str, primary_keyword: str, brand: str) -> List[str]:
        """Extract secondary keywords"""
        brand_keywords = self.brand_keywords.get(brand, [])
        content_lower = content.lower()

        # Filter out primary keyword and find related terms
        secondary = []
        for keyword in brand_keywords:
            if keyword.lower() != primary_keyword.lower():
                if keyword.lower() in content_lower:
                    secondary.append(keyword)

        # Add some content-specific keywords
        words = re.findall(r'\b[a-z]{5,}\b', content_lower)  # Longer words likely to be technical
        word_freq = Counter(words)
        technical_terms = [word for word, count in word_freq.most_common(5) if count > 1]

        return secondary + technical_terms

    def _estimate_search_volume(self, primary: str, secondary: List[str]) -> int:
        """Estimate search volume (simplified)"""
        # This would integrate with actual SEO tools in production
        base_volume = 1000  # Default

        # Adjust based on keyword characteristics
        if len(primary.split()) > 1:  # Long-tail keywords
            base_volume *= 1.5
        if any(word in primary.lower() for word in ['how', 'what', 'why', 'best']):
            base_volume *= 2  # Question/intent keywords

        return int(base_volume)

    def _assess_competition_level(self, keyword: str, content: str) -> str:
        """Assess competition level"""
        # Simplified competition assessment
        commercial_indicators = sum(1 for indicator in self.competition_indicators['high']
                                  if indicator in keyword.lower())

        if commercial_indicators > 0:
            return 'high'
        elif len(keyword.split()) > 2:  # Long-tail
            return 'low'
        else:
            return 'medium'

    def _detect_serp_intent(self, content: str, keyword: str) -> SERPIntent:
        """Detect SERP intent"""
        # Check keyword first
        keyword_lower = keyword.lower()
        for intent, keywords in self.intent_keywords.items():
            if any(kw in keyword_lower for kw in keywords):
                return intent

        # Check content patterns
        for intent, patterns in self.intent_patterns.items():
            for pattern in patterns:
                if pattern.search(content):
                    return intent

        return SERPIntent.INFORMATIONAL  # Default

    def _generate_title_suggestions(self, content: str, keyword: str, brand: str) -> List[str]:
        """Generate title suggestions"""
        suggestions = []

        # Extract first sentence/title-like content
        first_line = content.split('\n')[0].strip()
        if first_line and not first_line.startswith('#'):
            suggestions.append(f"{first_line} | {brand.title()}")

        # Keyword-optimized titles
        suggestions.append(f"{keyword.title()}: Complete Guide | {brand.title()}")
        suggestions.append(f"How to {keyword.title()} | {brand.title()}")
        suggestions.append(f"The Ultimate {keyword.title()} | {brand.title()}")

        return suggestions[:5]  # Return top 5

    def _generate_meta_description(self, content: str, keyword: str) -> str:
        """Generate meta description"""
        # Extract first meaningful paragraph
        paragraphs = [p.strip() for p in content.split('\n\n') if p.strip()]
        first_para = paragraphs[0] if paragraphs else content[:150]

        # Clean and truncate
        clean_desc = re.sub(r'[*#`]', '', first_para)
        if len(clean_desc) > 155:
            clean_desc = clean_desc[:152] + "..."

        return clean_desc

    def _analyze_headings_structure(self, content: str) -> Dict[str, List[str]]:
        """Analyze headings structure"""
        headings = {'h1': [], 'h2': [], 'h3': [], 'h4': []}

        # Find markdown headings
        heading_pattern = re.compile(r'^(#{1,4})\s+(.+)$', re.MULTILINE)
        for match in heading_pattern.finditer(content):
            level = len(match.group(1))
            text = match.group(2).strip()
            heading_key = f'h{level}'
            if heading_key in headings:
                headings[heading_key].append(text)

        return headings

    def _suggest_internal_links(self, content: str, brand: str) -> List[str]:
        """Suggest internal linking opportunities"""
        # This would integrate with actual site structure in production
        suggestions = []

        brand_keywords = self.brand_keywords.get(brand, [])
        for keyword in brand_keywords[:3]:  # Top 3 brand keywords
            if keyword.lower() in content.lower():
                suggestions.append(f"/{brand}/{keyword.replace(' ', '-')}")

        return suggestions

    def _identify_content_gaps(self, content: str, keyword: str) -> List[str]:
        """Identify content gaps for SEO improvement"""
        gaps = []

        # Check for missing elements
        if not re.search(r'\bh[12]\b', content):  # No H1/H2
            gaps.append("Missing proper heading structure")

        if len(content.split()) < 300:
            gaps.append("Content too short for comprehensive coverage")

        if keyword.lower() not in content.lower()[:100]:
            gaps.append(f"Primary keyword '{keyword}' not in first 100 words")

        # Check for multimedia
        if '```' not in content and 'image' not in content.lower():
            gaps.append("Missing code examples or images")

        return gaps

    def _calculate_keyword_density(self, content: str, primary: str, secondary: List[str]) -> float:
        """Calculate keyword density score"""
        content_lower = content.lower()
        words = content.split()
        total_words = len(words)

        if total_words == 0:
            return 0.0

        # Count keyword occurrences
        primary_count = content_lower.count(primary.lower())
        secondary_count = sum(content_lower.count(kw.lower()) for kw in secondary)

        # Optimal density: 1-3% for primary, 0.5-1% for secondary
        primary_density = (primary_count * len(primary.split())) / total_words
        secondary_density = (secondary_count * 2) / total_words  # Average secondary keyword length

        # Score based on optimal ranges
        primary_score = 1.0 if 0.01 <= primary_density <= 0.03 else 0.5
        secondary_score = 1.0 if 0.005 <= secondary_density <= 0.01 else 0.5

        return (primary_score + secondary_score) / 2

    def _assess_readability_for_seo(self, content: str) -> float:
        """Assess readability optimized for SEO"""
        sentences = re.split(r'[.!?]+', content)
        words = content.split()
        syllables = self._count_syllables(content)

        if not sentences or not words:
            return 0.0

        avg_words_per_sentence = len(words) / len(sentences)
        avg_syllables_per_word = syllables / len(words)

        # Use Flesch Reading Ease formula
        flesch_score = 206.835 - (1.015 * avg_words_per_sentence) - (84.6 * avg_syllables_per_word)

        # For SEO content, aim for 60-70 Flesch score (easily readable)
        if 60 <= flesch_score <= 70:
            return 1.0
        elif 50 <= flesch_score <= 80:
            return 0.8
        else:
            return 0.5

    def _count_syllables(self, text: str) -> int:
        """Count syllables in text"""
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

        return max(1, count)

    def _optimize_title(self, content: str, keyword: str, brand: str) -> str:
        """Optimize title for SEO"""
        # Extract current title
        first_line = content.split('\n')[0].strip()
        if first_line.startswith('#'):
            current_title = first_line[1:].strip()
        else:
            current_title = first_line

        # Ensure keyword is included
        if keyword.lower() not in current_title.lower():
            optimized = f"{current_title} | {keyword.title()}"
        else:
            optimized = current_title

        # Add brand if not present
        if brand.lower() not in optimized.lower():
            optimized = f"{optimized} | {brand.title()}"

        # Ensure length is appropriate
        if len(optimized) > 60:
            optimized = optimized[:57] + "..."

        return optimized

    def _optimize_content_keywords(self, content: str, primary: str, secondary: List[str]) -> Tuple[str, List[str]]:
        """Optimize content for keywords"""
        enhancements = []
        optimized = content

        # Ensure primary keyword appears naturally
        primary_lower = primary.lower()
        if primary_lower not in content.lower():
            # Add to first paragraph if missing
            paragraphs = content.split('\n\n')
            if paragraphs:
                first_para = paragraphs[0]
                # Simple insertion (would be more sophisticated in production)
                optimized = optimized.replace(first_para, f"{first_para} Learn about {primary}.", 1)
                enhancements.append(f"Added primary keyword '{primary}' to content")

        return optimized, enhancements

    def _optimize_headings(self, content: str, headings_structure: Dict[str, List[str]]) -> Tuple[str, List[str]]:
        """Optimize headings structure"""
        enhancements = []

        # Check for missing H1
        if not headings_structure.get('h1'):
            # Add H1 if missing
            first_line = content.split('\n')[0]
            if not first_line.startswith('#'):
                optimized = f"# {first_line}\n\n{content[len(first_line):].lstrip()}"
                enhancements.append("Added H1 heading for SEO")
                return optimized, enhancements

        return content, enhancements

    def _calculate_overall_seo_score(self, content: str, analysis: SEOAnalysis) -> float:
        """Calculate overall SEO score"""
        score_components = {
            'keyword_optimization': analysis.keyword_density_score,
            'readability': analysis.readability_seo_score,
            'title_optimization': 1.0 if analysis.primary_keyword in analysis.title_suggestions[0] else 0.7,
            'competition': 0.8 if analysis.competition_level == 'low' else (0.6 if analysis.competition_level == 'medium' else 0.4),
            'search_volume': min(1.0, analysis.search_volume_estimate / 10000)  # Normalize
        }

        # Weighted average
        weights = {
            'keyword_optimization': 0.3,
            'readability': 0.2,
            'title_optimization': 0.2,
            'competition': 0.15,
            'search_volume': 0.15
        }

        total_score = sum(score * weights[component] for component, score in score_components.items())
        return min(1.0, max(0.0, total_score))

    # BACKWARD COMPATIBILITY METHODS
    # ==============================

    def process_seo_enhancement(self, content: str, **kwargs) -> SEOEnhancedContent:
        """Legacy method for backward compatibility with seo_enhancement_processor.py"""
        return self.enhance_content_seo(content, **kwargs)

    def validate_seo_quality(self, content: str, seo_analysis: Optional[SEOAnalysis] = None) -> Dict[str, Any]:
        """Legacy method for backward compatibility"""
        if seo_analysis is None:
            seo_analysis = self.analyze_seo(content)

        return {
            'seo_score': self._calculate_overall_seo_score(content, seo_analysis),
            'keyword_optimized': seo_analysis.keyword_density_score > 0.7,
            'readability_score': seo_analysis.readability_seo_score,
            'primary_keyword': seo_analysis.primary_keyword,
            'competition_level': seo_analysis.competition_level
        }

    def calculate_seo_score(self, content: str, seo_analysis: SEOAnalysis) -> float:
        """Legacy method for backward compatibility"""
        return self._calculate_overall_seo_score(content, seo_analysis)


# BACKWARD COMPATIBILITY IMPORTS
# ==============================

# For seo_enhancement_processor.py compatibility
SEOEnhancementProcessor = ConsolidatedSEOService
SEOAnalysis = SEOAnalysis  # Re-export for compatibility
SEOEnhancedContent = SEOEnhancedContent

# Global instance for singleton access
_seo_service = None

def get_seo_service() -> ConsolidatedSEOService:
    """Get singleton instance of the consolidated SEO service"""
    global _seo_service
    if _seo_service is None:
        _seo_service = ConsolidatedSEOService()
    return _seo_service


if __name__ == "__main__":
    # Quick test
    service = ConsolidatedSEOService()

    print("🔍 CONSOLIDATED SEO SERVICE TEST")
    print("=" * 50)

    test_content = """
# The Race Condition That Almost Broke Production

So there I was, 2 AM on a Thursday, staring at logs that made no sense. Our payment processing system was losing transactions randomly - like once every 500 requests.

The issue was subtle. We had this shared cache map that multiple goroutines were hitting. One goroutine writing payment states, another reading to validate transactions. No mutex. Classic race condition.

What made it worse? The bug only triggered under load. The fix was embarrassing. One line: add a sync.RWMutex. But the lesson? Race conditions don't show up in testing. You need to design for concurrency from day one.

That race condition detector in the IDE? tbh, it's saved me more times than I can count.
"""

    print("📝 Analyzing test content for SEO...")
    analysis = service.analyze_seo(test_content, brand='digitaldreamscape')

    print("\n✅ SEO Analysis Complete:")
    print(f"  Primary Keyword: {analysis.primary_keyword}")
    print(f"  Secondary Keywords: {', '.join(analysis.secondary_keywords[:3])}")
    print(f"  SERP Intent: {analysis.serp_intent.value}")
    print(f"  Competition Level: {analysis.competition_level}")
    print(f"  Estimated Search Volume: {analysis.search_volume_estimate}")

    print("\n🎯 SEO Enhancement:")
    enhanced = service.enhance_content_seo(test_content)
    print(f"  SEO Score: {enhanced.seo_score:.3f}")
    print(f"  Enhancements Applied: {len(enhanced.enhancements_applied)}")
    print(f"  Optimized Title: {enhanced.title_optimized[:60]}...")

    print("\n🔍 Quality Validation:")
    validation = service.validate_seo_quality(test_content, analysis)
    print(f"  SEO Score: {validation['seo_score']:.3f}")
    print(f"  Keyword Optimized: {'✅' if validation['keyword_optimized'] else '❌'}")
    print(f"  Readability Score: {validation['readability_score']:.3f}")

    print("\n🎉 SEO service consolidation test complete!")