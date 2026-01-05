"""
Content Pipeline - Complete Checkpoint System
==============================================

From Devlog Draft → Episode → Blog-ready → Published
With Victor voice, SEO, vectorization, and category/questline styling.
"""

import os
import json
import yaml
from pathlib import Path
from typing import Dict, List, Any, Optional, Tuple
from enum import Enum
from dataclasses import dataclass, field
from datetime import datetime
import hashlib
import logging

logger = logging.getLogger(__name__)

class PipelineStatus(Enum):
    """Complete pipeline checkpoint states"""
    DEVLOG_DRAFT = "devlog_draft"
    EPISODE_DRAFT = "episode_draft"
    VOICE_APPLIED = "voice_applied"
    SEO_ENHANCED = "seo_enhanced"
    VECTORIZED = "vectorized"
    TEMPLATE_SELECTED = "template_selected"
    STYLED_HTML = "styled_html"
    QA_READY = "qa_ready"
    SCHEDULED = "scheduled"
    PUBLISHED = "published"
    SYNDICATED = "syndicated"
    MEASURED = "measured"
    ITERATION = "iteration"

class ContentCategory(Enum):
    """SSOT Content Categories"""
    TRADING = "trading"
    SWARM = "swarm"
    DREAMSCAPE_LORE = "dreamscape_lore"
    DEVLOG = "devlog"
    TOOLS = "tools"

class Questline(Enum):
    """Questline taxonomy"""
    MEMORY_NEXUS = "memory_nexus"
    AGENT_CELL_PHONE = "agent_cell_phone"
    TBOWS_TACTICS = "tbows_tactics"
    ENTERPRISE_DEPLOY = "enterprise_deploy"

class MissionType(Enum):
    """Mission type taxonomy"""
    BUILD = "build"
    FIX = "fix"
    AUDIT = "audit"
    LESSON = "lesson"
    LAUNCH = "launch"
    POSTMORTEM = "postmortem"

@dataclass
class ContentMetadata:
    """Complete content metadata throughout pipeline"""
    content_id: str
    episode_id: str
    post_id: str

    # Status tracking
    current_status: PipelineStatus
    status_history: List[Dict[str, Any]] = field(default_factory=list)

    # Content attributes
    title: str = ""
    category: Optional[ContentCategory] = None
    questline: Optional[Questline] = None
    mission_type: Optional[MissionType] = None

    # SEO metadata
    primary_keyword: str = ""
    secondary_keywords: List[str] = field(default_factory=list)
    meta_description: str = ""
    slug: str = ""

    # Template selection
    selected_template: str = "autoblogger_general_post"  # Use consolidated template service
    template_metadata: Dict[str, Any] = field(default_factory=dict)

    # Publishing
    wp_post_id: Optional[int] = None
    wp_url: Optional[str] = None
    canonical_url: Optional[str] = None
    published_at: Optional[datetime] = None

    # Analytics
    impressions: int = 0
    clicks: int = 0
    avg_time_on_page: float = 0.0
    conversions: int = 0
    engagement_score: float = 0.0

    # Source tracking
    source_refs: List[str] = field(default_factory=list)
    artifacts: List[str] = field(default_factory=list)

    def update_status(self, new_status: PipelineStatus, notes: str = ""):
        """Update status with history tracking"""
        self.status_history.append({
            'timestamp': datetime.now().isoformat(),
            'from_status': self.current_status.value if self.current_status else None,
            'to_status': new_status.value,
            'notes': notes
        })
        self.current_status = new_status
        logger.info(f"Content {self.content_id}: {self.current_status.value} → {new_status.value}")

@dataclass
class PipelineCheckpoint:
    """Individual checkpoint definition"""
    id: str
    status: PipelineStatus
    purpose: str
    inputs: List[str]
    outputs: List[Dict[str, str]]
    gates: List[str]
    transform: List[str] = field(default_factory=list)
    validation_rituals: List[str] = field(default_factory=list)

