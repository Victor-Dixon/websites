"""
Victor Voice Processor - Complete Voice Transformation System
===========================================================

Applies Victor's authentic voice patterns to content with proof-first mentality.
"""

import re
from typing import Dict, List, Tuple, Optional, Any
from dataclasses import dataclass
from enum import Enum
import logging
import warnings

logger = logging.getLogger(__name__)

# DEPRECATED: This module is deprecated in Phase 4
# Victor voice processing is now integrated into consolidated_quality_assessment
warnings.warn(
    "DEPRECATED: victor_voice_processor is deprecated. "
    "Victor voice processing is now integrated into consolidated_quality_assessment.",
    DeprecationWarning,
    stacklevel=2
)

class VoiceIntensity(Enum):
    """Voice transformation intensity levels"""
    LIGHT = 0.3    # Subtle adjustments
    MEDIUM = 0.6   # Balanced transformation
    STRONG = 0.8   # Significant voice application
    MAXIMUM = 1.0  # Full Victor immersion

class ContentCategory(Enum):
    """Content categories for voice adaptation"""
    TECHNICAL = "technical"
    STRATEGIC = "strategic"
    OPERATIONAL = "operational"
    NARRATIVE = "narrative"
    LEARNING = "learning"
    REFLECTION = "reflection"

@dataclass
class VoiceTransformationResult:
    """Result of voice transformation"""
    original_content: str
    transformed_content: str
    transformations_applied: List[str]
    voice_confidence_score: float
    proof_elements_found: int

