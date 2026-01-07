#!/usr/bin/env python3
"""
Mass Episode Processor V2 - Refactored Architecture
==================================================

Improved version with proper service separation, better quality metrics,
and enhanced Victor voice processing.

Key Improvements:
- Service-oriented architecture with single responsibilities
- Advanced quality metrics with 12 assessment criteria
- Sophisticated Victor voice transformation
- Comprehensive configuration management
- Better error handling and logging
- Modular design for easier testing and extension
"""

import sys
import argparse
from pathlib import Path
from typing import Dict, Any, List, Optional
from datetime import datetime
import logging

# Import our services
from config_service import ConfigurationService, ProcessingConfig
# Consolidated content discovery - replaces content_discovery_service
from consolidated_content_discovery import ConsolidatedContentDiscoveryService, DiscoveryResult
from content_processing_service import ContentProcessingService, ProcessedEpisode
from episode_publishing_service import WordPressPublishingService, BatchPublishingResult
# Consolidated quality assessment - replaces episode_quality_scorer
from consolidated_quality_assessment import ConsolidatedQualityAssessmentService

class MassEpisodeProcessorV2:
    """
    Refactored episode processor using service-oriented architecture.

    This version addresses the issues in the original:
    - Single massive class split into focused services
    - Improved quality metrics with better episode qualification
    - Enhanced Victor voice processing
    - Proper configuration management
    - Better error handling and logging
    """

    def __init__(self, config: Optional[ProcessingConfig] = None):
        """
        Initialize the processor with configuration.

        Args:
            config: Optional configuration override
        """
        self.config = config or ConfigurationService().get_config()
        self._setup_logging()

        # Initialize services
        service_configs = ConfigurationService().get_service_configs()

        self.discovery_service = ConsolidatedContentDiscoveryService(service_configs.get('content_discovery'))
        self.quality_scorer = ConsolidatedQualityAssessmentService()
        self.processing_service = ContentProcessingService(self.quality_scorer)
        self.publishing_service = WordPressPublishingService(service_configs['wordpress_publishing'])

        # Update processing service threshold
        self.processing_service.quality_threshold = self.config.quality_threshold

        logger.info("🎬 Mass Episode Processor V2 initialized")

    def _setup_logging(self):
        """Setup logging configuration"""
        log_level = getattr(logging, self.config.log_level.upper(), logging.INFO)

        logging.basicConfig(
            level=log_level,
            format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
            handlers=[
                logging.StreamHandler(),
                *(logging.FileHandler(self.config.log_file) for _ in [self.config.log_file] if self.config.log_file)
            ]
        )

    def run_mass_processing(self, batch_size: Optional[int] = None,
                          max_episodes: Optional[int] = None) -> Dict[str, Any]:
        """
        Run the complete mass processing pipeline.

        Args:
            batch_size: Override default batch size
            max_episodes: Override default max episodes

        Returns:
            Processing results summary
        """
        batch_size = batch_size or self.config.batch_size
        max_episodes = max_episodes or self.config.max_episodes

        logger.info("🎬 DIGITAL DREAMSCAPE - MASS EPISODE PROCESSING V2")
        logger.info("=" * 60)

        start_time = datetime.now()

        try:
            # Step 1: Discover content
            discovery_result = self.discovery_service.discover_all_content()
            self._log_discovery_results(discovery_result)

            if not discovery_result.sources:
                return {'error': 'No content sources discovered'}

            # Step 2: Process discovered content
            episodes_data = self._process_discovered_content(discovery_result, max_episodes)

            if not episodes_data:
                return {'error': 'No episodes could be processed'}

            # Step 3: Apply Victor voice and quality assessment
            processed_episodes = self._apply_victor_transformations(episodes_data)

            # Step 4: Quality filtering
            quality_results = self._filter_by_quality(processed_episodes)

            # Step 5: Batch publishing
            publishing_results = self._publish_ready_episodes(quality_results['ready_episodes'], batch_size)

            # Step 6: Generate comprehensive report
            processing_duration = (datetime.now() - start_time).total_seconds()
            result = self._generate_processing_report(
                discovery_result,
                quality_results,
                publishing_results,
                processing_duration
            )

            self._log_final_results(result)
            return result

        except Exception as e:
            logger.error(f"❌ Processing failed: {e}", exc_info=True)
            return {'error': str(e), 'partial_results': locals().get('result', {})}

    def _process_discovered_content(self, discovery_result: DiscoveryResult,
                                  max_episodes: int) -> List[Dict[str, Any]]:
        """Process raw content from discovery results"""
        logger.info("🔬 Processing discovered content...")

        episodes_data = []
        processed_count = 0

        for source in discovery_result.sources:
            logger.debug(f"Processing source: {source.path}")

            for raw_content in self.discovery_service.get_content_iterator(source):
                if processed_count >= max_episodes:
                    break

                episode = self.processing_service.process_raw_content(raw_content)
                if episode:
                    episodes_data.append(episode)
                    processed_count += 1

                if processed_count >= max_episodes:
                    break

        logger.info(f"📊 Processed {len(episodes_data)} episodes from {len(discovery_result.sources)} sources")
        return episodes_data

    def _apply_victor_transformations(self, episodes_data: List[Dict[str, Any]]) -> List[ProcessedEpisode]:
        """Apply Victor voice transformations to episodes"""
        logger.info("🎭 Applying Victor's voice transformations...")

        processed_episodes = []
        for episode_data in episodes_data:
            try:
                processed = self.processing_service.apply_victor_transformation(episode_data)
                processed_episodes.append(processed)
            except Exception as e:
                logger.error(f"Failed to process episode {episode_data.get('episode_id', 'unknown')}: {e}")

        logger.info(f"✅ Applied Victor voice to {len(processed_episodes)} episodes")
        return processed_episodes

    def _filter_by_quality(self, processed_episodes: List[ProcessedEpisode]) -> Dict[str, Any]:
        """Filter episodes by quality criteria"""
        logger.info("🎯 Applying quality filtering...")

        ready_episodes = []
        rejected_episodes = []

        for episode in processed_episodes:
            if episode.publish_ready:
                ready_episodes.append(episode)
            else:
                rejected_episodes.append(episode)

        # Log quality distribution
        total = len(processed_episodes)
        ready_count = len(ready_episodes)
        rejected_count = len(rejected_episodes)

        logger.info(f"✅ Quality Approved: {ready_count}/{total} episodes ({ready_count/total*100:.1f}%)")

        if rejected_episodes:
            logger.info(f"❌ Filtered out: {rejected_count} low-quality episodes")

            # Show top rejected episodes
            top_rejected = sorted(rejected_episodes, key=lambda x: x.quality_score, reverse=True)[:5]
            for ep in top_rejected:
                logger.debug(f"   • {ep.episode_data.title[:50]}... (score: {ep.quality_score:.2f})")

        return {
            'ready_episodes': ready_episodes,
            'rejected_episodes': rejected_episodes,
            'quality_distribution': {
                'total': total,
                'ready': ready_count,
                'rejected': rejected_count,
                'success_rate': ready_count / total if total > 0 else 0
            }
        }

    def _publish_ready_episodes(self, ready_episodes: List[ProcessedEpisode],
                              batch_size: int) -> BatchPublishingResult:
        """Publish ready episodes in batches"""
        if not ready_episodes:
            logger.info("⏭️ No episodes ready for publishing")
            return BatchPublishingResult(
                total_attempted=0,
                successful=0,
                failed=0,
                results=[],
                batch_duration=0.0,
                started_at=datetime.now()
            )

        logger.info(f"🚀 Publishing {len(ready_episodes)} ready episodes...")
        return self.publishing_service.publish_batch(ready_episodes, batch_size)

    def _generate_processing_report(self, discovery: DiscoveryResult,
                                  quality: Dict[str, Any],
                                  publishing: BatchPublishingResult,
                                  duration: float) -> Dict[str, Any]:
        """Generate comprehensive processing report"""
        return {
            'processing_timestamp': datetime.now().isoformat(),
            'duration_seconds': duration,

            'discovery': {
                'total_sources': len(discovery.sources),
                'total_files': discovery.total_files,
                'errors': len(discovery.errors)
            },

            'processing': {
                'episodes_processed': len(quality['ready_episodes']) + len(quality['rejected_episodes']),
                'quality_passed': len(quality['ready_episodes']),
                'quality_rejected': len(quality['rejected_episodes']),
                'quality_success_rate': quality['quality_distribution']['success_rate']
            },

            'publishing': {
                'total_attempted': publishing.total_attempted,
                'successful': publishing.successful,
                'failed': publishing.failed,
                'publish_success_rate': publishing.successful / publishing.total_attempted if publishing.total_attempted > 0 else 0,
                'batch_duration': publishing.batch_duration
            },

            'overall': {
                'total_discovered': discovery.total_files,
                'total_published': publishing.successful,
                'end_to_end_success_rate': publishing.successful / discovery.total_files if discovery.total_files > 0 else 0
            }
        }

    def _log_discovery_results(self, discovery: DiscoveryResult):
        """Log discovery phase results"""
        logger.info(f"🔍 Content Discovery Complete")
        logger.info(f"   📁 Sources found: {len(discovery.sources)}")
        logger.info(f"   📄 Total files: {discovery.total_files}")

        if discovery.errors:
            logger.warning(f"   ⚠️ Discovery errors: {len(discovery.errors)}")
            for error in discovery.errors[:3]:  # Show first 3 errors
                logger.warning(f"     • {error}")

    def _log_final_results(self, result: Dict[str, Any]):
        """Log final processing results"""
        logger.info("🎉 MASS PROCESSING COMPLETE")
        logger.info("=" * 40)

        discovery = result['discovery']
        processing = result['processing']
        publishing = result['publishing']
        overall = result['overall']

        logger.info(f"📊 Discovery: {discovery['total_files']} files from {discovery['total_sources']} sources")
        logger.info(f"🎭 Processing: {processing['episodes_processed']} episodes processed")
        logger.info(f"✅ Quality: {processing['quality_passed']} passed ({processing['quality_success_rate']:.1%})")
        logger.info(f"🚀 Publishing: {publishing['successful']}/{publishing['total_attempted']} successful")
        logger.info(f"📈 Success Rate: {overall['end_to_end_success_rate']:.1%}")
        logger.info(f"⏱️ Duration: {result['duration_seconds']:.1f} seconds")
        logger.info(f"🌐 Check: https://digitaldreamscape.site/blog/")

    def run_quality_analysis(self, max_episodes: Optional[int] = None) -> Dict[str, Any]:
        """Run quality analysis without publishing"""
        max_episodes = max_episodes or self.config.max_episodes

        logger.info("📊 QUALITY ANALYSIS MODE")
        logger.info("=" * 40)

        # Discover content
        discovery_result = self.discovery_service.discover_all_content()

        if not discovery_result.sources:
            return {'error': 'No content discovered'}

        # Process content
        episodes_data = self._process_discovered_content(discovery_result, max_episodes)

        if not episodes_data:
            return {'error': 'No episodes processed'}

        # Apply transformations
        processed_episodes = self._apply_victor_transformations(episodes_data)

        # Analyze quality distribution
        quality_scores = [ep.quality_score for ep in processed_episodes]
        quality_tiers = {}

        for ep in processed_episodes:
            tier = ep.quality_metrics.quality_tier
            quality_tiers[tier] = quality_tiers.get(tier, 0) + 1

        # Generate detailed report
        analysis = {
            'total_analyzed': len(processed_episodes),
            'quality_distribution': quality_tiers,
            'average_score': sum(quality_scores) / len(quality_scores) if quality_scores else 0,
            'score_range': {
                'min': min(quality_scores) if quality_scores else 0,
                'max': max(quality_scores) if quality_scores else 0
            },
            'threshold_analysis': {
                'current_threshold': self.config.quality_threshold,
                'would_publish': sum(1 for s in quality_scores if s >= self.config.quality_threshold),
                'would_reject': sum(1 for s in quality_scores if s < self.config.quality_threshold)
            },
            'top_performers': [
                {
                    'title': ep.episode_data.title[:50],
                    'score': ep.quality_score,
                    'tier': ep.quality_metrics.quality_tier,
                    'category': ep.episode_data.category.value
                }
                for ep in sorted(processed_episodes, key=lambda x: x.quality_score, reverse=True)[:5]
            ]
        }

        self._log_quality_analysis(analysis)
        return analysis

    def _log_quality_analysis(self, analysis: Dict[str, Any]):
        """Log quality analysis results"""
        logger.info("📈 Quality Analysis Results")
        logger.info("=" * 30)

        dist = analysis['quality_distribution']
        logger.info("🎯 Quality Distribution:")
        for tier in ['PLATINUM', 'GOLD', 'SILVER', 'BRONZE', 'REJECTED']:
            count = dist.get(tier, 0)
            if count > 0:
                logger.info(f"  {tier}: {count} episodes")

        logger.info(f"📊 Average Score: {analysis['average_score']:.3f}")
        logger.info(f"📊 Score Range: {analysis['score_range']['min']:.2f} - {analysis['score_range']['max']:.2f}")

        threshold = analysis['threshold_analysis']
        logger.info(f"🎯 Current Threshold ({threshold['current_threshold']}):")
        logger.info(f"  Would publish: {threshold['would_publish']} episodes")
        logger.info(f"  Would reject: {threshold['would_reject']} episodes")

        logger.info("🏆 Top Performers:")
        for i, ep in enumerate(analysis['top_performers'], 1):
            logger.info(f"  {i}. {ep['title']}... ({ep['score']:.2f} - {ep['tier']})")