@dataclass
class ContentPipeline:
    """Complete content pipeline with all checkpoints"""

    def __init__(self, config_path: Optional[str] = None):
        self.config_path = config_path or Path(__file__).parent / "content_pipeline_config.yaml"
        self.checkpoints: Dict[str, PipelineCheckpoint] = {}
        self.templates: Dict[str, Dict[str, Any]] = {}
        self.naming_convention = {}

        self.load_config()

    def load_config(self):
        """Load pipeline configuration from YAML"""
        if not Path(self.config_path).exists():
            self.create_default_config()
            return

        with open(self.config_path, 'r') as f:
            config = yaml.safe_load(f)

        # Load naming convention
        self.naming_convention = config.get('naming_convention', {})

        # Load checkpoints
        for checkpoint_data in config.get('checkpoints', []):
            checkpoint = PipelineCheckpoint(**checkpoint_data)
            self.checkpoints[checkpoint.status.value] = checkpoint

        # Load templates
        self.templates = config.get('templates', {})

    def create_default_config(self):
        """Create default pipeline configuration"""
        default_config = {
            'pipeline': 'dreamscape_lower_codex_content',
            'version': '1.0',
            'objective': 'Turn devlog drafts into episode + blog content with Victor voice, SEO, vectorization, and category/questline styling.',

            'naming_convention': {
                'content_id': 'LCDX-{YYYYMMDD}-{short_slug}',
                'episode_id': 'EP-{content_id}',
                'post_id': 'POST-{content_id}'
            },

            'checkpoints': [
                {
                    'id': '01',
                    'status': 'DEVLOG_DRAFT',
                    'purpose': 'Raw capture from agent/devlog; truth source.',
                    'inputs': ['devlog_entry_md', 'source_refs'],
                    'outputs': [
                        {'artifact': 'content/devlogs/{content_id}.md'},
                        {'metadata': 'content/meta/{content_id}.json'}
                    ],
                    'gates': [
                        'Has: what/why/result + proof links',
                        'No placeholders; no missing nouns'
                    ]
                },
                {
                    'id': '02',
                    'status': 'EPISODE_DRAFT',
                    'purpose': 'Narrative episode built from devlog; readable arc.',
                    'inputs': ['content/devlogs/{content_id}.md'],
                    'outputs': [{'artifact': 'content/episodes/{episode_id}.md'}],
                    'transform': [
                        'Extract: hook, conflict, decision, execution, outcome, lesson',
                        'Add: questline + mission type tags'
                    ],
                    'gates': [
                        'Episode has arc + 1 clear takeaway',
                        'Includes concrete artifacts/paths or URLs'
                    ]
                },
                {
                    'id': '03',
                    'status': 'VOICE_APPLIED',
                    'purpose': 'Apply Victor voice profile to episode + future blog draft.',
                    'inputs': ['content/episodes/{episode_id}.md'],
                    'outputs': [
                        {'artifact': 'content/episodes/{episode_id}.voice.md'},
                        {'artifact': 'content/blog_drafts/{post_id}.draft.md'}
                    ],
                    'transform': [
                        'Cadence: short lines, momentum, tasteful ellipses/dividers',
                        'Tone: plainspoken, proof-first, closure-first',
                        'Remove: corporate fluff, vague claims'
                    ],
                    'gates': [
                        'Reads like Victor (commander/builder posture)',
                        'Proof is present (links, metrics, diffs, logs)',
                        'No drift: every section earns its place'
                    ]
                },
                {
                    'id': '04',
                    'status': 'SEO_ENHANCED',
                    'purpose': 'SEO + discoverability improvements on the blog draft.',
                    'inputs': ['content/blog_drafts/{post_id}.draft.md'],
                    'outputs': [
                        {'artifact': 'content/blog_drafts/{post_id}.seo.md'},
                        {'metadata': 'content/seo/{post_id}.seo.json'}
                    ],
                    'transform': [
                        'Define: primary keyword + 3 secondary',
                        'Add: title variants, meta description, slug',
                        'Add: H2/H3 structure, FAQ, internal links',
                        'Add: image alt text plan (if images exist)'
                    ],
                    'gates': [
                        'Search intent matched (what/why/how/proof)',
                        '1 clear CTA aligned to site goal',
                        'No keyword stuffing; readable first'
                    ]
                },
                {
                    'id': '05',
                    'status': 'VECTORIZED',
                    'purpose': 'Make draft retrievable + remixable (embeddings + tags).',
                    'inputs': ['content/blog_drafts/{post_id}.seo.md'],
                    'outputs': [
                        {'artifact': 'content/vector/{post_id}.chunks.json'},
                        {'artifact': 'content/vector/{post_id}.embeddings.bin'}
                    ],
                    'transform': [
                        'Chunk: sections → embedding units',
                        'Attach: questline/category/mission_type/entities/tooling tags',
                        'Store: embeddings + citations to artifacts'
                    ],
                    'gates': [
                        'Chunks have stable IDs + source pointers',
                        'Tags are consistent (SSOT taxonomy)'
                    ]
                },
                {
                    'id': '06',
                    'status': 'TEMPLATE_SELECTED',
                    'purpose': 'Pick the correct styling template (block design).',
                    'inputs': ['content/vector/{post_id}.chunks.json'],
                    'outputs': [{'metadata': 'content/templates/{post_id}.template.json'}],
                    'gates': [
                        'Template resolved deterministically',
                        'Has fallback template if unknown'
                    ]
                },
                {
                    'id': '07',
                    'status': 'STYLED_HTML',
                    'purpose': 'Render into WordPress-friendly HTML with the right block styling.',
                    'inputs': ['content/blog_drafts/{post_id}.seo.md', 'content/templates/{post_id}.template.json'],
                    'outputs': [{'artifact': 'content/rendered/{post_id}.html'}],
                    'transform': [
                        'Wrap: hero, toc, sections, callouts, code blocks, CTAs',
                        'Apply: typography scale, spacing rhythm, quote/callout styles'
                    ],
                    'gates': [
                        'Valid HTML; no inline junk unless allowed',
                        'All blocks map to style system tokens'
                    ]
                },
                {
                    'id': '08',
                    'status': 'QA_READY',
                    'purpose': 'Validation rituals before scheduling/publish.',
                    'inputs': ['content/rendered/{post_id}.html'],
                    'outputs': [{'report': 'content/reports/{post_id}.qa.md'}],
                    'validation_rituals': [
                        'Link check: internal + external',
                        'Spell/grammar pass (light)',
                        'Brand pass: Victor voice + proof-first',
                        'SEO pass: title/meta/H-tags/slug',
                        'Style pass: blocks render clean on mobile'
                    ],
                    'gates': [
                        'All rituals pass OR exceptions explicitly logged'
                    ]
                },
                {
                    'id': '09',
                    'status': 'SCHEDULED',
                    'purpose': 'Assign publish time + distribution plan.',
                    'inputs': ['content/reports/{post_id}.qa.md'],
                    'outputs': [{'metadata': 'content/schedule/{post_id}.json'}],
                    'gates': [
                        'Channel targets defined',
                        'UTM plan present (if used)'
                    ]
                },
                {
                    'id': '10',
                    'status': 'PUBLISHED',
                    'purpose': 'Publish to WP with canonical + category/tags.',
                    'inputs': ['content/rendered/{post_id}.html', 'content/schedule/{post_id}.json'],
                    'outputs': [{'metadata': 'content/published/{post_id}.json'}],
                    'gates': [
                        'URL live + correct template applied'
                    ]
                }
            ],

            'template_system': {
                'ssot_taxonomy': {
                    'category': ['trading', 'swarm', 'dreamscape_lore', 'devlog', 'tools'],
                    'questline': ['memory_nexus', 'agent_cell_phone', 'tbows_tactics', 'enterprise_deploy'],
                    'mission_type': ['build', 'fix', 'audit', 'lesson', 'launch', 'postmortem']
                },

                'templates': [
                    {
                        'id': 'base_default',
                        'blocks': ['hero', 'toc', 'section', 'callout', 'code', 'quote', 'cta', 'footer'],
                        'styling_tokens': ['type_scale', 'spacing_rhythm', 'content_width', 'code_theme']
                    },
                    {
                        'id': 'trading_signal_report',
                        'blocks': ['hero_price_action', 'thesis', 'setup_rules', 'screenshots', 'risk_box', 'results_table', 'cta'],
                        'styling_tokens': ['chart_caption', 'risk_callout', 'rule_cards']
                    },
                    {
                        'id': 'swarm_engineering_log',
                        'blocks': ['hero', 'problem', 'constraints', 'approach', 'diffs', 'validation_ritual', 'next_steps', 'cta'],
                        'styling_tokens': ['diff_callout', 'checklist_blocks']
                    },
                    {
                        'id': 'dreamscape_lore_episode',
                        'blocks': ['hero_lore', 'scene', 'conflict', 'revelation', 'artifact_drop', 'oath_cta'],
                        'styling_tokens': ['lore_dividers', 'cinematic_quotes']
                    }
                ]
            }
        }

        # Save default config
        with open(self.config_path, 'w') as f:
            yaml.dump(default_config, f, default_flow_style=False)

        logger.info(f"Created default pipeline config at {self.config_path}")

