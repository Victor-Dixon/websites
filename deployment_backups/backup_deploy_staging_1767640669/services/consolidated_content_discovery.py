#!/usr/bin/env python3
"""
Consolidated Content Discovery Service
=====================================

UNIFIED CONTENT DISCOVERY - Phase 3 Consolidation
Consolidates content discovery logic from 20+ files into 1 core service.

CONSOLIDATED FROM:
- content_discovery_service.py (primary service)
- mass_episode_processor_v2.py (embedded discovery logic)
- generate_devlog_episodes.py (devlog discovery)
- setup_dreamscape_autoblogger.py (autoblogger discovery)
- publish_dreamscape_episodes.py (episode discovery)
- Plus additional scattered discovery patterns

PROVIDES:
- Unified content discovery across all sources
- Support for devlogs, agent workspaces, message queues
- Intelligent content prioritization and filtering
- Backward compatibility with existing systems
"""

import os
import json
import re
from pathlib import Path
from typing import List, Dict, Any, Optional, Iterator, Union
from dataclasses import dataclass, field
from datetime import datetime
from enum import Enum
import logging

logger = logging.getLogger(__name__)


class ContentSourceType(Enum):
    """Types of content sources"""
    DEVLOG = "devlog"
    AGENT_WORKSPACE = "agent_workspace"
    MESSAGE_QUEUE = "message_queue"
    AUTOBLOGGER = "autoblogger"
    EPISODE_DRAFT = "episode_draft"
    BLOG_POST = "blog_post"


class ContentPriority(Enum):
    """Content processing priority levels"""
    CRITICAL = "critical"      # Immediate processing required
    HIGH = "high"             # Important content
    MEDIUM = "medium"         # Standard content
    LOW = "low"              # Background processing
    ARCHIVE = "archive"       # Historical content only


@dataclass
class ContentSource:
    """Represents a discovered content source - extended version"""
    path: Path
    source_type: ContentSourceType
    agent_id: Optional[str] = None
    last_modified: Optional[datetime] = None
    file_count: int = 0
    priority: ContentPriority = ContentPriority.MEDIUM
    metadata: Dict[str, Any] = field(default_factory=dict)


@dataclass
class DiscoveryResult:
    """Results of content discovery operation - extended version"""
    sources: List[ContentSource]
    total_files: int
    discovery_time: datetime
    errors: List[str] = field(default_factory=list)
    prioritized_sources: List[ContentSource] = field(default_factory=list)


@dataclass
class ContentItem:
    """Individual content item discovered"""
    source_path: Path
    relative_path: str
    content_type: str
    title: Optional[str] = None
    created_date: Optional[datetime] = None
    modified_date: Optional[datetime] = None
    size_bytes: int = 0
    priority: ContentPriority = ContentPriority.MEDIUM
    metadata: Dict[str, Any] = field(default_factory=dict)