def main():
    """Main execution entry point"""
    parser = argparse.ArgumentParser(description="Mass Episode Processor V2 - Digital Dreamscape")
    parser.add_argument("--batch-size", type=int, help="Episodes per batch")
    parser.add_argument("--max-episodes", type=int, help="Maximum episodes to process")
    parser.add_argument("--quality-threshold", type=float, help="Quality threshold (0.0-1.0)")
    parser.add_argument("--quality-analysis", action="store_true", help="Run quality analysis only")
    parser.add_argument("--dry-run", action="store_true", help="Discover content only")
    parser.add_argument("--config", type=str, help="Path to configuration file")
    parser.add_argument("--log-level", choices=['DEBUG', 'INFO', 'WARNING', 'ERROR'], help="Logging level")

    args = parser.parse_args()

    # Load configuration
    config_service = ConfigurationService(args.config)
    config = config_service.get_config()

    # Apply command line overrides
    if args.batch_size:
        config.batch_size = args.batch_size
    if args.max_episodes:
        config.max_episodes = args.max_episodes
    if args.quality_threshold:
        config.quality_threshold = args.quality_threshold
    if args.log_level:
        config.log_level = args.log_level

    # Validate configuration
    validation = config_service.validate_config()
    if not validation['valid']:
        print("❌ Configuration validation failed:")
        for issue in validation['issues']:
            print(f"  • {issue}")
        sys.exit(1)

    # Initialize processor
    processor = MassEpisodeProcessorV2(config)

    try:
        if args.dry_run:
            # Discovery only mode
            print("🔍 DRY RUN MODE - Discovering content only")
            discovery = processor.discovery_service.discover_all_content()
            print(f"\n📊 Found {discovery.total_files} files in {len(discovery.sources)} sources")

            if discovery.sources:
                print("\n📝 Sample Sources:")
                for i, source in enumerate(discovery.sources[:5], 1):
                    print(f"{i}. {source.source_type}: {source.path} ({source.file_count} files)")

        elif args.quality_analysis:
            # Quality analysis mode
            result = processor.run_quality_analysis(args.max_episodes)

            if 'error' in result:
                print(f"❌ Analysis failed: {result['error']}")
                sys.exit(1)

        else:
            # Full processing mode
            result = processor.run_mass_processing(args.batch_size, args.max_episodes)

            if 'error' in result:
                print(f"❌ Processing failed: {result['error']}")
                sys.exit(1)

    except KeyboardInterrupt:
        print("\n⏹️ Processing interrupted by user")
        sys.exit(130)
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()