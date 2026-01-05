"""
Template Engine - Block Design System
=====================================

Renders content into WordPress-friendly HTML with category/questline/mission-specific styling.
"""

import json
from pathlib import Path
from typing import Dict, List, Any, Optional, Tuple
from dataclasses import dataclass
from enum import Enum
import logging
import warnings

logger = logging.getLogger(__name__)

# DEPRECATED: This module is deprecated in Phase 4
# Use consolidated_template_service.ConsolidatedTemplateService instead
warnings.warn(
    "DEPRECATED: template_engine is deprecated. "
    "Use consolidated_template_service.ConsolidatedTemplateService instead.",
    DeprecationWarning,
    stacklevel=2
)

class BlockType(Enum):
    """Available block types in the design system"""
    HERO = "hero"
    HERO_PRICE_ACTION = "hero_price_action"
    HERO_LORE = "hero_lore"
    TOC = "toc"
    SECTION = "section"
    CALLOUT = "callout"
    CODE = "code"
    QUOTE = "quote"
    CTA = "cta"
    FOOTER = "footer"

    # Specialized blocks
    THESIS = "thesis"
    SETUP_RULES = "setup_rules"
    SCREENSHOTS = "screenshots"
    RISK_BOX = "risk_box"
    RESULTS_TABLE = "results_table"
    PROBLEM = "problem"
    CONSTRAINTS = "constraints"
    APPROACH = "approach"
    DIFFS = "diffs"
    VALIDATION_RITUAL = "validation_ritual"
    NEXT_STEPS = "next_steps"

    # Lore blocks
    SCENE = "scene"
    CONFLICT = "conflict"
    REVELATION = "revelation"
    ARTIFACT_DROP = "artifact_drop"
    OATH_CTA = "oath_cta"

class CalloutVariant(Enum):
    """Callout block variants"""
    WARNING = "warning"
    WIN = "win"
    LOSS = "loss"
    RULE = "rule"
    PROOF = "proof"

class CodeVariant(Enum):
    """Code block variants"""
    TERMINAL = "terminal"
    DIFF = "diff"
    SNIPPET = "snippet"

@dataclass
class BlockDefinition:
    """Definition of a content block"""
    block_type: BlockType
    variant: Optional[str] = None
    classes: List[str] = None
    attributes: Dict[str, str] = None
    content: str = ""

    def __post_init__(self):
        if self.classes is None:
            self.classes = []
        if self.attributes is None:
            self.attributes = {}

@dataclass
class TemplateDefinition:
    """Complete template with blocks and styling"""
    id: str
    name: str
    description: str
    blocks: List[BlockType]
    styling_tokens: List[str]
    category_affinity: List[str] = None  # Preferred categories
    questline_affinity: List[str] = None  # Preferred questlines
    mission_affinity: List[str] = None  # Preferred mission types

