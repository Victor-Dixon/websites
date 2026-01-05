#!/usr/bin/env python3
"""
Consolidated Template Service
============================

UNIFIED TEMPLATE MANAGEMENT - Phase 3 Consolidation
Consolidates template systems from 12+ files into 1 unified service.

CONSOLIDATED FROM:
- template_engine.py (block-based HTML rendering)
- autoblogger YAML templates (content generation templates)
- content_pipeline.py (template selection logic)
- digital_dreamscape_pipeline.py (template integration)
- Plus additional template/rendering logic scattered throughout

PROVIDES:
- Unified template management for all content types
- Support for both YAML autoblogger templates and HTML block templates
- Template selection intelligence
- Content rendering pipeline
- Backward compatibility with existing systems
"""

import os
import yaml
import json
from pathlib import Path
from typing import Dict, List, Any, Optional, Tuple, Union
from dataclasses import dataclass
from enum import Enum
import logging
import re

logger = logging.getLogger(__name__)


class TemplateType(Enum):
    """Types of templates supported"""
    AUTOBLOGGER_YAML = "autoblogger_yaml"  # YAML-based content generation templates
    HTML_BLOCK = "html_block"             # Block-based HTML rendering templates
    MARKDOWN = "markdown"                 # Simple markdown templates


class ContentCategory(Enum):
    """Content categories for template selection"""
    TECHNICAL = "technical"
    STRATEGIC = "strategic"
    OPERATIONAL = "operational"
    NARRATIVE = "narrative"
    LEARNING = "learning"
    REFLECTION = "reflection"


@dataclass
class TemplateMetadata:
    """Metadata for a template"""
    template_id: str
    name: str
    description: str
    template_type: TemplateType
    content_categories: List[ContentCategory]
    brand: str
    priority: int = 0
    file_path: Optional[str] = None


@dataclass
class RenderedContent:
    """Result of content rendering"""
    html_content: str
    markdown_content: str
    template_used: str
    metadata: Dict[str, Any]
    validation_errors: List[str]


