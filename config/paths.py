#!/usr/bin/env python3
"""
Centralized Path Management for Websites Repository
Provides portable and scalable path resolution across different environments
"""

import os
from pathlib import Path
from typing import Optional, Dict, Any


class PathManager:
    """Centralized path management system"""

    def __init__(self, base_path: Optional[str] = None):
        """Initialize path manager with base directory"""
        if base_path is None:
            # Auto-detect base path (directory containing this config)
            self.base_path = Path(__file__).parent.parent
        else:
            self.base_path = Path(base_path)

        self._ensure_base_path_exists()

    def _ensure_base_path_exists(self):
        """Ensure base path exists"""
        if not self.base_path.exists():
            raise ValueError(f"Base path does not exist: {self.base_path}")

    @property
    def root(self) -> Path:
        """Root directory of the websites repository"""
        return self.base_path

    @property
    def scripts(self) -> Path:
        """Scripts directory"""
        return self.base_path / "scripts"

    @property
    def sites(self) -> Path:
        """Sites directory"""
        return self.base_path / "sites"

    @property
    def config(self) -> Path:
        """Configuration directory"""
        return self.base_path / "config"

    @property
    def content(self) -> Path:
        """Content directory"""
        return self.base_path / "content"

    @property
    def docs(self) -> Path:
        """Documentation directory"""
        return self.base_path / "docs"

    @property
    def tools(self) -> Path:
        """Tools directory"""
        return self.base_path / "tools"

    @property
    def ops(self) -> Path:
        """Operations directory"""
        return self.base_path / "ops"

    @property
    def src(self) -> Path:
        """Source code directory"""
        return self.base_path / "src"

    @property
    def tests(self) -> Path:
        """Tests directory"""
        return self.base_path / "tests"

    @property
    def archive(self) -> Path:
        """Archive directory"""
        return self.base_path / "archive"

    @property
    def temp(self) -> Path:
        """Temporary files directory"""
        return self.base_path / "temp"

    @property
    def runtime(self) -> Path:
        """Runtime data directory"""
        return self.base_path / "runtime"

    @property
    def assets(self) -> Path:
        """Assets directory"""
        return self.base_path / "assets"

    @property
    def backup(self) -> Path:
        """Backup directory"""
        return self.base_path / "backup"

    # Script subdirectories
    @property
    def audit_scripts(self) -> Path:
        """Audit scripts directory"""
        return self.scripts / "audit"

    @property
    def deploy_scripts(self) -> Path:
        """Deployment scripts directory"""
        return self.scripts / "deploy"

    @property
    def check_scripts(self) -> Path:
        """Check scripts directory"""
        return self.scripts / "check"

    @property
    def debug_scripts(self) -> Path:
        """Debug scripts directory"""
        return self.scripts / "debug"

    @property
    def test_scripts(self) -> Path:
        """Test scripts directory"""
        return self.scripts / "test"

    @property
    def service_scripts(self) -> Path:
        """Service scripts directory"""
        return self.scripts / "services"

    # Site subdirectories
    @property
    def production_sites(self) -> Path:
        """Production sites directory"""
        return self.sites / "production"

    @property
    def staging_sites(self) -> Path:
        """Staging sites directory"""
        return self.sites / "staging"

    @property
    def development_sites(self) -> Path:
        """Development sites directory"""
        return self.sites / "development"

    # Asset subdirectories
    @property
    def image_assets(self) -> Path:
        """Image assets directory"""
        return self.assets / "images"

    @property
    def doc_assets(self) -> Path:
        """Documentation assets directory"""
        return self.assets / "docs"

    def get_website_path(self, site_name: str, environment: str = "production") -> Path:
        """Get path to a specific website"""
        if environment == "production":
            return self.production_sites / site_name
        elif environment == "staging":
            return self.staging_sites / site_name
        elif environment == "development":
            return self.development_sites / site_name
        else:
            raise ValueError(f"Unknown environment: {environment}")

    def get_script_path(self, script_name: str, category: Optional[str] = None) -> Path:
        """Get path to a specific script"""
        if category:
            return getattr(self, f"{category}_scripts") / script_name
        else:
            return self.scripts / script_name

    def resolve_path(self, path_str: str) -> Path:
        """Resolve a path string to an absolute Path object"""
        # Handle environment variables
        path_str = os.path.expandvars(path_str)

        # Convert to Path object
        path = Path(path_str)

        # If relative, make it relative to base
        if not path.is_absolute():
            path = self.base_path / path

        return path

    def get_relative_path(self, target_path: Path) -> Path:
        """Get relative path from base directory"""
        try:
            return target_path.relative_to(self.base_path)
        except ValueError:
            # Path is not relative to base, return as-is
            return target_path

    def ensure_directory(self, path: Path) -> Path:
        """Ensure a directory exists, creating it if necessary"""
        path.mkdir(parents=True, exist_ok=True)
        return path

    def get_config_value(self, key: str, default: Any = None) -> Any:
        """Get configuration value (placeholder for future config system)"""
        # This could be extended to read from config files
        return default

    def __repr__(self) -> str:
        return f"PathManager(base_path={self.base_path})"


# Global instance for convenience
paths = PathManager()


def get_website_files(site_name: str, file_patterns: list) -> list:
    """Get files from a website directory matching patterns"""
    site_path = paths.get_website_path(site_name)
    files = []

    for pattern in file_patterns:
        files.extend(site_path.glob(pattern))

    return files


def setup_environment():
    """Setup environment-specific paths"""
    # This could be extended for different environments
    # For now, just ensure basic directories exist
    paths.ensure_directory(paths.scripts)
    paths.ensure_directory(paths.sites)
    paths.ensure_directory(paths.backup)


# Initialize environment on import
setup_environment()