"""
Configuration Service
====================

Centralized configuration management for the episode processing system.
"""

import os
from pathlib import Path
from typing import Dict, Any, Optional
import json
import logging
from dataclasses import dataclass, field

logger = logging.getLogger(__name__)

@dataclass
class ProcessingConfig:
    """Configuration for the episode processing pipeline"""

    # Content discovery settings
    base_paths: Dict[str, str] = field(default_factory=dict)
    file_patterns: list = field(default_factory=lambda: ['*.md', '*.json'])
    exclude_patterns: list = field(default_factory=list)
    max_depth: int = 3

    # Quality assessment settings
    quality_threshold: float = 0.7
    quality_weights: Dict[str, float] = field(default_factory=lambda: {
        'content_density': 0.15,
        'structural_integrity': 0.10,
        'factual_accuracy': 0.15,
        'storytelling_flow': 0.10,
        'emotional_resonance': 0.08,
        'insight_density': 0.07,
        'victor_voice_authenticity': 0.12,
        'readability_score': 0.08,
        'conversational_flow': 0.08,
        'shareability_score': 0.04,
        'timelessness': 0.03,
        'formatting_quality': 0.00
    })

    # Processing settings
    batch_size: int = 5
    max_episodes: int = 50
    victor_voice_intensity: float = 0.7

    # WordPress settings
    wp_url: str = 'https://digitaldreamscape.site'
    wp_user: str = 'dadudekc@gmail.com'
    wp_app_pass: str = ''
    wp_timeout: int = 30
    wp_retry_attempts: int = 3
    wp_retry_delay: int = 5

    # Logging settings
    log_level: str = 'INFO'
    log_file: Optional[str] = None

    # Performance settings
    max_workers: int = 4
    cache_enabled: bool = True
    cache_ttl: int = 3600  # 1 hour