class ConsolidatedTemplateService:
    """
    UNIFIED TEMPLATE SERVICE
    =======================

    Consolidates all template management from 12+ files into a single,
    comprehensive service with backward compatibility.
    """

    def __init__(self):
        """Initialize with all template sources"""
        self.templates: Dict[str, TemplateMetadata] = {}
        self.autoblogger_templates: Dict[str, Dict] = {}
        self.html_templates: Dict[str, Dict] = {}

        # Load all template sources
        self._load_autoblogger_templates()
        self._load_html_templates()
        self._setup_template_metadata()

    def _load_autoblogger_templates(self):
        """Load YAML autoblogger templates"""
        template_dir = Path(__file__).parent.parent.parent / "src" / "autoblogger" / "ssot" / "templates"

        if template_dir.exists():
            for yaml_file in template_dir.glob("*.yaml"):
                try:
                    with open(yaml_file, 'r', encoding='utf-8') as f:
                        template_data = yaml.safe_load(f)
                        template_id = yaml_file.stem
                        self.autoblogger_templates[template_id] = template_data
                        logger.info(f"Loaded autoblogger template: {template_id}")
                except Exception as e:
                    logger.error(f"Failed to load template {yaml_file}: {e}")

    def _load_html_templates(self):
        """Load HTML block templates from template_engine"""
        try:
            # Import the existing template engine
            from template_engine import TemplateEngine
            engine = TemplateEngine()

            # Extract template data from the engine
            for template_id, template_data in engine.templates.items():
                self.html_templates[template_id] = template_data

            logger.info(f"Loaded {len(self.html_templates)} HTML templates")

        except ImportError:
            logger.warning("TemplateEngine not available, HTML templates not loaded")

    def _setup_template_metadata(self):
        """Create unified template metadata"""
        # Autoblogger templates
        for template_id, template_data in self.autoblogger_templates.items():
            brand = template_data.get('brand', 'unknown')
            content_types = template_data.get('content_types', [])

            # Map content types to categories
            categories = []
            for ct in content_types:
                if 'technical' in ct or 'experiment' in ct:
                    categories.append(ContentCategory.TECHNICAL)
                elif 'strategic' in ct or 'project' in ct:
                    categories.append(ContentCategory.STRATEGIC)
                elif 'learning' in ct or 'resume' in ct:
                    categories.append(ContentCategory.LEARNING)
                else:
                    categories.append(ContentCategory.NARRATIVE)

            metadata = TemplateMetadata(
                template_id=f"autoblogger_{template_id}",
                name=template_data.get('template_name', template_id),
                description=f"Autoblogger template for {brand}",
                template_type=TemplateType.AUTOBLOGGER_YAML,
                content_categories=list(set(categories)),
                brand=brand,
                priority=10,  # Higher priority for autoblogger templates
                file_path=str(Path(__file__).parent.parent.parent / "src" / "autoblogger" / "ssot" / "templates" / f"{template_id}.yaml")
            )
            self.templates[metadata.template_id] = metadata

        # HTML block templates
        for template_id, template_data in self.html_templates.items():
            # Handle TemplateDefinition objects vs dicts
            if hasattr(template_data, 'name'):
                name = template_data.name
                description = getattr(template_data, 'description', f"HTML block template {template_id}")
            else:
                name = template_data.get('name', template_id) if isinstance(template_data, dict) else template_id
                description = template_data.get('description', f"HTML block template {template_id}") if isinstance(template_data, dict) else f"HTML block template {template_id}"

            metadata = TemplateMetadata(
                template_id=f"html_{template_id}",
                name=name,
                description=description,
                template_type=TemplateType.HTML_BLOCK,
                content_categories=[ContentCategory.TECHNICAL],  # Default, can be expanded
                brand="digitaldreamscape",  # HTML templates are DD-specific
                priority=5,  # Lower priority than autoblogger
                file_path=None  # HTML templates are in code
            )
            self.templates[metadata.template_id] = metadata

    def select_template(self, content: str, category: ContentCategory = None,
                       brand: str = None, content_type: str = None) -> str:
        """
        INTELLIGENT TEMPLATE SELECTION
        =============================

        Unified template selection logic from:
        - template_engine.py (HTML template selection)
        - autoblogger template selection logic
        - content_pipeline.py (template selection)

        Args:
            content: The content to be templated
            category: Content category
            brand: Target brand
            content_type: Specific content type

        Returns:
            Best template ID for the content
        """

        if category is None:
            category = self._detect_content_category(content)

        # Score templates based on suitability
        template_scores = {}

        for template_id, metadata in self.templates.items():
            score = 0

            # Brand match (highest priority)
            if brand and metadata.brand == brand:
                score += 50

            # Category match
            if category in metadata.content_categories:
                score += 30

            # Content type specific matching for autoblogger templates
            if metadata.template_type == TemplateType.AUTOBLOGGER_YAML:
                template_data = self.autoblogger_templates.get(template_id.replace('autoblogger_', ''), {})
                content_types = template_data.get('content_types', [])

                if content_type and content_type in content_types:
                    score += 40

                # Check if required inputs are available in content
                required_inputs = template_data.get('required_inputs', [])
                available_fields = self._extract_available_fields(content)

                # Bonus for templates where we have required inputs
                if all(req_input in available_fields for req_input in required_inputs):
                    score += 20

            # Priority bonus
            score += metadata.priority

            # Length suitability (prefer shorter templates for shorter content)
            content_length = len(content.split())
            if content_length < 500 and 'short' in template_id.lower():
                score += 10
            elif content_length > 1000 and 'comprehensive' in template_id.lower():
                score += 10

            template_scores[template_id] = score

        # Return highest scoring template
        if template_scores:
            best_template = max(template_scores.items(), key=lambda x: x[1])
            logger.info(f"Selected template: {best_template[0]} (score: {best_template[1]})")
            return best_template[0]

        # Fallback to default
        return "autoblogger_general_post"

    def render_content(self, content_data: Dict[str, Any], template_id: str = None) -> RenderedContent:
        """
        UNIFIED CONTENT RENDERING
        ========================

        Consolidated rendering logic from:
        - template_engine.py (HTML block rendering)
        - autoblogger template rendering
        - content_pipeline.py (rendering pipeline)

        Args:
            content_data: Content data dictionary
            template_id: Specific template to use (auto-selected if None)

        Returns:
            RenderedContent with HTML and markdown versions
        """

        if template_id is None:
            content_text = content_data.get('content_text', content_data.get('content', ''))
            category = content_data.get('category')
            brand = content_data.get('brand')
            content_type = content_data.get('content_type')

            template_id = self.select_template(content_text, category, brand, content_type)

        # Get template metadata
        metadata = self.templates.get(template_id)
        if not metadata:
            raise ValueError(f"Template not found: {template_id}")

        validation_errors = []

        try:
            if metadata.template_type == TemplateType.AUTOBLOGGER_YAML:
                html_content, markdown_content = self._render_autoblogger_template(content_data, template_id)
            elif metadata.template_type == TemplateType.HTML_BLOCK:
                html_content, markdown_content = self._render_html_template(content_data, template_id)
            else:
                # Fallback to basic markdown
                html_content = self._markdown_to_html(content_data.get('content', ''))
                markdown_content = content_data.get('content', '')

        except Exception as e:
            logger.error(f"Template rendering failed: {e}")
            validation_errors.append(f"Rendering error: {str(e)}")
            html_content = f"<p>Error rendering content: {e}</p>"
            markdown_content = content_data.get('content', '')

        return RenderedContent(
            html_content=html_content,
            markdown_content=markdown_content,
            template_used=template_id,
            metadata={'template_type': metadata.template_type.value, 'brand': metadata.brand},
            validation_errors=validation_errors
        )

    def _render_autoblogger_template(self, content_data: Dict[str, Any], template_id: str) -> Tuple[str, str]:
        """Render autoblogger YAML template"""
        template_key = template_id.replace('autoblogger_', '')
        template_data = self.autoblogger_templates.get(template_key)

        if not template_data:
            raise ValueError(f"Autoblogger template not found: {template_key}")

        # Basic template rendering (simplified version)
        # In full implementation, this would use Jinja2 or similar
        rendered_sections = []

        structure = template_data.get('structure', {})
        content_sections = structure.get('content_sections', [])

        for section in content_sections:
            section_name = section.get('section')
            heading = section.get('heading')
            template_text = section.get('template', '')

            # Simple variable substitution (basic implementation)
            rendered_text = self._substitute_variables(template_text, content_data)

            if heading and rendered_text.strip():
                rendered_sections.append(f"## {heading}\n\n{rendered_text}")
            elif rendered_text.strip():
                rendered_sections.append(rendered_text)

        markdown_content = '\n\n'.join(rendered_sections)
        html_content = self._markdown_to_html(markdown_content)

        return html_content, markdown_content

    def _render_html_template(self, content_data: Dict[str, Any], template_id: str) -> Tuple[str, str]:
        """Render HTML block template"""
        try:
            from template_engine import TemplateEngine
            engine = TemplateEngine()

            template_key = template_id.replace('html_', '')
            html_content = engine.render_content(content_data, template_key)
            markdown_content = self._html_to_markdown(html_content)

            return html_content, markdown_content

        except ImportError:
            # Fallback
            markdown_content = content_data.get('content', '')
            html_content = f"<div>{markdown_content}</div>"
            return html_content, markdown_content

    def validate_template(self, template_id: str) -> Dict[str, Any]:
        """
        VALIDATE TEMPLATE
        ================

        Consolidated validation from:
        - template_engine.py (HTML validation)
        - autoblogger template validation

        Returns validation results
        """
        metadata = self.templates.get(template_id)
        if not metadata:
            return {'valid': False, 'errors': [f"Template not found: {template_id}"]}

        errors = []

        try:
            if metadata.template_type == TemplateType.AUTOBLOGGER_YAML:
                template_key = template_id.replace('autoblogger_', '')
                template_data = self.autoblogger_templates.get(template_key, {})

                # Check required fields
                required_fields = ['template_name', 'structure']
                for field in required_fields:
                    if field not in template_data:
                        errors.append(f"Missing required field: {field}")

            elif metadata.template_type == TemplateType.HTML_BLOCK:
                template_key = template_id.replace('html_', '')
                if template_key not in self.html_templates:
                    errors.append(f"HTML template not found in engine: {template_key}")

        except Exception as e:
            errors.append(f"Validation error: {str(e)}")

        return {
            'valid': len(errors) == 0,
            'errors': errors,
            'template_type': metadata.template_type.value,
            'brand': metadata.brand
        }

    def get_available_templates(self, brand: str = None, category: ContentCategory = None) -> List[TemplateMetadata]:
        """Get available templates filtered by brand and category"""
        templates = list(self.templates.values())

        if brand:
            templates = [t for t in templates if t.brand == brand]

        if category:
            templates = [t for t in templates if category in t.content_categories]

        return sorted(templates, key=lambda t: t.priority, reverse=True)

    # UTILITY METHODS
    # ===============

    def _detect_content_category(self, content: str) -> ContentCategory:
        """Detect content category from text analysis"""
        content_lower = content.lower()

        # Technical indicators
        technical_keywords = ['api', 'code', 'function', 'database', 'server', 'error', 'debug']
        technical_score = sum(1 for keyword in technical_keywords if keyword in content_lower)

        # Strategic indicators
        strategic_keywords = ['strategy', 'plan', 'goal', 'vision', 'roadmap', 'leadership']
        strategic_score = sum(1 for keyword in strategic_keywords if keyword in content_lower)

        # Learning indicators
        learning_keywords = ['learned', 'learning', 'experience', 'skill', 'knowledge']
        learning_score = sum(1 for keyword in learning_keywords if keyword in content_lower)

        # Return highest scoring category
        scores = {
            ContentCategory.TECHNICAL: technical_score,
            ContentCategory.STRATEGIC: strategic_score,
            ContentCategory.LEARNING: learning_score
        }

        return max(scores.items(), key=lambda x: x[1])[0]

    def _extract_available_fields(self, content: str) -> List[str]:
        """Extract available field names from content (simplified)"""
        # This would be more sophisticated in a full implementation
        fields = []
        if 'experiment' in content.lower():
            fields.extend(['experiment_name', 'learning_text'])
        if 'resume' in content.lower():
            fields.extend(['resume_before', 'resume_after', 'skill_learned'])
        if 'project' in content.lower():
            fields.extend(['project_name', 'project_demo_text'])
        if 'idea' in content.lower():
            fields.extend(['idea_text', 'brainstorm_text'])

        return fields

    def _substitute_variables(self, template_text: str, variables: Dict[str, Any]) -> str:
        """Simple variable substitution (would use Jinja2 in full implementation)"""
        result = template_text

        # Basic variable substitution
        for key, value in variables.items():
            if isinstance(value, str):
                result = result.replace(f"{{{key}}}", value)
            elif value is not None:
                result = result.replace(f"{{{key}}}", str(value))

        # Remove conditional blocks that don't match (simplified)
        result = re.sub(r'\{#.*?if.*?#\}', '', result, flags=re.DOTALL)
        result = re.sub(r'\{#.*?#\}', '', result)

        return result

    def _markdown_to_html(self, markdown: str) -> str:
        """Basic markdown to HTML conversion (would use proper library)"""
        # Very basic conversion - in real implementation use markdown library
        html = markdown.replace('\n\n', '</p><p>')
        html = f"<p>{html}</p>"
        html = re.sub(r'\*\*(.*?)\*\*', r'<strong>\1</strong>', html)
        html = re.sub(r'\*(.*?)\*', r'<em>\1</em>', html)
        return html

    def _html_to_markdown(self, html: str) -> str:
        """Basic HTML to markdown conversion"""
        # Very basic conversion - in real implementation use html2text
        markdown = html.replace('<p>', '').replace('</p>', '\n\n')
        markdown = re.sub(r'<strong>(.*?)</strong>', r'**\1**', markdown)
        markdown = re.sub(r'<em>(.*?)</em>', r'*\1*', markdown)
        return markdown.strip()