class TemplateEngine:
    """Renders content using block design system"""

    def __init__(self, templates_path: Optional[str] = None):
        self.templates_path = Path(templates_path or "templates")
        self.templates: Dict[str, TemplateDefinition] = {}
        self.styling_tokens: Dict[str, Dict[str, Any]] = {}

        self.load_templates()
        self.load_styling_tokens()

    def load_templates(self):
        """Load template definitions"""
        # Default templates - in production these would be loaded from files
        self.templates = {
            'base_default': TemplateDefinition(
                id='base_default',
                name='Base Default',
                description='Standard blog post template',
                blocks=[
                    BlockType.HERO,
                    BlockType.TOC,
                    BlockType.SECTION,
                    BlockType.CALLOUT,
                    BlockType.CODE,
                    BlockType.QUOTE,
                    BlockType.CTA,
                    BlockType.FOOTER
                ],
                styling_tokens=[
                    'type_scale',
                    'spacing_rhythm',
                    'content_width',
                    'code_theme'
                ]
            ),

            'trading_signal_report': TemplateDefinition(
                id='trading_signal_report',
                name='Trading Signal Report',
                description='Specialized template for trading signals and analysis',
                blocks=[
                    BlockType.HERO_PRICE_ACTION,
                    BlockType.THESIS,
                    BlockType.SETUP_RULES,
                    BlockType.SCREENSHOTS,
                    BlockType.RISK_BOX,
                    BlockType.RESULTS_TABLE,
                    BlockType.CTA
                ],
                styling_tokens=[
                    'chart_caption',
                    'risk_callout',
                    'rule_cards'
                ],
                category_affinity=['trading']
            ),

            'swarm_engineering_log': TemplateDefinition(
                id='swarm_engineering_log',
                name='Swarm Engineering Log',
                description='Template for multi-agent system development logs',
                blocks=[
                    BlockType.HERO,
                    BlockType.PROBLEM,
                    BlockType.CONSTRAINTS,
                    BlockType.APPROACH,
                    BlockType.DIFFS,
                    BlockType.VALIDATION_RITUAL,
                    BlockType.NEXT_STEPS,
                    BlockType.CTA
                ],
                styling_tokens=[
                    'diff_callout',
                    'checklist_blocks'
                ],
                category_affinity=['swarm'],
                questline_affinity=['agent_cell_phone', 'memory_nexus']
            ),

            'dreamscape_lore_episode': TemplateDefinition(
                id='dreamscape_lore_episode',
                name='Dreamscape Lore Episode',
                description='Cinematic template for narrative lore episodes',
                blocks=[
                    BlockType.HERO_LORE,
                    BlockType.SCENE,
                    BlockType.CONFLICT,
                    BlockType.REVELATION,
                    BlockType.ARTIFACT_DROP,
                    BlockType.OATH_CTA
                ],
                styling_tokens=[
                    'lore_dividers',
                    'cinematic_quotes'
                ],
                category_affinity=['dreamscape_lore']
            )
        }

    def load_styling_tokens(self):
        """Load styling token definitions"""
        self.styling_tokens = {
            # Typography
            'type_scale': {
                'h1': 'text-4xl font-bold leading-tight',
                'h2': 'text-3xl font-semibold leading-tight mt-8 mb-4',
                'h3': 'text-2xl font-medium leading-tight mt-6 mb-3',
                'body': 'text-lg leading-relaxed',
                'caption': 'text-sm text-gray-600'
            },

            # Spacing
            'spacing_rhythm': {
                'section_margin': 'mb-8',
                'paragraph_margin': 'mb-4',
                'block_padding': 'p-6'
            },

            # Layout
            'content_width': 'max-w-4xl mx-auto',
            'sidebar_width': 'w-80',

            # Code themes
            'code_theme': 'tomorrow-night-eighties',

            # Specialized tokens
            'chart_caption': 'text-center text-sm text-gray-500 mt-2',
            'risk_callout': 'bg-red-50 border-l-4 border-red-400 p-4 rounded',
            'rule_cards': 'bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400',
            'diff_callout': 'bg-gray-900 text-green-400 p-4 rounded font-mono text-sm',
            'checklist_blocks': 'space-y-2',
            'lore_dividers': 'border-t-2 border-purple-200 my-8',
            'cinematic_quotes': 'text-xl italic text-center my-8 text-purple-800'
        }

    def select_template(self, category: Optional[str] = None,
                       questline: Optional[str] = None,
                       mission_type: Optional[str] = None) -> str:
        """Select appropriate template based on content attributes"""

        # Score templates based on affinity
        template_scores = {}

        for template_id, template in self.templates.items():
            score = 0

            # Category affinity
            if category and template.category_affinity and category in template.category_affinity:
                score += 3

            # Questline affinity
            if questline and template.questline_affinity and questline in template.questline_affinity:
                score += 2

            # Mission type affinity
            if mission_type and template.mission_affinity and mission_type in template.mission_affinity:
                score += 1

            template_scores[template_id] = score

        # Return highest scoring template, or base_default if no matches
        best_template = max(template_scores.items(), key=lambda x: x[1])

        if best_template[1] > 0:
            return best_template[0]
        else:
            return 'base_default'

    def render_content(self, content_data: Dict[str, Any],
                      template_id: str) -> str:
        """
        Render content using specified template

        Args:
            content_data: Dict with content sections and metadata
            template_id: Template to use for rendering

        Returns:
            Complete HTML string
        """
        if template_id not in self.templates:
            logger.warning(f"Template {template_id} not found, using base_default")
            template_id = 'base_default'

        template = self.templates[template_id]

        # Build HTML structure
        html_parts = []

        # Add CSS framework (Tailwind-like classes)
        html_parts.append(self._get_html_head())

        # Render each block in template order
        for block_type in template.blocks:
            block_html = self._render_block(block_type, content_data, template.styling_tokens)
            if block_html:
                html_parts.append(block_html)

        # Close HTML
        html_parts.append(self._get_html_footer())

        return '\n'.join(html_parts)

    def _render_block(self, block_type: BlockType, content_data: Dict[str, Any],
                     styling_tokens: List[str]) -> Optional[str]:
        """Render individual block"""

        # Get content for this block type
        block_content = content_data.get(block_type.value, '')

        if not block_content and block_type not in [BlockType.TOC, BlockType.FOOTER]:
            return None  # Skip empty blocks (except TOC/footer)

        # Generate block HTML based on type
        if block_type == BlockType.HERO:
            return self._render_hero_block(block_content, content_data)
        elif block_type == BlockType.HERO_PRICE_ACTION:
            return self._render_hero_price_action_block(block_content, content_data)
        elif block_type == BlockType.HERO_LORE:
            return self._render_hero_lore_block(block_content, content_data)
        elif block_type == BlockType.TOC:
            return self._render_toc_block(content_data)
        elif block_type == BlockType.SECTION:
            return self._render_section_block(block_content, styling_tokens)
        elif block_type == BlockType.CALLOUT:
            return self._render_callout_block(block_content, content_data.get('callout_variant', 'info'))
        elif block_type == BlockType.CODE:
            return self._render_code_block(block_content, content_data.get('code_variant', 'snippet'))
        elif block_type == BlockType.QUOTE:
            return self._render_quote_block(block_content)
        elif block_type == BlockType.CTA:
            return self._render_cta_block(block_content, content_data.get('cta_type', 'subscribe'))
        elif block_type == BlockType.FOOTER:
            return self._render_footer_block(content_data)
        else:
            # Specialized blocks
            return self._render_specialized_block(block_type, block_content, content_data)

    def _render_hero_block(self, content: str, content_data: Dict[str, Any]) -> str:
        """Render standard hero block"""
        title = content_data.get('title', 'Untitled')
        subtitle = content or content_data.get('excerpt', '')

        return f'''
        <div class="hero-block bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16 px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl font-bold leading-tight mb-6">{title}</h1>
                {f'<p class="text-xl opacity-90 leading-relaxed">{subtitle}</p>' if subtitle else ''}
            </div>
        </div>
        '''

    def _render_hero_price_action_block(self, content: str, content_data: Dict[str, Any]) -> str:
        """Render trading-specific hero with price action"""
        title = content_data.get('title', 'Untitled')
        current_price = content_data.get('current_price', 'N/A')
        signal = content_data.get('signal', 'HOLD')

        signal_class = {
            'BUY': 'text-green-400',
            'SELL': 'text-red-400',
            'HOLD': 'text-yellow-400'
        }.get(signal, 'text-gray-400')

        return f'''
        <div class="hero-trading bg-gray-900 text-white py-16 px-8">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h1 class="text-4xl font-bold leading-tight mb-4">{title}</h1>
                        <div class="text-6xl font-mono font-bold {signal_class}">{signal}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl mb-2">Current Price</div>
                        <div class="text-4xl font-mono font-bold">{current_price}</div>
                        {f'<div class="mt-4 text-lg">{content}</div>' if content else ''}
                    </div>
                </div>
            </div>
        </div>
        '''

    def _render_hero_lore_block(self, content: str, content_data: Dict[str, Any]) -> str:
        """Render cinematic lore hero"""
        title = content_data.get('title', 'Untitled')
        episode_num = content_data.get('episode_number', '')

        return f'''
        <div class="hero-lore bg-gradient-to-b from-purple-900 via-indigo-900 to-black text-white py-24 px-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
            <div class="max-w-4xl mx-auto text-center relative z-10">
                {f'<div class="text-sm uppercase tracking-wider mb-4 opacity-75">Episode {episode_num}</div>' if episode_num else ''}
                <h1 class="text-6xl font-bold leading-tight mb-8 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">{title}</h1>
                {f'<p class="text-xl leading-relaxed opacity-90 max-w-2xl mx-auto">{content}</p>' if content else ''}
            </div>
        </div>
        '''

    def _render_toc_block(self, content_data: Dict[str, Any]) -> str:
        """Render table of contents"""
        sections = content_data.get('sections', [])
        if not sections:
            return ''

        toc_items = []
        for i, section in enumerate(sections, 1):
            toc_items.append(f'<li><a href="#section-{i}" class="hover:text-blue-600 transition-colors">{section}</a></li>')

        return f'''
        <div class="toc-block bg-gray-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-semibold mb-4">Table of Contents</h3>
            <ul class="space-y-2">
                {''.join(toc_items)}
            </ul>
        </div>
        '''

    def _render_section_block(self, content: str, styling_tokens: List[str]) -> str:
        """Render content section"""
        if not content:
            return ''

        # Apply styling tokens
        classes = ['section-block']
        if 'spacing_rhythm' in styling_tokens:
            classes.append('mb-8')

        return f'''
        <div class="{' '.join(classes)}">
            <div class="prose prose-lg max-w-none">
                {content}
            </div>
        </div>
        '''

    def _render_callout_block(self, content: str, variant: str) -> str:
        """Render callout block with variant styling"""
        if not content:
            return ''

        variant_styles = {
            'warning': 'bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800',
            'win': 'bg-green-50 border-l-4 border-green-400 text-green-800',
            'loss': 'bg-red-50 border-l-4 border-red-400 text-red-800',
            'rule': 'bg-blue-50 border-l-4 border-blue-400 text-blue-800',
            'proof': 'bg-purple-50 border-l-4 border-purple-400 text-purple-800',
            'info': 'bg-gray-50 border-l-4 border-gray-400 text-gray-800'
        }

        style_class = variant_styles.get(variant, variant_styles['info'])

        return f'''
        <div class="callout-block {style_class} p-4 rounded-r-lg mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    {content}
                </div>
            </div>
        </div>
        '''

    def _render_code_block(self, content: str, variant: str) -> str:
        """Render code block with variant styling"""
        if not content:
            return ''

        variant_classes = {
            'terminal': 'bg-gray-900 text-green-400',
            'diff': 'bg-gray-900 text-green-400 font-mono',
            'snippet': 'bg-gray-100 text-gray-900'
        }

        code_class = variant_classes.get(variant, variant_classes['snippet'])

        # Basic syntax highlighting placeholder
        highlighted_code = content.replace('<', '&lt;').replace('>', '&gt;')

        return f'''
        <div class="code-block bg-gray-900 rounded-lg p-4 mb-6 overflow-x-auto">
            <pre class="text-sm"><code class="{code_class}">{highlighted_code}</code></pre>
        </div>
        '''

    def _render_quote_block(self, content: str) -> str:
        """Render quote block"""
        if not content:
            return ''

        return f'''
        <blockquote class="quote-block border-l-4 border-blue-400 pl-6 py-4 my-8 bg-blue-50 italic text-lg">
            "{content}"
        </blockquote>
        '''

    def _render_cta_block(self, content: str, cta_type: str) -> str:
        """Render call-to-action block"""
        button_text = content or "Learn More"
        button_class = "bg-blue-600 hover:bg-blue-700"

        if cta_type == 'discord':
            button_class = "bg-indigo-600 hover:bg-indigo-700"
            button_text = content or "Join Discord"
        elif cta_type == 'product':
            button_class = "bg-green-600 hover:bg-green-700"
            button_text = content or "Get Started"
        elif cta_type == 'follow_up':
            button_class = "bg-purple-600 hover:bg-purple-700"
            button_text = content or "Continue Reading"

        return f'''
        <div class="cta-block bg-gradient-to-r from-blue-600 to-purple-600 text-white py-12 px-8 rounded-lg my-8 text-center">
            <h3 class="text-2xl font-bold mb-4">Ready to dive deeper?</h3>
            <button class="inline-block {button_class} text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-200">
                {button_text}
            </button>
        </div>
        '''

    def _render_footer_block(self, content_data: Dict[str, Any]) -> str:
        """Render footer block"""
        author = content_data.get('author', 'Digital Dreamscape')
        tags = content_data.get('tags', [])

        tag_html = ''
        if tags:
            tag_items = [f'<span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm mr-2 mb-2">#{tag}</span>' for tag in tags]
            tag_html = f'<div class="mb-4"><div class="flex flex-wrap">{"".join(tag_items)}</div></div>'

        return f'''
        <footer class="footer-block border-t pt-8 mt-12">
            {tag_html}
            <div class="text-center text-gray-600">
                <p>Published by {author} • Part of the Digital Dreamscape series</p>
            </div>
        </footer>
        '''

    def _render_specialized_block(self, block_type: BlockType, content: str,
                                 content_data: Dict[str, Any]) -> Optional[str]:
        """Render specialized blocks for specific templates"""

        if block_type == BlockType.PROBLEM:
            return f'''
            <div class="problem-block bg-red-50 border-l-4 border-red-400 p-6 mb-6">
                <h3 class="text-lg font-semibold text-red-800 mb-3">The Problem</h3>
                <div class="text-red-700">{content}</div>
            </div>
            '''
        elif block_type == BlockType.APPROACH:
            return f'''
            <div class="approach-block bg-blue-50 border-l-4 border-blue-400 p-6 mb-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">Our Approach</h3>
                <div class="text-blue-700">{content}</div>
            </div>
            '''
        elif block_type == BlockType.VALIDATION_RITUAL:
            return f'''
            <div class="validation-block bg-green-50 border-l-4 border-green-400 p-6 mb-6">
                <h3 class="text-lg font-semibold text-green-800 mb-3">Validation Ritual</h3>
                <div class="text-green-700">{content}</div>
            </div>
            '''
        elif block_type == BlockType.REVELATION:
            return f'''
            <div class="revelation-block bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-400 p-8 mb-8 rounded-r-lg">
                <h3 class="text-2xl font-bold text-purple-800 mb-4">The Revelation</h3>
                <div class="text-purple-700 text-lg leading-relaxed">{content}</div>
            </div>
            '''

        return None  # Block type not implemented

    def _get_html_head(self) -> str:
        """Get HTML head with CSS framework"""
        return '''
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Digital Dreamscape</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                .prose { max-width: none; }
                .prose h1 { @apply text-4xl font-bold leading-tight mb-6; }
                .prose h2 { @apply text-3xl font-semibold leading-tight mt-8 mb-4; }
                .prose h3 { @apply text-2xl font-medium leading-tight mt-6 mb-3; }
                .prose p { @apply text-lg leading-relaxed mb-4; }
                .prose ul { @apply list-disc list-inside mb-4; }
                .prose ol { @apply list-decimal list-inside mb-4; }
                .prose li { @apply mb-2; }
                .prose code { @apply bg-gray-100 px-2 py-1 rounded text-sm font-mono; }
                .prose pre { @apply bg-gray-900 p-4 rounded overflow-x-auto; }
                .prose pre code { @apply text-green-400 bg-transparent p-0; }
                .prose blockquote { @apply border-l-4 border-blue-400 pl-6 py-4 my-8 bg-blue-50 italic; }
            </style>
        </head>
        <body class="bg-white text-gray-900">
            <div class="min-h-screen">
        '''

    def _get_html_footer(self) -> str:
        """Get HTML footer"""
        return '''
            </div>
        </body>
        </html>
        '''

    def validate_html(self, html_content: str) -> List[str]:
        """Basic HTML validation"""
        issues = []

        # Check for unclosed tags (basic check)
        if html_content.count('<div') != html_content.count('</div>'):
            issues.append("Unmatched div tags")

        if html_content.count('<p') != html_content.count('</p>'):
            issues.append("Unmatched p tags")

        # Check for required elements
        if '<title>' not in html_content:
            issues.append("Missing title tag")

        if '<!DOCTYPE html>' not in html_content:
            issues.append("Missing DOCTYPE")

        return issues