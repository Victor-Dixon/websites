"""
Episode Publishing Service
==========================

Handles publishing processed episodes to WordPress and managing publication workflows.
"""

import requests
from requests.auth import HTTPBasicAuth
from typing import List, Dict, Any, Optional, Tuple
from dataclasses import dataclass
from datetime import datetime
import time
import logging
from urllib.parse import urljoin

from content_processing_service import ProcessedEpisode

logger = logging.getLogger(__name__)

@dataclass
class PublishingResult:
    """Result of a publishing operation"""
    success: bool
    episode_id: str
    post_url: Optional[str] = None
    post_id: Optional[int] = None
    error_message: Optional[str] = None
    published_at: Optional[datetime] = None

@dataclass
class BatchPublishingResult:
    """Result of a batch publishing operation"""
    total_attempted: int
    successful: int
    failed: int
    results: List[PublishingResult]
    batch_duration: float
    started_at: datetime

class WordPressPublishingService:
    """Service for publishing episodes to WordPress"""

    def __init__(self, config: Dict[str, Any]):
        """
        Initialize WordPress publishing service.

        Args:
            config: Configuration containing:
                - wp_url: WordPress site URL
                - wp_user: WordPress username
                - wp_app_pass: WordPress application password
                - timeout: Request timeout (default: 30)
                - retry_attempts: Number of retry attempts (default: 3)
                - retry_delay: Delay between retries (default: 5)
        """
        self.wp_url = config.get('wp_url', 'https://digitaldreamscape.site')
        self.wp_user = config.get('wp_user', 'dadudekc@gmail.com')
        self.wp_app_pass = config.get('wp_app_pass', 'DuFX5WsrzkMPqJC0czhiaZCh')
        self.timeout = config.get('timeout', 30)
        self.retry_attempts = config.get('retry_attempts', 3)
        self.retry_delay = config.get('retry_delay', 5)

        self.auth = HTTPBasicAuth(self.wp_user, self.wp_app_pass)
        self.api_base = urljoin(self.wp_url, '/wp-json/wp/v2/')

    def publish_episode(self, episode: ProcessedEpisode) -> PublishingResult:
        """
        Publish a single episode to WordPress.

        Args:
            episode: Processed episode to publish

        Returns:
            PublishingResult with success/failure details
        """
        episode_id = episode.episode_data.episode_id

        logger.info(f"📝 Publishing episode: {episode.blog_title[:50]}...")

        # Prepare post data
        post_data = self._prepare_post_data(episode)

        # Attempt publishing with retries
        for attempt in range(self.retry_attempts):
            try:
                result = self._publish_to_wordpress(post_data, episode_id)

                if result.success:
                    logger.info(f"✅ Published: {result.post_url}")
                    return result
                else:
                    logger.warning(f"❌ Attempt {attempt + 1} failed: {result.error_message}")

                    if attempt < self.retry_attempts - 1:
                        logger.info(f"⏳ Retrying in {self.retry_delay} seconds...")
                        time.sleep(self.retry_delay)

            except Exception as e:
                logger.error(f"❌ Publishing error on attempt {attempt + 1}: {e}")

                if attempt < self.retry_attempts - 1:
                    time.sleep(self.retry_delay)

        # All attempts failed
        return PublishingResult(
            success=False,
            episode_id=episode_id,
            error_message=f"Failed after {self.retry_attempts} attempts",
            published_at=datetime.now()
        )

    def _publish_to_wordpress(self, post_data: Dict[str, Any], episode_id: str) -> PublishingResult:
        """Execute the actual WordPress API call"""
        api_url = f"{self.api_base}posts"

        try:
            response = requests.post(
                api_url,
                json=post_data,
                auth=self.auth,
                timeout=self.timeout
            )

            if response.status_code == 201:  # Created
                post_data_response = response.json()
                return PublishingResult(
                    success=True,
                    episode_id=episode_id,
                    post_url=post_data_response.get('link'),
                    post_id=post_data_response.get('id'),
                    published_at=datetime.now()
                )
            else:
                return PublishingResult(
                    success=False,
                    episode_id=episode_id,
                    error_message=f"HTTP {response.status_code}: {response.text[:200]}",
                    published_at=datetime.now()
                )

        except requests.exceptions.Timeout:
            return PublishingResult(
                success=False,
                episode_id=episode_id,
                error_message="Request timeout",
                published_at=datetime.now()
            )
        except requests.exceptions.ConnectionError:
            return PublishingResult(
                success=False,
                episode_id=episode_id,
                error_message="Connection error",
                published_at=datetime.now()
            )
        except Exception as e:
            return PublishingResult(
                success=False,
                episode_id=episode_id,
                error_message=f"Unexpected error: {e}",
                published_at=datetime.now()
            )

    def _prepare_post_data(self, episode: ProcessedEpisode) -> Dict[str, Any]:
        """Prepare post data for WordPress API"""
        # Map episode category to WordPress category
        wp_categories = self._map_category_to_wp(episode.episode_data.category)

        # Prepare tags (WordPress requires numeric IDs, so we'll use category-based tagging)
        wp_tags = self._prepare_tags(episode.episode_data.tags)

        return {
            'title': episode.blog_title,
            'content': episode.victor_content,
            'excerpt': episode.excerpt,
            'status': 'publish',
            'categories': wp_categories,
            'tags': wp_tags,
            # Custom fields for episode metadata
            'meta': {
                'episode_id': episode.episode_data.episode_id,
                'content_type': episode.episode_data.content_type,
                'agent_id': episode.episode_data.agent_id,
                'quality_score': episode.quality_score,
                'victor_voice_applied': True
            }
        }

    def _map_category_to_wp(self, category: Any) -> List[int]:
        """Map episode category to WordPress category IDs"""
        # Note: In a real implementation, you'd query WordPress for category IDs
        # For now, we'll use placeholder mappings
        category_mapping = {
            'technical': [1],      # Technology category
            'strategic': [2],      # Business/Strategy category
            'operational': [3],    # Operations category
            'narrative': [4],      # Personal/Stories category
            'learning': [5],       # Education category
            'reflection': [4]      # Personal/Stories category
        }

        # Handle both enum and string categories
        category_key = category.value if hasattr(category, 'value') else str(category)
        return category_mapping.get(category_key, [1])  # Default to first category

    def _prepare_tags(self, tags: List[str]) -> List[int]:
        """Prepare tags for WordPress (would need tag ID mapping in production)"""
        # Placeholder - in production you'd map tag names to WordPress tag IDs
        return []

    def publish_batch(self, episodes: List[ProcessedEpisode], batch_size: int = 5) -> BatchPublishingResult:
        """
        Publish a batch of episodes with rate limiting and error handling.

        Args:
            episodes: List of episodes to publish
            batch_size: Number of episodes per batch

        Returns:
            BatchPublishingResult with comprehensive results
        """
        started_at = datetime.now()
        results = []

        logger.info(f"🚀 Starting batch publication of {len(episodes)} episodes (batch size: {batch_size})")

        # Filter to only publish-ready episodes
        ready_episodes = [ep for ep in episodes if ep.publish_ready]

        if len(ready_episodes) != len(episodes):
            skipped = len(episodes) - len(ready_episodes)
            logger.info(f"⏭️ Skipping {skipped} episodes that are not publish-ready")

        successful = 0
        failed = 0

        # Process in batches
        for i in range(0, len(ready_episodes), batch_size):
            batch = ready_episodes[i:i + batch_size]
            batch_num = (i // batch_size) + 1
            total_batches = (len(ready_episodes) + batch_size - 1) // batch_size

            logger.info(f"🔄 Processing batch {batch_num}/{total_batches} ({len(batch)} episodes)")

            for episode in batch:
                result = self.publish_episode(episode)
                results.append(result)

                if result.success:
                    successful += 1
                else:
                    failed += 1

            # Rate limiting between batches (except for the last batch)
            if i + batch_size < len(ready_episodes):
                logger.info(f"⏳ Waiting 2 seconds before next batch...")
                time.sleep(2)

        batch_duration = (datetime.now() - started_at).total_seconds()

        result = BatchPublishingResult(
            total_attempted=len(ready_episodes),
            successful=successful,
            failed=failed,
            results=results,
            batch_duration=batch_duration,
            started_at=started_at
        )

        logger.info(f"🎉 Batch publishing complete: {successful}/{len(ready_episodes)} successful in {batch_duration:.1f}s")

        return result

    def validate_connection(self) -> Tuple[bool, Optional[str]]:
        """
        Validate WordPress connection and credentials.

        Returns:
            Tuple of (is_valid, error_message)
        """
        try:
            # Try to get site info
            response = requests.get(
                urljoin(self.wp_url, '/wp-json/'),
                auth=self.auth,
                timeout=10
            )

            if response.status_code == 200:
                return True, None
            else:
                return False, f"HTTP {response.status_code}: {response.text[:100]}"

        except Exception as e:
            return False, str(e)

    def get_publishing_stats(self, results: List[PublishingResult]) -> Dict[str, Any]:
        """Generate publishing statistics from results"""
        total = len(results)
        successful = sum(1 for r in results if r.success)
        failed = total - successful

        if results:
            avg_time = sum(
                (r.published_at - results[0].published_at).total_seconds()
                for r in results[1:]
                if r.published_at
            ) / max(1, len(results) - 1)
        else:
            avg_time = 0

        return {
            'total_episodes': total,
            'successful': successful,
            'failed': failed,
            'success_rate': successful / total if total > 0 else 0,
            'average_time_per_episode': avg_time,
            'error_summary': self._summarize_errors(results)
        }

    def _summarize_errors(self, results: List[PublishingResult]) -> Dict[str, int]:
        """Summarize error types from publishing results"""
        errors = {}
        for result in results:
            if not result.success and result.error_message:
                error_type = self._categorize_error(result.error_message)
                errors[error_type] = errors.get(error_type, 0) + 1

        return errors

    def _categorize_error(self, error_message: str) -> str:
        """Categorize error messages for reporting"""
        error_lower = error_message.lower()

        if 'timeout' in error_lower:
            return 'timeout'
        elif 'connection' in error_lower:
            return 'connection'
        elif '401' in error_message or 'unauthorized' in error_lower:
            return 'authentication'
        elif '403' in error_message or 'forbidden' in error_lower:
            return 'permission'
        elif '500' in error_message:
            return 'server_error'
        else:
            return 'other'