class ContentPipelineProcessor:
    """Processes content through the complete pipeline"""

    def __init__(self, pipeline: ContentPipeline):
        self.pipeline = pipeline
        self.content_base = Path("content")
        self.content_base.mkdir(exist_ok=True)

    def create_content_id(self, title: str, date: Optional[datetime] = None) -> str:
        """Generate content ID following naming convention"""
        date = date or datetime.now()
        short_slug = self._create_short_slug(title)
        return f"LCDX-{date.strftime('%Y%m%d')}-{short_slug}"

    def _create_short_slug(self, title: str) -> str:
        """Create short slug from title"""
        # Clean title and create slug
        clean_title = ''.join(c for c in title.lower() if c.isalnum() or c.isspace())
        words = clean_title.split()[:3]  # First 3 words
        slug = '-'.join(words)
        return slug[:20]  # Limit length

    def initialize_content(self, title: str, raw_content: str,
                          category: Optional[ContentCategory] = None,
                          questline: Optional[Questline] = None,
                          mission_type: Optional[MissionType] = None) -> ContentMetadata:
        """Initialize new content in the pipeline"""

        content_id = self.create_content_id(title)
        episode_id = f"EP-{content_id}"
        post_id = f"POST-{content_id}"

        metadata = ContentMetadata(
            content_id=content_id,
            episode_id=episode_id,
            post_id=post_id,
            title=title,
            category=category,
            questline=questline,
            mission_type=mission_type,
            current_status=PipelineStatus.DEVLOG_DRAFT
        )

        # Save initial devlog
        devlog_path = self.content_base / "devlogs" / f"{content_id}.md"
        devlog_path.parent.mkdir(parents=True, exist_ok=True)

        with open(devlog_path, 'w') as f:
            f.write(f"# {title}\n\n{raw_content}")

        # Save metadata
        meta_path = self.content_base / "meta" / f"{content_id}.json"
        meta_path.parent.mkdir(parents=True, exist_ok=True)

        with open(meta_path, 'w') as f:
            json.dump({
                'content_id': content_id,
                'title': title,
                'category': category.value if category else None,
                'questline': questline.value if questline else None,
                'mission_type': mission_type.value if mission_type else None,
                'created_at': datetime.now().isoformat(),
                'current_status': metadata.current_status.value
            }, f, indent=2)

        metadata.artifacts.append(str(devlog_path))
        metadata.artifacts.append(str(meta_path))

        logger.info(f"Initialized content: {content_id}")
        return metadata

    def advance_checkpoint(self, metadata: ContentMetadata,
                          target_status: PipelineStatus,
                          notes: str = "") -> bool:
        """Advance content to next checkpoint"""

        current_checkpoint = self.pipeline.checkpoints.get(metadata.current_status.value)
        target_checkpoint = self.pipeline.checkpoints.get(target_status.value)

        if not current_checkpoint or not target_checkpoint:
            logger.error(f"Invalid checkpoint transition: {metadata.current_status.value} → {target_status.value}")
            return False

        # Validate gates for current checkpoint
        if not self._validate_checkpoint_gates(metadata, current_checkpoint):
            logger.error(f"Gates not satisfied for {metadata.current_status.value}")
            return False

        # Apply transformations if defined
        if target_checkpoint.transform:
            if not self._apply_checkpoint_transforms(metadata, target_checkpoint):
                logger.error(f"Transform failed for {target_status.value}")
                return False

        # Update status
        metadata.update_status(target_status, notes)

        # Save updated metadata
        self._save_metadata(metadata)

        return True

    def _validate_checkpoint_gates(self, metadata: ContentMetadata,
                                  checkpoint: PipelineCheckpoint) -> bool:
        """Validate gates for a checkpoint"""
        # Basic validation - in production this would be more sophisticated
        for gate in checkpoint.gates:
            logger.debug(f"Validating gate: {gate}")
            # Placeholder - actual validation would check content quality,
            # SEO completeness, etc.

        return True  # All gates pass

    def _apply_checkpoint_transforms(self, metadata: ContentMetadata,
                                    checkpoint: PipelineCheckpoint) -> bool:
        """Apply transformations for a checkpoint"""
        for transform in checkpoint.transform:
            logger.debug(f"Applying transform: {transform}")
            # Placeholder - actual transforms would implement Victor voice,
            # SEO enhancement, template selection, etc.

        return True  # All transforms successful

    def _save_metadata(self, metadata: ContentMetadata):
        """Save content metadata to disk"""
        meta_path = self.content_base / "meta" / f"{metadata.content_id}.json"

        with open(meta_path, 'w') as f:
            json.dump({
                'content_id': metadata.content_id,
                'episode_id': metadata.episode_id,
                'post_id': metadata.post_id,
                'title': metadata.title,
                'category': metadata.category.value if metadata.category else None,
                'questline': metadata.questline.value if metadata.questline else None,
                'mission_type': metadata.mission_type.value if metadata.mission_type else None,
                'current_status': metadata.current_status.value,
                'status_history': metadata.status_history,
                'primary_keyword': metadata.primary_keyword,
                'secondary_keywords': metadata.secondary_keywords,
                'meta_description': metadata.meta_description,
                'slug': metadata.slug,
                'selected_template': metadata.selected_template,
                'wp_post_id': metadata.wp_post_id,
                'wp_url': metadata.wp_url,
                'canonical_url': metadata.canonical_url,
                'published_at': metadata.published_at.isoformat() if metadata.published_at else None,
                'artifacts': metadata.artifacts,
                'updated_at': datetime.now().isoformat()
            }, f, indent=2)

    def select_template(self, metadata: ContentMetadata) -> str:
        """Select appropriate template based on content attributes"""

        # Template selection logic based on category/questline/mission_type
        if metadata.category == ContentCategory.TRADING:
            return "trading_signal_report"
        elif metadata.category == ContentCategory.SWARM:
            return "swarm_engineering_log"
        elif metadata.category == ContentCategory.DREAMSCAPE_LORE:
            return "dreamscape_lore_episode"
        else:
            return "base_default"

    def get_pipeline_status(self, content_id: str) -> Optional[ContentMetadata]:
        """Get current pipeline status for content"""
        meta_path = self.content_base / "meta" / f"{content_id}.json"

        if not meta_path.exists():
            return None

        with open(meta_path, 'r') as f:
            data = json.load(f)

        # Reconstruct ContentMetadata from saved data
        metadata = ContentMetadata(
            content_id=data['content_id'],
            episode_id=data['episode_id'],
            post_id=data['post_id'],
            title=data['title'],
            current_status=PipelineStatus(data['current_status']),
            status_history=data.get('status_history', [])
        )

        # Restore optional fields
        if data.get('category'):
            metadata.category = ContentCategory(data['category'])
        if data.get('questline'):
            metadata.questline = Questline(data['questline'])
        if data.get('mission_type'):
            metadata.mission_type = MissionType(data['mission_type'])

        return metadata