class ConsolidatedContentDiscoveryService:
    """
    UNIFIED CONTENT DISCOVERY SERVICE
    ================================

    Consolidates all content discovery logic from 20+ files into a single,
    comprehensive service with backward compatibility.
    """

    def __init__(self, config: Optional[Dict[str, Any]] = None):
        """Initialize with configuration"""
        self.config = config or self._get_default_config()
        self._setup_source_patterns()
        self._setup_priority_rules()

    def _get_default_config(self) -> Dict[str, Any]:
        """Get default configuration"""
        return {
            'content_roots': [
                Path('devlogs'),
                Path('agent_workspaces'),
                Path('generated_content'),
                Path('src/autoblogger/ssot/content')
            ],
            'file_patterns': {
                'devlog': ['*.md', '*.txt'],
                'episode': ['*.md', '*.txt'],
                'blog_post': ['*.md', '*.html'],
                'message': ['*.json', '*.md']
            },
            'exclude_patterns': [
                '**/archive/**',
                '**/backup/**',
                '**/node_modules/**',
                '**/.git/**',
                '**/.*'
            ],
            'max_files_per_source': 100,
            'min_file_age_hours': 1  # Don't process files modified in last hour
        }

    def _setup_source_patterns(self):
        """Setup patterns for identifying content sources"""
        self.source_patterns = {
            ContentSourceType.DEVLOG: [
                re.compile(r'devlogs?/?', re.IGNORECASE),
                re.compile(r'development.*logs?/?', re.IGNORECASE)
            ],
            ContentSourceType.AGENT_WORKSPACE: [
                re.compile(r'agent_workspaces?/?', re.IGNORECASE),
                re.compile(r'agents?/?', re.IGNORECASE)
            ],
            ContentSourceType.MESSAGE_QUEUE: [
                re.compile(r'messages?/?|queues?/?', re.IGNORECASE),
                re.compile(r'inbox/?', re.IGNORECASE)
            ],
            ContentSourceType.AUTOBLOGGER: [
                re.compile(r'autoblogger/?|ssot/?', re.IGNORECASE),
                re.compile(r'content/?', re.IGNORECASE)
            ]
        }

    def _setup_priority_rules(self):
        """Setup rules for content prioritization"""
        self.priority_rules = {
            # Critical priority patterns
            ContentPriority.CRITICAL: [
                re.compile(r'emergency|critical|urgent', re.IGNORECASE),
                re.compile(r'security|breach|exploit', re.IGNORECASE),
                re.compile(r'down|offline|broken', re.IGNORECASE)
            ],
            # High priority patterns
            ContentPriority.HIGH: [
                re.compile(r'bug.*fix|issue.*resolution', re.IGNORECASE),
                re.compile(r'new.*feature|enhancement', re.IGNORECASE),
                re.compile(r'performance|optimization', re.IGNORECASE)
            ],
            # Medium priority (default)
            ContentPriority.MEDIUM: [],
            # Low priority patterns
            ContentPriority.LOW: [
                re.compile(r'documentation|docs|readme', re.IGNORECASE),
                re.compile(r'test|spec|example', re.IGNORECASE)
            ]
        }

    def discover_all_content(self) -> DiscoveryResult:
        """
        COMPREHENSIVE CONTENT DISCOVERY
        ==============================

        Consolidated discovery logic from:
        - content_discovery_service.py (file system scanning)
        - mass_episode_processor_v2.py (source enumeration)
        - generate_devlog_episodes.py (devlog discovery)
        - setup_dreamscape_autoblogger.py (content source detection)

        Returns:
            DiscoveryResult with all found content sources
        """
        discovery_start = datetime.now()
        sources = []
        errors = []

        try:
            # Discover from configured roots
            for root_path in self.config['content_roots']:
                if root_path.exists():
                    try:
                        root_sources = self._discover_from_root(root_path)
                        sources.extend(root_sources)
                    except Exception as e:
                        errors.append(f"Error discovering from {root_path}: {e}")

            # Sort sources by priority
            sources.sort(key=lambda s: s.priority.value, reverse=True)

            # Separate prioritized sources (critical + high)
            prioritized = [s for s in sources
                         if s.priority in [ContentPriority.CRITICAL, ContentPriority.HIGH]]

        except Exception as e:
            errors.append(f"Discovery failed: {e}")
            sources = []
            prioritized = []

        return DiscoveryResult(
            sources=sources,
            total_files=sum(s.file_count for s in sources),
            discovery_time=discovery_start,
            errors=errors,
            prioritized_sources=prioritized
        )

    def _discover_from_root(self, root_path: Path) -> List[ContentSource]:
        """Discover content sources from a root directory"""
        sources = []

        # Check if root itself is a content source
        root_source = self._classify_path(root_path)
        if root_source:
            self._populate_source_metadata(root_source)
            sources.append(root_source)

        # Recursively find sub-sources
        try:
            for item in root_path.rglob('*'):
                if item.is_dir() and not self._is_excluded(item):
                    source = self._classify_path(item)
                    if source:
                        self._populate_source_metadata(source)
                        sources.append(source)
        except PermissionError:
            logger.warning(f"Permission denied accessing {root_path}")

        return sources

    def _classify_path(self, path: Path) -> Optional[ContentSource]:
        """Classify a path as a content source"""
        path_str = str(path).lower()

        # Check exclusion patterns
        if self._is_excluded(path):
            return None

        # Determine source type
        source_type = None
        for stype, patterns in self.source_patterns.items():
            if any(pattern.search(path_str) for pattern in patterns):
                source_type = stype
                break

        # Default classification based on path structure
        if not source_type:
            if 'devlog' in path_str:
                source_type = ContentSourceType.DEVLOG
            elif 'agent' in path_str or 'workspace' in path_str:
                source_type = ContentSourceType.AGENT_WORKSPACE
            elif 'message' in path_str or 'inbox' in path_str:
                source_type = ContentSourceType.MESSAGE_QUEUE
            elif 'episode' in path_str or 'draft' in path_str:
                source_type = ContentSourceType.EPISODE_DRAFT

        if source_type:
            # Extract agent ID if applicable
            agent_id = None
            if source_type == ContentSourceType.AGENT_WORKSPACE:
                # Look for Agent-X pattern in path
                agent_match = re.search(r'Agent-(\d+)', str(path), re.IGNORECASE)
                if agent_match:
                    agent_id = f"Agent-{agent_match.group(1)}"

            return ContentSource(
                path=path,
                source_type=source_type,
                agent_id=agent_id
            )

        return None

    def _is_excluded(self, path: Path) -> bool:
        """Check if path should be excluded"""
        path_str = str(path)
        for pattern in self.config['exclude_patterns']:
            if path.match(pattern):
                return True
        return False

    def _populate_source_metadata(self, source: ContentSource):
        """Populate metadata for a content source"""
        try:
            if source.path.exists():
                # Count files
                file_patterns = self.config['file_patterns'].get(source.source_type.value, ['*'])
                file_count = 0

                for pattern in file_patterns:
                    file_count += len(list(source.path.glob(f'**/{pattern}')))

                source.file_count = file_count

                # Get last modified time
                if source.path.is_file():
                    source.last_modified = datetime.fromtimestamp(source.path.stat().st_mtime)
                else:
                    # For directories, get most recent file modification
                    newest_time = None
                    for pattern in file_patterns:
                        for file_path in source.path.glob(f'**/{pattern}'):
                            try:
                                file_time = datetime.fromtimestamp(file_path.stat().st_mtime)
                                if newest_time is None or file_time > newest_time:
                                    newest_time = file_time
                            except OSError:
                                continue
                    source.last_modified = newest_time

                # Determine priority
                source.priority = self._calculate_priority(source)

        except Exception as e:
            logger.warning(f"Error populating metadata for {source.path}: {e}")

    def _calculate_priority(self, source: ContentSource) -> ContentPriority:
        """Calculate processing priority for a source"""
        path_str = str(source.path).lower()

        # Check priority patterns
        for priority, patterns in self.priority_rules.items():
            if any(pattern.search(path_str) for pattern in patterns):
                return priority

        # Default priorities based on source type
        type_priorities = {
            ContentSourceType.MESSAGE_QUEUE: ContentPriority.HIGH,
            ContentSourceType.DEVLOG: ContentPriority.MEDIUM,
            ContentSourceType.AGENT_WORKSPACE: ContentPriority.MEDIUM,
            ContentSourceType.AUTOBLOGGER: ContentPriority.LOW,
            ContentSourceType.EPISODE_DRAFT: ContentPriority.MEDIUM
        }

        return type_priorities.get(source.source_type, ContentPriority.MEDIUM)

    def get_content_iterator(self, source: ContentSource) -> Iterator[ContentItem]:
        """
        GET CONTENT ITERATOR
        ===================

        Consolidated iterator logic from:
        - content_discovery_service.py (file iteration)
        - mass_episode_processor_v2.py (content processing)
        - generate_devlog_episodes.py (devlog iteration)

        Yields ContentItem objects for processing
        """
        file_patterns = self.config['file_patterns'].get(source.source_type.value, ['*'])

        for pattern in file_patterns:
            try:
                for file_path in source.path.glob(f'**/{pattern}'):
                    if file_path.is_file() and not self._is_excluded(file_path):
                        try:
                            stat = file_path.stat()
                            modified_time = datetime.fromtimestamp(stat.st_mtime)

                            # Skip recently modified files
                            min_age = self.config.get('min_file_age_hours', 1)
                            if (datetime.now() - modified_time).total_seconds() < (min_age * 3600):
                                continue

                            # Create content item
                            content_item = ContentItem(
                                source_path=file_path,
                                relative_path=str(file_path.relative_to(source.path)),
                                content_type=self._detect_content_type(file_path),
                                modified_date=modified_time,
                                size_bytes=stat.st_size,
                                priority=source.priority
                            )

                            # Extract additional metadata
                            self._extract_content_metadata(content_item)

                            yield content_item

                        except OSError as e:
                            logger.warning(f"Error processing {file_path}: {e}")
                            continue

            except Exception as e:
                logger.error(f"Error iterating pattern {pattern} in {source.path}: {e}")

    def _detect_content_type(self, file_path: Path) -> str:
        """Detect content type from file path and content"""
        # Based on file extension
        suffix = file_path.suffix.lower()
        if suffix == '.md':
            return 'markdown'
        elif suffix == '.txt':
            return 'text'
        elif suffix == '.json':
            return 'json'
        elif suffix in ['.html', '.htm']:
            return 'html'
        else:
            return 'unknown'

    def _extract_content_metadata(self, item: ContentItem):
        """Extract metadata from content item"""
        try:
            if item.content_type == 'markdown':
                # Extract title from first heading
                with open(item.source_path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read(1000)  # Read first 1000 chars
                    title_match = re.search(r'^#\s+(.+)$', content, re.MULTILINE)
                    if title_match:
                        item.title = title_match.group(1).strip()

            elif item.content_type == 'json':
                # Extract metadata from JSON
                with open(item.source_path, 'r', encoding='utf-8', errors='ignore') as f:
                    try:
                        data = json.load(f)
                        item.title = data.get('title')
                        item.metadata.update(data)
                    except json.JSONDecodeError:
                        pass

        except Exception as e:
            logger.warning(f"Error extracting metadata from {item.source_path}: {e}")

    def filter_content(self, items: List[ContentItem],
                      priority_filter: Optional[ContentPriority] = None,
                      type_filter: Optional[str] = None,
                      date_filter: Optional[datetime] = None) -> List[ContentItem]:
        """
        FILTER CONTENT
        =============

        Consolidated filtering logic from various discovery implementations
        """
        filtered = items

        if priority_filter:
            filtered = [item for item in filtered if item.priority == priority_filter]

        if type_filter:
            filtered = [item for item in filtered if item.content_type == type_filter]

        if date_filter:
            filtered = [item for item in filtered
                       if item.modified_date and item.modified_date > date_filter]

        return filtered

    # BACKWARD COMPATIBILITY METHODS
    # ==============================

    def discover_content_sources(self) -> List[ContentSource]:
        """Legacy method for backward compatibility"""
        result = self.discover_all_content()
        return result.sources

    def get_all_content_files(self) -> List[Path]:
        """Legacy method for backward compatibility"""
        result = self.discover_all_content()
        all_files = []
        for source in result.sources:
            for item in self.get_content_iterator(source):
                all_files.append(item.source_path)
        return all_files


# BACKWARD COMPATIBILITY IMPORTS
# ==============================

# For content_discovery_service.py compatibility
ContentDiscoveryService = ConsolidatedContentDiscoveryService
DiscoveryResult = DiscoveryResult  # Re-export for compatibility

# Global instance for singleton access
_discovery_service = None

def get_content_discovery_service(config: Optional[Dict[str, Any]] = None) -> ConsolidatedContentDiscoveryService:
    """Get singleton instance of the consolidated content discovery service"""
    global _discovery_service
    if _discovery_service is None:
        _discovery_service = ConsolidatedContentDiscoveryService(config)
    return _discovery_service


if __name__ == "__main__":
    # Quick test
    service = ConsolidatedContentDiscoveryService()

    print("🔍 CONSOLIDATED CONTENT DISCOVERY TEST")
    print("=" * 50)

    # Discover all content
    result = service.discover_all_content()

    print(f"📁 Sources Found: {len(result.sources)}")
    print(f"📄 Total Files: {result.total_files}")
    print(f"⏱️  Discovery Time: {result.discovery_time}")
    print(f"🚨 Errors: {len(result.errors)}")

    if result.errors:
        print("\n❌ Errors:")
        for error in result.errors[:3]:  # Show first 3 errors
            print(f"  • {error}")

    print(f"\n🎯 Prioritized Sources: {len(result.prioritized_sources)}")

    # Show top sources by file count
    top_sources = sorted(result.sources, key=lambda s: s.file_count, reverse=True)[:5]

    print("\n📊 Top Content Sources:")
    for i, source in enumerate(top_sources, 1):
        print(f"{i}. {source.path.name} ({source.source_type.value}) - {source.file_count} files")

    # Show sample content items from first source
    if result.sources:
        first_source = result.sources[0]
        print(f"\n📝 Sample Content from {first_source.path.name}:")
        items = list(service.get_content_iterator(first_source))
        for item in items[:3]:  # Show first 3 items
            title = item.title or "Untitled"
            print(f"  • {title} ({item.content_type}) - {item.size_bytes} bytes")

    print("\n🎉 Content discovery consolidation test complete!")