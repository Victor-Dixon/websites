"""
Content Discovery Service
========================

Responsible for discovering and cataloging all available content sources
for episode processing.
"""

import os
import json
import re
from pathlib import Path
from typing import List, Dict, Any, Optional, Iterator
from dataclasses import dataclass
from datetime import datetime
import logging

logger = logging.getLogger(__name__)

@dataclass
class ContentSource:
    """Represents a discovered content source"""
    path: Path
    source_type: str  # 'devlog', 'agent_workspace', 'message_queue'
    agent_id: Optional[str] = None
    last_modified: Optional[datetime] = None
    file_count: int = 0

@dataclass
class DiscoveryResult:
    """Results of content discovery operation"""
    sources: List[ContentSource]
    total_files: int
    discovery_time: datetime
    errors: List[str] = None

    def __post_init__(self):
        if self.errors is None:
            self.errors = []

class ContentDiscoveryService:
    """
    Service for discovering content across multiple sources.

    This service replaces the scattered discovery logic in MassEpisodeProcessor
    with a centralized, testable component.
    """

    def __init__(self, config: Dict[str, Any]):
        """
        Initialize discovery service with configuration.

        Args:
            config: Configuration dictionary containing:
                - base_paths: Dict of named paths (main_devlogs, agent_workspaces, etc.)
                - file_patterns: List of file patterns to include
                - exclude_patterns: List of patterns to exclude
                - max_depth: Maximum directory traversal depth
        """
        self.config = config
        self.base_paths = config.get('base_paths', {})
        self.file_patterns = config.get('file_patterns', ['*.md', '*.json'])
        self.exclude_patterns = config.get('exclude_patterns', [])
        self.max_depth = config.get('max_depth', 3)

    def discover_all_content(self) -> DiscoveryResult:
        """
        Discover all available content sources.

        Returns:
            DiscoveryResult with all discovered sources and metadata
        """
        logger.info("🔍 Starting comprehensive content discovery")
        start_time = datetime.now()

        all_sources = []
        total_files = 0
        errors = []

        # Discover main devlogs
        try:
            main_sources = self._discover_main_devlogs()
            all_sources.extend(main_sources)
            total_files += sum(source.file_count for source in main_sources)
        except Exception as e:
            error_msg = f"Failed to discover main devlogs: {e}"
            logger.error(error_msg)
            errors.append(error_msg)

        # Discover agent workspaces
        try:
            agent_sources = self._discover_agent_workspaces()
            all_sources.extend(agent_sources)
            total_files += sum(source.file_count for source in agent_sources)
        except Exception as e:
            error_msg = f"Failed to discover agent workspaces: {e}"
            logger.error(error_msg)
            errors.append(error_msg)

        # Discover message queues
        try:
            queue_sources = self._discover_message_queues()
            all_sources.extend(queue_sources)
            total_files += sum(source.file_count for source in queue_sources)
        except Exception as e:
            error_msg = f"Failed to discover message queues: {e}"
            logger.error(error_msg)
            errors.append(error_msg)

        discovery_time = datetime.now() - start_time
        logger.info(".2f")

        return DiscoveryResult(
            sources=all_sources,
            total_files=total_files,
            discovery_time=datetime.now(),
            errors=errors
        )

    def _discover_main_devlogs(self) -> List[ContentSource]:
        """Discover main devlogs directory"""
        sources = []

        devlogs_path = self.base_paths.get('main_devlogs')
        if not devlogs_path or not Path(devlogs_path).exists():
            logger.warning(f"Main devlogs path not found: {devlogs_path}")
            return sources

        devlogs_path = Path(devlogs_path)

        # Count markdown files
        md_files = list(devlogs_path.glob('*.md'))
        file_count = len(md_files)

        if file_count > 0:
            # Get last modified time from most recent file
            last_modified = max((f.stat().st_mtime for f in md_files), default=None)
            last_modified_dt = datetime.fromtimestamp(last_modified) if last_modified else None

            source = ContentSource(
                path=devlogs_path,
                source_type='devlog',
                agent_id=None,
                last_modified=last_modified_dt,
                file_count=file_count
            )
            sources.append(source)

            logger.info(f"📁 Found {file_count} devlogs in main directory")

        return sources

    def _discover_agent_workspaces(self) -> List[ContentSource]:
        """Discover agent workspace devlogs"""
        sources = []

        workspaces_path = self.base_paths.get('agent_workspaces')
        if not workspaces_path or not Path(workspaces_path).exists():
            logger.warning(f"Agent workspaces path not found: {workspaces_path}")
            return sources

        workspaces_path = Path(workspaces_path)
        total_files = 0

        for workspace_dir in workspaces_path.iterdir():
            if not workspace_dir.is_dir():
                continue

            # Extract agent ID from directory name
            agent_id = self._extract_agent_id(workspace_dir.name)
            if not agent_id:
                continue

            # Look for devlogs subdirectory
            devlogs_dir = workspace_dir / 'devlogs'
            if not devlogs_dir.exists():
                continue

            # Count files in devlogs directory
            file_count = len(list(devlogs_dir.glob('*.md')))

            if file_count > 0:
                # Get last modified time
                md_files = list(devlogs_dir.glob('*.md'))
                last_modified = max((f.stat().st_mtime for f in md_files), default=None)
                last_modified_dt = datetime.fromtimestamp(last_modified) if last_modified else None

                source = ContentSource(
                    path=devlogs_dir,
                    source_type='agent_workspace',
                    agent_id=agent_id,
                    last_modified=last_modified_dt,
                    file_count=file_count
                )
                sources.append(source)
                total_files += file_count

                logger.debug(f"  📁 Agent {agent_id}: {file_count} devlogs")

        logger.info(f"📁 Found {total_files} devlogs across {len(sources)} agent workspaces")
        return sources

    def _discover_message_queues(self) -> List[ContentSource]:
        """Discover message queue files"""
        sources = []

        queue_path = self.base_paths.get('message_queue')
        if not queue_path or not Path(queue_path).exists():
            logger.warning(f"Message queue path not found: {queue_path}")
            return sources

        queue_path = Path(queue_path)

        # Look for queue.json
        queue_file = queue_path / 'queue.json'
        if queue_file.exists():
            try:
                # Try to count messages in queue
                with open(queue_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)

                # Handle different queue formats
                if isinstance(data, list):
                    file_count = len(data)
                elif isinstance(data, dict) and 'messages' in data:
                    file_count = len(data['messages'])
                else:
                    file_count = 1  # Single file

                last_modified = queue_file.stat().st_mtime
                last_modified_dt = datetime.fromtimestamp(last_modified)

                source = ContentSource(
                    path=queue_file,
                    source_type='message_queue',
                    agent_id=None,
                    last_modified=last_modified_dt,
                    file_count=file_count
                )
                sources.append(source)

                logger.info(f"📁 Found message queue with {file_count} messages")

            except Exception as e:
                logger.error(f"Error reading message queue: {e}")

        return sources

    def _extract_agent_id(self, dirname: str) -> Optional[str]:
        """Extract agent ID from directory name"""
        # Handle patterns like "Agent-1", "agent_1", "Agent1"
        patterns = [
            r'^Agent-(\d+)$',
            r'^agent_(\d+)$',
            r'^Agent(\d+)$',
            r'^agent(\d+)$'
        ]

        for pattern in patterns:
            match = re.search(pattern, dirname, re.IGNORECASE)
            if match:
                return match.group(1)

        return None

    def get_content_iterator(self, source: ContentSource) -> Iterator[Dict[str, Any]]:
        """
        Get an iterator for content from a specific source.

        Yields:
            Dict containing content metadata and raw content
        """
        if source.source_type == 'devlog':
            yield from self._iterate_devlog_files(source.path)
        elif source.source_type == 'agent_workspace':
            yield from self._iterate_devlog_files(source.path, source.agent_id)
        elif source.source_type == 'message_queue':
            yield from self._iterate_message_queue(source.path)

    def _iterate_devlog_files(self, directory: Path, agent_id: Optional[str] = None) -> Iterator[Dict[str, Any]]:
        """Iterate through devlog markdown files"""
        for md_file in directory.glob('*.md'):
            try:
                with open(md_file, 'r', encoding='utf-8') as f:
                    content = f.read()

                yield {
                    'file_path': str(md_file),
                    'content_type': 'devlog',
                    'agent_id': agent_id,
                    'raw_content': content,
                    'last_modified': datetime.fromtimestamp(md_file.stat().st_mtime),
                    'size': md_file.stat().st_size
                }

            except Exception as e:
                logger.error(f"Error reading {md_file}: {e}")
                continue

    def _iterate_message_queue(self, queue_file: Path) -> Iterator[Dict[str, Any]]:
        """Iterate through message queue items"""
        try:
            with open(queue_file, 'r', encoding='utf-8') as f:
                data = json.load(f)

            # Handle different queue formats
            messages = []
            if isinstance(data, list):
                messages = data
            elif isinstance(data, dict) and 'messages' in data:
                messages = data['messages']

            for msg_data in messages:
                yield {
                    'file_path': str(queue_file),
                    'content_type': 'coordination',
                    'agent_id': None,
                    'raw_content': msg_data.get('message', {}).get('content', ''),
                    'timestamp': msg_data.get('created_at', ''),
                    'size': len(str(msg_data))
                }

        except Exception as e:
            logger.error(f"Error reading message queue {queue_file}: {e}")

    def validate_sources(self, sources: List[ContentSource]) -> Dict[str, Any]:
        """
        Validate discovered sources for accessibility and integrity.

        Returns:
            Dict with validation results
        """
        validation_results = {
            'valid_sources': [],
            'invalid_sources': [],
            'warnings': [],
            'total_valid_files': 0
        }

        for source in sources:
            try:
                if not source.path.exists():
                    validation_results['invalid_sources'].append({
                        'source': source,
                        'error': 'Path does not exist'
                    })
                    continue

                # Check if we can read the source
                if source.path.is_file():
                    # For files, try to read first few bytes
                    with open(source.path, 'r', encoding='utf-8') as f:
                        f.read(100)
                else:
                    # For directories, check if we can list contents
                    list(source.path.iterdir())

                validation_results['valid_sources'].append(source)
                validation_results['total_valid_files'] += source.file_count

            except Exception as e:
                validation_results['invalid_sources'].append({
                    'source': source,
                    'error': str(e)
                })

        return validation_results