class VictorVoiceProcessor:
    """
    Complete Victor voice processing system.

    Victor's voice characteristics:
    - Plainspoken, no corporate fluff
    - Proof-first mentality (links, logs, diffs, metrics)
    - Commander posture (decisive, builder-focused)
    - Short lines, momentum, tasteful ellipses
    - Cadence that drives action
    """

    def __init__(self):
        self.victor_patterns = {
            # Core voice transformations (always apply)
            'signature_phrases': {
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
                'Sort of': 'kinda'
            },

            # Proof-first language patterns
            'proof_emphasis': {
                'shows': 'proves',
                'indicates': 'shows in the logs',
                'suggests': 'the data shows',
                'appears': 'the metrics show',
                'seems': 'the traces show',
                'looks like': 'the diffs show'
            },

            # Commander posture (decisive, builder)
            'commander_posture': {
                'we should': "we're doing",
                'we could': "we're building",
                'maybe': 'definitely',
                'perhaps': 'absolutely',
                'possibly': 'actually',
                'I hope': 'I know',
                'I wish': 'we built'
            },

            # Cadence and rhythm
            'cadence_markers': [
                '...', 'right?', 'makes sense?', 'you see?', 'exactly.',
                'there it is.', 'boom.', 'done.', 'shipped.'
            ],

            # Remove corporate fluff
            'corporate_fluff': [
                'leverage', 'synergy', 'paradigm', 'ecosystem', 'stakeholder',
                'bandwidth', 'circle back', 'touch base', 'deep dive',
                'move the needle', 'thought leadership', 'best practices'
            ]
        }

        # Category-specific adaptations
        self.category_adaptations = {
            ContentCategory.TECHNICAL: {
                'intensity': VoiceIntensity.STRONG,
                'focus': 'precision',
                'add_patterns': {
                    'the code': 'the actual code',
                    'the system': 'the running system',
                    'it works': 'it runs clean'
                }
            },
            ContentCategory.STRATEGIC: {
                'intensity': VoiceIntensity.MEDIUM,
                'focus': 'decisiveness',
                'add_patterns': {
                    'strategy': 'battle plan',
                    'plan': 'execution roadmap',
                    'decision': 'command decision'
                }
            },
            ContentCategory.NARRATIVE: {
                'intensity': VoiceIntensity.MEDIUM,
                'focus': 'engagement',
                'add_patterns': {
                    'happened': 'went down',
                    'occurred': 'hit us',
                    'experienced': 'lived through'
                }
            }
        }

        # Proof element patterns
        self.proof_patterns = [
            r'https?://[^\s]+',  # URLs
            r'```\w*\n.*?\n```',  # Code blocks
            r'`[^`]+`',  # Inline code
            r'\b\d{4}-\d{2}-\d{2}\b',  # Dates
            r'\b\d+\.\d+%?\b',  # Numbers/percentages
            r'\b[A-Z]{2,}\b',  # Acronyms
            r'\bcommit\s+[a-f0-9]+\b',  # Git commits
            r'\bdiff\b|\blog\b|\btrace\b',  # Technical terms
        ]

    def apply_victor_voice(self, content: str,
                          category: Optional[ContentCategory] = None,
                          intensity: VoiceIntensity = VoiceIntensity.MEDIUM) -> VoiceTransformationResult:
        """
        Apply Victor's voice transformation to content.

        Args:
            content: Raw content to transform
            category: Content category for adaptation
            intensity: Transformation intensity level

        Returns:
            VoiceTransformationResult with transformed content
        """
        original_content = content
        transformations_applied = []

        # Get category-specific settings
        category_settings = self.category_adaptations.get(category, {})

        # Clean and prepare content
        content = self._clean_content(content)

        # Apply core voice transformations
        content, core_transforms = self._apply_core_transformations(content, intensity)
        transformations_applied.extend(core_transforms)

        # Apply category-specific adaptations
        if category_settings:
            content, category_transforms = self._apply_category_adaptations(
                content, category_settings, intensity
            )
            transformations_applied.extend(category_transforms)

        # Apply cadence and rhythm
        content, cadence_transforms = self._apply_cadence_transforms(content, intensity)
        transformations_applied.extend(cadence_transforms)

        # Remove corporate fluff
        content, fluff_transforms = self._remove_corporate_fluff(content, intensity)
        transformations_applied.extend(fluff_transforms)

        # Fix punctuation and formatting
        content = self._fix_formatting(content)

        # Calculate voice confidence and proof elements
        voice_confidence = self._calculate_voice_confidence(content, transformations_applied)
        proof_elements = self._count_proof_elements(content)

        return VoiceTransformationResult(
            original_content=original_content,
            transformed_content=content,
            transformations_applied=transformations_applied,
            voice_confidence_score=voice_confidence,
            proof_elements_found=proof_elements
        )

    def _clean_content(self, content: str) -> str:
        """Clean and prepare content for transformation"""
        # Remove markdown headers for processing (will be restored later)
        content = re.sub(r'^#+\s*', '', content, flags=re.MULTILINE)

        # Normalize ellipses
        content = re.sub(r'\.{3,}', '...', content)

        # Fix spacing
        content = re.sub(r'\s+', ' ', content)
        content = re.sub(r'\s*([.!?,])', r'\1', content)
        content = re.sub(r'([.!?])\s*([A-Za-z])', r'\1 \2', content)

        return content.strip()

    def _apply_core_transformations(self, content: str,
                                   intensity: VoiceIntensity) -> Tuple[str, List[str]]:
        """Apply core Victor voice transformations"""
        transformations = []
        result = content

        # Apply signature phrases
        for formal, victor in self.victor_patterns['signature_phrases'].items():
            if intensity.value >= 0.3:  # Apply even at light intensity
                pattern = r'\b' + re.escape(formal) + r'\b'
                if re.search(pattern, result, re.IGNORECASE):
                    result = re.sub(pattern, victor, result, flags=re.IGNORECASE)
                    transformations.append(f"'{formal}' → '{victor}'")

        # Apply proof-first language
        if intensity.value >= 0.5:
            for vague, proof_based in self.victor_patterns['proof_emphasis'].items():
                pattern = r'\b' + re.escape(vague) + r'\b'
                if re.search(pattern, result, re.IGNORECASE):
                    result = re.sub(pattern, proof_based, result, flags=re.IGNORECASE)
                    transformations.append(f"'{vague}' → '{proof_based}'")

        # Apply commander posture
        if intensity.value >= 0.6:
            for tentative, decisive in self.victor_patterns['commander_posture'].items():
                pattern = r'\b' + re.escape(tentative) + r'\b'
                if re.search(pattern, result, re.IGNORECASE):
                    result = re.sub(pattern, decisive, result, flags=re.IGNORECASE)
                    transformations.append(f"'{tentative}' → '{decisive}'")

        return result, transformations

    def _apply_category_adaptations(self, content: str,
                                   category_settings: Dict,
                                   intensity: VoiceIntensity) -> Tuple[str, List[str]]:
        """Apply category-specific voice adaptations"""
        transformations = []
        result = content

        add_patterns = category_settings.get('add_patterns', {})
        focus = category_settings.get('focus', 'general')

        # Apply category-specific patterns
        for original, adapted in add_patterns.items():
            if intensity.value >= 0.7:  # Only at higher intensity
                pattern = r'\b' + re.escape(original) + r'\b'
                if re.search(pattern, result, re.IGNORECASE):
                    result = re.sub(pattern, adapted, result, flags=re.IGNORECASE)
                    transformations.append(f"Category adaptation: '{original}' → '{adapted}'")

        # Add focus-specific flourishes
        if focus == 'precision' and intensity.value >= 0.8:
            # Add technical precision markers
            if not any(word in result.lower() for word in ['exactly', 'precisely', 'specifically']):
                result += ' Specifically.'
                transformations.append("Added technical precision marker")

        elif focus == 'decisiveness' and intensity.value >= 0.8:
            # Add commander posture reinforcement
            if not any(word in result.lower() for word in ['definitely', 'absolutely', 'actually']):
                result += ' We execute.'
                transformations.append("Added commander reinforcement")

        return result, transformations

    def _apply_cadence_transforms(self, content: str,
                                intensity: VoiceIntensity) -> Tuple[str, List[str]]:
        """Apply cadence and rhythm transformations"""
        transformations = []
        result = content

        if intensity.value < 0.5:
            return result, transformations

        # Add cadence markers strategically
        sentences = re.split(r'[.!?]+', result)
        cadence_markers = self.victor_patterns['cadence_markers']

        # Add markers to longer sentences for rhythm
        modified_sentences = []
        for sentence in sentences:
            sentence = sentence.strip()
            if not sentence:
                continue

            # Add cadence to substantial sentences
            if len(sentence.split()) > 10 and intensity.value >= 0.7:
                marker = cadence_markers[len(sentence) % len(cadence_markers)]
                sentence += f" {marker}"
                transformations.append(f"Added cadence: {marker}")

            modified_sentences.append(sentence)

        result = '. '.join(modified_sentences)

        # Add ellipses for thoughtful pauses in reflection-heavy content
        if 'learned' in result.lower() or 'realized' in result.lower():
            if intensity.value >= 0.6 and not result.endswith('...'):
                result += '...'
                transformations.append("Added thoughtful pause")

        return result, transformations

    def _remove_corporate_fluff(self, content: str,
                               intensity: VoiceIntensity) -> Tuple[str, List[str]]:
        """Remove corporate fluff and jargon"""
        transformations = []
        result = content

        if intensity.value < 0.7:
            return result, transformations

        # Remove corporate fluff words
        for fluff in self.victor_patterns['corporate_fluff']:
            pattern = r'\b' + re.escape(fluff) + r'\b'
            if re.search(pattern, result, re.IGNORECASE):
                # Replace with simpler alternatives where possible
                if fluff == 'leverage':
                    result = re.sub(pattern, 'use', result, flags=re.IGNORECASE)
                    transformations.append("Removed 'leverage' → 'use'")
                elif fluff == 'synergy':
                    result = re.sub(pattern, 'working together', result, flags=re.IGNORECASE)
                    transformations.append("Removed 'synergy' → 'working together'")
                else:
                    # Just remove the word
                    result = re.sub(pattern, '', result, flags=re.IGNORECASE)
                    transformations.append(f"Removed corporate fluff: '{fluff}'")

        # Clean up double spaces from removals
        result = re.sub(r'\s+', ' ', result)

        return result.strip(), transformations

    def _fix_formatting(self, content: str) -> str:
        """Fix punctuation and formatting"""
        # Ensure proper sentence endings
        if content and not content.endswith(('.', '!', '?', '...')):
            content += '.'

        # Fix spacing around punctuation
        content = re.sub(r'\s+([.!?,])', r'\1', content)
        content = re.sub(r'([.!?])\s*([A-Z])', r'\1 \2', content)

        # Fix multiple spaces
        content = re.sub(r'\s{2,}', ' ', content)

        return content.strip()

    def _calculate_voice_confidence(self, content: str, transformations: List[str]) -> float:
        """Calculate how confident we are in Victor voice application"""
        score = 0.0

        # Base score from transformations applied
        score += min(0.4, len(transformations) * 0.05)

        # Bonus for signature phrases found
        signature_count = sum(1 for t in transformations if 'signature' in t.lower() or any(
            phrase in t for phrase in ['idk', 'tbh', 'kinda', 'lowkey']
        ))
        score += min(0.3, signature_count * 0.1)

        # Bonus for proof elements
        proof_count = self._count_proof_elements(content)
        score += min(0.3, proof_count * 0.05)

        return min(1.0, score)

    def _count_proof_elements(self, content: str) -> int:
        """Count proof elements in content"""
        count = 0
        for pattern in self.proof_patterns:
            matches = re.findall(pattern, content, re.IGNORECASE)
            count += len(matches)
        return count

    def validate_voice_quality(self, content: str) -> Dict[str, Any]:
        """Validate Victor voice quality metrics"""
        validation = {
            'victor_voice_score': 0.0,
            'proof_elements': 0,
            'issues': [],
            'recommendations': []
        }

        # Check for Victor signature phrases
        victor_indicators = ['idk', 'tbh', 'kinda', 'lowkey', 'tryna', 'wanna', 'gon', 'js']
        victor_count = sum(1 for indicator in victor_indicators if indicator in content.lower())

        # Check for proof elements
        proof_count = self._count_proof_elements(content)

        # Calculate voice score
        voice_score = min(1.0, (victor_count * 0.1) + (proof_count * 0.05))
        validation['victor_voice_score'] = voice_score
        validation['proof_elements'] = proof_count

        # Generate issues and recommendations
        if victor_count < 3:
            validation['issues'].append("Low Victor voice presence")
            validation['recommendations'].append("Add more signature phrases (idk, tbh, kinda)")

        if proof_count < 2:
            validation['issues'].append("Insufficient proof elements")
            validation['recommendations'].append("Add URLs, code snippets, metrics, or logs")

        # Check for corporate fluff
        fluff_found = [fluff for fluff in self.victor_patterns['corporate_fluff']
                      if fluff in content.lower()]
        if fluff_found:
            validation['issues'].append(f"Corporate fluff detected: {fluff_found}")
            validation['recommendations'].append("Replace corporate jargon with plain language")

        return validation