# BACKWARD COMPATIBILITY
# =====================

# For template_engine.py compatibility
def select_template(content: str, category: str = None, **kwargs) -> str:
    """Legacy template selection function"""
    service = ConsolidatedTemplateService()
    return service.select_template(content, category, **kwargs)

def render_content(content_data: Dict[str, Any], template_id: str = None) -> str:
    """Legacy content rendering function"""
    service = ConsolidatedTemplateService()
    result = service.render_content(content_data, template_id)
    return result.html_content

# Global instance for singleton access
_template_service = None

def get_template_service() -> ConsolidatedTemplateService:
    """Get singleton instance of the consolidated template service"""
    global _template_service
    if _template_service is None:
        _template_service = ConsolidatedTemplateService()
    return _template_service


if __name__ == "__main__":
    # Quick test
    service = ConsolidatedTemplateService()

    print("🎨 CONSOLIDATED TEMPLATE SERVICE TEST")
    print("=" * 50)

    # Show available templates
    templates = service.get_available_templates()
    print(f"📋 Available Templates: {len(templates)}")
    for template in templates[:5]:  # Show first 5
        print(f"  • {template.template_id} ({template.brand}) - {template.template_type.value}")

    # Test template selection
    test_content = """
    I just learned how to implement authentication in my web app.
    The key was understanding JWT tokens and proper session management.
    Here's what I discovered...
    """

    selected = service.select_template(test_content, ContentCategory.LEARNING)
    print(f"\n🎯 Selected Template: {selected}")

    # Test rendering
    content_data = {
        'content': test_content,
        'title': 'JWT Authentication Learning',
        'brand': 'dadudekc',
        'content_type': 'experiments_learnings'
    }

    try:
        result = service.render_content(content_data)
        print("\n✅ Rendering successful!")
        print(f"Template Used: {result.template_used}")
        print(f"HTML Length: {len(result.html_content)} chars")
        print(f"Markdown Length: {len(result.markdown_content)} chars")
        if result.validation_errors:
            print(f"⚠️  Validation Errors: {result.validation_errors}")
    except Exception as e:
        print(f"❌ Rendering failed: {e}")

    print("\n🎉 Template service consolidation test complete!")