class ConfigurationService:
    """Service for managing application configuration"""

    def __init__(self, config_file: Optional[str] = None):
        """
        Initialize configuration service.

        Args:
            config_file: Path to JSON configuration file (optional)
        """
        self.config_file = config_file or self._find_default_config()
        self._config = ProcessingConfig()
        self._load_configuration()

    def _find_default_config(self) -> str:
        """Find default configuration file"""
        # Look in current directory first
        current_dir = Path.cwd()
        config_files = ['episode_processor_config.json', 'config.json', '.episode_config.json']

        for config_file in config_files:
            if (current_dir / config_file).exists():
                return str(current_dir / config_file)

        # Look in script directory
        script_dir = Path(__file__).parent
        for config_file in config_files:
            if (script_dir / config_file).exists():
                return str(script_dir / config_file)

        # Return default path (will be created if needed)
        return str(current_dir / 'episode_processor_config.json')

    def _load_configuration(self):
        """Load configuration from file and environment variables"""
        # Load from file if it exists
        if Path(self.config_file).exists():
            try:
                with open(self.config_file, 'r') as f:
                    file_config = json.load(f)
                self._merge_config(file_config)
                logger.info(f"Loaded configuration from {self.config_file}")
            except Exception as e:
                logger.warning(f"Failed to load config file {self.config_file}: {e}")

        # Override with environment variables
        self._load_from_environment()

        # Set default paths if not configured
        self._set_default_paths()

    def _merge_config(self, file_config: Dict[str, Any]):
        """Merge file configuration with defaults"""
        for key, value in file_config.items():
            if hasattr(self._config, key):
                setattr(self._config, key, value)
            else:
                logger.warning(f"Unknown configuration key: {key}")

    def _load_from_environment(self):
        """Load configuration from environment variables"""
        # WordPress settings
        if os.getenv('DREAM_WP_URL'):
            self._config.wp_url = os.getenv('DREAM_WP_URL')
        if os.getenv('DREAM_WP_USER'):
            self._config.wp_user = os.getenv('DREAM_WP_USER')
        if os.getenv('DREAM_WP_APP_PASS'):
            self._config.wp_app_pass = os.getenv('DREAM_WP_APP_PASS')

        # Quality settings
        if os.getenv('EPISODE_QUALITY_THRESHOLD'):
            try:
                self._config.quality_threshold = float(os.getenv('EPISODE_QUALITY_THRESHOLD'))
            except ValueError:
                logger.warning("Invalid EPISODE_QUALITY_THRESHOLD value")

        # Processing settings
        if os.getenv('EPISODE_BATCH_SIZE'):
            try:
                self._config.batch_size = int(os.getenv('EPISODE_BATCH_SIZE'))
            except ValueError:
                logger.warning("Invalid EPISODE_BATCH_SIZE value")

        if os.getenv('EPISODE_MAX_EPISODES'):
            try:
                self._config.max_episodes = int(os.getenv('EPISODE_MAX_EPISODES'))
            except ValueError:
                logger.warning("Invalid EPISODE_MAX_EPISODES value")

        # Logging
        if os.getenv('LOG_LEVEL'):
            self._config.log_level = os.getenv('LOG_LEVEL').upper()

    def _set_default_paths(self):
        """Set default base paths if not configured"""
        if not self._config.base_paths:
            # Default paths for the current setup
            self._config.base_paths = {
                'main_devlogs': 'D:/Agent_Cellphone_V2_Repository/devlogs',
                'agent_workspaces': 'D:/Agent_Cellphone_V2_Repository/agent_workspaces',
                'message_queue': 'D:/Agent_Cellphone_V2_Repository/message_queue'
            }

        # Validate paths exist
        for name, path_str in self._config.base_paths.items():
            path = Path(path_str)
            if not path.exists():
                logger.warning(f"Configured path does not exist: {name} -> {path_str}")

    def get_config(self) -> ProcessingConfig:
        """Get the current configuration"""
        return self._config

    def update_config(self, updates: Dict[str, Any]):
        """Update configuration values"""
        for key, value in updates.items():
            if hasattr(self._config, key):
                setattr(self._config, key, value)
                logger.info(f"Updated configuration: {key} = {value}")
            else:
                logger.warning(f"Unknown configuration key: {key}")

    def save_config(self, file_path: Optional[str] = None):
        """Save current configuration to file"""
        save_path = file_path or self.config_file

        # Convert config to dict for JSON serialization
        config_dict = {}
        for key in dir(self._config):
            if not key.startswith('_'):
                value = getattr(self._config, key)
                # Only serialize simple types
                if isinstance(value, (str, int, float, bool, list, dict)):
                    config_dict[key] = value

        try:
            with open(save_path, 'w') as f:
                json.dump(config_dict, f, indent=2)
            logger.info(f"Saved configuration to {save_path}")
        except Exception as e:
            logger.error(f"Failed to save configuration: {e}")

    def validate_config(self) -> Dict[str, Any]:
        """Validate current configuration"""
        issues = []

        # Check required WordPress settings
        if not self._config.wp_url:
            issues.append("WordPress URL is required")
        if not self._config.wp_user:
            issues.append("WordPress username is required")
        if not self._config.wp_app_pass:
            issues.append("WordPress app password is required")

        # Check quality threshold
        if not 0.0 <= self._config.quality_threshold <= 1.0:
            issues.append("Quality threshold must be between 0.0 and 1.0")

        # Check batch size
        if self._config.batch_size < 1:
            issues.append("Batch size must be at least 1")

        # Check base paths
        for name, path_str in self._config.base_paths.items():
            if not Path(path_str).exists():
                issues.append(f"Base path '{name}' does not exist: {path_str}")

        # Check quality weights sum to reasonable value
        total_weight = sum(self._config.quality_weights.values())
        if not 0.95 <= total_weight <= 1.05:  # Allow small tolerance
            issues.append(f"Quality weights should sum to ~1.0, got {total_weight}")

        return {
            'valid': len(issues) == 0,
            'issues': issues
        }

    def get_service_configs(self) -> Dict[str, Dict[str, Any]]:
        """Get configuration organized by service"""
        return {
            'content_discovery': {
                'base_paths': self._config.base_paths,
                'file_patterns': self._config.file_patterns,
                'exclude_patterns': self._config.exclude_patterns,
                'max_depth': self._config.max_depth
            },
            'quality_scorer': {
                'quality_weights': self._config.quality_weights
            },
            'content_processing': {
                'quality_threshold': self._config.quality_threshold,
                'victor_voice_intensity': self._config.victor_voice_intensity
            },
            'wordpress_publishing': {
                'wp_url': self._config.wp_url,
                'wp_user': self._config.wp_user,
                'wp_app_pass': self._config.wp_app_pass,
                'timeout': self._config.wp_timeout,
                'retry_attempts': self._config.wp_retry_attempts,
                'retry_delay': self._config.wp_retry_delay
            },
            'batch_processing': {
                'batch_size': self._config.batch_size,
                'max_episodes': self._config.max_episodes
            },
            'performance': {
                'max_workers': self._config.max_workers,
                'cache_enabled': self._config.cache_enabled,
                'cache_ttl': self._config.cache_ttl
            }
        }