# Utility functions for pipeline integration
def create_content_from_devlog(devlog_path: Path) -> Optional[ContentMetadata]:
    """Create pipeline content from existing devlog"""
    pipeline = ContentPipeline()
    processor = ContentPipelineProcessor(pipeline)

    if not devlog_path.exists():
        logger.error(f"Devlog not found: {devlog_path}")
        return None

    # Read devlog
    with open(devlog_path, 'r') as f:
        content = f.read()

    # Extract title from first line
    lines = content.split('\n')
    title = "Untitled Devlog"
    for line in lines[:5]:
        if line.strip().startswith('#'):
            title = line.strip().lstrip('#').strip()
            break

    # Initialize in pipeline
    return processor.initialize_content(title, content)

def get_content_status_report(content_id: str) -> Dict[str, Any]:
    """Generate status report for content"""
    pipeline = ContentPipeline()
    processor = ContentPipelineProcessor(pipeline)

    metadata = processor.get_pipeline_status(content_id)
    if not metadata:
        return {'error': f'Content not found: {content_id}'}

    return {
        'content_id': metadata.content_id,
        'title': metadata.title,
        'current_status': metadata.current_status.value,
        'category': metadata.category.value if metadata.category else None,
        'questline': metadata.questline.value if metadata.questline else None,
        'mission_type': metadata.mission_type.value if metadata.mission_type else None,
        'status_history': metadata.status_history,
        'artifacts': metadata.artifacts,
        'selected_template': metadata.selected_template
    }

