"""
SEO Enhancement Processor - Complete SEO Pipeline
=================================================

Handles keyword research, title optimization, meta descriptions, internal linking,
and SERP intent matching for Digital Dreamscape content.
"""

import re
from typing import Dict, List, Any, Optional, Tuple
from dataclasses import dataclass
from enum import Enum
import logging
from collections import Counter

logger = logging.getLogger(__name__)

class SERPIntent(Enum):
    """Search intent categories"""
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

@dataclass
class SEOAnalysis:
    """Complete SEO analysis result"""
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

@dataclass
class SEOEnhancedContent:
    """SEO-enhanced content result"""
    original_content: str
    enhanced_content: str
    seo_metadata: SEOAnalysis
    enhancements_applied: List[str]
    seo_score: float

class SEOEnhancementProcessor:
    """
    Complete SEO enhancement processor for Digital Dreamscape content.

    Features:
    - Primary/secondary keyword identification
    - Title optimization for CTR
    - Meta description crafting
    - H-tag structure optimization
    - Internal linking suggestions
    - SERP intent matching
    - Content gap analysis
    """

    def __init__(self):
        # Keyword databases (in production, these would be from APIs)
        self.primary_keywords_db = {
            'trading': ['trading strategy', 'trading signals', 'market analysis', 'trading bot'],
            'swarm': ['multi-agent system', 'swarm intelligence', 'agent coordination', 'distributed ai'],
            'dreamscape_lore': ['digital narrative', 'ai storytelling', 'interactive fiction', 'world building'],
            'devlog': ['development log', 'project update', 'build process', 'development diary'],
            'tools': ['developer tools', 'productivity software', 'automation tools', 'ai assistants']
        }

        self.secondary_keywords_db = {
            'technical': ['implementation', 'architecture', 'performance', 'optimization', 'debugging'],
            'strategic': ['planning', 'execution', 'leadership', 'decision making', 'roadmap'],
            'operational': ['process', 'workflow', 'automation', 'monitoring', 'maintenance'],
            'narrative': ['story', 'experience', 'journey', 'reflection', 'learning']
        }

        # SERP intent patterns
        self.intent_patterns = {
            SERPIntent.INFORMATIONAL: [
                r'how\s+to', r'what\s+is', r'why\s+does', r'explain', r'guide',
                r'tutorial', r'learn', r'understand', r'overview'
            ],
            SERPIntent.COMMERCIAL: [
                r'best\s+', r'top\s+', r'review', r'comparison', r'vs',
                r'versus', r'pros\s+and\s+cons', r'alternatives'
            ],
            SERPIntent.TRANSACTIONAL: [
                r'buy', r'purchase', r'download', r'get', r'install',
                r'sign\s+up', r'subscribe', r'register'
            ],
            SERPIntent.NAVIGATIONAL: [
                r'official', r'website', r'home\s+page', r'login', r'contact'
            ]
        }

        # Title templates for different content types
        self.title_templates = {
            ContentType.TUTORIAL: [
                "How to {primary} {secondary} - Complete Guide",
                "{primary} Tutorial: {secondary} Step by Step",
                "Master {primary}: {secondary} Implementation"
            ],
            ContentType.CASE_STUDY: [
                "How {primary} {secondary} Increased Performance by X%",
                "{primary} Success Story: {secondary} Results",
                "Real Results: {primary} {secondary} Case Study"
            ],
            ContentType.EXPLANATION: [
                "What is {primary}? {secondary} Explained",
                "Understanding {primary}: {secondary} Deep Dive",
                "{primary} Explained: The {secondary} Guide"
            ],
            ContentType.NEWS: [
                "Breaking: {primary} {secondary} Update",
                "{primary} News: {secondary} Developments",
                "Latest {primary}: {secondary} Announcement"
            ],
            ContentType.OPINION: [
                "Why {primary} {secondary} Matters Now",
                "The Future of {primary}: {secondary} Trends",
                "{primary} Opinion: {secondary} Analysis"
            ],
            ContentType.REFERENCE: [
                "{primary} Reference: {secondary} Documentation",
                "Complete {primary} {secondary} Guide",
                "{primary} Handbook: {secondary} Best Practices"
            ]
        }

    def enhance_content_seo(self, content: str, title: str,
                           category: str = None, questline: str = None) -> SEOEnhancedContent:
        """
        Complete SEO enhancement of content.

        Args:
            content: Raw content to enhance
            title: Current title
            category: Content category
            questline: Questline context

        Returns:
            SEOEnhancedContent with enhancements applied
        """
        # Step 1: Analyze content and extract SEO opportunities
        seo_analysis = self._analyze_content_for_seo(content, title, category, questline)

        # Step 2: Apply SEO enhancements
        enhanced_content, enhancements = self._apply_seo_enhancements(
            content, seo_analysis
        )

        # Step 3: Calculate SEO score
        seo_score = self._calculate_seo_score(enhanced_content, seo_analysis)

        return SEOEnhancedContent(
            original_content=content,
            enhanced_content=enhanced_content,
            seo_metadata=seo_analysis,
            enhancements_applied=enhancements,
            seo_score=seo_score
        )

    def _analyze_content_for_seo(self, content: str, title: str,
                               category: str = None, questline: str = None) -> SEOAnalysis:
        """Analyze content to extract SEO opportunities"""

        # Extract primary keyword
        primary_keyword = self._extract_primary_keyword(content, title, category)

        # Extract secondary keywords
        secondary_keywords = self._extract_secondary_keywords(content, primary_keyword)

        # Estimate search volume (simplified)
        search_volume = self._estimate_search_volume(primary_keyword, secondary_keywords)

        # Determine competition level
        competition_level = self._assess_competition(primary_keyword)

        # Determine SERP intent
        serp_intent = self._determine_serp_intent(content, primary_keyword)

        # Generate title suggestions
        title_suggestions = self._generate_title_suggestions(
            primary_keyword, secondary_keywords, serp_intent
        )

        # Craft meta description
        meta_description = self._craft_meta_description(
            title, primary_keyword, secondary_keywords, content
        )

        # Analyze headings structure
        headings_structure = self._analyze_headings_structure(content)

        # Suggest internal links
        internal_links = self._suggest_internal_links(content, category, questline)

        # Identify content gaps
        content_gaps = self._identify_content_gaps(content, primary_keyword, serp_intent)

        return SEOAnalysis(
            primary_keyword=primary_keyword,
            secondary_keywords=secondary_keywords,
            search_volume_estimate=search_volume,
            competition_level=competition_level,
            serp_intent=serp_intent,
            title_suggestions=title_suggestions,
            meta_description=meta_description,
            headings_structure=headings_structure,
            internal_links_suggestions=internal_links,
            content_gaps=content_gaps
        )

    def _extract_primary_keyword(self, content: str, title: str, category: str = None) -> str:
        """Extract the primary keyword from content"""

        # Start with title-based extraction
        title_words = re.findall(r'\b\w{4,}\b', title.lower())

        # Get category-specific keywords if available
        category_keywords = []
        if category and category in self.primary_keywords_db:
            category_keywords = self.primary_keywords_db[category]

        # Find the most relevant keyword from title
        content_lower = content.lower()

        # Score words by relevance
        word_scores = {}
        for word in title_words:
            score = 0
            # Bonus for being in category keywords
            if any(word in kw for kw in category_keywords):
                score += 3
            # Bonus for appearing in content
            if word in content_lower:
                score += 2
            # Bonus for being a noun (simplified check)
            if not word.endswith(('ing', 'ed', 'ly', 's')):
                score += 1

            word_scores[word] = score

        # Return highest scoring word, or first category keyword as fallback
        if word_scores:
            return max(word_scores.items(), key=lambda x: x[1])[0]

        return category_keywords[0] if category_keywords else "content"

    def _extract_secondary_keywords(self, content: str, primary_keyword: str) -> List[str]:
        """Extract secondary keywords from content"""

        # Get words from content
        words = re.findall(r'\b\w{4,}\b', content.lower())

        # Remove primary keyword and common stop words
        stop_words = {'that', 'with', 'have', 'this', 'will', 'your', 'from', 'they', 'know', 'want', 'been', 'good', 'much', 'some', 'time', 'very', 'when', 'come', 'here', 'just', 'like', 'long', 'make', 'many', 'over', 'such', 'take', 'than', 'them', 'well', 'were'}

        filtered_words = [w for w in words if w not in stop_words and w != primary_keyword]

        # Count frequency
        word_counts = Counter(filtered_words)

        # Get top 5 most frequent relevant words
        secondary_keywords = []
        for word, count in word_counts.most_common(10):
            if len(secondary_keywords) >= 5:
                break
            # Only include words that appear multiple times
            if count >= 2:
                secondary_keywords.append(word)

        return secondary_keywords

    def _estimate_search_volume(self, primary: str, secondary: List[str]) -> int:
        """Estimate search volume (simplified heuristic)"""

        # Simplified search volume estimation
        base_volume = 1000  # Base volume

        # Adjust based on keyword characteristics
        if len(primary) > 10:
            base_volume *= 0.7  # Long-tail keywords have lower volume

        if any(word in primary for word in ['how', 'what', 'why', 'tutorial']):
            base_volume *= 1.5  # Educational content has higher volume

        if len(secondary) > 3:
            base_volume *= 1.2  # Content with rich secondary keywords

        return int(base_volume)

    def _assess_competition(self, primary_keyword: str) -> str:
        """Assess competition level for keyword"""

        # Simplified competition assessment
        if len(primary_keyword.split()) > 3:
            return "low"  # Long-tail keywords have lower competition

        competitive_terms = ['best', 'top', 'review', 'guide', 'tutorial']
        if any(term in primary_keyword for term in competitive_terms):
            return "high"

        return "medium"

    def _determine_serp_intent(self, content: str, primary_keyword: str) -> SERPIntent:
        """Determine the SERP intent of the content"""

        content_lower = primary_keyword.lower() + " " + content[:500].lower()

        intent_scores = {intent: 0 for intent in SERPIntent}

        for intent, patterns in self.intent_patterns.items():
            for pattern in patterns:
                if re.search(pattern, content_lower, re.IGNORECASE):
                    intent_scores[intent] += 1

        # Return intent with highest score
        return max(intent_scores.items(), key=lambda x: x[1])[0]

    def _generate_title_suggestions(self, primary: str, secondary: List[str],
                                  intent: SERPIntent) -> List[str]:
        """Generate title suggestions based on keywords and intent"""

        suggestions = []

        # Get content type from intent
        content_type = {
            SERPIntent.INFORMATIONAL: ContentType.TUTORIAL,
            SERPIntent.COMMERCIAL: ContentType.CASE_STUDY,
            SERPIntent.TRANSACTIONAL: ContentType.REFERENCE,
            SERPIntent.NAVIGATIONAL: ContentType.REFERENCE
        }.get(intent, ContentType.EXPLANATION)

        # Get title templates for content type
        templates = self.title_templates.get(content_type, self.title_templates[ContentType.EXPLANATION])

        # Generate suggestions
        for template in templates[:3]:  # Use first 3 templates
            secondary_kw = secondary[0] if secondary else "guide"
            title = template.format(primary=primary.title(), secondary=secondary_kw.title())
            suggestions.append(title)

        return suggestions

    def _craft_meta_description(self, title: str, primary: str,
                              secondary: List[str], content: str) -> str:
        """Craft an optimized meta description"""

        # Extract first meaningful sentence
        sentences = re.split(r'[.!?]+', content)
        first_sentence = ""
        for sentence in sentences[:3]:
            sentence = sentence.strip()
            if len(sentence) > 20:
                first_sentence = sentence
                break

        # Craft meta description
        meta = f"{primary.title()}: {first_sentence[:100]}"

        if secondary:
            meta += f" Learn about {', '.join(secondary[:2])}."

        # Ensure proper length (150-160 characters)
        if len(meta) > 155:
            meta = meta[:152] + "..."

        return meta

    def _analyze_headings_structure(self, content: str) -> Dict[str, List[str]]:
        """Analyze and suggest headings structure"""

        structure = {'h1': [], 'h2': [], 'h3': [], 'missing': []}

        # Extract existing headings
        heading_pattern = r'^(#{1,3})\s+(.+)$'
        for line in content.split('\n'):
            match = re.match(heading_pattern, line)
            if match:
                level = len(match.group(1))
                text = match.group(2).strip()
                heading_tag = f'h{level}'
                if heading_tag in structure:
                    structure[heading_tag].append(text)

        # Suggest missing structure
        if not structure['h2']:
            structure['missing'].append("Add H2 sections to break up content")
        if len(structure['h2']) > 0 and not structure['h3']:
            structure['missing'].append("Consider H3 subheadings for deeper structure")

        return structure

    def _suggest_internal_links(self, content: str, category: str = None,
                             questline: str = None) -> List[str]:
        """Suggest internal linking opportunities"""

        suggestions = []

        # Basic internal linking suggestions based on content
        if category == 'trading' and 'strategy' in content.lower():
            suggestions.append("Link to: /trading-strategies/ (Trading Strategies section)")

        if 'agent' in content.lower():
            suggestions.append("Link to: /swarm-agents/ (Agent documentation)")

        if questline == 'memory_nexus':
            suggestions.append("Link to: /memory-nexus/ (Related Memory Nexus content)")

        # Generic suggestions
        if not suggestions:
            suggestions.append("Consider linking to related Digital Dreamscape episodes")
            suggestions.append("Link to relevant documentation or tools mentioned")

        return suggestions

    def _identify_content_gaps(self, content: str, primary_keyword: str,
                             intent: SERPIntent) -> List[str]:
        """Identify content gaps that could improve SEO"""

        gaps = []

        # Check for missing elements based on intent
        if intent == SERPIntent.INFORMATIONAL:
            if 'how to' not in content.lower() and 'tutorial' not in primary_keyword:
                gaps.append("Consider adding step-by-step instructions")
            if not re.search(r'\d+\.', content):  # Numbered steps
                gaps.append("Add numbered steps or clear process flow")

        if intent == SERPIntent.COMMERCIAL:
            if not any(word in content.lower() for word in ['vs', 'versus', 'comparison', 'alternative']):
                gaps.append("Add comparison or alternative options")
            if not re.search(r'\d+\s*(?:pros?|cons?|advantages?|disadvantages?)', content, re.IGNORECASE):
                gaps.append("Include pros/cons analysis")

        # General gaps
        if len(content.split()) < 500:
            gaps.append("Content may be too short for comprehensive coverage")

        if not re.search(r'https?://[^\s]+', content):
            gaps.append("Consider adding references or external links")

        return gaps

    def _apply_seo_enhancements(self, content: str, seo_analysis: SEOAnalysis) -> Tuple[str, List[str]]:
        """Apply SEO enhancements to content"""

        enhanced_content = content
        enhancements = []

        # Add primary keyword to title if not present
        if seo_analysis.primary_keyword not in enhanced_content.lower()[:200]:
            # This would typically be done at the title level, not content level
            enhancements.append(f"Consider incorporating primary keyword '{seo_analysis.primary_keyword}' in title")

        # Ensure proper heading structure
        if not re.search(r'^##\s+', enhanced_content, re.MULTILINE):
            enhanced_content = "## Introduction\n\n" + enhanced_content
            enhancements.append("Added H2 introduction heading")

        # Add meta description comment (for WordPress)
        if seo_analysis.meta_description:
            meta_comment = f"<!-- SEO Meta Description: {seo_analysis.meta_description} -->"
            enhanced_content = meta_comment + "\n\n" + enhanced_content
            enhancements.append("Added meta description for SEO")

        # Add internal linking suggestions as comments
        if seo_analysis.internal_links_suggestions:
            links_comment = "<!-- Internal Linking Suggestions:\n"
            for suggestion in seo_analysis.internal_links_suggestions:
                links_comment += f"  - {suggestion}\n"
            links_comment += "-->"
            enhanced_content += "\n\n" + links_comment
            enhancements.append("Added internal linking suggestions")

        # Add keyword density note
        keyword_density = content.lower().count(seo_analysis.primary_keyword) / len(content.split()) * 100
        if keyword_density < 0.5:
            enhancements.append(f"Consider increasing primary keyword '{seo_analysis.primary_keyword}' usage (currently {keyword_density:.2f}%)")

        return enhanced_content, enhancements

    def _calculate_seo_score(self, content: str, seo_analysis: SEOAnalysis) -> float:
        """Calculate overall SEO score"""

        score = 0.0

        # Title optimization (20%)
        if seo_analysis.primary_keyword.lower() in seo_analysis.title_suggestions[0].lower():
            score += 0.2

        # Keyword usage (20%)
        keyword_density = content.lower().count(seo_analysis.primary_keyword) / max(1, len(content.split())) * 100
        if 0.5 <= keyword_density <= 2.0:
            score += 0.2

        # Content structure (20%)
        if seo_analysis.headings_structure['h2']:
            score += 0.2

        # Internal linking (15%)
        if seo_analysis.internal_links_suggestions:
            score += 0.15

        # Meta description (15%)
        if seo_analysis.meta_description and 120 <= len(seo_analysis.meta_description) <= 160:
            score += 0.15

        # Content length (10%)
        if len(content.split()) >= 300:
            score += 0.1

        return min(1.0, score)

    def validate_seo_quality(self, content: str, seo_analysis: SEOAnalysis) -> Dict[str, Any]:
        """Validate SEO implementation quality"""

        validation = {
            'seo_score': seo_analysis.search_volume_estimate,  # Simplified
            'issues': [],
            'recommendations': [],
            'passed_checks': []
        }

        # Check title
        if len(seo_analysis.title_suggestions[0]) > 60:
            validation['issues'].append("Title too long for SERP display")
        else:
            validation['passed_checks'].append("Title length optimal")

        # Check meta description
        if not (120 <= len(seo_analysis.meta_description) <= 160):
            validation['issues'].append("Meta description length not optimal")
        else:
            validation['passed_checks'].append("Meta description length optimal")

        # Check keyword usage
        keyword_count = content.lower().count(seo_analysis.primary_keyword)
        if keyword_count == 0:
            validation['issues'].append(f"Primary keyword '{seo_analysis.primary_keyword}' not found in content")
        elif keyword_count > len(content.split()) * 0.03:  # Over 3%
            validation['issues'].append("Keyword density too high")
        else:
            validation['passed_checks'].append("Keyword usage optimal")

        # Check headings
        h2_count = len(seo_analysis.headings_structure.get('h2', []))
        if h2_count == 0:
            validation['issues'].append("No H2 headings found")
        else:
            validation['passed_checks'].append(f"Good heading structure ({h2_count} H2 tags)")

        return validation