# CLI interface for pipeline management
def main():
    """CLI interface for content pipeline"""
    import argparse

    parser = argparse.ArgumentParser(description="Digital Dreamscape Content Pipeline")
    parser.add_argument('action', choices=['init', 'status', 'advance', 'list'])
    parser.add_argument('--content-id', help='Content ID')
    parser.add_argument('--title', help='Content title')
    parser.add_argument('--devlog-path', help='Path to devlog file')
    parser.add_argument('--status', choices=[s.value for s in PipelineStatus], help='Target status')

    args = parser.parse_args()

    pipeline = ContentPipeline()
    processor = ContentPipelineProcessor(pipeline)

    if args.action == 'init':
        if args.devlog_path:
            metadata = create_content_from_devlog(Path(args.devlog_path))
            if metadata:
                print(f"Created content: {metadata.content_id}")
            else:
                print("Failed to create content")
        elif args.title:
            # Would need raw content input
            print("Title-only init not implemented yet")
        else:
            print("Provide --devlog-path or --title")

    elif args.action == 'status':
        if not args.content_id:
            print("Provide --content-id")
            return

        report = get_content_status_report(args.content_id)
        if 'error' in report:
            print(f"Error: {report['error']}")
        else:
            print(json.dumps(report, indent=2))

    elif args.action == 'advance':
        if not args.content_id or not args.status:
            print("Provide --content-id and --status")
            return

        metadata = processor.get_pipeline_status(args.content_id)
        if not metadata:
            print(f"Content not found: {args.content_id}")
            return

        target_status = PipelineStatus(args.status)
        success = processor.advance_checkpoint(metadata, target_status)

        if success:
            print(f"Advanced {args.content_id} to {target_status.value}")
        else:
            print(f"Failed to advance {args.content_id}")

    elif args.action == 'list':
        # List all content in pipeline
        meta_dir = Path("content/meta")
        if meta_dir.exists():
            for meta_file in meta_dir.glob("*.json"):
                try:
                    with open(meta_file, 'r') as f:
                        data = json.load(f)
                    print(f"{data['content_id']}: {data['title']} ({data['current_status']})")
                except Exception as e:
                    print(f"Error reading {meta_file}: {e}")

if __name__ == "__main__":
    main()